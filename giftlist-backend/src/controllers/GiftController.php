<?php

require_once __DIR__ . '/../../config/database.php';

function handleGetGifts($userId) {
    $db = getDbConnection();
    
    $recipientId = $_GET['recipientId'] ?? null;
    
    if ($recipientId) {
        $stmt = $db->prepare("SELECT id FROM recipients WHERE id = ? AND user_id = ?");
        $stmt->execute([$recipientId, $userId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => ['message' => 'Recipient not found']]);
            return;
        }
        
        $stmt = $db->prepare("
            SELECT g.id, g.recipient_id, g.name, g.description, g.price, g.url, g.purchased, g.created_at 
            FROM gifts g 
            INNER JOIN recipients r ON g.recipient_id = r.id 
            WHERE r.user_id = ? AND g.recipient_id = ?
            ORDER BY g.created_at DESC
        ");
        $stmt->execute([$userId, $recipientId]);
    } else {
        $stmt = $db->prepare("
            SELECT g.id, g.recipient_id, g.name, g.description, g.price, g.url, g.purchased, g.created_at 
            FROM gifts g 
            INNER JOIN recipients r ON g.recipient_id = r.id 
            WHERE r.user_id = ?
            ORDER BY g.created_at DESC
        ");
        $stmt->execute([$userId]);
    }
    
    $gifts = $stmt->fetchAll();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $gifts
    ]);
}

function handleGetGift($userId, $id) {
    $db = getDbConnection();
    
    $stmt = $db->prepare("
        SELECT g.id, g.recipient_id, g.name, g.description, g.price, g.url, g.purchased, g.created_at 
        FROM gifts g 
        INNER JOIN recipients r ON g.recipient_id = r.id 
        WHERE g.id = ? AND r.user_id = ?
    ");
    $stmt->execute([$id, $userId]);
    $gift = $stmt->fetch();
    
    if (!$gift) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'Gift not found']]);
        return;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $gift
    ]);
}

function handleCreateGift($userId) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['recipient_id']) || !isset($input['name'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => ['message' => 'Recipient ID and name are required']]);
        return;
    }

    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT id FROM recipients WHERE id = ? AND user_id = ?");
    $stmt->execute([$input['recipient_id'], $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'Recipient not found']]);
        return;
    }
    
    $stmt = $db->prepare("INSERT INTO gifts (recipient_id, name, description, price, url, purchased) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $input['recipient_id'],
        $input['name'],
        $input['description'] ?? null,
        $input['price'] ?? null,
        $input['url'] ?? null,
        $input['purchased'] ?? false
    ]);
    
    $giftId = $db->lastInsertId();
    
    $stmt = $db->prepare("SELECT id, recipient_id, name, description, price, url, purchased, created_at FROM gifts WHERE id = ?");
    $stmt->execute([$giftId]);
    $gift = $stmt->fetch();
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data' => $gift
    ]);
}

function handleUpdateGift($userId, $id) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $db = getDbConnection();
    
    $stmt = $db->prepare("
        SELECT g.id FROM gifts g 
        INNER JOIN recipients r ON g.recipient_id = r.id 
        WHERE g.id = ? AND r.user_id = ?
    ");
    $stmt->execute([$id, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'Gift not found']]);
        return;
    }

    $updates = [];
    $params = [];
    
    if (isset($input['name'])) {
        $updates[] = "name = ?";
        $params[] = $input['name'];
    }
    if (isset($input['description'])) {
        $updates[] = "description = ?";
        $params[] = $input['description'];
    }
    if (isset($input['price'])) {
        $updates[] = "price = ?";
        $params[] = $input['price'];
    }
    if (isset($input['url'])) {
        $updates[] = "url = ?";
        $params[] = $input['url'];
    }
    if (isset($input['purchased'])) {
        $updates[] = "purchased = ?";
        $params[] = $input['purchased'];
    }
    
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => ['message' => 'No fields to update']]);
        return;
    }

    $params[] = $id;
    
    $sql = "UPDATE gifts SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    $stmt = $db->prepare("SELECT id, recipient_id, name, description, price, url, purchased, created_at FROM gifts WHERE id = ?");
    $stmt->execute([$id]);
    $gift = $stmt->fetch();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $gift
    ]);
}

function handleDeleteGift($userId, $id) {
    $db = getDbConnection();
    
    $stmt = $db->prepare("
        SELECT g.id FROM gifts g 
        INNER JOIN recipients r ON g.recipient_id = r.id 
        WHERE g.id = ? AND r.user_id = ?
    ");
    $stmt->execute([$id, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'Gift not found']]);
        return;
    }

    $stmt = $db->prepare("DELETE FROM gifts WHERE id = ?");
    $stmt->execute([$id]);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => ['message' => 'Gift deleted']
    ]);
}

function handleGetRecipientGifts($userId, $recipientId) {
    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT id FROM recipients WHERE id = ? AND user_id = ?");
    $stmt->execute([$recipientId, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'Recipient not found']]);
        return;
    }
    
    $stmt = $db->prepare("
        SELECT id, recipient_id, name, description, price, url, purchased, created_at 
        FROM gifts 
        WHERE recipient_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$recipientId]);
    $gifts = $stmt->fetchAll();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $gifts
    ]);
}
