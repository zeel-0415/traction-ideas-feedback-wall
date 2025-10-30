<?php

declare(strict_types=1);

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Method not allowed']);
	exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true) ?: [];
$suggestionId = (int)($payload['suggestion_id'] ?? 0);
if ($suggestionId <= 0) {
	http_response_code(422);
	echo json_encode(['success' => false, 'message' => 'Invalid suggestion id']);
	exit;
}

$conn = get_db_connection();

// Ensure suggestion exists and is Open
$stmt = $conn->prepare("SELECT id, votes, status FROM suggestions WHERE id = ?");
$stmt->bind_param('i', $suggestionId);
$stmt->execute();
$res = $stmt->get_result();
$suggestion = $res->fetch_assoc();
$stmt->close();
if (!$suggestion || $suggestion['status'] !== 'Open') {
	http_response_code(404);
	echo json_encode(['success' => false, 'message' => 'Suggestion not found']);
	exit;
}

$userId = $_SESSION['user_id'] ?? null;
$fingerprint = null;
if (!$userId) {
	$fingerprint = fingerprint_guest();
}

// Insert vote if not exists
try {
	$conn->begin_transaction();
	if ($userId) {
		$stmt = $conn->prepare("INSERT INTO votes_log (suggestion_id, voter_user_id) VALUES (?, ?) ");
		$stmt->bind_param('ii', $suggestionId, $userId);
	} else {
		$stmt = $conn->prepare("INSERT INTO votes_log (suggestion_id, voter_fingerprint) VALUES (?, ?) ");
		$stmt->bind_param('is', $suggestionId, $fingerprint);
	}
	$stmt->execute();
	$stmt->close();

	// increment votes
	$stmt2 = $conn->prepare("UPDATE suggestions SET votes = votes + 1 WHERE id = ?");
	$stmt2->bind_param('i', $suggestionId);
	$stmt2->execute();
	$stmt2->close();

	// fetch new count
	$stmt3 = $conn->prepare("SELECT votes FROM suggestions WHERE id = ?");
	$stmt3->bind_param('i', $suggestionId);
	$stmt3->execute();
	$newRes = $stmt3->get_result();
	$row = $newRes->fetch_assoc();
	$stmt3->close();

	$conn->commit();
	echo json_encode(['success' => true, 'votes' => (int)$row['votes']]);
	exit;
} catch (mysqli_sql_exception $e) {
	$conn->rollback();
	// Duplicate vote (unique key violation) will land here
	http_response_code(200);
	echo json_encode(['success' => false, 'message' => 'You have already upvoted this suggestion.']);
	exit;
}
