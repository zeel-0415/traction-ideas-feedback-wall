<?php

declare(strict_types=1);

header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!is_admin()) {
	http_response_code(403);
	echo json_encode(['success' => false, 'message' => 'Forbidden']);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Method not allowed']);
	exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true) ?: [];
$id = (int)($payload['suggestion_id'] ?? 0);
if ($id <= 0) {
	http_response_code(422);
	echo json_encode(['success'=>false,'message'=>'Invalid id']);
	exit;
}

$conn = get_db_connection();
$stmt = $conn->prepare('DELETE FROM suggestions WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$rows = $stmt->affected_rows;
$stmt->close();

if ($rows < 1) {
	http_response_code(404);
	echo json_encode(['success'=>false,'message'=>'Not found']);
	exit;
}

echo json_encode(['success'=>true]);
