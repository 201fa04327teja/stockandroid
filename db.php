<?php
$host = "sql.freedb.tech";
$user = "freedb namratha sree";
$pass = "&vR8Fjb3rg72!dw";
$dbname = "freedb_stock android app";
$port = 3306; // Specify the port

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
  //  echo "Database connected successfully!";
}
?>
