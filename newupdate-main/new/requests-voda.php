<?php
require_once(__DIR__ . '/../Tools.php');
$scoreTarget = TargetScore();
system('sudo rm -rf cache');
require_once '/var/www/html/newupdate/Zebra_cURL.php';
$curl = new Zebra_cURL();
$curl->cache('/var/www/html/newupdate/cache', 59);
$curl->ssl(true, 2, '/var/www/html/newupdate/cacert.pem');
$curl->threads = 10;
$curl->option(CURLOPT_TIMEOUT, 2400);
@unlink('cache');

$starttime = microtime(true);

$cookieFile = __DIR__ . '/data/cookies.json';
$maxConcurrent = 2;
$selectedIndexes = [];
$urls_ar = [];
while (true) {
    $fp = fopen($cookieFile, 'c+');
    if (flock($fp, LOCK_EX)) {
        $contents = stream_get_contents($fp);
        $cookies = json_decode($contents, true);
        if (!is_array($cookies)) {
            flock($fp, LOCK_UN);
            fclose($fp);
            sleep(1);
            continue;
        }
        foreach ($cookies as $idx => $cookie) {
            if (!empty($cookie['isFree'])) {
                $cookies[$idx]['isFree'] = false;
                $selectedIndexes[] = $idx;
                $urls_ar[] = $cookie['cookie'];
                if (count($urls_ar) >= $maxConcurrent) break;
            }
        }
        $encoded = json_encode($cookies, JSON_PRETTY_PRINT);
        if ($encoded === false) {
            flock($fp, LOCK_UN);
            fclose($fp);
            sleep(1);
            continue;
        }
        rewind($fp);
        ftruncate($fp, 0);
        fwrite($fp, $encoded);
        flock($fp, LOCK_UN);
        fclose($fp);
        if (!empty($urls_ar)) break;
    } else {
        fclose($fp);
    }
    sleep(1);
}

$serverIP = trim(gethostbyname(gethostname()));
echo "\nIP ADDR: $serverIP";
$urls = [];
foreach ($urls_ar as $c) {
    $urls[] = 'http://' . $serverIP . '/newupdate/xavi-voda.php?c=' . urlencode($c);
}

$curl->get($urls, function($result) {
    if ($result->response[1] == CURLE_OK) {
        echo 'Success: ', $result->body;
    } else {
        echo 'Error: ', $result->response[0], PHP_EOL;
    }
});

$fp = fopen($cookieFile, 'c+');
flock($fp, LOCK_EX);
$contents = stream_get_contents($fp);
$cookies = json_decode($contents, true);
if (is_array($cookies)) {
    foreach ($selectedIndexes as $idx) {
        if (isset($cookies[$idx])) {
            $cookies[$idx]['isFree'] = true;
        }
    }

    $encoded = json_encode($cookies, JSON_PRETTY_PRINT);
    if ($encoded !== false) {
        rewind($fp);
        ftruncate($fp, 0);
        fwrite($fp, $encoded);
    }

    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($cookies, JSON_PRETTY_PRINT));
} else {
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, $contents);

}
flock($fp, LOCK_UN);
fclose($fp);

$endtime = microtime(true);
$duration = $endtime - $starttime;
echo "Execution time: " . $duration . " seconds";
?>
