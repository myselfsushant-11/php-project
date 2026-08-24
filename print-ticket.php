<?php
require_once __DIR__ . '/includes/auth.php';
require_login_redirect();

$code = clean($_GET['id'] ?? '');
$stmt = $pdo->prepare(
    "SELECT b.*, s.show_date, s.show_time, m.title AS movie_title, h.name AS hall_name
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ticket <?= htmlspecialchars($booking['booking_code']) ?></title>
<style>
  body { font-family: 'Courier New', monospace; background:#fff; color:#000; padding:2rem; }
  .ticket { max-width:380px; margin:0 auto; border:2px dashed #333; padding:1.5rem; }
  h2 { text-align:center; margin:0 0 4px; letter-spacing:2px; }
  .sub { text-align:center; font-size:0.75rem; letter-spacing:3px; margin-bottom:1rem; }
  .divider { border-top:1px dashed #999; margin:12px 0; }
  .row { display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.9rem; }
  .lbl { color:#555; text-transform:uppercase; font-size:0.7rem; }
  .val { font-weight:bold; }
  .total { font-size:1.2rem; text-align:right; }
  @media print { body { padding:0; } }
</style>
</head>
<body onload="window.print()">
  <div class="ticket">
    <h2>CINEPHILE</h2>
    <div class="sub">MOVIE TICKET</div>
    <div class="divider"></div>
    <div class="row"><span class="lbl">Booking ID</span><span class="val"><?= htmlspecialchars($booking['booking_code']) ?></span></div>
    <div class="row"><span class="lbl">Movie</span><span class="val"><?= htmlspecialchars($booking['movie_title']) ?></span></div>
    <div class="row"><span class="lbl">Hall</span><span class="val"><?= htmlspecialchars($booking['hall_name']) ?></span></div>
    <div class="row"><span class="lbl">Date</span><span class="val"><?= format_date_pretty($booking['show_date']) ?></span></div>
    <div class="row"><span class="lbl">Time</span><span class="val"><?= format_time12($booking['show_time']) ?></span></div>
    <div class="row"><span class="lbl">Seats</span><span class="val"><?= htmlspecialchars(implode(', ', $seatLabels)) ?></span></div>
    <div class="divider"></div>
    <div class="row"><span class="lbl">Customer</span><span class="val"><?= htmlspecialchars($booking['customer_name']) ?></span></div>
    <div class="row"><span class="lbl">Tickets</span><span class="val"><?= (int)$booking['total_seats'] ?></span></div>
    <div class="divider"></div>
    <div class="total">Total: <?= format_rupees($booking['total_amount']) ?></div>
  </div>
</body>
</html>
