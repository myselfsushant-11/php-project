<?php
require_once __DIR__ . '/../../includes/auth.php';
if (!is_admin_logged_in()) { json_out(['success' => false, 'message' => 'Admin login required.'], 401); }

$action = $_REQUEST['action'] ?? '';

switch ($action) {

    case 'list': {
        $halls = $pdo->query("SELECT * FROM halls ORDER BY name ASC")->fetchAll();
        json_out(['success' => true, 'halls' => $halls]);
    }

    case 'save': {
        $id       = (int) ($_POST['id'] ?? 0);
        $name     = clean($_POST['name'] ?? '');
        $rows     = (int) ($_POST['rows_count'] ?? 0);
        $cols     = (int) ($_POST['seats_per_row'] ?? 0);

        if ($name === '' || $rows <= 0 || $cols <= 0) {
            json_out(['success' => false, 'message' => 'Please fill all fields correctly.']);
        }
        $total = $rows * $cols;

        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE halls SET name=?, rows_count=?, seats_per_row=?, total_seats=? WHERE id=?');
            $stmt->execute([$name, $rows, $cols, $total, $id]);
            json_out(['success' => true, 'message' => 'Hall updated.']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO halls (name, rows_count, seats_per_row, total_seats) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $rows, $cols, $total]);
            json_out(['success' => true, 'message' => 'Hall added.']);
        }
    }

    case 'delete': {
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) json_out(['success' => false, 'message' => 'Invalid hall.']);
        $stmt = $pdo->prepare('SELECT COUNT(*) c FROM showtimes WHERE hall_id = ?');
        $stmt->execute([$id]);
        if ((int) $stmt->fetch()['c'] > 0) {
            json_out(['success' => false, 'message' => 'Cannot delete a hall that has showtimes scheduled.']);
        }
        $stmt = $pdo->prepare('DELETE FROM halls WHERE id = ?');
        $stmt->execute([$id]);
        json_out(['success' => true, 'message' => 'Hall deleted.']);
    }

    default:
        json_out(['success' => false, 'message' => 'Unknown action.'], 400);
}
