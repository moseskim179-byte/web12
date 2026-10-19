<?php
require __DIR__ . '/includes/functions.php';

$_SESSION = [];          // clear session data
session_destroy();       // end the session
redirect('login.php');
