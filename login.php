<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }
$assetBase = '';
$pageTitle = 'Login — CINEPHILE';
$redirect = $_GET['redirect'] ?? 'index.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="max-width:440px; padding-top:4rem; padding-bottom:5rem;">
  <div class="text-center mb-4">
    <div class="lm-brand fs-2">CINE<span style="color:var(--lm-accent)">PHILE</span></div>
    <p class="text-muted small mt-2">Welcome back. Your seat is waiting.</p>
  </div>

  <div class="lm-surface p-4 p-md-5">
    <h4 class="font-display mb-4">Login</h4>
    <form id="loginForm" novalidate>
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <div class="mb-3">
        <label class="lm-label">Email</label>
        <input type="email" name="email" class="form-control lm-input" placeholder="you@example.com" required>
      </div>
      <div class="mb-3">
        <label class="lm-label">Password</label>
        <input type="password" name="password" class="form-control lm-input" placeholder="••••••••" required>
      </div>
      <div id="loginError" class="text-danger small mb-3" style="display:none;"></div>
      <button type="submit" class="btn btn-lumiere w-100" id="loginBtn">Login</button>
    </form>
    <p class="text-center small text-muted mt-4 mb-0">
      Don't have an account? <a href="register.php" style="color:var(--lm-accent)">Register</a>
    </p>
  </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  const btn = document.getElementById('loginBtn');
  const errBox = document.getElementById('loginError');
  errBox.style.display = 'none';
  lmSetLoading(btn, true, 'Logging in…');

  const formData = new FormData(this);
  const { data } = await lmFetch('ajax/login.php', { method: 'POST', body: formData });

  lmSetLoading(btn, false);
  if (data.success) {
    lmToast(data.message, 'success');
    setTimeout(() => window.location.href = data.redirect || 'index.php', 600);
  } else {
    errBox.textContent = data.message;
    errBox.style.display = 'block';
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
