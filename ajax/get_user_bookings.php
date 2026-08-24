<?php
require_once __DIR__ . '/../includes/auth.php';
require_login_json();

$type = $_GET['type'] ?? 'upcoming'; // upcoming | past

$op = $type === 'past' ? '<' : '>=';

$stmt = $pdo->prepare(
    "SELECT b.booking_code, b.total_seats, b.total_amount, b.status,
            s.show_date, s.show_time, m.title AS movie_title, m.poster, h.name AS hall_name
     FROM bookings b
     JOIN showtimes s ON s.id = b.showtime_id
     JOIN movies m ON m.id = s.movie_id
     JOIN halls h ON h.id = s.hall_id
     WHERE b.user_id = ? AND CONCAT(s.show_date, ' ', s.show_time) $op NOW()
     ORDER BY s.show_date " . ($type === 'past' ? 'DESC' : 'ASC') . ", s.show_time ASC"
);
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll();

$bookings = array_map(function ($r) {
    return [
        'booking_code' => $r['booking_code'],
        'movie_title'  => $r['movie_title'],
        'poster'       => $r['poster'],
        'hall_name'    => $r['hall_name'],
        'date'         => format_date_pretty($r['show_date']),
        'time'         => format_time12($r['show_time']),
        'seats'        => (int) $r['total_seats'],
        'amount'       => format_rupees($r['total_amount']),
        'status'       => $r['status'],
    ];
}, $rows);

json_out(['success' => true, 'bookings' => $bookings]);
