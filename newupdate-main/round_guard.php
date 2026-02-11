<?php

// Maintenance guard: provides a window (e.g., minutes 58-59)
// to stop work and reset cookie/score states for a fresh round.

function rg_is_maintenance_window(): bool
{
    $min = (int)date('i');
    return ($min >= 58 && $min <= 59);
}

function rg_reset_files(): array
{
    $base = __DIR__;
    $targets = [
        $base . '/cookies-mtn.json',
        $base . '/cookies-newgame.json',
        $base . '/new/data/cookies-mtn.json',
        $base . '/new/data/cookies-mtn2.json',
        $base . '/new/data/cookies.json',
    ];
    return array_values(array_filter($targets, function ($p) {
        return file_exists($p);
    }));
}

function rg_unlock_all_cookies_in_file(string $path): bool
{
    $fp = fopen($path, 'c+');
    if (!$fp) return false;
    if (!flock($fp, LOCK_EX)) { fclose($fp); return false; }
    $data = json_decode(stream_get_contents($fp) ?: '[]', true);
    if (!is_array($data)) $data = [];

    $changed = false;
    foreach ($data as $i => $row) {
        if (!isset($row['isFree']) || !$row['isFree']) {
            $data[$i]['isFree'] = true;
            $changed = true;
        }
    }

    if ($changed) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
    }

    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

function rg_clear_score_locks(): void
{
    $lockFile = __DIR__ . '/new/data/score-locks.json';
    if (file_exists($lockFile)) {
        // overwrite with empty state
        @file_put_contents($lockFile, json_encode(['pools' => []], JSON_PRETTY_PRINT));
    }
}

function rg_round_reset(): void
{
    foreach (rg_reset_files() as $file) {
        rg_unlock_all_cookies_in_file($file);
    }
    rg_clear_score_locks();
}

?>

