<?php
// ajax/refresh_balance.php
session_start();
require_once '../config/config.php';
include_once __DIR__ . "/../models/UserModel.php";

header('Content-Type: application/json');

if (empty($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$uid = $_SESSION['id'];
$user = UserModel::GetOne($uid);

if ($user) {
    echo json_encode([
        'success' => true,
        'balance' => $user['money'],
        'credit' => $user['credit'] ?? 0
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>

---

<?php
// ajax/bind_card.php
session_start();
require_once '../config/config.php';
include_once __DIR__ . "/../models/BankModel.php";

header('Content-Type: application/json');

if (empty($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$uid = $_SESSION['id'];
$bankinfo = trim($_POST['bankinfo'] ?? '');
$bankid = trim($_POST['bankid'] ?? '');
$name = trim($_POST['name'] ?? '');

// Validate input
if (empty($bankinfo) || empty($bankid) || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
    exit;
}

// Check if bank account already exists
$existingBank = BankModel::GetFromUID($uid);
if ($existingBank) {
    echo json_encode(['success' => false, 'message' => 'Bạn đã có tài khoản ngân hàng được liên kết']);
    exit;
}

try {
    $bankData = [
        'uid' => $uid,
        'bankinfo' => $bankinfo,
        'bankid' => $bankid,
        'name' => $name,
        'create_time' => time()
    ];
    
    $result = BankModel::Create($bankData);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Liên kết tài khoản ngân hàng thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể liên kết tài khoản ngân hàng']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>

---

<?php
// ajax/change_password.php
session_start();
require_once '../config/config.php';
include_once __DIR__ . "/../models/UserModel.php";

header('Content-Type: application/json');

if (empty($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$uid = $_SESSION['id'];
$oldPass = $input['old_pass'] ?? '';
$newPass = $input['new_pass'] ?? '';

// Validate input
if (empty($oldPass) || empty($newPass)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
    exit;
}

if (strlen($newPass) < 6) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự']);
    exit;
}

// Get user info
$user = UserModel::GetOne($uid);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Verify old password
if (!password_verify($oldPass, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu cũ không chính xác']);
    exit;
}

try {
    $hashedNewPass = password_hash($newPass, PASSWORD_DEFAULT);
    $result = UserModel::UpdatePassword($uid, $hashedNewPass);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể đổi mật khẩu']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>

---

<?php
// ajax/update_profile.php
session_start();
require_once '../config/config.php';
include_once __DIR__ . "/../models/UserModel.php";

header('Content-Type: application/json');

if (empty($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$uid = $_SESSION['id'];
$field = $input['field'] ?? '';
$value = $input['value'] ?? '';

// Validate allowed fields
$allowedFields = ['name', 'phone', 'gender'];
if (!in_array($field, $allowedFields)) {
    echo json_encode(['success' => false, 'message' => 'Invalid field']);
    exit;
}

// Validate gender field
if ($field === 'gender' && !in_array($value, ['0', '1', '2'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid gender value']);
    exit;
}

// Validate phone field
if ($field === 'phone' && !empty($value) && !preg_match('/^[0-9+\-\s\(\)]{10,15}$/', $value)) {
    echo json_encode(['success' => false, 'message' => 'Số điện thoại không hợp lệ']);
    exit;
}

try {
    $result = UserModel::UpdateField($uid, $field, $value);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật thông tin']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>

---

<?php
// ajax/logout.php
session_start();

header('Content-Type: application/json');

// Destroy session
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>

---

<?php
// ajax/upload_avatar.php
session_start();
require_once '../config/config.php';
include_once __DIR__ . "/../models/UserModel.php";

header('Content-Type: application/json');

if (empty($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$uid = $_SESSION['id'];
$file = $_FILES['avatar'];

// Validate file type
$allowedTypes = ['image/jpeg', 'image/png'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận file JPG và PNG']);
    exit;
}

// Validate file size (2MB max)
if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File quá lớn (tối đa 2MB)']);
    exit;
}

try {
    // Create upload directory if it doesn't exist
    $uploadDir = __DIR__ . '/../uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $uid . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Update user avatar in database
        $avatarUrl = '/uploads/avatars/' . $filename;
        $result = UserModel::UpdateAvatar($uid, $avatarUrl);
        
        if ($result) {
            echo json_encode([
                'success' => true, 
                'message' => 'Upload avatar thành công',
                'url' => $avatarUrl
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật avatar']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể lưu file']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>