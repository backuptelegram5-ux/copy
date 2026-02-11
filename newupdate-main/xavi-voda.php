<?php

require_once('Tools.php');

$cookie = isset($_GET['c']) ? trim($_GET['c']) : '';

// ===== Tunables =====
define('LINK_MAX_TRIES', 4);      // playNow link dies after ~3-5 tries
define('TRIES_PER_BAND', 4);      // how many score attempts before falling back to next band
define('PUSH_STEP', 1);           // "try above anchor score" (1 = closest push-down)
define('MAX_FALLBACK_POS', 6);    // we fallback using pos 4,5,6

// ===== Helper: fetch fresh playNow params + x_power =====
function refreshGameSession(string $cookie): array {
    $uA = RandomUa();

    // 1) Get play-now redirect URL (contains unique_id, game_id, sigv1)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://gameplay.mzansigames.club/play-now');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: no-cache',
        'Connection: keep-alive',
        'Cookie: '.$cookie,
        'Pragma: no-cache',
        'Referer: https://gameplay.mzansigames.club/',
        'Sec-CH-UA: "Safari";v="15", "AppleWebKit";v="605"',
        'Sec-CH-UA-Mobile: ?1',
        'Sec-CH-UA-Platform: "iOS"',
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
    curl_exec($ch);
    $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    $query_str = parse_url($redirectedUrl, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    $unique_id = $query_params['unique_id'] ?? '';
    $game_id   = $query_params['game_id'] ?? '';
    $sigv1     = $query_params['sigv1'] ?? '';

    if (empty($unique_id) || empty($sigv1)) {
        return [
            'ok' => false,
            'error' => 'Cookie expired/not valid, please update.',
        ];
    }

    // 2) Get header that contains x_power
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://gameplay.mzansigames.club/new-game-check-user-status/'.$unique_id.'/'.$sigv1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array(
        'Referer:'.$redirectedUrl,
        'Sec-CH-UA: "Safari";v="15", "AppleWebKit";v="605"',
        'Sec-CH-UA-Mobile: ?1',
        'Sec-CH-UA-Platform: "iOS"',
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
    curl_setopt($ch, CURLOPT_HEADER, 1);

    $curl = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($curl, 0, $header_size);
    curl_close($ch);

    $x_power = X_Power($header);

    return [
        'ok' => true,
        'uA' => $uA,
        'redirectedUrl' => $redirectedUrl,
        'unique_id' => $unique_id,
        'game_id' => $game_id,
        'sigv1' => $sigv1,
        'x_power' => $x_power
    ];
}

// ===== Helper: attempt a score (keeps your original calls) =====
function attemptScore(array &$sess, string $cookie, int $score, int &$linkTryCount,$gameScore = 100): void {
    // refresh link if it has been used too many times
    if ($linkTryCount >= LINK_MAX_TRIES) {
        $sess = refreshGameSession($cookie);
        $linkTryCount = 0;
    }

    $increment = 1;
    $uA = $sess['uA'];

    $memory = validate_request($sess['x_power'], $score);
    $sess['x_power'] = generateRandomDivisionData(
        $score,
        $sess['redirectedUrl'],
        $sess['x_power'],
        $memory,
        $increment,
        $uA,
        $gameScore
    );

    $linkTryCount++;
}

// ===== Start =====
$pos = GetPosition($cookie);
$number3 = GetTargetScore(1);

echo "\nScore at position 1 is $number3, while our position is $pos (0 for not yet)\n";
system('clear');

// Skip if already high enough (you had <=1; keeping it but you can change to <=10)
$pos = GetPosition($cookie);
if ($pos > 0 && $pos <= 1) {
    echo "\nAlready in top 10 at position $pos, skipping request.";
    sleep(rand(120,340));
    exit;
}

$success = false;
$currentScore = null;

// "before" snapshot (your logic)
$beforecurrentScore = GetTargetScore($pos);

// ===== Band 1 (your original score list) =====
$gameScore = 1000;

$max = 2790;
$count = 10;
$step = 10;

if($number3>$max){
 $max = $number3;
}
// calculate minimum based on max, step, and count
$min = $max - ($count - 1) * $step;

$scores = range($max, $min, -$step); 
shuffle($scores);

// ===== Build fallback anchors from leaderboard pos 4/5/6 =====
$fallbackAnchors = [];
for ($p = 4; $p <= MAX_FALLBACK_POS; $p++) {
    $val = GetTargetScore($p);
    if (is_numeric($val)) {
        $fallbackAnchors[] = (int)$val;
    }
}
// remove duplicates but keep order
$fallbackAnchors = array_values(array_unique($fallbackAnchors));

// If no leaderboard anchors exist, use our own fallback bands
if (empty($fallbackAnchors)) {
    // Choose your own levels (example ladder)
    $fallbackAnchors = [600, 400, 300, 200, 100];
}

// ===== Build bands: [primary list] then [anchor+1 list] per anchor =====
$bands = [];
$bands[] = [
    'name' => 'primary',
    'scores' => $scores
];

foreach ($fallbackAnchors as $anchor) {
    // try just above the anchor, closest to pushing them down
    // we generate a small ladder: anchor+1, anchor+2, ... up to TRIES_PER_BAND
    $bandScores = [];
    for ($i = 1; $i <= TRIES_PER_BAND; $i++) {
        $bandScores[] = $anchor + (PUSH_STEP * $i);
    }

    $bands[] = [
        'name' => 'anchor_above_'.$anchor,
        'scores' => $bandScores
    ];
}

// ===== Session init =====
$sess = refreshGameSession($cookie);
if (!$sess['ok']) {
    echo "\n".$sess['error']."\n";
    exit;
}
echo "\nX-Powered-Version: ".$sess['x_power']."\n";

// ===== Main loop across bands =====
foreach ($bands as $bandIndex => $band) {

    // force fresh link on every band change (your requirement)
    $sess = refreshGameSession($cookie);
    if (!$sess['ok']) {
        echo "\n".$sess['error']."\n";
        exit;
    }
    $linkTryCount = 0;

    echo "\n\n--- Band: {$band['name']} (fresh playNow) ---\n";

    $triesInBand = 0;

    foreach ($band['scores'] as $score) {

        // Stop after TRIES_PER_BAND attempts in each band (even if band has more scores)
        if ($triesInBand >= TRIES_PER_BAND) {
            echo "\nBand '{$band['name']}' exhausted after ".TRIES_PER_BAND." tries. Falling back...\n";
            break;
        }

        // your original "already in use" check
        if (isScoreInTop10($score)) {
            echo "Already in use $score\n";
            continue;
        }

        echo "\nTrying score => $score\n";

        attemptScore($sess, $cookie, $score, $linkTryCount,$gameScore);
        $triesInBand++;

        $pos = GetPosition($cookie);
        $currentScore = GetTargetScore($pos);

        echo "\nLeaderboard value: $currentScore at pos $pos";

        // your success condition (kept as-is)
        if ($currentScore !== $beforecurrentScore && $pos > 0 && $pos <= 6) {
            $success = true;
            break 2; // exit bands loop too
        }

        echo "\nScore $score failed to update.";
    }
}

if ($success) {
    echo "\nLeaderboard updated with score: $currentScore";
} else {
    echo "\nFailed to update leaderboard";
}
