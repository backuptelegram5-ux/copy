<?php
session_start();
header('Content-Type: application/json');
if (empty($_SESSION['user'])) {
  http_response_code(401);
  echo json_encode(['error' => true, 'message' => 'Not authenticated']);
  exit;
}

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
  @mkdir($dataDir, 0777, true);
}

function loadCookies($path)
{
  if (!file_exists($path))
    return [];
  $raw = file_get_contents($path);
  $arr = json_decode($raw, true);
  return is_array($arr) ? $arr : [];
}
function saveCookies($path, $arr)
{
  file_put_contents($path, json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}
function storeFileForDomain($domain)
{
  $dir = __DIR__ . '/data';
  if ($domain === 'yellorush.co.za' || ($domain === 'www.yellorush.co.za'))
    return $dir . '/cookies-mtn.json';
  if ($domain === 'staging.yellorush.co.za')
    return $dir . '/cookies-mtn2.json';
  return $dir . '/cookies.json'; // Vodacom or others
}
function fetchInfo($cookie, $domain)
{
  $ua = 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36';
  $url = 'https://' . $domain . '/my-winnings?display=tab3';
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
  $phone = ($phoneNode->length > 0) ? trim($phoneNode->item(0)->nodeValue) : '';

  $nameNode = $xp->query("//div[contains(@class,'user-name-new')]//h6");
  $name = ($nameNode->length > 0) ? trim($nameNode->item(0)->nodeValue) : '';

  return ['name' => $name, 'phone' => $phone];
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input))
  $input = [];
$action = isset($input['action']) ? $input['action'] : '';

if ($action === 'list') {
  $fVoda = $dataDir . '/cookies.json';
  $fMtn = $dataDir . '/cookies-mtn.json';
  $fMtn2 = $dataDir . '/cookies-mtn2.json';
  $list = array_merge(loadCookies($fVoda), loadCookies($fMtn), loadCookies($fMtn2));
  echo json_encode(['error' => false, 'items' => $list], JSON_UNESCAPED_SLASHES);
  exit;
}

if ($action === 'add_paste') {
  $cookie = trim(isset($input['cookie']) ? $input['cookie'] : '');
  $network = trim(isset($input['network']) ? $input['network'] : '');
  $domain = trim(isset($input['domain']) ? $input['domain'] : '');
  $label = trim(isset($input['label']) ? $input['label'] : '');
  if(empty($label)){
    $label = 'Yello';
  }
  if ($cookie === '' || $network === '' || $domain === '') {
    echo json_encode(['error' => true, 'message' => 'Missing fields']);
    exit;
  }

  $info = fetchInfo($cookie, $domain);
  if ($info['name'] === '' || $info['phone'] === '') {
    echo json_encode(['error' => true, 'message' => 'Invalid cookie (no name/phone)']);
    exit;
  }

  $store = storeFileForDomain($domain);
  $list = loadCookies($store);
  foreach ($list as $row) {
    $c = isset($row['cookie']) ? $row['cookie'] : '';
    $p = isset($row['phone']) ? $row['phone'] : '';
    if ($c === $cookie || $p === $info['phone']) {
      echo json_encode(['error' => true, 'message' => 'Duplicate cookie/phone for this domain']);
      exit;
    }
  }
  $list[] = [
    'id' => bin2hex(random_bytes(6)),
    'network' => $network,
    'domain' => $domain,
    'label' => $label,
    'cookie' => $cookie,
    'phone' => $info['phone'],
    'name' => $info['name'],
    'balance' => null,
    'isFree' => true,
    'created_at' => date('c'),
  ];
  saveCookies($store, $list);
  echo json_encode(['error' => false, 'message' => sprintf('%s (%s) added.', $info['name'], $info['phone'])]);
  exit;
}

if ($action === 'remove') {
  $id = isset($input['id']) ? $input['id'] : '';
  $file = basename(isset($input['file']) ? $input['file'] : '');
  if ($id === '') {
    echo json_encode(['error' => true, 'message' => 'Bad remove request']);
    exit;
  }
  $files = [$dataDir . '/cookies.json', $dataDir . '/cookies-mtn.json', $dataDir . '/cookies-mtn2.json'];
  $removed = false;
  foreach ($files as $path) {
    if (!file_exists($path))
      continue;
    $list = loadCookies($path);
    $out = [];
    foreach ($list as $row) {
      $rid = isset($row['id']) ? $row['id'] : '';
      if ($rid === $id) {
        $removed = true;
        continue;
      }
      $out[] = $row;
    }
    if ($removed) {
      saveCookies($path, $out);
      break;
    }
  }
  echo json_encode(['error' => false, 'message' => $removed ? 'Removed' : 'Not found']);
  exit;
}

if ($action === 'balance_mtn') {
  // expects last 9 digits + domain; Vodacom returns null for now
  $msisdn9 = preg_replace('/\D+/', '', isset($input['msisdn9']) ? $input['msisdn9'] : '');
  $domain = isset($input['domain']) ? $input['domain'] : '';
  if (!preg_match('/^\d{9}$/', $msisdn9)) {
    echo json_encode(['error' => true, 'message' => 'Bad msisdn9']);
    exit;
  }

  // Vodacom not supported yet
  if ($domain === 'gameplay.mzansigames.club') {
    echo json_encode(['error' => false, 'balance' => null]);
    exit;
  }

  // Call local mtn.php via GET with ?number=
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
  $baseUrl = $scheme . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
  $url = $baseUrl . '/mtn.php?number=' . urlencode($msisdn9);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  $resp = curl_exec($ch);
  if (curl_errno($ch)) {
    $err = curl_error($ch);
    curl_close($ch);
    echo json_encode(['error' => true, 'message' => 'Balance request failed: ' . $err]);
    exit;
  }
  curl_close($ch);

  // Parse Airtime balance
  $data = json_decode($resp, true);
  $val = null;
  if (is_array($data) && isset($data['summaryBalanceList'][0]['balances'])) {
    foreach ($data['summaryBalanceList'][0]['balances'] as $b) {
      if (isset($b['balanceType']) && stripos($b['balanceType'], 'airtime') !== false) {
        $val = $b['balanceValue'];
        break;
      }
    }
  }
  if ($val === null && preg_match('/"balanceType"\s*:\s*"Airtime"[^}]*"balanceValue"\s*:\s*"([^"]+)"/si', $resp, $m)) {
    $val = $m[1];
  }
  echo json_encode(['error' => false, 'balance' => $val]);
  exit;
}

echo json_encode(['error' => true, 'message' => 'Unknown action']);
