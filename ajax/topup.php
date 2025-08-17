<?php
// ajax/topup.php - API xử lý các request liên quan đến nạp tiền
session_start();

require_once '../config/config.php';
require_once '../models/TopupModel.php';
require_once '../models/UserModel.php';

// Headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Error handler
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'timestamp' => time()
    ]);
    exit;
}

// Success handler
function sendSuccess($message, $data = null) {
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => time()
    ]);
    exit;
}

// Validate user session
function validateUserSession() {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        sendError('Vui lòng đăng nhập', 401);
    }
    return $_SESSION['user_id'];
}

// Validate admin session  
function validateAdminSession() {
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        sendError('Không có quyền truy cập', 403);
    }
    return $_SESSION['admin_id'];
}

// File upload handler
function handleFileUpload($fileKey, $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'], $maxSize = 5242880) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Lỗi upload file'];
    }
    
    $file = $_FILES[$fileKey];
    
    // Validate file type
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Loại file không được hỗ trợ'];
    }
    
    // Validate file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File quá lớn (tối đa 5MB)'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'topup_' . time() . '_' . uniqid() . '.' . $extension;
    $uploadDir = '../uploads/topup/';
    
    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return [
            'success' => true,
            'filename' => $fileName,
            'filepath' => $filePath,
            'url' => '/uploads/topup/' . $fileName
        ];
    } else {
        return ['success' => false, 'message' => 'Không thể lưu file'];
    }
}

// Main request handler
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Chỉ chấp nhận POST request', 405);
}

$action_type = $_POST['action_type'] ?? '';

try {
    switch ($action_type) {
        
        // ===== USER ACTIONS =====
        
        case 'submit_topup_request':
            $user_id = validateUserSession();
            $topup_method_id = $_POST['topup_method_id'] ?? '';
            $amount = $_POST['amount'] ?? 0;
            
            // Validation
            if (empty($topup_method_id)) {
                sendError('Vui lòng chọn phương thức thanh toán');
            }
            
            $amount = floatval($amount);
            if ($amount < 10000) {
                sendError('Số tiền tối thiểu là 10,000đ');
            }
            
            // Validate topup method exists
            $topupMethod = TopupModel::GetOne($topup_method_id);
            if (!$topupMethod) {
                sendError('Phương thức thanh toán không tồn tại');
            }
            
            if (!$topupMethod['status']) {
                sendError('Phương thức thanh toán hiện đang tạm dừng');
            }
            
            // Handle file upload
            $uploadResult = handleFileUpload('proof');
            if (!$uploadResult['success']) {
                sendError($uploadResult['message']);
            }
            
            // Create topup request
            $requestData = [
                'user_id' => $user_id,
                'topup_method_id' => $topup_method_id,
                'amount' => $amount,
                'proof_image' => $uploadResult['url'],
                'status' => 0, // Pending
                'date' => time(),
                'created_at' => date('Y-m-d H:i:s'),
                'note' => $_POST['note'] ?? ''
            ];
            
            $requestId = TopupModel::CreateRequest($requestData);
            
            if ($requestId) {
                // Log activity
                error_log("Topup request created: User $user_id, Amount $amount, Method $topup_method_id");
                
                sendSuccess('Gửi yêu cầu nạp tiền thành công! Vui lòng chờ admin duyệt.', [
                    'request_id' => $requestId,
                    'amount' => $amount,
                    'method' => $topupMethod['name']
                ]);
            } else {
                sendError('Không thể tạo yêu cầu nạp tiền');
            }
            break;
            
        case 'get_user_topup_history':
            $user_id = validateUserSession();
            
            $history = TopupModel::GetUserHistory($user_id);
            
            sendSuccess('Lấy lịch sử thành công', $history);
            break;
            
        case 'get_topup_methods':
            // Get active topup methods for users
            $methods = TopupModel::GetActive();
            
            // Add metadata for each method
            foreach ($methods as &$method) {
                $method['metadata'] = TopupModel::GetMetadata($method['id']);
            }
            
            sendSuccess('Lấy phương thức thanh toán thành công', $methods);
            break;
            
        case 'get_one_topup':
            $id = $_POST['id'] ?? '';
            
            if (empty($id)) {
                sendError('Thiếu ID phương thức');
            }
            
            $topup = TopupModel::GetOne($id);
            if (!$topup) {
                sendError('Không tìm thấy phương thức thanh toán');
            }
            
            // Add metadata
            $topup['metadata'] = TopupModel::GetMetadata($id);
            
            sendSuccess('Lấy thông tin thành công', $topup);
            break;
            
        // ===== ADMIN ACTIONS =====
        
        case 'create_topup':
            validateAdminSession();
            
            $name = trim($_POST['name'] ?? '');
            $display_name = trim($_POST['display_name'] ?? '');
            
            if (empty($name)) {
                sendError('Tên phương thức không được để trống');
            }
            
            // Check if name already exists
            if (TopupModel::CheckNameExists($name)) {
                sendError('Tên phương thức đã tồn tại');
            }
            
            $data = [
                'name' => $name,
                'display_name' => $display_name ?: $name,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $id = TopupModel::Create($data);
            
            if ($id) {
                sendSuccess('Tạo phương thức thanh toán thành công', ['id' => $id]);
            } else {
                sendError('Không thể tạo phương thức thanh toán');
            }
            break;
            
        case 'update_topup_info':
            validateAdminSession();
            
            $topup_id = $_POST['topup_id'] ?? '';
            $name = trim($_POST['name'] ?? '');
            $display_name = trim($_POST['display_name'] ?? '');
            
            if (empty($topup_id) || empty($name)) {
                sendError('Thiếu thông tin bắt buộc');
            }
            
            // Check if topup exists
            $existing = TopupModel::GetOne($topup_id);
            if (!$existing) {
                sendError('Không tìm thấy phương thức thanh toán');
            }
            
            // Check if new name conflicts with other records
            if ($name !== $existing['name'] && TopupModel::CheckNameExists($name, $topup_id)) {
                sendError('Tên phương thức đã được sử dụng');
            }
            
            $data = [
                'name' => $name,
                'display_name' => $display_name ?: $name,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if (TopupModel::Update($topup_id, $data)) {
                sendSuccess('Cập nhật thông tin thành công');
            } else {
                sendError('Không thể cập nhật thông tin');
            }
            break;
            
        case 'delete_topup':
            validateAdminSession();
            
            $topup_id = $_POST['topup_id'] ?? '';
            
            if (empty($topup_id)) {
                sendError('Thiếu ID phương thức');
            }
            
            // Check if topup exists
            $existing = TopupModel::GetOne($topup_id);
            if (!$existing) {
                sendError('Không tìm thấy phương thức thanh toán');
            }
            
            // Check if there are pending requests for this method
            $pendingRequests = TopupModel::CountPendingRequests($topup_id);
            if ($pendingRequests > 0) {
                sendError("Không thể xóa phương thức này vì còn $pendingRequests yêu cầu đang chờ xử lý");
            }
            
            // Delete metadata first
            TopupModel::DeleteAllMetadata($topup_id);
            
            // Delete topup method
            if (TopupModel::Delete($topup_id)) {
                sendSuccess('Xóa phương thức thanh toán thành công');
            } else {
                sendError('Không thể xóa phương thức thanh toán');
            }
            break;
            
        case 'add_topup_meta':
            validateAdminSession();
            
            $topup_id = $_POST['topup_id'] ?? '';
            $meta_key = trim($_POST['meta_key'] ?? '');
            $meta_value = trim($_POST['meta_value'] ?? '');
            
            if (empty($topup_id) || empty($meta_key) || empty($meta_value)) {
                sendError('Thiếu thông tin bắt buộc');
            }
            
            // Check if topup exists
            if (!TopupModel::GetOne($topup_id)) {
                sendError('Không tìm thấy phương thức thanh toán');
            }
            
            // Check if meta key already exists for this topup
            if (TopupModel::CheckMetaKeyExists($topup_id, $meta_key)) {
                sendError('Trường thông tin này đã tồn tại');
            }
            
            $metaData = [
                'topup_id' => $topup_id,
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $metaId = TopupModel::AddMetadata($metaData);
            
            if ($metaId) {
                sendSuccess('Thêm thông tin thành công', ['meta_id' => $metaId]);
            } else {
                sendError('Không thể thêm thông tin');
            }
            break;
            
        case 'update_topup_meta':
            validateAdminSession();
            
            $meta_id = $_POST['meta_id'] ?? '';
            $meta_key = trim($_POST['meta_key'] ?? '');
            $meta_value = trim($_POST['meta_value'] ?? '');
            
            if (empty($meta_id) || empty($meta_key) || empty($meta_value)) {
                sendError('Thiếu thông tin bắt buộc');
            }
            
            // Check if meta exists
            $existing = TopupModel::GetMetadataById($meta_id);
            if (!$existing) {
                sendError('Không tìm thấy thông tin này');
            }
            
            // Check if new key conflicts with other meta in same topup
            if ($meta_key !== $existing['meta_key'] && 
                TopupModel::CheckMetaKeyExists($existing['topup_id'], $meta_key)) {
                sendError('Trường thông tin này đã tồn tại');
            }
            
            $data = [
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if (TopupModel::UpdateMetadata($meta_id, $data)) {
                sendSuccess('Cập nhật thông tin thành công');
            } else {
                sendError('Không thể cập nhật thông tin');
            }
            break;
            
        case 'delete_topup_meta':
            validateAdminSession();
            
            $meta_id = $_POST['meta_id'] ?? '';
            
            if (empty($meta_id)) {
                sendError('Thiếu ID thông tin');
            }
            
            // Check if meta exists
            if (!TopupModel::GetMetadataById($meta_id)) {
                sendError('Không tìm thấy thông tin này');
            }
            
            if (TopupModel::DeleteMetadata($meta_id)) {
                sendSuccess('Xóa thông tin thành công');
            } else {
                sendError('Không thể xóa thông tin');
            }
            break;
            
        case 'toggle_topup_status':
            validateAdminSession();
            
            $topup_id = $_POST['topup_id'] ?? '';
            
            if (empty($topup_id)) {
                sendError('Thiếu ID phương thức');
            }
            
            $existing = TopupModel::GetOne($topup_id);
            if (!$existing) {
                sendError('Không tìm thấy phương thức thanh toán');
            }
            
            $newStatus = $existing['status'] ? 0 : 1;
            
            if (TopupModel::Update($topup_id, ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')])) {
                $statusText = $newStatus ? 'kích hoạt' : 'tạm dừng';
                sendSuccess("Đã $statusText phương thức thanh toán", ['new_status' => $newStatus]);
            } else {
                sendError('Không thể thay đổi trạng thái');
            }
            break;
            
        case 'get_admin_topup_requests':
            validateAdminSession();
            
            $page = intval($_POST['page'] ?? 1);
            $limit = intval($_POST['limit'] ?? 20);
            $status = $_POST['status'] ?? 'all';
            
            $requests = TopupModel::GetAdminRequests($page, $limit, $status);
            $total = TopupModel::CountAdminRequests($status);
            
            sendSuccess('Lấy danh sách yêu cầu thành công', [
                'requests' => $requests,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]);
            break;
            
        case 'approve_topup_request':
            validateAdminSession();
            
            $request_id = $_POST['request_id'] ?? '';
            $admin_note = trim($_POST['admin_note'] ?? '');
            
            if (empty($request_id)) {
                sendError('Thiếu ID yêu cầu');
            }
            
            $request = TopupModel::GetRequest($request_id);
            if (!$request) {
                sendError('Không tìm thấy yêu cầu nạp tiền');
            }
            
            if ($request['status'] != 0) {
                sendError('Yêu cầu này đã được xử lý');
            }
            
            // Begin transaction
            TopupModel::beginTransaction();
            
            try {
                // Update request status
                $updateData = [
                    'status' => 1,
                    'admin_note' => $admin_note,
                    'processed_at' => date('Y-m-d H:i:s'),
                    'processed_by' => $_SESSION['admin_id']
                ];
                
                if (!TopupModel::UpdateRequest($request_id, $updateData)) {
                    throw new Exception('Không thể cập nhật trạng thái yêu cầu');
                }
                
                // Add money to user account
                $user = UserModel::GetOne($request['user_id']);
                if (!$user) {
                    throw new Exception('Không tìm thấy thông tin người dùng');
                }
                
                $newBalance = $user['money'] + $request['amount'];
                if (!UserModel::UpdateBalance($request['user_id'], $newBalance)) {
                    throw new Exception('Không thể cập nhật số dư người dùng');
                }
                
                // Create transaction log
                TopupModel::LogTransaction([
                    'user_id' => $request['user_id'],
                    'type' => 'topup',
                    'amount' => $request['amount'],
                    'balance_before' => $user['money'],
                    'balance_after' => $newBalance,
                    'reference_id' => $request_id,
                    'description' => "Nạp tiền qua {$request['method_name']}",
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                // Send notification to user
                TopupModel::SendNotification($request['user_id'], [
                    'title' => 'Nạp tiền thành công',
                    'content' => "Yêu cầu nạp tiền {$request['amount']}đ đã được duyệt thành công.",
                    'type' => 'topup_approved'
                ]);
                
                TopupModel::commit();
                
                sendSuccess('Duyệt yêu cầu nạp tiền thành công', [
                    'request_id' => $request_id,
                    'amount' => $request['amount'],
                    'new_balance' => $newBalance
                ]);
                
            } catch (Exception $e) {
                TopupModel::rollback();
                sendError('Lỗi xử lý: ' . $e->getMessage());
            }
            break;
            
        case 'reject_topup_request':
            validateAdminSession();
            
            $request_id = $_POST['request_id'] ?? '';
            $admin_note = trim($_POST['admin_note'] ?? '');
            
            if (empty($request_id)) {
                sendError('Thiếu ID yêu cầu');
            }
            
            if (empty($admin_note)) {
                sendError('Vui lòng nhập lý do từ chối');
            }
            
            $request = TopupModel::GetRequest($request_id);
            if (!$request) {
                sendError('Không tìm thấy yêu cầu nạp tiền');
            }
            
            if ($request['status'] != 0) {
                sendError('Yêu cầu này đã được xử lý');
            }
            
            $updateData = [
                'status' => 2,
                'admin_note' => $admin_note,
                'processed_at' => date('Y-m-d H:i:s'),
                'processed_by' => $_SESSION['admin_id']
            ];
            
            if (TopupModel::UpdateRequest($request_id, $updateData)) {
                // Send notification to user
                TopupModel::SendNotification($request['user_id'], [
                    'title' => 'Yêu cầu nạp tiền bị từ chối',
                    'content' => "Yêu cầu nạp tiền {$request['amount']}đ đã bị từ chối. Lý do: $admin_note",
                    'type' => 'topup_rejected'
                ]);
                
                sendSuccess('Từ chối yêu cầu nạp tiền thành công');
            } else {
                sendError('Không thể từ chối yêu cầu');
            }
            break;
            
        default:
            sendError('Action không được hỗ trợ', 400);
            break;
    }
    
} catch (Exception $e) {
    error_log("Topup API Error: " . $e->getMessage());
    sendError('Có lỗi xảy ra: ' . $e->getMessage(), 500);
}

// Enhanced TopupModel methods (add these to your TopupModel.php)
class TopupModelExtended extends TopupModel {
    
    public static function GetActive() {
        $sql = "SELECT * FROM topup_methods WHERE status = 1 ORDER BY name ASC";
        return Database::query($sql);
    }
    
    public static function GetMetadata($topup_id) {
        $sql = "SELECT * FROM topup_metadata WHERE topup_id = ? ORDER BY meta_key ASC";
        return Database::query($sql, [$topup_id]);
    }
    
    public static function CheckNameExists($name, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM topup_methods WHERE name = ?";
        $params = [$name];
        
        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $result = Database::queryOne($sql, $params);
        return $result['count'] > 0;
    }
    
    public static function CreateRequest($data) {
        $sql = "INSERT INTO topup_requests (user_id, topup_method_id, amount, proof_image, status, date, created_at, note) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        return Database::insert($sql, [
            $data['user_id'],
            $data['topup_method_id'], 
            $data['amount'],
            $data['proof_image'],
            $data['status'],
            $data['date'],
            $data['created_at'],
            $data['note']
        ]);
    }
    
    public static function GetUserHistory($user_id, $limit = 50) {
        $sql = "SELECT tr.*, tm.name as method_name, tm.display_name 
                FROM topup_requests tr 
                LEFT JOIN topup_methods tm ON tr.topup_method_id = tm.id 
                WHERE tr.user_id = ? 
                ORDER BY tr.created_at DESC 
                LIMIT ?";
        
        return Database::query($sql, [$user_id, $limit]);
    }
    
    public static function CountPendingRequests($topup_method_id) {
        $sql = "SELECT COUNT(*) as count FROM topup_requests WHERE topup_method_id = ? AND status = 0";
        $result = Database::queryOne($sql, [$topup_method_id]);
        return $result['count'];
    }
    
    public static function DeleteAllMetadata($topup_id) {
        $sql = "DELETE FROM topup_metadata WHERE topup_id = ?";
        return Database::execute($sql, [$topup_id]);
    }
    
    public static function CheckMetaKeyExists($topup_id, $meta_key, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM topup_metadata WHERE topup_id = ? AND meta_key = ?";
        $params = [$topup_id, $meta_key];
        
        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $result = Database::queryOne($sql, $params);
        return $result['count'] > 0;
    }
    
    public static function AddMetadata($data) {
        $sql = "INSERT INTO topup_metadata (topup_id, meta_key, meta_value, created_at) VALUES (?, ?, ?, ?)";
        return Database::insert($sql, [
            $data['topup_id'],
            $data['meta_key'],
            $data['meta_value'], 
            $data['created_at']
        ]);
    }
    
    public static function GetMetadataById($id) {
        $sql = "SELECT * FROM topup_metadata WHERE id = ?";
        return Database::queryOne($sql, [$id]);
    }
    
    public static function UpdateMetadata($id, $data) {
        $sql = "UPDATE topup_metadata SET meta_key = ?, meta_value = ?, updated_at = ? WHERE id = ?";
        return Database::execute($sql, [
            $data['meta_key'],
            $data['meta_value'],
            $data['updated_at'],
            $id
        ]);
    }
    
    public static function DeleteMetadata($id) {
        $sql = "DELETE FROM topup_metadata WHERE id = ?";
        return Database::execute($sql, [$id]);
    }
    
    public static function GetAdminRequests($page = 1, $limit = 20, $status = 'all') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT tr.*, tm.name as method_name, tm.display_name, u.username, u.email
                FROM topup_requests tr 
                LEFT JOIN topup_methods tm ON tr.topup_method_id = tm.id 
                LEFT JOIN users u ON tr.user_id = u.id";
        
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " WHERE tr.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY tr.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return Database::query($sql, $params);
    }
    
    public static function CountAdminRequests($status = 'all') {
        $sql = "SELECT COUNT(*) as count FROM topup_requests";
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        
        $result = Database::queryOne($sql, $params);
        return $result['count'];
    }
    
    public static function GetRequest($id) {
        $sql = "SELECT tr.*, tm.name as method_name, tm.display_name, u.username, u.email
                FROM topup_requests tr 
                LEFT JOIN topup_methods tm ON tr.topup_method_id = tm.id 
                LEFT JOIN users u ON tr.user_id = u.id 
                WHERE tr.id = ?";
        
        return Database::queryOne($sql, [$id]);
    }
    
    public static function UpdateRequest($id, $data) {
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE topup_requests SET " . implode(', ', $fields) . " WHERE id = ?";
        return Database::execute($sql, $params);
    }
    
    public static function LogTransaction($data) {
        $sql = "INSERT INTO transaction_logs (user_id, type, amount, balance_before, balance_after, reference_id, description, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        return Database::insert($sql, [
            $data['user_id'],
            $data['type'],
            $data['amount'],
            $data['balance_before'],
            $data['balance_after'],
            $data['reference_id'],
            $data['description'],
            $data['created_at']
        ]);
    }
    
    public static function SendNotification($user_id, $data) {
        $sql = "INSERT INTO notifications (user_id, title, content, type, created_at) VALUES (?, ?, ?, ?, ?)";
        
        return Database::insert($sql, [
            $user_id,
            $data['title'],
            $data['content'],
            $data['type'],
            date('Y-m-d H:i:s')
        ]);
    }
    
    // Database transaction methods
    public static function beginTransaction() {
        return Database::beginTransaction();
    }
    
    public static function commit() {
        return Database::commit();
    }
    
    public static function rollback() {
        return Database::rollback();
    }
}
?>