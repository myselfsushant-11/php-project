<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$email    = strtolower(clean($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    json_out(['success' => false, 'message' => 'Email and password are required.']);
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    json_out(['success' => false, 'message' => 'Invalid email or password.']);
}

session_regenerate_id(true);
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['name'];
$_SESSION['user_email'] = $user['email'];

$redirect = $_POST['redirect'] ?? 'index.php';
json_out(['success' => true, 'message' => 'Welcome back, ' . $user['name'] . '!', 'redirect' => $redirect]);
