<?php
/**
 * CINEFILE — Shared helper functions
 */

function json_out($data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function clean(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function require_login_json(): void
{
    if (!is_logged_in()) {
        json_out(['success' => false, 'message' => 'Please login to continue.', 'auth_required' => true], 401);
    }
}

function require_login_redirect(): void
{
    if (!is_logged_in()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function is_admin_logged_in(): bool
{
    return isset($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generate_booking_code(PDO $pdo): string
{
    do {
        $code = 'CF-' . date('Ymd') . '-' . str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare('SELECT id FROM bookings WHERE booking_code = ?');
        $stmt->execute([$code]);
    } while ($stmt->fetch());

    return $code;
}

function generate_seats_for_showtime(PDO $pdo, int $showtimeId, int $hallId): void
{
    $stmt = $pdo->prepare('SELECT rows_count, seats_per_row FROM halls WHERE id = ?');
    $stmt->execute([$hallId]);
    $hall = $stmt->fetch();
    if (!$hall) return;

    $insert = $pdo->prepare(
        'INSERT IGNORE INTO seats (showtime_id, seat_row, seat_number, seat_label, status)
         VALUES (?, ?, ?, ?, "available")'
    );

    for ($r = 0; $r < (int) $hall['rows_count']; $r++) {
        $rowLetter = chr(65 + $r);
        for ($c = 1; $c <= (int) $hall['seats_per_row']; $c++) {
            $insert->execute([$showtimeId, $rowLetter, $c, $rowLetter . $c]);
        }
    }
}

function upload_image(array $file, string $destDir, string $prefix = 'img'): ?string
{
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null;
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        return null;
    }
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $filename = $prefix . '_' . uniqid() . '_' . time() . '.' . $ext;
    $target = rtrim($destDir, '/') . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return null;
    }
    return $target;
}

function format_rupees($amount): string
{
    return 'Rs. ' . number_format((float) $amount, 0);
}

function format_time12($time): string
{
    return date('g:i A', strtotime($time));
}

function format_date_pretty($date): string
{
    return date('d M Y', strtotime($date));
}
