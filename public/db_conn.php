<?php
$serverName = "localhost";
$username = "root";
$password = "";
$dbname = "your_database";

// Create connection
$conn = mysqli_connect($serverName, $username, $password, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
?>