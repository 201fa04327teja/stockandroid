<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow only POST and OPTIONS
header("Access-Control-Allow-Headers: Content-Type"); // Allow Content-Type header
header("Content-Type: application/json"); // Set response format to JSON

include 'db.php'; // Include database connection

// Handle preflight request for CORS
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

// Get raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($data)) {
    $name = isset($data['name']) ? $data['name'] : '';
    $email = isset($data['email']) ? $data['email'] : '';
    $phone = isset($data['phone']) ? $data['phone'] : '';
    $password = isset($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : '';

    // Check for missing fields
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit();
    }

    // Check if email already exists
    $checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkQuery->bind_param("s", $email);
    $checkQuery->execute();
    $checkQuery->store_result();

    if ($checkQuery->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $password);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "User registered successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Signup failed"]);
        }
        $stmt->close();
    }

    $checkQuery->close();
}

$conn->close();
?>
