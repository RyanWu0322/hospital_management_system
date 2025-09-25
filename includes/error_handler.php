<?php
/**
 * 錯誤處理和日誌記錄函數
 */

// 設置錯誤顯示（生產環境中應設為0）
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// 創建日誌目錄
$log_dir = __DIR__ . '/../logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}

/**
 * 記錄錯誤到日誌文件
 */
function logError($message, $type = 'ERROR') {
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $user_id = $_SESSION['user_id'] ?? 'Guest';

    $log_message = "[$timestamp] [$type] [IP: $ip] [User: $user_id] [UA: $user_agent] $message" . PHP_EOL;

    $log_file = __DIR__ . '/../logs/error.log';
    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
}

/**
 * 記錄安全事件
 */
function logSecurityEvent($event, $details = '') {
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $user_id = $_SESSION['user_id'] ?? 'Guest';
    $username = $_SESSION['username'] ?? 'Guest';

    $log_message = "[$timestamp] [SECURITY] [IP: $ip] [User: $username ($user_id)] [UA: $user_agent] Event: $event";
    if ($details) {
        $log_message .= " - Details: $details";
    }
    $log_message .= PHP_EOL;

    $log_file = __DIR__ . '/../logs/security.log';
    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
}

/**
 * 安全地顯示錯誤給用戶（不洩露敏感信息）
 */
function showUserError($user_message) {
    echo "<script>alert('" . addslashes($user_message) . "');</script>";
}

/**
 * 處理數據庫錯誤
 */
function handleDatabaseError($stmt, $operation) {
    if (!$stmt) {
        logError("Database prepare failed for $operation: " . mysqli_error($GLOBALS['conn']));
        showUserError('系統錯誤，請稍後再試。');
        return false;
    }

    if (!$stmt->execute()) {
        logError("Database execute failed for $operation: " . $stmt->error);
        showUserError('操作失敗，請稍後再試。');
        return false;
    }

    return true;
}

/**
 * 自定義錯誤處理器
 */
function customErrorHandler($severity, $message, $file, $line) {
    $error_types = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_NOTICE => 'NOTICE',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE'
    ];

    $type = $error_types[$severity] ?? 'UNKNOWN';
    logError("$type: $message in $file on line $line");

    // 對於致命錯誤，顯示通用錯誤消息
    if ($severity === E_ERROR || $severity === E_USER_ERROR) {
        showUserError('系統發生錯誤，請聯絡管理員。');
    }

    return true; // 不執行PHP內部錯誤處理器
}

// 設置自定義錯誤處理器
set_error_handler('customErrorHandler');

/**
 * 異常處理器
 */
function customExceptionHandler($exception) {
    logError("Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    showUserError('系統發生異常，請聯絡管理員。');
}

// 設置異常處理器
set_exception_handler('customExceptionHandler');

/**
 * 關機時的錯誤處理
 */
function shutdownHandler() {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR)) {
        logError("Fatal error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line']);
        // 清理輸出並顯示錯誤頁面
        if (!headers_sent()) {
            http_response_code(500);
            echo "<script>alert('系統發生嚴重錯誤，請聯絡管理員。'); window.location.href='../public/login.php';</script>";
        }
    }
}

// 註冊關機處理器
register_shutdown_function('shutdownHandler');
?>