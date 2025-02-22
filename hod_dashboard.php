<?php
session_start();

// Check if the user is logged in and is an HOD
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hod') {
    header("Location: login.php");
    exit();
}

$branch = $_SESSION['branch'];
$hod_folder = "uploads/{$branch}_hod/";

// Fetch all faculty folders
$faculty_folders = [];
if (is_dir($hod_folder)) {
    $folders = scandir($hod_folder);
    foreach ($folders as $folder) {
        if ($folder !== '.' && $folder !== '..' && is_dir($hod_folder . $folder)) {
            $faculty_folders[] = $folder;
        }
    }
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['faculty_folder'])) {
    $faculty_folder_name = $_POST['faculty_folder'];
    $faculty_folder_path = $hod_folder . $faculty_folder_name . '/';

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['file']['name']);
        $file_path = $faculty_folder_path . $file_name;
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
    $file_to_delete = $hod_folder . $_GET['faculty_folder'] . '/' . basename($_GET['delete']);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .dashboard-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 800px;
            width: 100%;
        }
        .dashboard-container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .dashboard-container th, .dashboard-container td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .dashboard-container th {
            background-color: #f4f4f4;
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>HOD Dashboard - <?php echo $branch; ?></h2>
        <h3>Faculty Folders</h3>
        <table>
            <thead>
                <tr>
                    <th>Faculty Folder</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($faculty_folders)): ?>
                    <?php foreach ($faculty_folders as $folder): ?>
                        <tr>
                            <td><?php echo $folder; ?></td>
                            <td>
                                <a href="hod_dashboard.php?faculty_folder=<?php echo $folder; ?>">View Files</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No faculty folders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (isset($_GET['faculty_folder'])): ?>
            <?php
            $selected_faculty_folder = $_GET['faculty_folder'];
            $files = [];
            if (is_dir($hod_folder . $selected_faculty_folder)) {
                $files = scandir($hod_folder . $selected_faculty_folder);
                $files = array_diff($files, ['.', '..']);
            }
            ?>
            <h3>Files in <?php echo $selected_faculty_folder; ?></h3>
            <div class="file-list">
                <?php if (!empty($files)): ?>
                    <?php foreach ($files as $file): ?>
                        <div class="file-item">
                            <a href="<?php echo $hod_folder . $selected_faculty_folder . '/' . $file; ?>" target="_blank"><?php echo $file; ?></a>
                            <button onclick="deleteFile('<?php echo $selected_faculty_folder; ?>', '<?php echo $file; ?>')">Delete</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No files found in this folder.</p>
                <?php endif; ?>
            </div>

            <div class="upload-form">
                <h3>Upload File</h3>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="faculty_folder" value="<?php echo $selected_faculty_folder; ?>">
                    <input type="file" name="file" required>
                    <button type="submit">Upload</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>

    <script>
        function deleteFile(facultyFolder, fileName) {
            if (confirm("Are you sure you want to delete this file?")) {
                window.location.href = "hod_dashboard.php?faculty_folder=" + facultyFolder + "&delete=" + fileName;
            }
        }
    </script>
</body>
</html>