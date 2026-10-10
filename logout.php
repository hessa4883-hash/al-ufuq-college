<?php
require_once __DIR__ . '/config/db.php';

session_unset();
session_destroy();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

set_flash('success', 'تم تسجيل خروجك بنجاح. نتمنى لك يوماً سعيداً!');
header('Location: index.php');
exit;
