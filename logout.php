<?php
// logout.php - Session Termination
require_once __DIR__ . '/includes/auth.php';

session_unset();
session_destroy();

session_start();
set_flash_message('success', 'You have been logged out successfully.');
header('Location: /login.php');
exit;
