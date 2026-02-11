<?php
session_start();
if (empty($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Y’ello Manager — Control</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Orbitron:wght@600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <!-- Single, compact header (desktop) + mobile hamburger -->
  <header class="neo-header">
    <div class="h-inner">
      <button id="mToggle" class="hamburger" aria-label="Menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
      <div class="brand">
        <div class="logo"><span></span></div>
        <div class="title">Y’ello Manager</div>
      </div>
      <nav class="h-actions" aria-label="Primary">
        <a href="subscribe.php" class="pill">Subscribe</a>
        <a href="scores.php" class="pill">Scores</a>
        <a href="other.php" class="pill">Other</a>
        <a href="settings.php" class="pill">Settings</a>
        <a href="logout.php" class="pill warn">Logout</a>
      </nav>
    </div>
  </header>

  <!-- Mobile-only drawer (opens with hamburger) -->
  <aside id="mDrawer" class="drawer" aria-hidden="true">
    <nav class="d-nav">
      <a href="home.php" class="active">Dashboard</a>
      <a href="#">Subscribe</a>
      <a href="#">Register</a>
      <a href="#">Settings</a>
      <a href="logout.php">Logout</a>
    </nav>
  </aside>

  <main class="page">
    <!-- Hero -->
    <section class="hero">
      <div class="hero-left">
        <h1 class="headline"><span class="accent">Control</span> your game, beautifully.</h1>
        <p class="sub">Manage cookies, Modify Game flows, and track balances all in one place.</p>
        <div class="quick">
          <span class="chip">MTN</span><span class="chip">Vodacom</span><span class="chip">Telkom-Rush</span>
        </div>
      </div>
      <div class="hero-right">
        <div class="orb"></div>
      </div>
    </section>

    <!-- Panels -->
    <section class="grid">
      <!-- Manual cookie add -->
      <article class="card" id="manual">
        <header class="card-h">
          <h2>Add Cookie <small>(Manual)</small></h2>
        </header>
        <div class="card-b">
          <div class="row two">
            <div class="f">
              <label>Network</label>
              <select id="netSelect">
                <option value="">Choose…</option>
                <option value="MTN">MTN</option>
                <option value="voda">Vodacom</option>
                <option value="mtn2">Telkom-Rush (MTN 70R)</option>
              </select>
            </div>
            <div class="f">
              <label>Domain</label>
              <select id="domSelect">
                <option value="">Choose…</option>
                <option value="www.yellorush.co.za">yellorush.co.za</option>
                <option value="gameplay.mzansigames.club">gameplay.mzansigames.club</option>
                <option value="staging.yellorush.co.za">staging.yellorush.co.za</option>
              </select>
            </div>
          </div>
          <div class="f">
            <label>Label (owner / notes) — optional</label>
            <input type="text" id="label" placeholder="e.g., John MTN 083…" />
          </div>
          <div class="f">
            <label>Cookie string</label>
            <textarea id="cookieStr" rows="4" placeholder="Paste full cookie header here"></textarea>
          </div>
          <div class="foot">
            <button id="btnAdd" class="btn glow">Validate & Save</button>
            <span id="addMsg" class="msg"></span>
          </div>
        </div>
      </article>

      <!-- OTP -->
      <article class="card" id="otp">
        <header class="card-h">
          <h2>Add via Number & OTP</h2>
        </header>
        <div class="card-b">
          <div class="row two">
            <div class="f">
              <label>Provider</label>
              <select id="provider">
                <option value="">Choose…</option>
                <option value="mtn">MTN (yellorush.co.za)</option>
                <option value="voda">Vodacom (gameplay.mzansigames.club)</option>
                <option value="mtn2">Telkom-Rush (staging.yellorush.co.za)</option>
              </select>
            </div>
            <div class="f">
              <label>MSISDN</label>
              <input id="msisdn" type="text" placeholder="e.g., 0831234567" />
            </div>
          </div>
          <div class="row">
            <button id="sendBtn" class="btn" onclick="return sendOTPClick()">Send OTP</button>
          </div>
          <div id="otpRow" class="row" style="display:none">
            <div class="f">
              <label>OTP</label>
              <input id="otpCode" type="text" placeholder="6‑digit OTP" />
            </div>
            <button id="verifyBtn" class="btn ok">Verify & Save</button>
          </div>
          <div id="otpMsg" class="msg mt"></div>
        </div>
      </article>
    </section>

    <!-- Stored cookies -->
    <section class="card" id="stores">
      <header class="card-h">
        <h2>Stored Cookies</h2>
        <div class="muted" id="countNote">Loading…</div>
      </header>
      <div class="card-b">
        <div class="table-wrap">
          <table class="neo-table" id="cookiesTable">
            <thead>
              <tr>
                <th>Owner</th>
                <th>Phone</th>
                <th>Network</th>
                <th>Domain</th>
                <th>Label</th>
                <th>Balance</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="cookiesBody">
              <tr><td colspan="7" class="muted">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <script src="assets/ui.js"></script>
</body>
</html>
