<?php
require_once __DIR__ . '/includes/auth.php';
$assetBase = '';
$pageTitle = 'Movies — CINEPHILE';

$nowShowing = $pdo->query("SELECT * FROM movies WHERE status='now_showing' ORDER BY created_at DESC")->fetchAll();
$comingSoon = $pdo->query("SELECT * FROM movies WHERE status='coming_soon' ORDER BY release_date ASC")->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container container-xl py-5">
  <div class="mb-5">
    <span class="lm-hero-badge">Browse</span>
    <h1 class="font-display fw-bold">All Movies</h1>
  </div>

  <div class="lm-section-head" id="now">
    <h2><span class="lm-accent-bar"></span>Now Showing</h2>
  </div>
  <div class="row g-4 mb-5">
    <?php foreach ($nowShowing as $m): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="movie.php?id=<?= $m['id'] ?>" class="movie-card d-block">
        <div class="poster-wrap">
          <img src="<?= htmlspecialchars($m['poster'] ?: 'assets/images/movies/placeholder.jpg') ?>" alt="<?= htmlspecialchars($m['title']) ?>" loading="lazy">
          <?php if ($m['rating'] > 0): ?><span class="rating-badge"><i class="bi bi-star-fill"></i> <?= number_format($m['rating'],1) ?></span><?php endif; ?>
        </div>
        <div class="card-body">
          <div class="movie-title"><?= htmlspecialchars($m['title']) ?></div>
          <div class="movie-meta"><?= htmlspecialchars($m['genre']) ?> • <?= htmlspecialchars($m['language']) ?> • <?= (int)$m['duration'] ?>min</div>
          <span class="btn btn-lumiere btn-sm w-100">Book Now</span>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
    <?php if (!$nowShowing): ?><p class="text-muted">No movies currently showing.</p><?php endif; ?>
  </div>

  <div class="lm-section-head" id="coming-soon">
    <h2><span class="lm-accent-bar"></span>Coming Soon</h2>
  </div>
  <div class="row g-4">
    <?php foreach ($comingSoon as $m): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="movie.php?id=<?= $m['id'] ?>" class="coming-card d-block">
        <div class="poster-wrap">
          <img src="<?= htmlspecialchars($m['poster'] ?: 'assets/images/movies/placeholder.jpg') ?>" alt="<?= htmlspecialchars($m['title']) ?>" loading="lazy">
          <span class="release-badge">Releases <?= format_date_pretty($m['release_date']) ?></span>
        </div>
        <div class="card-body">
          <div class="movie-title"><?= htmlspecialchars($m['title']) ?></div>
          <div class="movie-meta"><?= htmlspecialchars($m['genre']) ?></div>
          <span class="btn btn-outline-lumiere btn-sm w-100">View Details</span>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
    <?php if (!$comingSoon): ?><p class="text-muted">No upcoming releases yet.</p><?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
