<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db.php'; // Include database connection

// Handle preflight request
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($data)) {
    $title = isset($data['title']) ? $data['title'] : '';
    $description = isset($data['description']) ? $data['description'] : '';
    $rent = isset($data['rent']) ? $data['rent'] : 0;
    $address = isset($data['address']) ? $data['address'] : '';
    $city = isset($data['city']) ? $data['city'] : '';
    $area = isset($data['area']) ? (float)$data['area'] : 0;
    $amenities = isset($data['amenities']) ? $data['amenities'] : '';
    $image_url = isset($data['image_url']) ? $data['image_url'] : '';

    if (empty($title) || empty($rent) || empty($address) || empty($city)) {
        echo json_encode(["status" => "error", "message" => "All required fields must be filled"]);
        exit();
    }

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO properties (title, description, rent, address, city, area, amenities, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssdss", $title, $description, $rent, $address, $city, $area, $amenities, $image_url);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Property added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add property"]);
    }

    $stmt->close();
}

$conn->close();
?>
