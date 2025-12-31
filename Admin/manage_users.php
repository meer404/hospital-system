<?php
include '../db_connect.php';
include '../header.php';
$pageTitle = 'Admin: Manage Users';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') die("Access Denied.");
$result = $conn->query("SELECT user_id, full_name, email, phone, role, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } </style>
</head>
<body>
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4">Admin Panel - Manage All Users</h2>
                    <a href="../dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light"><tr><th>ID</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Registered On</th></tr></thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr><td>" . $row['user_id'] . "</td>";
                                    echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                    echo "<td><span class='badge bg-info'>" . ucfirst(htmlspecialchars($row['role'])) . "</span></td>";
                                    echo "<td>" . htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))) . "</td></tr>";
                                }
                            } else echo "<tr><td colspan='6' class='text-center'>No users found.</td></tr>";
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