<?php
// Put this at the top of every page that must be protected.
// The page must set $base BEFORE including this file ('' for root, '../' for folders).
require_once __DIR__ . '/functions.php';

if (empty($_SESSION['user_id'])) {
    redirect(($base ?? '') . 'login.php');
}
