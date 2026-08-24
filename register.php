<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }
$assetBase = '';
$pageTitle = 'Register — CINEFILE';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="max-width:480px; padding-top:4rem; padding-bottom:5rem;">
  <div class="text-center mb-4">
    <div class="lm-brand fs-2">CINE<span style="color:var(--lm-accent)">PHILE</span></div>
    <p class="text-muted small mt-2">Create your account to start booking.</p>
  </div>

  <div class="lm-surface p-4 p-md-5">
    <h4 class="font-display mb-4">Register</h4>
    <form id="registerForm" novalidate>
      <div class="mb-3">
        <label class="lm-label">Full Name</label>
        <input type="text" name="name" class="form-control lm-input" placeholder="Sushant Bolakhe" required>
      </div>
      <div class="mb-3">
        <label class="lm-label">Email</label>
        <input type="email" name="email" class="form-control lm-input" placeholder="you@example.com" required>
      </div>
      <div class="mb-3">
        <label class="lm-label">Phone</label>
        <input type="text" name="phone" class="form-control lm-input" placeholder="98XXXXXXXX" required>
      </div>
      <div class="row">
        <div class="col-6 mb-3">
          <label class="lm-label">Password</label>
          <input type="password" name="password" class="form-control lm-input" placeholder="Min. 6 chars" required>
        </div>
        <div class="col-6 mb-3">
          <label class="lm-label">Confirm</label>
          <input type="password" name="confirm_password" class="form-control lm-input" placeholder="Repeat" required>
        </div>
      </div>
      <div id="regError" class="text-danger small mb-3" style="display:none;"></div>
      <button type="submit" class="btn btn-lumiere w-100" id="regBtn">Create Account</button>
    </form>
    <p class="text-center small text-muted mt-4 mb-0">
      Already have an account? <a href="login.php" style="color:var(--lm-accent)">Login</a>
    </p>
  </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  const btn = document.getElementById('regBtn');
  const errBox = document.getElementById('regError');
  errBox.style.display = 'none';
  lmSetLoading(btn, true, 'Creating account…');

  const formData = new FormData(this);
  const { data } = await lmFetch('ajax/register.php', { method: 'POST', body: formData });

  lmSetLoading(btn, false);
  if (data.success) {
    lmToast(data.message, 'success');
    setTimeout(() => window.location.href = 'login.php', 900);
  } else {
    errBox.textContent = data.message;
    errBox.style.display = 'block';
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
