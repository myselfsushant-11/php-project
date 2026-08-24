<?php
require_once __DIR__ . '/../includes/auth.php';
require_login_json();

$current = $_POST['current_password'] ?? '';
$new     = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($current === '' || $new === '' || $confirm === '') {
    json_out(['success' => false, 'message' => 'All fields are required.']);
}
if (strlen($new) < 6) {
    json_out(['success' => false, 'message' => 'New password must be at least 6 characters.']);
}
if ($new !== $confirm) {
    json_out(['success' => false, 'message' => 'New passwords do not match.']);
}

$stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || !password_verify($current, $user['password'])) {
    json_out(['success' => false, 'message' => 'Current password is incorrect.']);
}

$hash = password_hash($new, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
$stmt->execute([$hash, $_SESSION['user_id']]);

json_out(['success' => true, 'message' => 'Password changed successfully.']);
