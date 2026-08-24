<?php
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$email    = strtolower(clean($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ?');
$stmt->execute([$email]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($password, $admin['password'])) {
    json_out(['success' => false, 'message' => 'Invalid admin credentials.']);
}

session_regenerate_id(true);
$_SESSION['admin_id']   = $admin['id'];
$_SESSION['admin_name'] = $admin['name'];

json_out(['success' => true, 'message' => 'Welcome, ' . $admin['name'] . '!']);
