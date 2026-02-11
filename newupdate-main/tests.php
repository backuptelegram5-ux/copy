<?php
require_once('Tools-mtn-v2.php');
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

$cookieFile = __DIR__ . '/cookies-mtn.json';
$maxConcurrent = 3;
$selectedIndexes = [];
$urls_ar = [];
while (true) {
    $fp = fopen($cookieFile, 'c+');
    if (flock($fp, LOCK_EX)) {
        $cookies = json_decode(stream_get_contents($fp), true);
        foreach ($cookies as $idx => $cookie) {
            if (!empty($cookie['isFree'])) {
                $cookies[$idx]['isFree'] = false;
                $selectedIndexes[] = $idx;
                $urls_ar[] = $cookie['value'];
                if (count($urls_ar) >= $maxConcurrent) break;
            }
        }
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($cookies, JSON_PRETTY_PRINT));
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
    $urls[] = 'http://'.$serverIP.'/newupdate/xavi-test.php?c=' . urlencode($c);
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
$cookies = json_decode(stream_get_contents($fp), true);
foreach ($selectedIndexes as $idx) {
    $cookies[$idx]['isFree'] = true;
}
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($cookies, JSON_PRETTY_PRINT));
flock($fp, LOCK_UN);
fclose($fp);

$endtime = microtime(true);
$duration = $endtime - $starttime;
echo "Execution time: " . $duration . " seconds";
?>
