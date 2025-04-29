<?php
$host = "sql.freedb.tech";
$user = "freedb_rental";
$pass = "tSY&Fb&63D9j9!N";
$dbname = "freedb_rental";
$port = 3306; // Specify the port

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
  //  echo "Database connected successfully!";
}
?>
