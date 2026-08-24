<?php
require_once __DIR__ . '/../../includes/auth.php';
if (!is_admin_logged_in()) { json_out(['success' => false, 'message' => 'Admin login required.'], 401); }

$q = trim($_GET['q'] ?? '');

$sql = "SELECT u.id, u.name, u.email, u.phone, u.created_at,
               (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.id AND b.status='confirmed') AS total_bookings
        FROM users u";
$params = [];
if ($q !== '') {
    $sql .= " WHERE u.name LIKE ? OR u.email LIKE ?";
    $params = ["%$q%", "%$q%"];
}
$sql .= " ORDER BY u.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Never expose password hashes to the admin UI.
json_out(['success' => true, 'users' => $users]);
