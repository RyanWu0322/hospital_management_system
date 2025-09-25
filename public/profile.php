<?php
session_start();
include '../includes/session.php';
include '../config/config.php';
include '../includes/csrf.php';
include '../includes/validation.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCSRFToken();

    // 驗證輸入
    $username = validateUsername($_POST['username']);
    $password = validatePassword($_POST['password']);

    if (!$username) {
        echo "<script>alert('用戶名格式不正確（3-50字元，只能包含字母、數字、底線、連字符）');</script>";
    } elseif (!$password) {
        echo "<script>alert('密碼至少要有6個字元');</script>";
    } else {
        // 檢查用戶名是否已被其他用戶使用
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check_stmt->bind_param('si', $username, $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('用戶名已被使用');</script>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
            $stmt->bind_param('ssi', $username, $hashed_password, $user_id);
            if ($stmt->execute()) {
                echo "<script>alert('個人資料已更新');</script>";
                $_SESSION['username'] = $username;
            } else {
                echo "<script>alert('更新失敗');</script>";
            }
        }
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
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <label for="password">密碼：</label>
            <input type="password" name="password" id="password" required>
            <?php echo getCSRFTokenField(); ?>
            <button type="submit">更新</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>



</html>
