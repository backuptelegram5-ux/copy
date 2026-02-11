<?php
date_default_timezone_set('Africa/Johannesburg');
$current_time = new DateTime();
$check_time = new DateTime('04:00');
$check_tim = new DateTime('12:00');

require_once('Tools-new.php');
system('cls');

$uA = RandomUa();
$scoreTarget = TargetScore();
$number3 = GetTargetScore(1);

$cookie = isset($_GET['c']) ? trim($_GET['c']) : '';
if (empty($cookie)) {
    die("\nNo cookie provided. Usage: script.php?c=your_cookie_string_here\n");
}

$pos = GetPosition($cookie);
echo "\nOur target score is: $number3 at pos $pos";
$scoreBefore = GetTargetScore($pos);

// ────────────────────────────────────────────────────────────────
// Helper function: Attempt play-now and parse redirect params
// Returns array or false on failure
// ────────────────────────────────────────────────────────────────
function attemptPlayNow($cookie, $uA)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://staging.yellorush.co.za/play-now',
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Cookie: ' . $cookie,
            'Pragma: no-cache',
            'Host: staging.yellorush.co.za',
            'Referer: https://staging.yellorush.co.za/',
            'Sec-CH-UA: "Safari";v="15", "AppleWebKit";v="605"',
            'Sec-CH-UA-Mobile: ?1',
            'Sec-CH-UA-Platform: "iOS"',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: ' . $uA,
        ],
    ]);

    $response = curl_exec($ch);
    $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 400 || empty($redirectedUrl)) {
        return false;
    }

    $query_str = parse_url($redirectedUrl, PHP_URL_QUERY);
    parse_str($query_str, $query_params);

    $unique_id = $query_params['unique_id'] ?? '';
    $game_id = $query_params['game_id'] ?? '';
    $sigv1 = $query_params['sigv1'] ?? '';

    return [
        'redirectedUrl' => $redirectedUrl,
        'unique_id' => $unique_id,
        'game_id' => $game_id,
        'sigv1' => $sigv1,
    ];
}

// ────────────────────────────────────────────────────────────────
// First attempt
// ────────────────────────────────────────────────────────────────
echo "\nAttempting normal login with provided cookie...\n";
$result = attemptPlayNow($cookie, $uA);

if (!$result || empty($result['unique_id']) || empty($result['game_id'])) {
    echo "\n[Fallback] No valid unique_id/game_id → trying phone login recovery...\n";

    $cookieFile = 'new/data/cookies-mtn2.json';

    if (!file_exists($cookieFile)) {
        die("\nCookie file missing: $cookieFile\n");
    }

    $cookiesData = json_decode(file_get_contents($cookieFile), true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($cookiesData)) {
        die("\nInvalid JSON in cookie file\n");
    }

    $targetEntry = null;
    $targetIndex = -1;

    foreach ($cookiesData as $idx => $entry) {
        if (isset($entry['cookie']) && trim($entry['cookie']) === trim($cookie)) {
            $targetEntry = $entry;
            $targetIndex = $idx;
            break;
        }
    }

    if (!$targetEntry || empty($targetEntry['phone'])) {
        die("\nNo matching cookie entry found in json or phone is missing\n");
    }

    $phone = trim($targetEntry['phone']);
    echo "\nUsing phone for recovery: $phone\n";

    // Step 1: encrypt-msisdn
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://dep.penroseza.com/encrypt-msisdn/v2',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Basic UGVucm9zZV9HYW1pbmc6Qm83c2pIeVQ4MGdB',
            'Content-Type: application/json',
            'Origin: https://voucher-store.yellowrush.co.za',
            'Referer: https://voucher-store.yellowrush.co.za/',
            'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36',
        ],
        CURLOPT_POSTFIELDS => json_encode(['msisdn' => $phone]),
    ]);

    $encJson = curl_exec($ch);
    $encCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($encCode !== 200 || empty($encJson)) {
        die("\nencrypt-msisdn failed (HTTP $encCode)\n");
    }

    $encData = json_decode($encJson, true);
    $eMsisdn = $encData['e-msisdn'] ?? '';

    if (empty($eMsisdn) || strlen($eMsisdn) < 200) {
        die("\nNo valid e-msisdn received\n");
    }

    // Step 2: GET with ?e-msisdn
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://staging.yellorush.co.za?e-msisdn=' . urlencode($eMsisdn),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'User-Agent: ' . $uA,
            'Upgrade-Insecure-Requests: 1',
        ],
    ]);

    $loginResp = curl_exec($ch);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headersPart = substr($loginResp, 0, $headerSize);
    curl_close($ch);

    // Extract Set-Cookie lines
    preg_match_all('/Set-Cookie:\s*([^\r\n]+)/i', $headersPart, $cookieMatches);
    $newCookieParts = $cookieMatches[1] ?? [];

    if (empty($newCookieParts)) {
        die("\nNo Set-Cookie headers found in login response\n");
    }

    $newCookie = implode('; ', array_map('trim', $newCookieParts));
    echo "\nNew cookie acquired (length: " . strlen($newCookie) . ")\n";

    // Update json file
    $cookiesData[$targetIndex]['cookie'] = $newCookie;
    $cookiesData[$targetIndex]['updated_at'] = date('c'); // optional

    if (file_put_contents($cookieFile, json_encode($cookiesData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
        die("\nFailed to save updated cookie file!\n");
    }

    echo "\nCookie file updated successfully for phone $phone\n";

    // Use the new cookie from now on
    $cookie = $newCookie;

    // Retry play-now with fresh cookie
    echo "\nRetrying play-now with new cookie...\n";
    $result = attemptPlayNow($cookie, $uA);

    if (!$result || empty($result['unique_id']) || empty($result['game_id'])) {
        die("\nFallback login succeeded but still no valid game params. Aborting.\n");
    }
}

// If we reach here → we have valid params
$redirectedUrl = $result['redirectedUrl'];
$unique_id = $result['unique_id'];
$game_id = $result['game_id'];
$sigv1 = $result['sigv1'];

echo "\nUsing unique_id: $unique_id | game_id: $game_id | sigv1: $sigv1\n";

// ────────────────────────────────────────────────────────────────
// Your original new-game-check-user-status call
// ────────────────────────────────────────────────────────────────
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://staging.yellorush.co.za/new-game-check-user-status/$unique_id/$sigv1",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => [
        'Host: staging.yellorush.co.za',
        'Referer: ' . $redirectedUrl,
        'Sec-CH-UA: "Safari";v="15", "AppleWebKit";v="605"',
        'Sec-CH-UA-Mobile: ?1',
        'Sec-CH-UA-Platform: "iOS"',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: ' . $uA,
    ],
]);

$resp = curl_exec($ch);
$headerSz = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($resp, 0, $headerSz);
$body = substr($resp, $headerSz);
curl_close($ch);

$x_power = X_Power($headers);
echo "\nX-Powered-Version: $x_power\n";

// ────────────────────────────────────────────────────────────────
// Your score submission loop (unchanged from here)
// ────────────────────────────────────────────────────────────────
$testSom = GetTargetScore($pos);
$MAX_SCORE = 500;
$range = 500;
$multiplier = ($pos <= 10 || $pos == 0) ? 0 : floor($number3 / $range);
$attemptLimit = 2;

if ($pos >= 1 && $pos <= 10) {
    die("\nStill within top 10 — exiting...\n");
}

while (true) {
    if ($multiplier <= 0) {
        $score = rand(100, 500);
        $min = 100;
        $max = 500;
    } else {
        $min = $range * $multiplier + 1;
        $max = $range * ($multiplier + 1);
        $score = rand($min, $max);
        while ($score < $number3) {
            $score += rand(1, 10);
        }
    }

    while ($score > 500) {
        $score -= 1;
    }

    echo "\nTrying score $score in range $min - $max";

    if ($score > ($MAX_SCORE * 2 + 1)) {
        sleep(rand(15, 20));
    }

    $increment = 1;
    $tries = 0;
    $success = false;

    do {
        $uA = RandomUa();
        $memory = validate_request($x_power, $score);
        $x_power = generateRandomDivisionData($score, $redirectedUrl, $x_power, $memory, $increment, $uA);
        sleep(rand(60, 120));

        $pos = GetPosition($cookie);
        $currentScore = GetTargetScore($pos);
        echo "\nLeaderboard value: $currentScore (expected $score)";

        if ($currentScore == $score) {
            $success = true;
        }
        $tries++;
    } while (!$success && $tries < $attemptLimit);

    if ($success) {
        echo "\nLeaderboard updated with score: $currentScore";
        $multiplier++;
        break;
    } else {
        echo "\nFailed for range $min - $max, stepping down";
        $multiplier = max(0, $multiplier - 1);
    }
}

echo "\nDone.\n";
