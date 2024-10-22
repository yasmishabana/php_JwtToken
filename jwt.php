<?php
use \Firebase\JWT\JWT;

require_once __DIR__ . '/vendor/autoload.php'; 

function generate_jwt($user_id, $username) {
    $secret_key = "YOUR_SECRET_KEY";
    $issued_at = time();
    $expiration_time = $issued_at + (60 * 10); // 10 minutes expiration
    $payload = [
        "iss" => "yourdomain.com",
        "iat" => $issued_at,
        "exp" => $expiration_time,
        "data" => [
            "id" => $user_id,
            "username" => $username
        ]
    ];

    return JWT::encode($payload, $secret_key, 'HS256');
    // JWT::encode($payload, $secret_key);
}

function generate_refresh_token() {
    return bin2hex(random_bytes(50)); // Generate a random refresh token
}
?>
