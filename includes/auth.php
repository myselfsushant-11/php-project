<?php
/**
 * CINEFILE — Session bootstrap
 * Include this at the very top of every page (before any HTML output).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
