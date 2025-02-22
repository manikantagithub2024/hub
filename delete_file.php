<?php
session_start();

// Check if the user is logged in and is the principal
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'principal') {
    header("Location: login.php");
    exit();
}

$uploads_folder = "uploads/";

// Get folder and file details from the query parameters
$hod_folder = isset($_GET['hod_folder']) ? $_GET['hod_folder'] : null;
$faculty_folder = isset($_GET['faculty_folder']) ? $_GET['faculty_folder'] : null;
$file_name = isset($_GET['file']) ? $_GET['file'] : null;

if ($hod_folder && $faculty_folder && $file_name) {
    $file_path = $uploads_folder . $hod_folder . '/' . $faculty_folder . '/' . $file_name;
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo "File deleted successfully.";
        } else {
            echo "Failed to delete file.";
        }
    } else {
        echo "File not found.";
    }
} else {
    echo "Invalid request.";
}

// Redirect back to the principal dashboard
header("Location: principal_dashboard.php?hod_folder=$hod_folder&faculty_folder=$faculty_folder");
exit();
?>