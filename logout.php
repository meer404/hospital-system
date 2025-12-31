<?php
// logout.php
session_start(); // Find the existing session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to login page
header("Location: index.php");
exit();
?>