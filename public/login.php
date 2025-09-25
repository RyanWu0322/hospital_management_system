<?php
session_start();
include '../config/config.php';
include '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCSRFToken();
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare('SELECT id, password, role, failed_attempts, locked FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role, $failed_attempts, $locked);
        $stmt->fetch();
        
        if ($locked) {
            echo '<script>alert("您的帳號已被鎖定。請聯絡管理員。");</script>';
        } elseif (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['last_activity'] = time();
            $_SESSION['session_regenerated'] = time();

            // 重新生成會話ID以防會話固定攻擊
            session_regenerate_id(true);

            // 成功登入後重置失敗次數
            $stmt = $conn->prepare('UPDATE users SET failed_attempts = 0 WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();

            header('Location: dashboard.php');
            exit();
        } else {
            $failed_attempts++;
            if ($failed_attempts >= 3) {
                $stmt = $conn->prepare('UPDATE users SET failed_attempts = ?, locked = 1 WHERE id = ?');
                $stmt->bind_param('ii', $failed_attempts, $id);
                $stmt->execute();
                echo '<script>alert("您的帳號已因多次登錄失敗而被鎖定。");</script>';
            } else {
                $stmt = $conn->prepare('UPDATE users SET failed_attempts = ? WHERE id = ?');
                $stmt->bind_param('ii', $failed_attempts, $id);
                $stmt->execute();
                echo '<script>alert("密碼無效。您還有 ' . (3 - $failed_attempts) . ' 次機會。");</script>';
            }
        }
    } else {
        echo '<script>alert("用戶不存在。");</script>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>登入系統</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>

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

    <div class="login-container">
        <div class="login-left">
            <img src="..\pic\lefticon.webp" alt="Logo">
            <h1>醫護管理系統</h1>
        </div>
        <div class="login-right">
            <img src="..\pic\icon.png" alt="logo" >
            <h3>請輸入你的帳號密碼</h3>
            <form method="post">
                <label for="username">使用者名稱</label>
                <input type="text" name="username" id="username" required>
                <label for="password">密碼</label>
                <input type="password" name="password" id="password" required>
                <div class="remember-me">
                    <input type="checkbox" name="remember_me" id="remember_me">
                    <label for="remember_me">記住帳號密碼 (非本人電腦請勿點選)</label>
                </div>
                <?php echo getCSRFTokenField(); ?>
                <button type="submit">登入系統</button>
            </form>
        </div>
    </div>
</body>
</html>
