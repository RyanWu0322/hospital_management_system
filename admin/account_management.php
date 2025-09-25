<?php
session_start();
include '../includes/session.php';
include '../config/config.php';
include '../includes/csrf.php';

// 處理POST請求（鎖定/解鎖和刪除用戶）
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCSRFToken();

    if (isset($_POST['toggle_lock'])) {
        $user_id = (int)$_POST['toggle_lock'];
        $user_status = $_POST['status'];
        $new_status = $user_status == 'active' ? 'locked' : 'active';

        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $new_status, $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('使用者狀態已更新');</script>";
        } else {
            echo "<script>alert('更新失敗');</script>";
        }
    }

    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['delete_user'];

        // 防止刪除admin用戶
        $check_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $check_stmt->bind_param('i', $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result()->fetch_assoc();

        if ($result && $result['username'] !== 'admin') {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param('i', $user_id);
            if ($stmt->execute()) {
                echo "<script>alert('使用者已刪除');</script>";
            } else {
                echo "<script>alert('刪除失敗');</script>";
            }
        } else {
            echo "<script>alert('無法刪除管理員帳戶');</script>";
        }
    }
}

// 獲取使用者名單
$users = $conn->query("SELECT id, username, role, status FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>帳號管理介面</title>
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
    <style>
        .table-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .status-active {
            background-color: green;
            color: white;
            padding: 5px;
            border-radius: 5px;
            text-align: center;
        }
        .status-locked {
            background-color: red;
            color: white;
            padding: 5px;
            border-radius: 5px;
            text-align: center;
        }
        .action-buttons a {
            margin-right: 10px;
            color: blue;
            text-decoration: none;
        }
        .action-buttons a:hover {
            text-decoration: underline;
        }
        .no-action {
            background-color: #9D9D9D;
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="table-container">
        <h2>帳號管理介面</h2>
        <table>
            <thead>
                <tr>
                    <th>名稱</th>
                    <th>密碼</th>
                    <th>身分類別</th>
                    <th>帳號狀態</th>
                    <th>動作</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>******</td>
                        <td><?php echo htmlspecialchars($row['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="<?php echo $row['status'] == 'active' ? 'status-active' : 'status-locked'; ?>">
                            <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td class="action-buttons">
                            <?php if ($row['username'] == 'admin'): ?>
                                <span class="no-action">不允許更動</span>
                            <?php else: ?>
                                <form method="post" style="display:inline;">
                                    <?php echo getCSRFTokenField(); ?>
                                    <input type="hidden" name="toggle_lock" value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" onclick="return confirm('確定要<?php echo $row['status'] == 'active' ? '鎖定' : '解鎖'; ?>這個使用者嗎？')" style="background:none;border:none;color:blue;text-decoration:none;cursor:pointer;"><?php echo $row['status'] == 'active' ? '鎖定' : '解鎖'; ?></button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <?php echo getCSRFTokenField(); ?>
                                    <input type="hidden" name="delete_user" value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" onclick="return confirm('確定要刪除此使用者嗎？')" style="background:none;border:none;color:blue;text-decoration:none;cursor:pointer;">刪除</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
