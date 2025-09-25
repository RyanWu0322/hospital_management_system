<?php
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
</head>
<body>
<header class="header">
    <nav>
        <ul>
            <li><a href="../public/dashboard.php">儀表板</a></li>
            <?php if ($role == 'admin'): ?>
                <li><a href="../admin/add_user.php">新增使用者</a></li>
                <li><a href="../admin/manage_beds.php">管理病床</a></li>
                <li><a href="../admin/account_management.php">帳號管理</a></li>
            <?php endif; ?>
            <?php if ($role == 'doctor' || $role == 'nurse'): ?>
                <li><a href="../public/profile.php">個人資料管理</a></li>
                <li><a href="../public/medical_records.php">病歷管理</a></li>
            <?php endif; ?>
            <li><a href="../public/logout.php">登出</a></li>
        </ul>
    </nav>
</header>
</body>
</html>
