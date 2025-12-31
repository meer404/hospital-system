<?php
include '../db_connect.php';
include '../header.php';
$pageTitle = 'Manage Prescriptions';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') die("Access Denied.");
$user_id = $_SESSION['user_id']; $message = ''; $message_type = '';
$doctor_id = $conn->query("SELECT doctor_id FROM doctors WHERE user_id = $user_id")->fetch_assoc()['doctor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_prescription') {
    $appointment_id = $_POST['appointment_id']; $diagnosis = $_POST['diagnosis'];
    $medicines = $_POST['medicines']; $advice = $_POST['advice']; $date_issued = date('Y-m-d');
    $conn->begin_transaction();
    try {
        $stmt_pr = $conn->prepare("INSERT INTO prescriptions (appointment_id, diagnosis, medicines, advice, date_issued) VALUES (?, ?, ?, ?, ?)");
        $stmt_pr->bind_param("issss", $appointment_id, $diagnosis, $medicines, $advice, $date_issued); $stmt_pr->execute();
        $stmt_app = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE appointment_id = ? AND doctor_id = ?");
        $stmt_app->bind_param("ii", $appointment_id, $doctor_id); $stmt_app->execute();
        $conn->commit();
        $message = "Prescription saved and appointment marked as completed!"; $message_type = 'success';
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $message = "Error saving prescription: " . $exception->getMessage(); $message_type = 'danger';
    }
}
$app_query = "SELECT a.appointment_id, a.appointment_date, u.full_name AS patient_name FROM appointments a
              JOIN patients p ON a.patient_id = p.patient_id JOIN users u ON p.user_id = u.user_id
              WHERE a.doctor_id = ? AND a.status = 'approved' AND a.appointment_id NOT IN (SELECT appointment_id FROM prescriptions)
              ORDER BY a.appointment_date";
$stmt_app_list = $conn->prepare($app_query); $stmt_app_list->bind_param("i", $doctor_id); $stmt_app_list->execute();
$appointments_result = $stmt_app_list->get_result();
$past_pr_query = "SELECT pr.*, a.appointment_date, u.full_name AS patient_name FROM prescriptions pr
                  JOIN appointments a ON pr.appointment_id = a.appointment_id JOIN patients p ON a.patient_id = p.patient_id
                  JOIN users u ON p.user_id = u.user_id WHERE a.doctor_id = ? ORDER BY pr.date_issued DESC";
$stmt_past_pr = $conn->prepare($past_pr_query); $stmt_past_pr->bind_param("i", $doctor_id); $stmt_past_pr->execute();
$past_prescriptions_result = $stmt_past_pr->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Prescriptions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } </style>
</head>
<body>
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4">Manage Prescriptions</h2>
                    <a href="../dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                <?php endif; ?>

                <h5 class="mb-3">Create New Prescription</h5>
                <form action="prescriptions.php" method="POST" class="p-4 bg-light rounded mb-5">
                    <input type="hidden" name="action" value="add_prescription">
                    <div class="mb-3">
                        <label for="appointment_id" class="form-label">Select Approved Appointment:</label>
                        <select name="appointment_id" id="appointment_id" class="form-select" required>
                            <option value="">-- Select Patient & Date --</option>
                            <?php
                            if ($appointments_result->num_rows > 0) {
                                while($row = $appointments_result->fetch_assoc()) {
                                    echo "<option value='" . $row['appointment_id'] . "'>" . htmlspecialchars($row['patient_name']) . " - (" . htmlspecialchars($row['appointment_date']) . ")</option>";
                                }
                            } else echo "<option value='' disabled>No approved appointments are pending.</option>";
                            ?>
                        </select>
                    </div>
                    <div class="mb-3"><label for="diagnosis" class="form-label">Diagnosis:</label><textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required></textarea></div>
                    <div class="mb-3"><label for="medicines" class="form-label">Medicines:</label><textarea class="form-control" id="medicines" name="medicines" rows="5" required></textarea></div>
                    <div class="mb-3"><label for="advice" class="form-label">Advice:</label><textarea class="form-control" id="advice" name="advice" rows="2"></textarea></div>
                    <button type="submit" class="btn btn-primary">Save Prescription</button>
                </form>

                <h5 class="mb-3">Past Prescriptions</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light"><tr><th>Date Issued</th><th>Patient</th><th>Diagnosis</th><th>Medicines</th><th>Advice</th></tr></thead>
                        <tbody>
                            <?php
                            if ($past_prescriptions_result->num_rows > 0) {
                                while($row = $past_prescriptions_result->fetch_assoc()) {
                                    echo "<tr><td>" . htmlspecialchars($row['date_issued']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['patient_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['diagnosis']) . "</td>";
                                    echo "<td>" . nl2br(htmlspecialchars($row['medicines'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['advice']) . "</td></tr>";
                                }
                            } else echo "<tr><td colspan='5' class='text-center'>You have not written any prescriptions.</td></tr>";
                            $stmt_past_pr->close();
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