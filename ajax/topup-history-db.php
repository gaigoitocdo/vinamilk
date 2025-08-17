<?php
// ajax/topup-history-db.php - Complete fixed version
include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/TopupModel.php";
include_once __DIR__ . "/../models/NotiModel.php";

session_start();
$adminId = $_SESSION['admin_id'] ?? 0;

// Xử lý các action
if (isset($_POST['action_type'])) {
    $id = (int)($_POST['id'] ?? 0);
    $db = Database::getInstance();

    try {
        switch ($_POST['action_type']) {
            case 'refuse_topup':
                $reason = trim($_POST['reason'] ?? 'Từ chối nạp tiền');
                
                // Lấy thông tin topup
                $t = TopupModel::GetOneHistory($id);
                if (!$t) {
                    echo json_encode(['success' => false, 'message' => 'Không tìm thấy yêu cầu']);
                    exit;
                }

                if ($t['status'] != 0) {
                    echo json_encode(['success' => false, 'message' => 'Yêu cầu đã được xử lý']);
                    exit;
                }

                // Cập nhật trạng thái từ chối
                TopupModel::UpdateHistory($id, ['status' => 2, 'refuse_reason' => $reason]);

                // Gửi thông báo
                NotiModel::Send($adminId, $t['user_id'], 'Nạp tiền thất bại', 
                    "Yêu cầu nạp tiền " . number_format($t['amount']) . " VND bị từ chối. Lý do: {$reason}", 'red');

                echo json_encode(['success' => true, 'message' => 'Đã từ chối thành công']);
                break;

            case 'accept_topup':
                // Lấy thông tin topup
                $t = TopupModel::GetOneHistory($id);
                if (!$t) {
                    echo json_encode(['success' => false, 'message' => 'Không tìm thấy yêu cầu']);
                    exit;
                }

                if ($t['status'] != 0) {
                    echo json_encode(['success' => false, 'message' => 'Yêu cầu đã được xử lý']);
                    exit;
                }

                // Bắt đầu transaction
                $db->pdo->beginTransaction();

                // Cập nhật trạng thái chấp nhận
                TopupModel::UpdateHistory($id, ['status' => 1]);

                // Cộng tiền cho user
                $db->pdo_execute("UPDATE users SET money = money + ? WHERE id = ?", [$t['amount'], $t['user_id']]);

                // Gửi thông báo
                NotiModel::Send($adminId, $t['user_id'], 'Nạp tiền thành công', 
                    "Yêu cầu nạp tiền " . number_format($t['amount']) . " VND đã được duyệt thành công.", 'green');

                $db->pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Đã duyệt thành công']);
                break;

            case 'get_stats':
                $today = date('Y-m-d');
                $stats = [
                    'pending' => $db->pdo_query_values("SELECT COUNT(*) FROM topup_history WHERE status = 0"),
                    'approved' => $db->pdo_query_values("SELECT COUNT(*) FROM topup_history WHERE status = 1 AND DATE(created_at) = ?", [$today]),
                    'rejected' => $db->pdo_query_values("SELECT COUNT(*) FROM topup_history WHERE status = 2 AND DATE(created_at) = ?", [$today]),
                    'total_today' => $db->pdo_query_values("SELECT COALESCE(SUM(amount), 0) FROM topup_history WHERE status = 1 AND DATE(created_at) = ?", [$today])
                ];
                echo json_encode(['success' => true, 'data' => $stats]);
                break;
        }
    } catch (Exception $e) {
        if ($db->pdo->inTransaction()) {
            $db->pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
    exit;
}

// DataTables processing
$db = Database::getInstance();
$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$searchVal = trim($_POST['search']['value'] ?? '');

$where = "WHERE 1 ";
$params = [];

// Filter by status
if (isset($_POST['status_filter']) && $_POST['status_filter'] !== '') {
    $where .= " AND th.status = ? ";
    $params[] = $_POST['status_filter'];
}

// Filter by date range
if (!empty($_POST['date_from'])) {
    $where .= " AND DATE(th.created_at) >= ? ";
    $params[] = $_POST['date_from'];
}

if (!empty($_POST['date_to'])) {
    $where .= " AND DATE(th.created_at) <= ? ";
    $params[] = $_POST['date_to'];
}

// Search
if ($searchVal !== '') {
    $where .= " AND (th.topup_type LIKE ? OR th.proof LIKE ? OR u.username LIKE ?) ";
    $searchParam = "%{$searchVal}%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
}

// Đếm tổng
$totalRecords = $db->pdo_query_values("SELECT COUNT(*) FROM topup_history");

// Đếm sau filter
$totalFiltered = $db->pdo_query_values(
    "SELECT COUNT(*) FROM topup_history th LEFT JOIN users u ON th.user_id = u.id $where", 
    $params
);

// Lấy dữ liệu
$sql = "
    SELECT th.*, u.username 
    FROM topup_history th 
    LEFT JOIN users u ON th.user_id = u.id
    $where
    ORDER BY th.id DESC
    LIMIT $start, $length
";
$rows = $db->pdo_query($sql, $params);

echo json_encode([
    "draw" => $draw,
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalFiltered,
    "data" => $rows,
]);
?>