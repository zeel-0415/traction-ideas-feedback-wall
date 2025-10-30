<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= e(APP_NAME) ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="/assets/css/styles.css" rel="stylesheet">
    <script>(function(){try{var t=localStorage.getItem('ti-theme');if(!t){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-bs-theme',t);}catch(e){}})();</script>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top">
	<div class="container">
		<a class="navbar-brand fw-semibold" href="/">Traction Ideas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbars" aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbars">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item"><a class="nav-link" href="/">Open</a></li>
				<li class="nav-item"><a class="nav-link" href="/resolved.php">Resolved</a></li>
				<?php if (is_admin()): ?>
				<li class="nav-item"><a class="nav-link" href="/admin/dashboard.php">Admin</a></li>
				<?php endif; ?>
			</ul>
            <ul class="navbar-nav align-items-center gap-2">
				<li class="nav-item">
                    <button class="btn btn-sm btn-outline-secondary" id="theme-toggle" type="button" aria-label="Toggle theme" title="Toggle theme">Dark</button>
				</li>
				<?php if ($user): ?>
                <li class="nav-item"><a class="btn btn-primary" href="/add.php">Add Suggestion</a></li>
				<li class="nav-item"><span class="navbar-text">Hi, <?= e($user['name']) ?> (<?= e($user['role']) ?>)</span></li>
				<li class="nav-item"><a class="btn btn-outline-secondary" href="/logout.php">Logout</a></li>
				<?php else: ?>
                <li class="nav-item"><a class="btn btn-outline-primary" href="/login.php">Login</a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</nav>
<main class="container py-4">
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>
