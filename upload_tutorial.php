<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// ✅ Include the database connection
include 'db.php'; // db.php should define $conn

// Handle preflight request
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

$title = isset($data['title']) ? $conn->real_escape_string($data['title']) : '';
$video_link = isset($data['video_link']) ? $conn->real_escape_string($data['video_link']) : '';
$description = isset($data['description']) ? $conn->real_escape_string($data['description']) : '';
$category = isset($data['category']) ? $conn->real_escape_string($data['category']) : '';

// Check required fields
if (empty($title) || empty($video_link) || empty($description)) {
    echo json_encode(["status" => "error", "message" => "Title, video link, and description are required"]);
    exit();
}

// Insert into database
$sql = "INSERT INTO tutorials (title, video_link, description, category) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $title, $video_link, $description, $category);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Tutorial uploaded successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to upload tutorial"]);
}

$stmt->close();
$conn->close();
?>
