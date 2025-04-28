<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $query = "SELECT * FROM tutorials ORDER BY id DESC"; // latest first (optional)
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $tutorials = [];
        while ($row = $result->fetch_assoc()) {
            $tutorials[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $tutorials]);
    } else {
        echo json_encode(["status" => "error", "message" => "No tutorials found"]);
    }

    $conn->close();
}
?>
