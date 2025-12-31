<?php
// db_connect.php 



// Start a session for all pages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/gimi');


$servername = "localhost";
$username = "root"; // Your MySQL username
$password = "";     // Your MySQL password
$dbname = "clinic_appointments"; // [cite: 11]

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>