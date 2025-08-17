<?php

function getCurrentPage() {
    // Kiểm tra controller parameter từ URL rewrite
    if (isset($_GET['controller'])) {
        return $_GET['controller'];
    }
    
    // Fallback: kiểm tra URL path
    $currentPath = $_SERVER['REQUEST_URI'];
    
    // Xử lý các trường hợp đặc biệt cho các trang đánh giá
    if (strpos($currentPath, 'lottery-category') !== false) return 'lottery-category';
    if (strpos($currentPath, 'lottery-result') !== false) return 'lottery-result';
  
    if (strpos($currentPath, 'lottery-history') !== false) return 'lottery-history';
    if (strpos($currentPath, 'lottery.html') !== false) return 'lottery';
    if (strpos($currentPath, 'topup') !== false) return 'topup';
    if (strpos($currentPath, 'location-manager') !== false) return 'location-manager';
    if (strpos($currentPath, 'dashboard') !== false) return 'dashboard';
    if (strpos($currentPath, 'website-settings') !== false) return 'website-settings';
    if (strpos($currentPath, 'users') !== false) return 'users';
    if (strpos($currentPath, 'character-management') !== false) return 'character-management';
    if (strpos($currentPath, 'withdraw-history') !== false) return 'withdraw-history';
    if (strpos($currentPath, 'vip-level') !== false) return 'vip-level';
    if (strpos($currentPath, 'order-admin') !== false) return 'order-admin';
    if (strpos($currentPath, 'membershipcard') !== false) return 'membershipcard';
    if (strpos($currentPath, 'admin-products') !== false) return 'admin-products';
    if (strpos($currentPath, 'bank-bind') !== false) return 'bank-bind';
    if (strpos($currentPath, 'level-management') !== false) return 'level-management';
    if (strpos($currentPath, 'send-notification') !== false) return 'send-notification';
    if (strpos($currentPath, 'lottery-edit') !== false) return 'lottery-edit';
    
    // Lấy tên file từ URL (bỏ .html)
    if (preg_match('/\/([^\/]+)\.html/', $currentPath, $matches)) {
        return $matches[1];
    }
    
    // Fallback cuối cùng
    $currentFile = basename($_SERVER['PHP_SELF'], '.php');
    return $currentFile === 'index' ? 'dashboard' : $currentFile;
}

// Hàm kiểm tra active state cho nav-link
function isActivePage($page) {
    $currentPage = getCurrentPage();
    return $currentPage === $page ? 'active' : '';
}

function admin_side_bar() {
    $currentPage = getCurrentPage();
?>
    <!-- External Libraries -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
      :root {
            /* Color Palette */
             --primary-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
            --primary-color: #8b5cf6;
            --primary-dark: #6d28d9;
            --secondary-color: #d946ef;
            --accent-color: #f472b6;
            --gold-accent: #d4af37;
            
            --dark-bg: #0f172a;
            --dark-surface: #1e293b;
            --dark-card: #2d3748;
            --dark-border: #4b5563;
            --dark-hover: #6b7280;
            
            --text-primary: #f8fafc;
            --text-secondary: #d1d5db;
            --text-muted: #9ca3af;
            --text-accent: #e5e7eb;
            
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            
            /* Light Theme */
            --light-bg: #f8fafc;
            --light-surface: #ffffff;
            --light-card: #f1f5f9;
            --light-border: #e5e7eb;
            --light-hover: #d1d5db;
            --light-text-primary: #1f2937;
            --light-text-secondary: #4b5563;
            --light-text-muted: #6b7280;
            
            /* Layout */
            --sidebar-width: 280px;
            --border-radius: 16px;
            --border-radius-sm: 8px;
            --shadow-dark: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
            --shadow-light: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="light"] {
            --dark-bg: var(--light-bg);
            --dark-surface: var(--light-surface);
            --dark-card: var(--light-card);
            --dark-border: var(--light-border);
            --dark-hover: var(--light-hover);
            --text-primary: var(--light-text-primary);
            --text-secondary: var(--light-text-secondary);
            --text-muted: var(--light-text-muted);
            --shadow-dark: var(--shadow-light);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color var(--transition), color var(--transition), border-color var(--transition);
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            background: var(--dark-bg);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Sidebar */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--dark-surface);
            border-right: 1px solid var(--dark-border);
            box-shadow: var(--shadow-dark);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 1.5rem;
            background: var(--primary-gradient);
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid var(--dark-border);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            transition: var(--transition);
        }

        .sidebar-brand:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        .sidebar-brand-icon {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem;
            border-radius: var(--border-radius-sm);
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: relative;
        }

        .nav-section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1.5rem;
            right: 1.5rem;
            height: 1px;
            background: var(--dark-border);
            opacity: 0.5;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 1rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: var(--border-radius-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--primary-gradient);
            opacity: 0.1;
            transition: width var(--transition);
        }

        .nav-item:hover::before {
            width: 100%;
        }

        .nav-item.active {
            background: rgba(139, 92, 246, 0.1);
            color: var(--text-primary);
            border-left: 4px solid var(--primary-color);
            box-shadow: inset 0 0 10px rgba(139, 92, 246, 0.2);
        }

        .nav-item:hover {
            background: var(--dark-hover);
            color: var(--text-primary);
            transform: translateX(2px);
            box-shadow: var(--shadow-light);
        }

        .nav-item-icon {
            margin-right: 1rem;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .nav-item.active .nav-item-icon {
            color: var(--primary-color);
            transform: scale(1.1);
        }

        /* Card Styles */
        .card {
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-dark);
            margin-bottom: 1.5rem;
            transition: var(--transition-slow);
            position: relative;
            overflow: hidden;
        }

   

        .card-header {
            background: var(--dark-surface);
            padding: 1.5rem;
            border-bottom: 1px solid var(--dark-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Form Styles */
        .form-control, .form-select {
            background: var(--dark-surface);
            border: 1px solid var(--dark-border);
            color: var(--text-primary);
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
            background: var(--dark-surface);
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        /* Button Styles */
        .btn {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
        }

        .btn-secondary {
            background: var(--dark-hover);
            color: var(--text-primary);
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn:hover::after {
            width: 300px;
            height: 300px;
        }
.table>:not(caption)>*>* {
    padding: .5rem .5rem;
    color: #ffffff;
    background-color: var(--bs-table-bg);
    border-bottom-width: var(--bs-border-width);
    box-shadow: inset 0 0 0 9999px var(--bs-table-bg-state, var(--bs-table-bg-type, var(--bs-table-accent-bg)));
}
        /* Table Styles */
        .table {
            color: var(--text-primary);
            background: var(--dark-surface);
            --bs-table-bg: transparent;
            --bs-table-hover-bg: rgba(139, 92, 246, 0.05);
        }

        .table thead th {
            background: var(--dark-card);
            color: var(--text-primary);
            border-bottom: 2px solid var(--dark-border);
            padding: 1rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr {
            transition: var(--transition);
        }


        .table td {
            border-bottom: 1px solid var(--dark-border);
            padding: 1rem;
        }

        /* Modal Styles */
        .modal-content {
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius);
            color: var(--text-primary);
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-bottom: 1px solid var(--dark-border);
        }

        .modal-body {
            background: var(--dark-surface);
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid var(--dark-border);
            background: var(--dark-surface);
        }

        /* Tooltip Styles */
        .tooltip {
            --bs-tooltip-bg: var(--dark-surface);
            --bs-tooltip-color: var(--text-primary);
            border: 1px solid var(--dark-border);
            border-radius: var(--border-radius-sm);
        }

        .tooltip-inner {
            background: var(--dark-surface);
            color: var(--text-primary);
            padding: 0.5rem 0.75rem;
        }

        /* Progress Bar Styles */
        .progress {
            background: var(--dark-border);
            border-radius: var(--border-radius-sm);
            height: 1rem;
            overflow: hidden;
        }

        .progress-bar {
            background: var(--primary-gradient);
            transition: width var(--transition-slow);
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            background-size: 1rem 1rem;
            animation: progress-bar-stripes 1s linear infinite;
        }

        @keyframes progress-bar-stripes {
            0% { background-position: 1rem 0; }
            100% { background-position: 0 0; }
        }
  /* Badge Styles */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .badge.bg-gray-500 {
            background: var(--dark-hover);
            color: var(--text-primary);
        }

        .badge.bg-red-600 {
            background: var(--danger-color);
            color: white;
        }

        .badge.bg-green-600 {
            background: var(--success-color);
            color: white;
        }

        .badge.bg-yellow-500 {
            background: var(--warning-color);
            color: white;
        }

        .badge:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

             .badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }



        .badge-warning {
            background: linear-gradient(135deg, var(--warning-color), var(--warning-dark));
            color: var(--text-primary);
        }

        .badge-danger {
            background: linear-gradient(135deg, var(--danger-color), var(--danger-dark));
            color: var(--text-primary);
        }

        .badge-primary {
            background: var(--primary-color);
            color: white;
        }

        .badge-success {
            background: var(--success-color);
            color: white;
        }

        /* Animation Styles */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in-left {
            animation: slideInLeft 0.5s ease-out;
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Lazy Loading */
        .lazy-load {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .lazy-load.loaded {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .admin-sidebar {
                width: 240px;
            }

            .admin-main {
                margin-left: 240px;
            }
        }

        @media (max-width: 992px) {
            .admin-sidebar {
                width: 200px;
            }

            .admin-main {
                margin-left: 200px;
            }

            .card {
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .admin-sidebar.open {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }

            .table td, .table th {
                padding: 0.5rem;
                font-size: 0.85rem;
            }
        }

        /* Accessibility */
        .nav-item:focus, .btn:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        /* High Contrast Mode */
        @media (prefers-contrast: high) {
            :root {
                --primary-color: #0055ff;
                --dark-bg: #000000;
                --dark-surface: #111111;
                --dark-card: #222222;
                --text-primary: #ffffff;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-card);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>

<body>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <a href="dashboard.html" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-cog"></i>
            </div>
            <span>Admin Panel</span>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section-title">Dashboard</div>
        <a href="dashboard.html" class="nav-item <?php echo isActivePage('dashboard'); ?>">
            <i class="nav-item-icon fas fa-tachometer-alt"></i>
            <span class="nav-item-text">Dashboard</span>
        </a>

        <div class="nav-section-title">Quản lý hệ thống</div>
        <a href="website-settings.html" class="nav-item <?php echo isActivePage('website-settings'); ?>">
            <i class="nav-item-icon fas fa-user-friends"></i>
            <span class="nav-item-text">Mã giới thiệu</span>
        </a>
        <a href="users.html" class="nav-item <?php echo isActivePage('users'); ?>">
            <i class="nav-item-icon fas fa-users"></i>
            <span class="nav-item-text">Quản lý khách hàng</span>
        </a>
   
       
        
        <a href="withdraw-history.html" class="nav-item <?php echo isActivePage('withdraw-history'); ?>">
            <i class="nav-item-icon fas fa-money-check-alt"></i>
            <span class="nav-item-text">Xử lý rút tiền</span>
        </a>
        <a href="bank-bind.html" class="nav-item <?php echo isActivePage('bank-bind'); ?>">
            <i class="nav-item-icon fas fa-university"></i>
            <span class="nav-item-text">Quản lý ngân hàng</span>
        </a>

        <div class="nav-section-title">VIP & Thành viên</div>
        <a href="vip-level.html" class="nav-item <?php echo isActivePage('vip-level'); ?>">
            <i class="nav-item-icon fas fa-crown"></i>
            <span class="nav-item-text">Cấp độ VIP</span>
        </a>
   

        <div class="nav-section-title">Trò chơi & Giải trí</div>
        <a href="lottery-category.html" class="nav-item <?php echo isActivePage('lottery-category'); ?>">
            <i class="nav-item-icon fas fa-tags"></i>
            <span class="nav-item-text">Danh mục lottery</span>
        </a>
        <a href="lottery.html" class="nav-item <?php echo isActivePage('lottery'); ?>">
            <i class="nav-item-icon fas fa-ticket-alt"></i>
            <span class="nav-item-text">Danh sách lottery</span>
        </a>
        <a href="lottery-edit.html" class="nav-item <?php echo isActivePage('lottery-edit'); ?>">
            <i class="nav-item-icon fas fa-edit"></i>
            <span class="nav-item-text">Sửa kết quả</span>
        </a>
        
        <a href="lottery-history.html" class="nav-item <?php echo isActivePage('lottery-history'); ?>">
            <i class="nav-item-icon fas fa-history"></i>
            <span class="nav-item-text">Lịch sử lottery</span>
        </a>
        <a href="lottery-result.html" class="nav-item <?php echo isActivePage('lottery-result'); ?>">
            <i class="nav-item-icon fas fa-chart-bar"></i>
            <span class="nav-item-text">Kết quả lottery</span>
        </a>
        
        <div class="nav-section-title">Thông báo & Tin nhắn</div>
        <a href="send-notification.html" class="nav-item <?php echo isActivePage('send-notification'); ?>">
            <i class="nav-item-icon fas fa-bell"></i>
            <span class="nav-item-text">Quản lý thông báo</span>
        </a>
    </nav>
</aside>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php
}

function admin_top_bar() {
?>
<style>
    .admin-main {
        margin-left: var(--sidebar-width);
        flex: 1;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background: var(--dark-bg);
    }

    .admin-header {
        background: var(--dark-surface);
        border-bottom: 1px solid var(--dark-border);
        box-shadow: var(--shadow-sm-dark);
        padding: 0 2rem;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 99;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .sidebar-toggle {
        display: none;
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 1.25rem;
        padding: 0.5rem;
        border-radius: 8px;
        transition: var(--transition);
    }

    .sidebar-toggle:hover {
        background: var(--dark-hover);
        color: var(--primary-color);
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin: 0;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--dark-card);
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid var(--dark-border);
    }

    .status-indicator.online {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border-color: rgba(16, 185, 129, 0.3);
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .theme-toggle {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 1rem;
        padding: 0.5rem;
        border-radius: 8px;
        transition: var(--transition);
    }

    .theme-toggle:hover {
        background: var(--dark-hover);
        color: var(--primary-color);
        transform: rotate(180deg);
    }

    .dropdown-menu-dark {
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        box-shadow: var(--shadow-lg-dark);
    }

    @media (max-width: 768px) {
        .admin-main {
            margin-left: 0;
        }

        .sidebar-toggle {
            display: block;
        }

        .page-title {
            font-size: 1.25rem;
        }

        .header-right {
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
        }
    }
</style>

<main class="admin-main">
    <!-- Enhanced Header with Dark Theme -->
    <header class="admin-header">
        <div class="header-left">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h1 class="page-title" id="pageTitle">Dashboard</h1>
                <p class="page-subtitle" id="pageSubtitle">Tổng quan hệ thống quản lý</p>
            </div>
        </div>
        <div class="header-right">
            <div class="header-actions">
                <!-- Theme Toggle -->
                <button class="theme-toggle" onclick="toggleTheme()" title="Chuyển đổi theme">
                    <i class="fas fa-moon"></i>
                </button>
                
                <!-- System Status -->
                <div class="status-indicator online" id="systemStatus">
                    <div class="status-dot"></div>
                    <span>Hệ thống hoạt động</span>
                </div>
                
                <!-- Quick Actions -->
                <div class="dropdown">
                    <button class="btn btn-outline btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bolt"></i>
                        Thao tác nhanh
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-plus me-2"></i>Thêm người dùng</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-file-export me-2"></i>Xuất báo cáo</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-database me-2"></i>Backup dữ liệu</a></li>
                    </ul>
                </div>
                
                <!-- Settings -->
                <button class="btn btn-outline btn-sm" onclick="openSettings()">
                    <i class="fas fa-cogs"></i>
                    Cài đặt
                </button>
                
                <!-- User Profile -->
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i>
                        <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i>Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-key me-2"></i>Đổi mật khẩu</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-chart-line me-2"></i>Thống kê cá nhân</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Enhanced JavaScript -->
    <script>
        // ===== THEME MANAGEMENT =====
        let isDarkTheme = true;
        
        function toggleTheme() {
            isDarkTheme = !isDarkTheme;
            const themeIcon = document.querySelector('.theme-toggle i');
            
            if (isDarkTheme) {
                themeIcon.className = 'fas fa-moon';
                document.documentElement.style.setProperty('--primary-gradient', 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%)');
            } else {
                themeIcon.className = 'fas fa-sun';
                document.documentElement.style.setProperty('--primary-gradient', 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%)');
            }
            
            // Add rotation animation
            themeIcon.parentElement.style.transform = 'rotate(360deg)';
            setTimeout(() => {
                themeIcon.parentElement.style.transform = '';
            }, 300);
            
            // Store preference
            localStorage.setItem('darkTheme', isDarkTheme);
            showNotification('success', 'Đã chuyển đổi theme thành công!');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('open');
            
            // Add backdrop for mobile
            if (window.innerWidth <= 768) {
                let backdrop = document.querySelector('.sidebar-backdrop');
                if (sidebar.classList.contains('open')) {
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.className = 'sidebar-backdrop';
                        backdrop.style.cssText = `
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(0, 0, 0, 0.5);
                            z-index: 99;
                            backdrop-filter: blur(2px);
                        `;
                        backdrop.onclick = toggleSidebar;
                        document.body.appendChild(backdrop);
                    }
                } else if (backdrop) {
                    backdrop.remove();
                }
            }
        }

        function openSettings() {
            showNotification('info', 'Trang cài đặt đang được phát triển', 'Thông báo');
        }

        function showNotification(type, message, title = '') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} notification-toast`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                min-width: 300px;
                max-width: 400px;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                background: var(--dark-surface);
                border: 1px solid var(--dark-border);
                color: var(--text-primary);
                box-shadow: var(--shadow-dark);
                transform: translateX(100%);
                transition: transform 0.3s ease;
            `;
            
            const iconMap = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };
            
            const colorMap = {
                success: '#10b981',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#06b6d4'
            };
            
            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-${iconMap[type]}" style="color: ${colorMap[type]}; font-size: 1.25rem;"></i>
                    <div style="flex: 1;">
                        ${title ? `<div style="font-weight: 600; margin-bottom: 0.25rem;">${title}</div>` : ''}
                        <div>${message}</div>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 0.25rem;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Auto remove
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 4000);
        }

        // ===== PAGE TITLE MANAGEMENT =====
        function updatePageInfo(page) {
            const pageTitles = {
                'dashboard': { title: 'Dashboard', subtitle: 'Tổng quan hệ thống quản lý', breadcrumb: 'Dashboard' },
                'users': { title: 'Quản lý khách hàng', subtitle: 'Danh sách và thông tin khách hàng', breadcrumb: 'Khách hàng' },
       
                'withdraw-history': { title: 'Xử lý rút tiền', subtitle: 'Quản lý giao dịch rút tiền', breadcrumb: 'Rút tiền' },
                'website-settings': { title: 'Mã giới thiệu', subtitle: 'Cài đặt hệ thống mã giới thiệu', breadcrumb: 'Mã giới thiệu' },
                'location-manager': { title: 'Quản lý vị trí', subtitle: 'Danh sách các vị trí hoạt động', breadcrumb: 'Vị trí' },
       
                'vip-level': { title: 'Cấp độ VIP', subtitle: 'Quản lý các cấp độ thành viên VIP', breadcrumb: 'VIP' },
                'bank-bind': { title: 'Quản lý ngân hàng', subtitle: 'Thông tin tài khoản ngân hàng liên kết', breadcrumb: 'Ngân hàng' },
               
                'lottery-category': { title: 'Danh mục đánh giá', subtitle: 'Quản lý danh mục đánh giá', breadcrumb: 'Danh mục' },
                'lottery': { title: 'Danh sách đánh giá', subtitle: 'Quản lý danh sách đánh giá', breadcrumb: 'Danh sách' },
                'lottery-result': { title: 'Kết quả đánh giá', subtitle: 'Quản lý kết quả đánh giá', breadcrumb: 'Kết quả' },
                'lottery-results': { title: 'Quản lý kết quả', subtitle: 'Quản lý và điều chỉnh kết quả xổ số', breadcrumb: 'Quản lý kết quả' }, // THÊM MỚI
                'lottery-history': { title: 'Lịch sử đánh giá', subtitle: 'Xem lịch sử đánh giá', breadcrumb: 'Lịch sử' },
   
            
                'send-notification': { title: 'Quản lý thông báo', subtitle: 'Gửi và quản lý thông báo hệ thống', breadcrumb: 'Thông báo' }
            };

            const currentPage = getCurrentPageFromJS(); // Sẽ cần implement hàm này bằng JS
            if (pageTitles[currentPage]) {
                document.getElementById('pageTitle').textContent = pageTitles[currentPage].title;
                document.getElementById('pageSubtitle').textContent = pageTitles[currentPage].subtitle;
                document.title = pageTitles[currentPage].title + ' - Admin Panel';
            }
        }

        // Lấy current page từ URL bằng JavaScript
        function getCurrentPageFromJS() {
            const path = window.location.pathname;
            const currentFile = path.split('/').pop().replace('.html', '').replace('.php', '');
            return currentFile === 'index' ? 'dashboard' : currentFile;
        }

        // ===== REAL-TIME UPDATES =====
        function updateSystemStatus() {
            const statusEl = document.getElementById('systemStatus');
            const isOnline = Math.random() > 0.05; // 95% uptime simulation
            
            if (isOnline) {
                statusEl.className = 'status-indicator online';
                statusEl.innerHTML = '<div class="status-dot"></div><span>Hệ thống hoạt động</span>';
            } else {
                statusEl.className = 'status-indicator offline';
                statusEl.style.background = 'rgba(239, 68, 68, 0.1)';
                statusEl.style.borderColor = '#ef4444';
                statusEl.style.color = '#ef4444';
                statusEl.innerHTML = '<div class="status-dot" style="background: #ef4444;"></div><span>Hệ thống offline</span>';
            }
        }

        // ===== NAVIGATION ENHANCEMENTS =====
        function addLoadingState(element) {
            element.classList.add('loading');
            setTimeout(() => {
                element.classList.remove('loading');
            }, 1000);
        }

        // ===== INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            // Load theme preference
            const savedTheme = localStorage.getItem('darkTheme');
            if (savedTheme !== null) {
                isDarkTheme = savedTheme === 'true';
            }
            
            // Update page info
            updatePageInfo();
            
            // Add click handlers for nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (this.getAttribute('href') !== '#') {
                        addLoadingState(this);
                    }
                });
            });
            
            // Periodic status updates
            setInterval(updateSystemStatus, 30000);
            
            // Welcome notification
            setTimeout(() => {
                showNotification('success', 'Chào mừng đến với Admin Panel!', 'Xin chào');
            }, 1000);
            
            console.log('🌙 Dark Theme Admin Panel Ready!');
        });

        // ===== KEYBOARD SHORTCUTS =====
        document.addEventListener('keydown', function(e) {
            // Alt + S for settings
            if (e.altKey && e.key === 's') {
                e.preventDefault();
                openSettings();
            }
            
            // Alt + T for theme toggle
            if (e.altKey && e.key === 't') {
                e.preventDefault();
                toggleTheme();
            }
            
            // Alt + M for sidebar toggle
            if (e.altKey && e.key === 'm') {
                e.preventDefault();
                toggleSidebar();
            }
        });
    </script>

<?php
}
?>