<?php
// admin_payment_management.php - Trang quản lý phương thức thanh toán cho Admin
session_start();



// Kết nối database
require_once 'config/database.php'; // File kết nối DB của bạn

// Xử lý AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'get_methods':
            getPaymentMethods();
            break;
        case 'add_method':
            addPaymentMethod();
            break;
        case 'update_method':
            updatePaymentMethod();
            break;
        case 'delete_method':
            deletePaymentMethod();
            break;
        case 'toggle_status':
            toggleMethodStatus();
            break;
        case 'get_transactions':
            getRecentTransactions();
            break;
        case 'get_stats':
            getStats();
            break;
    }
    exit;
}

// Functions để xử lý API
function getPaymentMethods() {
    global $conn;
    
    try {
        // Lấy danh sách phương thức thanh toán
        $sql = "SELECT * FROM topup_methods ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Lấy metadata cho mỗi phương thức
        foreach ($methods as &$method) {
            $meta_sql = "SELECT meta_key, meta_value FROM topup_metadata WHERE topup_id = ?";
            $meta_stmt = $conn->prepare($meta_sql);
            $meta_stmt->execute([$method['id']]);
            $method['metadata'] = $meta_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode(['success' => true, 'data' => $methods]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addPaymentMethod() {
    global $conn;
    
    try {
        $conn->beginTransaction();
        
        $name = $_POST['name'];
        $display_name = $_POST['display_name'];
        $status = (int)$_POST['status'];
        $metadata = json_decode($_POST['metadata'], true);
        
        // Thêm phương thức thanh toán
        $sql = "INSERT INTO topup_methods (name, display_name, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $display_name, $status]);
        $method_id = $conn->lastInsertId();
        
        // Thêm metadata
        if (!empty($metadata)) {
            $meta_sql = "INSERT INTO topup_metadata (topup_id, meta_key, meta_value) VALUES (?, ?, ?)";
            $meta_stmt = $conn->prepare($meta_sql);
            
            foreach ($metadata as $meta) {
                $meta_stmt->execute([$method_id, $meta['key'], $meta['value']]);
            }
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Đã thêm phương thức thanh toán']);
        
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updatePaymentMethod() {
    global $conn;
    
    try {
        $conn->beginTransaction();
        
        $id = (int)$_POST['id'];
        $name = $_POST['name'];
        $display_name = $_POST['display_name'];
        $status = (int)$_POST['status'];
        $metadata = json_decode($_POST['metadata'], true);
        
        // Cập nhật phương thức thanh toán
        $sql = "UPDATE topup_methods SET name = ?, display_name = ?, status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $display_name, $status, $id]);
        
        // Xóa metadata cũ và thêm mới
        $delete_sql = "DELETE FROM topup_metadata WHERE topup_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->execute([$id]);
        
        if (!empty($metadata)) {
            $meta_sql = "INSERT INTO topup_metadata (topup_id, meta_key, meta_value) VALUES (?, ?, ?)";
            $meta_stmt = $conn->prepare($meta_sql);
            
            foreach ($metadata as $meta) {
                $meta_stmt->execute([$id, $meta['key'], $meta['value']]);
            }
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Đã cập nhật phương thức thanh toán']);
        
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deletePaymentMethod() {
    global $conn;
    
    try {
        $id = (int)$_POST['id'];
        
        // Kiểm tra xem có giao dịch nào đang sử dụng không
        $check_sql = "SELECT COUNT(*) FROM topup_transactions WHERE topup_method_id = ? AND status = 0";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$id]);
        $pending_count = $check_stmt->fetchColumn();
        
        if ($pending_count > 0) {
            echo json_encode(['success' => false, 'message' => 'Không thể xóa phương thức có giao dịch đang chờ duyệt']);
            return;
        }
        
        $conn->beginTransaction();
        
        // Xóa metadata trước
        $delete_meta_sql = "DELETE FROM topup_metadata WHERE topup_id = ?";
        $delete_meta_stmt = $conn->prepare($delete_meta_sql);
        $delete_meta_stmt->execute([$id]);
        
        // Xóa phương thức thanh toán
        $delete_sql = "DELETE FROM topup_methods WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->execute([$id]);
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Đã xóa phương thức thanh toán']);
        
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function toggleMethodStatus() {
    global $conn;
    
    try {
        $id = (int)$_POST['id'];
        
        $sql = "UPDATE topup_methods SET status = 1 - status, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Đã thay đổi trạng thái phương thức']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getRecentTransactions() {
    global $conn;
    
    try {
        $sql = "SELECT 
                    tt.id,
                    tt.user_id,
                    tt.amount,
                    tt.status,
                    tt.created_at,
                    tm.display_name as method_name,
                    u.username
                FROM topup_transactions tt
                LEFT JOIN topup_methods tm ON tt.topup_method_id = tm.id
                LEFT JOIN users u ON tt.user_id = u.id
                ORDER BY tt.created_at DESC
                LIMIT 10";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $transactions]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getStats() {
    global $conn;
    
    try {
        // Tổng số phương thức
        $total_methods_sql = "SELECT COUNT(*) FROM topup_methods";
        $total_methods = $conn->query($total_methods_sql)->fetchColumn();
        
        // Phương thức đang hoạt động
        $active_methods_sql = "SELECT COUNT(*) FROM topup_methods WHERE status = 1";
        $active_methods = $conn->query($active_methods_sql)->fetchColumn();
        
        // Giao dịch chờ duyệt
        $pending_transactions_sql = "SELECT COUNT(*) FROM topup_transactions WHERE status = 0";
        $pending_transactions = $conn->query($pending_transactions_sql)->fetchColumn();
        
        // Doanh thu hôm nay
        $today_revenue_sql = "SELECT COALESCE(SUM(amount), 0) FROM topup_transactions 
                             WHERE status = 1 AND DATE(created_at) = CURDATE()";
        $today_revenue = $conn->query($today_revenue_sql)->fetchColumn();
        
        $stats = [
            'total_methods' => $total_methods,
            'active_methods' => $active_methods,
            'pending_transactions' => $pending_transactions,
            'today_revenue' => $today_revenue
        ];
        
        echo json_encode(['success' => true, 'data' => $stats]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lịch sử nạp tiền</h6>
    </div>
    <div class="card-body">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Chờ duyệt</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingTopups">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đã duyệt</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="approvedTopups">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Từ chối</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="rejectedTopups">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tổng tiền hôm nay</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalTopupAmount">0đ</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Trạng thái:</label>
                <select class="form-control" id="statusFilter">
                    <option value="">Tất cả</option>
                    <option value="0">Chờ duyệt</option>
                    <option value="1">Đã duyệt</option>
                    <option value="2">Từ chối</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Từ ngày:</label>
                <input type="date" class="form-control" id="dateFrom">
            </div>
            <div class="col-md-3">
                <label>Đến ngày:</label>
                <input type="date" class="form-control" id="dateTo">
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <div>
                    <button class="btn btn-primary" id="refreshBtn">
                        <i class="fas fa-sync-alt"></i> Làm mới
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên người nạp</th>
                        <th>Số tiền</th>
                        <th>Phương thức</th>
                        <th>Minh chứng</th>
                        <th>Ngày thực hiện</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal" id="modalPreview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-colorful">
                <h5 class="modal-title">Preview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="display: flex; justify-content: center;">
                <img style="height: calc(100% - 50px)" id="img-modal-preview" src="">
            </div>
        </div>
    </div>
</div>

<?php admin_footer(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">

<script>
    function render_status(s) {
        if (s == 2) return `<span class="badge badge-danger">Từ chối</span>`;
        if (s == 1) return `<span class="badge badge-success">Đã duyệt</span>`;
        if (s == 0) return `<span class="badge badge-warning">Chờ duyệt</span>`;
        return "Unknown";
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    function loadStats() {
        $.ajax({
            url: 'ajax/topup-history-db.php',
            type: 'POST',
            data: { action_type: 'get_stats' },
            success: function(response) {
                if (response.success) {
                    $('#pendingTopups').text(response.data.pending || 0);
                    $('#approvedTopups').text(response.data.approved || 0);
                    $('#rejectedTopups').text(response.data.rejected || 0);
                    $('#totalTopupAmount').text(formatMoney(response.data.total_today || 0));
                }
            }
        });
    }

    $(document).ready(function() {
        const dbtable = $("#dataTable").DataTable({
            ajax: {
                url: "ajax/topup-history-db.php",
                dataSrc: 'data'
            },
            order: [[0, 'desc']],
            processing: true,
            serverSide: true,
            serverMethod: 'post',
            columns: [
                { data: 'id' },
                { data: 'username' },
                { 
                    data: 'amount',
                    render: (data) => formatMoney(data)
                },
                { data: 'topup_type' },
                {
                    data: 'proof',
                    render: (data, _, row) => data ? `<img width='100' src='${data}' class="img_viewer" style="cursor: pointer;">` : 'Không có'
                },
                {
                    data: 'date',
                    render: (data, type, row) => moment.unix(data).format("DD/MM/YYYY HH:mm:ss")
                },
                {
                    data: 'status',
                    render: render_status
                },
                {
                    data: 'id',
                    render: (data, type, row) => {
                        if (row.status == 0) {
                            return `<div class="btn-group">
                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectTopup(${data})">Từ chối</button>
                                <button type="button" class="btn btn-success btn-sm" onclick="approveTopup(${data})">Chấp nhận</button>
                            </div>`;
                        } else if (row.status == 2) {
                            return `<textarea class="form-control" readonly>${row.refuse_reason || "Từ chối"}</textarea>`;
                        } else {
                            return '<span class="text-success">Đã xử lý</span>';
                        }
                    }
                }
            ],
            language: {
                search: "Tìm kiếm:",
                lengthMenu: "Hiển thị _MENU_ mục",
                info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                paginate: {
                    first: "Đầu",
                    last: "Cuối",
                    next: "Tiếp", 
                    previous: "Trước"
                }
            }
        });

        // Load stats
        loadStats();

        // Image viewer
        $(document).on("click", ".img_viewer", function() {
            $("#img-modal-preview").attr("src", $(this).attr("src"));
            $("#modalPreview").modal("show");
        });

        // Refresh button
        $('#refreshBtn').click(function() {
            loadStats();
            dbtable.ajax.reload();
        });

        // Filter changes
        $('#statusFilter, #dateFrom, #dateTo').change(function() {
            dbtable.ajax.reload();
        });

        // Auto refresh every 30 seconds
        setInterval(function() {
            loadStats();
            dbtable.ajax.reload(null, false);
        }, 30000);
    });

    function rejectTopup(id) {
        let reason = prompt("Nhập lý do từ chối (có thể bỏ trống):");
        
        $.ajax({
            url: 'ajax/topup-history-db.php',
            type: 'POST',
            data: {
                action_type: "refuse_topup",
                id: id,
                reason: reason || 'Từ chối nạp tiền'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success("Từ chối thành công");
                    $("#dataTable").DataTable().ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message || "Lỗi không xác định");
                }
            },
            error: function() {
                toastr.error('Lỗi kết nối');
            }
        });
    }

    function approveTopup(id) {
        if (!confirm('Bạn có chắc muốn duyệt yêu cầu này?')) return;

        $.ajax({
            url: 'ajax/topup-history-db.php',
            type: 'POST',
            data: {
                action_type: "accept_topup",
                id: id
            },
            success: function(response) {
                if (response.success) {
                    toastr.success("Duyệt thành công");
                    $("#dataTable").DataTable().ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message || "Lỗi không xác định");
                }
            },
            error: function() {
                toastr.error('Lỗi kết nối');
            }
        });
    }

    // Configure toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3000
    };
</script>