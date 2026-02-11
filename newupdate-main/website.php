<?php
session_start();

$password = 'flashkidd';
$message  = '';
$success  = '';

error_reporting(E_ALL);
ini_set('display_errors', 1);

function fetchInfo($cookie, $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: no-cache',
        'Connection: keep-alive',
        'Cookie: ' . $cookie,
        'Pragma: no-cache',
        'Referer: ' . strtok($url, '?'),
        'Sec-Ch-Ua: "Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
        'Sec-Ch-Ua-Mobile: ?1',
        'Sec-Ch-Ua-Platform: "Android"',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36'
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $html = curl_exec($ch);

    if (curl_errno($ch)) {
        file_put_contents("curl_error.txt", curl_error($ch));
    }

    curl_close($ch);

    // Save full HTML to inspect the response
    file_put_contents("debug.html", $html);

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $phoneNode = $xpath->query("//span[contains(@class,'phone-cont')]");
    $phone = $phoneNode->length > 0 ? trim($phoneNode->item(0)->nodeValue) : '';

    $nameNode = $xpath->query("//div[contains(@class,'user-name-new')]//h6");
    $name = $nameNode->length > 0 ? trim($nameNode->item(0)->nodeValue) : '';

    // Save what it extracted
    file_put_contents("debug_info.txt", "Name: $name\nPhone: $phone");

    return array('phone' => $phone, 'name' => $name);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered = trim($_POST['password'] ?? '');
    $cookie  = trim($_POST['cookie'] ?? '');
    $type    = trim($_POST['type'] ?? 'voda');

    if ($entered === $password) {
        // $file = $type === 'mtn' ? 'cookies-mtn.json' : 'cookies.json';
        // $url  = $type === 'mtn'
        //     ? 'https://www.yellorush.co.za/my-winnings?display=tab3'
        //     : 'https://gameplay.mzansigames.club/my-winnings?display=tab3';
        if ($type == 'mtn2'){
            $url = 'https://rush-games-telkom.yellorush.co.za/my-winnings?display=tab3';
            $file = 'cookies-newgame.json';
        }else if($type == 'voda'){
            $url = 'https://gameplay.mzansigames.club/my-winnings?display=tab3';
            $file = 'cookies.json';
        }else{
            $url = 'https://www.yellorush.co.za/my-winnings?display=tab3';
            $file = 'cookies-mtn.json';
        }
        $list = json_decode(file_get_contents($file), true);
        foreach ($list as $entry) {
            if ($entry['value'] === $cookie) {
                $message = 'Cookie already exists';
                break;
            }
        }

        if ($message === '') {
            $info = fetchInfo($cookie, $url);
            if ($info['name'] && $info['phone']) {
                $list[] = array('value' => $cookie, 'isFree' => true);
                file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                $success = sprintf('%s (%s) has been added.', htmlspecialchars($info['name']), htmlspecialchars($info['phone']));
            } else {
                $message = 'Invalid cookie';
            }
        }
    } else {
        $message = 'Incorrect password';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Cookie</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(145deg, #1f2937, #111827);
            margin: 0;
            padding: 0;
            color: #fff;
        }
        .container {
            max-width: 420px;
            margin: 80px auto;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }
        h2 {
            text-align: center;
            margin-top: 0;
            font-weight: 600;
        }
        label {
            display: block;
            margin-top: 16px;
            margin-bottom: 6px;
        }
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border-radius: 6px;
            border: 1px solid #444;
            background: #111827;
            color: #fff;
        }
        textarea {
            height: 80px;
            resize: vertical;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background: #10b981;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #059669;
        }
        .message {
            color: #f87171;
            text-align: center;
            margin-top: 15px;
        }
        .success {
            color: #4ade80;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Add Cookie</h2>
    <?php if ($message): ?><p class="message"><?= $message ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
    <form method="post">
        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <label>Cookie:</label>
        <textarea name="cookie" placeholder="Paste cookie here..." required></textarea>

        <label>Type:</label>
        <select name="type">
            <option value="voda">Vodacom</option>
            <option value="mtn">MTN</option>
            <option value="mtn2">MTN 70R</option>
        </select>

        <button type="submit">Add</button>
    </form>
</div>
</body>
</html>
