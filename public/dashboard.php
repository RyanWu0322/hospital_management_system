<?php
session_start();
include '../includes/session.php';
include '../config/config.php';

// 獲取醫生總人數
$doctor_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'doctor' AND status = 'active'")->fetch_assoc()['count'];

// 獲取護士總人數
$nurse_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'nurse' AND status = 'active'")->fetch_assoc()['count'];

// 獲取病床總床數
$bed_total = $conn->query("SELECT COUNT(*) as count FROM beds")->fetch_assoc()['count'];

// 獲取需要住院的患者數量
$inpatient_count = $conn->query("SELECT COUNT(*) as count FROM medical_records WHERE inpatient = 1")->fetch_assoc()['count'];

// 計算使用中的病床數
$bed_occupied = $inpatient_count;

// 計算未使用病床數
$bed_vacant = $bed_total - $bed_occupied;

// 獲取病歷數統計
$record_count = $conn->query("SELECT COUNT(*) as count FROM medical_records")->fetch_assoc()['count'];

// 計算護病比
$nurse_to_patient_ratio = $nurse_count . ':' . ($inpatient_count > 0 ? $inpatient_count : 1);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
    <style>
        .dashboard-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .dashboard-header h2 {
            font-size: 2.5em;
            color: #333;
        }
        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }
        .dashboard-card {
            flex: 1;
            min-width: 200px;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .dashboard-card h3 {
            margin-bottom: 15px;
            font-size: 1.5em;
            color: #555;
        }
        .dashboard-card p {
            font-size: 1.2em;
            color: #333;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Dashboard</h2>
        </div>
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <h3>醫生總人數</h3>
                <p><?php echo $doctor_count; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>護士總人數</h3>
                <p><?php echo $nurse_count; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>病床總床數</h3>
                <p><?php echo $bed_total; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>使用中病床</h3>
                <p><?php echo $bed_occupied; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>未使用病床</h3>
                <p><?php echo $bed_vacant; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>病例數統計</h3>
                <p><?php echo $record_count; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>護病比</h3>
                <p><?php echo $nurse_to_patient_ratio; ?></p>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>


</html>
