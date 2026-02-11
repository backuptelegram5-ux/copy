 <?php
date_default_timezone_set('Africa/Johannesburg');
// $current_time = new DateTime();
// $check_time = new DateTime('04:00'); 
// $check_tim = new DateTime('12:00');

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

function fetchPlaySession($cookie, $uA) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://yellorush.co.za/play-now');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: no-cache',
        'Connection: keep-alive',
        'Cookie: '.$cookie,
        'Pragma: no-cache',
        'Host: www.yellorush.co.za',
        'Referer: https://yellorush.co.za/',
        'Sec-CH-UA: \"Safari\";v=\"15\", \"AppleWebKit\";v=\"605\"',
        'Sec-CH-UA-Mobile: ?1',
        'Sec-CH-UA-Platform: \"iOS\"',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: '.$uA
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $curl = curl_exec($ch);
    $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                
    curl_close($ch);
    $query_str = parse_url($redirectedUrl, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    return [
        'url' => $redirectedUrl,
        'unique_id' => isset($query_params['unique_id']) ? $query_params['unique_id'] : '',
        'game_id' => isset($query_params['game_id']) ? $query_params['game_id'] : '',
        'sigv1' => isset($query_params['sigv1']) ? $query_params['sigv1'] : '',
    ];
}

function refreshCookieFromOtp($currentCookie){
    $cookieFilePath = __DIR__ . '/new/data/cookies-mtn.json';
    $mobileUA = 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36';
    if (!file_exists($cookieFilePath)) {
        echo "\nCookie file not found for OTP refresh.";
        return null;
    }

    $fp = fopen($cookieFilePath, 'c+');
    if (!$fp) return null;
    if (!flock($fp, LOCK_EX)) { fclose($fp); return null; }
    $list = json_decode(stream_get_contents($fp), true);
    flock($fp, LOCK_UN);
    fclose($fp);
    if (!is_array($list)) return null;

    $matchIndex = null;
    $entry = null;
    foreach ($list as $idx => $row) {
        $storedCookie = isset($row['value']) ? $row['value'] : (isset($row['cookie']) ? $row['cookie'] : '');
        if ($storedCookie === $currentCookie) {
            $matchIndex = $idx;
            $entry = $row;
            break;
        }
    }

    if ($entry === null) {
        echo "\nNo matching entry found for OTP refresh.";
        return null;
    }

    $otp = isset($entry['OTP']) ? $entry['OTP'] : (isset($entry['otp']) ? $entry['otp'] : null);
    $numberRaw = isset($entry['phone']) ? $entry['phone'] : (isset($entry['number']) ? $entry['number'] : null);
    $number = normalize_msisdn($numberRaw);
    if (!$otp || !$number) {
        echo "\nMissing OTP/number for refresh.";
        return null;
    }

    $tmpCookie = tempnam(sys_get_temp_dir(), 'otp_cookie_');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://yellorush.co.za/otp/' . $number);
    curl_setopt($ch, CURLOPT_USERAGENT, $mobileUA);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $tmpCookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $tmpCookie);
    $html = curl_exec($ch);
    if (curl_errno($ch)) {
        echo "\nOTP token fetch failed: " . curl_error($ch);
        curl_close($ch);
        @unlink($tmpCookie);
        return null;
    }
    curl_close($ch);

    $token = extract_csrf_token($html);
    if (!$token) {
        echo "\nCould not extract OTP token.";
     file_put_contents('debug.html', $html);
        @unlink($tmpCookie);
        return null;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://yellorush.co.za/submitotp');
    curl_setopt($ch, CURLOPT_USERAGENT, $mobileUA);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $tmpCookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $tmpCookie);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        '_token' => $token,
        'email' => $number,
        'password' => $otp
    ]));
    curl_exec($ch);
    if (curl_errno($ch)) {
        echo "\nOTP submit failed: " . curl_error($ch);
        curl_close($ch);
        @unlink($tmpCookie);
        return null;
    }
    curl_close($ch);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://yellorush.co.za/play-now');
    curl_setopt($ch, CURLOPT_USERAGENT, $mobileUA);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $tmpCookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $tmpCookie);
    curl_exec($ch);
    curl_close($ch);

    $jar = parse_cookiejar($tmpCookie);
    @unlink($tmpCookie);
    $xsrf = null; $sessionName = null; $sessionVal = null;
    foreach ($jar as $k=>$v){
        if ($xsrf === null && stripos($k, 'xsrf') !== false) { $xsrf = $v; }
        if ($sessionVal === null && stripos($k, 'session') !== false) { $sessionName=$k; $sessionVal=$v; }
    }
    if (!$xsrf || !$sessionVal) {
        echo "\nFailed to refresh cookie via OTP.";
        return null;
    }

    $newCookie = "XSRF-TOKEN={$xsrf};{$sessionName}={$sessionVal}";

    $fp = fopen($cookieFilePath, 'c+');
    if ($fp && flock($fp, LOCK_EX)) {
        $existing = json_decode(stream_get_contents($fp), true);
        if (!is_array($existing)) { $existing = []; }
        $updated = false;
        foreach ($existing as $idx => $row) {
            $storedCookie = isset($row['cookie']) ? $row['cookie'] : '';
            if ($storedCookie === $currentCookie || ($numberRaw && isset($row['phone']) && $row['phone'] === $numberRaw)) {
                $existing[$idx]['cookie'] = $newCookie;
                if (isset($existing[$idx]['cookie'])) {
                    $existing[$idx]['cookie'] = $newCookie;
                }
                $updated = true;
                break;
            }
        }
        if ($updated) {
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($existing, JSON_PRETTY_PRINT));
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    echo "\nCookie refreshed via OTP flow.";
    return $newCookie;
}


require_once('Tools-mtn-v2.php');
system('clear');

$uA = RandomUa();
$scoreTarget = TargetScore();
$number3 = GetTargetScore(1);

$cookie = "XSRF-TOKEN=eyJpdiI6Ik53ODZVYmQ4RTlyT2pTay9HTWVwMkE9PSIsInZhbHVlIjoiR0Vra2NSai9jajFmbHE1Nndkb0JFSzgzQWFjd0k0b3hmQXF1K1ZYZnVCbWxTWGxTNktTKzJIOXhSNG9rR1JxOThEa3R3ejlHTzhBSFZvNFduRDJKRXIwVklWUCtQOW1ScDVCNDZFYXFwMGxlYXgrRFRaMDh3R21QaGc4THdJZU4iLCJtYWMiOiIwMjdmMGE5YzA4YjRjNDhlYjYzZGIxY2ZmMDdkNjhmODBjZWJkNzMzZDQ3N2FjN2Q3ZTViODVkNDFkNTQ1NzFhIiwidGFnIjoiIn0%3D; yello_rush_session=eyJpdiI6IjUwT1p2eWkvS1VXSjFHOGsrUGdaMUE9PSIsInZhbHVlIjoiUGt6S0dFbzU5R0Z2VnQrdkM3TXRIYlFMMk4vc0xQQmxoT0NPUTBOQmRGb21HUkxBN294NkV3bGo5NWVmMFhtQlBSc3UzWUxxTmR3MFgzUTVPZzEzNE1lbFN2K1lSamN3VVlWbVFsTHA0UEp5bmdzRlZUMGh0SW5rUkFWZXo0UlEiLCJtYWMiOiI4OGZhMzU1MGQwNWY1YzViODUxOWQwOTAzZjY2YjFmYTBjNGFkODYwZDMxNmZiYmY5NmQyNDE2M2UzZmQ0Mzk5IiwidGFnIjoiIn0%3D";
        


$pos = GetPosition ($cookie);
$b4Score = GetTargetScore($pos);

echo "\nScore at position 1 is $number3, while our position is $pos(0 for not yet)\n";
        $playData = fetchPlaySession($cookie, $uA);
        $redirectedUrl = $playData['url'];
        $unique_id = $playData['unique_id'];
        $game_id = $playData['game_id'];
        $sigv1 = $playData['sigv1'];

             if (empty($unique_id)){
              $refreshed = refreshCookieFromOtp($cookie);
              if ($refreshed) {
                $cookie = $refreshed;
                $playData = fetchPlaySession($cookie, $uA);
                $redirectedUrl = $playData['url'];
                $unique_id = $playData['unique_id'];
                $game_id = $playData['game_id'];
                $sigv1 = $playData['sigv1'];
              }
              if (empty($unique_id)){
                "Cookie expired/not valid, please update.\n";
                 return;
              }
             }

        ###################
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

// Build and shuffle the score list so each score is attempted once in random order
// $max = 33642;
// $count = 10;
// $min = $max - ($count - 1);
// $scores = range($max, $min);
// shuffle($scores);


$max = 9842;
$count = 10;
$step = 1;

// calculate minimum based on max, step, and count
$min = $max - ($count - 1) * $step;

$scores = range($max, $min, -$step); 

shuffle($scores);
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
    $x_power = generateRandomDivisionData($score, $redirectedUrl, $x_power, $memory, $increment, $uA);
    $pos = GetPosition($cookie);
    $currentScore = GetTargetScore($pos);
    echo "\nLeaderboard value: $currentScore at pos $pos";
    if ($currentScore == $score && $pos > 0 && $pos <= 6) {
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
