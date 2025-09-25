<?php
session_start();
include '../includes/session.php';
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_SESSION['user_id']; // Assuming the logged-in user is the doctor
    $nurse_id = $_POST['nurse_id'];
    $condition = $_POST['condition'];
    $remarks = $_POST['remarks'];
    $inpatient = isset($_POST['inpatient']) ? 1 : 0;
    $record_date = date('Y-m-d');
    $photo = '';

    // 處理照片上傳
    if (!empty($_FILES['photo']['name'])) {
        $photo = 'uploads/' . basename($_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], '../' . $photo)) {
            $photo_uploaded = true;
        } else {
            $photo_uploaded = false;
            echo "<script>alert('照片上傳失敗');</script>";
        }
    }

    $stmt = $conn->prepare("INSERT INTO medical_records (patient_id, doctor_id, nurse_id, `condition`, remarks, record_date, inpatient, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiisssis', $patient_id, $doctor_id, $nurse_id, $condition, $remarks, $record_date, $inpatient, $photo);

    if ($stmt->execute()) {
        echo "<script>alert('病歷已新增');</script>";
    } else {
        echo "<script>alert('新增失敗');</script>";
    }
}

// 獲取護士名單（排除鎖定帳號）
$nurses = $conn->query("SELECT id, username FROM users WHERE role = 'nurse' AND status = 'active'");

// 獲取病歷（排除鎖定帳號）
$medical_records_result = $conn->query("SELECT mr.id, mr.patient_id, mr.doctor_id, mr.nurse_id, mr.condition, mr.record_date, mr.inpatient, mr.photo, 
                                        d.username AS doctor_name, n.username AS nurse_name 
                                        FROM medical_records mr 
                                        JOIN users d ON mr.doctor_id = d.id 
                                        JOIN users n ON mr.nurse_id = n.id 
                                        WHERE d.status = 'active' AND n.status = 'active'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>病歷管理系統</title>
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
    <style>
        .switch_demo {
            position: relative;
            width: 150px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        .switch_demo-checkbox {
            display: none;
        }
        .switch_demo-label {
            display: block;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid #999999;
            border-radius: 20px;
        }
        .switch_demo-inner {
            display: block;
            width: 200%;
            margin-left: -100%;
            transition: margin 0.3s ease-in 0s;
        }
        .switch_demo-inner:before,
        .switch_demo-inner:after {
            display: block;
            float: left;
            width: 50%;
            height: 30px;
            padding: 0;
            line-height: 30px;
            font-size: 14px;
            color: white;
            font-family: Trebuchet, Arial, sans-serif;
            font-weight: bold;
            box-sizing: border-box;
        }
        .switch_demo-inner:before {
            content: "是";
            padding-left: 10px;
            background-color: #34A7C1;
            color: #FFFFFF;
        }
        .switch_demo-inner:after {
            content: "否";
            padding-right: 10px;
            background-color: #EEEEEE;
            color: #999999;
            text-align: right;
        }
        .switch_demo-switch {
            display: block;
            width: 18px;
            margin: 6px;
            background: #FFFFFF;
            position: absolute;
            top: 0;
            bottom: 0;
            right: 116px;
            border: 2px solid #999999;
            border-radius: 20px;
            transition: all 0.3s ease-in 0s;
        }
        .switch_demo-checkbox:checked + .switch_demo-label .switch_demo-inner {
            margin-left: 0;
        }
        .switch_demo-checkbox:checked + .switch_demo-label .switch_demo-switch {
            right: 0px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h2>病歷管理系統</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="patient_id">病人ID：</label>
            <input type="text" name="patient_id" id="patient_id" required>
            <label for="nurse_id">護士：</label>
            <select name="nurse_id" id="nurse_id" required>
                <?php while ($nurse = $nurses->fetch_assoc()): ?>
                    <option value="<?php echo $nurse['id']; ?>"><?php echo $nurse['username']; ?></option>
                <?php endwhile; ?>
            </select>
            <label for="condition">病況：</label>
            <select name="condition" id="condition">
                <option value="骨折">骨折</option>
                <option value="牙痛">牙痛</option>
                <option value="需要手術">需要手術</option>
                <option value="急診">急診</option>
                <option value="心臟驟停">心臟驟停</option>
                <option value="發燒">發燒</option>
                <option value="咳嗽">咳嗽</option>
                <option value="頭痛">頭痛</option>
                <option value="胃痛">胃痛</option>
                <option value="皮疹">皮疹</option>
                <option value="其他">其他</option>
                <option value="高血壓">高血壓</option>
                <option value="糖尿病">糖尿病</option>
                <option value="哮喘">哮喘</option>
                <option value="過敏">過敏</option>
                <option value="貧血">貧血</option>
                <option value="腦中風">腦中風</option>
                <option value="關節炎">關節炎</option>
                <option value="癌症">癌症</option>
                <option value="抑鬱症">抑鬱症</option>
                <option value="焦慮症">焦慮症</option>
                <option value="肺炎">肺炎</option>
                <option value="尿路感染">尿路感染</option>
                <option value="腎結石">腎結石</option>
                <option value="肝炎">肝炎</option>
                <option value="甲狀腺問題">甲狀腺問題</option>
                <option value="心臟病">心臟病</option>
                <option value="中耳炎">中耳炎</option>
                <option value="視力模糊">視力模糊</option>
                <option value="聽力損失">聽力損失</option>
                <option value="骨質疏鬆">骨質疏鬆</option>
                <option value="中暑">中暑</option>
                <option value="脫水">脫水</option>
                <option value="食物中毒">食物中毒</option>
                <option value="腸胃炎">腸胃炎</option>
                <option value="低血糖">低血糖</option>
            </select>
            <label for="remarks">備註：</label>
            <textarea name="remarks" id="remarks" required></textarea>
            <label for="inpatient">是否住院：</label>
            <div class="center">
                <div class="switch_demo">
                    <input type="checkbox" name="inpatient" class="switch_demo-checkbox" id="switch_demo">
                    <label class="switch_demo-label" for="switch_demo">
                        <span class="switch_demo-inner"></span>
                        <span class="switch_demo-switch"></span>
                    </label>
                </div>
            </div>
            <label for="photo">病歷照片：</label>
            <input type="file" name="photo" id="photo">
            <button type="submit">新增病歷</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>病歷ID</th>
                    <th>病人ID</th>
                    <th>醫生ID</th>
                    <th>護士ID</th>
                    <th>病況</th>
                    <th>日期</th>
                    <th>是否住院</th>
                    <th>照片</th>
                    <th>動作</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $medical_records_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['patient_id']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td><?php echo $row['nurse_name']; ?></td>
                        <td><?php echo $row['condition']; ?></td>
                        <td><?php echo $row['record_date']; ?></td>
                        <td><?php echo $row['inpatient'] == 1 ? '是' : '否'; ?></td>
                        <td><img src="../<?php echo $row['photo']; ?>" alt="病歷照片" width="100"></td>
                        <td>
                            <a href="edit_medical_record.php?record_id=<?php echo $row['id']; ?>">編輯</a>
                            <a href="delete_medical_record.php?record_id=<?php echo $row['id']; ?>&patient_id=<?php echo $row['patient_id']; ?>" onclick="return confirm('確定要刪除這個病歷嗎？')">刪除</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
