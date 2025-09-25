<?php
session_start();
include '../includes/session.php';
include '../config/config.php';

// 獲取統計數據（排除鎖定帳號）
$doctors_count = $conn->query("SELECT COUNT(*) AS count FROM doctors d JOIN users u ON d.user_id = u.id WHERE u.status = 'active'")->fetch_assoc()['count'];
$nurses_count = $conn->query("SELECT COUNT(*) AS count FROM nurses n JOIN users u ON n.user_id = u.id WHERE u.status = 'active'")->fetch_assoc()['count'];
$patients_count = $conn->query("SELECT COUNT(*) AS count FROM patients")->fetch_assoc()['count'];
$total_beds = $conn->query("SELECT COUNT(*) AS count FROM beds")->fetch_assoc()['count'];
$occupied_beds = $conn->query("SELECT COUNT(*) AS count FROM beds WHERE status = 'occupied'")->fetch_assoc()['count'];
$vacant_beds = $conn->query("SELECT COUNT(*) AS count FROM beds WHERE status = 'vacant'")->fetch_assoc()['count'];
$medical_records = $conn->query("SELECT COUNT(*) AS count FROM medical_records")->fetch_assoc()['count'];

$nurse_patient_ratio = $nurses_count > 0 ? round($patients_count / $nurses_count, 2) : 0;

$response = [
    'doctors' => $doctors_count,
    'nurses' => $nurses_count,
    'patients' => $patients_count,
    'total_beds' => $total_beds,
    'occupied_beds' => $occupied_beds,
    'vacant_beds' => $vacant_beds,
    'medical_records' => $medical_records,
    'nurse_patient_ratio' => $nurse_patient_ratio
];

echo json_encode($response);
?>


