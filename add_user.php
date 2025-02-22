<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "mani#200312";
$dbname = "admin_panel";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
$branch = $_POST['branch'];
$role = $_POST['role'];

// Insert user into database
$sql = "INSERT INTO users (username, email, password, branch, role) VALUES ('$username', '$email', '$password', '$branch', '$role')";

if ($conn->query($sql) === TRUE) {
    // Create folder based on role
    $uploads_dir = "uploads/";
    
    if ($role == "hod") {
        // Create HOD folder
        $hod_dir = $uploads_dir . $username . "/";
        if (!file_exists($hod_dir)) {
            mkdir($hod_dir, 0777, true);
            echo "HOD folder created successfully.<br>";
        }
    } elseif ($role == "faculty") {
        // Create faculty folder inside the corresponding HOD folder
        $hod_dir = $uploads_dir . $branch . "_hod/";
        $faculty_dir = $hod_dir . $username . "/";
        
        if (!file_exists($hod_dir)) {
            mkdir($hod_dir, 0777, true);
            echo "HOD folder created successfully.<br>";
        }
        
        if (!file_exists($faculty_dir)) {
            mkdir($faculty_dir, 0777, true);
            echo "Faculty folder created successfully.<br>";
        }
    }

    echo "New user created successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>