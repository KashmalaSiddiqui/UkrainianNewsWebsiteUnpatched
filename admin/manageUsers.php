<?php
include('adminHeader.php');
require_once './includes/config/config.php';



if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    // Check for existing username or email
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $checkStmt->bind_param("ss", $username, $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $success_message = "Username or Email already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $username, $email, $password);

        if ($stmt->execute()) {
            $success_message = "User added successfully!";
        } else {
            $success_message = "Error: " . $stmt->error;
        }
    }
}

// Remove User
if (isset($_POST['remove_user'])) {
    $user_id = intval($_POST['user_id']);

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $success_message = "User removed successfully!";
}

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="./styles/main.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1 {
            font-size: 2rem;
            text-align: center;
            color: #333;
        }

        .form-section {
            margin-bottom: 30px;
            margin: 0 auto;
            align-items: center;
            text-align: center;
            padding-left: 50px;

        }

        .form-section input[type="text"],
        .form-section input[type="password"],
        .form-section input[type="email"] {
            width: 80%;
            padding: 10px;

            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-section button {
            padding: 10px 20px;
            background-color: #004080;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
            /* Center the button */
        }

        .form-section button:hover {
            background-color: #0056b3;
        }

        h2 {
            font-size: 1.5rem;
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .users-list {
            margin: 0 auto;
            text-align: center;
        }

        .users-list table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .users-list th,
        .users-list td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .users-list th {
            background-color: #004080;
            color: white;
        }

        .users-list button {
            padding: 5px 15px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;

            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .users-list button:hover {
            background-color: #c0392b;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Manage Users</h1>

        <!-- Display Success Message -->
        <?php if (isset($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <!-- Add User Form -->
        <div class="form-section">
            <h2>Add New User</h2>
            <form method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <div style="margin-bottom: 30px;">
                    <button type="submit" name="add_user">Add User</button>
                </div>
            </form>
        </div>

        <!-- Users List -->
        <div class="users-list">
            <h2>Users List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="remove_user">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>