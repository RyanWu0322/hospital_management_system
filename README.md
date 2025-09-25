# 醫護管理系統 (Hospital Management System)

## 專案概述

這是一個基於PHP和MySQL開發的簡易醫護管理系統，原始作業來自2024年4月9日「網路程式設計」課程作業，經過全面的安全性改進和功能增強。

### 課程資訊
- **課程名稱**: 網路程式設計
- **作業日期**: 2024-04-09
- **改進時間**: 2025年02月-2025年03月
- **主要改進**: 安全性強化、錯誤處理、代碼品質提升

## 系統功能

### 🏥 核心功能
- **使用者管理**: 醫生、護士、管理員帳戶管理
- **病歷管理**: 新增、編輯、查看醫療記錄
- **病床管理**: 床位數量控制和狀態管理
- **個人資料管理**: 使用者可更新個人資訊
- **管理員面板**: 帳戶控制、使用者狀態管理

### 👤 使用者角色
1. **管理員 (Admin)**
   - 管理所有使用者帳戶
   - 鎖定/解鎖使用者
   - 刪除使用者（除admin外）
   - 新增醫生和護士帳戶
   - 病床數量管理

2. **醫生 (Doctor)**
   - 新增病歷記錄
   - 編輯病歷資訊
   - 上傳醫療照片
   - 查看醫療記錄

3. **護士 (Nurse)**
   - 協助醫療記錄管理
   - 查看指派的病歷

## 技術架構

### 🛠️ 開發環境
- **後端語言**: PHP 7.4+
- **資料庫**: MySQL 5.7+
- **前端**: HTML5, CSS3, JavaScript
- **伺服器**: Apache/Nginx

### 📁 目錄結構
```
hospital_management_system/
├── admin/                    # 管理員功能模組
│   ├── account_management.php   # 帳戶管理
│   ├── add_user.php             # 新增使用者
│   ├── manage_beds.php          # 病床管理
│   └── verify_admin.php         # 管理員驗證
├── config/                   # 配置文件
│   └── config.php               # 資料庫配置
├── includes/                 # 共用模組
│   ├── session.php              # 會話管理
│   ├── header.php               # 頁面標頭
│   ├── footer.php               # 頁面底部
│   ├── csrf.php                 # CSRF保護
│   ├── validation.php           # 輸入驗證
│   └── error_handler.php        # 錯誤處理
├── public/                   # 公開頁面
│   ├── login.php                # 登入頁面
│   ├── dashboard.php            # 儀表板
│   ├── profile.php              # 個人資料
│   ├── medical_records.php      # 病歷管理
│   ├── edit_medical_record.php  # 編輯病歷
│   └── css/                     # 樣式文件
├── uploads/                  # 文件上傳目錄
├── logs/                     # 日誌文件目錄
└── README.md                 # 專案文檔
```

## 安全性改進

### 🔒 主要安全增強

#### 1. SQL注入防護
- **問題**: 原始代碼使用字符串拼接進行SQL查詢
- **解決**: 全面改用預處理語句 (Prepared Statements)
```php
// 修復前 (存在SQL注入風險)
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// 修復後 (安全)
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
```

#### 2. XSS攻擊防護
- **問題**: 用戶輸入直接輸出到HTML中
- **解決**: 所有輸出都經過HTML轉義
```php
// 修復前 (存在XSS風險)
<td><?php echo $row['username']; ?></td>

// 修復後 (安全)
<td><?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td>
```

#### 3. CSRF攻擊防護
- **新增功能**: 實現完整的CSRF保護系統
- **令牌驗證**: 所有表單都包含CSRF令牌
- **狀態變更保護**: 危險操作改用POST請求

#### 4. 文件上傳安全
- **文件類型驗證**: 只允許圖片文件 (JPEG, PNG, GIF)
- **文件大小限制**: 最大5MB
- **安全文件命名**: 使用唯一ID生成文件名
- **MIME類型檢測**: 使用真實文件內容檢測類型

#### 5. 會話管理安全
- **會話劫持保護**: IP和User-Agent檢查
- **自動超時**: 30分鐘無活動自動登出
- **會話ID輪換**: 定期更新會話ID
- **安全設置**: HttpOnly, SameSite cookies

## 安裝與設置

### 🚀 環境需求
- PHP 7.4 或更高版本
- MySQL 5.7 或更高版本
- Apache/Nginx 網頁伺服器
- PHP擴展: mysqli, fileinfo, session

### 📥 安裝步驟

1. **下載專案**
```bash
git clone https://github.com/your-repo/hospital_management_system.git
cd hospital_management_system
```

2. **資料庫設置**
```sql
-- 創建資料庫
CREATE DATABASE hospital_management_system_db;

-- 創建使用者表
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'doctor', 'nurse') NOT NULL,
    specialty VARCHAR(100),
    status ENUM('active', 'locked') DEFAULT 'active',
    failed_attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 創建病歷表
CREATE TABLE medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    nurse_id INT NOT NULL,
    condition VARCHAR(255) NOT NULL,
    remarks TEXT NOT NULL,
    record_date DATE NOT NULL,
    inpatient TINYINT(1) DEFAULT 0,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(id),
    FOREIGN KEY (nurse_id) REFERENCES users(id)
);

-- 創建病床表
CREATE TABLE beds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bed_number INT NOT NULL,
    status ENUM('occupied', 'vacant') DEFAULT 'vacant'
);
```

3. **配置文件**
- 編輯 `config/config.php` 設置資料庫連接資訊
- 確保 `uploads/` 和 `logs/` 目錄有寫入權限

4. **預設帳戶**
- **管理員帳戶**:
  - 使用者名稱: `admin`
  - 密碼: `admin`

## 使用方式

### 🔐 登入系統
1. 開啟瀏覽器前往 `http://your-domain/public/login.php`
2. 使用預設管理員帳戶登入
3. 首次登入後建議立即修改管理員密碼

### 👨‍⚕️ 管理員操作
1. **新增使用者**: 進入管理面板新增醫生或護士帳戶
2. **帳戶管理**: 鎖定/解鎖使用者帳戶
3. **病床管理**: 設定病床總數

### 🩺 醫療記錄管理
1. 醫生可新增病歷記錄
2. 上傳相關醫療照片
3. 編輯和更新病歷資訊

## 安全特性

### 🛡️ 安全機制

1. **輸入驗證**
   - 用戶名格式檢查
   - 密碼強度驗證
   - 數據類型驗證

2. **輸出編碼**
   - HTML內容轉義
   - URL參數編碼

3. **存取控制**
   - 角色基礎權限管理
   - 會話狀態驗證

4. **日誌記錄**
   - 安全事件記錄
   - 錯誤日誌追蹤

### 📊 安全日誌
系統會自動記錄以下事件：
- 登入嘗試
- 權限變更
- 文件上傳
- 系統錯誤

日誌文件位置：`logs/error.log`, `logs/security.log`

## 開發歷程

### 📝 版本歷史

#### v2.0 (2024-2025) - 安全增強版
- ✅ 修復所有SQL注入漏洞
- ✅ 實現XSS防護
- ✅ 新增CSRF保護
- ✅ 強化文件上傳安全
- ✅ 改進會話管理
- ✅ 新增錯誤處理系統
- ✅ 實現輸入驗證
- ✅ 添加安全日誌

#### v1.0 (2024-04-09) - 原始作業版
- 基本功能實現
- 用戶管理系統
- 病歷管理功能
- 簡單的權限控制

### 🎓 學習成果

這個專案展示了從基礎網路程式設計到企業級安全開發的演進：

1. **基礎程式設計** (原始作業)
   - PHP基本語法
   - MySQL資料庫操作
   - HTML/CSS介面設計

2. **安全程式設計** (改進版)
   - Web應用安全最佳實踐
   - OWASP Top 10 漏洞防護
   - 安全程式設計模式

## 貢獻與維護

### 🤝 如何貢獻
1. Fork 此專案
2. 創建功能分支
3. 提交更改
4. 發起 Pull Request

### 🚨 問題回報
如發現bug或安全問題，請通過以下方式回報：
- GitHub Issues
- 電子郵件聯繫

## 授權條款

此專案採用 MIT 授權條款，詳見 LICENSE 文件。

## 聯繫資訊

- **開發者**: [您的姓名]
- **課程**: 網路程式設計 (2024)
- **專案類型**: 學術作業改進

---

**注意**: 此系統僅供學習和展示用途，若要用於生產環境，請進行進一步的安全檢測和效能優化。