<?php
require_once __DIR__ . '/includes/auth.php';
require_login_redirect();
$assetBase = '';
$pageTitle = 'My Profile — CINEPHILE';

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5" style="max-width:640px;">
  <h3 class="font-display mb-4">My Profile</h3>

  <div class="lm-surface p-4 mb-4">
    <div class="d-flex align-items-center gap-3 mb-4">
      <img id="profilePreview" src="<?= htmlspecialchars($user['profile_image'] ?: 'assets/images/movies/placeholder-avatar.jpg') ?>"
           style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid var(--lm-border);">
      <div>
        <div class="fw-semibold fs-5"><?= htmlspecialchars($user['name']) ?></div>
        <div class="small text-muted"><?= htmlspecialchars($user['email']) ?></div>
        <div class="small text-muted">Member since <?= format_date_pretty($user['created_at']) ?></div>
      </div>
    </div>

    <form id="profileForm" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="lm-label">Profile Image</label>
        <input type="file" name="profile_image" id="profileImageInput" class="form-control lm-input" accept=".jpg,.jpeg,.png,.webp">
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="lm-label">Full Name</label>
          <input type="text" name="name" class="form-control lm-input" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="lm-label">Phone</label>
          <input type="text" name="phone" class="form-control lm-input" value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>
      </div>
      <div id="profileMsg" class="small mb-3" style="display:none;"></div>
      <button type="submit" class="btn btn-lumiere" id="profileBtn">Save Changes</button>
    </form>
  </div>

  <div class="lm-surface p-4" id="password">
    <h6 class="mb-3">Change Password</h6>
    <form id="passwordForm">
      <div class="mb-3">
        <label class="lm-label">Current Password</label>
        <input type="password" name="current_password" class="form-control lm-input" required>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="lm-label">New Password</label>
          <input type="password" name="new_password" class="form-control lm-input" required>
        </div>
        <div class="col-md-6">
          <label class="lm-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control lm-input" required>
        </div>
      </div>
      <div id="passwordMsg" class="small mb-3" style="display:none;"></div>
      <button type="submit" class="btn btn-outline-lumiere" id="passwordBtn">Update Password</button>
    </form>
  </div>
</div>

<script>
document.getElementById('profileImageInput').addEventListener('change', function () {
  if (this.files[0]) {
    document.getElementById('profilePreview').src = URL.createObjectURL(this.files[0]);
  }
});

document.getElementById('profileForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  const btn = document.getElementById('profileBtn');
  const msg = document.getElementById('profileMsg');
  lmSetLoading(btn, true, 'Saving…');
  const { data } = await lmFetch('ajax/update_profile.php', { method: 'POST', body: new FormData(this) });
  lmSetLoading(btn, false);
  msg.style.display = 'block';
  msg.className = 'small mb-3 ' + (data.success ? 'text-success' : 'text-danger');
  msg.textContent = data.message;
  if (data.success) lmToast(data.message, 'success');
});

document.getElementById('passwordForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  const btn = document.getElementById('passwordBtn');
  const msg = document.getElementById('passwordMsg');
  lmSetLoading(btn, true, 'Updating…');
  const { data } = await lmFetch('ajax/change_password.php', { method: 'POST', body: new FormData(this) });
  lmSetLoading(btn, false);
  msg.style.display = 'block';
  msg.className = 'small mb-3 ' + (data.success ? 'text-success' : 'text-danger');
  msg.textContent = data.message;
  if (data.success) { lmToast(data.message, 'success'); this.reset(); }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
