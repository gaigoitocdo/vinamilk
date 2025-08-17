<?php
// ajax/enhanced-payment-management.php - Hệ thống quản lý thanh toán tích hợp
session_start();
require_once '../config/config.php';
require_once '../models/TopupModel.php';
require_once '../models/WithdrawModel.php';
require_once '../models/UserModel.php';
require_once '../models/NotiModel.php';
require_once '../models/BankModel.php';

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Helper functions
function sendResponse($success, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

function validateSession($type = 'admin') {
    if ($type === 'admin') {
        if (!isset($_SESSION['admin_id'])) {
            sendResponse(false, 'Unauthorized access', null, 401);
        }
        return $_SESSION['admin_id'];
    } else {
        if (!isset($_SESSION['id'])) {
            sendResponse(false, 'User not logged in', null, 401);
        }
        return $_SESSION['id'];
    }
}

function logActivity($adminId, $action, $details = '') {
    $db = Database::getInstance();
    $db->pdo_execute("INSERT INTO admin_logs (admin_id, action, details, created_at) VALUES (?, ?, ?, NOW())", 
        [$adminId, $action, $details]);
}

// Main handler
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Only POST requests allowed', null, 405);
}

$actionType = $_POST['action_type'] ?? '';

try {
    switch ($actionType) {
        
        // ===== TOPUP MANAGEMENT =====
        case 'create_topup_method':
            $adminId = validateSession('admin');
            $name = trim($_POST['name'] ?? '');
            $displayName = trim($_POST['display_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($name)) {
                sendResponse(false, 'Method name is required');
            }
            
            // Check if name exists
            $db = Database::getInstance();
            $exists = $db->pdo_query_one("SELECT id FROM topup_info WHERE name = ?", [$name]);
            if ($exists) {
                sendResponse(false, 'Method name already exists');
            }
            
            $result = $db->pdo_execute("INSERT INTO topup_info (name, display_name, description, status, created_at) VALUES (?, ?, ?, 1, NOW())", 
                [$name, $displayName, $description]);
            
            if ($result) {
                $methodId = $db->pdo->lastInsertId();
                logActivity($adminId, 'CREATE_TOPUP_METHOD', "Created method: $name (ID: $methodId)");
                sendResponse(true, 'Topup method created successfully', ['id' => $methodId]);
            } else {
                sendResponse(false, 'Failed to create topup method');
            }
            break;
            
        case 'update_topup_method':
            $adminId = validateSession('admin');
            $methodId = $_POST['method_id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            $displayName = trim($_POST['display_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($methodId) || empty($name)) {
                sendResponse(false, 'Method ID and name are required');
            }
            
            $db = Database::getInstance();
            $result = $db->pdo_execute("UPDATE topup_info SET name = ?, display_name = ?, description = ?, updated_at = NOW() WHERE id = ?", 
                [$name, $displayName, $description, $methodId]);
            
            if ($result) {
                logActivity($adminId, 'UPDATE_TOPUP_METHOD', "Updated method: $name (ID: $methodId)");
                sendResponse(true, 'Topup method updated successfully');
            } else {
                sendResponse(false, 'Failed to update topup method');
            }
            break;
            
        case 'delete_topup_method':
            $adminId = validateSession('admin');
            $methodId = $_POST['method_id'] ?? 0;
            
            if (empty($methodId)) {
                sendResponse(false, 'Method ID is required');
            }
            
            $db = Database::getInstance();
            
            // Check if method has pending transactions
            $pendingCount = $db->pdo_query_one("SELECT COUNT(*) as count FROM topup_history WHERE topup_type_id = ? AND status = 0", [$methodId]);
            if ($pendingCount['count'] > 0) {
                sendResponse(false, 'Cannot delete method with pending transactions');
            }
            
            // Delete metadata first
            $db->pdo_execute("DELETE FROM topup_metadata WHERE topup_id = ?", [$methodId]);
            
            // Delete method
            $result = $db->pdo_execute("DELETE FROM topup_info WHERE id = ?", [$methodId]);
            
            if ($result) {
                logActivity($adminId, 'DELETE_TOPUP_METHOD', "Deleted method ID: $methodId");
                sendResponse(true, 'Topup method deleted successfully');
            } else {
                sendResponse(false, 'Failed to delete topup method');
            }
            break;
            
        case 'add_topup_metadata':
            $adminId = validateSession('admin');
            $methodId = $_POST['method_id'] ?? 0;
            $metaKey = trim($_POST['meta_key'] ?? '');
            $metaValue = trim($_POST['meta_value'] ?? '');
            
            if (empty($methodId) || empty($metaKey) || empty($metaValue)) {
                sendResponse(false, 'All fields are required');
            }
            
            $db = Database::getInstance();
            $result = $db->pdo_execute("INSERT INTO topup_metadata (topup_id, meta_key, meta_value, created_at) VALUES (?, ?, ?, NOW())", 
                [$methodId, $metaKey, $metaValue]);
            
            if ($result) {
                $metaId = $db->pdo->lastInsertId();
                logActivity($adminId, 'ADD_TOPUP_METADATA', "Added metadata: $metaKey for method ID: $methodId");
                sendResponse(true, 'Metadata added successfully', ['id' => $metaId]);
            } else {
                sendResponse(false, 'Failed to add metadata');
            }
            break;
            
        case 'update_topup_metadata':
            $adminId = validateSession('admin');
            $metaId = $_POST['meta_id'] ?? 0;
            $metaKey = trim($_POST['meta_key'] ?? '');
            $metaValue = trim($_POST['meta_value'] ?? '');
            
            if (empty($metaId) || empty($metaKey) || empty($metaValue)) {
                sendResponse(false, 'All fields are required');
            }
            
            $db = Database::getInstance();
            $result = $db->pdo_execute("UPDATE topup_metadata SET meta_key = ?, meta_value = ?, updated_at = NOW() WHERE id = ?", 
                [$metaKey, $metaValue, $metaId]);
            
            if ($result) {
                logActivity($adminId, 'UPDATE_TOPUP_METADATA', "Updated metadata ID: $metaId");
                sendResponse(true, 'Metadata updated successfully');
            } else {
                sendResponse(false, 'Failed to update metadata');
            }
            break;
            
        case 'delete_topup_metadata':
            $adminId = validateSession('admin');
            $metaId = $_POST['meta_id'] ?? 0;
            
            if (empty($metaId)) {
                sendResponse(false, 'Metadata ID is required');
            }
            
            $db = Database::getInstance();
            $result = $db->pdo_execute("DELETE FROM topup_metadata WHERE id = ?", [$metaId]);
            
            if ($result) {
                logActivity($adminId, 'DELETE_TOPUP_METADATA', "Deleted metadata ID: $metaId");
                sendResponse(true, 'Metadata deleted successfully');
            } else {
                sendResponse(false, 'Failed to delete metadata');
            }
            break;
            
        case 'approve_topup':
            $adminId = validateSession('admin');
            $topupId = $_POST['topup_id'] ?? 0;
            $adminNote = trim($_POST['admin_note'] ?? '');
            
            if (empty($topupId)) {
                sendResponse(false, 'Topup ID is required');
            }
            
            $db = Database::getInstance();
            
            // Get topup details
            $topup = $db->pdo_query_one("SELECT * FROM topup_history WHERE id = ?", [$topupId]);
            if (!$topup) {
                sendResponse(false, 'Topup record not found');
            }
            
            if ($topup['status'] != 0) {
                sendResponse(false, 'Topup already processed');
            }
            
            // Start transaction
            $db->pdo->beginTransaction();
            
            try {
                // Update topup status
                $db->pdo_execute("UPDATE topup_history SET status = 1, admin_note = ?, processed_by = ?, processed_at = NOW() WHERE id = ?", 
                    [$adminNote, $adminId, $topupId]);
                
                // Update user balance
                $db->pdo_execute("UPDATE users SET money = money + ? WHERE id = ?", 
                    [$topup['amount'], $topup['user_id']]);
                
                // Add notification
                NotiModel::Send($adminId, $topup['user_id'], 'Nạp tiền thành công', 
                    "Yêu cầu nạp tiền {$topup['amount']} VND đã được duyệt thành công.", 'green');
                
                // Log transaction
                $db->pdo_execute("INSERT INTO transaction_logs (user_id, type, amount, description, created_at) VALUES (?, 'topup', ?, ?, NOW())", 
                    [$topup['user_id'], $topup['amount'], "Nạp tiền được duyệt - ID: $topupId"]);
                
                $db->pdo->commit();
                
                logActivity($adminId, 'APPROVE_TOPUP', "Approved topup ID: $topupId, Amount: {$topup['amount']}");
                sendResponse(true, 'Topup approved successfully');
                
            } catch (Exception $e) {
                $db->pdo->rollBack();
                sendResponse(false, 'Failed to approve topup: ' . $e->getMessage());
            }
            break;
            
        case 'reject_topup':
            $adminId = validateSession('admin');
            $topupId = $_POST['topup_id'] ?? 0;
            $adminNote = trim($_POST['admin_note'] ?? '');
            
            if (empty($topupId) || empty($adminNote)) {
                sendResponse(false, 'Topup ID and rejection reason are required');
            }
            
            $db = Database::getInstance();
            
            // Get topup details
            $topup = $db->pdo_query_one("SELECT * FROM topup_history WHERE id = ?", [$topupId]);
            if (!$topup) {
                sendResponse(false, 'Topup record not found');
            }
            
            if ($topup['status'] != 0) {
                sendResponse(false, 'Topup already processed');
            }
            
            // Update topup status
            $result = $db->pdo_execute("UPDATE topup_history SET status = 2, admin_note = ?, processed_by = ?, processed_at = NOW() WHERE id = ?", 
                [$adminNote, $adminId, $topupId]);
            
            if ($result) {
                // Add notification
                NotiModel::Send($adminId, $topup['user_id'], 'Yêu cầu nạp tiền bị từ chối', 
                    "Yêu cầu nạp tiền {$topup['amount']} VND đã bị từ chối. Lý do: $adminNote", 'red');
                
                logActivity($adminId, 'REJECT_TOPUP', "Rejected topup ID: $topupId, Reason: $adminNote");
                sendResponse(true, 'Topup rejected successfully');
            } else {
                sendResponse(false, 'Failed to reject topup');
            }
            break;
            
        // ===== WITHDRAW MANAGEMENT =====
        case 'approve_withdraw':
            $adminId = validateSession('admin');
            $withdrawId = $_POST['withdraw_id'] ?? 0;
            $adminNote = trim($_POST['admin_note'] ?? '');
            
            if (empty($withdrawId)) {
                sendResponse(false, 'Withdraw ID is required');
            }
            
            $db = Database::getInstance();
            
            // Get withdraw details
            $withdraw = $db->pdo_query_one("SELECT * FROM withdraw_history WHERE id = ?", [$withdrawId]);
            if (!$withdraw) {
                sendResponse(false, 'Withdraw record not found');
            }
            
            if ($withdraw['status'] != 0) {
                sendResponse(false, 'Withdraw already processed');
            }
            
            // Update withdraw status
            $result = $db->pdo_execute("UPDATE withdraw_history SET status = 1, admin_note = ?, processed_by = ?, processed_at = NOW() WHERE id = ?", 
                [$adminNote, $adminId, $withdrawId]);
            
            if ($result) {
                // Add notification
                NotiModel::Send($adminId, $withdraw['uid'], 'Rút tiền thành công', 
                    "Yêu cầu rút tiền {$withdraw['money']} VND đã được xử lý thành công.", 'green');
                
                // Log transaction
                $db->pdo_execute("INSERT INTO transaction_logs (user_id, type, amount, description, created_at) VALUES (?, 'withdraw', ?, ?, NOW())", 
                    [$withdraw['uid'], $withdraw['money'], "Rút tiền được duyệt - ID: $withdrawId"]);
                
                logActivity($adminId, 'APPROVE_WITHDRAW', "Approved withdraw ID: $withdrawId, Amount: {$withdraw['money']}");
                sendResponse(true, 'Withdraw approved successfully');
            } else {
                sendResponse(false, 'Failed to approve withdraw');
            }
            break;
            
        case 'reject_withdraw':
            $adminId = validateSession('admin');
            $withdrawId = $_POST['withdraw_id'] ?? 0;
            $adminNote = trim($_POST['admin_note'] ?? '');
            
            if (empty($withdrawId) || empty($adminNote)) {
                sendResponse(false, 'Withdraw ID and rejection reason are required');
            }
            
            $db = Database::getInstance();
            
            // Get withdraw details
            $withdraw = $db->pdo_query_one("SELECT * FROM withdraw_history WHERE id = ?", [$withdrawId]);
            if (!$withdraw) {
                sendResponse(false, 'Withdraw record not found');
            }
            
            if ($withdraw['status'] != 0) {
                sendResponse(false, 'Withdraw already processed');
            }
            
            // Start transaction
            $db->pdo->beginTransaction();
            
            try {
                // Update withdraw status
                $db->pdo_execute("UPDATE withdraw_history SET status = 2, admin_note = ?, processed_by = ?, processed_at = NOW() WHERE id = ?", 
                    [$adminNote, $adminId, $withdrawId]);
                
                // Refund money to user
                $db->pdo_execute("UPDATE users SET money = money + ? WHERE id = ?", 
                    [$withdraw['money'], $withdraw['uid']]);
                
                // Add notification
                NotiModel::Send($adminId, $withdraw['uid'], 'Yêu cầu rút tiền bị từ chối', 
                    "Yêu cầu rút tiền {$withdraw['money']} VND đã bị từ chối và số tiền đã được hoàn lại. Lý do: $adminNote", 'red');
                
                // Log transaction
                $db->pdo_execute("INSERT INTO transaction_logs (user_id, type, amount, description, created_at) VALUES (?, 'withdraw_refund', ?, ?, NOW())", 
                    [$withdraw['uid'], $withdraw['money'], "Rút tiền bị từ chối - hoàn tiền - ID: $withdrawId"]);
                
                $db->pdo->commit();
                
                logActivity($adminId, 'REJECT_WITHDRAW', "Rejected withdraw ID: $withdrawId, Reason: $adminNote");
                sendResponse(true, 'Withdraw rejected and money refunded successfully');
                
            } catch (Exception $e) {
                $db->pdo->rollBack();
                sendResponse(false, 'Failed to reject withdraw: ' . $e->getMessage());
            }
            break;
            
        // ===== DATA RETRIEVAL =====
        case 'get_topup_methods':
            validateSession('admin');
            $db = Database::getInstance();
            
            $methods = $db->pdo_query("SELECT * FROM topup_info ORDER BY name ASC");
            foreach ($methods as &$method) {
                $method['metadata'] = $db->pdo_query("SELECT * FROM topup_metadata WHERE topup_id = ? ORDER BY meta_key ASC", [$method['id']]);
            }
            
            sendResponse(true, 'Topup methods retrieved successfully', $methods);
            break;
            
        case 'get_topup_method':
            validateSession('admin');
            $methodId = $_POST['method_id'] ?? 0;
            
            if (empty($methodId)) {
                sendResponse(false, 'Method ID is required');
            }
            
            $db = Database::getInstance();
            $method = $db->pdo_query_one("SELECT * FROM topup_info WHERE id = ?", [$methodId]);
            
            if (!$method) {
                sendResponse(false, 'Method not found');
            }
            
            $method['metadata'] = $db->pdo_query("SELECT * FROM topup_metadata WHERE topup_id = ? ORDER BY meta_key ASC", [$methodId]);
            
            sendResponse(true, 'Method retrieved successfully', $method);
            break;
            
        case 'get_pending_topups':
            validateSession('admin');
            $db = Database::getInstance();
            
            $pending = $db->pdo_query("
                SELECT th.*, u.username, ti.name as method_name 
                FROM topup_history th 
                LEFT JOIN users u ON th.user_id = u.id 
                LEFT JOIN topup_info ti ON th.topup_type_id = ti.id 
                WHERE th.status = 0 
                ORDER BY th.created_at ASC
            ");
            
            sendResponse(true, 'Pending topups retrieved successfully', $pending);
            break;
            
        case 'get_pending_withdraws':
            validateSession('admin');
            $db = Database::getInstance();
            
            $pending = $db->pdo_query("
                SELECT wh.*, u.username 
                FROM withdraw_history wh 
                LEFT JOIN users u ON wh.uid = u.id 
                WHERE wh.status = 0 
                ORDER BY wh.create_time ASC
            ");
            
            sendResponse(true, 'Pending withdraws retrieved successfully', $pending);
            break;
            
        case 'get_payment_statistics':
            validateSession('admin');
            $db = Database::getInstance();
            
            $stats = [
                'pending_topups' => $db->pdo_query_values("SELECT COUNT(*) FROM topup_history WHERE status = 0"),
                'pending_withdraws' => $db->pdo_query_values("SELECT COUNT(*) FROM withdraw_history WHERE status = 0"),
                'total_topup_today' => $db->pdo_query_values("SELECT COALESCE(SUM(amount), 0) FROM topup_history WHERE DATE(created_at) = CURDATE() AND status = 1"),
                'total_withdraw_today' => $db->pdo_query_values("SELECT COALESCE(SUM(money), 0) FROM withdraw_history WHERE DATE(created_at) = CURDATE() AND status = 1"),
                'active_methods' => $db->pdo_query_values("SELECT COUNT(*) FROM topup_info WHERE status = 1")
            ];
            
            sendResponse(true, 'Statistics retrieved successfully', $stats);
            break;
            
        // ===== USER ACTIONS =====
        case 'submit_topup_request':
            $userId = validateSession('user');
            $methodId = $_POST['method_id'] ?? 0;
            $amount = floatval($_POST['amount'] ?? 0);
            $note = trim($_POST['note'] ?? '');
            
            if (empty($methodId) || $amount < 10000) {
                sendResponse(false, 'Invalid method or amount (minimum 10,000 VND)');
            }
            
            // Handle file upload
            if (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
                sendResponse(false, 'Proof image is required');
            }
            
            $uploadDir = '../uploads/topup/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = 'topup_' . $userId . '_' . time() . '_' . uniqid() . '.' . pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);
            $uploadPath = $uploadDir . $fileName;
            
            if (!move_uploaded_file($_FILES['proof']['tmp_name'], $uploadPath)) {
                sendResponse(false, 'Failed to upload proof image');
            }
            
            $db = Database::getInstance();
            $result = $db->pdo_execute("INSERT INTO topup_history (user_id, topup_type_id, amount, proof, status, date, created_at, note) VALUES (?, ?, ?, ?, 0, ?, NOW(), ?)", 
                [$userId, $methodId, $amount, '/uploads/topup/' . $fileName, time(), $note]);
            
            if ($result) {
                sendResponse(true, 'Topup request submitted successfully');
            } else {
                sendResponse(false, 'Failed to submit topup request');
            }
            break;
            
        case 'submit_withdraw_request':
            $userId = validateSession('user');
            $amount = floatval($_POST['amount'] ?? 0);
            
            if ($amount < 10000) {
                sendResponse(false, 'Minimum withdrawal amount is 10,000 VND');
            }
            
            $db = Database::getInstance();
            
            // Check user balance
            $user = $db->pdo_query_one("SELECT money FROM users WHERE id = ?", [$userId]);
            if (!$user || $user['money'] < $amount) {
                sendResponse(false, 'Insufficient balance');
            }
            
            // Check if user has bank info
            $bank = $db->pdo_query_one("SELECT * FROM bank_bind WHERE uid = ?", [$userId]);
            if (!$bank) {
                sendResponse(false, 'Please add bank information first');
            }
            
            // Start transaction
            $db->pdo->beginTransaction();
            
            try {
                // Deduct money from user
                $db->pdo_execute("UPDATE users SET money = money - ? WHERE id = ?", [$amount, $userId]);
                
                // Create withdraw request
                $db->pdo_execute("INSERT INTO withdraw_history (uid, money, bankid, bankinfo, bankname, status, create_time) VALUES (?, ?, ?, ?, ?, 0, ?)", 
                    [$userId, $amount, $bank['bankid'], $bank['bankinfo'], $bank['name'], time()]);
                
                $db->pdo->commit();
                
                sendResponse(true, 'Withdraw request submitted successfully');
                
            } catch (Exception $e) {
                $db->pdo->rollBack();
                sendResponse(false, 'Failed to submit withdraw request: ' . $e->getMessage());
            }
            break;
            
        default:
            sendResponse(false, 'Invalid action type', null, 400);
    }
    
} catch (Exception $e) {
    error_log("Payment Management Error: " . $e->getMessage());
    sendResponse(false, 'An error occurred: ' . $e->getMessage(), null, 500);
}
?>