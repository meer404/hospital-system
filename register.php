<?php
include 'db_connect.php';
$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name']; $email = $_POST['email']; $password = $_POST['password'];
    $phone = $_POST['phone']; $gender = $_POST['gender']; $date_of_birth = $_POST['date_of_birth'];
    $role = $_POST['role']; $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, phone, gender, date_of_birth, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $full_name, $email, $password_hash, $phone, $gender, $date_of_birth, $role);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        if ($role == 'patient') $conn->query("INSERT INTO patients (user_id) VALUES ($user_id)");
        elseif ($role == 'doctor') $conn->query("INSERT INTO doctors (user_id) VALUES ($user_id)");
        
        $message = "Registration successful! <a href='index.php' class='alert-link'>Click here to login.</a>";
        $message_type = 'success';
    } else {
        $message = ($conn->errno == 1062) ? "Error: This email address is already registered." : "Error: " . $stmt->error;
        $message_type = 'danger';
    }
    $stmt->close(); $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Clinic System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-3">Create Account</h2>
                        <p class="card-subtitle text-muted text-center mb-4">Join as a doctor or patient.</p>

                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                        <?php endif; ?>
                        
                        <form action="register.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Full Name:</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password:</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number:</label>
                                    <input type="text" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth:</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender:</label>
                                    <select id="gender" name="gender" class="form-select" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="role" class="form-label">I am a:</label>
                                    <select id="role" name="role" class="form-select" required>
                                        <option value="">-- Select Role --</option>
                                        <option value="patient">Patient</option><option value="doctor">Doctor</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-3">Register</button>
                        </form>
                        <p class="text-center text-muted mt-4 mb-0">Already have an account? <a href="index.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>