<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function fetchInfo($cookie, $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Cookie: ' . $cookie,
        'User-Agent: Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36'
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $html = curl_exec($ch);
    curl_close($ch);

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $phoneNode = $xpath->query("//span[contains(@class,'phone-cont')]");
    $phone = $phoneNode->length > 0 ? trim($phoneNode->item(0)->nodeValue) : '';

    $nameNode = $xpath->query("//div[contains(@class,'user-name-new')]//h6");
    $name = $nameNode->length > 0 ? trim($nameNode->item(0)->nodeValue) : '';

    return ['name' => $name, 'phone' => $phone];
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index = (int)$_POST['index'];
    $file = 'cookies-newgame.json';

    $list = json_decode(file_get_contents($file), true);
    array_splice($list, $index, 1);
    file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

function renderList($file, $url) {
    if (!file_exists($file)) return;

    $list = json_decode(file_get_contents($file), true);
    foreach ($list as $i => $entry) {
        $info = fetchInfo($entry['value'], $url);
        $label = $info['name'] && $info['phone']
            ? "{$info['name']} ({$info['phone']})"
            : "Invalid or expired";

        echo "<li>";
        echo ($i + 1) . ". $label";
        echo "<form method='POST' style='display:inline'>
                <input type='hidden' name='index' value='$i'>
                <button type='submit'>Remove</button>
              </form>";
        echo "</li>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cookie Manager</title>
    <style>
        body {
            background: #0f172a;
            color: #e5e7eb;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            background: #1e293b;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        form {
            margin: 0;
        }
        button {
            background: #ef4444;
            border: none;
            padding: 6px 12px;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        h2 {
            border-bottom: 1px solid #334155;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <h2>Cookies List</h2>
    <ul>
        <?php renderList('cookies-newgame.json', 'https://rush-games-telkom.yellorush.co.za/my-winnings?display=tab3'); ?>
    </ul>
</body>
</html>
