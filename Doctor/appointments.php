<?php
include '../db_connect.php';
include '../header.php';
$pageTitle = 'My Appointments';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') die("Access Denied.");
$doctor_user_id = $_SESSION['user_id'];
$doctor_id = $conn->query("SELECT doctor_id FROM doctors WHERE user_id = $doctor_user_id")->fetch_assoc()['doctor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id']; $status = '';
    if (isset($_POST['action_approve'])) $status = 'approved';
    elseif (isset($_POST['action_cancel'])) $status = 'cancelled';
    
    if ($status) {
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ? AND doctor_id = ?");
        $stmt->bind_param("sii", $status, $appointment_id, $doctor_id); $stmt->execute(); $stmt->close();
        header("Location: appointments.php"); exit();
    }
}
$query = "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, a.notes, u.full_name AS patient_name, u.phone AS patient_phone
          FROM appointments a JOIN patients p ON a.patient_id = p.patient_id JOIN users u ON p.user_id = u.user_id
          WHERE a.doctor_id = ? ORDER BY a.appointment_date, a.appointment_time";
$stmt = $conn->prepare($query); $stmt->bind_param("i", $doctor_id); $stmt->execute();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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
                                <th>Patient</th><th>Phone</th><th>Date</th><th>Time</th>
                                <th>Reason</th><th>Status</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $badge_class = getStatusBadge($row['status']);
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['patient_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['patient_phone']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['appointment_date']) . "</td>";
                                    echo "<td>" . htmlspecialchars(date('h:i A', strtotime($row['appointment_time']))) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
                                    echo "<td><span class='badge bg-{$badge_class}'>" . ucfirst(htmlspecialchars($row['status'])) . "</span></td>";
                                    echo "<td style='min-width: 170px;'>";
                                    if ($row['status'] == 'pending') {
                                        echo "<form action='appointments.php' method='POST' class='d-inline-block me-1'>
                                                <input type='hidden' name='appointment_id' value='{$row['appointment_id']}'>
                                                <button type='submit' name='action_approve' class='btn btn-sm btn-success'><i class='bi bi-check-lg'></i></button>
                                              </form>";
                                        echo "<form action='appointments.php' method='POST' class='d-inline-block'>
                                                <input type='hidden' name='appointment_id' value='{$row['appointment_id']}'>
                                                <button type='submit' name='action_cancel' class='btn btn-sm btn-danger'><i class='bi bi-x-lg'></i></button>
                                              </form>";
                                    } else echo "N/A";
                                    echo "</td></tr>";
                                }
                            } else echo "<tr><td colspan='7' class='text-center'>You have no appointments.</td></tr>";
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