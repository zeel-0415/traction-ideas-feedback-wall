<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$conn = get_db_connection();

$stats = [
	'total' => 0,
	'open' => 0,
	'resolved' => 0,
	'votes' => 0,
];

$res1 = $conn->query("SELECT COUNT(*) AS c FROM suggestions");
$stats['total'] = (int)($res1->fetch_assoc()['c'] ?? 0);
$res2 = $conn->query("SELECT COUNT(*) AS c FROM suggestions WHERE status='Open'");
$stats['open'] = (int)($res2->fetch_assoc()['c'] ?? 0);
$res3 = $conn->query("SELECT COUNT(*) AS c FROM suggestions WHERE status='Resolved'");
$stats['resolved'] = (int)($res3->fetch_assoc()['c'] ?? 0);
$res4 = $conn->query("SELECT COALESCE(SUM(votes),0) AS v FROM suggestions");
$stats['votes'] = (int)($res4->fetch_assoc()['v'] ?? 0);

include __DIR__ . '/../includes/header.php';
?>
<h5 class="mb-3">Admin Dashboard</h5>
<div class="row g-3">
	<div class="col-6 col-md-3">
		<div class="card text-center">
			<div class="card-body">
				<div class="text-muted">Total</div>
				<div class="fs-3 fw-bold"><?= (int)$stats['total'] ?></div>
			</div>
		</div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center">
			<div class="card-body">
				<div class="text-muted">Open</div>
				<div class="fs-3 fw-bold text-primary"><?= (int)$stats['open'] ?></div>
			</div>
		</div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center">
			<div class="card-body">
				<div class="text-muted">Resolved</div>
				<div class="fs-3 fw-bold text-success"><?= (int)$stats['resolved'] ?></div>
			</div>
		</div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center">
			<div class="card-body">
				<div class="text-muted">Total Votes</div>
				<div class="fs-3 fw-bold"><?= (int)$stats['votes'] ?></div>
			</div>
		</div>
	</div>
</div>
<div class="mt-4">
	<a class="btn btn-primary" href="/admin/suggestions.php">Manage Suggestions</a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
