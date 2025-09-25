<?php
session_start();
include '../includes/session.php';
include '../config/config.php';
include '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCSRFToken();
    $total_beds = isset($_POST['total_beds']) ? (int)$_POST['total_beds'] : 0;

    if ($total_beds <= 0) {
        echo "<script>alert('請輸入有效的病床數量');</script>";
    } else {
        $stmt = $conn->prepare("UPDATE beds SET bed_number = ?");
        $stmt->bind_param('i', $total_beds);

        if ($stmt->execute()) {
            echo "<script>alert('病床數量已更新');</script>";
        } else {
            echo "<script>alert('更新失敗');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>管理病床</title>
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h2>管理病床</h2>
        <form method="post" action="">
            <label for="total_beds">病床總數</label>
            <input type="number" name="total_beds" id="total_beds" required><br>
            <?php echo getCSRFTokenField(); ?>
            <button type="submit">更新</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
