<?php
require_once __DIR__ . '/includes/auth.php';
$assetBase = '';
$pageTitle = 'Terms of Service — CINEPHILE';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container container-xl py-5" style="max-width:720px;">
  <div class="mb-4">
    <span class="lm-hero-badge">Support</span>
    <h1 class="font-display fw-bold">Terms of Service</h1>
  </div>

  <div id="academic-notice" class="lm-surface p-4 mb-4" style="border-radius:var(--lm-radius); border:1px solid var(--lm-border);">
    <h6 class="lm-footer-heading mb-2">Academic Notice</h6>
    <p class="small mb-0" style="color:var(--lm-text-dim);">CINEPHILE is a student / academic demo project built to showcase full-stack development skills. It is not a real cinema booking service — no real payments are processed, and no real showtimes, halls, or transactions exist.</p>
  </div>

  <div class="small" style="color:var(--lm-text-dim); line-height:1.8;">
    <p>By using this demo, you agree that any data entered (accounts, bookings, payment details) is for demonstration purposes only and should not include real personal or financial information.</p>
    <p>All movie titles, posters, and related media shown are used for educational, non-commercial purposes.</p>
    <p>This project is provided as-is, with no warranty, as part of a portfolio / coursework submission.</p>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>