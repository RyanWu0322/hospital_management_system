<?php
session_start();
include '../includes/session.php';
include '../config/config.php';

// 處理鎖定/解鎖使用者請求
if (isset($_GET['toggle_lock'])) {
    $user_id = $_GET['toggle_lock'];
    $user_status = $_GET['status'];
    $new_status = $user_status == 'active' ? 'locked' : 'active';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $new_status, $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('使用者狀態已更新');</script>";
    } else {
        echo "<script>alert('更新失敗');</script>";
    }
}

// 處理刪除使用者請求
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('使用者已刪除');</script>";
    } else {
        echo "<script>alert('刪除失敗');</script>";
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
                        <td><?php echo $row['username']; ?></td>
                        <td>******</td>
                        <td><?php echo $row['role']; ?></td>
                        <td class="<?php echo $row['status'] == 'active' ? 'status-active' : 'status-locked'; ?>">
                            <?php echo $row['status']; ?>
                        </td>
                        <td class="action-buttons">
                            <?php if ($row['username'] == 'admin'): ?>
                                <span class="no-action">不允許更動</span>
                            <?php else: ?>
                                <a href="account_management.php?toggle_lock=<?php echo $row['id']; ?>&status=<?php echo $row['status']; ?>" onclick="return confirm('確定要<?php echo $row['status'] == 'active' ? '鎖定' : '解鎖'; ?>這個使用者嗎？')"><?php echo $row['status'] == 'active' ? '鎖定' : '解鎖'; ?></a>
                                <a href="account_management.php?delete_user=<?php echo $row['id']; ?>" onclick="return confirm('確定要刪除此使用者嗎？')">刪除</a>
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
