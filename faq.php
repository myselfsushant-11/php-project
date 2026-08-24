<?php
require_once __DIR__ . '/includes/auth.php';
$assetBase = '';
$pageTitle = 'FAQ — CINEFILE';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';

$faqs = [
  ['How do I book a seat?', 'Pick a movie, choose a showtime, select your seats on the seat map, and confirm at checkout.'],
  ['Can I cancel a booking?', 'Head to "My Bookings" in your profile menu — cancellations are available up until the showtime, subject to the cinema\'s policy.'],
  ['Is payment real?', 'No. CINEFILE is an academic demo project, so no real payments are processed anywhere on this site.'],
  ['Do I need an account to browse movies?', 'No — browsing and search are open to everyone. You\'ll only need an account to complete a booking.'],
  ['How do I change my password?', 'Go to your Profile page and open the "Change Password" section.'],
];
?>

<div class="container container-xl py-5" style="max-width:820px;">
  <div class="mb-5">
    <span class="lm-hero-badge">Support</span>
    <h1 class="font-display fw-bold">Frequently Asked Questions</h1>
  </div>

  <div class="accordion" id="faqAccordion">
    <?php foreach ($faqs as $i => $f): ?>
    <div class="accordion-item lm-surface border-0 mb-3" style="border-radius:var(--lm-radius); overflow:hidden;">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed text-light" style="background:var(--lm-surface);" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>">
          <?= htmlspecialchars($f[0]) ?>
        </button>
      </h2>
      <div id="faq<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
        <div class="accordion-body small" style="color:var(--lm-text-dim);"><?= htmlspecialchars($f[1]) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <p class="small mt-4" style="color:var(--lm-text-dim);">Still have a question? <a href="contact.php" style="color:var(--lm-accent);">Contact us</a>.</p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>