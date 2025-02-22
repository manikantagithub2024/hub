<?php
header('Content-Type: application/json'); // Ensure JSON response

// Database connection
$servername = "localhost";
$username = "root";
$password = "mani#200312";
$dbname = "admin_panel";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get input data
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST; // Fallback for form submissions
}

$username = $data['username'];

if (empty($username)) {
    echo json_encode(["success" => false, "message" => "Username is required"]);
    exit;
}

// Fetch user details before deletion
$sql = "SELECT branch, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}

$user = $result->fetch_assoc();
$branch = $user['branch'];
$role = $user['role'];

// Delete user from database
$sql = "DELETE FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    // Remove the user's folder
    $uploads_dir = "uploads/";
    if ($role == "hod") {
        $user_dir = $uploads_dir . $username . "/";
    } elseif ($role == "faculty") {
        $user_dir = $uploads_dir . $branch . "_hod/" . $username . "/";
    }

    if (isset($user_dir) && file_exists($user_dir)) {
        deleteFolder($user_dir);
    }

    echo json_encode(["success" => true, "message" => "User deleted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error deleting user: " . $stmt->error]);
}

$stmt->close();
$conn->close();

// Function to delete folder and its contents
function deleteFolder($folder) {
    if (!is_dir($folder)) return;
    $files = array_diff(scandir($folder), array('.', '..'));
    foreach ($files as $file) {
        $filePath = $folder . DIRECTORY_SEPARATOR . $file;
        is_dir($filePath) ? deleteFolder($filePath) : unlink($filePath);
    }
    rmdir($folder);
}
?>
