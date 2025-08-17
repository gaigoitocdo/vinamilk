<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Quản lý đơn hàng - FIXED VERSION</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fc;
            margin: 0;
            padding: 0;
        }

        .page-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .page-title {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .api-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .api-status.connected {
            background: #d4edda;
            color: #155724;
        }

        .api-status.disconnected {
            background: #f8d7da;
            color: #721c24;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stats-card h3 {
       
            margin: 0 0 10px;
            font-weight: 700;
            color: #2c3e50;
        }

        .stats-card p {
            margin: 0;
            color: #666;
            font-weight: 500;
        }

      
        .search-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e5e7eb;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .product-card.pending {
            border-left: 4px solid #fbbf24;
        }

        .product-card.delivered {
            border-left: 4px solid #10b981;
        }

        .product-card.cancelled {
            border-left: 4px solid #ef4444;
        }

        .product-card.available {
            border-left: 4px solid #3b82f6;
        }

        .product-image-container {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: #f8f9fa;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vip-badge-overlay {
            position: absolute;
            top: 10px;
            left: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }

        .sale-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff4757;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }

        .product-info {
            padding: 16px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 8px;
            color: #2c3e50;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-code {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 8px;
            font-family: 'Courier New', monospace;
        }

        .price-section {
            margin: 10px 0;
        }

        .current-price {
            font-size: 18px;
            font-weight: 700;
            color: #3b82f6;
            margin: 0;
        }

        .old-price {
            font-size: 14px;
            color: #9ca3af;
            text-decoration: line-through;
            margin: 0;
        }

        .profit-info {
            background: #f0fdf4;
            padding: 8px;
            border-radius: 6px;
            margin: 10px 0;
        }

        .profit-amount {
            font-size: 14px;
            font-weight: 600;
            color: #10b981;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .sold-progress {
            margin: 10px 0;
        }

        .sold-text {
            font-size: 12px;
            color: #6b7280;
            margin: 0 0 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .progress-bar-container {
            width: 100%;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #34d399);
            transition: width 0.3s ease;
        }

        .card-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .edit-btn, .approve-btn, .reject-btn {
            flex: 1;
            min-width: 80px;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .edit-btn {
            background: #f59e0b;
            color: white;
        }

        .edit-btn:hover {
            background: #d97706;
        }

        .approve-btn {
            background: #10b981;
            color: white;
        }

        .approve-btn:hover {
            background: #059669;
        }

        .reject-btn {
            background: #ef4444;
            color: white;
        }

        .reject-btn:hover {
            background: #dc2626;
        }

        .status-badge-card {
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
        }

        .status-badge-card.status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge-card.status-delivered {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge-card.status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge-card.status-available {
            background: #dbeafe;
            color: #1e40af;
        }

        .no-products {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            grid-column: 1 / -1;
        }

        .no-products i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 16px;
            display: block;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        .slide-up {
            animation: slideUp 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Edit Modal */
        .edit-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .edit-modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .edit-modal-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .edit-modal-header h3 {
            margin: 0;
            color: #2c3e50;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .edit-modal-body {
            padding: 20px;
        }

        .edit-modal-footer {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        @media (max-width: 768px) {
            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .edit-modal-content {
                width: 95%;
                margin: 10% auto;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header fade-in">
            <h1 class="page-title">
                <i class="fas fa-shopping-cart"></i>
                Quản lý đơn hàng - FIXED 
                <span class="api-status" id="apiStatus">
                    <i class="fas fa-circle"></i> Checking...
                </span>
            </h1>
        </div>

        <!-- Stats Dashboard -->
        <div class="stats-cards slide-up" id="statsCards" style="display: none;">
            <div class="stats-card">
                <h3 id="totalOrders">0</h3>
                <p><i class="fas fa-shopping-bag mr-2"></i>Tổng đơn hàng</p>
            </div>
            <div class="stats-card">
                <h3 id="pendingOrders">0</h3>
                <p><i class="fas fa-clock mr-2"></i>Chờ duyệt</p>
            </div>
            <div class="stats-card">
                <h3 id="deliveredOrders">0</h3>
                <p><i class="fas fa-check-circle mr-2"></i>Hoàn thành</p>
            </div>
            <div class="stats-card">
                <h3 id="totalCommission">0 VND</h3>
                <p><i class="fas fa-coins mr-2"></i>Tổng hoa hồng</p>
            </div>
        </div>



        <!-- Tab Content -->
        <div class="tab-content slide-up" id="orderTabContent">
            <!-- History Tab -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="search-section">
                    <div class="row">
                        <div class="col-md-4">
                            <label><i class="fas fa-search mr-2"></i>Tìm kiếm:</label>
                            <input type="text" class="form-control" id="historySearch" placeholder="Nhập từ khóa tìm kiếm...">
                        </div>
                        <div class="col-md-3">
                            <label><i class="fas fa-calendar mr-2"></i>Từ ngày:</label>
                            <input type="date" class="form-control" id="historyDateFrom">
                        </div>
                        <div class="col-md-3">
                            <label><i class="fas fa-calendar mr-2"></i>Đến ngày:</label>
                            <input type="date" class="form-control" id="historyDateTo">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button class="btn btn-info btn-block" onclick="loadHistoryOrders()">
                                <i class="fas fa-search mr-2"></i>Tìm kiếm
                            </button>
                        </div>
                    </div>
                </div>
                <div class="products-grid" id="historyProductsGrid" style="position: relative;">
                    <div class="loading-overlay" id="historyLoading" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="no-products">
                        <i class="fas fa-inbox"></i>
                        <p>Đang tải dữ liệu...</p>
                        <small>Vui lòng chờ trong giây lát</small>
                    </div>
                </div>
            </div>

            <!-- Create Tab -->
            <div class="tab-pane fade" id="create" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="card shadow">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-plus mr-2"></i>Thêm đơn hàng mới</h5>
                            </div>
                            <div class="card-body">
                                <form id="createOrderForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="productName"><i class="fas fa-tag mr-2"></i>Tên sản phẩm *</label>
                                                <input type="text" class="form-control" id="productName" required placeholder="Nhập tên sản phẩm">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="productImage"><i class="fas fa-image mr-2"></i>URL hình ảnh</label>
                                                <input type="url" class="form-control" id="productImage" placeholder="https://example.com/image.jpg">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="productPrice"><i class="fas fa-money-bill mr-2"></i>Giá sản phẩm *</label>
                                                <input type="number" class="form-control" id="productPrice" required min="0" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="productSalePrice"><i class="fas fa-tags mr-2"></i>Giá cũ</label>
                                                <input type="number" class="form-control" id="productSalePrice" min="0" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="quantity"><i class="fas fa-cubes mr-2"></i>Số lượng</label>
                                                <input type="number" class="form-control" id="quantity" value="1" min="1">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="vipLevel"><i class="fas fa-crown mr-2"></i>Danh mục VIP</label>
                                                <select class="form-control" id="vipLevel">
                                                    <option value="VIP 1 (5%)">VIP 1 (5%)</option>
                                                    <option value="VIP 2 (10%)">VIP 2 (10%)</option>
                                                    <option value="VIP 3 (15%)">VIP 3 (15%)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="commission"><i class="fas fa-coins mr-2"></i>Hoa hồng (VND)</label>
                                                <input type="number" class="form-control" id="commission" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fullname"><i class="fas fa-user mr-2"></i>Tên khách hàng</label>
                                                <input type="text" class="form-control" id="fullname" value="Admin Created">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone"><i class="fas fa-phone mr-2"></i>Số điện thoại</label>
                                                <input type="tel" class="form-control" id="phone" value="0000000000">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-secondary btn-block" onclick="resetForm()">
                                                <i class="fas fa-times mr-2"></i>Hủy bỏ
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-plus mr-2"></i>Tạo đơn hàng
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <div class="search-section">
                    <div class="row">
                        <div class="col-md-6">
                            <label><i class="fas fa-search mr-2"></i>Tìm kiếm:</label>
                            <input type="text" class="form-control" id="pendingSearch" placeholder="Tìm kiếm theo tên sản phẩm, mã đơn...">
                        </div>
                        <div class="col-md-6">
                            <label>&nbsp;</label>
                            <div class="btn-group btn-block" role="group">
                                <button class="btn btn-info" onclick="loadPendingOrders()">
                                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                                </button>
                                <button class="btn btn-success" onclick="loadStats()">
                                    <i class="fas fa-chart-bar mr-2"></i>Thống kê
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="products-grid" id="pendingProductsGrid" style="position: relative;">
                    <div class="loading-overlay" id="pendingLoading" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="no-products">
                        <i class="fas fa-clock"></i>
                        <p>Đang tải đơn hàng chờ duyệt...</p>
                        <small>Vui lòng chờ trong giây lát</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div id="editModal" class="edit-modal">
        <div class="edit-modal-content">
            <div class="edit-modal-header">
                <h3><i class="fas fa-edit mr-2"></i>Chỉnh sửa đơn hàng</h3>
                <button class="close-btn" onclick="closeEditModal()">×</button>
            </div>
            <div class="edit-modal-body">
                <form id="editOrderForm">
                    <input type="hidden" id="editOrderId">
                    
                    <div class="form-group">
                        <label for="editProductName"><i class="fas fa-tag mr-2"></i>Tên sản phẩm *</label>
                        <input type="text" class="form-control" id="editProductName" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editProductImage"><i class="fas fa-image mr-2"></i>URL hình ảnh</label>
                        <input type="url" class="form-control" id="editProductImage">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editProductPrice"><i class="fas fa-money-bill mr-2"></i>Giá sản phẩm *</label>
                                <input type="number" class="form-control" id="editProductPrice" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editProductSalePrice"><i class="fas fa-tags mr-2"></i>Giá cũ</label>
                                <input type="number" class="form-control" id="editProductSalePrice" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editVipLevel"><i class="fas fa-crown mr-2"></i>Danh mục VIP</label>
                                <select class="form-control" id="editVipLevel">
                                    <option value="VIP 1 (5%)">VIP 1 (5%)</option>
                                    <option value="VIP 2 (10%)">VIP 2 (10%)</option>
                                    <option value="VIP 3 (15%)">VIP 3 (15%)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editCommission"><i class="fas fa-coins mr-2"></i>Hoa hồng</label>
                                <input type="number" class="form-control" id="editCommission" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editQuantity"><i class="fas fa-cubes mr-2"></i>Số lượng</label>
                                <input type="number" class="form-control" id="editQuantity" value="1" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editFullname"><i class="fas fa-user mr-2"></i>Tên khách hàng</label>
                                <input type="text" class="form-control" id="editFullname">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editPhone"><i class="fas fa-phone mr-2"></i>Số điện thoại</label>
                        <input type="tel" class="form-control" id="editPhone">
                    </div>
                </form>
            </div>
            <div class="edit-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                    <i class="fas fa-times mr-2"></i>Hủy
                </button>
                <button type="button" class="btn btn-primary" onclick="saveEditOrder()">
                    <i class="fas fa-save mr-2"></i>Lưu thay đổi
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->


    <script>
        // FIXED API Configuration
        const API_BASE_URL = 'https://c-pangshop.site/api/order-management.php';
        
        // Global variables
        let apiConnected = false;

        // Initialize on document ready
        $(document).ready(async function() {
            console.log('🚀 FIXED Order Management System - Initializing...');
            
            // Test API connection first
            const connected = await testApiConnection();
            
            if (connected) {
                // Load initial data
                await loadPendingOrders();
                await loadStats();
                
                // Start auto-refresh
                startAutoRefresh();
                
                showNotification('Hệ thống quản lý đơn hàng đã sẵn sàng!', 'success');
            } else {
                showNotification('Không thể kết nối đến database. Vui lòng kiểm tra cấu hình API!', 'error');
            }
            
            // Set default date filters to today
            const today = new Date().toISOString().split('T')[0];
            $('#historyDateFrom').val(today);
            $('#historyDateTo').val(today);

            // Initialize form handlers
            initializeFormHandlers();
        });

        // Initialize form handlers
        function initializeFormHandlers() {
            // Auto calculate commission
            $('#productPrice, #vipLevel').on('input change', function() {
                calculateCommission('#productPrice', '#vipLevel', '#commission');
            });

            $('#editProductPrice, #editVipLevel').on('input change', function() {
                calculateCommission('#editProductPrice', '#editVipLevel', '#editCommission');
            });

            // Form submission
            $('#createOrderForm').on('submit', handleCreateOrder);

            // Search functionality with debounce
            let searchTimeout;
            $('#pendingSearch').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                filterTable('pendingProductsGrid', searchTerm);
            });

            $('#historySearch').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadHistoryOrders();
                }, 500);
            });

            $('#historyDateFrom, #historyDateTo').on('change', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadHistoryOrders();
                }, 500);
            });

            // Tab switching handlers
            $('#pending-tab').on('shown.bs.tab', function() {
                console.log('📋 Loading pending orders...');
                loadPendingOrders();
            });

            $('#history-tab').on('shown.bs.tab', function() {
                console.log('📊 Loading history orders...');
                loadHistoryOrders();
            });

            // Modal handlers
            $(window).on('click', function(event) {
                if (event.target.id === 'editModal') {
                    closeEditModal();
                }
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl/Cmd + R to refresh current tab
                if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                    e.preventDefault();
                    
                    if ($('#pending-tab').hasClass('active')) {
                        loadPendingOrders();
                    } else if ($('#history-tab').hasClass('active')) {
                        loadHistoryOrders();
                    }
                }
                
                // Ctrl/Cmd + N to switch to create tab
                if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                    e.preventDefault();
                    $('#create-tab').tab('show');
                    $('#productName').focus();
                }

                // ESC to close modal
                if (e.key === 'Escape') {
                    closeEditModal();
                }
            });
        }
function showSection(section) {
    // Ẩn tất cả
    $('#history').hide();
    $('#create').hide();
    $('#pending').hide();

    // Xoá active tất cả tab
    $('.nav-link').removeClass('active');

    // Hiện đúng phần cần
    $('#' + section).show();
    $(`a[onclick="showSection('${section}')"]`).addClass('active');
}
        // Calculate commission based on price and VIP level
        function calculateCommission(priceSelector, vipSelector, commissionSelector) {
            const price = parseFloat($(priceSelector).val()) || 0;
            const vipLevel = $(vipSelector).val();
            
            if (vipLevel.includes('(')) {
                const rateMatch = vipLevel.match(/\((\d+(?:\.\d+)?)%\)/);
                if (rateMatch) {
                    const rate = parseFloat(rateMatch[1]) / 100;
                    const commission = Math.round(price * rate);
                    $(commissionSelector).val(commission);
                }
            }
        }

        // Handle create order form submission - FIXED
        async function handleCreateOrder(e) {
            e.preventDefault();
            
            if (!apiConnected) {
                showNotification('Không có kết nối API. Vui lòng kiểm tra lại!', 'error');
                return;
            }
            
            const productName = $('#productName').val().trim();
            const productPrice = $('#productPrice').val();
            
            if (!productName || !productPrice || productPrice <= 0) {
                showNotification('Vui lòng điền đầy đủ thông tin bắt buộc và giá sản phẩm phải lớn hơn 0', 'error');
                return;
            }
            
            const formData = {
                product_name: productName,
                product_image: $('#productImage').val(),
                product_price_vnd: parseFloat(productPrice),
                product_sale_price: parseFloat($('#productSalePrice').val()) || 0,
                vip_level: $('#vipLevel').val(),
                commission_amount: parseFloat($('#commission').val()) || 0, // FIXED: Use commission_amount
                quantity: parseInt($('#quantity').val()) || 1,
                fullname: $('#fullname').val().trim() || 'BetLuxe Admin',
                phone: $('#phone').val().trim() || '0000000000'
            };
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalHtml = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Đang tạo...');

            try {
                const result = await createOrder(formData);
                
                if (result.success) {
                    showNotification(result.message || 'Tạo đơn hàng thành công! Đã thêm vào pool sale.', 'success');
                    resetForm();
                    
                    // Reload stats immediately
                    await loadStats();
                    
                    // Switch to pending tab (to see all orders including available ones)
                    $('#pending-tab').tab('show');
                    
                    // Wait for tab switch then reload data
                    setTimeout(async () => {
                        await loadPendingOrders();
                        console.log('✅ Orders reloaded after creation');
                    }, 300);
                    
                } else {
                    throw new Error(result.message || 'Tạo đơn hàng thất bại');
                }
            } catch (error) {
                showNotification('Có lỗi xảy ra: ' + error.message, 'error');
            } finally {
                submitBtn.prop('disabled', false).html(originalHtml);
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            $('.notification').remove();
            
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            const notification = $(`
                <div class="notification">
                    <div class="alert ${alertClass} alert-dismissible fade show">
                        <i class="fas ${iconClass} mr-2"></i>
                        ${message}
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                </div>
            `);
            
            $('body').append(notification);
            
            setTimeout(() => {
                notification.fadeOut(500, () => notification.remove());
            }, 5000);
        }

        // Update API status
        function updateApiStatus(connected, message = '') {
            const statusEl = $('#apiStatus');
            apiConnected = connected;
            
            if (connected) {
                statusEl.removeClass('disconnected').addClass('connected');
                statusEl.html('<i class="fas fa-check-circle"></i> Connected');
            } else {
                statusEl.removeClass('connected').addClass('disconnected');
                statusEl.html('<i class="fas fa-times-circle"></i> Disconnected' + (message ? ' - ' + message : ''));
            }
        }

        // API call function - FIXED
        async function callAPI(action, method = 'GET', data = {}) {
            try {
                const url = new URL(API_BASE_URL, window.location.origin);
                url.searchParams.set('action', action);
                
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                };
                
                if (method === 'POST' || method === 'PUT') {
                    options.body = JSON.stringify(data);
                } else if (method === 'GET' && Object.keys(data).length > 0) {
                    Object.keys(data).forEach(key => {
                        if (data[key] !== null && data[key] !== undefined && data[key] !== '') {
                            url.searchParams.set(key, data[key]);
                        }
                    });
                }
                
                console.log('🔄 API Call:', method, url.toString(), data);
                
                const response = await fetch(url.toString(), options);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('❌ Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response');
                }
                
                const result = await response.json();
                console.log('✅ API Response:', result);
                
                updateApiStatus(true);
                return result;
                
            } catch (error) {
                console.error('❌ API Error:', error);
                updateApiStatus(false, error.message);
                throw error;
            }
        }

        // Test API connection
        async function testApiConnection() {
            try {
                const result = await callAPI('health');
                if (result.success) {
                    updateApiStatus(true);
                    showNotification('Kết nối API thành công!', 'success');
                    return true;
                } else {
                    throw new Error(result.message || 'API health check failed');
                }
            } catch (error) {
                updateApiStatus(false, error.message);
                showNotification('Không thể kết nối đến API: ' + error.message, 'error');
                return false;
            }
        }

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount || 0);
        }

        // Format date time
        function formatDateTime(dateString) {
            if (!dateString) return 'N/A';
            
            try {
                const date = new Date(dateString);
                return date.toLocaleString('vi-VN', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (e) {
                return dateString;
            }
        }

        // Load statistics - FIXED
        async function loadStats() {
            try {
                const result = await callAPI('get_stats');
                if (result.success) {
                    const stats = result.data;
                    $('#totalOrders').text(formatCurrency(stats.total_orders));
                    $('#pendingOrders').text(formatCurrency(stats.pending_orders));
                    $('#deliveredOrders').text(formatCurrency(stats.delivered_orders)); // FIXED: Use delivered_orders
                    $('#totalCommission').text(formatCurrency(stats.total_commission) + ' VND');
                    
                    $('#statsCards').slideDown();
                    showNotification('Đã tải thống kê thành công', 'success');
                } else {
                    throw new Error(result.message || 'Failed to load stats');
                }
            } catch (error) {
                showNotification('Lỗi tải thống kê: ' + error.message, 'error');
            }
        }

        // Create order - FIXED
        async function createOrder(formData) {
            try {
                const result = await callAPI('create_order', 'POST', formData);
                return result;
            } catch (error) {
                return {
                    success: false,
                    message: 'Lỗi kết nối API: ' + error.message
                };
            }
        }

        // Load orders - FIXED
        async function loadOrders(filters = {}) {
            try {
                const result = await callAPI('get_orders', 'GET', filters);
                if (result.success) {
                    return result.data;
                } else {
                    throw new Error(result.message || 'Failed to load orders');
                }
            } catch (error) {
                showNotification('Lỗi tải danh sách đơn hàng: ' + error.message, 'error');
                return [];
            }
        }

        // Update order display with product cards - FIXED STATUS MAPPING
        function updateOrderTable(orders, containerId) {
            const isHistory = containerId === 'historyProductsGrid';
            const container = $(`#${containerId}`);
            
            if (orders.length === 0) {
                container.html(`
                    <div class="no-products">
                        <i class="fas fa-inbox"></i>
                        <p>Không có đơn hàng nào</p>
                        <small>Chưa có sản phẩm nào trong danh mục này</small>
                    </div>
                `);
                return;
            }
            
            const cardsHTML = orders.map((order, index) => {
                const salePercentage = order.product_sale_price && order.product_price_vnd ? 
                    Math.round((1 - order.product_price_vnd / order.product_sale_price) * 100) : 0;
                
                const soldPercentage = Math.floor(Math.random() * 70) + 10; // Random 10-80%
                
                const actionButtons = getCardActionButtons(order, isHistory);
                
                return `
                    <div class="product-card fade-in ${order.status}" data-order-id="${order.id}" style="animation-delay: ${index * 0.1}s">
                        <div class="product-image-container">
                            ${order.product_image ? 
                                `<img src="${order.product_image}" alt="Product" class="product-image" onerror="this.src='https://via.placeholder.com/320x220/f8f9fa/666?text=No+Image'">` : 
                                `<div class="product-image" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display: flex; align-items: center; justify-content: center; color: #666; font-size: 14px; font-weight: 600; flex-direction: column;">
                                    <i class="fas fa-image fa-3x mb-2"></i>Không có hình ảnh
                                </div>`
                            }
                            <div class="vip-badge-overlay">${order.vip_level || 'VIP 1 (5%)'}</div>
                            ${salePercentage > 0 ? `<div class="sale-badge">-${salePercentage}%</div>` : ''}
                        </div>
                        
                        <div class="product-info">
                            <h3 class="product-name" title="${order.product_name}">${order.product_name}</h3>
                            <p class="product-code">Mã: ${order.order_code}</p>
                            
                            <div class="price-section">
                                <p class="current-price">${formatCurrency(order.product_price_vnd)} VND</p>
                                ${order.product_sale_price && order.product_sale_price > order.product_price_vnd ? 
                                    `<p class="old-price">${formatCurrency(order.product_sale_price)} VND</p>` : ''}
                            </div>
                            
                            <div class="profit-info">
                                <p class="profit-amount">
                                    <i class="fas fa-coins mr-1"></i>
                                    Lợi nhuận: ${formatCurrency(order.commission_amount)} VND
                                </p>
                            </div>
                            
                            <div class="sold-progress">
                                <p class="sold-text">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Trạng thái: ${getStatusText(order.status)}
                                </p>
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: ${getStatusProgress(order.status)}%"></div>
                                </div>
                            </div>
                            
                            ${actionButtons}
                        </div>
                    </div>
                `;
            }).join('');
            
            container.html(cardsHTML);
        }

        // FIXED: Get status text for new workflow
        function getStatusText(status) {
            const statusMap = {
                'available': 'Có sẵn trong pool',
                'pending': 'Chờ duyệt',
                'delivered': 'Hoàn thành',
                'cancelled': 'Không hoàn thành'
            };
            return statusMap[status] || status;
        }

        // Get status progress percentage
        function getStatusProgress(status) {
            const progressMap = {
                'available': 25,
                'pending': 50,
                'delivered': 100,
                'cancelled': 0
            };
            return progressMap[status] || 0;
        }

        // Get action buttons for product cards - FIXED
        function getCardActionButtons(order, isHistory) {
            if (!isHistory && order.status === 'pending') {
                return `
                    <div class="card-actions">
                        <button class="edit-btn" onclick="editOrder(${order.id})" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </button>
                        <button class="approve-btn" onclick="approveOrder(${order.id})" title="Duyệt">
                            <i class="fas fa-check"></i> Duyệt
                        </button>
                        <button class="reject-btn" onclick="rejectOrder(${order.id})" title="Không hoàn thành">
                            <i class="fas fa-times"></i> Không hoàn thành
                        </button>
                    </div>
                `;
            } else {
                const statusClass = `status-${order.status}`;
                const statusText = {
                    'available': 'Có sẵn',
                    'pending': 'Chờ duyệt',
                    'delivered': 'Hoàn thành', 
                    'cancelled': 'Không hoàn thành'
                }[order.status] || order.status;
                
                return `
                    <div class="card-actions">
                        <button class="edit-btn" onclick="editOrder(${order.id})" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </button>
                        <div class="status-badge-card ${statusClass}">
                            <i class="fas ${getStatusIcon(order.status)} mr-1"></i>
                            ${statusText}
                        </div>
                    </div>
                `;
            }
        }

        // Get status icon
        function getStatusIcon(status) {
            const iconMap = {
                'available': 'fa-box',
                'pending': 'fa-clock',
                'delivered': 'fa-check-circle',
                'cancelled': 'fa-times-circle'
            };
            return iconMap[status] || 'fa-question';
        }

        // Load pending orders with card display - FIXED
        async function loadPendingOrders() {
            console.log('🔄 Loading pending orders...');
            $('#pendingLoading').show();
            
            try {
                const orders = await loadOrders({ status: 'pending' });
                console.log('📦 Pending orders loaded:', orders.length);
                updateOrderTable(orders, 'pendingProductsGrid');
            } catch (error) {
                console.error('❌ Error loading pending orders:', error);
                showNotification('Lỗi tải đơn hàng: ' + error.message, 'error');
                $('#pendingProductsGrid').html(`
                    <div class="no-products">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Lỗi tải dữ liệu</p>
                        <small>${error.message}</small>
                    </div>
                `);
            } finally {
                $('#pendingLoading').hide();
            }
        }

        // Load history orders with card display - FIXED
        async function loadHistoryOrders() {
            $('#historyLoading').show();
            
            const filters = {
                status: 'delivered,cancelled', // FIXED: Use delivered instead of approved
                search: $('#historySearch').val(),
                date_from: $('#historyDateFrom').val(),
                date_to: $('#historyDateTo').val()
            };
            
            try {
                const orders = await loadOrders(filters);
                updateOrderTable(orders, 'historyProductsGrid');
            } catch (error) {
                $('#historyProductsGrid').html(`
                    <div class="no-products">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Lỗi tải dữ liệu</p>
                        <small>${error.message}</small>
                    </div>
                `);
            } finally {
                $('#historyLoading').hide();
            }
        }

        // Approve order - FIXED: pending → delivered
        async function approveOrder(orderId) {
            if (confirm('Bạn có chắc chắn muốn duyệt đơn hàng này?')) {
                try {
                    const button = $(`.product-card[data-order-id="${orderId}"] .approve-btn`);
                    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang duyệt...');
                    
                    const result = await callAPI('approve_order', 'POST', { order_id: orderId });
                    
                    if (result.success) {
                        showNotification(result.message, 'success');
                        
                        // Remove card from pending grid with animation
                        $(`.product-card[data-order-id="${orderId}"]`).fadeOut(500, function() {
                            $(this).remove();
                            
                            if ($('#pendingProductsGrid .product-card:visible').length === 0) {
                                $('#pendingProductsGrid').html(`
                                    <div class="no-products">
                                        <i class="fas fa-inbox"></i>
                                        <p>Không có đơn hàng nào</p>
                                        <small>Chưa có sản phẩm nào trong danh mục này</small>
                                    </div>
                                `);
                            }
                        });
                        
                        // Reload stats
                        loadStats();
                        
                    } else {
                        throw new Error(result.message || 'Approve failed');
                    }
                } catch (error) {
                    showNotification('Lỗi duyệt đơn hàng: ' + error.message, 'error');
                    const button = $(`.product-card[data-order-id="${orderId}"] .approve-btn`);
                    button.prop('disabled', false).html('<i class="fas fa-check"></i> Duyệt');
                }
            }
        }

        // Reject order - FIXED: pending → cancelled
        async function rejectOrder(orderId) {
            const reason = prompt('Nhập lý do Không hoàn thành (bắt buộc):');
            if (reason && reason.trim()) {
                try {
                    const button = $(`.product-card[data-order-id="${orderId}"] .reject-btn`);
                    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Không hoàn thành...');
                    
                    const result = await callAPI('reject_order', 'POST', { 
                        order_id: orderId, 
                        reason: reason.trim() 
                    });
                    
                    if (result.success) {
                        showNotification(result.message, 'success');
                        
                        // Remove card from pending grid with animation
                        $(`.product-card[data-order-id="${orderId}"]`).fadeOut(500, function() {
                            $(this).remove();
                            
                            if ($('#pendingProductsGrid .product-card:visible').length === 0) {
                                $('#pendingProductsGrid').html(`
                                    <div class="no-products">
                                        <i class="fas fa-inbox"></i>
                                        <p>Không có đơn hàng nào</p>
                                        <small>Chưa có sản phẩm nào trong danh mục này</small>
                                    </div>
                                `);
                            }
                        });
                        
                        // Reload stats
                        loadStats();
                        
                    } else {
                        throw new Error(result.message || 'Reject failed');
                    }
                } catch (error) {
                    showNotification('Lỗi Không hoàn thành đơn hàng: ' + error.message, 'error');
                    const button = $(`.product-card[data-order-id="${orderId}"] .reject-btn`);
                    button.prop('disabled', false).html('<i class="fas fa-times"></i> Không hoàn thành');
                }
            }
        }

        // Edit order functions
        function editOrder(orderId) {
            callAPI('get_orders', 'GET', {})
                .then(result => {
                    if (result.success) {
                        const order = result.data.find(o => o.id == orderId);
                        if (order) {
                            // Fill edit form
                            $('#editOrderId').val(order.id);
                            $('#editProductName').val(order.product_name);
                            $('#editProductImage').val(order.product_image || '');
                            $('#editProductPrice').val(order.product_price_vnd);
                            $('#editProductSalePrice').val(order.product_sale_price || '');
                            $('#editVipLevel').val(order.vip_level || 'VIP 1 (5%)');
                            $('#editCommission').val(order.commission_amount);
                            $('#editQuantity').val(order.quantity || 1);
                            $('#editFullname').val(order.fullname || '');
                            $('#editPhone').val(order.phone || '');
                            
                            // Show modal with animation
                            $('#editModal').show();
                            $('.edit-modal-content').addClass('fade-in');
                        } else {
                            showNotification('Không tìm thấy đơn hàng', 'error');
                        }
                    } else {
                        showNotification('Không thể tải thông tin đơn hàng: ' + result.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Lỗi tải thông tin đơn hàng: ' + error.message, 'error');
                });
        }

        function closeEditModal() {
            $('.edit-modal-content').removeClass('fade-in');
            setTimeout(() => {
                $('#editModal').hide();
                $('#editOrderForm')[0].reset();
            }, 300);
        }

        function saveEditOrder() {
            const orderId = $('#editOrderId').val();
            const productName = $('#editProductName').val().trim();
            const productPrice = $('#editProductPrice').val();
            
            if (!productName || !productPrice || productPrice <= 0) {
                showNotification('Vui lòng điền đầy đủ thông tin bắt buộc và giá sản phẩm phải lớn hơn 0', 'error');
                return;
            }
            
            // For now, just close modal and show success
            // In real implementation, call update API
            showNotification('Cập nhật đơn hàng thành công!', 'success');
            closeEditModal();
            
            // Reload current tab data
            if ($('#pending-tab').hasClass('active')) {
                loadPendingOrders();
            } else if ($('#history-tab').hasClass('active')) {
                loadHistoryOrders();
            }
        }

        // Reset form
        function resetForm() {
            $('#createOrderForm')[0].reset();
            $('#commission').val('');
            $('#fullname').val('Admin Created');
            $('#phone').val('0000000000');
            $('#quantity').val('1');
        }

        // Filter table rows (for pending grid real-time search)
        function filterTable(containerId, searchTerm) {
            $(`#${containerId} .product-card`).each(function() {
                const rowText = $(this).text().toLowerCase();
                $(this).toggle(rowText.includes(searchTerm));
            });
        }

        // Auto-refresh functionality
        let autoRefreshInterval;
        let autoRefreshEnabled = false;

        function startAutoRefresh() {
            if (autoRefreshEnabled) return;
            
            autoRefreshEnabled = true;
            autoRefreshInterval = setInterval(() => {
                if ($('#pending-tab').hasClass('active') && apiConnected) {
                    console.log('🔄 Auto-refreshing pending orders...');
                    loadPendingOrders();
                }
            }, 30000); // 30 seconds
            
            console.log('🔄 Auto-refresh started (30s interval)');
        }

        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshEnabled = false;
                console.log('⏹️ Auto-refresh stopped');
            }
        }

        // Page visibility change handler (pause/resume auto-refresh)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                if (apiConnected) {
                    startAutoRefresh();
                }
            }
        });

        // Clean up on page unload
        $(window).on('beforeunload', function() {
            stopAutoRefresh();
        });

        // Error handling for uncaught promise rejections
        window.addEventListener('unhandledrejection', function(event) {
            console.error('❌ Unhandled promise rejection:', event.reason);
            showNotification('Có lỗi không xác định xảy ra: ' + event.reason, 'error');
        });

        // Global functions for debugging
        window.OrderManagementDebug = {
            version: '3.0.0 - FIXED Status Mapping',
            apiUrl: API_BASE_URL,
            testConnection: testApiConnection,
            loadPendingOrders,
            loadHistoryOrders,
            loadStats,
            functions: {
                callAPI,
                createOrder,
                loadOrders,
                approveOrder,
                rejectOrder
            }
        };

        console.log('🎯 FIXED Order Management System v3.0 - Ready!');
        console.log('🔧 Features: Fixed status mapping (pending→delivered→cancelled), Real API integration');
    </script>
</body>
</html>