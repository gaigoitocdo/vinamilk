<?php
// ========== FILE: /admin/views/hunting/orders.php ==========
// Quản lý đơn hàng săn đơn - Tích hợp với admin hiện có

session_start();

// Include navbar admin hiện có
include_once __DIR__ . '/../../navbar.php';

// Include database
include_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_status') {
        $order_id = (int)($_POST['order_id'] ?? 0);
        $new_status = $_POST['new_status'] ?? '';
        $admin_notes = $_POST['admin_notes'] ?? '';
        
        if ($order_id && $new_status) {
            try {
                $sql = "UPDATE accepted_orders SET order_status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?";
                $db->pdo_execute($sql, [$new_status, $admin_notes, $order_id]);
                
                header("Location: orders.php?success=updated&order_id=" . $order_id);
                exit;
            } catch (Exception $e) {
                $error_message = "Lỗi cập nhật: " . $e->getMessage();
            }
        }
    }
    
    if ($action === 'bulk_update') {
        $order_ids = $_POST['order_ids'] ?? [];
        $bulk_status = $_POST['bulk_status'] ?? '';
        $bulk_notes = $_POST['bulk_notes'] ?? '';
        
        if (!empty($order_ids) && $bulk_status) {
            try {
                $placeholders = str_repeat('?,', count($order_ids) - 1) . '?';
                $sql = "UPDATE accepted_orders SET order_status = ?, admin_notes = ?, updated_at = NOW() WHERE id IN ($placeholders)";
                $params = array_merge([$bulk_status, $bulk_notes], $order_ids);
                $db->pdo_execute($sql, $params);
                
                header("Location: orders.php?success=bulk_updated&count=" . count($order_ids));
                exit;
            } catch (Exception $e) {
                $error_message = "Lỗi cập nhật hàng loạt: " . $e->getMessage();
            }
        }
    }
}

// Pagination and filters
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

$status_filter = $_GET['status'] ?? '';
$hunting_id_filter = $_GET['hunting_id'] ?? '';
$user_id_filter = $_GET['user_id'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$sort_by = $_GET['sort'] ?? 'created_at';
$sort_order = $_GET['order'] ?? 'desc';

// Build WHERE clause
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "o.order_status = ?";
    $params[] = $status_filter;
}

if ($hunting_id_filter) {
    $where_conditions[] = "o.hunting_id LIKE ?";
    $params[] = "%$hunting_id_filter%";
}

if ($user_id_filter) {
    $where_conditions[] = "o.user_id = ?";
    $params[] = (int)$user_id_filter;
}

if ($date_from) {
    $where_conditions[] = "DATE(o.created_at) >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $where_conditions[] = "DATE(o.created_at) <= ?";
    $params[] = $date_to;
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Validate sort parameters
$allowed_sorts = ['created_at', 'order_status', 'total_amount', 'commission_earned', 'hunting_id'];
$sort_by = in_array($sort_by, $allowed_sorts) ? $sort_by : 'created_at';
$sort_order = in_array($sort_order, ['asc', 'desc']) ? $sort_order : 'desc';

// Get total count
$count_sql = "SELECT COUNT(*) FROM accepted_orders o $where_clause";
$total_records = $db->pdo_query_value($count_sql, $params) ?: 0;
$total_pages = ceil($total_records / $limit);

// Get orders with product info
$sql = "SELECT o.*, p.title, p.hinh_anh, p.gia_sale
        FROM accepted_orders o 
        LEFT JOIN san_pham_shop p ON o.product_id = p.id 
        $where_clause
        ORDER BY o.$sort_by $sort_order 
        LIMIT $limit OFFSET $offset";

$orders = $db->pdo_query($sql, $params) ?: [];

// Get summary stats for current filter
$stats_sql = "SELECT 
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue,
    SUM(commission_earned) as total_commission,
    AVG(total_amount) as avg_order_value,
    COUNT(CASE WHEN o.order_status = 'pending' THEN 1 END) as pending_count,
    COUNT(CASE WHEN o.order_status = 'processing' THEN 1 END) as processing_count,
    COUNT(CASE WHEN o.order_status = 'shipped' THEN 1 END) as shipped_count,
    COUNT(CASE WHEN o.order_status = 'delivered' THEN 1 END) as delivered_count,
    COUNT(CASE WHEN o.order_status = 'cancelled' THEN 1 END) as cancelled_count
    FROM accepted_orders o $where_clause";

$stats = $db->pdo_query_one($stats_sql, $params) ?: [];

// Get status distribution for pie chart
$status_chart_sql = "SELECT order_status, COUNT(*) as count 
                     FROM accepted_orders o $where_clause
                     GROUP BY order_status";
$status_chart_data = $db->pdo_query($status_chart_sql, $params) ?: [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng săn đơn</title>
    
    <!-- Admin Template CSS -->
    <link href="/admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="/admin/css/sb-admin-2.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .order-card {
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .order-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .status-pending { border-left-color: #ffc107; }
        .status-processing { border-left-color: #17a2b8; }
        .status-shipped { border-left-color: #6f42c1; }
        .status-delivered { border-left-color: #28a745; }
        .status-cancelled { border-left-color: #dc3545; }
        
        .stats-overview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .filter-panel {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .order-actions {
            position: sticky;
            top: 0;
            background: white;
            padding: 15px 0;
            border-bottom: 1px solid #e3e6f0;
            margin-bottom: 20px;
            z-index: 10;
        }
        
        .bulk-actions {
            display: none;
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .bulk-actions.show {
            display: block;
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .order-timeline {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .amount-highlight {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .order-card {
                margin-bottom: 15px;
            }
            
            .stats-overview .row > div {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        
        <!-- Sidebar -->
        <?php admin_side_bar(); ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php admin_top_bar(); ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng săn đơn
                        </h1>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="exportOrders()">
                                <i class="fas fa-download"></i> Xuất Excel
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="location.reload()">
                                <i class="fas fa-sync-alt"></i> Làm mới
                            </button>
                        </div>
                    </div>
                    
                    <!-- Success/Error Messages -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> 
                            <?php if ($_GET['success'] === 'updated'): ?>
                                Đã cập nhật đơn hàng #<?= htmlspecialchars($_GET['order_id'] ?? '') ?> thành công!
                            <?php elseif ($_GET['success'] === 'bulk_updated'): ?>
                                Đã cập nhật <?= (int)($_GET['count'] ?? 0) ?> đơn hàng thành công!
                            <?php endif; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Stats Overview -->
                    <div class="stats-overview">
                        <div class="row text-center">
                            <div class="col-md-2 col-6">
                                <div class="h4 mb-0"><?= number_format($stats['total_orders'] ?? 0) ?></div>
                                <div class="small">Tổng đơn hàng</div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="h4 mb-0"><?= number_format($stats['total_revenue'] ?? 0) ?>₫</div>
                                <div class="small">Tổng doanh thu</div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="h4 mb-0"><?= number_format($stats['total_commission'] ?? 0) ?>₫</div>
                                <div class="small">Tổng hoa hồng</div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="h4 mb-0"><?= number_format($stats['avg_order_value'] ?? 0) ?>₫</div>
                                <div class="small">Giá trị TB/đơn</div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="h4 mb-0"><?= number_format($stats['pending_count'] ?? 0) ?></div>
                                <div class="small">Chờ xử lý</div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="h4 mb-0"><?= number_format($stats['delivered_count'] ?? 0) ?></div>
                                <div class="small">Đã giao</div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-panel">
                        <form method="GET" class="row">
                            <div class="col-md-2 col-6 mb-2">
                                <label class="form-label small">Trạng thái</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">Tất cả</option>
                                    <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                    <option value="processing" <?= $status_filter === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                                    <option value="shipped" <?= $status_filter === 'shipped' ? 'selected' : '' ?>>Đã gửi</option>
                                    <option value="delivered" <?= $status_filter === 'delivered' ? 'selected' : '' ?>>Đã giao</option>
                                    <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-6 mb-2">
                                <label class="form-label small">Mã săn đơn</label>
                                <input type="text" name="hunting_id" class="form-control form-control-sm" 
                                       value="<?= htmlspecialchars($hunting_id_filter) ?>" placeholder="HD...">
                            </div>
                            <div class="col-md-1 col-6 mb-2">
                                <label class="form-label small">User ID</label>
                                <input type="number" name="user_id" class="form-control form-control-sm" 
                                       value="<?= htmlspecialchars($user_id_filter) ?>" placeholder="ID">
                            </div>
                            <div class="col-md-2 col-6 mb-2">
                                <label class="form-label small">Từ ngày</label>
                                <input type="date" name="date_from" class="form-control form-control-sm" 
                                       value="<?= htmlspecialchars($date_from) ?>">
                            </div>
                            <div class="col-md-2 col-6 mb-2">
                                <label class="form-label small">Đến ngày</label>
                                <input type="date" name="date_to" class="form-control form-control-sm" 
                                       value="<?= htmlspecialchars($date_to) ?>">
                            </div>
                            <div class="col-md-1 col-6 mb-2">
                                <label class="form-label small">Sắp xếp</label>
                                <select name="sort" class="form-control form-control-sm">
                                    <option value="created_at" <?= $sort_by === 'created_at' ? 'selected' : '' ?>>Ngày tạo</option>
                                    <option value="total_amount" <?= $sort_by === 'total_amount' ? 'selected' : '' ?>>Giá trị</option>
                                    <option value="order_status" <?= $sort_by === 'order_status' ? 'selected' : '' ?>>Trạng thái</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-12">
                                <label class="form-label small">&nbsp;</label>
                                <div class="btn-group d-block">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search"></i> Lọc
                                    </button>
                                    <a href="orders.php" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-times"></i> Xóa
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Order Actions -->
                    <div class="order-actions">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <input type="checkbox" id="selectAll" class="mr-2">
                                <label for="selectAll" class="mb-0">Chọn tất cả</label>
                                <span class="ml-3 text-muted">
                                    Hiển thị <?= count($orders) ?> / <?= number_format($total_records) ?> đơn hàng
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-warning btn-sm" onclick="showBulkActions()">
                                    <i class="fas fa-edit"></i> Cập nhật hàng loạt
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Actions Panel -->
                    <div class="bulk-actions" id="bulkActionsPanel">
                        <form method="POST" id="bulkForm">
                            <input type="hidden" name="action" value="bulk_update">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Trạng thái mới</label>
                                    <select name="bulk_status" class="form-control" required>
                                        <option value="">Chọn trạng thái...</option>
                                        <option value="pending">Chờ xử lý</option>
                                        <option value="processing">Đang xử lý</option>
                                        <option value="shipped">Đã gửi hàng</option>
                                        <option value="delivered">Đã giao hàng</option>
                                        <option value="cancelled">Đã hủy</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ghi chú</label>
                                    <input type="text" name="bulk_notes" class="form-control" placeholder="Ghi chú cho tất cả đơn hàng được chọn...">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Cập nhật (<span id="selectedCount">0</span> đơn)
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="hideBulkActions()">
                                        <i class="fas fa-times"></i> Hủy
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Orders List -->
                    <?php if (empty($orders)): ?>
                        <div class="card shadow">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Không có đơn hàng nào</h5>
                                <p class="text-muted">Thử thay đổi bộ lọc hoặc kiểm tra trang Dashboard</p>
                                <a href="dashboard.php" class="btn btn-primary">
                                    <i class="fas fa-tachometer-alt"></i> Về Dashboard
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Orders List as Cards -->
                        <div class="row">
                            <?php foreach ($orders as $order): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card order-card status-<?= $order['order_status'] ?>">
                                        <div class="card-body">
                                            <!-- Order Header -->
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <input type="checkbox" class="order-checkbox mr-2" value="<?= $order['id'] ?>">
                                                    <h6 class="card-title mb-1 text-primary fw-bold d-inline">
                                                        <?= htmlspecialchars($order['hunting_id']) ?>
                                                    </h6>
                                                    <small class="text-muted d-block">ID: <?= $order['id'] ?></small>
                                                </div>
                                                <div>
                                                    <?php
                                                    $status_badges = [
                                                        'pending' => ['warning', 'Chờ xử lý'],
                                                        'processing' => ['info', 'Đang xử lý'],
                                                        'shipped' => ['primary', 'Đã gửi'],
                                                        'delivered' => ['success', 'Đã giao'],
                                                        'cancelled' => ['danger', 'Đã hủy']
                                                    ];
                                                    $badge = $status_badges[$order['order_status']] ?? ['secondary', 'N/A'];
                                                    ?>
                                                    <span class="badge badge-<?= $badge[0] ?> status-badge"><?= $badge[1] ?></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Product Info -->
                                            <div class="d-flex align-items-center mb-3">
                                                <?php if ($order['hinh_anh']): ?>
                                                    <img src="/<?= htmlspecialchars($order['hinh_anh']) ?>" 
                                                         alt="Product" class="product-thumb me-3">
                                                <?php else: ?>
                                                    <div class="product-thumb bg-light d-flex align-items-center justify-content-center me-3">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold"><?= htmlspecialchars(substr($order['title'] ?? 'N/A', 0, 25)) ?>...</div>
                                                    <small class="text-muted">User #<?= $order['user_id'] ?></small>
                                                </div>
                                            </div>
                                            
                                            <!-- Order Info -->
                                            <div class="row text-center mb-3">
                                                <div class="col-6">
                                                    <div class="amount-highlight text-danger"><?= number_format($order['total_amount'] ?? 0) ?>₫</div>
                                                    <small class="text-muted">Tổng tiền</small>
                                                </div>
                                                <div class="col-6">
                                                    <div class="amount-highlight text-success"><?= number_format($order['commission_earned'] ?? 0) ?>₫</div>
                                                    <small class="text-muted">Hoa hồng</small>
                                                </div>
                                            </div>
                                            
                                            <!-- Dates -->
                                            <div class="mb-3 order-timeline">
                                                <div class="small text-muted">
                                                    <i class="fas fa-calendar"></i> Tạo: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                                </div>
                                                <?php if ($order['updated_at'] !== $order['created_at']): ?>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-edit"></i> Cập nhật: <?= date('d/m/Y H:i', strtotime($order['updated_at'])) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Customer Notes -->
                                            <?php if (!empty($order['customer_notes'])): ?>
                                                <div class="mb-3">
                                                    <small class="text-muted">Ghi chú khách hàng:</small>
                                                    <p class="small"><?= htmlspecialchars($order['customer_notes']) ?></p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Admin Notes -->
                                            <?php if (!empty($order['admin_notes'])): ?>
                                                <div class="mb-3">
                                                    <small class="text-muted">Ghi chú admin:</small>
                                                    <p class="small"><?= htmlspecialchars($order['admin_notes']) ?></p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Actions -->
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-primary flex-grow-1" 
                                                        onclick="updateOrderStatus(<?= $order['id'] ?>, '<?= htmlspecialchars($order['order_status']) ?>', '<?= htmlspecialchars(addslashes($order['admin_notes'] ?? '')) ?>')">
                                                    <i class="fas fa-edit"></i> Cập nhật
                                                </button>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="viewOrderDetails(<?= $order['id'] ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Orders pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php
                                $query_params = $_GET;
                                
                                // Previous button
                                if ($page > 1):
                                    $query_params['page'] = $page - 1;
                                    $prev_url = '?' . http_build_query($query_params);
                                ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= $prev_url ?>">‹ Trước</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php
                                // Page numbers
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                for ($i = $start_page; $i <= $end_page; $i++):
                                    $query_params['page'] = $i;
                                    $page_url = '?' . http_build_query($query_params);
                                    $active_class = ($i === $page) ? 'active' : '';
                                ?>
                                    <li class="page-item <?= $active_class ?>">
                                        <a class="page-link" href="<?= $page_url ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php
                                // Next button
                                if ($page < $total_pages):
                                    $query_params['page'] = $page + 1;
                                    $next_url = '?' . http_build_query($query_params);
                                ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= $next_url ?>">Tiếp ›</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Hệ thống săn đơn hàng &copy; <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" id="modal_order_id">
                        
                        <div class="form-group">
                            <label class="form-label">Trạng thái mới</label>
                            <select name="new_status" id="modal_status" class="form-control" required>
                                <option value="pending">Chờ xử lý</option>
                                <option value="processing">Đang xử lý</option>
                                <option value="shipped">Đã gửi hàng</option>
                                <option value="delivered">Đã giao hàng</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Ghi chú admin</label>
                            <textarea name="admin_notes" id="modal_notes" class="form-control" rows="3" 
                                      placeholder="Ghi chú nội bộ..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="/admin/vendor/jquery/jquery.min.js"></script>
    <script src="/admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/admin/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/admin/js/sb-admin-2.min.js"></script>
    
    <script>
        // Update order status
        function updateOrderStatus(orderId, currentStatus, currentNotes) {
            document.getElementById('modal_order_id').value = orderId;
            document.getElementById('modal_status').value = currentStatus;
            document.getElementById('modal_notes').value = currentNotes;
            
            $('#updateStatusModal').modal('show');
        }
        
        // View order details
        function viewOrderDetails(orderId) {
            // You can implement a detailed view modal or redirect to details page
            alert('Xem chi tiết đơn hàng ID: ' + orderId);
        }
        
        // Export orders
        function exportOrders() {
            // Get current filter parameters
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'excel');
            
            // Create download link
            const exportUrl = 'orders.php?' + params.toString();
            window.open(exportUrl, '_blank');
        }
        
        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.order-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
        
        // Update selected count
        function updateSelectedCount() {
            const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
            document.getElementById('selectedCount').textContent = selectedCheckboxes.length;
            
            // Update bulk form with selected IDs
            const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
            
            // Remove existing hidden inputs
            const existingInputs = document.querySelectorAll('#bulkForm input[name="order_ids[]"]');
            existingInputs.forEach(input => input.remove());
            
            // Add new hidden inputs for selected IDs
            const bulkForm = document.getElementById('bulkForm');
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'order_ids[]';
                input.value = id;
                bulkForm.appendChild(input);
            });
        }
        
        // Add event listeners to individual checkboxes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('order-checkbox')) {
                updateSelectedCount();
            }
        });
        
        // Show bulk actions
        function showBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Vui lòng chọn ít nhất một đơn hàng!');
                return;
            }
            
            document.getElementById('bulkActionsPanel').classList.add('show');
        }
        
        // Hide bulk actions
        function hideBulkActions() {
            document.getElementById('bulkActionsPanel').classList.remove('show');
        }
        
        // Form validation for bulk update
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một đơn hàng!');
                return false;
            }
            
            const bulkStatus = document.querySelector('[name="bulk_status"]').value;
            if (!bulkStatus) {
                e.preventDefault();
                alert('Vui lòng chọn trạng thái mới!');
                return false;
            }
            
            if (!confirm(`Bạn có chắc muốn cập nhật ${selectedCheckboxes.length} đơn hàng?`)) {
                e.preventDefault();
                return false;
            }
            
            return true;
        });
        
        // Auto-refresh every 30 seconds for pending orders
        setInterval(function() {
            const pendingCount = document.querySelectorAll('.status-pending').length;
            if (pendingCount > 0) {
                console.log(`${pendingCount} đơn hàng đang chờ xử lý`);
                // You can implement subtle background refresh here
            }
        }, 30000);
        
        // Real-time notifications (placeholder)
        function checkNewOrders() {
            // This would typically make an AJAX call to check for new orders
            // and show notifications
        }
        
        // Initialize tooltips and other UI enhancements
        $(document).ready(function() {
            // Enable tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        });
    </script>

</body>
</html>