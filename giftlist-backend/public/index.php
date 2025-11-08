<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../src/utils/helpers.php';
require_once __DIR__ . '/../src/middleware/auth.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/RecipientController.php';
require_once __DIR__ . '/../src/controllers/GiftController.php';

$method = getRequestMethod();
$uri = getRequestUri();

if ($method === 'GET' && $uri === '/health') {
    http_response_code(200);
    echo json_encode(['success' => true, 'data' => ['status' => 'OK']]);
    exit;
}

if ($method === 'POST' && $uri === '/auth/register') {
    handleAuthRegister();
    exit;
}

if ($method === 'POST' && $uri === '/auth/login') {
    handleAuthLogin();
    exit;
}

if ($method === 'GET' && $uri === '/auth/me') {
    $userId = authenticate();
    handleAuthMe($userId);
    exit;
}

if ($method === 'GET' && $uri === '/recipients') {
    $userId = authenticate();
    handleGetRecipients($userId);
    exit;
}

if ($method === 'GET' && ($params = parseRoute('/recipients/{id}', $uri))) {
    $userId = authenticate();
    handleGetRecipient($userId, $params[0]);
    exit;
}

if ($method === 'POST' && $uri === '/recipients') {
    $userId = authenticate();
    handleCreateRecipient($userId);
    exit;
}

if ($method === 'PUT' && ($params = parseRoute('/recipients/{id}', $uri))) {
    $userId = authenticate();
    handleUpdateRecipient($userId, $params[0]);
    exit;
}

if ($method === 'DELETE' && ($params = parseRoute('/recipients/{id}', $uri))) {
    $userId = authenticate();
    handleDeleteRecipient($userId, $params[0]);
    exit;
}

if ($method === 'GET' && $uri === '/gifts') {
    $userId = authenticate();
    handleGetGifts($userId);
    exit;
}

if ($method === 'GET' && ($params = parseRoute('/gifts/{id}', $uri))) {
    $userId = authenticate();
    handleGetGift($userId, $params[0]);
    exit;
}

if ($method === 'GET' && ($params = parseRoute('/recipients/{id}/gifts', $uri))) {
    $userId = authenticate();
    handleGetRecipientGifts($userId, $params[0]);
    exit;
}

if ($method === 'POST' && $uri === '/gifts') {
    $userId = authenticate();
    handleCreateGift($userId);
    exit;
}

if ($method === 'PUT' && ($params = parseRoute('/gifts/{id}', $uri))) {
    $userId = authenticate();
    handleUpdateGift($userId, $params[0]);
    exit;
}

if ($method === 'DELETE' && ($params = parseRoute('/gifts/{id}', $uri))) {
    $userId = authenticate();
    handleDeleteGift($userId, $params[0]);
    exit;
}

http_response_code(404);
echo json_encode(['success' => false, 'error' => ['message' => 'Endpoint not found']]);
