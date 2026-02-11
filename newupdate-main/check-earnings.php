<?php

function fetchEarnings($cookie) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://gameplay.mzansigames.club/my-winnings?display=tab3');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: no-cache',
        'Connection: keep-alive',
        'Cookie: ' . $cookie,
        'Pragma: no-cache',
        'Referer: https:/www.gameplay.mzansigames.club/',
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
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $html = curl_exec($ch);
    curl_close($ch);

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $phoneNode = $xpath->query("//span[contains(@class,'phone-cont')]");
    $phone = $phoneNode->length > 0 ? trim($phoneNode->item(0)->nodeValue) : '';

    $nameNode = $xpath->query("//div[contains(@class,'user-name-new')]//h6");
    $name = $nameNode->length > 0 ? trim($nameNode->item(0)->nodeValue) : '';

    $rows = $xpath->query("//tbody/tr");
    $currentMonth = date('m');
    $currentYear = date('Y');
    $total = 0;
    foreach ($rows as $row) {
        $cells = $row->getElementsByTagName('td');
        if ($cells->length > 2) {
            $date = trim($cells->item(1)->nodeValue);
            $parts = explode('/', $date);
            if (count($parts) >= 3) {
                $month = $parts[1];
                $year = $parts[2];
                if ($month == $currentMonth && $year == $currentYear) {
                    $amountStr = trim($cells->item(2)->nodeValue);
                    $amount = floatval(preg_replace('/[^0-9.]/', '', $amountStr));
                    $total += $amount;
                }
            }
        }
    }

    return [
        'phone' => $phone,
        'name' => $name,
        'total' => $total
    ];
}

$cookies = json_decode(file_get_contents('cookies.json'), true);
$overall = 0;

foreach ($cookies as $entry) {
    $data = fetchEarnings($entry['value']);
    $overall += $data['total'];
    echo "Number: {$data['phone']} | Name: {$data['name']} | Earnings this month: R{$data['total']}\n";
}

echo "\nOverall earnings this month: R{$overall}\n";
?>
