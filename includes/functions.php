<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

function e(string $value): string {
	return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function valid_category(string $category): bool {
	$allowed = ['Feature','Design','Bug','Idea'];
	return in_array($category, $allowed, true);
}

function csrf_token(): string {
	if (empty($_SESSION[CSRF_TOKEN_KEY])) {
		$_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
	}
	return $_SESSION[CSRF_TOKEN_KEY];
}

function csrf_field(): string {
	$token = csrf_token();
	return '<input type="hidden" name="_csrf" value="' . e($token) . '">';
}

function verify_csrf(): void {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$sent = $_POST['_csrf'] ?? '';
		if (!hash_equals((string)($_SESSION[CSRF_TOKEN_KEY] ?? ''), (string)$sent)) {
			http_response_code(419);
			echo 'Invalid CSRF token';
			exit;
		}
	}
}

function str_limit(string $text, int $limit = 200): string {
	if (mb_strlen($text) <= $limit) return $text;
	return mb_substr($text, 0, $limit - 1) . '…';
}

function fingerprint_guest(): string {
	$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
	$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
	$sess = session_id();
	return hash('sha256', $ip . '|' . $ua . '|' . $sess);
}
