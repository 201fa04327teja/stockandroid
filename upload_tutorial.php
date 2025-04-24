<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "your_database";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $video_link = $conn->real_escape_string($_POST['video_link']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);

    $sql = "INSERT INTO tutorials (title, video_link, description, category)
            VALUES ('$title', '$video_link', '$description', '$category')";

    if ($conn->query($sql) === TRUE) {
        echo "Tutorial uploaded successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!-- HTML Form -->
<form method="post" action="">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Video Link (e.g., YouTube embed):</label><br>
    <input type="text" name="video_link" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Category:</label><br>
    <input type="text" name="category"><br><br>

    <input type="submit" value="Upload Tutorial">
</form>
