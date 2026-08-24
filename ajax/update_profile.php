<?php
require_once __DIR__ . '/../includes/auth.php';
require_login_json();

$name  = clean($_POST['name'] ?? '');
$phone = clean($_POST['phone'] ?? '');

if ($name === '' || $phone === '') {
    json_out(['success' => false, 'message' => 'Name and phone are required.']);
}
if (!preg_match('/^[0-9+\-\s]{7,15}$/', $phone)) {
    json_out(['success' => false, 'message' => 'Please enter a valid phone number.']);
}

$imagePath = null;
if (!empty($_FILES['profile_image']['name'])) {
    $imagePath = upload_image($_FILES['profile_image'], __DIR__ . '/../assets/images/uploads', 'user' . $_SESSION['user_id']);
    if (!$imagePath) {
        json_out(['success' => false, 'message' => 'Invalid image. Use jpg, jpeg, png or webp under 5MB.']);
    }
    $imagePath = 'assets/images/uploads/' . basename($imagePath);
}

if ($imagePath) {
    $stmt = $pdo->prepare('UPDATE users SET name = ?, phone = ?, profile_image = ? WHERE id = ?');
    $stmt->execute([$name, $phone, $imagePath, $_SESSION['user_id']]);
} else {
    $stmt = $pdo->prepare('UPDATE users SET name = ?, phone = ? WHERE id = ?');
    $stmt->execute([$name, $phone, $_SESSION['user_id']]);
}

$_SESSION['user_name'] = $name;

json_out(['success' => true, 'message' => 'Profile updated successfully.']);
