<?php
//
// Include database connection
include('./includes/config/config.php');
include('./includes/header.php');

// Get the news ID from the query parameter
$id = $_GET['id'] ?? null;
// $commentsFile = __DIR__ . "../admin/uploads/comments.txt";

if (!$id) {
    die("Invalid news ID.");
}

// Fetch the news article
$stmt = $conn->prepare("SELECT title, content, url_to_image, source_name, published_at FROM news WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();

if (!$news) {
    die("News article not found.");
}

// Fetch all comments for the news article
$stmt = $conn->prepare("SELECT comment FROM comments WHERE news_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$comments = $stmt->get_result();

// Fetch ads
$ads = $conn->query("SELECT title, link FROM ads LIMIT 4");

/*Vulnerable: DOM-based XSS
In this the comment is being directly outputted to the page without any sanitization. 
This can be exploited by an attacker to execute arbitrary JavaScript code in the context of the user's browser. 
To fix this, we need to escape the comment content before outputting it to the page.

*/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($news['title']); ?></title>
    <link rel="stylesheet" href="./styles/main.css">
</head>

<body>
    <!-- Back Arrow -->
    <?php if (isset($_SERVER['HTTP_REFERER'])): ?>
        <a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>" class="back-arrow-news">&larr; Back</a>
    <?php endif; ?>
    <div class="complete-container-news ">
        <div class="sidebar left-sidebar">
            <?php while ($ad = $ads->fetch_assoc()): ?>
                <div class="ad">
                    <a href="<?php echo htmlspecialchars($ad['link']); ?>" target="_blank">
                        <img src="<?php echo htmlspecialchars($ad['link']); ?>" alt="<?php echo htmlspecialchars($ad['title']); ?>" class="ad-image">
                    </a>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- News Article Container -->
        <div class=" page-container"> <!-- style="max-width: 700px; margin: 0 auto; background-color: #fff; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);border-radius: 5px;"> -->
            <!-- News Title -->
            <h2 style=" font-size: 2rem; color: #222;"><?php echo htmlspecialchars($news['title']); ?></h2>
            <!-- News Source and Publish Date -->
            <small style="color: gray; display: block; margin-bottom: 20px;">
                Source: <?php echo htmlspecialchars($news['source_name'] ?? 'Unknown Source'); ?> |
                Published At: <?php echo $news['published_at']; ?>
            </small>
            <!-- News Image (only if exists) -->
            <?php if (!empty($news['url_to_image'])): ?>
                <img src="<?php echo htmlspecialchars($news['url_to_image']); ?>" alt="News Image"
                    style="width: 100%; height: auto; margin-bottom: 20px; border-radius: 5px;">
            <?php endif; ?>
            <!-- News Content -->
            <p style="line-height: 1.8; color: #444; font-size: 1.1rem;">
                <?php echo nl2br(htmlspecialchars($news['content'] ?? 'Content not available.')); ?>
            </p>
            <!-- Approved Comments Section with DOM-Based Vulnerability -->
            <div class="approved-comments">
                <h3>Approved Comments</h3>
                <ul class="comments-list">
                    <?php while ($comment = $comments->fetch_assoc()): ?>
                        <li data-comment="<?php echo $comment['comment']; ?>"><?php echo $comment['comment']; ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <script>
                // JavaScript fetching the user comment from the data-attribute
                document.querySelectorAll('.comments-list li').forEach(function(li) {
                    // DOM-based vulnerability: Rendering innerHTML from unescaped data
                    let userComment = li.getAttribute('data-comment');
                    li.innerHTML = userComment; // Unsafe usage
                });
            </script>
            <!-- Comment Box -->
            <div class="comment-section" style="margin-top: 40px;">
                <h3 style="margin-bottom: 10px;">Leave a Comment</h3>
                <form action="../scripts/submitComment.php" method="POST">
                    <textarea name="comment" rows="5" placeholder="Write your comment here..."
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
                    <input type="hidden" name="news_id" value="<?php echo $id; ?>">
                    <button type="submit" style="margin-top: 10px; padding: 10px 20px; background-color: #004080; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Submit Comment
                    </button>
                </form>
            </div>
        </div>
        <!-- Right Sidebar (Ads) -->
        <div class="sidebar right-sidebar">
            <?php
            $ads->data_seek(0); // Reset the pointer to the beginning of the result set
            while ($ad = $ads->fetch_assoc()): ?>
                <div class="ad">
                    <a href="<?php echo htmlspecialchars($ad['link']); ?>" target="_blank">
                        <img src="<?php echo htmlspecialchars($ad['link']); ?>" alt="<?php echo htmlspecialchars($ad['title']); ?>" class="ad-image">
                    </a>
                </div>
            <?php endwhile; ?>
        </div>

    </div>
    <!-- Footer -->
    <footer style="background-color: #004080; color: white; text-align: center; padding: 10px 0; position: relative; bottom: 0; width: 100%;">
        <p>&copy; <?php echo date("Y"); ?> Kyiv News</p>
    </footer>
</body>

</html>