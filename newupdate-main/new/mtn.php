<?php

if (isset($_GET['number'])) {
    $number = $_GET['number'];
    if($number == '737943880')
    {
        die("Mf af");
    }
    $mtnkey = "rchgprod1753735490078" . rand(111111, 999999);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.mtn.co.za/api/recharge/v1/authenticate-customer/' . $mtnkey . '/MTNSite');
    // curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/cookie.txt');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'host: www.mtn.co.za',
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, 1);
    $token2 = $data['token'] ?? null;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.mtn.co.za/api/recharge/v1/summary-balance/' . $mtnkey . '/MTNSite?msisdn=27' . $number . '&balanceType=ALL');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/cookie.txt');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json, text/plain, */*',
        'Accept-Language: en-US,en;q=0.9',
        'Authorization: Bearer ' . $token2 . '',
        'Connection: keep-alive',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-origin',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
        'sec-ch-ua: "Not)A;Brand";v="8", "Chromium";v="138", "Google Chrome";v="138"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
    ]);


    $response = curl_exec($ch);

    curl_close($ch);
    $data = json_decode($response, 1);
    $data = json_encode($data, JSON_PRETTY_PRINT);
    ;
    echo $data;
}
?>