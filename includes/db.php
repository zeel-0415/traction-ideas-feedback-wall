<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

function get_db_connection(): mysqli {
	static $conn = null;
	if ($conn instanceof mysqli) {
		return $conn;
	}
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($conn->connect_error) {
		die('Database connection failed: ' . htmlspecialchars((string)$conn->connect_error));
	}
	$conn->set_charset('utf8mb4');
	return $conn;
}
