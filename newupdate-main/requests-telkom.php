<?php
//echo "cool down";return;
system('sudo rm -rf cache');
require_once '/var/www/html/newupdate/Zebra_cURL.php';
$curl = new Zebra_cURL();
$curl->cache('/var/www/html/newupdate/cache', 59);
$curl->ssl(true, 2, '/var/www/html/newupdate/cacert.pem');
$curl->threads = 10;
$curl->option(CURLOPT_TIMEOUT, 900);
@unlink('cache');

$starttime = microtime(true);

$c_values = [

"XSRF-TOKEN=eyJpdiI6IjYzcW5GMWxGeVBnbUY1WFp2QVQ2L3c9PSIsInZhbHVlIjoiNDgwM1hvNU9ZdWhxRklLZFRRaTVjbmttWDZNdGxnMGRUaDhwdHJPS0sxMm1KRndZZVRHayt1ak9NSU1IN3pqNFZxZmxybmM5anh3endMRGdMQ1JDeklIYUhwTjdDOEpBbmM2amZiUlFvRDJMclIyUjZDOTdWMnhTT1B5Y2QzeGMiLCJtYWMiOiJhNzdhNGJiNzJjNTFhYzNjMzU0MjdkOGRjZjcwNWYxYjRlYzA1NDA2Y2I3NGY3NTAwY2FlNDNkNzFjOWI3NmU5IiwidGFnIjoiIn0%3D; wozagames_mzansi_games_session=eyJpdiI6IlRVNkxhbUJGVlpERFloWHpLbzR1cHc9PSIsInZhbHVlIjoiRDY3UDQ1bVJSeWptV0xuU1V6c21LSWRERWxlQ090NitQeXZUM0pxMG9iNkl6VzEyU1Jib21qVCtqc24vcWg3dVdiNEtiZVlhSHltL0RRMnZKUWtEY2xqc01yQ1dwdUxGYUFrc3diSHBJNVg0Y2FoamZnL0VKeW5sdWdNWkxzRmEiLCJtYWMiOiIwYmY4YmEyZWY0NzI0ZGIxMjRjYTExYjY3OWI0MGM5Yjg2ODBjMmIwOWE4YmEzYzdkYTQyZjUzNWIzZGNkZmFjIiwidGFnIjoiIn0%3D",

    ];

$urls_ar = array();

foreach ($c_values as $c) {

    
  $url = 'http://167.71.187.98/newupdate/xavi-telkom.php?c=' . urlencode($c);


array_push($urls_ar, $url);

}



$curl->get($urls_ar, function($result) {
    if ($result->response[1] == CURLE_OK) {
        echo 'Success: ', $result->body;
    } else {
        echo 'Error: ', $result->response[0], PHP_EOL;
    }
});

$endtime = microtime(true);
$duration = $endtime - $starttime;
echo "Execution time: " . $duration . " seconds";
