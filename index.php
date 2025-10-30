<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$category = isset($_GET['category']) ? trim((string)$_GET['category']) : '';
$search = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
switch($_GET['sort'] ?? 'votes'){
	case 'date': $sort = 'created_at DESC'; break;
	default: $sort = 'votes DESC, created_at DESC'; break;
}

$conn = get_db_connection();
$sql = "SELECT s.id, s.user_id, s.title, s.description, s.category, s.votes, s.status, s.created_at, u.name AS author
		FROM suggestions s JOIN users u ON u.id = s.user_id
		WHERE s.status = 'Open'";
$params = [];
$types = '';
if ($category !== '' && valid_category($category)) {
	$sql .= " AND s.category = ?";
	$types .= 's';
	$params[] = $category;
}
if ($search !== '') {
	$sql .= " AND (s.title LIKE ? OR s.description LIKE ?)";
	$types .= 'ss';
	$like = '%' . $search . '%';
	$params[] = $like; $params[] = $like;
}
$sql .= " ORDER BY $sort";
$stmt = $conn->prepare($sql);
if ($types !== '') {
	$stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

include __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
	<form class="row g-2 align-items-center" method="get">
		<div class="col-auto">
			<select name="category" class="form-select">
				<option value="">All Categories</option>
				<?php foreach(['Feature','Design','Bug','Idea'] as $cat): ?>
				<option value="<?= e($cat) ?>" <?= $category===$cat?'selected':'' ?>><?= e($cat) ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="col-auto">
			<input type="text" class="form-control" name="q" placeholder="Search..." value="<?= e($search) ?>">
		</div>
		<div class="col-auto">
			<select name="sort" class="form-select">
				<option value="votes" <?= (($_GET['sort'] ?? 'votes')==='votes')?'selected':'' ?>>Sort by Votes</option>
				<option value="date" <?= (($_GET['sort'] ?? '')==='date')?'selected':'' ?>>Newest</option>
			</select>
		</div>
		<div class="col-auto">
			<button class="btn btn-outline-secondary" type="submit">Apply</button>
		</div>
	</form>
	<div class="ms-auto">
		<a href="/resolved.php" class="btn btn-link">View Resolved</a>
	</div>
</div>

<div class="row g-3">
<?php while ($row = $result->fetch_assoc()): ?>
	<?php $isOwner = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$row['user_id']; ?>
	<div class="col-12 col-md-6 col-lg-4">
		<div class="card card-idea h-100">
			<div class="card-body d-flex flex-column">
				<div class="d-flex justify-content-between align-items-start mb-2">
					<h5 class="card-title mb-0"><?= e($row['title']) ?></h5>
					<span class="badge text-bg-secondary badge-category"><?= e($row['category']) ?></span>
				</div>
				<p class="card-text text-muted"><?= e(str_limit($row['description'])) ?></p>
				<div class="mt-auto d-flex justify-content-between align-items-center">
					<div class="small text-muted">By <?= e($row['author']) ?> · <?= e(date('M j, Y', strtotime($row['created_at']))) ?></div>
					<div class="d-flex align-items-center gap-2">
						<span class="badge text-bg-primary" data-votes-for="<?= (int)$row['id'] ?>"><?= (int)$row['votes'] ?></span>
						<button class="btn btn-sm btn-outline-primary" onclick="Traction.upvote(<?= (int)$row['id'] ?>)">Upvote</button>
						<?php if ($isOwner): ?>
							<a class="btn btn-sm btn-outline-secondary" href="/edit.php?id=<?= (int)$row['id'] ?>">Edit</a>
						<?php endif; ?>
						<?php if ($isOwner || is_admin()): ?>
							<form method="post" action="/delete.php" onsubmit="return confirm('Delete this suggestion?');">
								<?= csrf_field() ?>
								<input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
								<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
							</form>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endwhile; $stmt->close(); ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
