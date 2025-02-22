<?php
session_start();

// Check if the user is logged in and is a faculty member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

$branch = $_SESSION['branch'];
$username = $_SESSION['username'];
$faculty_folder = "uploads/{$branch}_hod/{$username}/";

// Create the faculty folder if it doesn't exist
if (!is_dir($faculty_folder)) {
    mkdir($faculty_folder, 0777, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['file']['name']);
        $file_path = $faculty_folder . $file_name;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            $message = "File uploaded successfully.";
        } else {
            $message = "Failed to upload file.";
        }
    } else {
        $message = "No file selected or upload error.";
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $file_to_delete = $faculty_folder . basename($_GET['delete']);
    if (file_exists($file_to_delete)) {
        if (unlink($file_to_delete)) {
            $message = "File deleted successfully.";
        } else {
            $message = "Failed to delete file.";
        }
    } else {
        $message = "File not found.";
    }
}

// Fetch list of files in the faculty folder
$files = [];
if (is_dir($faculty_folder)) {
    $files = scandir($faculty_folder);
    $files = array_diff($files, ['.', '..']); // Remove . and .. from the list
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .dashboard-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .upload-form {
            margin-top: 20px;
        }
        .upload-form input[type="file"] {
            margin-bottom: 10px;
        }
        .upload-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .upload-form button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            color: green;
        }
        .file-list {
            margin-top: 20px;
        }
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .file-item a {
            color: #007bff;
            text-decoration: none;
        }
        .file-item a:hover {
            text-decoration: underline;
        }
        .file-item button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .file-item button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Faculty Dashboard - <?php echo $username; ?></h1>
        <h2>Upload Files</h2>
        <form class="upload-form" action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit">Upload</button>
        </form>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <h2>Uploaded Files</h2>
        <div class="file-list">
            <?php if (!empty($files)): ?>
                <?php foreach ($files as $file): ?>
                    <div class="file-item">
                        <a href="<?php echo $faculty_folder . $file; ?>" target="_blank"><?php echo $file; ?></a>
                        <button onclick="deleteFile('<?php echo $file; ?>')">Delete</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No files uploaded yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function deleteFile(fileName) {
            if (confirm("Are you sure you want to delete this file?")) {
                window.location.href = "faculty_dashboard.php?delete=" + fileName;
            }
        }
    </script>
</body>
</html>