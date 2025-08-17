<?php
// ========== FILE: /admin/controllers/dashboard.php ==========
// Dashboard quản lý săn đơn hàng - Version với CSS inline

session_start();

// Include files
$navbar_path = __DIR__ . '/../navbar.php';
if (file_exists($navbar_path)) {
    include_once $navbar_path;
}

$database_path = __DIR__ . '/../../config/database.php';
if (file_exists($database_path)) {
    include_once $database_path;
}

// Mock data nếu không có database
$stats = [
    'today_hunts' => 15, 'today_orders' => 8, 'conversion_rate' => 53.3,
    'today_revenue' => 2450000, 'today_commission' => 98000, 'active_users' => 5,
    'active_products' => 12, 'month_hunts' => 287, 'month_orders' => 156, 'month_revenue' => 18750000
];

$recentActivity = [
    ['hunting_id' => 'HD20241227001', 'title' => 'iPhone 15 Pro Max', 'user_id' => 1, 'hunting_time' => '2024-12-27 10:30:00', 'action_taken' => 'accepted'],
    ['hunting_id' => 'HD20241227002', 'title' => 'Samsung Galaxy S24 Ultra', 'user_id' => 2, 'hunting_time' => '2024-12-27 10:25:00', 'action_taken' => 'pending'],
    ['hunting_id' => 'HD20241227003', 'title' => 'MacBook Air M3', 'user_id' => 1, 'hunting_time' => '2024-12-27 10:20:00', 'action_taken' => 'accepted'],
    ['hunting_id' => 'HD20241227004', 'title' => 'AirPods Pro 2', 'user_id' => 3, 'hunting_time' => '2024-12-27 10:15:00', 'action_taken' => 'declined'],
];

$topProducts = [
    ['title' => 'iPhone 15 Pro Max', 'hinh_anh' => '', 'gia_sale' => 25000000, 'hunt_count' => 12, 'accepted_count' => 8],
    ['title' => 'Samsung Galaxy S24', 'hinh_anh' => '', 'gia_sale' => 18000000, 'hunt_count' => 9, 'accepted_count' => 5],
    ['title' => 'MacBook Air M3', 'hinh_anh' => '', 'gia_sale' => 30000000, 'hunt_count' => 7, 'accepted_count' => 4],
    ['title' => 'AirPods Pro 2', 'hinh_anh' => '', 'gia_sale' => 6000000, 'hunt_count' => 5, 'accepted_count' => 3],
];

$chartData = [
    ['date' => '2024-12-21', 'hunts' => 5, 'orders' => 2],
    ['date' => '2024-12-22', 'hunts' => 8, 'orders' => 4],
    ['date' => '2024-12-23', 'hunts' => 12, 'orders' => 7],
    ['date' => '2024-12-24', 'hunts' => 6, 'orders' => 3],
    ['date' => '2024-12-25', 'hunts' => 3, 'orders' => 1],
    ['date' => '2024-12-26', 'hunts' => 14, 'orders' => 9],
    ['date' => '2024-12-27', 'hunts' => 15, 'orders' => 8],
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quản lý săn đơn hàng</title>
    
    <!-- CDN CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Reset và Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background-color: #f8f9fc;
            color: #5a5c69;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
        }
        
        #wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles - Updated for Bootstrap Admin Template */
        .sidebar {
            width: 224px;
            min-height: 100vh;
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            flex-shrink: 0;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }
        
        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            color: #fff !important;
            font-size: 1rem;
        }
        
        .sidebar-brand:hover {
            color: #fff !important;
            text-decoration: none;
        }
        
        .sidebar-brand-icon {
            margin-right: 0.5rem;
            font-size: 2rem;
        }
        
        .rotate-n-15 {
            transform: rotate(-15deg);
        }
        
        .sidebar-brand-text {
            font-size: 1.25rem;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 0 1rem 1rem;
        }
        
        .sidebar .nav-item {
            position: relative;
        }
        
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem;
            color: rgba(255, 255, 255, 0.8) !important;
            text-decoration: none;
            font-weight: 400;
            position: relative;
        }
        
        .sidebar .nav-link:hover {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.1);
            text-decoration: none;
        }
        
        .sidebar .nav-link.active {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.3);
        }
        
        .sidebar .nav-link.collapsed::after {
            content: '';
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 4px solid rgba(255, 255, 255, 0.5);
            border-top: 4px solid transparent;
            border-bottom: 4px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link[aria-expanded="true"]::after {
            transform: translateY(-50%) rotate(90deg);
        }
        
        /* Collapse Items */
        .collapse-inner {
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .collapse-header {
            color: #6c757d;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 1.5rem 1.5rem 0.5rem;
            text-transform: uppercase;
            margin: 0;
        }
        
        .collapse-item {
            color: #3a3b45;
            text-decoration: none;
            padding: 0.5rem 1.5rem;
            margin: 0 0.5rem;
            display: block;
            font-size: 0.85rem;
            border-radius: 0.35rem;
            white-space: nowrap;
        }
        
        .collapse-item:hover {
            color: #5a5c69;
            background-color: #eaecf4;
            text-decoration: none;
        }
        
        .collapse-item.active {
            color: #4e73df;
            background-color: #e3f2fd;
            font-weight: 700;
        }
        
        /* Sidebar Toggle Button */
        #sidebarToggle {
            width: 2.5rem;
            height: 2.5rem;
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        #sidebarToggle:hover {
            background-color: rgba(255, 255, 255, 0.25);
            color: rgba(255, 255, 255, 0.75);
        }
        
        #sidebarToggle::before {
            content: '';
            display: inline-block;
            width: 1rem;
            height: 1rem;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='rgba%28255,255,255,.5%29' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 14l6-6-6-6'/%3e%3c/svg%3e");
        }
        
        /* Content Wrapper */
        #content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        
        #content {
            flex: 1;
            padding: 0;
        }
        
        /* Topbar */
        .topbar {
            height: 4.375rem;
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .topbar .navbar-search {
            max-width: 25rem;
        }
        
        .topbar .form-control {
            background-color: #f8f9fc;
            border: 1px solid #d1d3e2;
            border-radius: 10rem;
            font-size: 0.85rem;
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 2.75rem 0.375rem 0.75rem;
        }
        
        .topbar .btn {
            border-radius: 10rem;
            color: #6c757d;
            background-color: #5a5c69;
            border-color: #5a5c69;
        }
        
        /* Main Content */
        .container-fluid {
            padding: 1.5rem;
        }
        
        /* Page Heading */
        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .page-heading h1 {
            font-size: 1.75rem;
            font-weight: 400;
            line-height: 1.2;
            color: #5a5c69;
            margin: 0;
        }
        
        /* Stats Cards */
        .card-stats {
            color: white;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s;
            border: none;
        }
        
        .card-stats:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .card-stats-1 { background: linear-gradient(45deg, #4e73df, #224abe); }
        .card-stats-2 { background: linear-gradient(45deg, #1cc88a, #13855c); }
        .card-stats-3 { background: linear-gradient(45deg, #36b9cc, #258391); }
        .card-stats-4 { background: linear-gradient(45deg, #f6c23e, #dda20a); }
        .card-stats-5 { background: linear-gradient(45deg, #e74a3b, #c0392b); }
        .card-stats-6 { background: linear-gradient(45deg, #858796, #60616f); }
        
        .card-stats .card-body {
            padding: 1.25rem;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0;
        }
        
        .stats-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            opacity: 0.8;
        }
        
        /* Regular Cards */
        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            border-top-left-radius: calc(0.35rem - 1px);
            border-top-right-radius: calc(0.35rem - 1px);
        }
        
        .card-body {
            flex: 1 1 auto;
            padding: 1.25rem;
        }
        
        .card-title {
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
            font-weight: 400;
            line-height: 1.2;
            color: #5a5c69;
        }
        
        /* Activity Items */
        .activity-item {
            border-left: 3px solid #007bff;
            padding: 0.75rem 1rem;
            margin-bottom: 0.75rem;
            background: #f8f9fa;
            border-radius: 0 0.25rem 0.25rem 0;
            transition: all 0.3s ease;
        }
        
        .activity-item:hover {
            background: #e9ecef;
            transform: translateX(3px);
        }
        
        .activity-item.accepted { border-left-color: #28a745; }
        .activity-item.declined { border-left-color: #dc3545; }
        .activity-item.pending { border-left-color: #ffc107; }
        
        /* Product Items */
        .product-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: 0.25rem;
            background: #f8f9fa;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .product-item:hover {
            background: #e9ecef;
        }
        
        .product-thumb {
            width: 40px;
            height: 40px;
            background: #e9ecef;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.35rem;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }
        
        .btn-primary {
            color: #fff;
            background-color: #4e73df;
            border-color: #4e73df;
        }
        
        .btn-primary:hover {
            color: #fff;
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        
        .btn-success {
            color: #fff;
            background-color: #1cc88a;
            border-color: #1cc88a;
        }
        
        .btn-success:hover {
            color: #fff;
            background-color: #17a673;
            border-color: #169b6b;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.765625rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.35rem;
        }
        
        .badge-primary { color: #fff; background-color: #4e73df; }
        .badge-success { color: #fff; background-color: #1cc88a; }
        .badge-warning { color: #212529; background-color: #f6c23e; }
        .badge-danger { color: #fff; background-color: #e74a3b; }
        .badge-secondary { color: #fff; background-color: #858796; }
        
        /* Chart Container */
        .chart-area {
            position: relative;
            height: 20rem;
            width: 100%;
        }
        
        /* Footer */
        .sticky-footer {
            background-color: #fff;
            padding: 2rem 0;
            flex-shrink: 0;
        }
        
        .copyright {
            line-height: 1;
            font-size: 0.8rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                min-height: auto;
            }
            
            #wrapper {
                flex-direction: column;
            }
            
            .container-fluid {
                padding: 1rem;
            }
            
            .page-heading {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .page-heading > div {
                margin-top: 1rem;
            }
            
            .stats-number {
                font-size: 1.5rem;
            }
        }
        
        /* Animations */
        .refresh-indicator {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Utilities */
        .text-center { text-align: center !important; }
        .text-muted { color: #6c757d !important; }
        .text-primary { color: #4e73df !important; }
        .text-success { color: #1cc88a !important; }
        .text-warning { color: #f6c23e !important; }
        .text-danger { color: #e74a3b !important; }
        
        .font-weight-bold { font-weight: 700 !important; }
        .small { font-size: 0.875em; }
        
        .mb-0 { margin-bottom: 0 !important; }
        .mb-1 { margin-bottom: 0.25rem !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .mb-3 { margin-bottom: 1rem !important; }
        .mb-4 { margin-bottom: 1.5rem !important; }
        
        .mr-2 { margin-right: 0.5rem !important; }
        .ml-2 { margin-left: 0.5rem !important; }
        
        .d-flex { display: flex !important; }
        .justify-content-between { justify-content: space-between !important; }
        .align-items-center { align-items: center !important; }
        .align-items-start { align-items: flex-start !important; }
        .flex-grow-1 { flex-grow: 1 !important; }
        
        .bg-white { background-color: #fff !important; }
        
        /* Grid System */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -0.75rem;
            margin-left: -0.75rem;
        }
        
        .col-xl-2, .col-xl-4, .col-xl-8, .col-xl-12,
        .col-lg-5, .col-lg-7,
        .col-md-4, .col-md-6, .col-md-12,
        .col-sm-6, .col-12 {
            position: relative;
            width: 100%;
            padding-right: 0.75rem;
            padding-left: 0.75rem;
        }
        
        @media (min-width: 576px) {
            .col-sm-6 { flex: 0 0 50%; max-width: 50%; }
        }
        
        @media (min-width: 768px) {
            .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
            .col-md-6 { flex: 0 0 50%; max-width: 50%; }
            .col-md-12 { flex: 0 0 100%; max-width: 100%; }
        }
        
        @media (min-width: 992px) {
            .col-lg-5 { flex: 0 0 41.666667%; max-width: 41.666667%; }
            .col-lg-7 { flex: 0 0 58.333333%; max-width: 58.333333%; }
        }
        
        @media (min-width: 1200px) {
            .col-xl-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
            .col-xl-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
            .col-xl-8 { flex: 0 0 66.666667%; max-width: 66.666667%; }
            .col-xl-12 { flex: 0 0 100%; max-width: 100%; }
        }
    </style>
</head>

<body>
    <div id="wrapper">
        
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="sidebar-brand-text mx-3"><?=function_exists('get_config') ? get_config("web_name") : "Admin Panel"?></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Trang chủ</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="users.php">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Quản lý người dùng</span></a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/admin/views/products/admin-products.php">
                    <i class="fas fa-fw fa-box"></i>
                    <span>Quản lý sản phẩm</span></a>
            </li>

            <!-- Menu săn đơn hàng -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseHunting"
                    aria-expanded="true" aria-controls="collapseHunting">
                    <i class="fas fa-fw fa-dice"></i>
                    <span>Quản lý săn đơn hàng</span>
                </a>
                <div id="collapseHunting" class="collapse show" aria-labelledby="headingHunting" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Săn đơn hàng</h6>
                        <a class="collapse-item active" href="/admin/controllers/dashboard.php">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </a>
                        <a class="collapse-item" href="/admin/views/products/admin-products.php">
                            <i class="fas fa-gift"></i> Sản phẩm săn đơn
                        </a>
                        <a class="collapse-item" href="/admin/controllers/history.php">
                            <i class="fas fa-history"></i> Lịch sử săn đơn
                        </a>
                        <a class="collapse-item" href="/admin/controllers/orders.php">
                            <i class="fas fa-shopping-cart"></i> Đơn hàng
                        </a>
                        <a class="collapse-item" href="/admin/controllers/settings.php">
                            <i class="fas fa-cog"></i> Cấu hình
                        </a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFinance"
                    aria-expanded="true" aria-controls="collapseFinance">
                    <i class="fas fa-fw fa-dollar-sign"></i>
                    <span>Quản lý tài chính</span>
                </a>
                <div id="collapseFinance" class="collapse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Tài chính</h6>
                        <a class="collapse-item" href="topup.php">Phương thức nạp tiền</a>
                        <a class="collapse-item" href="bank-bind.php">Quản lý ngân hàng</a>
                        <a class="collapse-item" href="topup-history.php">Lịch sử nạp tiền</a>
                        <a class="collapse-item" href="withdraw-history.php">Lịch sử rút tiền</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLottery"
                    aria-expanded="true" aria-controls="collapseLottery">
                    <i class="fas fa-fw fa-star"></i>
                    <span>Quản lý đánh giá</span>
                </a>
                <div id="collapseLottery" class="collapse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Đánh giá</h6>
                        <a class="collapse-item" href="lottery-category.php">Danh mục đánh giá</a>
                        <a class="collapse-item" href="lottery.php">Danh sách đánh giá</a>
                        <a class="collapse-item" href="lottery-result.php">Kết quả đánh giá</a>
                        <a class="collapse-item" href="lottery-history.php">Lịch sử đặt cược</a>
                        <a class="collapse-item" href="lottery-edit.php">Dự đoán kết quả</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="vip-level.php">
                    <i class="fas fa-fw fa-crown"></i>
                    <span>Quản lý VIP</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="system.php">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Cài đặt hệ thống</span></a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        
        <!-- Content Wrapper -->
        <div id="content-wrapper">
            
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Topbar Search -->
                <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Tìm kiếm..." 
                               aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-search fa-fw"></i>
                        </a>
                        <!-- Dropdown - Messages -->
                        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                            aria-labelledby="searchDropdown">
                            <form class="form-inline mr-auto w-100 navbar-search">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light border-0 small"
                                        placeholder="Tìm kiếm..." aria-label="Search">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>

                    <!-- Nav Item - Alerts -->
                    <li class="nav-item dropdown no-arrow mx-1">
                        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell fa-fw"></i>
                            <!-- Counter - Alerts -->
                            <span class="badge badge-danger badge-counter">3+</span>
                        </a>
                        <!-- Dropdown - Alerts -->
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="alertsDropdown">
                            <h6 class="dropdown-header">
                                Thông báo trung tâm
                            </h6>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-file-alt text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">19 tháng 12, 2024</div>
                                    <span class="font-weight-bold">Có đơn hàng săn đơn mới cần xử lý!</span>
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="mr-3">
                                    <div class="icon-circle bg-success">
                                        <i class="fas fa-donate text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">7 tháng 12, 2024</div>
                                    Doanh thu hôm nay đã đạt 290,000₫!
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="mr-3">
                                    <div class="icon-circle bg-warning">
                                        <i class="fas fa-exclamation-triangle text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">2 tháng 12, 2024</div>
                                    Có 5 đơn hàng đang chờ xử lý
                                </div>
                            </a>
                            <a class="dropdown-item text-center small text-gray-500" href="#">Xem tất cả thông báo</a>
                        </div>
                    </li>

                    <!-- Nav Item - Messages -->
                    <li class="nav-item dropdown no-arrow mx-1">
                        <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-envelope fa-fw"></i>
                            <!-- Counter - Messages -->
                            <span class="badge badge-danger badge-counter">7</span>
                        </a>
                        <!-- Dropdown - Messages -->
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="messagesDropdown">
                            <h6 class="dropdown-header">
                                Tin nhắn trung tâm
                            </h6>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="/admin/assets/img/undraw_profile_1.svg" alt="..." style="width: 60px; height: 60px;">
                                    <div class="status-indicator bg-success"></div>
                                </div>
                                <div class="font-weight-bold">
                                    <div class="text-truncate">Xin chào! Tôi muốn hỏi về tính năng săn đơn hàng...</div>
                                    <div class="small text-gray-500">Emily Fowler · 58 phút trước</div>
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="dropdown-list-image mr-3">
                                    <img class="rounded-circle" src="/admin/assets/img/undraw_profile_2.svg" alt="..." style="width: 60px; height: 60px;">
                                    <div class="status-indicator"></div>
                                </div>
                                <div>
                                    <div class="text-truncate">Tôi có thắc mắc về hoa hồng khi săn đơn hàng thành công</div>
                                    <div class="small text-gray-500">Jae Chun · 1 ngày trước</div>
                                </div>
                            </a>
                            <a class="dropdown-item text-center small text-gray-500" href="#">Đọc thêm tin nhắn</a>
                        </div>
                    </li>

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Quản trị viên</span>
                            <img class="img-profile rounded-circle" src="/assets/image/admin.png" style="width: 40px; height: 40px;">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Thông tin cá nhân
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Cài đặt
                            </a>
                            <a class="dropdown-item" href="/admin/views/hunting/dashboard.php">
                                <i class="fas fa-dice fa-sm fa-fw mr-2 text-gray-400"></i>
                                Dashboard săn đơn hàng
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Đăng xuất
                            </a>
                        </div>
                    </li>

                </ul>

            </nav>
            
            <!-- Main Content -->
            <div id="content">
                <div class="container-fluid">
                    
                    <!-- Page Heading -->
                    <div class="page-heading">
                        <h1>
                            <i class="fas fa-dice"></i> Dashboard Săn đơn hàng
                        </h1>
                        <div>
                            <button class="btn btn-primary btn-sm" onclick="refreshDashboard()" id="refreshBtn">
                                <i class="fas fa-sync-alt"></i> Làm mới
                            </button>
                            <a href="/nhap-hang/" class="btn btn-success btn-sm" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Xem trang người dùng
                            </a>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row">
                        
                        <!-- Lần săn hôm nay -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-stats card-stats-1">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stats-label">Lần săn hôm nay</div>
                                            <div class="stats-number"><?= number_format($stats['today_hunts']) ?></div>
                                        </div>
                                        <div>
                                            <i class="fas fa-dice fa-2x" style="opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Đơn hàng hôm nay -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-stats card-stats-2">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stats-label">Đơn hàng hôm nay</div>
                                            <div class="stats-number"><?= number_format($stats['today_orders']) ?></div>
                                        </div>
                                        <div>
                                            <i class="fas fa-shopping-cart fa-2x" style="opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tỷ lệ chuyển đổi -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-stats card-stats-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stats-label">Tỷ lệ chuyển đổi</div>
                                            <div class="stats-number"><?= $stats['conversion_rate'] ?>%</div>
                                        </div>
                                        <div>
                                            <i class="fas fa-chart-line fa-2x" style="opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Doanh thu hôm nay -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-stats card-stats-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stats-label">Doanh thu hôm nay</div>
                                            <div class="stats-number"><?= number_format($stats['today_revenue']) ?>₫</div>
                                        </div>
                                        <div>
                                            <i class="fas fa-dollar-sign fa-2x" style="opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User hoạt động -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-stats card-stats-5">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stats-label">User hoạt động</div>
                                            <div class="stats-number"><?= number_format($stats['active_users']) ?></div>
                                        </div>
                                        <div>
                                            <i class="fas fa-users fa-2x" style="opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sản phẩm hoạt động -->
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card card-stats card-stats-6">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stats-label">Sản phẩm hoạt động</div>
                                            <div class="stats-number"><?= number_format($stats['active_products']) ?></div>
                                        </div>
                                        <div>
                                            <i class="fas fa-box fa-2x" style="opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Overview -->
                        <div class="col-xl-12 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4 mb-3">
                                            <div class="h4 text-primary mb-0"><?= number_format($stats['month_hunts']) ?></div>
                                            <div class="small text-muted">Lần săn tháng này</div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="h4 text-success mb-0"><?= number_format($stats['month_orders']) ?></div>
                                            <div class="small text-muted">Đơn hàng tháng này</div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="h4 text-warning mb-0"><?= number_format($stats['month_revenue']) ?>₫</div>
                                            <div class="small text-muted">Doanh thu tháng</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Charts and Activities -->
                    <div class="row">

                        <!-- Biểu đồ hoạt động -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="font-weight-bold text-primary mb-0">
                                        <i class="fas fa-chart-area"></i> Thống kê hoạt động 7 ngày qua
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="huntingChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top sản phẩm được săn -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="font-weight-bold text-primary mb-0">
                                        <i class="fas fa-star"></i> Top sản phẩm được săn (7 ngày)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($topProducts)): ?>
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-0">Chưa có dữ liệu</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($topProducts as $index => $product): ?>
                                            <div class="product-item">
                                                <div class="product-thumb">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="font-weight-bold">#<?= $index + 1 ?></div>
                                                    <div class="small text-muted"><?= htmlspecialchars(substr($product['title'], 0, 25)) ?>...</div>
                                                    <div class="small text-success"><?= number_format($product['gia_sale']) ?>₫</div>
                                                </div>
                                                <div class="text-center">
                                                    <span class="badge badge-primary"><?= $product['hunt_count'] ?> lần</span>
                                                    <div class="small text-muted"><?= $product['accepted_count'] ?> nhận</div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Hoạt động gần đây -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="font-weight-bold text-primary mb-0">
                                        <i class="fas fa-history"></i> Hoạt động gần đây
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recentActivity)): ?>
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-clock fa-2x mb-2"></i>
                                            <p class="mb-0">Chưa có hoạt động nào</p>
                                        </div>
                                    <?php else: ?>
                                        <div style="max-height: 400px; overflow-y: auto;">
                                            <?php foreach ($recentActivity as $activity): ?>
                                                <div class="activity-item <?= $activity['action_taken'] ?>">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <div class="font-weight-bold">
                                                                Mã: <?= htmlspecialchars($activity['hunting_id']) ?>
                                                            </div>
                                                            <div class="text-muted small">
                                                                <?= htmlspecialchars($activity['title'] ?? 'Sản phẩm không xác định') ?>
                                                            </div>
                                                            <div class="text-muted small">
                                                                User #<?= $activity['user_id'] ?> | 
                                                                <?= date('d/m/Y H:i', strtotime($activity['hunting_time'])) ?>
                                                            </div>
                                                        </div>
                                                        <div class="ml-2">
                                                            <?php
                                                            $badgeClass = [
                                                                'pending' => 'warning',
                                                                'accepted' => 'success', 
                                                                'declined' => 'danger'
                                                            ][$activity['action_taken']] ?? 'secondary';
                                                            
                                                            $badgeText = [
                                                                'pending' => 'Chờ',
                                                                'accepted' => 'Nhận', 
                                                                'declined' => 'Từ chối'
                                                            ][$activity['action_taken']] ?? 'N/A';
                                                            ?>
                                                            <span class="badge badge-<?= $badgeClass ?>"><?= $badgeText ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="font-weight-bold text-primary mb-0">
                                        <i class="fas fa-bolt"></i> Thao tác nhanh
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <a href="products.php" class="btn btn-primary btn-sm mb-2">
                                            <i class="fas fa-gift"></i> Quản lý sản phẩm săn đơn
                                        </a>
                                        <a href="orders.php" class="btn btn-success btn-sm mb-2">
                                            <i class="fas fa-shopping-cart"></i> Xem đơn hàng mới
                                        </a>
                                        <a href="history.php" class="btn btn-primary btn-sm mb-2">
                                            <i class="fas fa-history"></i> Lịch sử săn đơn
                                        </a>
                                        <a href="settings.php" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-cog"></i> Cài đặt hệ thống
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Footer -->
            <div class="sticky-footer bg-white">
                <div class="container-fluid">
                    <div class="copyright text-center">
                        <span>Hệ thống săn đơn hàng © <?= date('Y') ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Chart data
        let chartData = <?= json_encode($chartData) ?>;
        
        // Create chart
        const ctx = document.getElementById('huntingChart').getContext('2d');
        const huntingChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' });
                }),
                datasets: [{
                    label: 'Lần săn đơn',
                    data: chartData.map(item => item.hunts || 0),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }, {
                    label: 'Đơn hàng',
                    data: chartData.map(item => item.orders || 0),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#1cc88a',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        
        // Refresh dashboard
        function refreshDashboard() {
            const refreshBtn = document.getElementById('refreshBtn');
            const icon = refreshBtn.querySelector('i');
            
            // Add spinning animation
            icon.classList.add('refresh-indicator');
            refreshBtn.disabled = true;
            
            // Reload page after animation
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
        
        // Auto refresh every 5 minutes
        setInterval(function() {
            console.log('Auto refreshing stats...');
            // Implement AJAX refresh here if needed
        }, 300000);
        
        // Real-time clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleString('vi-VN');
            document.title = `Dashboard (${timeString.split(' ')[1]}) - Quản lý săn đơn hàng`;
        }
        
        // Update clock every minute
        setInterval(updateClock, 60000);
        updateClock();
        
        // Document ready
        $(document).ready(function() {
            // Initialize tooltips if needed
            $('[data-toggle="tooltip"]').tooltip();
            
            // Welcome message for new users
            <?php if ($stats['today_hunts'] === 0 && $stats['today_orders'] === 0): ?>
                setTimeout(function() {
                    if (confirm('Hôm nay chưa có hoạt động săn đơn nào. Bạn có muốn kiểm tra cài đặt hệ thống không?')) {
                        window.location.href = 'settings.php';
                    }
                }, 3000);
            <?php endif; ?>
            
            // Add hover effects for cards
            $('.card-stats').hover(
                function() {
                    $(this).css('transform', 'translateY(-2px)');
                },
                function() {
                    $(this).css('transform', 'translateY(0)');
                }
            );
        });
    </script>
</body>
</html>