 <?php
function GetTargetScore($position_)
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