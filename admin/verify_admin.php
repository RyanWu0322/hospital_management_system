<?php
session_start();
include '../config/config.php';
include '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCSRFToken();
    $password = $_POST['password']; 
    $admin_password = 'admin'; //admin預設密碼

    if ($password == $admin_password) {
        $_SESSION['admin_verified'] = true;
        header("Location: account_management.php");
    } else {
        echo "<script>alert('密碼錯誤'); window.location.href='account_management.php';</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>驗證管理員</title>
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
</head>
<body>
    <div class="container">
        <h2>輸入管理員密碼</h2>
        <form method="post" action="">
            <label for="password">密碼：</label>
            <input type="password" name="password" id="password" required>
            <?php echo getCSRFTokenField(); ?>
            <button type="submit">提交</button>
        </form>
    </div>
</body>

<svg class="editorial"
    xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    viewBox="0 24 150 28 "
    preserveAspectRatio="none">
<defs>
<path id="gentle-wave"
d="M-160 44c30 0 
    58-18 88-18s
    58 18 88 18 
    58-18 88-18 
    58 18 88 18
    v44h-352z" />
</defs>
    <g class="parallax1">
        <use xlink:href="#gentle-wave" x="50" y="3" fill="#f461c1"/>
    </g>
    <g class="parallax2">
        <use xlink:href="#gentle-wave" x="50" y="0" fill="#4579e2"/>
    </g>
    <g class="parallax3">
        <use xlink:href="#gentle-wave" x="50" y="9" fill="#3461c1"/>
    </g>
    <g class="parallax4">
        <use xlink:href="#gentle-wave" x="50" y="6" fill="#fff"/>  
    </g>
</svg>
</html>
