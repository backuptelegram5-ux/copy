<?php

/**
 * Game Configuration Array
 * Maps game names (slug format) to their scoring parameters
 * 
 * Structure:
 * 'game-slug' => [
 *     'max'       => maximum score,
 *     'gameScore' => base game score,
 *     'step'      => score decrement step (0 = no step, counts down by 1),
 *     'count'     => number of score attempts
 * ]
 */
$gameConfig = [
    // Declared games with known values
    'balloon-bandit' => [
        'max'       => 1239,
        'gameScore' => 500,
        'step'      => 1,  // No step, counts 1239, 1238, 1237...
        'count'     => 10
    ],
    'jumpmaster' => [
        'max'       => 9000,
        'gameScore' => 3000,
        'step'      => 10,  // 9000, 8990, 8980...
        'count'     => 10
    ],
    
    // Placeholder games (update values as needed)
    'rainbow-rush' => [
        'max'       => 5000,
        'gameScore' => 2000,
        'step'      => 10,
        'count'     => 10
    ],
    'helpless-hadeda' => [
        'max'       => 3000,
        'gameScore' => 1000,
        'step'      => 5,
        'count'     => 10
    ],
    'taxi-driva' => [
        'max'       => 4000,
        'gameScore' => 1500,
        'step'      => 10,
        'count'     => 10
    ],
    'rat-aisle-land' => [
        'max'       => 3500,
        'gameScore' => 1200,
        'step'      => 5,
        'count'     => 10
    ],
    'get-to-game' => [
        'max'       => 2500,
        'gameScore' => 800,
        'step'      => 5,
        'count'     => 10
    ],
    'fast-arrow' => [
        'max'       => 6000,
        'gameScore' => 2500,
        'step'      => 10,
        'count'     => 10
    ],
    'superbug-blitz' => [
        'max'       => 5500,
        'gameScore' => 2000,
        'step'      => 10,
        'count'     => 10
    ],
    'easter-eggsplosion' => [
        'max'       => 2000,
        'gameScore' => 1000,
        'step'      => 1,
        'count'     => 10
    ],
    'blue-switcheroo' => [
        'max'       => 2000,
        'gameScore' => 700,
        'step'      => 1,
        'count'     => 10
    ],
    'cupids-run' => [
        'max'       => 4500,
        'gameScore' => 1500,
        'step'      => 10,
        'count'     => 10
    ],
    'space-hop' => [
        'max'       => 3500,
        'gameScore' => 1200,
        'step'      => 5,
        'count'     => 10
    ],
    'boo-bash' => [
        'max'       => 2800,
        'gameScore' => 900,
        'step'      => 5,
        'count'     => 10
    ],
    'motorway-madness' => [
        'max'       => 5000,
        'gameScore' => 1800,
        'step'      => 10,
        'count'     => 10
    ],
    'stomp-em-out' => [
        'max'       => 3200,
        'gameScore' => 1100,
        'step'      => 5,
        'count'     => 10
    ],
    'ufo-escape' => [
        'max'       => 4200,
        'gameScore' => 1400,
        'step'      => 10,
        'count'     => 10
    ],
    'taxi-defender' => [
        'max'       => 4000,
        'gameScore' => 1300,
        'step'      => 10,
        'count'     => 10
    ],
    'sunset-sweeps' => [
        'max'       => 3000,
        'gameScore' => 1000,
        'step'      => 5,
        'count'     => 10
    ],
    'padkos-chaos' => [
        'max'       => 4500,
        'gameScore' => 1600,
        'step'      => 10,
        'count'     => 10
    ]
];

/**
 * Get game configuration by game slug
 * 
 * @param string $gameSlug - The game name/slug (e.g., 'balloon-bandit')
 * @return array - Configuration array with max, gameScore, step, count
 */
function getGameConfig($gameSlug) {
    global $gameConfig;
    
    // Normalize game slug (lowercase, replace spaces/underscores with hyphens)
    $slug = strtolower(trim($gameSlug));
    $slug = str_replace(['_', ' '], '-', $slug);
    
    // Return game config or default values
    if (isset($gameConfig[$slug])) {
        return $gameConfig[$slug];
    }
    
    // Default fallback config
    return [
        'max'       => 100,
        'gameScore' => 100,
        'step'      => 1,
        'count'     => 10
    ];
}

/**
 * Extract game slug from redirect URL
 * 
 * @param string $redirectUrl - The full redirect URL
 * @return string - Game slug (e.g., 'balloon-bandit' from 'https://yellorush.co.za/balloon-bandit/?...')
 */
function extractGameSlug($redirectUrl) {
    // Parse URL and get path
    $path = parse_url($redirectUrl, PHP_URL_PATH);
    
    // Remove leading/trailing slashes and extract game name
    $path = trim($path, '/');
    $parts = explode('/', $path);
    
    // Return first part (game slug)
    return isset($parts[0]) ? $parts[0] : 'unknown';
}

?>
