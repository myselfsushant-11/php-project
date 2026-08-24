<?php
require_once __DIR__ . '/../includes/auth.php';
session_unset();
session_destroy();
json_out(['success' => true, 'message' => 'Logged out successfully.']);
