<?php
session_start();
// echo "Session started";

// Set timeout in seconds (30 minutes)
$timeout = 1800;

// Set a fixed expiration time for the session when it's first created
if (!isset($_SESSION['CREATION_TIME'])) {
    $_SESSION['CREATION_TIME'] = time(); // Set creation time
}

// Check if the session has exceeded its fixed expiration time
if (time() - $_SESSION['CREATION_TIME'] > $timeout) {
    // echo "Session timeout";
    // Destroy the session if it has expired
    session_unset();
    session_destroy();
    header("Location: /index.php?timeout=true");
    exit();
}
