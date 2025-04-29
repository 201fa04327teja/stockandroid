<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST" && $data) {
    $title = $data['title'] ?? '';
    $content = $data['content'] ?? '';
    $author = $data['author'] ?? '';
    $category = $data['category'] ?? '';
    $image_url = $data['image_url'] ?? '';

    if (empty($title) || empty($content) || empty($author) || empty($image_url)) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO blogs (title, content, author, category, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $content, $author, $category, $image_url);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Blog posted"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
