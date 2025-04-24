<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($data)) {
    $name = isset($data['name']) ? $data['name'] : '';
    $description = isset($data['description']) ? $data['description'] : '';
    $rent = isset($data['rent']) ? (float)$data['rent'] : 0;
    $category = isset($data['category']) ? $data['category'] : '';
    $brand = isset($data['brand']) ? $data['brand'] : '';
    $condition = isset($data['condition']) ? $data['condition'] : 'Used';
    $image_url = isset($data['image_url']) ? $data['image_url'] : '';

    if (empty($name) || empty($rent) || empty($category) || empty($brand)) {
        echo json_encode(["status" => "error", "message" => "All required fields must be filled"]);
        exit();
    }

    // Insert query
    $sql = "INSERT INTO products (name, description, rent, category, brand, `condition`, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssdssss", $name, $description, $rent, $category, $brand, $condition, $image_url);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Product added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add product"]);
    }

    $stmt->close();
}

$conn->close();
?>
