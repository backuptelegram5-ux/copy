<?php
session_start();


function getTikTokVideoUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    $response = curl_exec($ch);
    curl_close($ch);

    if (preg_match('/"UrlList":\["(.*?)"/', $response, $matches)) {
        $video_url = $matches[1];
        $video_url = str_replace('\u002F', '/', $video_url);
        return $video_url;
    } else {
        return false;
    }
}

$video_url = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tiktok_url'])) {
    $url = $_POST['tiktok_url'];
    $custom_name = isset($_POST['custom_name']) ? trim($_POST['custom_name']) : '';
    $video_url = getTikTokVideoUrl($url);
    if ($video_url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $video_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        $video_content = curl_exec($ch);
        curl_close($ch);
        @unlink('cookie.txt');

        if ($video_content === false) {
            $error = "Failed to download video content. Please try again.";
        } else {
            $file_name = $custom_name ? preg_replace('/[^A-Za-z0-9_\-]/', '_', $custom_name) : basename(parse_url($video_url, PHP_URL_PATH));
            $file_name .= "@flashkidd.mp4";

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $file_name);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($video_content));
            echo $video_content;
            exit;
        }
    } else {
        $error = "Unable to download video. Please check the URL and try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>TikTok Video Downloader</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-image: url('one_piece_background.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            color: white;
            font-family: 'Comic Sans MS', sans-serif;
        }
        .sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.8);
            border-right: 2px solid #FFD700;
            padding-top: 60px;
            transition: 0.3s;
            overflow-x: hidden;
            z-index: 1;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #FFD700;
            display: block;
            transition: 0.3s;
            margin-top: 20px;
        }
        .sidebar a:hover {
            background: #FFD700;
            color: black;
        }
        .sidebar .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }
        .openbtn {
            font-size: 20px;
            cursor: pointer;
            background: rgba(0, 0, 0, 0.8);
            color: #FFD700;
            padding: 10px 15px;
            border: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 2;
        }
        .openbtn:hover {
            background: #FFD700;
            color: black;
        }
        .main-content {
            transition: margin-left 0.3s;
            padding: 16px;
            margin-left: 20px;
            margin-top: 60px;
            text-align: center;
        }
        .card {
            background: rgba(0, 0, 0, 0.7);
            border: 2px solid #FFD700;
            border-radius: 10px;
            margin-top: 20px;
        }
        .btn-primary {
            background-color: #FFD700;
            border: none;
            color: black;
        }
        .btn-primary:hover {
            background-color: #ffcc00;
            color: black;
        }
        .container {
            margin-top: 50px;
        }
        .card-title {
            color: #FFD700;
        }
        .form-group label {
            color: #FFD700;
        }
        .alert-info {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: #FFD700;
            color: #FFD700;
        }
        .alert-danger {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: #B22222;
            color: #FFD700;
        }
    </style>
</head>
<body>
     

    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <h3 class="card-title text-center">TikTok Video Downloader</h3>
                        <?php if ($video_url): ?>
                            <div class="alert alert-info">Extracted Video URL: <?php echo htmlspecialchars($video_url); ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="post" action="index.php">
                            <div class="form-group">
                                <label for="tiktok_url">TikTok Video URL</label>
                                <input type="text" class="form-control" id="tiktok_url" name="tiktok_url" placeholder="Enter TikTok video URL" required>
                            </div>
                            <div class="form-group">
                                <label for="custom_name">Custom Video Name (Optional)</label>
                                <input type="text" class="form-control" id="custom_name" name="custom_name" placeholder="Enter custom video name">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Download</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



        <script>
        function openNav() {
            document.getElementById("mySidebar").style.width = "250px";
            document.querySelector(".main-content").style.marginLeft = "260px";
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
            document.querySelector(".main-content").style.marginLeft = "20px";
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
