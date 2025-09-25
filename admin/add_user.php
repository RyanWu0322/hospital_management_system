<?php
session_start();
include '../includes/session.php';
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $specialty = $role == 'doctor' ? $_POST['specialty'] : null;

    $stmt = $conn->prepare('INSERT INTO users (username, password, role, specialty) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $username, $password, $role, $specialty);
    if ($stmt->execute()) {
        echo "<script>alert('用戶已成功新增');</script>";
    } else {
        echo "<script>alert('新增用戶失敗');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>新增使用者</title>
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
    <script>
        function toggleSpecialty() {
            var role = document.getElementById("role").value;
            var specialtyDiv = document.getElementById("specialtyDiv");
            if (role == 'doctor') {
                specialtyDiv.style.display = "block";
            } else {
                specialtyDiv.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h2>新增使用者</h2>
        <form method="post" action="">
            <label for="username">用戶名：</label>
            <input type="text" name="username" id="username" required>
            <label for="password">密碼：</label>
            <input type="password" name="password" id="password" required>
            <label for="role">角色：</label>
            <select name="role" id="role" onchange="toggleSpecialty()" required>
                <option value="doctor">醫生</option>
                <option value="nurse">護士</option>
            </select>
            <div id="specialtyDiv" style="display:none;">
                <label for="specialty">科別：</label>
                <select name="specialty" id="specialty">
                    <option value="骨科">骨科</option>
                    <option value="齒科">齒科</option>
                    <option value="外科">外科</option>
                    <option value="內科">內科</option>
                    <option value="婦產科">婦產科</option>
                    <option value="耳鼻喉科">耳鼻喉科</option>
                    <option value="眼科">眼科</option>
                    <option value="皮膚科">皮膚科</option>
                    <option value="精神科">精神科</option>
                    <option value="心理輔導">心理輔導</option>
                    <option value="復健科">復健科</option>
                    <option value="兒科">兒科</option>
                    <option value="老年科">老年科</option>
                    <option value="放射科">放射科</option>
                    <option value="麻醉科">麻醉科</option>
                    <option value="燒傷科">燒傷科</option>
                    <option value="感染科">感染科</option>
                    <option value="急診醫學科">急診醫學科</option>
                </select>
            </div>
            <button type="submit">新增用戶</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>


</html>
