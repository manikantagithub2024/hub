<?php
session_start();

if (!isset($_SESSION['verification_code']) || !isset($_SESSION['user_id'])) {
    die('Invalid request');
}

$entered_code = $_POST['verification_code'];

if ($entered_code == $_SESSION['verification_code']) {
    // Verification successful
    $role = $_SESSION['role'];
    $branch = $_SESSION['branch'];
    $username = $_SESSION['username'];

    // Redirect based on role
    if ($role === 'hod') {
        header("Location: hod_dashboard.php");
    } else if ($role === 'faculty') {
        header("Location: faculty_dashboard.php");
    } else if ($role === 'principal') {
        header("Location: principal_dashboard.php");
    } else {
        header("Location: dashboard.php"); // Default dashboard for other roles
    }
    exit();
} else {
    echo "Invalid verification code. Please try again.";
}
?>