<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST" && $data) {
    $title = $data['title'] ?? '';
    $video_link = $data['video_link'] ?? '';
    $description = $data['description'] ?? '';
    $category = $data['category'] ?? '';

    if (empty($title) || empty($video_link) || empty($description)) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO tutorials (title, video_link, description, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $video_link, $description, $category);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Tutorial uploaded"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
