<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gửi Thông Báo - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<style>
    :root {
        --dark-bg: #1a1d29;
        --dark-surface: #242938;
        --dark-card: #2a2f42;
        --dark-border: #363b4d;
        --dark-hover: #3a4052;
        --text-primary: #ffffff;
        --text-secondary: #b8bcc8;
        --text-muted: #8b92a5;
        --text-accent: #e2e8f0;
        --primary-color: #6366f1;
        --primary-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --border-radius: 8px;
        --border-radius-sm: 6px;
        --transition: all 0.2s ease;
        --shadow-lg-dark: 0 8px 16px rgba(0, 0, 0, 0.2);
        --shadow-dark: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    body {
        background: var(--dark-bg);
        color: var(--text-primary);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        font-size: 14px;
    }

    .content-area {
        padding: 1rem;
        background: var(--dark-bg);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .stat-card {
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius-sm);
        padding: 0.75rem;
        text-align: center;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-gradient);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-dark);
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-icon {
        font-size: 1.5rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        opacity: 0.8;
    }

    .toast-custom {
        position: fixed;
        top: 60px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10000;
        background: #2e2257;
        color: #fff;
        padding: 18px 30px;
        border-radius: 18px;
        font-size: 16px;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(0,0,0,0.24);
        text-align: center;
        border: 2px solid #fa19c4;
        opacity: 1;
        transition: opacity 0.3s ease;
        max-width: 90%;
        word-wrap: break-word;
    }

    .toast-custom.hide {
        opacity: 0;
    }

    .toast-success {
        background: #1e3a32 !important;
        border-color: #10b981 !important;
    }

    .toast-error {
        background: #3c1e1e !important;
        border-color: #ef4444 !important;
    }

    .card {
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg-dark);
        transition: var(--transition);
        margin-bottom: 1rem;
    }

    .card-header {
        background: var(--dark-card);
        border-bottom: 1px solid var(--dark-border);
        padding: 0.75rem 1rem;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
    }

    .card-body {
        padding: 1rem;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.25rem;
        font-weight: 500;
        color: var(--text-accent);
        font-size: 0.8rem;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
      
        border: 1px solid var(--dark-border);
        color: var(--text-primary);
        border-radius: var(--border-radius-sm);
        font-size: 0.8rem;
        transition: var(--transition);
    }

    .form-control:focus, .form-select:focus {
        outline: none;
   
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
       
    }

    .form-control::placeholder {
        color: var(--text-muted);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: var(--border-radius-sm);
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: var(--transition);
        white-space: nowrap;
    }

    .btn-primary {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .select-all-btn {
        background: var(--dark-hover);
        color: var(--text-primary);
        border: none;
        padding: 0.25rem 0.5rem;
        border-radius: var(--border-radius-sm);
        font-size: 0.7rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .select-all-btn:hover {
        background: var(--primary-color);
    }

    .preview-area {
        background: var(--dark-card);
        border-radius: var(--border-radius);
        padding: 1rem;
        border: 1px solid var(--dark-border);
        position: sticky;
        top: 1rem;
    }

    .preview-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .notification-preview {
        background: var(--dark-surface);
        border-radius: var(--border-radius-sm);
        padding: 0.75rem;
        border-left: 3px solid var(--primary-color);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .notification-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .notification-icon {
        margin-right: 0.5rem;
        font-size: 1rem;
        color: var(--primary-color);
    }

    .notification-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-primary);
    }

    .notification-content {
        color: var(--text-secondary);
        line-height: 1.4;
        margin-bottom: 0.5rem;
        font-size: 0.8rem;
    }

    .notification-time {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    .user-selection {
        margin-bottom: 1rem;
    }

    .user-search-container {
        margin-bottom: 0.5rem;
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .user-search {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius-sm);
        font-size: 0.75rem;
        background: var(--dark-card);
        color: var(--text-primary);
    }

    .user-controls {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .selected-count {
        font-size: 0.7rem;
        color: var(--text-secondary);
        padding: 0.25rem 0.5rem;
        background: var(--dark-card);
        border-radius: var(--border-radius-sm);
        border: 1px solid var(--dark-border);
    }

    .user-list-container {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius-sm);
        background: var(--dark-surface);
    }

    .user-checkbox {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-bottom: 1px solid var(--dark-border);
        transition: var(--transition);
        cursor: pointer;
    }

    .user-checkbox:hover {
        background: rgba(99, 102, 241, 0.05);
    }

    .user-checkbox.selected {
        background: rgba(99, 102, 241, 0.1);
        border-left: 3px solid var(--primary-color);
    }

    .user-info {
        display: flex;
        align-items: center;
        flex-grow: 1;
        min-width: 0;
    }

    .user-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        margin-right: 0.5rem;
        font-size: 0.7rem;
        flex-shrink: 0;
    }

    .user-details {
        flex-grow: 1;
        min-width: 0;
    }

    .user-name {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.75rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email {
        font-size: 0.7rem;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-badges {
        display: flex;
        gap: 0.25rem;
        flex-shrink: 0;
    }

    .vip-badge {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: #333;
        padding: 0.125rem 0.375rem;
        border-radius: 8px;
        font-size: 0.6rem;
        font-weight: 600;
    }

    .status-badge {
        padding: 0.125rem 0.375rem;
        border-radius: 8px;
        font-size: 0.6rem;
        font-weight: 500;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.2);
        color: var(--success-color);
    }

    .status-inactive {
        background: rgba(239, 68, 68, 0.2);
        color: var(--danger-color);
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-bottom: 0.75rem;
    }

    .form-check input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--primary-color);
    }

    .form-check label {
        margin: 0;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 0.8rem;
    }

    .char-counter {
        color: var(--text-muted);
        font-size: 0.7rem;
        float: right;
        margin-top: 0.25rem;
    }

    .loading {
        text-align: center;
        padding: 1rem;
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    .no-users-found {
        text-align: center;
        padding: 1rem;
        color: var(--text-muted);
        font-style: italic;
        font-size: 0.8rem;
    }

    .api-status {
        position: fixed;
        top: 10px;
        right: 10px;
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 1000;
        background: #2a2f42;
        color: #f59e0b;
        border: 1px solid #f59e0b;
        cursor: pointer;
        transition: all 0.3s ease;
        max-width: 200px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .content-area {
            padding: 0.75rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .preview-area {
            position: static;
        }

        .api-status {
            position: relative;
            top: auto;
            right: auto;
            margin: 10px 0;
            display: block;
        }
    }
</style>

<body>
    <div class="content-area">
        <!-- API Status Indicator -->
        <div id="apiStatus" class="api-status">🔄 Connecting...</div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number" id="totalUsers">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="stat-label">Tổng Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-number" id="onlineUsers">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="stat-label">Users Online</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-number" id="vipUsers">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="stat-label">VIP Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="stat-number" id="sentNotifications">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="stat-label">Đã gửi</div>
            </div>
            <div class="stat-card" id="refreshCard" style="cursor: pointer;">
                <div class="stat-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="stat-number">Refresh</div>
                <div class="stat-label">Tải lại dữ liệu</div>
            </div>
        </div>

        <div class="form-grid">
            <!-- Form Column -->
            <div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-bell"></i>
                            Tạo thông báo mới
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="notificationForm">
                            <div class="form-group">
                                <label for="notificationTitle">Tiêu đề *</label>
                                <input type="text" class="form-control" id="notificationTitle" placeholder="Nhập tiêu đề thông báo..." required>
                            </div>

                            <div class="form-group">
                                <label for="notificationContent">Nội dung *</label>
                                <textarea class="form-control" id="notificationContent" placeholder="Nhập nội dung thông báo..." required></textarea>
                                <small class="char-counter" id="charCounter">0/500 ký tự</small>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label for="notificationType">Loại thông báo</label>
                                    <select class="form-select" id="notificationType">
                                        <option value="info">Thông tin</option>
                                        <option value="success">Thành công</option>
                                        <option value="warning">Cảnh báo</option>
                                        <option value="error">Lỗi</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="notificationIcon">Icon</label>
                                    <select class="form-select" id="notificationIcon">
                                        <option value="fas fa-info-circle">Thông tin</option>
                                        <option value="fas fa-check-circle">Thành công</option>
                                        <option value="fas fa-exclamation-triangle">Cảnh báo</option>
                                        <option value="fas fa-times-circle">Lỗi</option>
                                        <option value="fas fa-bell">Thông báo</option>
                                        <option value="fas fa-star">Quan trọng</option>
                                        <option value="fas fa-crown">VIP</option>
                                        <option value="fas fa-gift">Quà tặng</option>
                                        <option value="fas fa-bullhorn">Công bố</option>
                                        <option value="fas fa-heart">Cảm ơn</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" id="isImportant">
                                <label for="isImportant">Thông báo quan trọng (hiển thị nổi bật)</label>
                            </div>

                            <div class="form-group">
                                <label for="sendTo">Gửi đến</label>
                                <select class="form-select" id="sendTo">
                                    <option value="all">Tất cả users</option>
                                    <option value="vip">Chỉ VIP users</option>
                                    <option value="specific">Chọn users cụ thể</option>
                                </select>
                            </div>

                            <div id="userSelection" class="user-selection" style="display: none;">
                                <label>Chọn users:</label>
                                
                                <div class="user-search-container">
                                    <input type="text" class="user-search" id="userSearch" 
                                           placeholder="🔍 Tìm kiếm theo tên, username, email..." 
                                           onkeyup="filterUsers()">
                                    <button type="button" class="select-all-btn" onclick="clearSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="user-controls">
                                    <button type="button" class="select-all-btn" onclick="toggleSelectAll()">
                                        <i class="fas fa-check-square"></i> <span id="selectAllText">Chọn tất cả</span>
                                    </button>
                                    <button type="button" class="select-all-btn" onclick="selectVipOnly()">
                                        <i class="fas fa-crown"></i> Chỉ VIP
                                    </button>
                                    <div class="selected-count">
                                        Đã chọn: <span id="selectedCount">0</span>/<span id="totalCount">0</span>
                                    </div>
                                </div>

                                <div class="user-list-container">
                                    <div id="userList" class="loading">
                                        <i class="fas fa-spinner fa-spin"></i> Đang tải danh sách users...
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" id="sendButton">
                                <i class="fas fa-paper-plane"></i> Gửi Thông Báo
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Preview Column -->
            <div>
                <div class="preview-area">
                    <div class="preview-title">
                        <i class="fas fa-eye"></i> Xem trước
                    </div>
                    <div class="notification-preview" id="notificationPreview">
                        <div class="notification-header">
                            <i class="notification-icon fas fa-info-circle" id="previewIcon"></i>
                            <div class="notification-title" id="previewTitle">Tiêu đề thông báo</div>
                        </div>
                        <div class="notification-content" id="previewContent">Nội dung thông báo sẽ hiển thị ở đây...</div>
                        <div class="notification-time">Vừa xong</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let allUsers = [];
        let filteredUsers = [];
        let selectedUsers = new Set();
        
        // API endpoint - CORRECT PATH
        const API_ENDPOINT = 'https://aura.okbabe.club/api/admin-notifications.php';

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initializing Admin Notification System...');
            console.log('🔗 API Endpoint:', API_ENDPOINT);
            
            setupEventListeners();
            setupCharCounter();
            updatePreview();
            loadDashboardData();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Form submission
            document.getElementById('notificationForm').addEventListener('submit', handleFormSubmit);
            
            // Preview updates
            document.getElementById('notificationTitle').addEventListener('input', updatePreview);
            document.getElementById('notificationContent').addEventListener('input', updatePreview);
            document.getElementById('notificationType').addEventListener('change', updatePreview);
            document.getElementById('notificationIcon').addEventListener('change', updatePreview);

            // Send to selection
            document.getElementById('sendTo').addEventListener('change', function() {
                const userSelection = document.getElementById('userSelection');
                if (this.value === 'specific') {
                    userSelection.style.display = 'block';
                    loadUserList();
                } else {
                    userSelection.style.display = 'none';
                    selectedUsers.clear();
                    document.getElementById('userSearch').value = '';
                    filteredUsers = [...allUsers];
                }
            });

            // Refresh button
            document.getElementById('refreshCard').addEventListener('click', function() {
                const icon = this.querySelector('.fas');
                const text = this.querySelector('.stat-number');
                
                icon.className = 'fas fa-spinner fa-spin';
                text.textContent = 'Đang tải...';
                
                loadDashboardData().finally(() => {
                    icon.className = 'fas fa-sync-alt';
                    text.textContent = 'Refresh';
                });
            });

            // API status click to retry
            document.getElementById('apiStatus').addEventListener('click', function() {
                updateApiStatus('loading', 'Đang kết nối lại...');
                loadDashboardData();
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('notificationForm').dispatchEvent(new Event('submit'));
                }
            });
        }

        // Setup character counter
        function setupCharCounter() {
            const contentTextarea = document.getElementById('notificationContent');
            const charCounter = document.getElementById('charCounter');

            contentTextarea.addEventListener('input', function() {
                const length = this.value.length;
                charCounter.textContent = `${length}/500 ký tự`;
                
                if (length > 450) {
                    charCounter.style.color = 'var(--warning-color)';
                } else if (length > 500) {
                    charCounter.style.color = 'var(--danger-color)';
                    this.value = this.value.substring(0, 500);
                    charCounter.textContent = '500/500 ký tự';
                } else {
                    charCounter.style.color = 'var(--text-muted)';
                }
            });
        }

        // Load dashboard data from API
        async function loadDashboardData() {
            try {
                console.log('📊 Loading data from API...');
                updateApiStatus('loading', 'Đang kết nối...');
                
                const response = await fetch(API_ENDPOINT, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    credentials: 'omit'
                });

                console.log('📡 Response status:', response.status, response.statusText);

                if (!response.ok) {
                    if (response.status === 404) {
                        updateApiStatus('error', 'File không tồn tại');
                        showSetupInstructions();
                        throw new Error('API file not found (404). Please create /api/admin-notifications.php');
                    } else {
                        throw new Error(`API Error ${response.status}: ${response.statusText}`);
                    }
                }

                const data = await response.json();
                console.log('✅ API Response:', data);

                if (data.success) {
                    // Update statistics
                    if (data.stats) {
                        document.getElementById('totalUsers').textContent = data.stats.total || 0;
                        document.getElementById('onlineUsers').textContent = data.stats.online || 0;
                        document.getElementById('vipUsers').textContent = data.stats.vip || 0;
                        document.getElementById('sentNotifications').textContent = data.stats.sent || 0;
                    }

                    // Store users data
                    if (data.users && Array.isArray(data.users)) {
                        allUsers = data.users.map(user => ({
                            id: parseInt(user.id),
                            username: user.username || '',
                            name: user.name || user.username || '',
                            email: user.email || '',
                            vip: parseInt(user.vip) || 0,
                            status: user.status || 'active'
                        }));
                        filteredUsers = [...allUsers];
                        console.log(`👥 Loaded ${allUsers.length} users`);
                    }

                    updateApiStatus('connected', 'Kết nối thành công');
                } else {
                    throw new Error(data.message || 'API returned error');
                }
                
            } catch (error) {
                console.error('❌ API Error:', error);
                updateApiStatus('error', 'Lỗi kết nối');
                
                // Set fallback values
                document.getElementById('totalUsers').textContent = '0';
                document.getElementById('onlineUsers').textContent = '0';
                document.getElementById('vipUsers').textContent = '0';
                document.getElementById('sentNotifications').textContent = '0';
                
                showToast('❌ Lỗi API: ' + error.message, 'error');
            }
        }

        // Update API status indicator
        function updateApiStatus(status, message) {
            const statusDiv = document.getElementById('apiStatus');
            
            if (status === 'connected') {
                statusDiv.style.background = '#1e3a32';
                statusDiv.style.color = '#10b981';
                statusDiv.style.borderColor = '#10b981';
                statusDiv.textContent = '✅ ' + message;
            } else if (status === 'error') {
                statusDiv.style.background = '#3c1e1e';
                statusDiv.style.color = '#ef4444';
                statusDiv.style.borderColor = '#ef4444';
                statusDiv.textContent = '❌ ' + message;
            } else {
                statusDiv.style.background = '#2a2f42';
                statusDiv.style.color = '#f59e0b';
                statusDiv.style.borderColor = '#f59e0b';
                statusDiv.textContent = '🔄 ' + message;
            }
        }

        // Show setup instructions
        function showSetupInstructions() {
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            
            modal.innerHTML = `
                <div style="background: var(--dark-surface); padding: 2rem; border-radius: 12px; max-width: 500px; border: 2px solid var(--danger-color);">
                    <h3 style="color: var(--danger-color); margin-top: 0;">
                        <i class="fas fa-exclamation-triangle"></i> API Setup Required
                    </h3>
                    <p><strong>File không tồn tại:</strong> /api/admin-notifications.php</p>
                    
                    <div style="background: var(--dark-card); padding: 12px; border-radius: 6px; margin: 10px 0;">
                        <strong>🔧 Hướng dẫn setup:</strong><br>
                        1. Tạo folder /api/ trên server<br>
                        2. Upload file admin-notifications.php<br>
                        3. Chmod 644 cho file<br>
                        4. Kiểm tra database connection
                    </div>
                    
                    <div style="background: var(--dark-hover); padding: 8px; border-radius: 4px; font-size: 12px; margin-bottom: 15px;">
                        <strong>URL cần:</strong> ${API_ENDPOINT}
                    </div>
                    
                    <button onclick="this.closest('div').parentElement.remove(); loadDashboardData();" 
                            style="background: var(--primary-color); color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; margin-right: 10px;">
                        🔄 Thử lại
                    </button>
                    <button onclick="this.closest('div').parentElement.remove();" 
                            style="background: var(--dark-hover); color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                        ❌ Đóng
                    </button>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // Load user list for selection
        function loadUserList() {
            const userList = document.getElementById('userList');
            
            if (filteredUsers.length === 0) {
                userList.innerHTML = '<div class="no-users-found">Không tìm thấy users</div>';
                updateUserCounts();
                return;
            }

            const usersHtml = filteredUsers.map(user => `
                <div class="user-checkbox ${selectedUsers.has(user.id) ? 'selected' : ''}" onclick="toggleUser(${user.id})">
                    <input type="checkbox" id="user_${user.id}" ${selectedUsers.has(user.id) ? 'checked' : ''} 
                           onchange="updateSelectedUsers(${user.id}, this.checked)">
                    <div class="user-info">
                        <div class="user-avatar">${(user.name || user.username || '?').charAt(0).toUpperCase()}</div>
                        <div class="user-details">
                            <div class="user-name">${user.name || user.username}</div>
                            <div class="user-email">${user.email}</div>
                        </div>
                        <div class="user-badges">
                            ${user.vip >= 1 ? '<span class="vip-badge">VIP</span>' : ''}
                            <span class="status-badge status-${user.status}">${user.status === 'active' ? 'Online' : 'Offline'}</span>
                        </div>
                    </div>
                </div>
            `).join('');

            userList.innerHTML = usersHtml;
            updateUserCounts();
            updateSelectAllButton();
        }

        // Toggle user selection
        function toggleUser(userId) {
            const checkbox = document.getElementById(`user_${userId}`);
            checkbox.checked = !checkbox.checked;
            updateSelectedUsers(userId, checkbox.checked);
        }

        // Update selected users
        function updateSelectedUsers(userId, isSelected) {
            if (isSelected) {
                selectedUsers.add(userId);
            } else {
                selectedUsers.delete(userId);
            }
            
            const userDiv = document.querySelector(`#user_${userId}`).closest('.user-checkbox');
            if (isSelected) {
                userDiv.classList.add('selected');
            } else {
                userDiv.classList.remove('selected');
            }
            
            updateUserCounts();
            updateSelectAllButton();
        }

        // Update user counts
        function updateUserCounts() {
            document.getElementById('selectedCount').textContent = selectedUsers.size;
            document.getElementById('totalCount').textContent = filteredUsers.length;
        }

        // Update select all button
        function updateSelectAllButton() {
            const selectAllText = document.getElementById('selectAllText');
            const visibleUserIds = filteredUsers.map(u => u.id);
            const allVisibleSelected = visibleUserIds.every(id => selectedUsers.has(id));
            
            if (allVisibleSelected && visibleUserIds.length > 0) {
                selectAllText.textContent = 'Bỏ chọn tất cả';
            } else {
                selectAllText.textContent = 'Chọn tất cả';
            }
        }

        // Toggle select all
        function toggleSelectAll() {
            const visibleUserIds = filteredUsers.map(u => u.id);
            const allVisibleSelected = visibleUserIds.every(id => selectedUsers.has(id));
            
            if (allVisibleSelected) {
                visibleUserIds.forEach(id => {
                    selectedUsers.delete(id);
                    const checkbox = document.getElementById(`user_${id}`);
                    if (checkbox) {
                        checkbox.checked = false;
                        checkbox.closest('.user-checkbox').classList.remove('selected');
                    }
                });
            } else {
                visibleUserIds.forEach(id => {
                    selectedUsers.add(id);
                    const checkbox = document.getElementById(`user_${id}`);
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest('.user-checkbox').classList.add('selected');
                    }
                });
            }
            
            updateUserCounts();
            updateSelectAllButton();
        }

        // Select VIP users only
        function selectVipOnly() {
            selectedUsers.clear();
            document.querySelectorAll('#userList input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
                cb.closest('.user-checkbox').classList.remove('selected');
            });
            
            filteredUsers.forEach(user => {
                if (user.vip >= 1) {
                    selectedUsers.add(user.id);
                    const checkbox = document.getElementById(`user_${user.id}`);
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest('.user-checkbox').classList.add('selected');
                    }
                }
            });
            
            updateUserCounts();
            updateSelectAllButton();
        }

        // Filter users
        function filterUsers() {
            const searchTerm = document.getElementById('userSearch').value.toLowerCase().trim();
            
            if (!searchTerm) {
                filteredUsers = [...allUsers];
            } else {
                filteredUsers = allUsers.filter(user => {
                    const name = (user.name || '').toLowerCase();
                    const username = (user.username || '').toLowerCase();
                    const email = (user.email || '').toLowerCase();
                    const id = user.id.toString();
                    
                    return name.includes(searchTerm) || 
                           username.includes(searchTerm) || 
                           email.includes(searchTerm) ||
                           id.includes(searchTerm);
                });
            }
            
            loadUserList();
            updateSelectAllButton();
        }

        // Clear search
        function clearSearch() {
            document.getElementById('userSearch').value = '';
            filterUsers();
        }

        // Update preview
        function updatePreview() {
            const title = document.getElementById('notificationTitle').value || 'Tiêu đề thông báo';
            const content = document.getElementById('notificationContent').value || 'Nội dung thông báo sẽ hiển thị ở đây...';
            const type = document.getElementById('notificationType').value;
            const icon = document.getElementById('notificationIcon').value;
            
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewContent').textContent = content;
            document.getElementById('previewIcon').className = 'notification-icon ' + icon;
            
            const preview = document.getElementById('notificationPreview');
            const colors = {
                info: '#6366f1',
                success: '#10b981',
                warning: '#f59e0b',
                error: '#ef4444'
            };
            
            preview.style.borderLeftColor = colors[type] || '#6366f1';
            
            // Update time
            const now = new Date();
            const timeStr = now.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
            document.querySelector('.notification-time').textContent = `Hôm nay ${timeStr}`;
        }

        // Handle form submission
        async function handleFormSubmit(e) {
            e.preventDefault();
            
            const title = document.getElementById('notificationTitle').value.trim();
            const content = document.getElementById('notificationContent').value.trim();
            const sendTo = document.getElementById('sendTo').value;
            
            if (!title || !content) {
                showToast('❌ Vui lòng nhập đầy đủ tiêu đề và nội dung!', 'error');
                return;
            }
            
            if (sendTo === 'specific' && selectedUsers.size === 0) {
                showToast('❌ Vui lòng chọn ít nhất một user!', 'error');
                return;
            }

            const submitBtn = document.getElementById('sendButton');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';

            try {
                console.log('📤 Sending notification...');
                updateApiStatus('loading', 'Đang gửi...');
                
                const notificationData = {
                    action: 'send_notification',
                    title: title,
                    content: content,
                    type: document.getElementById('notificationType').value,
                    color: getColorByType(document.getElementById('notificationType').value),
                    icon: document.getElementById('notificationIcon').value,
                    is_important: document.getElementById('isImportant').checked,
                    send_to: sendTo,
                    selected_users: sendTo === 'specific' ? Array.from(selectedUsers) : []
                };

                console.log('📊 Payload:', notificationData);

                const response = await fetch(API_ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    credentials: 'omit',
                    body: JSON.stringify(notificationData)
                });

                if (!response.ok) {
                    throw new Error(`API Error ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();
                console.log('📨 Result:', result);

                if (result.success) {
                    const recipientCount = result.recipients || 0;
                    showToast(`🎉 Gửi thành công đến <strong>${recipientCount}</strong> người dùng!`, 'success');
                    
                    updateApiStatus('connected', 'Gửi thành công');
                    
                    // Update sent count
                    const currentSent = parseInt(document.getElementById('sentNotifications').textContent) || 0;
                    document.getElementById('sentNotifications').textContent = currentSent + recipientCount;
                    
                    // Reset form
                    resetForm();
                } else {
                    throw new Error(result.message || 'Unknown error');
                }
                
            } catch (error) {
                console.error('❌ Send error:', error);
                updateApiStatus('error', 'Lỗi gửi');
                showToast('❌ Lỗi: ' + error.message, 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // Reset form
        function resetForm() {
            document.getElementById('notificationForm').reset();
            selectedUsers.clear();
            document.getElementById('userSelection').style.display = 'none';
            updatePreview();
            document.getElementById('charCounter').textContent = '0/500 ký tự';
        }

        // Get color by type
        function getColorByType(type) {
            const colors = {
                info: '#6366f1',
                success: '#10b981',
                warning: '#f59e0b',
                error: '#ef4444'
            };
            return colors[type] || '#6366f1';
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const existingToast = document.querySelector('.toast-custom');
            if (existingToast) {
                existingToast.remove();
            }
            
            const toast = document.createElement('div');
            toast.className = `toast-custom toast-${type}`;
            toast.innerHTML = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('hide');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }, 5000);
        }

        // Initial setup
        console.log('✅ Admin Notification System Ready!');
        console.log('🔗 API:', API_ENDPOINT);
        console.log('💡 Keyboard shortcut: Ctrl + Enter to send');
    </script>
</body>
</html>