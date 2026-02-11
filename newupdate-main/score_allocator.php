<?php

// Centralized score allocation with file locking
// Stores assignments under new/data/score-locks.json

function sa_lock_file_path()
{
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'new' . DIRECTORY_SEPARATOR . 'data';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    return $dir . DIRECTORY_SEPARATOR . 'score-locks.json';
}

function sa_load_state($fp)
{
    $contents = stream_get_contents($fp);
    $state = json_decode($contents ?: '{}', true);
    if (!is_array($state)) $state = [];
    if (!isset($state['pools'])) $state['pools'] = [];
    return $state;
}

function sa_save_state($fp, $state)
{
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($state, JSON_PRETTY_PRINT));
}

function sa_cookie_key($cookie)
{
    return substr(sha1((string)$cookie), 0, 16);
}

// Acquire or renew a unique score for a cookie
// - $poolKey: string to separate different score sets
// - $scores: ordered list (highest first preferred)
// - $ttlSeconds: automatic release if older than TTL
function sa_allocate_score($cookie, $poolKey, $scores, $ttlSeconds = 180)
{
    $file = sa_lock_file_path();
    $fp = fopen($file, 'c+');
    if (!$fp) return null;
    if (!flock($fp, LOCK_EX)) { fclose($fp); return null; }

    $now = time();
    $state = sa_load_state($fp);
    if (!isset($state['pools'][$poolKey])) {
        $state['pools'][$poolKey] = [
            'assignments' => [],        // cookieKey => ['score'=>int,'ts'=>int]
            'used_by_score' => []       // score => cookieKey
        ];
    }
    $pool =& $state['pools'][$poolKey];

    // expire old assignments
    foreach ($pool['assignments'] as $ckey => $info) {
        if (!isset($info['ts']) || $now - (int)$info['ts'] > $ttlSeconds) {
            $score = $info['score'] ?? null;
            if ($score !== null && isset($pool['used_by_score'][$score]) && $pool['used_by_score'][$score] === $ckey) {
                unset($pool['used_by_score'][$score]);
            }
            unset($pool['assignments'][$ckey]);
        }
    }

    $cookieKey = sa_cookie_key($cookie);

    // renew existing assignment for this cookie
    if (isset($pool['assignments'][$cookieKey])) {
        $pool['assignments'][$cookieKey]['ts'] = $now;
        $score = $pool['assignments'][$cookieKey]['score'];
        sa_save_state($fp, $state);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $score;
    }

    // find first available score from provided list
    foreach ($scores as $score) {
        if (!isset($pool['used_by_score'][$score])) {
            $pool['used_by_score'][$score] = $cookieKey;
            $pool['assignments'][$cookieKey] = ['score' => $score, 'ts' => $now];
            sa_save_state($fp, $state);
            flock($fp, LOCK_UN);
            fclose($fp);
            return $score;
        }
    }

    // none available
    sa_save_state($fp, $state);
    flock($fp, LOCK_UN);
    fclose($fp);
    return null;
}

// Explicitly release a score for a cookie
function sa_release_score($cookie, $poolKey)
{
    $file = sa_lock_file_path();
    $fp = fopen($file, 'c+');
    if (!$fp) return false;
    if (!flock($fp, LOCK_EX)) { fclose($fp); return false; }

    $state = sa_load_state($fp);
    if (!isset($state['pools'][$poolKey])) {
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }

    $pool =& $state['pools'][$poolKey];
    $cookieKey = sa_cookie_key($cookie);
    if (isset($pool['assignments'][$cookieKey])) {
        $score = $pool['assignments'][$cookieKey]['score'] ?? null;
        unset($pool['assignments'][$cookieKey]);
        if ($score !== null && isset($pool['used_by_score'][$score])) {
            unset($pool['used_by_score'][$score]);
        }
    }

    sa_save_state($fp, $state);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

?>

