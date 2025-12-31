<?php
include 'db_connect.php';
include 'header.php';
$pageTitle = "Dashboard";
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$full_name = $_SESSION['full_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } </style>
</head>
<body>
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-4 px-5">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-0">Welcome, <?php echo htmlspecialchars($full_name); ?>!</h2>
                    <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </div>
            </div>
            <div class="card-body p-5">
                <h3 class="h5 mb-3">Your Dashboard</h3>
                <p class="text-muted">You are logged in as a: <span class="badge bg-primary fs-6"><?php echo ucfirst($role); ?></span></p>
                
                <nav class="mt-4">
                    <p class="fw-bold">Available actions:</p>
                    <div class="list-group">
                        <?php if ($role == 'admin'): ?>
                            <a href="Admin/manage_users.php" class="list-group-item list-group-item-action"><i class="bi bi-people-fill me-2"></i>Manage Users</a>
                            <a href="Admin/manage_doctors.php" class="list-group-item list-group-item-action"><i class="bi bi-person-badge me-2"></i>Manage Doctors</a>
                            <a href="Admin/manage_patients.php" class="list-group-item list-group-item-action"><i class="bi bi-person-fill me-2"></i>Manage Patients</a>
                        <?php elseif ($role == 'doctor'): ?>
                            <a href="Doctor/profile.php" class="list-group-item list-group-item-action"><i class="bi bi-person-vcard me-2"></i>My Profile & Schedule</a>
                            <a href="Doctor/appointments.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar-check me-2"></i>My Appointments</a>
                            <a href="Doctor/prescriptions.php" class="list-group-item list-group-item-action"><i class="bi bi-file-earmark-medical me-2"></i>Manage Prescriptions</a>
                        <?php elseif ($role == 'patient'): ?>
                            <a href="Patient/profile.php" class="list-group-item list-group-item-action"><i class="bi bi-person-circle me-2"></i>My Profile</a>
                            <a href="Patient/book_appointment.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar-plus me-2"></i>Book New Appointment</a>
                            <a href="Patient/my_appointments.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar3 me-2"></i>My Appointments</a>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'footer.php'; ?>