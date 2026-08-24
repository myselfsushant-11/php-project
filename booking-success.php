<?php
require_once __DIR__ . '/includes/auth.php';
require_login_redirect();
$assetBase = '';

$code = clean($_GET['id'] ?? '');
$stmt = $pdo->prepare(
    "SELECT b.*, s.show_date, s.show_time, m.title AS movie_title, m.poster, h.name AS hall_name
     FROM bookings b
     JOIN showtimes s ON s.id = b.showtime_id
     JOIN movies m ON m.id = s.movie_id
     JOIN halls h ON h.id = s.hall_id
     WHERE b.booking_code = ? AND b.user_id = ?"
);
$stmt->execute([$code, $_SESSION['user_id']]);
$booking = $stmt->fetch();
if (!$booking) { http_response_code(404); die('Booking not found.'); }

$stmt = $pdo->prepare(
    "SELECT st.seat_label FROM booking_seats bs
     JOIN seats st ON st.id = bs.seat_id
     WHERE bs.booking_id = ? ORDER BY st.seat_row, st.seat_number"
);
$stmt->execute([$booking['id']]);
$seatLabels = array_column($stmt->fetchAll(), 'seat_label');

$pageTitle = 'Booking Confirmed — CINEPHILE';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">
  <div class="text-center mb-4">
    <i class="bi bi-check-circle-fill fs-1" style="color:var(--lm-success)"></i>
    <h3 class="font-display mt-2">Booking Confirmed!</h3>
    <p class="text-muted">Your digital ticket is ready below.</p>
  </div>

  <div class="ticket-card" id="ticketArea">
    <div class="ticket-notch left"></div>
    <div class="ticket-notch right"></div>
    <div class="text-center mb-3">
      <div class="lm-brand fs-4">CINE<span style="color:var(--lm-accent)">FILE</span></div>
      <div class="small text-muted" style="letter-spacing:2px;">MOVIE TICKET</div>
    </div>
    <div class="ticket-divider"></div>
    <div class="ticket-row"><span class="lbl">Booking ID</span><span class="val"><?= htmlspecialchars($booking['booking_code']) ?></span></div>
    <div class="ticket-row"><span class="lbl">Movie</span><span class="val"><?= htmlspecialchars($booking['movie_title']) ?></span></div>
    <div class="ticket-row"><span class="lbl">Hall</span><span class="val"><?= htmlspecialchars($booking['hall_name']) ?></span></div>
    <div class="ticket-row"><span class="lbl">Date</span><span class="val"><?= format_date_pretty($booking['show_date']) ?></span></div>
    <div class="ticket-row"><span class="lbl">Time</span><span class="val"><?= format_time12($booking['show_time']) ?></span></div>
    <div class="ticket-row"><span class="lbl">Seats</span><span class="val"><?= htmlspecialchars(implode(', ', $seatLabels)) ?></span></div>
    <div class="ticket-divider"></div>
    <div class="ticket-row"><span class="lbl">Customer</span><span class="val"><?= htmlspecialchars($booking['customer_name']) ?></span></div>
    <div class="ticket-row"><span class="lbl">Tickets</span><span class="val"><?= (int)$booking['total_seats'] ?></span></div>
    <div class="ticket-row"><span class="lbl">Total</span><span class="val fs-5" style="color:var(--lm-accent)"><?= format_rupees($booking['total_amount']) ?></span></div>
    <div class="ticket-divider"></div>
    <div class="text-center small text-muted">Payment Status: <strong class="text-light"><?= ucfirst($booking['payment_status']) ?></strong></div>
  </div>

  <div class="text-center mt-4 d-flex gap-3 justify-content-center">
    <a href="print-ticket.php?id=<?= urlencode($booking['booking_code']) ?>" target="_blank" class="btn btn-lumiere"><i class="bi bi-printer me-2"></i>Print Ticket</a>
    <a href="my-bookings.php" class="btn btn-outline-lumiere">My Bookings</a>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
