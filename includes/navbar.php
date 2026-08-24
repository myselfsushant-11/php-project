<?php $base = $assetBase ?? ''; ?>
<nav class="navbar navbar-expand-lg lm-navbar">
  <div class="container container-xl">
    <a class="navbar-brand lm-brand" href="<?= $base ?>index.php">
𝐂𝐈𝐍𝐄<span>𝐩𝐡𝐢𝐥𝐞</span></a>

    <button class="navbar-toggler border-0 text-light" type="button" data-bs-toggle="collapse" data-bs-target="#lmNav">
      <i class="bi bi-list fs-2 text-light"></i>
    </button>

    <div class="collapse navbar-collapse" id="lmNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>movies.php">Movies</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>movies.php#coming-soon">Coming Soon</a></li>
        <?php if (is_logged_in()): ?>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>my-bookings.php">My Bookings</a></li>
        <?php endif; ?>
      </ul>

      <form class="d-flex position-relative lm-search-form me-3 mb-2 mb-lg-0" style="max-width:280px;" onsubmit="return false;">
        <input type="text" id="navSearchInput" class="form-control" placeholder="Search movies…" autocomplete="off">
        <div id="searchResultsBox"></div>
      </form>

      <?php if (is_logged_in()): ?>
        <div class="dropdown">
          <a class="btn btn-outline-lumiere dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i> <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end lm-surface border-0 mt-2">
            <li><a class="dropdown-item text-light" href="<?= $base ?>profile.php"><i class="bi bi-person me-2"></i>My Profile</a></li>
            <li><a class="dropdown-item text-light" href="<?= $base ?>my-bookings.php"><i class="bi bi-ticket-perforated me-2"></i>My Bookings</a></li>
            <li><a class="dropdown-item text-light" href="<?= $base ?>profile.php#password"><i class="bi bi-shield-lock me-2"></i>Change Password</a></li>
            <li><hr class="dropdown-divider" style="border-color:var(--lm-border)"></li>
            <li><a class="dropdown-item text-danger" href="<?= $base ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="<?= $base ?>login.php" class="btn btn-lumiere">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<script>
(function () {
  const input = document.getElementById('navSearchInput');
  const box = document.getElementById('searchResultsBox');
  let timer = null;
  input.addEventListener('input', function () {
    clearTimeout(timer);
    const q = this.value.trim();
    if (q.length < 2) { box.style.display = 'none'; return; }
    timer = setTimeout(() => {
      fetch('<?= $base ?>ajax/search_movies.php?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => {
          if (!data.success || data.movies.length === 0) {
            box.innerHTML = '<div class="p-3 text-muted small">No movies found.</div>';
          } else {
            box.innerHTML = data.movies.map(m => `
              <a href="<?= $base ?>movie.php?id=${m.id}">
                <img src="<?= $base ?>${m.poster || 'assets/images/movies/placeholder.jpg'}" onerror="this.style.display='none'">
                <div>
                  <div class="fw-semibold small">${m.title}</div>
                  <div class="text-muted" style="font-size:.72rem">${m.genre} • ${m.language}</div>
                </div>
              </a>`).join('');
          }
          box.style.display = 'block';
        });
    }, 300);
  });
  document.addEventListener('click', (e) => {
    if (!box.contains(e.target) && e.target !== input) box.style.display = 'none';
  });
})();
</script>
