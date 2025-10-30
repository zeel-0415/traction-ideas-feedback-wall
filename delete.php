<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed'); }
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { http_response_code(422); exit('Invalid id'); }

$conn = get_db_connection();
$stmt = $conn->prepare('SELECT user_id FROM suggestions WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();
if (!$row) { http_response_code(404); exit('Not found'); }

$ownerId = (int)$row['user_id'];
$currentUserId = (int)$_SESSION['user_id'];

if (!($currentUserId === $ownerId || is_admin())) {
	http_response_code(403);
	exit('Forbidden');
}

$stmt2 = $conn->prepare('DELETE FROM suggestions WHERE id = ?');
$stmt2->bind_param('i', $id);
$stmt2->execute();
$stmt2->close();

header('Location: /');
exit;
