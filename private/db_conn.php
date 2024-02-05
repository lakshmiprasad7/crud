<?php
$serverName = "localhost";
$username = "root";
$password = "";
$dbname = "pd_data";

// Create connection
$conn = mysqli_connect($serverName, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connected successfully";
}
?>
