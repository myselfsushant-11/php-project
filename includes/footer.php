<?php $base = $assetBase ?? ''; ?>
<footer class="lm-footer">
  <div class="container container-xl">
    <div class="row gy-4">
      <div class="col-md-3">
        <div class="lm-footer-brand mb-2">𝐂𝐈𝐍𝐄<span style="color:var(--lm-accent)">𝐩𝐡𝐢𝐥𝐞</span></div>
        <p class="small mb-3">Cinema, elevated. Book your seat under the lights.</p>
        <div class="lm-footer-social">
          <a href="https://github.com/yourusername/cinefile" target="_blank" rel="noopener noreferrer" class="lm-footer-social-link" aria-label="GitHub repository"><i class="bi bi-github"></i></a>
          <a href="https://your-portfolio.example.com" target="_blank" rel="noopener noreferrer" class="lm-footer-social-link" aria-label="Portfolio website"><i class="bi bi-globe2"></i></a>
          <a href="https://linkedin.com/in/yourusername" target="_blank" rel="noopener noreferrer" class="lm-footer-social-link" aria-label="LinkedIn profile"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
      <div class="col-md-3">
        <h6 class="lm-footer-heading">Explore</h6>
        <ul class="list-unstyled small lm-footer-links">
          <li class="mb-2"><a href="<?= $base ?>movies.php">Now Showing</a></li>
          <li class="mb-2"><a href="<?= $base ?>movies.php#coming-soon">Coming Soon</a></li>
          <li class="mb-2"><a href="<?= $base ?>my-bookings.php">My Bookings</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h6 class="lm-footer-heading">Support</h6>
        <ul class="list-unstyled small lm-footer-links">
          <li class="mb-2"><a href="<?= $base ?>faq.php">FAQ</a></li>
          <li class="mb-2"><a href="<?= $base ?>contact.php">Contact Us</a></li>
          <li class="mb-2"><a href="<?= $base ?>terms.php">Terms of Service</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h6 class="lm-footer-heading">Project</h6>
        <ul class="list-unstyled small lm-footer-links">
          <li class="mb-2"><a href="<?= $base ?>terms.php#academic-notice">Academic Notice</a></li>
          <li class="mb-2"><a href="https://github.com/yourusername/cinefile" target="_blank" rel="noopener noreferrer">Source on GitHub</a></li>
        </ul>
      </div>
    </div>
    <hr class="lm-footer-divider">
    <div class="lm-footer-bottom text-center">
      <p class="small mb-1">&copy; <?= date('Y') ?> CINEFILE Cinemas. All rights reserved.</p>
    </div>
  </div>
</footer>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="lmToastContainer"></div>

</body>
</html>