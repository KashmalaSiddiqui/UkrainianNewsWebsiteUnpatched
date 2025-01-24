<?php
if (session_status() === PHP_SESSION_NONE) {
    include('./includes/sessionmanagement.php');
}
$uploadedFilePath = isset($_SESSION['uploaded_image']) ? $_SESSION['uploaded_image'] : '';

// Display previously uploaded file
if (isset($_SESSION['uploaded_image'])) {
    $uploadedFilePath = $_SESSION['uploaded_image'];
}
?>
<header class="admin-header">
    <div class="header-container">
        <h1>
            <a href="adminDashboard.php" style="text-decoration: none; color: inherit;"> Admin Dashboard</a>
        </h1>
        <!-- Navigation -->
        <nav>
            <a href="manageUsers.php">Manage Users</a>
            <a href="manageComments.php">Manage Comments</a>
            <a href="manageAd.php">Manage Advertisements</a>
        </nav>
        <!-- Uploaded Image or Malicious File (Top-Right Corner) -->
        <?php if (!empty($uploadedFilePath)): ?>
            <?php
            $fileExtension = pathinfo($uploadedFilePath, PATHINFO_EXTENSION);
            if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                <img src="<?php echo htmlspecialchars($uploadedFilePath); ?>" alt="Uploaded Image" class="uploaded-image">
            <?php else: ?>
                <p class="non-image-warning">Non-image file uploaded</p>
            <?php endif; ?>
        <?php endif; ?>
        <button class="logout-button" onclick="location.href='adminlogout.php'">Logout</button>

    </div>
</header>