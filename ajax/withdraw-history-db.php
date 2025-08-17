<?php
include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/WithdrawModel.php";
include_once __DIR__ . "/../models/NotiModel.php";

session_start();
$adminId = $_SESSION['admin_id'] ?? 0;

// Handle JSON response function
function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// 1) Handle refuse/accept actions
if (isset($_POST['action_type'])) {
    $id = (int)($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID không hợp lệ']);
    }

    switch ($_POST['action_type']) {
        case 'refuse_withdraw':
            try {
                $reason = trim($_POST['reason'] ?? 'Không có lý do cụ thể');
                
                // Get withdrawal info first
                $w = WithdrawModel::GetOneHistory($id);
                if (!$w) {
                    sendJsonResponse(['success' => false, 'message' => 'Không tìm thấy yêu cầu rút tiền']);
                }
                
                // Check if already processed
                if ($w['status'] != 0) {
                    sendJsonResponse(['success' => false, 'message' => 'Yêu cầu này đã được xử lý']);
                }
                
                $amount = (int)$w['money'];
                
                // Update status to refused and add reason
                WithdrawModel::UpdateHistory($id, [
                    'status' => 2,
                    'desc' => $reason
                ]);
                
                // Send notification
                NotiModel::Send(
                    $adminId,
                    $w['uid'],
                    'Rút tiền thất bại',
                    "Yêu cầu rút tiền {$amount} điểm của bạn đã bị từ chối. Lý do: {$reason}",
                    'red',
                    $amount
                );
                
                sendJsonResponse(['success' => true, 'message' => 'Đã từ chối yêu cầu rút tiền']);
                
            } catch (Exception $e) {
                sendJsonResponse(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
            }
            break;

        case 'accept_withdraw':
            try {
                // Get withdrawal info first
                $w = WithdrawModel::GetOneHistory($id);
                if (!$w) {
                    sendJsonResponse(['success' => false, 'message' => 'Không tìm thấy yêu cầu rút tiền']);
                }
                
                // Check if already processed
                if ($w['status'] != 0) {
                    sendJsonResponse(['success' => false, 'message' => 'Yêu cầu này đã được xử lý']);
                }
                
                $amount = (int)$w['money'];
                
                // Update status to approved
                WithdrawModel::UpdateHistory($id, ['status' => 1]);
                
                // Send notification
                NotiModel::Send(
                    $adminId,
                    $w['uid'],
                    'Rút tiền thành công',
                    "Yêu cầu rút tiền số điểm {$amount} của bạn đã được chấp nhận và sẽ nhận tiền trong vòng 5 phút!",
                    'green',
                    $amount
                );
                
                sendJsonResponse(['success' => true, 'message' => 'Đã chấp nhận yêu cầu rút tiền']);
                
            } catch (Exception $e) {
                sendJsonResponse(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
            }
            break;
            
        default:
            sendJsonResponse(['success' => false, 'message' => 'Hành động không hợp lệ']);
    }
}

// 2) Handle DataTables request
try {
    $db = Database::getInstance();
    
    // Get DataTables parameters
    $draw = intval($_POST['draw'] ?? 0);
    $start = intval($_POST['start'] ?? 0);
    $length = intval($_POST['length'] ?? 10);
    
    // Handle ordering
    $orderColumn = 0;
    $orderDir = 'desc';
    if (isset($_POST['order'][0])) {
        $orderColumn = intval($_POST['order'][0]['column']);
        $orderDir = $_POST['order'][0]['dir'] === 'asc' ? 'asc' : 'desc';
    }
    
    // Map column index to column name
    $columns = ['id', 'username', 'money', 'desc', 'create_time', 'bankinfo', 'status', 'id'];
    $orderColumnName = $columns[$orderColumn] ?? 'id';
    
    // Handle search
    $searchVal = trim($_POST['search']['value'] ?? '');
    $whereConditions = [];
    $searchParams = [];
    
    if ($searchVal !== '') {
        $whereConditions[] = "(`u`.`username` LIKE ? OR `w`.`bankid` LIKE ? OR `w`.`bankinfo` LIKE ? OR `w`.`bankname` LIKE ?)";
        $searchTerm = "%{$searchVal}%";
        $searchParams = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
    }
    
    $whereClause = empty($whereConditions) ? '' : 'WHERE ' . implode(' AND ', $whereConditions);
    
    // Get total records
    $totalRecords = $db->pdo_query_values("SELECT COUNT(*) FROM `withdraw_history`");
    
    // Get filtered records count
    $filteredQuery = "
        SELECT COUNT(*) 
        FROM `withdraw_history` AS w 
        LEFT JOIN `users` AS u ON w.uid = u.id
        $whereClause
    ";
    
    if (!empty($searchParams)) {
        $totalFiltered = $db->pdo_query_values($filteredQuery, $searchParams);
    } else {
        $totalFiltered = $totalRecords;
    }
    
    // Get data
    $dataQuery = "
        SELECT w.*, u.username 
        FROM `withdraw_history` AS w 
        LEFT JOIN `users` AS u ON w.uid = u.id
        $whereClause
        ORDER BY `w`.`$orderColumnName` $orderDir
        LIMIT $start, $length
    ";
    
    if (!empty($searchParams)) {
        $rows = $db->pdo_query($dataQuery, $searchParams);
    } else {
        $rows = $db->pdo_query($dataQuery);
    }
    
    // Format data
    $formattedRows = [];
    foreach ($rows as $row) {
        $formattedRows[] = [
            'id' => $row['id'],
            'username' => $row['username'] ?? 'N/A',
            'money' => number_format($row['money']),
            'desc' => $row['desc'] ?? '',
            'create_time' => $row['create_time'],
            'bankinfo' => $row['bankinfo'] ?? '',
            'bankid' => $row['bankid'] ?? '',
            'bankname' => $row['bankname'] ?? '',
            'status' => $row['status']
        ];
    }
    
    // Build response
    $response = [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalFiltered,
        "data" => $formattedRows
    ];
    
    sendJsonResponse($response);
    
} catch (Exception $e) {
    sendJsonResponse([
        'draw' => intval($_POST['draw'] ?? 0),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Lỗi server: ' . $e->getMessage()
    ]);
}