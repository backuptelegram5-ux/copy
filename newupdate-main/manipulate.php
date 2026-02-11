<?php
@unlink('cookie.txt');

$cookies = ['XSRF-TOKEN=eyJpdiI6IngxSW1QQ1h0cDkxM0JBajlUOVNGbHc9PSIsInZhbHVlIjoiQXJXS0tGYWNtUUtQQ0dTeUNQdElFMXVENm9ITS9aejZoMGdpVXdCdFhLdHBRWWFCVis3UmpJSGdmcjg4VjZ3SnNXRGZqTU1hR0RwdnVoMkVvWG9jb1VRQXpKL3UvZmd5bjRGcFBkUzlOSUlUaUdDeGQ1QWxJWUFOTk4yV0dqdnIiLCJtYWMiOiIyNjA1MmExNjBhYzA2OTJkZDVhMmM1ZDU5NDVhODdlYzVlOTY2YTMyOTRlMTAyNGFiZjliMjNmNjM4NzAyZGVkIiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6IjBOVUdJNDJmRXh6bEhtTFJ4ZU9PWnc9PSIsInZhbHVlIjoicnI1b3FndjhIRXpKYlNzQ3lQbVVrcnZ0SDJnaU1PSkJrelBzaHJsRWN0eTV2dEE0YUNHZEtiaENRenVoTzdvNUpQZncrYnVPYTk1SUYxMVowMkNLbXF1SEJJajBPZTVlK0hOZEdKSWVGZWRIR01uVExZaFZ2MXp6Ny9MbEZ3aU8iLCJtYWMiOiI3NTQ3ZDhkNTVkN2EyNzM2YWQ2YmYzYTcyMWRmYTkxNDhiZGQzZjdkMDAzM2Y5ZTQxNDNhNzExMzA1ZjQzZGNjIiwidGFnIjoiIn0%3D' => 270825982860,

"XSRF-TOKEN=eyJpdiI6ImxyRzJ0SVJZRm92MU1GT2laZTF1RVE9PSIsInZhbHVlIjoiTWd5eFg4a2NoUGE4SUhiaVYvSHlHVUpNZ0w2ZFZ3eVFiMDBPVGZpS3c1cHU1YjA2NERuYjhyUmVSdnJNUSt1bmUwMlRXR01HZFlLcCthTXpmakg3Y0NiMXpKWWZsNHpoVE1hNnlwb29iRjNoYlQwOEkxSXAvS2ZYRVdjSnhNZGciLCJtYWMiOiIyOTA1YzZjZjk0MDJiMWNmOTljM2Y5M2Q5ZDBjZjI1YzY3ZWZjNTc1Mjk3NTU4ZjJmOTZiNjYwMTRlMWNkMzlmIiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6IkM5WVlLWXRMWUJZUGxTczBKZmhiOWc9PSIsInZhbHVlIjoiU3BQTlIxRHhyTzdxUFhQaml3VytJTm83VU9aamxLQS9TOU9oemxtYmRkalo0cDhpb21MbkNjaUNHQXk4VXhOYnhSeWFSZENrQ2R1NG4vMm14KzM1S2pQQmtvUy9yRGpHcFFmMVR6QkhhM1ZtNGpDSVN4K3BEd3BGRDhUUVFTTTgiLCJtYWMiOiI2MzBiMWUzMWE5NWE3Nzk4OTAzNjI5OTI0N2UyNDNlNzIwYzRiNDhiMjI4NmE3ODJmZWUxYzNkZDc3MzMyYjUwIiwidGFnIjoiIn0%3D" => 277063768248,

"XSRF-TOKEN=eyJpdiI6IlFXRXlxUEFHdG5zQ0RpL1YzMlI0cEE9PSIsInZhbHVlIjoiMitLWk44V3E1bVc3Mk9zQ1NicWpzVTRadjVkQ0pQaHppMEk5THkzcUhRQThPL0pjMVpHb0I1QkZsSnN6R2xPb0UxdmhBMklscDJyVHVKU3hFaUpEang3TTRSdUt4a29BOVFBU3FTK0RoZVZEdGo4U1BuWWpFZEdMZCtMQ0Y1bWciLCJtYWMiOiJmMjZiYjQyMjFiMDg3NWRiZjA4YTA1N2MyNDdiNTM2NjJiYjFiYWMwMzhlMmQ5NWQxMDc5NWMwMDcxMGNhYzczIiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6InVaemV5TFdKSmNLQXZtdjdUNVgxZ3c9PSIsInZhbHVlIjoiWUR5L3Vqamw0enNWem9xbWd6SEduYW1CaU9Wd3Y4WElFenkxSzRJYk4wWG0xY25wQW10MGlrZVBMc2NjNzFpYnFDRXBXZkU5SVoxNExRUGJKRFlhZmY3aEl2QThja1piOThYdFkvc1dUVEUvak05QUNpY3lTN1RrOEZQR21kVkEiLCJtYWMiOiJlOWU0OWYyMjQ4MGFjYTUzODY0YzgxNjAzMjc3MDg2NTVlMmRiZTgyNDViMGVkY2Y0NzE3ZjllYjRkN2IxNmMwIiwidGFnIjoiIn0%3D" => 270766334122,

"XSRF-TOKEN=eyJpdiI6IjBsQkRJSm5GYm9ITEUwWWRUZ05zTmc9PSIsInZhbHVlIjoiOHV4K0xoeHZRV0F5czVHZFRXUGZvR0ZHbnllVjUvRVdtSGkvMnZKQ0xwZXlPZVFyaUQvL2RrM3BwY0ViVHNkSVhMM0xBNCtQTWR2NFZjZDlGZTBUMTR1WDh1VG53L0N3UUc0R2QvMVZCSUZZWTNYNlNGTnJiZjVmRVZHVWppYVIiLCJtYWMiOiIyM2FmM2IwOTRjNjhkNGYyMGVjYjVmOTM3YjQ5ZjZmMjRiN2ZjNGFjZDkyMTE5NGEwZjhhNjJkODRjNDAyZDg4IiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6IkNLaUhqcEM0VGZsay9BWHB2azBMVEE9PSIsInZhbHVlIjoiRzVSTTlVUm8vY0UyZTErb2FzTkdpVjZIbThqU0ltRFFub1FJK3R4b1RndG5PRk5lY0VCVmhuUDRlWG4xTEhIeXhOUzVEcUcxenJ0QTRVWTUvSDE3clJEV3BQdHViS3dDL1VuUzczTHZIbXFlUURLazZ6R2xJV2tlTGgyaGJNa2ciLCJtYWMiOiI5ZmY2YTQyNGY4MmY5M2RkNjgxNzE5Nzg5MmQ4MjVmNTVmOTg0ZDczNzBiMmQ5ZTE0YzMyZDQyZTYzOWM5ZTVmIiwidGFnIjoiIn0%3D" => 270725248866,

"XSRF-TOKEN=eyJpdiI6Imhxa1pyMGRXZG1MR0tEalZLc2pYaGc9PSIsInZhbHVlIjoiNGhHRnVYWEsxR3dwYzUvT09hbGZWbE1tczhaeXlXeHd2Y25PNnlHcFNTcXZ1SGN4RlgrcjF4aDFlMmZ1bis5eDZ6UHZNQWU4TDBkckt1SFAwMU44ZW9xVytFeUtxTGFnOWZzckJSYmNqQ00vaFZVMkRQc2w0aHUzeVc2bmFhaTIiLCJtYWMiOiIyYjRlMmM1ZTYxNTM5OGIzNzI3Yjg1MjUyYmQ0N2UyOWQzNmIyY2QyY2U4NjE0ZWEyMTlmMDRkZGUyMGQxNzc3IiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6IkRXSnkrOG5NeHJtZmk5RjJBUmlDb1E9PSIsInZhbHVlIjoiNGdkWHRwaTdKQ2htUVdoc3VEZGlmRWMvd0VlZmN4MHBKZmtxUmlKeGhET0p4OHU5RG9vajg3U1VodkNoK3JTWkpVK2pKcGprc2dBaU1VQXRxakpITXpIaUpWT2w5M3NxTGQxd3VhWnQyYjBMRHJDcTFRQ3NrVkFLY3NKZXZUZDkiLCJtYWMiOiI5OTRlZWU0Yjc1OTA3ZGQ5ZmJhMzJhZWIyYTg4ZTEwNjQ5MjBlZmVjYTc5Y2ZlZDI4ZjliMTU5Njg1MzlkYWY2IiwidGFnIjoiIn0%3D" => 270827286570,

"SRF-TOKEN=eyJpdiI6IjdTMitrK2pRM0hvQzNTTDJGNlVnb1E9PSIsInZhbHVlIjoiRkxWMnNCcTFSQ2RsVGphTjRVeUlaOGxScU4xYVBIdlRtaW5oaEpLeFgxZ3JCcllZWkJzZnVuN2I3SjdJVTJpT3RaNmZiMjFtUWYxR0FsT3I4SDF2Z0FZOFYyZEJOVXhtMzhXRWdYcTRnT0I4aHFNaTJaS1ZZT3Z3dWVJNmpoWE4iLCJtYWMiOiI2NGFiODA5Mzg0OGQyY2JjNDNiOWMwZTg4ZDFkYjc5NDBiZDhiZTFjYTM1ZjAyOTY4Yzk0MjMzOTJmODJjNGI3IiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6Ik95c2p0MzhPaCtBNTBkSUdFMU9UTEE9PSIsInZhbHVlIjoiK1ZPV0IwejFmK1hhbElwOHFKQlpDdmhrV282QnpJR3g3TEN6K2JId0M0M25iMHlmeCttM1VvTlNBS28xQ3ZaSFJjbWd6RStDU0xKQTc4NDdQc1hPQlk1TFhOWElaMzYyUzJTZmgzRTdaRmRabDFuSzZtK09waE1Mb0pvaTdmM2IiLCJtYWMiOiJhMzYzOGY3Y2YyZjhiODA4MWZkZmQ1MDEyZWFlMzVkODZkODRkOTRlNTI5OTAyNGMxZWJkNmVkMDdkOWJmNjk5IiwidGFnIjoiIn0%3D" => 270829775873

];

foreach ($cookies as $cookie => $number) {

// $number = 270647319547;
echo "<br><hr>Number; $number";
$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://play.mzansigames.club/profile/edit');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Cookie: '.$cookie,
            'Pragma: no-cache',
            'Referer: https://play.mzansigames.club/',
            'Sec-CH-UA: \"Safari\";v=\"15\", \"AppleWebKit\";v=\"605\"',
            'Sec-CH-UA-Mobile: ?1',
            'Sec-CH-UA-Platform: \"iOS\"',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
        // curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
       $curl = curl_exec($ch);
        curl_close($ch);

        // Extract the _token value
        preg_match('/name="_token" value="([^"]+)"/', $curl, $tokenMatches);
$token = $tokenMatches[1];

// Extract the user_id value
preg_match('/name="user_id" value="([^"]+)"/', $curl, $userIdMatches);
$user_id = $userIdMatches[1];

// Adjusted regex to extract the player_name value
preg_match('/name="player_name"[^>]*value="([^"]+)"/', $curl, $playerNameMatches);
$player_name = $playerNameMatches[1];

echo "<br>Extracted token: " . $token . "\n";
echo "<br>Extracted user_id: " . $user_id . "\n";
echo "<br>Extracted player_name: " . $player_name;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://play.mzansigames.club/profile/'.$user_id.'');
                curl_setopt($ch, CURLOPT_POST, 1);
                $headers = array(
                    'Cookie: '.$cookie,
                    'Content-Type: multipart/form-data; boundary=----WebKitFormBoundaryj9Skkb5BiGEP3dSt',
                    'User-Agent: Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36'
                );

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
                // curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
                curl_setopt($ch, CURLOPT_POSTFIELDS, '------WebKitFormBoundaryj9Skkb5BiGEP3dSt
Content-Disposition: form-data; name="_method"

PUT
------WebKitFormBoundaryj9Skkb5BiGEP3dSt
Content-Disposition: form-data; name="_token"

'.$token.'
------WebKitFormBoundaryj9Skkb5BiGEP3dSt
Content-Disposition: form-data; name="profile_picture"


------WebKitFormBoundaryj9Skkb5BiGEP3dSt
Content-Disposition: form-data; name="user_id"

'.$user_id.'
------WebKitFormBoundaryj9Skkb5BiGEP3dSt
Content-Disposition: form-data; name="player_name"

'.$player_name.'
------WebKitFormBoundaryj9Skkb5BiGEP3dSt
Content-Disposition: form-data; name="mobile_number"

'.$number.'
------WebKitFormBoundaryj9Skkb5BiGEP3dSt
Content-Disposition: form-data; name="is_new_game_notification_on"

1
------WebKitFormBoundaryj9Skkb5BiGEP3dSt--');

$curl = curl_exec($ch);
curl_close($ch);

if(strpos($curl, "Profile Updated Successfully")){
    echo "<br>Done, new number => $number";
}

}