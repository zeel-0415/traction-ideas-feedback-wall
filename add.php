<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

require_login();

$errors = [];
$title = '';
$description = '';
$category = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$title = trim((string)($_POST['title'] ?? ''));
	$description = trim((string)($_POST['description'] ?? ''));
	$category = trim((string)($_POST['category'] ?? ''));

	if ($title === '' || mb_strlen($title) > 255) $errors[] = 'Title is required (≤255).';
	if ($description === '') $errors[] = 'Description is required.';
	if (!valid_category($category)) $errors[] = 'Invalid category.';

	if (!$errors) {
		$conn = get_db_connection();
		$stmt = $conn->prepare('INSERT INTO suggestions (user_id, title, description, category) VALUES (?,?,?,?)');
		$userId = (int)$_SESSION['user_id'];
		$stmt->bind_param('isss', $userId, $title, $description, $category);
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
				<h5 class="card-title mb-3">Add Suggestion</h5>
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
					<button class="btn btn-primary" type="submit">Create</button>
					<a class="btn btn-outline-secondary" href="/">Cancel</a>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
