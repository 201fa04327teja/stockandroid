<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins
header("Content-Type: application/json");

include 'db.php'; // Include database connection

$sql = "SELECT * FROM properties ORDER BY created_at DESC";
$result = $conn->query($sql);

$properties = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $properties]);
} else {
    echo json_encode(["status" => "error", "message" => "No properties found"]);
}

$conn->close();
?>
