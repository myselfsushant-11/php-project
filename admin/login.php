<?php
require_once __DIR__ . '/../includes/auth.php';
if (is_admin_logged_in()) { header('Location: dashboard.php'); exit; }
$assetBase = '../';
$pageTitle = 'Admin Login — CINEFILE';
include __DIR__ . '/../includes/header.php';
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh; max-width:440px;">
  <div class="w-100">
    <div class="text-center mb-4">
      <div class="lm-brand fs-2">CINE<span style="color:var(--lm-accent)">FILE</span></div>
      <p class="text-muted small mt-2">Studio Admin Console</p>
    </div>
    <div class="lm-surface p-4 p-md-5">
      <h4 class="font-display mb-4">Admin Login</h4>
      <form id="adminLoginForm" novalidate>
        <div class="mb-3">
          <label class="lm-label">Email</label>
          <input type="email" name="email" class="form-control lm-input" placeholder="admin@cinefile.com" required>
        </div>
        <div class="mb-3">
          <label class="lm-label">Password</label>
          <input type="password" name="password" class="form-control lm-input" placeholder="••••••••" required>
        </div>
        <div id="adminLoginError" class="text-danger small mb-3" style="display:none;"></div>
        <button type="submit" class="btn btn-lumiere w-100" id="adminLoginBtn">Login</button>
      </form>
      <p class="text-center small text-muted mt-4 mb-0">Default: admin@cinefile.com / Admin@123</p>
    </div>
  </div>
</div>

<script>
document.getElementById('adminLoginForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  const btn = document.getElementById('adminLoginBtn');
  const errBox = document.getElementById('adminLoginError');
  errBox.style.display = 'none';
  lmSetLoading(btn, true, 'Logging in…');
  const { data } = await lmFetch('ajax/login.php', { method: 'POST', body: new FormData(this) });
  lmSetLoading(btn, false);
  if (data.success) {
    lmToast(data.message, 'success');
    setTimeout(() => window.location.href = 'dashboard.php', 500);
  } else {
    errBox.textContent = data.message;
    errBox.style.display = 'block';
  }
});
</script>
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="lmToastContainer"></div>
<script src="../assets/js/app.js"></script>
</body>
</html>
