<?php
require_once __DIR__ . '/../../includes/auth.php';
if (!is_admin_logged_in()) { json_out(['success' => false, 'message' => 'Admin login required.'], 401); }

$action = $_REQUEST['action'] ?? '';

switch ($action) {

    case 'list': {
        $stmt = $pdo->query(
            "SELECT s.*, m.title AS movie_title, h.name AS hall_name,
                    (SELECT COUNT(*) FROM seats st WHERE st.showtime_id = s.id) AS total_seats,
                    (SELECT COUNT(*) FROM seats st WHERE st.showtime_id = s.id AND st.status='booked') AS booked_seats
             FROM showtimes s
             JOIN movies m ON m.id = s.movie_id
             JOIN halls h ON h.id = s.hall_id
             ORDER BY s.show_date DESC, s.show_time DESC"
        );
        json_out(['success' => true, 'showtimes' => $stmt->fetchAll()]);
    }

    case 'options': {
        $movies = $pdo->query("SELECT id, title FROM movies WHERE status='now_showing' ORDER BY title")->fetchAll();
        $halls  = $pdo->query("SELECT id, name FROM halls ORDER BY name")->fetchAll();
        json_out(['success' => true, 'movies' => $movies, 'halls' => $halls]);
    }

    case 'save': {
        $id        = (int) ($_POST['id'] ?? 0);
        $movieId   = (int) ($_POST['movie_id'] ?? 0);
        $hallId    = (int) ($_POST['hall_id'] ?? 0);
        $showDate  = $_POST['show_date'] ?? '';
        $showTime  = $_POST['show_time'] ?? '';
        $price     = (float) ($_POST['ticket_price'] ?? 0);

        if (!$movieId || !$hallId || !$showDate || !$showTime || $price <= 0) {
            json_out(['success' => false, 'message' => 'Please fill in all fields correctly.']);
        }

        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE showtimes SET movie_id=?, hall_id=?, show_date=?, show_time=?, ticket_price=? WHERE id=?');
            $stmt->execute([$movieId, $hallId, $showDate, $showTime, $price, $id]);
            json_out(['success' => true, 'message' => 'Showtime updated.']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO showtimes (movie_id, hall_id, show_date, show_time, ticket_price) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$movieId, $hallId, $showDate, $showTime, $price]);
            $newId = (int) $pdo->lastInsertId();
            generate_seats_for_showtime($pdo, $newId, $hallId);
            json_out(['success' => true, 'message' => 'Showtime created and seats generated.']);
        }
    }

    case 'delete': {
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) json_out(['success' => false, 'message' => 'Invalid showtime.']);
        $stmt = $pdo->prepare('SELECT COUNT(*) c FROM bookings WHERE showtime_id = ?');
        $stmt->execute([$id]);
        if ((int) $stmt->fetch()['c'] > 0) {
            json_out(['success' => false, 'message' => 'Cannot delete a showtime that already has bookings.']);
        }
        $stmt = $pdo->prepare('DELETE FROM showtimes WHERE id = ?');
        $stmt->execute([$id]);
        json_out(['success' => true, 'message' => 'Showtime deleted.']);
    }

    default:
        json_out(['success' => false, 'message' => 'Unknown action.'], 400);
}
