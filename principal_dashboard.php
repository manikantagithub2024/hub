<?php
session_start();

// Check if the user is logged in and is the principal
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'principal') {
    header("Location: login.php");
    exit();
}

$uploads_folder = "uploads/";

// Fetch all HOD folders
$hod_folders = [];
if (is_dir($uploads_folder)) {
    $folders = scandir($uploads_folder);
    foreach ($folders as $folder) {
        if ($folder !== '.' && $folder !== '..' && is_dir($uploads_folder . $folder)) {
            $hod_folders[] = $folder;
        }
    }
}

// Check if an HOD folder is selected
$selected_hod_folder = isset($_GET['hod_folder']) ? $_GET['hod_folder'] : null;
$faculty_folders = [];
if ($selected_hod_folder && is_dir($uploads_folder . $selected_hod_folder)) {
    $faculty_folders = scandir($uploads_folder . $selected_hod_folder);
    $faculty_folders = array_diff($faculty_folders, ['.', '..']);
}

// Check if a faculty folder is selected
$selected_faculty_folder = isset($_GET['faculty_folder']) ? $_GET['faculty_folder'] : null;
$files = [];
if ($selected_faculty_folder && is_dir($uploads_folder . $selected_hod_folder . '/' . $selected_faculty_folder)) {
    $files = scandir($uploads_folder . $selected_hod_folder . '/' . $selected_faculty_folder);
    $files = array_diff($files, ['.', '..']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal Dashboard</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .folder-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .folder-item {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
        }
        .folder-item:hover {
            background-color: #0056b3;
        }
        .faculty-list {
            margin-top: 20px;
        }
        .faculty-item {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .faculty-item:hover {
            background-color: #218838;
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
        <h1>Principal Dashboard</h1>
        <h2>HOD Folders</h2>
        <div class="folder-list">
            <?php if (!empty($hod_folders)): ?>
                <?php foreach ($hod_folders as $folder): ?>
                    <a href="principal_dashboard.php?hod_folder=<?php echo $folder; ?>" class="folder-item">
                        <?php echo $folder; ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No HOD folders found.</p>
            <?php endif; ?>
        </div>

        <?php if ($selected_hod_folder): ?>
            <h2>Faculty Folders in <?php echo $selected_hod_folder; ?></h2>
            <div class="faculty-list">
                <?php if (!empty($faculty_folders)): ?>
                    <?php foreach ($faculty_folders as $folder): ?>
                        <a href="principal_dashboard.php?hod_folder=<?php echo $selected_hod_folder; ?>&faculty_folder=<?php echo $folder; ?>" class="faculty-item">
                            <?php echo $folder; ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No faculty folders found.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($selected_faculty_folder): ?>
            <h2>Files in <?php echo $selected_faculty_folder; ?></h2>
            <div class="file-list">
                <?php if (!empty($files)): ?>
                    <?php foreach ($files as $file): ?>
                        <div class="file-item">
                            <a href="<?php echo $uploads_folder . $selected_hod_folder . '/' . $selected_faculty_folder . '/' . $file; ?>" target="_blank"><?php echo $file; ?></a>
                            <button onclick="deleteFile('<?php echo $selected_hod_folder; ?>', '<?php echo $selected_faculty_folder; ?>', '<?php echo $file; ?>')">Delete</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No files found in this folder.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function deleteFile(hodFolder, facultyFolder, fileName) {
            if (confirm("Are you sure you want to delete this file?")) {
                window.location.href = "delete_file.php?hod_folder=" + hodFolder + "&faculty_folder=" + facultyFolder + "&file=" + fileName;
            }
        }
    </script>
</body>
</html>