<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$conn = get_db_connection();
$res = $conn->query("SELECT s.id, s.title, s.status, s.votes, s.category, u.name AS author, s.created_at
FROM suggestions s JOIN users u ON u.id = s.user_id
ORDER BY s.created_at DESC");

include __DIR__ . '/../includes/header.php';
?>
<h5 class="mb-3">Manage Suggestions</h5>
<div class="table-responsive">
	<table class="table align-middle">
		<thead>
			<tr>
				<th>ID</th>
				<th>Title</th>
				<th>Author</th>
				<th>Category</th>
				<th>Votes</th>
				<th>Status</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php while($row = $res->fetch_assoc()): ?>
			<tr data-id="<?= (int)$row['id'] ?>">
				<td><?= (int)$row['id'] ?></td>
				<td><?= e($row['title']) ?></td>
				<td><?= e($row['author']) ?></td>
				<td><?= e($row['category']) ?></td>
				<td><span class="badge text-bg-primary"><?= (int)$row['votes'] ?></span></td>
				<td><span class="badge <?= $row['status']==='Resolved'?'text-bg-success':'text-bg-secondary' ?> status-badge"><?= e($row['status']) ?></span></td>
				<td class="d-flex gap-2">
					<button class="btn btn-sm btn-outline-success" onclick="toggleStatus(<?= (int)$row['id'] ?>, 'resolve')">Resolve</button>
					<button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(<?= (int)$row['id'] ?>, 'unresolve')">Unresolve</button>
					<button class="btn btn-sm btn-outline-danger" onclick="deleteSuggestion(<?= (int)$row['id'] ?>)">Delete</button>
				</td>
			</tr>
		<?php endwhile; ?>
		</tbody>
	</table>
</div>
<script>
async function toggleStatus(id, action){
	try{
		const res = await fetch('/api/admin/update_status.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ suggestion_id:id, action }) });
		const data = await res.json();
		if(!res.ok || !data.success){ Traction.toast(data.message||'Failed', 'danger'); return; }
		const tr = document.querySelector('tr[data-id="'+id+'"] .status-badge');
		if(tr){ tr.textContent = data.status; tr.className = 'badge ' + (data.status==='Resolved'?'text-bg-success':'text-bg-secondary') + ' status-badge'; }
		Traction.toast('Status updated', 'success');
	}catch(e){ Traction.toast('Network error', 'danger'); }
}
async function deleteSuggestion(id){
	if(!confirm('Delete this suggestion?')) return;
	try{
		const res = await fetch('/api/admin/delete_suggestion.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ suggestion_id:id }) });
		const data = await res.json();
		if(!res.ok || !data.success){ Traction.toast(data.message||'Failed', 'danger'); return; }
		const tr = document.querySelector('tr[data-id="'+id+'"]');
		if(tr) tr.remove();
		Traction.toast('Deleted', 'success');
	}catch(e){ Traction.toast('Network error', 'danger'); }
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
