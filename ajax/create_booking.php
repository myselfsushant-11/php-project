<?php
require_once __DIR__ . '/../includes/auth.php';
require_login_json();

$showtimeId    = (int) ($_POST['showtime_id'] ?? 0);
$seatIds       = json_decode($_POST['seat_ids'] ?? '[]', true);
$customerName  = clean($_POST['customer_name'] ?? '');
$customerEmail = clean($_POST['customer_email'] ?? '');
$customerPhone = clean($_POST['customer_phone'] ?? '');

if (!$showtimeId || !is_array($seatIds) || count($seatIds) === 0) {
    json_out(['success' => false, 'message' => 'No seats selected.']);
}
if ($customerName === '' || $customerEmail === '' || $customerPhone === '') {
    json_out(['success' => false, 'message' => 'Please fill in all your details.']);
}

$seatIds = array_values(array_unique(array_map('intval', $seatIds)));

$stmt = $pdo->prepare('SELECT id, ticket_price FROM showtimes WHERE id = ?');
$stmt->execute([$showtimeId]);
$showtime = $stmt->fetch();
if (!$showtime) {
    json_out(['success' => false, 'message' => 'Showtime not found.']);
}

$pdo->beginTransaction();
try {
    // Lock and verify the requested seats belong to this showtime and are still available
    $placeholders = implode(',', array_fill(0, count($seatIds), '?'));
    $stmt = $pdo->prepare(
        "SELECT id, status FROM seats WHERE showtime_id = ? AND id IN ($placeholders) FOR UPDATE"
    );
    $stmt->execute(array_merge([$showtimeId], $seatIds));
    $seats = $stmt->fetchAll();

    $takenOrMissing = count($seats) !== count($seatIds)
        || array_filter($seats, fn($s) => $s['status'] !== 'available');

    if ($takenOrMissing) {
        $pdo->rollBack();
        json_out(['success' => false, 'message' => 'One or more selected seats are no longer available.', 'seats_taken' => true]);
    }

    $ticketPrice = (float) $showtime['ticket_price'];
    $totalSeats  = count($seatIds);
    $totalAmount = $ticketPrice * $totalSeats;
    $bookingCode = generate_booking_code($pdo);

    $stmt = $pdo->prepare(
        'INSERT INTO bookings (booking_code, user_id, showtime_id, customer_name, customer_email, customer_phone, total_seats, total_amount, status, payment_status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, "confirmed", "confirmed")'
    );
    $stmt->execute([
        $bookingCode, $_SESSION['user_id'], $showtimeId,
        $customerName, $customerEmail, $customerPhone,
        $totalSeats, $totalAmount
    ]);
    $bookingId = (int) $pdo->lastInsertId();

    $insertSeat = $pdo->prepare('INSERT INTO booking_seats (booking_id, seat_id, price) VALUES (?, ?, ?)');
    $updateSeat = $pdo->prepare('UPDATE seats SET status = "booked" WHERE id = ?');
    foreach ($seatIds as $seatId) {
        $insertSeat->execute([$bookingId, $seatId, $ticketPrice]);
        $updateSeat->execute([$seatId]);
    }

    $pdo->commit();
    json_out(['success' => true, 'booking_code' => $bookingCode]);
} catch (Throwable $e) {
    $pdo->rollBack();
    json_out(['success' => false, 'message' => 'Something went wrong while confirming your booking. Please try again.'], 500);
}
