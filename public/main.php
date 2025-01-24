<?php

// Include database connection
include('./includes/header.php');
include('./includes/config/config.php');
include('./includes/footer.php');
include('./scripts/fetchnews.php'); // Dynamically fetch news and save to the database



/* VULNERABILITY 1- Reflected XSS- vulnerability: in this vulnerability, the user input is directly displayed back 
on the page without proper sanitization.
Reflected XSS- vulnerability: In the below code the searchQuery directly gets the user input 
and displays it back on the page/ inserts it in the HTML without proper sanitization. 
We implemnetd it in two places in the code. where we give users an option to signup to the newsletter and
we are reflecting the user input back to the page without proper sanitization.
*/
$searchQuery = "";

$output = "";

$news = []; // Initialize as an empty array
if (isset($_GET['query'])) {
    $searchQuery = $_GET['query']; // User input directly from query string
    $output = "<div id=\"results\" title=\"$searchQuery\">Search Results for: <b>$searchQuery</b></div>";
    // Filter news results based on the search query
    $stmt = $conn->prepare("SELECT id, title, content, url_to_image, published_at FROM news WHERE title LIKE ? OR content LIKE ? ORDER BY published_at DESC");
    $searchTerm = "%" . $searchQuery . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $news[] = $row;
        }
    }
} else {
    // Fetch all news from the database
    include('./scripts/fetchnews.php'); // Dynamically fetch news and save to the database

    // Fetch all news from the database
    $result = $conn->query("SELECT id, title, content, url_to_image, published_at FROM news ORDER BY published_at DESC");


    $news = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $news[] = $row;
        }
    }
}
// Fetch and Save News from API

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyiv News</title>
    <link rel="stylesheet" href="./styles/main.css">
    <style>
        /* Basic layout */
        .container {
            display: flex;
            flex-direction: row;
            margin: 20px;
            gap: 20px;
            width: calc(100% - 40px);
        }

        .main-content {
            flex: 4;
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .side-panel {
            flex: 1;
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .main-news img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .main-news-heading a {
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            color: #000;
        }

        .main-news-heading a:hover {
            color: #007bff;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .news-box {
            border: 1px solid #ddd;
            padding: 10px;
            background: #f9f9f9;
            text-align: center;
        }

        .news-box img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .news-box h3 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .news-box a {
            text-decoration: none;
            color: #000;
        }

        .news-box a:hover {
            color: #007bff;
        }

        .signup-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .signup-box h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .signup-box p {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        .signup-box input[type="text"] {
            width: 90%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .signup-box button {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .signup-box button:hover {
            background-color: #333;
        }


    </style>
</head>

<body class="main-page">

    <div class="container">
        <?php if (isset($output)) echo $output; ?>
        <!-- Main Content Area -->
        <div class="main-content">
            <?php if (!empty($news[0])): ?>
                <div class="main-news">
                    <img src="<?php echo $news[0]['url_to_image'] ?? 'placeholder.jpg'; ?>" alt="Main News Image">
                    <h2 class="main-news-heading">
                        <a href="news.php?id=<?php echo $news[0]['id']; ?>">
                            <?php echo $news[0]['title']; ?>
                        </a>
                    </h2>
                    <small>Published At: <?php echo $news[0]['published_at']; ?></small>
                </div>
            <?php else: ?>
                <p>No main news available.</p>
            <?php endif; ?>

            <!-- Grid for Remaining News -->
            <div class="news-grid">
                <?php for ($i = 1; $i < count($news); $i++): ?>
                    <div class="news-box">
                        <img src="<?php echo $news[$i]['url_to_image'] ?? 'placeholder.jpg'; ?>" alt="News Thumbnail">
                        <h3>
                            <a href="news.php?id=<?php echo $news[$i]['id']; ?>">
                                <?php echo $news[$i]['title']; ?>
                            </a>
                        </h3>
                        <small>Published At: <?php echo $news[$i]['published_at']; ?></small>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="side-panel">
            <!-- Must Read Section -->
            <div class="must-read">
                <h3>Must Read</h3>
                <?php for ($i = 4; $i < 7 && $i < count($news); $i++): ?>
                    <div class="side-news-item">
                        <img src="<?php echo $news[$i]['url_to_image'] ?? 'placeholder.jpg'; ?>" alt="Thumbnail">
                        <a href="news.php?id=<?php echo $news[$i]['id']; ?>"><?php echo $news[$i]['title']; ?></a>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- More Headlines Section -->
            <div class="more-headlines">
                <h3>More Headlines</h3>
                <?php for ($i = 7; $i < 10 && $i < count($news); $i++): ?>
                    <div class="side-news-item">
                        <img src="<?php echo $news[$i]['url_to_image'] ?? 'placeholder.jpg'; ?>" alt="Thumbnail">
                        <a href="news.php?id=<?php echo $news[$i]['id']; ?>"><?php echo $news[$i]['title']; ?></a>
                    </div>
                <?php endfor; ?>
            </div>
            <div class="signup-box">
                <h3>Sign up for our Newsletter</h3>
                <p>The latest news from around the world. Timely. Accurate. Fair.</p>
                <form action="" method="GET">
                    <input type="text" name="email" placeholder="E-mail address" required>
                    <button type="submit" name="subscribe">Subscribe</button>
                </form>

                <?php
                // Reflect user input without sanitization to demonstrate Reflected XSS
                if (isset($_GET['subscribe']) && !empty($_GET['email'])) {
                    $email = $_GET['email']; // Unsanitized input
                    echo "<p title='$email'>Thank you for subscribing with: <b>$email</b></p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>