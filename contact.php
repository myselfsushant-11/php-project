<?php
require_once __DIR__ . '/includes/auth.php';
$assetBase = '';
$pageTitle = 'Contact Us — CINEPHILE';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container container-xl py-5" style="max-width:640px;">
  <div class="mb-5">
    <span class="lm-hero-badge">Support</span>
    <h1 class="font-display fw-bold">Contact Us</h1>
    <p class="small mt-2" style="color:var(--lm-text-dim);">This is a static demo form — messages aren't actually sent anywhere.</p>
  </div>

  <form onsubmit="return false;">
    <div class="mb-3">
      <label class="lm-label">Name</label>
      <input type="text" class="form-control" placeholder="Your name" required>
    </div>
    <div class="mb-3">
      <label class="lm-label">Email</label>
      <input type="email" class="form-control" placeholder="you@example.com" required>
    </div>
    <div class="mb-4">
      <label class="lm-label">Message</label>
      <textarea class="form-control" rows="5" placeholder="How can we help?" required></textarea>
    </div>
    <button type="submit" class="btn btn-lumiere">Send Message</button>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>