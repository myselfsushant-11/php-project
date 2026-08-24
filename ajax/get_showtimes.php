<?php
require_once __DIR__ . '/../includes/auth.php';

$movieId = (int) ($_GET['movie_id'] ?? 0);
$date    = $_GET['date'] ?? date('Y-m-d');

if (!$movieId || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    json_out(['success' => false, 'message' => 'Invalid request.']);
}

$stmt = $pdo->prepare(
    "SELECT s.id, s.show_time, s.ticket_price, h.id AS hall_id, h.name AS hall_name
     FROM showtimes s
     JOIN halls h ON h.id = s.hall_id
     WHERE s.movie_id = ? AND s.show_date = ?
     ORDER BY h.name ASC, s.show_time ASC"
);
$stmt->execute([$movieId, $date]);
$rows = $stmt->fetchAll();

$grouped = [];
foreach ($rows as $r) {
    $hallName = $r['hall_name'];
    if (!isset($grouped[$hallName])) {
        $grouped[$hallName] = [];
    }
    $grouped[$hallName][] = [
        'id'    => $r['id'],
        'time'  => format_time12($r['show_time']),
        'price' => $r['ticket_price'],
    ];
}

json_out(['success' => true, 'halls' => $grouped]);
