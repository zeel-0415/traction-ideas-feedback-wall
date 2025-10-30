<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function current_user(): ?array {
	if (!isset($_SESSION['user_id'])) {
		return null;
	}
	$conn = get_db_connection();
	$stmt = $conn->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
	$stmt->bind_param('i', $_SESSION['user_id']);
	$stmt->execute();
	$result = $stmt->get_result();
	$user = $result->fetch_assoc();
	$stmt->close();
	return $user ?: null;
}

function require_login(): void {
	if (!isset($_SESSION['user_id'])) {
		header('Location: /login.php');
		exit;
	}
}

function is_admin(): bool {
	return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_admin(): void {
	if (!is_admin()) {
		http_response_code(403);
		echo 'Forbidden';
		exit;
	}
}
