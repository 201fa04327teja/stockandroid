<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

$sql = "SELECT id, title, content, author, category, image_url, created_at FROM blogs ORDER BY created_at DESC";
$result = $conn->query($sql);

$blogs = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $blogs]);
} else {
    echo json_encode(["status" => "success", "data" => []]);
}

$conn->close();
?>
