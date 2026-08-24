<?php
require_once __DIR__ . '/../includes/auth.php';

$showtimeId = (int) ($_GET['showtime_id'] ?? 0);
if (!$showtimeId) {
    json_out(['success' => false, 'message' => 'Invalid showtime.']);
}

$stmt = $pdo->prepare(
    'SELECT id, seat_row, seat_number, seat_label, status
     FROM seats WHERE showtime_id = ?
     ORDER BY seat_row ASC, seat_number ASC'
);
$stmt->execute([$showtimeId]);
$seats = $stmt->fetchAll();

json_out(['success' => true, 'seats' => $seats]);
