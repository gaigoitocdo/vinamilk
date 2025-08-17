<?php
// File: ajax/users-db.php - Fixed version with spin count update and reset all feature

// Bật error reporting để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Kiểm tra file config
    if (!file_exists(__DIR__ . "/../config/config.php")) {
        throw new Exception("Config file not found");
    }
    include_once __DIR__ . "/../config/config.php";
    
    // Kiểm tra UserModel
    if (!file_exists(__DIR__ . "/../models/UserModel.php")) {
        throw new Exception("UserModel file not found");
    }
    include_once __DIR__ . "/../models/UserModel.php";
    
    // Kiểm tra Database
    if (!class_exists('Database')) {
        throw new Exception("Database class not found");
    }
    
    $db = Database::getInstance();
    if (!$db) {
        throw new Exception("Cannot connect to database");
    }
    
    // Get PDO connection - FIX: Ensure we have PDO connection
    $pdo = $db->pdo ?? $db->getConnection();
    if (!$pdo) {
        throw new Exception("Cannot get PDO connection");
    }
    
    $table_name = 'users';
    
    // Log request data for debugging
    error_log("POST data: " . json_encode($_POST));
    
    // Xử lý các action CRUD
    if (isset($_POST['action_type'])) {
        $action_type = $_POST['action_type'];
        
        switch ($action_type) {
            case 'add_user':
                $result = UserModel::Create($_POST);
                echo json_encode(['success' => true, 'message' => 'Tạo user thành công']);
                break;

            case 'edit_user':
                $id = $_POST['id'] ?? 0;
                if (!$id) {
                    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
                    exit;
                }
                $result = UserModel::Update($id, $_POST);
                echo json_encode(['success' => true, 'message' => 'Cập nhật user thành công']);
                break;

            case 'get_user':
                $id = $_POST['id'] ?? 0;
                if (!$id) {
                    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
                    exit;
                }
                $user = UserModel::GetOne($id);
                
                // Add spin_count if not exists
                if (!isset($user['spin_count'])) {
                    $spinQuery = "SELECT spin_count FROM users WHERE id = ?";
                    $spinResult = $db->pdo_query_one($spinQuery, [$id]);
                    $user['spin_count'] = $spinResult['spin_count'] ?? 5;
                }
                
                echo json_encode(['success' => true, 'data' => $user]);
                break;

            case 'delete_user':
                $id = $_POST['id'] ?? 0;
                if (!$id) {
                    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
                    exit;
                }
                $result = UserModel::Delete($id);
                echo json_encode(['success' => true, 'message' => 'Xóa user thành công']);
                break;

            case 'update_user_status':
                $id = $_POST['id'] ?? 0;
                $status = $_POST['status'] ?? 1;
                if (!$id) {
                    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
                    exit;
                }
                $result = UserModel::UpdateStatus($id, $status);
                echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
                break;

            case 'update_user_money':
                $id = $_POST['id'] ?? 0;
                $num = $_POST['num'] ?? 0;
                $charge_type = $_POST['charge_type'] ?? 'Nạp tiền';
                $desc = $_POST['desc'] ?? '';
                if (!$id) {
                    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
                    exit;
                }
                $result = UserModel::UpdateMoney($id, $num, $charge_type, $desc);
                echo json_encode(['success' => true, 'message' => 'Cập nhật số dư thành công']);
                break;

            // FIXED: Handle spin count update
            case 'update_user_spin_count':
                $userId = intval($_POST['user_id'] ?? 0);
                $spinAmount = intval($_POST['spin_amount'] ?? 5);
                $actionType = $_POST['spin_action'] ?? 'add';
                $note = $_POST['note'] ?? '';
                
                error_log("Spin count update - User: $userId, Amount: $spinAmount, Action: $actionType");
                
                if (!$userId) {
                    echo json_encode(['success' => false, 'message' => 'User ID không hợp lệ']);
                    exit;
                }
                
                if ($spinAmount < 0) {
                    echo json_encode(['success' => false, 'message' => 'Số lượt quay phải >= 0']);
                    exit;
                }
                
                // Get current user info
                $stmt = $pdo->prepare("SELECT id, spin_count, username FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user) {
                    echo json_encode(['success' => false, 'message' => 'Không tìm thấy user']);
                    exit;
                }
                
                $oldSpinCount = intval($user['spin_count'] ?? 0);
                
                // Calculate new spin count
                if ($actionType === 'set') {
                    $newSpinCount = $spinAmount;
                    $actionDesc = "Đặt cố định";
                } else { // 'add' or any other value defaults to add
                    $newSpinCount = $oldSpinCount + $spinAmount;
                    $actionDesc = "Cộng thêm";
                }
                
                // Ensure non-negative
                $newSpinCount = max(0, $newSpinCount);
                
                // Update spin count
                $stmt = $pdo->prepare("UPDATE users SET spin_count = ?, last_login = ? WHERE id = ?");
                $result = $stmt->execute([$newSpinCount, time(), $userId]);
                
                if ($result) {
                    $message = "{$actionDesc} {$spinAmount} lượt quay cho {$user['username']}. Từ {$oldSpinCount} → {$newSpinCount} lượt";
                    
                    echo json_encode([
                        'success' => true,
                        'message' => $message,
                        'old_spin_count' => $oldSpinCount,
                        'new_spin_count' => $newSpinCount,
                        'user_id' => $userId,
                        'username' => $user['username']
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Không thể cập nhật lượt quay']);
                }
                break;

            // NEW: Reset all users' spin count to 0
            case 'reset_all_spin_counts':
                try {
                    $stmt = $pdo->prepare("UPDATE users SET spin_count = 0");
                    $result = $stmt->execute();
                    
                    if ($result) {
                        $affectedRows = $stmt->rowCount();
                        echo json_encode([
                            'success' => true, 
                            'message' => "Đã reset lượt quay về 0 cho {$affectedRows} users",
                            'affected_rows' => $affectedRows
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Không thể reset lượt quay']);
                    }
                } catch (Exception $e) {
                    error_log("Reset all spin counts error: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
                }
                break;

            // ENHANCED: Handle bulk spin count update
            case 'bulk_update_spin_count':
                $action = $_POST['action'] ?? 'add';
                $amount = intval($_POST['amount'] ?? 5);
                $note = $_POST['note'] ?? '';
                
                try {
                    if ($action === 'reset') {
                        // Reset all users to 5 spins
                        $stmt = $pdo->prepare("UPDATE users SET spin_count = 5");
                        $result = $stmt->execute();
                        $affectedRows = $stmt->rowCount();
                        $message = "Đã reset tất cả {$affectedRows} users về 5 lượt quay";
                        
                    } else if ($action === 'reset_zero') {
                        // Reset all users to 0 spins
                        $stmt = $pdo->prepare("UPDATE users SET spin_count = 0");
                        $result = $stmt->execute();
                        $affectedRows = $stmt->rowCount();
                        $message = "Đã reset tất cả {$affectedRows} users về 0 lượt quay";
                        
                    } else if ($action === 'add') {
                        // Add spins to all users
                        $stmt = $pdo->prepare("UPDATE users SET spin_count = spin_count + ?");
                        $result = $stmt->execute([$amount]);
                        $affectedRows = $stmt->rowCount();
                        $message = "Đã cộng {$amount} lượt quay cho {$affectedRows} users";
                        
                    } else if ($action === 'set') {
                        // Set fixed amount for all users
                        $stmt = $pdo->prepare("UPDATE users SET spin_count = ?");
                        $result = $stmt->execute([$amount]);
                        $affectedRows = $stmt->rowCount();
                        $message = "Đã đặt {$amount} lượt quay cho {$affectedRows} users";
                        
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
                        exit;
                    }
                    
                    if ($result) {
                        echo json_encode([
                            'success' => true, 
                            'message' => $message,
                            'affected_rows' => $affectedRows
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật database']);
                    }
                    
                } catch (Exception $e) {
                    error_log("Bulk spin count update error: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
                }
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Action không hợp lệ: ' . $action_type]);
                break;
        }
        exit;
    }
    
    // Xử lý DataTable data
    if (isset($_POST['draw'])) {
        $draw = intval($_POST['draw'] ?? 1);
        $start = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        
        // Xử lý sorting
        $columnIndex = $_POST['order'][0]['column'] ?? 0;
        $columnName = $_POST['columns'][$columnIndex]['data'] ?? 'id';
        $columnSortOrder = $_POST['order'][0]['dir'] ?? 'desc';
        
        // Mapping column names để tránh lỗi SQL injection
        $allowedColumns = [
            'id', 'username', 'phone', 'name', 'money', 'credit', 
            'bank_name', 'bank_account', 'bank_owner', 'gender', 
            'type', 'status', 'vip', 'order_count', 'reg_date', 'last_login', 'spin_count'
        ];
        
        if (!in_array($columnName, $allowedColumns)) {
            $columnName = 'id';
        }
        
        $searchValue = $_POST['search']['value'] ?? '';
        
        // Search Query
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " AND (username LIKE :search OR 
                name LIKE :search OR 
                phone LIKE :search OR
                bank_name LIKE :search OR
                bank_owner LIKE :search)";
        }
        
        // Count total records
        $countQuery = "SELECT COUNT(*) as allcount FROM `$table_name` WHERE 1";
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute();
        $totalRecords = $stmt->fetch(PDO::FETCH_ASSOC)['allcount'];
        
        // Count filtered records
        $countFilterQuery = "SELECT COUNT(*) as allcount FROM `$table_name` WHERE 1 " . $searchQuery;
        $stmt = $pdo->prepare($countFilterQuery);
        if ($searchValue != '') {
            $stmt->bindValue(':search', '%' . $searchValue . '%');
        }
        $stmt->execute();
        $totalRecordwithFilter = $stmt->fetch(PDO::FETCH_ASSOC)['allcount'];
        
        // Fetch records - FIXED: Added spin_count to query
        $empQuery = "SELECT 
            id,
            username,
            phone,
            name,
            money,
            credit,
            bank_name,
            bank_account,
            bank_owner,
            gender,
            type,
            status,
            vip,
            order_count,
            reg_date,
            last_login,
            total_topup,
            member_code,
            COALESCE(spin_count, 0) as spin_count
            FROM `$table_name` 
            WHERE 1 " . $searchQuery . " 
            ORDER BY `" . $columnName . "` " . $columnSortOrder . " 
            LIMIT " . $start . "," . $length;
        
        $stmt = $pdo->prepare($empQuery);
        if ($searchValue != '') {
            $stmt->bindValue(':search', '%' . $searchValue . '%');
        }
        $stmt->execute();
        $empRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get orders count for each user
        $userIds = array_column($empRecords, 'id');
        $ordersData = [];
        
        if (!empty($userIds)) {
            $orderQuery = "SELECT 
                user_id,
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders
                FROM orders 
                WHERE user_id IN (" . implode(',', array_map('intval', $userIds)) . ") 
                GROUP BY user_id";
            
            try {
                $stmt = $pdo->prepare($orderQuery);
                $stmt->execute();
                $orderResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($orderResults as $order) {
                    $ordersData[$order['user_id']] = [
                        'total_orders' => $order['total_orders'],
                        'pending_orders' => $order['pending_orders'],
                        'completed_orders' => $order['completed_orders']
                    ];
                }
            } catch (Exception $e) {
                error_log("Orders query error: " . $e->getMessage());
            }
        }
        
        $data = array();
        foreach ($empRecords as $row) {
            $userId = $row['id'];
            $orderInfo = $ordersData[$userId] ?? ['total_orders' => 0, 'pending_orders' => 0, 'completed_orders' => 0];
            
            $data[] = array(
                'id' => $row['id'],
                'username' => $row['username'],
                'phone' => $row['phone'] ?? '',
                'name' => $row['name'] ?? '',
                'money' => number_format($row['money'] ?? 0, 0, ',', '.'),
                'spin_count' => intval($row['spin_count'] ?? 0), // FIXED: Include spin_count with default 0
                'credit' => $row['credit'] ?? 0,
                'bank_name' => $row['bank_name'] ?? '',
                'bank_account' => $row['bank_account'] ?? '',
                'bank_owner' => $row['bank_owner'] ?? '',
                'gender' => $row['gender'] ?? 0,
                'type' => $row['type'] ?? 0,
                'status' => $row['status'] ?? 1,
                'vip' => $row['vip'] ?? 1,
                'order_count' => $row['order_count'] ?? 0,
                'pending_orders' => $orderInfo['pending_orders'],
                'completed_orders' => $orderInfo['completed_orders'],
                'total_orders' => $orderInfo['total_orders'],
                'country' => 'VN',
                'created_at' => $row['reg_date'] ? date('Y-m-d H:i:s', $row['reg_date']) : '',
                'updated_at' => $row['last_login'] ? date('Y-m-d H:i:s', $row['last_login']) : '',
                'total_topup' => $row['total_topup'] ?? 0,
                'member_code' => $row['member_code'] ?? ''
            );
        }
        
        // Response for DataTables
        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordwithFilter,
            "data" => $data
        );
        
        echo json_encode($response);
        exit;
    }
    
    // If no valid request
    echo json_encode(['error' => 'Invalid request', 'post_data' => $_POST]);
    
} catch (Exception $e) {
    error_log("Error in users-db.php: " . $e->getMessage());
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>