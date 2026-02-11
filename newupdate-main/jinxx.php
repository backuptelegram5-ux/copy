<?php
date_default_timezone_set('Africa/Johannesburg');
$current_time = new DateTime();
//Business of the day
require_once('Tools.php');
//system('cls');
$scoreTarget = TargetScore();
$number3 = GetTargetScore(1);


echo "\nOur target at num3 is: $number3";


// $cookiez = ['XSRF-TOKEN=eyJpdiI6Ik1MYXV0UTY4ay9uWGZqeGdQSWdpYVE9PSIsInZhbHVlIjoiWDkxc2NjdHk5K2FNays4RDZGVEhIb2JIY3RJZ1FxeVVQVE5Tb3gzaXM5QjV1NGNVbG9KYmtBVkExTXRsbE1LaUJnVXRucTZiWjEvZG54SUIxaytDMU1haW1vdk4vK0kwQ3diTFc1ODRTOHd4NGNYZkFqSm9RSzJvTzdvWGxTeFMiLCJtYWMiOiJkMWQ0YjIwNTdlOGVmMzczYmQwYTJhMjExNTNlMGYyODdjODBhOWM1Y2M4MjQ3ZjMzNDAxYTVmNWQ1MTViZGQwIiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6ImRpQTBwSEkxZ3kvQnI5Y2hUbjVTbnc9PSIsInZhbHVlIjoiVzdxMEwrbFVJNndFdGdvY2NWeU9XQW9VVXoyTmJkTjIwMGlXeXJNYTQxK2ZmK1JVNUZvNGc1Sm53TUtMS25CQ1p3MTk4ZzAyUkVpOHpBYXFPb1pJTDJzZFBLSUVkWE5nSHliSE42eXB6cWdhRm0zWDZiY0xCT3JYT1NaVHB5UXYiLCJtYWMiOiI5YmYzZTM1OWZjM2ZmNzQwZjhlZjIxYzYwMWJiNTAyM2JmZTMzZTY0ZGVmMmFjMjgzNThmMmQ5ZTRmYzRjOWUxIiwidGFnIjoiIn0%3D',

// 'XSRF-TOKEN=eyJpdiI6Im13T3JBNUo1RGoxT203eW04UkRsaUE9PSIsInZhbHVlIjoiMHJiVDNlV2V6QXVoQllYNlhRZUZ0ajlYTW1CR3dHUUVGU2p6QWhFSm15bkNUdnQwcnlnSDJPc0RYTUtWT0daM2UrMStObTIrWi9oME5rUy9KcStGc3pzQWVwZWVNSW50V0dPaDN2aU1wY0swMTNpUTEzVUZxMmNTNXczOXMwaGsiLCJtYWMiOiJlOTc4OGQ2NDc3ZjM5YzUyNTg4OTcwODMxNjExNDBiNDlkODdmMTM5ZGU3MmQ0MTcxNmQxMGRhOGM4ZTIxYzM5IiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6InNYbVNHZW5TMmxpemVHekNYU3NZZHc9PSIsInZhbHVlIjoiREpzS001eWFnbVMwY2RJUVEvQlFsQVA3eTVCTVFsbEZmK0pkZ2p3N0ZFY2VvckE5Z1NMNXhaOVVFL1N1cFZkU2xianc2UHNpaTB5clZFd0N0MjNrNXo5dlpxT2c0dW9JY0dWUjJna1hmNHlxZDMvRnI4NkM2RkNzTElWb0dxSkQiLCJtYWMiOiIyMjAyNGI0ZTczZmRkYmQ1NDM0OGE0NjU3NzNiZjA2NDViNzI3YmEzMzdmZTMxZjhiZjE2OWQ1YzdjMmJjODY2IiwidGFnIjoiIn0%3D',

// 'XSRF-TOKEN=eyJpdiI6Ii9CaUJoekwvMjRjNmdKUWFva3R3SlE9PSIsInZhbHVlIjoiOU91djlVMS82R0YvVWo3SUxRTVk5SUloU2I3dkNNS0dJUzhTNXVwdjJkcDllWG84dURYM3RVcC91QXQ4UTdNTERjZUErWE1Xd1Y0b0c4VUwvZWZSMnU0OUN2UjFCYUVlZWdDWUhkN1JsQkx3NWZWOHppczd5Z2pEbkN2bFJINWciLCJtYWMiOiJkNDE2MzYxMmU5NTU3YmEzMzczODFjMTUxMWM3MTJiNjA0MWI0OWM1YWQwMWYxNmU4ZWZmMTUzNGU2YjE5YjI4IiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6IlpKNGhEMGgwNy9VOEhpTzVXZDN0aFE9PSIsInZhbHVlIjoibjc5ZlRZWllmRGNBM3hCTnBFSWY1Vy9kenlWQnZQNWZBZXhGVStNbm9pa25xMGxQbmIrcTlNaDd3bTJBeit3Y0NjVC9IMjJYNnZReVBrYis4S212cGR5R1lsL3dpREF0RTQ3d2N2aFE4WnlOM3l3anlheEcrTy9FcnA5OXVQZVgiLCJtYWMiOiI0ZGQ0YzU3YzE1NmU5NzA4NTZmMDY3ZGVhNjEwM2RkMTI4OTA4ZjJjNDcxNDgzMDhhOWNkYzA4MjMxMTkyYjU2IiwidGFnIjoiIn0%3D',

// 'XSRF-TOKEN=eyJpdiI6IlRQcHhmTi9zcVhwNWl5clU5ckh0eHc9PSIsInZhbHVlIjoiL21zVlBIYWZlcWRIb0pMWThqVjFjdGlnS0NFTENaQVROUTZPbWRiQzNSM1Z5OFcrZG5VL01JNHloYzRsUUFIVEJ1S29YTmdXT24rM2UvM2pEVlN6L3Z4VUE2cGlIQVRnT3ppcFJTRkFSS0M3YnBTcWNzMy9NekdYbElNWC9oQ3QiLCJtYWMiOiIyNzkwNGFmYzVlYTU2ZGEwYTFjNmQyMTEzNTU5YzkzNmYwNjFlOWM4MjhlZjhlZDc4YjE0NjFlOGRjYTllYWUwIiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6ImtGMy9LMGJ5UUk5aldEQlZSTGRjN2c9PSIsInZhbHVlIjoibXg0SUJuV0d0UDFFM0J0SnVydnJlaFNCZG5rRkpjenRjSENHSjZjWVpuQmlZQ1pudGo0M3NBOWszK2YxUzZOTkZvU1ZlUkt0c2x1TCt2bkFveStwMGltaC9PbytjbnkvRUx1RFlSWGtkd2kwMUNpeWRtZVFxemFDV2l5TkNIS2giLCJtYWMiOiJiY2UyYTAxNmEwMzY1YjZkZGQ4NDI5ZmJjNzZkZjIzOWNhMWY5M2I1YjJhNDA1MTk3NzI2YmFlMDIyYmExYTk5IiwidGFnIjoiIn0%3D',

// '_ga=GA1.1.1739939425.1731436752; _ga_47GFPLWSMZ=GS1.1.1731436752.1.0.1731436758.0.0.0; XSRF-TOKEN=eyJpdiI6InlscWlIaWdtckRuQUNyMFJzRGNGcFE9PSIsInZhbHVlIjoiSTRTc3crT2Uxb0FGQmdFb0pnVngrVjkxeTRmMy9QeWpsWlFqWVV4dVpsSlhoT1pPRFJnb3FmblluQ0s3WVd5MEw0MndFSysrQ0ZqRmg5NTI2cUJQSTFxdGpOR0JGZ2VEL1BJVHF6RkE2ZkFBMnFvQnlRSVdyWFlBbWFIWCtSTXoiLCJtYWMiOiJjOTE0NDMxYTY2NzYzZmIyNDU4NDMzMGNjNGZiMDhiODE3OWQyOGNlNDRmMDBhZDgwY2Y1MzJlOTVlNjdjZGY0IiwidGFnIjoiIn0%3D; vodacom_mzansi_games_session=eyJpdiI6InF0YmRkZCtPZXRnd2x5UUtkdGNCOEE9PSIsInZhbHVlIjoieER6WXQvQnNOa0xmalNkNTlaY1pHZ2k1NkhGQ1BLWndiTE5wWWY0NWV0b1BjaUt1RENuOC91YkZpUlRndTVJNWJ0d0NhejVhbGlFSUlkTDdlcGRnekpxb0tBZytEaFV6SHlHc0ZUSzJ0amdoeWdwbi93T2lOaUVrYUNTRldQeE4iLCJtYWMiOiI2ZTQ5YzYwZjdhZmY0MjJjZWQ5YjRjNzg3NWY3ZGYzYzE0YTNmZDVkYzRjYjljNmQ2YTQyOTdmYzA1NGRhZjY4IiwidGFnIjoiIn0%3D'];
//foreach ($cookiez as $cookie){

        $cookie = isset($_GET['c']) ? trim($_GET['c']) : '';
        
        //echo "Cookie; $cookie";
        $pos = GetPosition ($cookie);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://gameplay.mzansigames.club/play-now');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Cookie: '.$cookie,
            'Pragma: no-cache',
            'Referer: https://gameplay.mzansigames.club/',
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
        $curl = curl_exec($ch);
        $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                    
        curl_close($ch);
        $query_str = parse_url($redirectedUrl, PHP_URL_QUERY);
        parse_str($query_str, $query_params);
        $unique_id = isset($query_params['unique_id']) ? $query_params['unique_id'] : '';
        $game_id = isset($query_params['game_id']) ? $query_params['game_id'] : '';
        $sigv1 = isset($query_params['sigv1']) ? $query_params['sigv1'] : '';

       // echo "<br>Uniquie_id: $unique_id<hr>";
        //echo "<br>Game_id: $game_id<hr>";

        ###################
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://gameplay.mzansigames.club/new-game-check-user-status/'.$unique_id.'/'.$sigv1.'');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array(
            'Referer:'.$redirectedUrl,
            'Sec-CH-UA: \"Safari\";v=\"15\", \"AppleWebKit\";v=\"605\"',
            'Sec-CH-UA-Mobile: ?1',
            'Sec-CH-UA-Platform: \"iOS\"',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Upgrade-Insecure-Requests: 1',
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
        
        
           if(($pos >= 1 && $pos <=2 )|| $pos == 0){
                $score = rand(5,rand(7,10));
        }else{
            // $number3 = GetTargetScore($pos);
            
            
             $score = rand($number3,($number3+rand(5,10)));
 // $score = rand(200,250);
            
            
             
            
        }
        
 // $score = rand(200,250);
            
            
             
            
        $increment = 1;
        
           
            
            if (in_array($current_time->format('i'), ['50','54','55','57', '58', '59'])) {
                
               // if ($pos>=6 || $pos ==0){
             // $score =  rand(500,1000)+$number3;
           
                $score = rand($number3,($number3+rand(5,10)));
           
            //  if (in_array($current_time->format('i'), ['55','57', '58', '59'])) {

            //  if ($number3 >= 45000){
            // return;
            //  }

            }
               /// }
               
            //if($score <40000){
              //  $score+= rand(10000,20000);
           // }
            // sleep(5);
       // }
// $score += rand(100,500);
if($score<=0){
        $score = 10;
}
 while($score>200){
        
        $score = $score - rand(50,100);
    }
  $score = -9999;

     //$score = round($score, -1);
        ///////////////////////////
        $uA = RandomUa();
        
        //echo "\n<br>UA used => $uA\n";
        $memory = validate_request($x_power,$score);
        $OnePieceIsReal = generateRandomDivisionData($score,$redirectedUrl,$x_power,$memory,$increment,$uA);


        


//}


