<?php
include '../db_connect.php';

include '../header.php';
$pageTitle = 'Book Appointment';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') die("Access Denied.");
$patient_user_id = $_SESSION['user_id']; $message = ''; $message_type = '';
$patient_id = $conn->query("SELECT patient_id FROM patients WHERE user_id = $patient_user_id")->fetch_assoc()['patient_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id']; $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time']; $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $notes);
    
    if ($stmt->execute()) { $message = "Appointment booked successfully! Your request is pending approval."; $message_type = 'success'; }
    else { $message = "Error: " . $stmt->error; $message_type = 'danger'; }
    $stmt->close();
}
$doctor_query = "SELECT d.doctor_id, u.full_name, d.specialization FROM doctors d JOIN users u ON d.user_id = u.user_id WHERE d.availability_status = 'available'";
$doctors_result = $conn->query($doctor_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="h4">Book New Appointment</h2>
                            <a href="../dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                        </div>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <form action="book_appointment.php" method="POST">
                            <div class="mb-3">
                                <label for="doctor_id" class="form-label">Select Doctor:</label>
                                <select name="doctor_id" id="doctor_id" class="form-select" required>
                                    <option value="">-- Choose a Doctor --</option>
                                    <?php
                                    if ($doctors_result->num_rows > 0) {
                                        while($row = $doctors_result->fetch_assoc()) {
                                            echo "<option value='" . $row['doctor_id'] . "'>" . htmlspecialchars($row['full_name']) . " - (" . htmlspecialchars($row['specialization']) . ")</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="appointment_date" class="form-label">Select Date:</label>
                                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="appointment_time" class="form-label">Select Time:</label>
                                    <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Reason / Notes (Optional):</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Book Appointment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>