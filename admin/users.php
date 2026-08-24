<?php
$pageTitle = 'Users — CINEFILE Admin';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>
<div class="admin-main">
  <div class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-lumiere d-md-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <h5 class="mb-0 font-display">Users</h5>
    </div>
    <span class="text-muted small" id="userCount"></span>
  </div>

  <div class="admin-content">
    <div class="lm-surface p-3 mb-3">
      <input type="text" id="userSearch" class="form-control lm-input" placeholder="Search by name or email…">
    </div>

    <div class="lm-surface p-3">
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th><th>Total Bookings</th><th>Status</th></tr></thead>
          <tbody id="usersTbody"><tr><td colspan="6" class="text-center text-muted py-4">Loading…</td></tr></tbody>
        </table>
      </div>
    </div>
    <p class="small text-muted mt-3"><i class="bi bi-shield-lock me-1"></i>Passwords are hashed and never displayed, by design.</p>
  </div>
</div>

<script>
async function loadUsers(q = '') {
  const { data } = await lmFetch('ajax/users.php?q=' + encodeURIComponent(q));
  const users = data.users || [];
  document.getElementById('userCount').textContent = users.length + ' user(s)';
  const tbody = document.getElementById('usersTbody');
  if (!users.length) { tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>'; return; }
  tbody.innerHTML = users.map(u => `
    <tr>
      <td>${u.name}</td>
      <td>${u.email}</td>
      <td>${u.phone}</td>
      <td>${new Date(u.created_at).toLocaleDateString()}</td>
      <td>${u.total_bookings}</td>
      <td><span class="badge bg-success">Active</span></td>
    </tr>`).join('');
}

let searchTimer;
document.getElementById('userSearch').addEventListener('input', function () {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => loadUsers(this.value), 300);
});

loadUsers();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
