<?php
require_once __DIR__ . '/includes/auth.php';
$assetBase = '';
$pageTitle = 'CINEPHILE — Cinema, elevated';

$heroMovies = $pdo->query("SELECT * FROM movies WHERE status='now_showing' ORDER BY created_at DESC LIMIT 5")->fetchAll();
$nowShowing = $pdo->query("SELECT * FROM movies WHERE status='now_showing' ORDER BY created_at DESC LIMIT 4")->fetchAll();
$comingSoon = $pdo->query("SELECT * FROM movies WHERE status='coming_soon' ORDER BY release_date ASC LIMIT 6")->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<?php if ($heroMovies): ?>
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
  <div class="carousel-inner">
    <?php foreach ($heroMovies as $i => $hero): ?>
    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
      <section class="lm-hero" style="background-image:url('<?= htmlspecialchars($hero['backdrop'] ?: 'assets/images/movies/placeholder-backdrop.jpg') ?>');">
        <div class="lm-hero-content">
          <span class="lm-hero-badge">Now Showing</span>
          <h1 class="lm-hero-title"><?= htmlspecialchars($hero['title']) ?></h1>
          <div class="lm-hero-meta">
            <span><?= htmlspecialchars($hero['genre']) ?></span>
            <span class="dot"></span>
            <span><?= (int)$hero['duration'] ?> min</span>
            <span class="dot"></span>
            <span><?= htmlspecialchars($hero['language']) ?></span>
            <span class="lm-rating-pill"><i class="bi bi-star-fill"></i> <?= number_format((float)$hero['rating'], 1) ?></span>
          </div>
          <p class="lm-hero-desc"><?= htmlspecialchars(mb_strimwidth($hero['description'], 0, 200, '…')) ?></p>
          <div class="d-flex gap-3">
            <a href="movie.php?id=<?= $hero['id'] ?>#shows" class="btn btn-lumiere px-4"><i class="bi bi-ticket-perforated me-1"></i> Book Now</a>
            <a href="movie.php?id=<?= $hero['id'] ?>" class="btn btn-outline-lumiere px-4">More Details</a>
          </div>
        </div>
      </section>
    </div>
    <?php endforeach; ?>
  </div>
  <?php if (count($heroMovies) > 1): ?>
  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
  <?php endif; ?>
</div>
<?php endif; ?>

<section class="lm-section container container-xl" id="now-showing">
  <div class="lm-section-head">
    <h2><span class="lm-accent-bar"></span>Now Showing</h2>
    <a href="movies.php" class="btn btn-outline-lumiere btn-sm">View All <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="row g-4">
    <?php foreach ($nowShowing as $m): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="movie.php?id=<?= $m['id'] ?>" class="movie-card d-block">
        <div class="poster-wrap">
          <img src="<?= htmlspecialchars($m['poster'] ?: 'assets/images/movies/placeholder.jpg') ?>" alt="<?= htmlspecialchars($m['title']) ?>" loading="lazy">
          <?php if ($m['rating'] > 0): ?>
          <span class="rating-badge"><i class="bi bi-star-fill"></i> <?= number_format($m['rating'], 1) ?></span>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <div class="movie-title"><?= htmlspecialchars($m['title']) ?></div>
          <div class="movie-meta"><?= htmlspecialchars($m['genre']) ?> • <?= htmlspecialchars($m['language']) ?></div>
          <span class="btn btn-lumiere btn-sm w-100">Book Now</span>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php if ($comingSoon): ?>
<section class="lm-section container container-xl" id="coming-soon">
  <div class="lm-section-head">
    <h2><span class="lm-accent-bar"></span>Coming Soon</h2>
  </div>
  <div class="row g-4">
    <?php foreach ($comingSoon as $m): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="movie.php?id=<?= $m['id'] ?>" class="coming-card d-block">
        <div class="poster-wrap">
          <img src="<?= htmlspecialchars($m['poster'] ?: 'assets/images/movies/placeholder.jpg') ?>" alt="<?= htmlspecialchars($m['title']) ?>" loading="lazy">
          <span class="release-badge"><?= format_date_pretty($m['release_date']) ?></span>
        </div>
        <div class="p-2">
          <div class="movie-title" style="font-size:0.9rem;"><?= htmlspecialchars($m['title']) ?></div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>