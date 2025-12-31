<?php
include '../db_connect.php';
include '../header.php';
$pageTitle = 'Patient Profile';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') die("Access Denied.");
$user_id = $_SESSION['user_id']; $message = ''; $message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name']; $phone = $_POST['phone']; $gender = $_POST['gender']; $date_of_birth = $_POST['date_of_birth'];
    $blood_group = $_POST['blood_group']; $address = $_POST['address']; $emergency_contact = $_POST['emergency_contact']; $medical_history = $_POST['medical_history'];

    $conn->begin_transaction();
    try {
        $stmt_user = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, gender = ?, date_of_birth = ? WHERE user_id = ?");
        $stmt_user->bind_param("ssssi", $full_name, $phone, $gender, $date_of_birth, $user_id); $stmt_user->execute();
        
        $stmt_patient = $conn->prepare("UPDATE patients SET blood_group = ?, address = ?, emergency_contact = ?, medical_history = ? WHERE user_id = ?");
        $stmt_patient->bind_param("ssssi", $blood_group, $address, $emergency_contact, $medical_history, $user_id); $stmt_patient->execute();
        
        $conn->commit();
        $message = "Profile updated successfully!"; $message_type = 'success';
        $_SESSION['full_name'] = $full_name;
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $message = "Error updating profile: " . $exception->getMessage(); $message_type = 'danger';
    }
}
$query = "SELECT u.full_name, u.email, u.phone, u.gender, u.date_of_birth, p.blood_group, p.address, p.emergency_contact, p.medical_history
          FROM users u JOIN patients p ON u.user_id = p.user_id WHERE u.user_id = ?";
$stmt = $conn->prepare($query); $stmt->bind_param("i", $user_id); $stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();
if (!$patient) die("Error: Could not find patient data.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } </style>
</head>
<body class="dashboard-body">
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4">Patient Profile</h2>
                    <a href="../dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                <?php endif; ?>

                <form action="profile.php" method="POST">
                    <h5 class="mb-3">Personal Details</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name:</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($patient['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email (Cannot be changed):</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($patient['email']); ?>" readonly disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number:</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth:</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($patient['date_of_birth']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender:</label>
                            <select id="gender" name="gender" class="form-select" required>
                                <option value="Male" <?php echo ($patient['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($patient['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($patient['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Medical & Contact Details</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="blood_group" class="form-label">Blood Group:</label>
                            <input type="text" class="form-control" id="blood_group" name="blood_group" value="<?php echo htmlspecialchars($patient['blood_group']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="emergency_contact" class="form-label">Emergency Contact Phone:</label>
                            <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="<?php echo htmlspecialchars($patient['emergency_contact']); ?>">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Address:</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($patient['address']); ?></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="medical_history" class="form-label">Medical History (Allergies, etc.):</label>
                            <textarea class="form-control" id="medical_history" name="medical_history" rows="5"><?php echo htmlspecialchars($patient['medical_history']); ?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>