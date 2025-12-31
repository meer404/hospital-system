<?php
include '../db_connect.php';
include '../header.php';
$pageTitle = 'My Appointments';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') die("Access Denied.");
$patient_user_id = $_SESSION['user_id'];
$patient_id = $conn->query("SELECT patient_id FROM patients WHERE user_id = $patient_user_id")->fetch_assoc()['patient_id'];

$query = "SELECT a.appointment_date, a.appointment_time, a.status, a.notes, u.full_name AS doctor_name, d.specialization
          FROM appointments a JOIN doctors d ON a.doctor_id = d.doctor_id
          JOIN users u ON d.user_id = u.user_id WHERE a.patient_id = ?
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $conn->prepare($query); $stmt->bind_param("i", $patient_id); $stmt->execute();
$result = $stmt->get_result();

function getStatusBadge($status) {
    switch ($status) {
        case 'approved': return 'success';
        case 'pending': return 'warning';
        case 'completed': return 'info';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } </style>
</head>
<body>
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4">My Appointments</h2>
                    <a href="../dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Doctor</th><th>Specialization</th><th>Date</th>
                                <th>Time</th><th>Status</th><th>My Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $badge_class = getStatusBadge($row['status']);
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['doctor_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['specialization']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['appointment_date']) . "</td>";
                                    echo "<td>" . htmlspecialchars(date('h:i A', strtotime($row['appointment_time']))) . "</td>";
                                    echo "<td><span class='badge bg-{$badge_class}'>" . ucfirst(htmlspecialchars($row['status'])) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>You have no appointments.</td></tr>";
                            }
                            $stmt->close();
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