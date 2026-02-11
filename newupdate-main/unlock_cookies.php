<?php
$cookieFile = __DIR__ . '/cookies.json';
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
$unlocked = 0;
foreach ($cookies as &$cookie) {
    if ($unlocked >= 4) {
        break;
    }
    $cookie['isFree'] = true;
    $unlocked++;
}
file_put_contents($cookieFile, json_encode($cookies, JSON_PRETTY_PRINT));
echo "Unlocked {$unlocked} cookies.\n";
?>
