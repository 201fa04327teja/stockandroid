<?php
// Start output buffering to prevent unwanted output
ob_start();

// Set CORS and content-type headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Include database connection
include 'db.php';

// Handle preflight request for CORS
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    ob_end_clean();
    exit();
}

// Get raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Initialize response array
$response = ["status" => "error", "message" => "Invalid request"];

// Check if request is POST and data is valid
if ($_SERVER["REQUEST_METHOD"] == "POST" && is_array($data)) {
    $name = isset($data['name']) ? trim($data['name']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $phone = isset($data['phone']) ? trim($data['phone']) : '';
    $password = isset($data['password']) ? $data['password'] : '';

    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $response = ["status" => "error", "message" => "All fields are required"];
        http_response_code(400);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = ["status" => "error", "message" => "Invalid email format"];
        http_response_code(400);
    } else {
        try {
            // Check if email already exists
            $checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $checkQuery->bind_param("s", $email);
            $checkQuery->execute();
            $checkQuery->store_result();

            if ($checkQuery->num_rows > 0) {
                $response = ["status" => "error", "message" => "Email already registered"];
                http_response_code(409);
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);

                if ($stmt->execute()) {
                    $response = ["status" => "success", "message" => "User registered successfully"];
                    http_response_code(201);
                } else {
                    $response = ["status" => "error", "message" => "Failed to register user"];
                    http_response_code(500);
                }
                $stmt->close();
            }
            $checkQuery->close();
        } catch (Exception $e) {
            // Log error to file instead of outputting
            error_log("Signup error: " . $e->getMessage());
            $response = ["status" => "error", "message" => "Server error occurred"];
            http_response_code(500);
        }
    }
} else {
    $response = ["status" => "error", "message" => "Invalid request method or data"];
    http_response_code(400);
}

// Close database connection
$conn->close();

// Clear output buffer and send JSON response
ob_end_clean();
echo json_encode($response);
?>
