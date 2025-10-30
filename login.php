<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$email = trim((string)($_POST['email'] ?? ''));
	$password = (string)($_POST['password'] ?? '');
	if ($email === '' || $password === '') {
		$error = 'Email and password are required.';
	} else {
		$conn = get_db_connection();
		$stmt = $conn->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ?');
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$res = $stmt->get_result();
		$user = $res->fetch_assoc();
		$stmt->close();
		if ($user && password_verify($password, $user['password_hash'])) {
			$_SESSION['user_id'] = (int)$user['id'];
			$_SESSION['role'] = $user['role'];
			header('Location: /');
			exit;
		}
		$error = 'Invalid credentials.';
	}
}

include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
	<div class="col-12 col-md-6 col-lg-5">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title mb-3">Login</h5>
				<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
				<form method="post">
					<?= csrf_field() ?>
					<div class="mb-3">
						<label class="form-label">Email</label>
						<input type="email" class="form-control" name="email" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Password</label>
						<input type="password" class="form-control" name="password" required>
					</div>
					<button type="submit" class="btn btn-primary">Sign in</button>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
