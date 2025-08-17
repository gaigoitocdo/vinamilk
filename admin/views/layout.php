<?php
// ========== FILE: /admin/views/hunting/layout.php ==========
// Layout wrapper cho thư mục hunting - Updated version với full design

// Include config
if (file_exists(__DIR__ . '/../../../config/config.php')) {
    include_once __DIR__ . '/../../../config/config.php';
}

function hunting_header($title = "Admin Panel") {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?></title>
        
        <!-- CDN CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
        
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
            
            /* Dropdown styles */
            .dropdown-list {
                border: none;
                border-radius: 0.35rem;
                box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
                max-width: 25rem;
                max-height: 25rem;
                overflow-y: auto;
            }
            
            .dropdown-header {
                background-color: #4e73df;
                border: none;
                font-weight: 800;
                color: #fff;
                text-align: center;
                font-size: 0.85rem;
                padding: 0.5rem 1.5rem;
            }
            
            .dropdown-item {
                padding: 1rem;
                border-top: 1px solid #e3e6f0;
            }
            
            .dropdown-list-image {
                position: relative;
            }
            
            .status-indicator {
                position: absolute;
                bottom: 0;
                right: 0;
                width: 1rem;
                height: 1rem;
                border-radius: 50%;
                border: 2px solid #fff;
            }
            
            .status-indicator.bg-success {
                background-color: #1cc88a;
            }
            
            .icon-circle {
                height: 2.5rem;
                width: 2.5rem;
                border-radius: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .icon-circle.bg-primary {
                background-color: #4e73df;
            }
            
            .icon-circle.bg-success {
                background-color: #1cc88a;
            }
            
            .icon-circle.bg-warning {
                background-color: #f6c23e;
            }
            
            .badge-counter {
                position: absolute;
                transform: scale(0.9);
                transform-origin: top right;
                right: 0.25rem;
                top: -0.25rem;
            }
            
            .topbar-divider {
                width: 0;
                border-right: 1px solid #e3e6f0;
                height: calc(4.375rem - 2rem);
                margin: auto 1rem;
            }
            
            .img-profile {
                height: 2rem;
                width: 2rem;
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
                margin-bottom: 0.5rem;
                font-size: 1.25rem;
                font-weight: 400;
                line-height: 1.2;
                color: #5a5c69;
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
            
            .btn-danger {
                color: #fff;
                background-color: #e74a3b;
                border-color: #e74a3b;
            }
            
            .btn-danger:hover {
                color: #fff;
                background-color: #c0392b;
                border-color: #a93226;
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
            
            /* Form Elements */
            .form-control {
                display: block;
                width: 100%;
                height: calc(1.5em + 0.75rem + 2px);
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
                font-weight: 400;
                line-height: 1.5;
                color: #6e707e;
                background-color: #fff;
                background-clip: padding-box;
                border: 1px solid #d1d3e2;
                border-radius: 0.35rem;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            
            .form-control:focus {
                color: #6e707e;
                background-color: #fff;
                border-color: #bac8f3;
                outline: 0;
                box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            }
            
            /* Tables */
            .table {
                width: 100%;
                margin-bottom: 1rem;
                color: #858796;
            }
            
            .table th,
            .table td {
                padding: 0.75rem;
                vertical-align: top;
                border-top: 1px solid #e3e6f0;
            }
            
            .table thead th {
                vertical-align: bottom;
                border-bottom: 2px solid #e3e6f0;
            }
            
            .table tbody + tbody {
                border-top: 2px solid #e3e6f0;
            }
            
            /* Modal */
            .modal-content {
                position: relative;
                display: flex;
                flex-direction: column;
                width: 100%;
                pointer-events: auto;
                background-color: #fff;
                background-clip: padding-box;
                border: none;
                border-radius: 0.35rem;
                box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
                outline: 0;
            }
            
            .modal-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                padding: 1rem 1rem;
                border-bottom: 1px solid #e3e6f0;
                border-top-left-radius: calc(0.35rem - 1px);
                border-top-right-radius: calc(0.35rem - 1px);
            }
            
            .modal-body {
                position: relative;
                flex: 1 1 auto;
                padding: 1rem;
            }
            
            .modal-footer {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: flex-end;
                padding: 0.75rem;
                border-top: 1px solid #e3e6f0;
                border-bottom-right-radius: calc(0.35rem - 1px);
                border-bottom-left-radius: calc(0.35rem - 1px);
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
            .text-gray-600 { color: #6c757d !important; }
            .text-gray-500 { color: #b7b9cc !important; }
            
            .font-weight-bold { font-weight: 700 !important; }
            .small { font-size: 0.875em; }
            
            .mb-0 { margin-bottom: 0 !important; }
            .mb-1 { margin-bottom: 0.25rem !important; }
            .mb-2 { margin-bottom: 0.5rem !important; }
            .mb-3 { margin-bottom: 1rem !important; }
            .mb-4 { margin-bottom: 1.5rem !important; }
            
            .mr-2 { margin-right: 0.5rem !important; }
            .mr-3 { margin-right: 1rem !important; }
            .ml-2 { margin-left: 0.5rem !important; }
            .ml-auto { margin-left: auto !important; }
            .mx-3 { margin-left: 1rem !important; margin-right: 1rem !important; }
            .my-0 { margin-top: 0 !important; margin-bottom: 0 !important; }
            .my-2 { margin-top: 0.5rem !important; margin-bottom: 0.5rem !important; }
            .my-auto { margin-top: auto !important; margin-bottom: auto !important; }
            
            .d-flex { display: flex !important; }
            .d-none { display: none !important; }
            .d-inline-block { display: inline-block !important; }
            .d-md-none { display: none !important; }
            .d-md-inline { display: inline !important; }
            .d-md-block { display: block !important; }
            .d-lg-inline { display: inline !important; }
            .d-sm-none { display: none !important; }
            .d-sm-inline-block { display: inline-block !important; }
            
            .justify-content-between { justify-content: space-between !important; }
            .justify-content-center { justify-content: center !important; }
            .align-items-center { align-items: center !important; }
            .align-items-start { align-items: flex-start !important; }
            .flex-grow-1 { flex-grow: 1 !important; }
            
            .bg-white { background-color: #fff !important; }
            .bg-light { background-color: #f8f9fc !important; }
            
            .border-0 { border: 0 !important; }
            .rounded-circle { border-radius: 50% !important; }
            .shadow { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important; }
            
            .static-top { position: static !important; }
            .no-arrow { position: relative; }
            
            @media (min-width: 768px) {
                .d-md-none { display: none !important; }
                .d-md-inline { display: inline !important; }
                .d-md-block { display: block !important; }
            }
            
            @media (min-width: 576px) {
                .d-sm-none { display: none !important; }
                .d-sm-inline-block { display: inline-block !important; }
            }
            
            @media (min-width: 992px) {
                .d-lg-inline { display: inline !important; }
            }
            
            /* Input Group */
            .input-group {
                position: relative;
                display: flex;
                flex-wrap: wrap;
                align-items: stretch;
                width: 100%;
            }
            
            .input-group > .form-control {
                position: relative;
                flex: 1 1 auto;
                width: 1%;
                min-width: 0;
                margin-bottom: 0;
            }
            
            .input-group-append {
                margin-left: -1px;
                display: flex;
            }
            
            .input-group-append .btn {
                position: relative;
                z-index: 2;
            }
            
            /* Navigation */
            .navbar-nav {
                display: flex;
                flex-direction: column;
                padding-left: 0;
                margin-bottom: 0;
                list-style: none;
            }
            
            .navbar-expand .navbar-nav {
                flex-direction: row;
            }
            
            .nav-item {
                margin-bottom: 0;
            }
            
            .nav-link {
                display: block;
                padding: 0.5rem 1rem;
                color: #007bff;
                text-decoration: none;
                background-color: transparent;
                border: 0;
            }
            
            .navbar-light .navbar-nav .nav-link {
                color: rgba(0, 0, 0, 0.5);
            }
            
            /* Accordion/Collapse */
            .accordion {
                overflow-anchor: none;
            }
            
            .collapse:not(.show) {
                display: none;
            }
            
            .collapse.show {
                display: block;
            }
            
            .collapsing {
                height: 0;
                overflow: hidden;
                transition: height 0.35s ease;
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
                    <div class="sidebar-brand-text mx-3"><?= function_exists('get_config') ? get_config("web_name") : "Admin Panel" ?></div>
                </a>

                <!-- Divider -->
                <hr class="sidebar-divider my-0">

                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span></a>
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
                    <div id="collapseHunting" class="collapse" aria-labelledby="headingHunting" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Săn đơn hàng</h6>
                            <a class="collapse-item" href="dashboard.php">
                                <i class="fas fa-chart-line"></i> Dashboard
                            </a>
                            <a class="collapse-item" href="products.php">
                                <i class="fas fa-gift"></i> Sản phẩm săn đơn
                            </a>
                            <a class="collapse-item" href="history.php">
                                <i class="fas fa-history"></i> Lịch sử săn đơn
                            </a>
                            <a class="collapse-item" href="orders.php">
                                <i class="fas fa-shopping-cart"></i> Đơn hàng
                            </a>
                            <a class="collapse-item" href="settings.php">
                                <i class="fas fa-cog"></i> Cấu hình
                            </a>
                            <a class="collapse-item" href="withdraw-history.php">
                                <i class="fas fa-wallet"></i> Lịch sử rút tiền
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Menu tài chính -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFinance"
                        aria-expanded="true" aria-controls="collapseFinance">
                        <i class="fas fa-fw fa-dollar-sign"></i>
                        <span>Quản lý tài chính</span>
                    </a>
                    <div id="collapseFinance" class="collapse" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Tài chính</h6>
                            <a class="collapse-item" href="topup.php">
                                <i class="fas fa-money-bill"></i> Phương thức nạp tiền
                            </a>
                            <a class="collapse-item" href="bank-bind.php">
                                <i class="fas fa-university"></i> Quản lý ngân hàng
                            </a>
                            <a class="collapse-item" href="topup-history.php">
                                <i class="fas fa-history"></i> Lịch sử nạp tiền
                            </a>
                            <a class="collapse-item" href="withdraw-history.php">
                                <i class="fas fa-wallet"></i> Lịch sử rút tiền
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Menu đánh giá -->
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
            <div id="content-wrapper" class="d-flex flex-column">
                
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
                                <a class="dropdown-item" href="dashboard.php">
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
                        <div class="page-heading">
                            <h1>
                                <i class="fas <?= $GLOBALS['page_icon'] ?? 'fa-home' ?>"></i> <?= htmlspecialchars($title) ?>
                            </h1>
                            <div>
                                <button class="btn btn-primary btn-sm" onclick="location.reload()">
                                    <i class="fas fa-sync-alt"></i> Làm mới
                                </button>
                            </div>
                        </div>
    <?php
}

function hunting_footer() {
    ?>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Hệ thống quản lý © <?= date('Y') ?></span>
                        </div>
                    </div>
                </footer>

            </div>
        </div>

        <!-- Logout Modal -->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Sẵn sàng để rời khỏi?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Chọn "Đăng xuất" bên dưới nếu bạn đã sẵn sàng kết thúc phiên hiện tại của mình.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
                        <a class="btn btn-primary" href="login.php">Đăng xuất</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        
        <script>
            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Global API function
            window.api = function(options) {
                $.ajax({
                    url: options.url || "ajax/admin.php",
                    method: options.method || "POST",
                    data: options.data || {},
                    dataType: "json",
                    beforeSend: function() {
                        if (options.beforeSend) {
                            options.beforeSend();
                        }
                    },
                    success: function(response) {
                        if (options.onSuccess) {
                            options.onSuccess(response);
                        } else {
                            if (response.success) {
                                toastr.success(response.message || "Thành công!");
                                if (options.reload !== false) {
                                    setTimeout(() => location.reload(), 1500);
                                }
                            } else {
                                toastr.error(response.message || "Có lỗi xảy ra!");
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('API Error:', error);
                        toastr.error("Không thể kết nối server!");
                        if (options.onError) {
                            options.onError(xhr, status, error);
                        }
                    },
                    complete: function() {
                        if (options.onComplete) {
                            options.onComplete();
                        }
                    }
                });
            };

            // Sidebar functionality
            $(document).ready(function() {
                // Toggle the side navigation
                $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
                    $("body").toggleClass("sidebar-toggled");
                    $(".sidebar").toggleClass("toggled");
                    if ($(".sidebar").hasClass("toggled")) {
                        $('.sidebar .collapse').collapse('hide');
                    }
                });

                // Close any open menu accordions when window is resized below 768px
                $(window).resize(function() {
                    if ($(window).width() < 768) {
                        $('.sidebar .collapse').collapse('hide');
                    }
                    
                    // Toggle the side navigation when window is resized below 480px
                    if ($(window).width() < 480 && !$(".sidebar").hasClass("toggled")) {
                        $("body").addClass("sidebar-toggled");
                        $(".sidebar").addClass("toggled");
                        $('.sidebar .collapse').collapse('hide');
                    }
                });

                // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
                $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
                    if ($(window).width() > 768) {
                        var e0 = e.originalEvent,
                            delta = e0.wheelDelta || -e0.detail;
                        this.scrollTop += (delta < 0 ? 1 : -1) * 30;
                        e.preventDefault();
                    }
                });

                // Initialize tooltips
                $('[data-toggle="tooltip"]').tooltip();

                // Initialize DataTables if present
                if ($.fn.DataTable) {
                    $('.dataTable').DataTable({
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json"
                        },
                        "pageLength": 25,
                        "responsive": true,
                        "order": [[0, "desc"]]
                    });
                }

                // Scroll to top functionality
                $(window).scroll(function() {
                    if ($(this).scrollTop() > 100) {
                        $('.scroll-to-top').fadeIn();
                    } else {
                        $('.scroll-to-top').fadeOut();
                    }
                });

                // Smooth scrolling using jQuery easing
                $(document).on('click', 'a.scroll-to-top', function(e) {
                    var $anchor = $(this);
                    $('html, body').stop().animate({
                        scrollTop: ($($anchor.attr('href')).offset().top)
                    }, 1000, 'easeInOutExpo');
                    e.preventDefault();
                });

                // Auto-hide alerts after 5 seconds
                $('.alert').delay(5000).fadeOut('slow');

                // Confirmation dialogs for delete actions
                $(document).on('click', '[data-confirm]', function(e) {
                    if (!confirm($(this).data('confirm'))) {
                        e.preventDefault();
                        return false;
                    }
                });

                // Form validation helpers
                $('.needs-validation').on('submit', function(e) {
                    if (this.checkValidity() === false) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    $(this).addClass('was-validated');
                });

                // Auto-refresh notifications (every 30 seconds)
                setInterval(function() {
                    // Update notification counts if needed
                    updateNotificationCounts();
                }, 30000);

                // Initialize any custom components
                initializeCustomComponents();
            });

            // Update notification counts
            function updateNotificationCounts() {
                // This would typically make an AJAX call to get updated counts
                // Implementation depends on your notification system
            }

            // Initialize custom components
            function initializeCustomComponents() {
                // Add any custom component initialization here
                
                // Example: Initialize custom dropdowns
                $('.custom-dropdown').each(function() {
                    // Custom dropdown logic
                });

                // Example: Initialize custom modals
                $('.custom-modal').each(function() {
                    // Custom modal logic
                });
            }

            // Utility functions
            window.showLoading = function() {
                $('body').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
            };

            window.hideLoading = function() {
                $('.loading-overlay').remove();
            };

            window.formatCurrency = function(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            };

            window.formatNumber = function(number) {
                return new Intl.NumberFormat('vi-VN').format(number);
            };

            // Global error handler
            window.onerror = function(msg, url, lineNo, columnNo, error) {
                console.error('Global error:', {
                    message: msg,
                    source: url,
                    line: lineNo,
                    column: columnNo,
                    error: error
                });
                return false;
            };
        </script>

        <!-- Additional CSS for loading overlay -->
        <style>
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }

            .scroll-to-top {
                position: fixed;
                right: 1rem;
                bottom: 1rem;
                display: none;
                width: 2.75rem;
                height: 2.75rem;
                text-align: center;
                color: #fff;
                background: rgba(90, 92, 105, 0.5);
                line-height: 46px;
                border-radius: 100%;
                z-index: 1031;
            }

            .scroll-to-top:focus,
            .scroll-to-top:hover {
                color: white;
                background: #5a5c69;
                text-decoration: none;
            }

            .animated--grow-in {
                animation-name: growIn;
                animation-duration: 200ms;
                animation-timing-function: transform cubic-bezier(0.18, 1.25, 0.4, 1), opacity cubic-bezier(0, 1, 0.4, 1);
            }

            @keyframes growIn {
                0% {
                    transform: scale(0.9);
                    opacity: 0;
                }
                100% {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            .sidebar-toggled .sidebar {
                width: 6.5rem;
            }

            .sidebar-toggled .sidebar .nav-item .nav-link {
                text-align: center;
                padding: 0.75rem 1rem;
                width: 6.5rem;
            }

            .sidebar-toggled .sidebar .nav-item .nav-link span {
                font-size: 0.65rem;
                display: block;
            }

            .sidebar-toggled .sidebar .sidebar-brand .sidebar-brand-text {
                display: none;
            }

            .sidebar-toggled .sidebar .sidebar-heading {
                text-align: center;
            }

            @media (min-width: 768px) {
                .sidebar-toggled .sidebar {
                    width: 6.5rem !important;
                }
                
                .sidebar-toggled .sidebar .nav-item .collapse {
                    display: none;
                }
                
                .sidebar-toggled .sidebar .nav-item .nav-link {
                    text-align: center;
                    padding: 0.75rem 1rem;
                    width: 6.5rem;
                }
                
                .sidebar-toggled .sidebar .nav-item .nav-link span {
                    font-size: 0.65rem;
                    display: block;
                }
                
                .sidebar-toggled .sidebar .sidebar-brand .sidebar-brand-text {
                    display: none;
                }
            }
        </style>
    </body>
    </html>
    <?php
}

// Auto start
if (!headers_sent() && !isset($HUNTING_LAYOUT_LOADED)) {
    $HUNTING_LAYOUT_LOADED = true;
    $page_title = isset($page_title) ? $page_title : "Admin Panel";
    hunting_header($page_title);
}
?>