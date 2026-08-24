<?php
require_once __DIR__ . '/includes/auth.php';
require_login_redirect();
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
if (!$show) { http_response_code(404); die('Showtime not found.'); }

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$pageTitle = 'Checkout — CINEPHILE';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5" style="max-width:760px;">
  <h3 class="font-display mb-4">Checkout</h3>

  <div class="lm-surface p-4 mb-4">
    <div class="d-flex gap-3 mb-3">
      <img src="<?= htmlspecialchars($show['poster'] ?: 'assets/images/movies/placeholder.jpg') ?>" style="width:64px; height:90px; object-fit:cover; border-radius:8px;">
      <div>
        <h5 class="font-display mb-1"><?= htmlspecialchars($show['movie_title']) ?></h5>
        <div class="small text-muted"><?= htmlspecialchars($show['hall_name']) ?> • <?= format_date_pretty($show['show_date']) ?> • <?= format_time12($show['show_time']) ?></div>
      </div>
    </div>
    <hr style="border-color:var(--lm-border)">
    <div class="row small">
      <div class="col-6 mb-2 text-muted">Selected Seats</div>
      <div class="col-6 mb-2 text-end fw-semibold" id="coSeats">—</div>
      <div class="col-6 mb-2 text-muted">Price / Ticket</div>
      <div class="col-6 mb-2 text-end fw-semibold">Rs. <?= number_format($show['ticket_price'],0) ?></div>
      <div class="col-6 text-muted">Total Amount</div>
      <div class="col-6 text-end fs-5 fw-bold" style="color:var(--lm-accent)" id="coTotal">Rs. 0</div>
    </div>
  </div>

  <div class="lm-surface p-4">
    <h6 class="mb-3">Your Details</h6>
    <form id="checkoutForm">
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="lm-label">Full Name</label>
          <input type="text" name="customer_name" class="form-control lm-input" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="lm-label">Phone</label>
          <input type="text" name="customer_phone" class="form-control lm-input" value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>
        <div class="col-12">
          <label class="lm-label">Email</label>
          <input type="email" name="customer_email" class="form-control lm-input" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
      </div>
      <div class="lm-surface p-3 mb-3 small" style="background:var(--lm-bg-soft)">
        <i class="bi bi-info-circle me-1" style="color:var(--lm-accent)"></i>
        Payment Status: <strong>Confirmed</strong> — this academic demo does not process real payments.
      </div>
      <div id="checkoutError" class="text-danger small mb-3" style="display:none;"></div>
      <button type="submit" class="btn btn-lumiere w-100" id="confirmBtn">Confirm Booking</button>
    </form>
  </div>
</div>

<script>
const showtimeId = <?= (int)$show['id'] ?>;
const ticketPrice = <?= (float)$show['ticket_price'] ?>;
let seatIds = [];

(function init() {
  const storedShowtime = sessionStorage.getItem('lm_showtime_id');
  const stored = sessionStorage.getItem('lm_selected_seats');
  if (!stored || parseInt(storedShowtime) !== showtimeId) {
    window.location.href = 'seat-selection.php?showtime_id=' + showtimeId;
    return;
  }
  seatIds = JSON.parse(stored);
  if (!seatIds.length) {
    window.location.href = 'seat-selection.php?showtime_id=' + showtimeId;
    return;
  }
  document.getElementById('coTotal').textContent = 'Rs. ' + (seatIds.length * ticketPrice).toLocaleString();
  fetch(`ajax/get_seats.php?showtime_id=${showtimeId}`).then(r => r.json()).then(data => {
    const labels = data.seats.filter(s => seatIds.includes(s.id)).map(s => s.seat_label);
    document.getElementById('coSeats').textContent = labels.join(', ');
  });
})();

document.getElementById('checkoutForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  const btn = document.getElementById('confirmBtn');
  const errBox = document.getElementById('checkoutError');
  errBox.style.display = 'none';
  lmSetLoading(btn, true, 'Confirming booking…');

  const formData = new FormData(this);
  formData.append('showtime_id', showtimeId);
  formData.append('seat_ids', JSON.stringify(seatIds));

  const { data } = await lmFetch('ajax/create_booking.php', { method: 'POST', body: formData });
  lmSetLoading(btn, false);

  if (data.success) {
    sessionStorage.removeItem('lm_selected_seats');
    sessionStorage.removeItem('lm_showtime_id');
    lmToast('Booking confirmed!', 'success');
    setTimeout(() => window.location.href = 'booking-success.php?id=' + data.booking_code, 700);
  } else {
    errBox.textContent = data.message;
    errBox.style.display = 'block';
    if (data.seats_taken) {
      setTimeout(() => window.location.href = 'seat-selection.php?showtime_id=' + showtimeId, 2200);
    }
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
