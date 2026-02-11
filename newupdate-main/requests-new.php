<?php
require_once(__DIR__ . '/../Tools-mtn-v2.php');
require_once('/var/www/html/newupdate/round_guard.php');
$scoreTarget = TargetScore();

// clean cache directory
system('sudo rm -rf cache');

require_once '/var/www/html/newupdate/Zebra_cURL.php';
$curl = new Zebra_cURL();
$curl->cache('/var/www/html/newupdate/cache', 59);
$curl->ssl(true, 2, '/var/www/html/newupdate/cacert.pem');
$curl->threads = 10;
$curl->option(CURLOPT_TIMEOUT, 2400);
@unlink('cache');

// record start time
$starttime = microtime(true);

// Maintenance window: stop scheduling and reset round, then exit
if (function_exists('rg_is_maintenance_window') && rg_is_maintenance_window()) {
    echo "[" . date("H:i:s") . "] [Maintenance] Minute 58-59: resetting state and skipping run.\n";
    if (function_exists('rg_round_reset')) {
        rg_round_reset();
    }
    exit;
}

$cookieFile = '/var/www/html/newupdate/new/data/cookies-mtn.json';
$maxConcurrent = rand(2, 4);
$selectedIndexes = [];
$urls_ar = [];

// Ensure cookie file exists and is valid JSON array
if (!file_exists($cookieFile)) {
    @file_put_contents($cookieFile, "[]");
}

// If file is empty or whitespace, initialize to []
$rawInit = @file_get_contents($cookieFile);
if ($rawInit === false || trim($rawInit) === '') {
    @file_put_contents($cookieFile, "[]");
}

/* ────────────────────────────────
   STEP 1: SAFE COOKIE ASSIGNMENT
   (with auto-recovery)
──────────────────────────────── */
$fp = fopen($cookieFile, 'c+');
if ($fp && flock($fp, LOCK_EX)) {
    rewind($fp);
    $data = stream_get_contents($fp);
    $cookies = json_decode($data, true);
    if (!is_array($cookies)) {
        // Attempt to recover from bad JSON by resetting file to []
        $cookies = [];
    }

    $now = time();

    // 1️⃣ Recovery: free any stuck cookies older than 15 min
    if (!empty($cookies)) {
        foreach ($cookies as &$c) {
            if (!empty($c['takenAt']) && ($now - $c['takenAt'] > 600)) {
                $c['isFree'] = true;
                unset($c['takenAt']);
            }
        }
        unset($c);
    }

    // 2️⃣ Pick new cookies up to $maxConcurrent
    if (!empty($cookies)) {
        foreach ($cookies as $idx => $c) {
            if (!empty($c['isFree'])) {
                $cookies[$idx]['isFree'] = false;
                $cookies[$idx]['takenAt'] = $now;
                $selectedIndexes[] = $idx;
                $urls_ar[] = $c['cookie'];
                if (count($urls_ar) >= $maxConcurrent) break;
            }
        }
    }

    // 3️⃣ Write back updated JSON
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($cookies, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
}
fclose($fp);

// If maintenance kicked in right after selection, revert and exit
if (function_exists('rg_is_maintenance_window') && rg_is_maintenance_window()) {
    $fp2 = fopen($cookieFile, 'c+');
    if ($fp2 && flock($fp2, LOCK_EX)) {
        rewind($fp2);
        $data2 = stream_get_contents($fp2);
        $cookies2 = json_decode($data2, true);
        if (!is_array($cookies2)) $cookies2 = [];
        foreach ($selectedIndexes as $idx) {
            if (isset($cookies2[$idx])) {
                $cookies2[$idx]['isFree'] = true;
                unset($cookies2[$idx]['takenAt']);
            }
        }
        ftruncate($fp2, 0);
        rewind($fp2);
        fwrite($fp2, json_encode($cookies2, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        fflush($fp2);
        flock($fp2, LOCK_UN);
    }
    if ($fp2) fclose($fp2);
    echo "[" . date("H:i:s") . "] [Maintenance] Skipping dispatch, cookies reverted.\n";
    exit;
}

/* ────────────────────────────────
   STEP 2: MAIN WORK SECTION
   (no file lock here)
──────────────────────────────── */
if (empty($urls_ar)) {
    echo "[" . date("H:i:s") . "] No free cookies found. Waiting for next cron...\n";
    exit;
}

$serverIP = trim(gethostbyname(gethostname()));
echo "[" . date("H:i:s") . "] IP ADDR: $serverIP\n";

$urls = [];
foreach ($urls_ar as $c) {
    $urls[] = 'http://' . $serverIP . '/newupdate/xavi-test.php?c=' . urlencode($c);
}

$curl->get($urls, function($result) {
    if ($result->response[1] == CURLE_OK) {
        echo "Success: ", $result->body, PHP_EOL;
    } else {
        echo "Error: ", $result->response[0], PHP_EOL;
    }
});

/* ────────────────────────────────
   STEP 3: MARK USED COOKIES FREE
──────────────────────────────── */
$fp = fopen($cookieFile, 'c+');
if ($fp && flock($fp, LOCK_EX)) {
    rewind($fp);
    $data = stream_get_contents($fp);
    $cookies = json_decode($data, true);
    if (!is_array($cookies)) $cookies = [];

    if (!empty($selectedIndexes) && !empty($cookies)) {
        foreach ($selectedIndexes as $idx) {
            if (isset($cookies[$idx])) {
                $cookies[$idx]['isFree'] = true;
                unset($cookies[$idx]['takenAt']);
            }
        }
    }

    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($cookies, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
}
fclose($fp);

/* ────────────────────────────────
   STEP 4: FINAL TIMESTAMP + TIME
──────────────────────────────── */
$duration = round(microtime(true) - $starttime, 2);
echo "[" . date("H:i:s") . "] Execution time: {$duration}s\n";
?>
