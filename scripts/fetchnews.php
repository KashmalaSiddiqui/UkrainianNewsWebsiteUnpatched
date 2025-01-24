<?php
include('./includes/config/config.php');
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* 
Vulnerability 4: SSRF (Server-Side Request Forgery) first implementation
SSRF: In this vulnerability the server directly takes the input from the GET requets and directly uses it to fetch 
the data from the API.
what we are doing here is that, we are taking the input from the user and directly using it to fetch the data 
from the API.So if the search query is a url we are just directly fetching the data from the url provided by the user., howveer if it is specific words then 
*/
// News API setup
$searchQuery = $_GET['query'] ?? null; //SSRF 
$apiKey = "e9ce1e6f24774aa0b15e46c02be021b2";
if ($searchQuery) {
    if (filter_var($searchQuery, FILTER_VALIDATE_URL)) {
        $apiUrl = $searchQuery; // Directly fetch from the provided URL for SSRF testing
    } else {
        $apiUrl = "https://newsapi.org/v2/everything?q=$searchQuery&apiKey=$apiKey"; // Fetch news based on the search query from News API
    }
} else {
    $apiUrl = "https://newsapi.org/v2/top-headlines?q=ukrain&apiKey=$apiKey"; // Default: Fetch top headlines
}

//echo "Fetching URL: $apiUrl<br>";
// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return the response as a string
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification (optional)
curl_setopt($ch, CURLOPT_TIMEOUT, 40); // Set timeout (in seconds)

// Set User-Agent header
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "User-Agent: MyNewsApp/1.0",
]);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
    curl_close($ch);
    die("Failed to fetch news using cURL.");
}

curl_close($ch);
//Print the response
// echo "cURL Response:<br>";
// echo "<pre>" . htmlspecialchars($response) . "</pre>";


// Decode the JSON response
$newsData = json_decode($response, true);
if (isset($newsData['status'])) {
    if ($newsData['status'] === 'ok') {
        $articles = $newsData['articles'];

        // Prepare SQL query to insert news
        $stmt = $conn->prepare("INSERT INTO news (title, content, url, url_to_image, source_name, published_at)
                            VALUES (?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content)");


        foreach ($articles as $article) {
            $title = $article['title'] ?? 'No title';
            $content = $article['description'] ?? 'No description available.';
            $url = $article['url'] ?? '#';
            $urlToImage = $article['urlToImage'] ?? null;
            $sourceName = $article['source']['name'] ?? 'Unknown';
            $publishedAt = $article['publishedAt'] ?? date('Y-m-d H:i:s');

            // Fix publishedAt format to match MySQL DATETIME
            if ($publishedAt) {
                $publishedAt = str_replace("Z", "", $publishedAt); // Remove 'Z'
                $publishedAt = str_replace("T", " ", $publishedAt); // Replace 'T' with a space
            }

            // Skip articles with no image or empty image URL
            if (empty($urlToImage)) {
                continue;
            }

            // Skip articles where the title matches variations of "removed"
            if (stripos($title, 'removed') !== false) {
                continue;
            }
            // Bind parameters and execute
            $stmt->bind_param("ssssss", $title, $content, $url, $urlToImage, $sourceName, $publishedAt);


            if (!$stmt->execute()) {
            }
        }
    }
} else {
    // Debug: Print the actual 'status' value and message
    // echo "Status is: " . ($newsData['status'] ?? 'Undefined') . "<br>";
    // echo "Message: " . ($newsData['message'] ?? 'No message provided');
    echo "cURL Response:<br>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
