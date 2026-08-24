<?php
require_once __DIR__ . '/includes/auth.php';
$assetBase = '';

$showtimeId = (int) ($_GET['showtime_id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT s.*, m.title AS movie_title, m.poster, h.name AS hall_name
     FROM showtimes s
     JOIN movies m ON m.id = s.movie_id
     JOIN halls h ON h.id = s.hall_id
     WHERE s.id = ?"
);
$stmt->execute([$showtimeId]);
$show = $stmt->fetch();

if (!$show) {
    http_response_code(404);
    die('Showtime not found.');
}

$pageTitle = 'Select Seats — ' . htmlspecialchars($show['movie_title']);
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5" style="max-width:900px;">
  <div class="d-flex align-items-center gap-3 mb-4">
    <img src="<?= htmlspecialchars($show['poster'] ?: 'assets/images/movies/placeholder.jpg') ?>" style="width:56px; height:78px; object-fit:cover; border-radius:8px;">
    <div>
      <h5 class="mb-1 font-display"><?= htmlspecialchars($show['movie_title']) ?></h5>
      <div class="small text-muted"><?= htmlspecialchars($show['hall_name']) ?> • <?= format_date_pretty($show['show_date']) ?> • <?= format_time12($show['show_time']) ?></div>
    </div>
  </div>

  <div class="screen-curve">
    <div class="curve"></div>
    <small>Screen this way</small>
  </div>

  <div id="seatMap" class="seat-map mb-4">
    <div class="text-center text-muted py-5"><span class="spinner-border spinner-border-sm me-2"></span>Loading seats…</div>
  </div>

  <div class="d-flex justify-content-center gap-4 mb-5 small">
    <span><span class="seat-legend-box" style="background:var(--lm-surface); border:1.5px solid var(--lm-border);"></span>Available</span>
    <span><span class="seat-legend-box" style="background:var(--lm-accent);"></span>Selected</span>
    <span><span class="seat-legend-box" style="background:#2a2a36;"></span>Booked</span>
  </div>

  <div class="lm-surface p-4">
    <div class="row align-items-center">
      <div class="col-md-8">
        <div class="small text-muted mb-1">Selected Seats</div>
        <div id="selectedSeatsText" class="fw-semibold mb-2">None selected</div>
        <div class="small text-muted">Tickets: <span id="ticketCount">0</span> × Rs. <?= number_format($show['ticket_price'],0) ?></div>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <div class="small text-muted">Total</div>
        <div class="fs-3 fw-bold" style="color:var(--lm-accent)">Rs. <span id="totalAmount">0</span></div>
      </div>
    </div>
    <hr style="border-color:var(--lm-border)">
    <button id="continueBtn" class="btn btn-lumiere w-100" disabled>Continue</button>
  </div>
</div>

<!-- Login required modal -->
<div class="modal fade" id="loginRequiredModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content lm-surface border-0 text-center p-4">
      <i class="bi bi-lock fs-1 mb-2" style="color:var(--lm-accent)"></i>
      <h5 class="font-display">Login Required</h5>
      <p class="text-muted">Please login or create an account before booking your tickets.</p>
      <div class="d-flex gap-2 justify-content-center">
        <a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-lumiere">Login</a>
        <a href="register.php" class="btn btn-outline-lumiere">Register</a>
      </div>
    </div>
  </div>
</div>

<script>
const showtimeId = <?= (int)$show['id'] ?>;
const ticketPrice = <?= (float)$show['ticket_price'] ?>;
const isLoggedIn = <?= is_logged_in() ? 'true' : 'false' ?>;
const MAX_SEATS = 5;
let selected = [];
let allSeats = [];

async function loadSeats() {
  const { data } = await lmFetch(`ajax/get_seats.php?showtime_id=${showtimeId}`);
  if (!data.success) { document.getElementById('seatMap').innerHTML = '<p class="text-danger text-center">Could not load seats.</p>'; return; }
  allSeats = data.seats;
  renderSeats();
}

function renderSeats() {
  const rows = {};
  allSeats.forEach(s => { (rows[s.seat_row] ||= []).push(s); });
  let html = '';
  Object.keys(rows).sort().forEach(r => {
    html += `<div class="seat-row"><div class="row-label">${r}</div>`;
    rows[r].forEach(s => {
      const cls = s.status === 'booked' ? 'booked' : (selected.includes(s.id) ? 'selected' : '');
      html += `<div class="seat ${cls}" data-id="${s.id}" data-label="${s.seat_label}" data-status="${s.status}">${s.seat_number}</div>`;
    });
    html += `</div>`;
  });
  document.getElementById('seatMap').innerHTML = html;

  document.querySelectorAll('.seat').forEach(el => {
    el.addEventListener('click', () => {
      if (el.dataset.status === 'booked') return;
      const id = parseInt(el.dataset.id);
      if (selected.includes(id)) {
        selected = selected.filter(x => x !== id);
      } else {
        if (selected.length >= MAX_SEATS) {
          lmToast(`You can book up to ${MAX_SEATS} seats at a time. Complete this booking first, then book again for more seats.`, 'error');
          return;
        }
        selected.push(id);
      }
      renderSeats();
      updateSummary();
    });
  });
}

function updateSummary() {
  const labels = allSeats.filter(s => selected.includes(s.id)).map(s => s.seat_label);
  document.getElementById('selectedSeatsText').textContent = labels.length ? labels.join(', ') : 'None selected';
  document.getElementById('ticketCount').textContent = labels.length;
  document.getElementById('totalAmount').textContent = (labels.length * ticketPrice).toLocaleString();
  document.getElementById('continueBtn').disabled = labels.length === 0;
}

document.getElementById('continueBtn').addEventListener('click', () => {
  if (!isLoggedIn) {
    new bootstrap.Modal(document.getElementById('loginRequiredModal')).show();
    return;
  }
  sessionStorage.setItem('lm_selected_seats', JSON.stringify(selected));
  sessionStorage.setItem('lm_showtime_id', showtimeId);
  window.location.href = 'checkout.php?showtime_id=' + showtimeId;
});

loadSeats();
</script>