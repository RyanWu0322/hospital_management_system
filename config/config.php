<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospital_management_system_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

$admin_check = $conn->query("SELECT id FROM users WHERE username='admin'");
if ($admin_check->num_rows == 0) {
    $admin_password = password_hash('admin', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, password, role, failed_attempts, locked) VALUES ('admin', '$admin_password', 'admin', 0, 0)");
}
?>
