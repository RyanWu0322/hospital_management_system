<?php
session_start();
include '../includes/session.php';
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $total_beds = $_POST['total_beds'];
    // 假設這裡有邏輯來更新病床數量
    $conn->query("UPDATE beds SET bed_number = $total_beds");
    echo "<script>alert('病床數量已更新');</script>";
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
            <button type="submit">更新</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
