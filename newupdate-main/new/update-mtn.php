<?php
$cookieFile = __DIR__ . '/data/cookies-mtn.json';
if (!file_exists($cookieFile)) {
    fwrite(STDERR, "Cookie file not found\n");
    exit(1);
}
$data = file_get_contents($cookieFile);
$cookies = json_decode($data, true);
if (!is_array($cookies)) {
    fwrite(STDERR, "Invalid cookie file\n");
    exit(1);
}
foreach ($cookies as &$cookie) {
    $cookie['isFree'] = true;
}
file_put_contents($cookieFile, json_encode($cookies, JSON_PRETTY_PRINT));
echo "All cookies have been unlocked.\n";
?>
