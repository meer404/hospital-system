<?php
include '../db_connect.php';
include '../header.php';
$pageTitle = 'Doctor Profile';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') die("Access Denied.");
$user_id = $_SESSION['user_id']; $message = ''; $message_type = ''; $schedule_message = ''; $schedule_message_type = '';
$doctor_id = $conn->query("SELECT doctor_id FROM doctors WHERE user_id = $user_id")->fetch_assoc()['doctor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'update_profile') {
        $specialization = $_POST['specialization']; $qualification = $_POST['qualification']; $years = $_POST['years_of_experience'];
        $status = $_POST['availability_status']; $about = $_POST['about'];
        $stmt = $conn->prepare("UPDATE doctors SET specialization = ?, qualification = ?, years_of_experience = ?, availability_status = ?, about = ? WHERE doctor_id = ?");
        $stmt->bind_param("ssissi", $specialization, $qualification, $years, $status, $about, $doctor_id);
        if ($stmt->execute()) { $message = "Profile updated successfully!"; $message_type = 'success'; }
        else { $message = "Error: " . $stmt->error; $message_type = 'danger'; }
        $stmt->close();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'add_schedule') {
        $day = $_POST['available_day']; $start = $_POST['start_time']; $end = $_POST['end_time'];
        $stmt = $conn->prepare("INSERT INTO schedules (doctor_id, available_day, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $doctor_id, $day, $start, $end);
        if ($stmt->execute()) { $schedule_message = "Schedule slot added!"; $schedule_message_type = 'success'; }
        else { $schedule_message = "Error: " . $stmt->error; $schedule_message_type = 'danger'; }
        $stmt->close();
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'delete_schedule' && isset($_GET['id'])) {
    $schedule_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM schedules WHERE schedule_id = ? AND doctor_id = ?"); $stmt->bind_param("ii", $schedule_id, $doctor_id);
    if ($stmt->execute()) { $schedule_message = "Schedule slot deleted!"; $schedule_message_type = 'success'; }
    else { $schedule_message = "Error: " . $stmt->error; $schedule_message_type = 'danger'; }
    $stmt->close();
}
$doctor = $conn->query("SELECT u.full_name, u.email, d.* FROM users u JOIN doctors d ON u.user_id = d.user_id WHERE u.user_id = $user_id")->fetch_assoc();
$schedules_result = $conn->query("SELECT * FROM schedules WHERE doctor_id = $doctor_id ORDER BY FIELD(available_day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } </style>
</head>
<body>
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4">Doctor Profile & Schedule</h2>
                    <a href="../dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                </div>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                <?php endif; ?>

                <form action="profile.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <h5 class="mb-3">Professional Details</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Full Name:</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($doctor['full_name']); ?>" disabled></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Email:</label><input type="email" class="form-control" value="<?php echo htmlspecialchars($doctor['email']); ?>" disabled></div>
                        <div class="col-md-6 mb-3"><label for="specialization" class="form-label">Specialization:</label><input type="text" class="form-control" id="specialization" name="specialization" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" required></div>
                        <div class="col-md-6 mb-3"><label for="qualification" class="form-label">Qualification (e.g., MD, MBBS):</label><input type="text" class="form-control" id="qualification" name="qualification" value="<?php echo htmlspecialchars($doctor['qualification']); ?>" required></div>
                        <div class="col-md-6 mb-3"><label for="years_of_experience" class="form-label">Years of Experience:</label><input type="number" class="form-control" id="years_of_experience" name="years_of_experience" value="<?php echo htmlspecialchars($doctor['years_of_experience']); ?>" required></div>
                        <div class="col-md-6 mb-3"><label for="availability_status" class="form-label">Availability Status:</label><select id="availability_status" name="availability_status" class="form-select" required><option value="available" <?php echo ($doctor['availability_status'] == 'available') ? 'selected' : ''; ?>>Available</option><option value="unavailable" <?php echo ($doctor['availability_status'] == 'unavailable') ? 'selected' : ''; ?>>Unavailable</option></select></div>
                        <div class="col-12 mb-3"><label for="about" class="form-label">About Me:</label><textarea class="form-control" id="about" name="about" rows="4"><?php echo htmlspecialchars($doctor['about']); ?></textarea></div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
                </form>
                
                <hr class="my-5">

                <h5 class="mb-3">Manage Weekly Schedule</h5>
                <?php if ($schedule_message): ?>
                    <div class="alert alert-<?php echo $schedule_message_type; ?>"><?php echo $schedule_message; ?></div>
                <?php endif; ?>

                <form action="profile.php" method="POST" class="row g-3 align-items-end p-3 bg-light rounded mb-4">
                    <input type="hidden" name="action" value="add_schedule">
                    <div class="col-md-4"><label for="available_day" class="form-label">Day:</label><select id="available_day" name="available_day" class="form-select" required><option value="Sunday">Sunday</option><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option></select></div>
                    <div class="col-md-3"><label for="start_time" class="form-label">Start Time:</label><input type="time" class="form-control" id="start_time" name="start_time" required></div>
                    <div class="col-md-3"><label for="end_time" class="form-label">End Time:</label><input type="time" class="form-control" id="end_time" name="end_time" required></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Add Slot</button></div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light"><tr><th>Day</th><th>Start Time</th><th>End Time</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php
                        if ($schedules_result->num_rows > 0) {
                            while($row = $schedules_result->fetch_assoc()) {
                                echo "<tr><td>" . htmlspecialchars($row['available_day']) . "</td>";
                                echo "<td>" . htmlspecialchars(date('h:i A', strtotime($row['start_time']))) . "</td>";
                                echo "<td>" . htmlspecialchars(date('h:i A', strtotime($row['end_time']))) . "</td>";
                                echo "<td><a class='btn btn-sm btn-outline-danger' href='profile.php?action=delete_schedule&id=" . $row['schedule_id'] . "' onclick='return confirm(\"Are you sure?\");'><i class='bi bi-trash'></i></a></td></tr>";
                            }
                        } else echo "<tr><td colspan='4' class='text-center'>You have no schedule slots defined.</td></tr>";
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