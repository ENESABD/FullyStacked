<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/jwt.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;

function handleAuthRegister() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name']) || !isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => ['message' => 'Name, email, and password are required']]);
        return;
    }

    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$input['email']]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => ['message' => 'Email already exists']]);
        return;
    }

    $passwordHash = password_hash($input['password'], PASSWORD_BCRYPT);
    
    $stmt = $db->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$input['name'], $input['email'], $passwordHash]);
    
    $userId = $db->lastInsertId();
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $userId,
            'name' => $input['name'],
            'email' => $input['email']
        ]
    ]);
}

function handleAuthLogin() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => ['message' => 'Email and password are required']]);
        return;
    }

    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT id, name, email, password_hash FROM users WHERE email = ?");
    $stmt->execute([$input['email']]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($input['password'], $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => ['message' => 'Invalid credentials']]);
        return;
    }

    $config = getJwtConfig();
    $issuedAt = time();
    $expire = $issuedAt + $config['expiration'];
    
    $payload = [
        'iss' => $config['issuer'],
        'aud' => $config['audience'],
        'iat' => $issuedAt,
        'exp' => $expire,
        'userId' => $user['id']
    ];
    
    $token = JWT::encode($payload, $config['secret'], 'HS256');
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => [
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ]
        ]
    ]);
}

function handleAuthMe($userId) {
    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'User not found']]);
        return;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $user
    ]);
}
