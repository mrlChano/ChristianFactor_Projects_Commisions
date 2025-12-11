<?php
// Simple endpoint to generate a new Pokémon captcha code and store it in session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {
    $pokemonWords = [
        'PIKACHU',
        'CHARMANDER',
        'SQUIRTLE',
        'BULBASAUR',
        'EEVEE',
        'SNORLAX',
        'MEWTWO',
        'LAPRAS',
        'DRAGONITE',
        'JIGGLYPUFF'
    ];

    $code = $pokemonWords[array_rand($pokemonWords)];
    $_SESSION['captcha_code'] = $code;

    echo json_encode([
        'success' => true,
        'code' => $code
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Unable to refresh code: ' . $e->getMessage()
    ]);
}
?>

