<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/jwt.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function authenticate() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => ['message' => 'Missing or invalid authorization header']]);
        exit;
    }

    $token = $matches[1];
    $config = getJwtConfig();

    try {
        $decoded = JWT::decode($token, new Key($config['secret'], 'HS256'));
        return $decoded->userId;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => ['message' => 'Invalid or expired token']]);
        exit;
    }
}
