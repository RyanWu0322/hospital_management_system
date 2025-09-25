<?php
session_start();
include '../includes/session.php';
include '../config/config.php';
include '../includes/csrf.php';

function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    return rmdir($dir);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCSRFToken();
    $record_id = isset($_POST['record_id']) ? $_POST['record_id'] : '';
    $condition = isset($_POST['condition']) ? $_POST['condition'] : '';
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
    $inpatient = isset($_POST['inpatient']) ? 1 : 0;
    $patient_id = isset($_POST['patient_id']) ? $_POST['patient_id'] : '';
    $nurse_id = isset($_POST['nurse_id']) ? $_POST['nurse_id'] : '';

    if (!empty($record_id) && !empty($condition) && !empty($remarks)) {
        // 處理照片上傳
        $photo = '';
        if (!empty($_FILES['photo']['name'])) {
            $upload_dir = '../uploads/';

            // 檢查檔案大小 (最大 5MB)
            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                echo "<script>alert('照片檔案大小不能超過 5MB');</script>";
                exit();
            }

            // 檢查檔案類型
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime_type, $allowed_types)) {
                echo "<script>alert('只允許上傳 JPEG、PNG 或 GIF 圖片檔案');</script>";
                exit();
            }

            // 生成安全的檔案名稱
            $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $safe_filename = uniqid('medical_edit_', true) . '.' . $extension;
            $photo_path = $upload_dir . $safe_filename;

            // 確保上傳目錄存在
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                // 刪除原有照片
                $stmt_photo = $conn->prepare("SELECT photo FROM medical_records WHERE id = ?");
                $stmt_photo->bind_param('i', $record_id);
                $stmt_photo->execute();
                $result = $stmt_photo->get_result()->fetch_assoc();
                $old_photo = $result['photo'];
                if ($old_photo && file_exists('../' . $old_photo)) {
                    unlink('../' . $old_photo);
                }
                $photo = 'uploads/' . $safe_filename;
            } else {
                echo "<script>alert('照片上傳失敗');</script>";
                exit();
            }
        } else {
            $stmt_photo = $conn->prepare("SELECT photo FROM medical_records WHERE id = ?");
            $stmt_photo->bind_param('i', $record_id);
            $stmt_photo->execute();
            $result = $stmt_photo->get_result()->fetch_assoc();
            $photo = $result['photo'];
        }

        // SQL UPDATE 語句
        $stmt = $conn->prepare("UPDATE medical_records SET `condition` = ?, `remarks` = ?, `inpatient` = ?, `photo` = ?, `nurse_id` = ? WHERE id = ?");
        $stmt->bind_param('ssiisi', $condition, $remarks, $inpatient, $photo, $nurse_id, $record_id);
        
        if ($stmt->execute()) {
            header('Location: medical_records.php');
            exit();
        } else {
            echo "<script>alert('更新失敗');</script>";
        }
    } else {
        echo "<script>alert('所有字段都是必需的。');</script>";
    }
}

$record_id = isset($_GET['record_id']) ? (int)$_GET['record_id'] : 0;
if ($record_id <= 0) {
    echo "<script>alert('無效的病歷ID'); window.location.href='medical_records.php';</script>";
    exit();
}
$stmt = $conn->prepare("SELECT * FROM medical_records WHERE id = ?");
$stmt->bind_param('i', $record_id);
$stmt->execute();
$record = $stmt->get_result()->fetch_assoc();
if (!$record) {
    echo "<script>alert('病歷不存在'); window.location.href='medical_records.php';</script>";
    exit();
}
$nurses = $conn->query("SELECT id, username FROM users WHERE role = 'nurse' AND status = 'active'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>編輯病歷</title>
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
        <h2>編輯病歷</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($record['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($record['patient_id'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="condition">病況：</label>
            <input type="text" name="condition" id="condition" value="<?php echo htmlspecialchars($record['condition'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <label for="remarks">備註：</label>
            <textarea name="remarks" id="remarks" required><?php echo htmlspecialchars($record['remarks'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            <label for="inpatient">是否住院：</label>
            <div class="center">
                <div class="switch_demo">
                    <input type="checkbox" name="inpatient" class="switch_demo-checkbox" id="switch_demo" <?php echo $record['inpatient'] == 1 ? 'checked' : ''; ?>>
                    <label class="switch_demo-label" for="switch_demo">
                        <span class="switch_demo-inner"></span>
                        <span class="switch_demo-switch"></span>
                    </label>
                </div>
            </div>
            <label for="nurse_id">護士：</label>
            <select name="nurse_id" id="nurse_id" required>
                <?php while ($nurse = $nurses->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($nurse['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $record['nurse_id'] == $nurse['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($nurse['username'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endwhile; ?>
            </select>
            <label for="photo">病歷照片：</label>
            <input type="file" name="photo" id="photo">
            <?php echo getCSRFTokenField(); ?>
            <button type="submit">更新</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
