<?php
include '../db_connect.php';
include '../header.php';
$pageTitle = 'Admin: Manage Patients';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') die("Access Denied.");
$query = "SELECT u.full_name, u.email, u.phone, u.gender, p.blood_group, p.address
          FROM users u JOIN patients p ON u.user_id = p.user_id
          WHERE u.role = 'patient' ORDER BY u.full_name";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Manage Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } </style>
</head>
<body>
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4">Admin Panel - Manage Patients</h2>
                    <a href="../dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light"><tr><th>Patient Name</th><th>Email</th><th>Phone</th><th>Gender</th><th>Blood Group</th><th>Address</th></tr></thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr><td>" . htmlspecialchars($row['full_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['blood_group']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['address']) . "</td></tr>";
                                }
                            } else echo "<tr><td colspan='6' class='text-center'>No patients found.</td></tr>";
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>