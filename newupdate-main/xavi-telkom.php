 <?php
//sleep(rand(30,60));

date_default_timezone_set('Africa/Johannesburg');
$current_time = new DateTime();
$check_time = new DateTime('04:00'); 
$check_tim = new DateTime('12:00');


//Business of the day

require_once('Tools-mtn-v2.php');
// while(true){
system('cls');
$uA = RandomUa();
$scoreTarget = TargetScore();
$number3 = GetTargetScore(1);

// if ($number3>=400){
//  sleep(rand(10,90));
// }



    




$cookie = 'XSRF-TOKEN=eyJpdiI6ImFXZnF4MFFOaE9oUHppT2FFMzlBQmc9PSIsInZhbHVlIjoiamNxRDlYUXJzaG5FZzVVUUw2QXoyTnNraW9FeEM1SHJxb2xzMUJ5MmVLTUIzWnpyRi8rYm1FSUVyWlBjeWZUbm9ZTkg1aFZMZHFrOVIvZXE3MnlMQjdycm15Qkg3aEJ0ZE9VeklEZ1JVMVJ1QlpHdTNLem82eElnRUtncmFibUoiLCJtYWMiOiIwNDI2ZDAzMGFjMWZiOGY0YzA2ZTMxYWUwYTQ1OTYyMjlhM2NjODU2NjcwMjcyNjQ1ODJlYzFlOTE0MzE3OWQwIiwidGFnIjoiIn0%3D; yello_rush_session=eyJpdiI6IndCSFNqVGYrZFNIMzQvL2ZUZXNLcFE9PSIsInZhbHVlIjoiOUtXb1pPdk12TlFvOHRIVDE5OWsxTk1DSVVacHJUWk5BNVlNZ0IyUEl6dE5PSWtJSnZHZG52OTRwbERqZG82SmxJRzU0TndaS1k5WEI3RGQ3SmRDWHZnYUlFR25vZUp5OHF1ZHo3SDJwZUlVemFnOExVcjV5Y2s3N1FBT2Z2bmUiLCJtYWMiOiI4OWUzMzE3YmNkMDI0ZTFhYjJkNWMwMzI3OTE0NjI1ZDExYWVjNjE5NWY1MmU2MjBjNmQwYjA4NTJkZmE4ZTg5IiwidGFnIjoiIn0%3D';
        

// $MAX_SCORE = 6000;

$pos = GetPosition ($cookie);
$b4Score = GetTargetScore($pos);
echo "\nOur target score is: $number3 at pos $pos";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://yellorush.co.za/play-now');
        // curl_setopt($ch, CURLOPT_PROXY, 'http://p.webshare.io:80');
        // curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'ofzhbdla-rotate:5hgqeorbbfwm');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Cookie: '.$cookie,
            'Pragma: no-cache',
            'Host: www.yellorush.co.za',
            'Referer: https://yellorush.co.za/',
            'Sec-CH-UA: \"Safari\";v=\"15\", \"AppleWebKit\";v=\"605\"',
            'Sec-CH-UA-Mobile: ?1',
            'Sec-CH-UA-Platform: \"iOS\"',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: '.$uA
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $curl = curl_exec($ch);
        $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                    
        curl_close($ch);
        $query_str = parse_url($redirectedUrl, PHP_URL_QUERY);
        parse_str($query_str, $query_params);
        $unique_id = isset($query_params['unique_id']) ? $query_params['unique_id'] : '';
        $game_id = isset($query_params['game_id']) ? $query_params['game_id'] : '';
        $sigv1 = isset($query_params['sigv1']) ? $query_params['sigv1'] : '';

             if (empty($unique_id)){
                 return;
             }

        // echo "<br>Uniquie_id: $unique_id<hr>";
        // echo "<br>Game_id: $game_id<hr>";


        ###################
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://yellorush.co.za/new-game-check-user-status/'.$unique_id.'/'.$sigv1.'');
        // curl_setopt($ch, CURLOPT_PROXY, 'http://p.webshare.io:80');
        // curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'ofzhbdla-rotate:5hgqeorbbfwm');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array(
            'Host: www.yellorush.co.za',
            'Referer:'.$redirectedUrl,
            'Sec-CH-UA: \"Safari\";v=\"15\", \"AppleWebKit\";v=\"605\"',
            'Sec-CH-UA-Mobile: ?1',
            'Sec-CH-UA-Platform: \"iOS\"',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: '.$uA,
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $curl = curl_exec($ch);

        // Separate headers and body
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($curl, 0, $header_size);
        $body = substr($curl, $header_size);
        curl_close($ch);

        
        
        $x_power = X_Power($header);
        echo "\n<br> X-Powered-Version: $x_power\n";



// Adjustable score range for leaderboard placement
$scoreStart = 49679; // lowest score to consider
$scoreEnd   = 49690; // highest score to consider

// Skip if this cookie already holds a top 10 position
// Refresh position to ensure it's up to date
$pos = GetPosition($cookie);
if ($pos > 0 && $pos <= 1) {
    echo "\nAlready in top 10 at position $pos, skipping request.";
  sleep(rand(120,340));
    exit;
}

$success = false;
$currentScore = null;

// Build and shuffle the score list so each score is attempted once in random order
// $max = 33642;
// $count = 10;
// $min = $max - ($count - 1);
// $scores = range($max, $min);
// shuffle($scores);


$max = 4900;
$count = 10;
$step = 100;

// calculate minimum based on max, step, and count
$min = $max - ($count - 1) * $step;

$scores = range($max, $min, -$step); // negative step for descending
shuffle($scores);

print_r($scores);

foreach ($scores as $score) {
    echo "\nTrying score $score";
    $increment = 1;
    $uA = RandomUa();
    $memory = validate_request($x_power, $score);
    $x_power = generateRandomDivisionData($score, $redirectedUrl, $x_power, $memory, $increment, $uA);
    $pos = GetPosition($cookie);
    $currentScore = GetTargetScore($pos);
    echo "\nLeaderboard value: $currentScore at pos $pos";
    if ($currentScore != $b4Score && $pos > 0 && $pos <= 6) {
        $success = true;
        break;
    }
    echo "\nScore $score failed to update.";
}

if ($success) {
    echo "\nLeaderboard updated with score: $currentScore";
} else {
    echo "\nFailed to update leaderboard";
}
