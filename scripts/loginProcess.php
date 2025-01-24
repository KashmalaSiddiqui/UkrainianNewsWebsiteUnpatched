<?php


include_once '../includes/sessionManagement.php';
include('../includes/config/config.php');
ob_start(); // Start output buffering




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    /* Vulnerability: CWE-692 Incomplete Denylist for XSS Prevention
How is it implemented? When a user submits a form, the input is not sanitized or validated.
so if the user inputs a script tag, or a javascript: link, it will be stored in the database, and when the admin 
tries to view the user's profile, the script will be executed
How we can fix this vulnerability is by using a complete denylist,  which is a list of all the possible XSS attacks that can be done,
*/

    // Incomplete Denylist for XSS Prevention (VULNERABLE)
    $denylist = ['<script>', '</script>']; // Incomplete list
    foreach ($denylist as $badInput) {
        $username = str_replace($badInput, '', $username);
        $password = str_replace($badInput, '', $password);
    }

    echo "Welcome, " . $username;

    // Database Query
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        header("Location: /main.php"); // Redirect to index.php
        exit();
    } else {
        echo "Invalid password.";
    }
    // }
    $stmt->close();
    $conn->close();
}

ob_end_flush(); // Flush the output buffer
