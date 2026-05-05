<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang_code = $_SESSION['lang'] ?? 'en';

if (!in_array($lang_code, ['en', 'ar'])) {
    $lang_code = 'en';
}

include __DIR__ . "/lang/$lang_code.php";
