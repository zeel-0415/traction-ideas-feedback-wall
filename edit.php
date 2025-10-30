<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(404); exit('Not found'); }

$conn = get_db_connection();
$stmt = $conn->prepare('SELECT * FROM suggestions WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$idea = $res->fetch_assoc();
$stmt->close();
if (!$idea) { http_response_code(404); exit('Not found'); }

$ownerId = (int)$idea['user_id'];
$currentUserId = (int)($_SESSION['user_id']);

// Ownership rule: only owner can edit; admin can edit admin-owned suggestions only
if (!($currentUserId === $ownerId || (is_admin() && $ownerId === $currentUserId))) {
	http_response_code(403);
	exit('Forbidden');
}

$errors = [];
$title = $idea['title'];
$description = $idea['description'];
$category = $idea['category'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$title = trim((string)($_POST['title'] ?? ''));
	$description = trim((string)($_POST['description'] ?? ''));
	$category = trim((string)($_POST['category'] ?? ''));
	if ($title === '' || mb_strlen($title) > 255) $errors[] = 'Title is required (≤255).';
	if ($description === '') $errors[] = 'Description is required.';
	if (!valid_category($category)) $errors[] = 'Invalid category.';
	if (!$errors) {
		$stmt = $conn->prepare('UPDATE suggestions SET title=?, description=?, category=?, updated_at=NOW() WHERE id=?');
		$stmt->bind_param('sssi', $title, $description, $category, $id);
		$stmt->execute();
		$stmt->close();
		header('Location: /');
		exit;
	}
}

include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
	<div class="col-12 col-md-8 col-lg-7">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title mb-3">Edit Suggestion</h5>
				<?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
				<form method="post">
					<?= csrf_field() ?>
					<div class="mb-3">
						<label class="form-label">Title</label>
						<input name="title" class="form-control" maxlength="255" required value="<?= e($title) ?>">
					</div>
					<div class="mb-3">
						<label class="form-label">Description</label>
						<textarea name="description" class="form-control" rows="6" required><?= e($description) ?></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label">Category</label>
						<select name="category" class="form-select" required>
							<?php foreach(['Feature','Design','Bug','Idea'] as $cat): ?>
							<option value="<?= e($cat) ?>" <?= $category===$cat?'selected':'' ?>><?= e($cat) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<button class="btn btn-primary" type="submit">Save Changes</button>
					<a class="btn btn-outline-secondary" href="/">Cancel</a>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
