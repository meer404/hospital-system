<?php
// login.php 
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, full_name, password_hash, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct!
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role']; // [cite: 15]

            // Redirect to the main dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid password
            $_SESSION['login_error'] = "Invalid email or password.";
            header("Location: index.php");
            exit();
        }
    } else {
        // No user found
        $_SESSION['login_error'] = "Invalid email or password.";
        header("Location: index.php");
        exit();
    }
    $stmt->close();
} else {
    // Not a POST request
    header("Location: index.php");
    exit();
}
$conn->close();
?>