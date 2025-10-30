(function(){
	function showToast(message, type){
		const container = document.getElementById('toast-container');
		if(!container) return;
		const wrapper = document.createElement('div');
		wrapper.className = 'toast align-items-center text-bg-' + (type || 'primary') + ' border-0';
		wrapper.setAttribute('role', 'alert');
		wrapper.setAttribute('aria-live', 'assertive');
		wrapper.setAttribute('aria-atomic', 'true');
		wrapper.innerHTML = '<div class="d-flex"><div class="toast-body">'+ message +'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>';
		container.appendChild(wrapper);
		const toast = new bootstrap.Toast(wrapper, { delay: 2500 });
		toast.show();
		wrapper.addEventListener('hidden.bs.toast', function(){ wrapper.remove(); });
	}
	function initTheme(){
		let saved = localStorage.getItem('ti-theme');
		if(!saved){
			saved = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
		}
		document.documentElement.setAttribute('data-bs-theme', saved);
		const btn = document.getElementById('theme-toggle');
		if(btn){ btn.textContent = saved === 'dark' ? 'Light' : 'Dark'; }
	}
	function toggleTheme(){
		const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
		const next = current === 'light' ? 'dark' : 'light';
		document.documentElement.setAttribute('data-bs-theme', next);
		localStorage.setItem('ti-theme', next);
		const btn = document.getElementById('theme-toggle');
		if(btn){ btn.textContent = next === 'dark' ? 'Light' : 'Dark'; }
	}
	// Initialize ASAP for better UX if this script loads before user interaction
	initTheme();
	window.addEventListener('DOMContentLoaded', function(){
		const btn = document.getElementById('theme-toggle');
		if(btn){ btn.addEventListener('click', toggleTheme); }
	});
	window.Traction = {
		toast: showToast,
		upvote: async function(suggestionId){
			try{
				const res = await fetch('/api/upvote.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ suggestion_id: suggestionId })
				});
				const data = await res.json();
				if (!res.ok || !data.success){
					showToast(data.message || 'Failed to upvote', 'danger');
					return;
				}
				const badge = document.querySelector('[data-votes-for="'+ suggestionId +'"]');
				if (badge) badge.textContent = data.votes;
				showToast('Upvoted!', 'success');
			}catch(e){
				showToast('Network error', 'danger');
			}
		}
	};
})();
