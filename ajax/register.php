<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$name     = clean($_POST['name'] ?? '');
$email    = strtolower(clean($_POST['email'] ?? ''));
$phone    = clean($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if ($name === '' || $email === '' || $phone === '' || $password === '') {
    json_out(['success' => false, 'message' => 'All fields are required.']);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_out(['success' => false, 'message' => 'Please enter a valid email address.']);
}
if (!preg_match('/^[0-9+\-\s]{7,15}$/', $phone)) {
    json_out(['success' => false, 'message' => 'Please enter a valid phone number.']);
}
if (strlen($password) < 6) {
    json_out(['success' => false, 'message' => 'Password must be at least 6 characters.']);
}
if ($password !== $confirm) {
    json_out(['success' => false, 'message' => 'Passwords do not match.']);
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    json_out(['success' => false, 'message' => 'An account with this email already exists.']);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)');
$stmt->execute([$name, $email, $phone, $hash]);

json_out(['success' => true, 'message' => 'Registration successful! Redirecting to login…']);
