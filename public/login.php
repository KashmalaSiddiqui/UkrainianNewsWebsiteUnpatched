<?php
// include_once('./includes/config/sessionManagement.php');
include('./includes/sessionmanagement.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="./styles/main.css">
</head>

<body class="login-page">
    <!-- Back Arrow -->
    <?php if (isset($_SERVER['HTTP_REFERER'])): ?>
        <a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>" class="back-arrow" style="text-decoration: none;">&larr; Back</a>
    <?php endif; ?>
    <div class="container">
        <h2>
            <a href="main.php" style="text-decoration: none; color: inherit;"> User Login</a>
        </h2>
        <form action="./scripts/loginProcess.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>

</html>