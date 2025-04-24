<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins
header("Content-Type: application/json");

include 'db.php'; // Include database connection

$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);

$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $products]);
} else {
    echo json_encode(["status" => "error", "message" => "No products found"]);
}

$conn->close();
?>
