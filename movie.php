<?php
require_once __DIR__ . '/includes/auth.php';
$assetBase = '';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM movies WHERE id = ?');
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    http_response_code(404);
    die('Movie not found.');
}

$pageTitle = htmlspecialchars($movie['title']) . ' — CINEPHILE';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<section class="lm-hero" style="min-height:56vh; background-image:url('<?= htmlspecialchars($movie['backdrop'] ?: 'assets/images/movies/placeholder-backdrop.jpg') ?>');">
  <div class="lm-hero-content">

    <span class="lm-hero-badge"><?= $movie['status'] === 'now_showing' ? 'Now Showing' : 'Coming Soon' ?></span>
    <h1 class="lm-hero-title"><?= htmlspecialchars($movie['title']) ?></h1>
    <div class="lm-hero-meta">
      <span><?= htmlspecialchars($movie['genre']) ?></span>
      <span class="dot"></span>
      <span><?= (int)$movie['duration'] ?> min</span>
      <span class="dot"></span>
      <span><?= htmlspecialchars($movie['language']) ?></span>
      <?php if ($movie['rating'] > 0): ?>
      <span class="lm-rating-pill"><i class="bi bi-star-fill"></i> <?= number_format($movie['rating'],1) ?></span>
      <?php endif; ?>
    </div>
  </div>
</section>

<div class="container container-xl py-5">
  <div class="row g-5">
    <div class="col-lg-4">
      <img src="<?= htmlspecialchars($movie['poster'] ?: 'assets/images/movies/placeholder.jpg') ?>" class="img-fluid rounded-4 shadow" style="width:100%; aspect-ratio:2/3; object-fit:cover;" alt="<?= htmlspecialchars($movie['title']) ?>">
      <?php if ($movie['trailer_url']): ?>
      <a href="#" data-bs-toggle="modal" data-bs-target="#trailerModal" class="btn btn-outline-lumiere w-100 mt-3"><i class="bi bi-play-circle me-2"></i>Watch Trailer</a>
      <?php endif; ?>
    </div>
    <div class="col-lg-8">
      <h4 class="font-display mb-3">Synopsis</h4>
      <p class="text-muted"><?= nl2br(htmlspecialchars($movie['description'])) ?></p>
      <div class="row g-3 mt-3">
        <div class="col-6 col-md-3"><div class="lm-surface p-3 text-center"><div class="small text-muted">Release Date</div><div class="fw-semibold"><?= format_date_pretty($movie['release_date']) ?></div></div></div>
        <div class="col-6 col-md-3"><div class="lm-surface p-3 text-center"><div class="small text-muted">Duration</div><div class="fw-semibold"><?= (int)$movie['duration'] ?> min</div></div></div>
        <div class="col-6 col-md-3"><div class="lm-surface p-3 text-center"><div class="small text-muted">Language</div><div class="fw-semibold"><?= htmlspecialchars($movie['language']) ?></div></div></div>
        <div class="col-6 col-md-3"><div class="lm-surface p-3 text-center"><div class="small text-muted">Genre</div><div class="fw-semibold"><?= htmlspecialchars($movie['genre']) ?></div></div></div>
      </div>
    </div>
  </div>

  <?php if ($movie['status'] === 'now_showing'): ?>
  <div id="shows" class="mt-5 pt-3">
    <h4 class="font-display mb-4"><span class="lm-accent-bar"></span>Available Shows</h4>

    <div class="date-scroll mb-4" id="dateTabs">
      <?php for ($i = 0; $i < 7; $i++):
        $d = date('Y-m-d', strtotime("+$i day"));
        $label = $i === 0 ? 'Today' : date('D', strtotime($d));
        $num = date('d', strtotime($d));
        $mon = date('M', strtotime($d));
      ?>
      <div class="date-tab <?= $i === 0 ? 'active' : '' ?>" data-date="<?= $d ?>">
        <div class="d-day"><?= $label ?></div>
        <div class="d-num"><?= $num ?> <?= $mon ?></div>
      </div>
      <?php endfor; ?>
    </div>

    <div id="showtimesContainer">
      <div class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Loading showtimes…</div>
    </div>
  </div>
  <?php else: ?>
  <div class="lm-surface p-4 mt-5 text-center">
    <i class="bi bi-hourglass-split fs-2 d-block mb-2" style="color:var(--lm-accent)"></i>
    <p class="mb-0">Showtimes will be available closer to the release date — <?= format_date_pretty($movie['release_date']) ?>.</p>
  </div>
  <?php endif; ?>
</div>

<?php if ($movie['trailer_url']): ?>
<div class="modal fade" id="trailerModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content lm-surface border-0">
      <div class="modal-header border-0"><h5 class="modal-title">Trailer</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <div class="modal-body ratio ratio-16x9">
        <iframe src="" id="trailerFrame" allowfullscreen></iframe>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('trailerModal')?.addEventListener('show.bs.modal', function () {
  document.getElementById('trailerFrame').src = "<?= htmlspecialchars($movie['trailer_url']) ?>";
});
document.getElementById('trailerModal')?.addEventListener('hidden.bs.modal', function () {
  document.getElementById('trailerFrame').src = "";
});
</script>
<?php endif; ?>

<script>
const dateTabs = document.querySelectorAll('.date-tab');
const showtimesContainer = document.getElementById('showtimesContainer');
const movieId = <?= (int)$movie['id'] ?>;

async function loadShowtimes(date) {
  showtimesContainer.innerHTML = '<div class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Loading showtimes…</div>';
  const { data } = await lmFetch(`ajax/get_showtimes.php?movie_id=${movieId}&date=${date}`);
  if (!data.success || Object.keys(data.halls).length === 0) {
    showtimesContainer.innerHTML = '<div class="lm-surface p-4 text-center text-muted">No shows scheduled for this date.</div>';
    return;
  }
  let html = '';
  for (const hall in data.halls) {
    html += `<div class="lm-surface p-3 p-md-4 mb-3">
      <div class="fw-semibold mb-3"><i class="bi bi-camera-reels me-2" style="color:var(--lm-accent)"></i>${hall}</div>
      <div class="d-flex flex-wrap gap-2">`;
    data.halls[hall].forEach(s => {
      html += `<a href="seat-selection.php?showtime_id=${s.id}" class="showtime-pill">${s.time}<div class="small text-muted fw-normal">Rs. ${Number(s.price).toLocaleString()}</div></a>`;
    });
    html += `</div></div>`;
  }
  showtimesContainer.innerHTML = html;
}

dateTabs.forEach(tab => {
  tab.addEventListener('click', () => {
    dateTabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    loadShowtimes(tab.dataset.date);
  });
});

if (dateTabs.length) loadShowtimes(dateTabs[0].dataset.date);
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
