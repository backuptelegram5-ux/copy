<?php
session_start();

// Hardcoded simple auth
$USERS = [
  'flash' => '@flash123',
];

// Already logged in?
if (!empty($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = trim($_POST['user'] ?? '');
  $pass = trim($_POST['pass'] ?? '');

  if (isset($USERS[$user]) && hash_equals($USERS[$user], $pass)) {
    $_SESSION['user'] = $user;
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
    header('Location: index.php');
    exit;
  } else {
    $error = 'Invalid credentials';
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Y’ello Manager — Sign in</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Roboto, sans-serif;
      background: radial-gradient(circle at top, #1a1a2e, #0f0c29);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .authwrap {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
      padding: 20px;
    }
    .authcard {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 16px;
      padding: 30px;
      max-width: 360px;
      width: 100%;
      backdrop-filter: blur(12px);
      box-shadow: 0 0 20px rgba(0,0,0,0.5);
      animation: fadeIn 0.6s ease;
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .logo {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #00e5ff, #a855f7);
      border-radius: 12px;
    }
    .title {
      font-weight: bold;
      font-size: 1.2rem;
    }
    h1 {
      margin: 15px 0 5px 0;
      font-size: 1.4rem;
      font-weight: 600;
    }
    .caption {
      font-size: 0.85rem;
      opacity: 0.8;
    }
    .input {
      padding: 10px;
      border-radius: 8px;
      border: none;
      outline: none;
      width: 100%;
      background: rgba(255,255,255,0.1);
      color: white;
      font-size: 0.95rem;
    }
    .btn {
      padding: 10px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.2s ease;
      font-size: 0.95rem;
    }
    .primary {
      background: linear-gradient(135deg, #00e5ff, #a855f7);
      color: #fff;
    }
    .primary:hover {
      opacity: 0.9;
    }
    .small {
      font-size: 0.85rem;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(10px);}
      to {opacity: 1; transform: translateY(0);}
    }
    @media (max-width: 480px) {
      .authcard {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="authwrap">
    <div class="authcard">
      <div class="brand" style="margin-bottom:10px">
        <div class="logo"></div>
        <div class="title">Y’ello Manager</div>
      </div>
      <h1>Welcome back</h1>
      <div class="caption">Sign in to manage</div>
      <?php if ($error): ?>
        <div class="small" style="color:#ffb4b4;margin-bottom:10px"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form method="post" action="">
        <div style="display:grid;gap:12px;margin:12px 0">
          <input class="input" type="text" name="user" placeholder="Username" required autofocus>
          <input class="input" type="password" name="pass" placeholder="Password" required>
          <button class="btn primary" type="submit">Sign in</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
