<?php
// 安全的會話管理

// 設置安全的會話設定
if (session_status() == PHP_SESSION_NONE) {
    // 設置安全的會話參數
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // 在HTTPS環境中應設為1
    ini_set('session.cookie_samesite', 'Strict');

    // 設置會話過期時間為30分鐘
    ini_set('session.gc_maxlifetime', 1800);
    session_start();
}

// 檢查會話是否有效
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

// 會話劫持保護：檢查IP地址和用戶代理
if (!isset($_SESSION['ip_address'])) {
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
} else {
    if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] ||
        $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_destroy();
        header('Location: ../public/login.php?error=session_invalid');
        exit();
    }
}

// 會話超時檢查
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > 1800) { // 30分鐘超時
        session_destroy();
        header('Location: ../public/login.php?error=session_timeout');
        exit();
    }
}
$_SESSION['last_activity'] = time();

// 定期重新生成會話ID（每5分鐘）
if (!isset($_SESSION['session_regenerated'])) {
    $_SESSION['session_regenerated'] = time();
} elseif (time() - $_SESSION['session_regenerated'] > 300) { // 5分鐘
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = time();
}
?>
