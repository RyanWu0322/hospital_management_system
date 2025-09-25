<?php
session_start();
include '../includes/session.php';
include '../config/config.php';

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
    $stmt->bind_param('ssi', $username, $password, $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('個人資料已更新');</script>";
        $_SESSION['username'] = $username;
    } else {
        echo "<script>alert('更新失敗');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>個人資料管理</title>
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h2>個人資料管理</h2>
        <form method="post" action="">
            <label for="username">用戶名：</label>
            <input type="text" name="username" id="username" value="<?php echo $user['username']; ?>" required>
            <label for="password">密碼：</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">更新</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>



</html>
