<?php
include('../includes/config/config.php');


// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $news_id = $_POST['news_id'] ?? null;
    $comment = $_POST['comment'] ?? null;

    // Validate input
    if (empty($news_id) || empty($comment)) {
        die("News ID or comment cannot be empty.");
    }

    // Save the comment directly
    $stmt = $conn->prepare("INSERT INTO comments (news_id, comment) VALUES (?, ?)");
    $stmt->bind_param("is", $news_id, $comment);


    if ($stmt->execute()) {
        // Redirect to the news page
        header("Location: /news.php?id=$news_id");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
