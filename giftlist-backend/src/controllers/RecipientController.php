<?php

require_once __DIR__ . '/../../config/database.php';

function handleGetRecipients($userId) {
    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT id, user_id, name, relationship, notes, created_at FROM recipients WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $recipients = $stmt->fetchAll();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $recipients
    ]);
}

function handleGetRecipient($userId, $id) {
    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT id, user_id, name, relationship, notes, created_at FROM recipients WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    $recipient = $stmt->fetch();
    
    if (!$recipient) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'Recipient not found']]);
        return;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $recipient
    ]);
}

function handleCreateRecipient($userId) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => ['message' => 'Name is required']]);
        return;
    }

    $db = getDbConnection();
    
    $stmt = $db->prepare("INSERT INTO recipients (user_id, name, relationship, notes) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $userId,
        $input['name'],
        $input['relationship'] ?? null,
        $input['notes'] ?? null
    ]);
    
    $recipientId = $db->lastInsertId();
    
    $stmt = $db->prepare("SELECT id, user_id, name, relationship, notes, created_at FROM recipients WHERE id = ?");
    $stmt->execute([$recipientId]);
    $recipient = $stmt->fetch();
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data' => $recipient
    ]);
}

function handleUpdateRecipient($userId, $id) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT id FROM recipients WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'Recipient not found']]);
        return;
    }

    $updates = [];
    $params = [];
    
    if (isset($input['name'])) {
        $updates[] = "name = ?";
        $params[] = $input['name'];
    }
    if (isset($input['relationship'])) {
        $updates[] = "relationship = ?";
        $params[] = $input['relationship'];
    }
    if (isset($input['notes'])) {
        $updates[] = "notes = ?";
        $params[] = $input['notes'];
    }
    
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => ['message' => 'No fields to update']]);
        return;
    }

    $params[] = $id;
    $params[] = $userId;
    
    $sql = "UPDATE recipients SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    $stmt = $db->prepare("SELECT id, user_id, name, relationship, notes, created_at FROM recipients WHERE id = ?");
    $stmt->execute([$id]);
    $recipient = $stmt->fetch();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $recipient
    ]);
}

function handleDeleteRecipient($userId, $id) {
    $db = getDbConnection();
    
    $stmt = $db->prepare("SELECT id FROM recipients WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => ['message' => 'Recipient not found']]);
        return;
    }

    $stmt = $db->prepare("DELETE FROM recipients WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => ['message' => 'Recipient deleted']
    ]);
}
