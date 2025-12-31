<?php
// header.php
// This file MUST be included first by all other files
// It includes db_connect.php, which starts the session and defines BASE_URL
include_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Clinic System' : 'Clinic System'; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/Assets/css/layout.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/dashboard.php">
                <i class="bi bi-heart-pulse-fill"></i> Clinic System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo BASE_URL; ?>/dashboard.php">Dashboard</a>
                        </li>
                        
                        <?php // Show links based on role
                        switch ($_SESSION['role']) {
                            case 'admin':
                                echo '<li class="nav-item"><a class="nav-link" href="' . BASE_URL . '/Admin/manage_users.php">Users</a></li>';
                                echo '<li class="nav-item"><a class="nav-link" href="' . BASE_URL . '/Admin/manage_doctors.php">Doctors</a></li>';
                                echo '<li class="nav-item"><a class="nav-link" href="' . BASE_URL . '/Admin/manage_patients.php">Patients</a></li>';
                                break;
                            case 'doctor':
                                echo '<li class="nav-item"><a class="nav-link" href="' . BASE_URL . '/Doctor/profile.php">Profile</a></li>';
                                echo '<li class="nav-item"><a class="nav-link" href="' . BASE_URL . '/Doctor/appointments.php">Appointments</a></li>';
                                echo '<li class="nav-item"><a class="nav-link" href="' . BASE_URL . '/Doctor/prescriptions.php">Prescriptions</a></li>';
                                break;
                            case 'patient':
                                echo '<li class="nav-item"><a class="nav-link" href="' . BASE_URL . '/Patient/profile.php">My Profile</a></li>';
                                echo '<li class="nav-item"><a class="nav-link" href="' . BASE_URL . '/Patient/book_appointment.php">Book Appointment</a></li>';
                                echo '<li class="nav-item"><a class="nav-link" href="' . BASE_URL . '/Patient/my_appointments.php">My Appointments</a></li>';
                                break;
                        } ?>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
                
            </div>
        </div>
    </nav>

    <main class="container my-5"></main>