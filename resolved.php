<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$conn = get_db_connection();
$stmt = $conn->prepare("SELECT s.id, s.title, s.description, s.category, s.votes, s.status, s.created_at, u.name AS author
FROM suggestions s JOIN users u ON u.id = s.user_id
WHERE s.status = 'Resolved' ORDER BY s.updated_at DESC, s.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();

include __DIR__ . '/includes/header.php';
?>
<h5 class="mb-3">Resolved Suggestions</h5>
<div class="row g-3">
<?php while ($row = $result->fetch_assoc()): ?>
	<div class="col-12 col-md-6 col-lg-4">
		<div class="card card-idea h-100 border-success">
			<div class="card-body d-flex flex-column">
				<div class="d-flex justify-content-between align-items-start mb-2">
					<h6 class="card-title mb-0"><?= e($row['title']) ?></h6>
					<span class="badge text-bg-success badge-category">Resolved</span>
				</div>
				<p class="card-text text-muted"><?= e(str_limit($row['description'])) ?></p>
				<div class="mt-auto d-flex justify-content-between align-items-center">
					<div class="small text-muted">By <?= e($row['author']) ?> · <?= e(date('M j, Y', strtotime($row['created_at']))) ?></div>
					<span class="badge text-bg-primary"><?= (int)$row['votes'] ?></span>
				</div>
			</div>
		</div>
	</div>
<?php endwhile; $stmt->close(); ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
