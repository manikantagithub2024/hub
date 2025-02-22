<?php
function db_connect() {
    $conn = new mysqli('localhost', 'root', 'mani#200312', 'admin_panel');

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
