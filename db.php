<?php
$host = "sql.freedb.tech";
$user = "freedb_namratha";
$pass = '&aUpT8W67qTKUHE';  // ✅ Using single quotes to avoid issues with special characters
$dbname = "freedb_stockApp";
$port = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
  //  echo "Database connected successfully!";
}
?>
