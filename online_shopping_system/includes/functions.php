<?php
// Start the session once (needed for login).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Safely print text on a page (prevents XSS).
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Format money.
function money($value): string {
    return 'TSh ' . number_format((float)$value, 2);
}

// Go to another page.
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}
