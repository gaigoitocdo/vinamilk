<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Phương thức Thanh toán - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header p {
            color: #666;
            font-size: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 40px;
            margin-bottom: 15px;
            display: block;
        }

        .stat-icon.success { color: #28a745; }
        .stat-icon.warning { color: #ffc107; }
        .stat-icon.info { color: #17a2b8; }
        .stat-icon.primary { color: #007bff; }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .panel {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .panel-header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-title {
            font-size: 18px;
            font-weight: 600;
        }

        .panel-body {
            padding: 25px;
        }

        .btn {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #333;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 0;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s ease;
        }

        .modal-close:hover {
            background: rgba(255,255,255,0.2);
        }

        .modal-body {
            padding: 25px;
        }

        .metadata-item {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }

        .metadata-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }

        .alert-info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
            color: #0c5460;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-credit-card"></i>
                Quản lý Phương thức Thanh toán
            </h1>
            <p>Quản lý các phương thức thanh toán cho hệ thống nạp tiền</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-credit-card stat-icon success"></i>
                <div class="stat-number" id="totalMethods">5</div>
                <div class="stat-label">Tổng phương thức</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle stat-icon success"></i>
                <div class="stat-number" id="activeMethods">4</div>
                <div class="stat-label">Đang hoạt động</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock stat-icon warning"></i>
                <div class="stat-number" id="pendingTransactions">12</div>
                <div class="stat-label">Giao dịch chờ duyệt</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line stat-icon info"></i>
                <div class="stat-number" id="todayRevenue">2.5M</div>
                <div class="stat-label">Doanh thu hôm nay</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Payment Methods List -->
            <div class="panel">
                <div class="panel-header">
                    <h3 class="panel-title">
                        <i class="fas fa-list"></i>
                        Danh sách Phương thức
                    </h3>
                    <button class="btn btn-sm" onclick="openAddModal()">
                        <i class="fas fa-plus"></i>
                        Thêm mới
                    </button>
                </div>
                <div class="panel-body">
                    <div id="methodsList">
                        <!-- Payment methods will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="panel">
                <div class="panel-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <h3 class="panel-title">
                        <i class="fas fa-history"></i>
                        Giao dịch gần đây
                    </h3>
                    <button class="btn btn-sm" onclick="refreshTransactions()">
                        <i class="fas fa-refresh"></i>
                        Làm mới
                    </button>
                </div>
                <div class="panel-body">
                    <div id="transactionsList">
                        <!-- Recent transactions will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Method Modal -->
    <div id="methodModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Thêm Phương thức Thanh toán</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="methodForm">
                    <input type="hidden" id="methodId">
                    
                    <div class="form-group">
                        <label class="form-label">Tên phương thức *</label>
                        <input type="text" class="form-input" id="methodName" placeholder="VD: vietcombank" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tên hiển thị *</label>
                        <input type="text" class="form-input" id="displayName" placeholder="VD: Ngân hàng Vietcombank" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" id="methodStatus">
                            <option value="1">Hoạt động</option>
                            <option value="0">Tạm ngưng</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Thông tin thanh toán</label>
                        <div id="metadataContainer">
                            <!-- Metadata fields will be added here -->
                        </div>
                        <button type="button" class="btn btn-sm" onclick="addMetadataField()">
                            <i class="fas fa-plus"></i>
                            Thêm thông tin
                        </button>
                    </div>
                    
                    <div style="text-align: right; margin-top: 30px;">
                        <button type="button" class="btn" onclick="closeModal()" style="background: #6c757d; margin-right: 10px;">
                            <i class="fas fa-times"></i>
                            Hủy
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i>
                            Lưu lại
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Sample data - In real application, this would come from backend
        let paymentMethods = [
            {
                id: 1,
                name: 'vietcombank',
                display_name: 'Ngân hàng Vietcombank',
                status: 1,
                metadata: [
                    { key: 'Tên ngân hàng', value: 'Ngân hàng TMCP Ngoại thương Việt Nam' },
                    { key: 'Số tài khoản', value: '1234567890' },
                    { key: 'Tên chủ tài khoản', value: 'COUPANG SHOP VIETNAM' },
                    { key: 'Chi nhánh', value: 'Vietcombank - Chi nhánh Hà Nội' }
                ]
            },
            {
                id: 2,
                name: 'momo',
                display_name: 'Ví điện tử MoMo',
                status: 1,
                metadata: [
                    { key: 'Số điện thoại', value: '0987654321' },
                    { key: 'Tên tài khoản', value: 'COUPANG SHOP' },
                    { key: 'QR Code', value: 'https://example.com/qr-momo.png' }
                ]
            },
            {
                id: 3,
                name: 'techcombank',
                display_name: 'Ngân hàng Techcombank',
                status: 0,
                metadata: [
                    { key: 'Tên ngân hàng', value: 'Ngân hàng TMCP Kỹ thương Việt Nam' },
                    { key: 'Số tài khoản', value: '9876543210' },
                    { key: 'Tên chủ tài khoản', value: 'COUPANG SHOP VIETNAM' }
                ]
            }
        ];

        let recentTransactions = [
            { id: 1, user_id: 123, method: 'Vietcombank', amount: 500000, status: 0, created_at: '2025-07-04 15:30:00' },
            { id: 2, user_id: 456, method: 'MoMo', amount: 200000, status: 1, created_at: '2025-07-04 14:15:00' },
            { id: 3, user_id: 789, method: 'Vietcombank', amount: 1000000, status: 1, created_at: '2025-07-04 13:45:00' },
            { id: 4, user_id: 321, method: 'MoMo', amount: 100000, status: 2, created_at: '2025-07-04 12:20:00' }
        ];

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadPaymentMethods();
            loadRecentTransactions();
            updateStats();
        });

        function loadPaymentMethods() {
            const container = document.getElementById('methodsList');
            let html = '<table class="table"><thead><tr><th>Phương thức</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>';
            
            paymentMethods.forEach(method => {
                const statusBadge = method.status ? 
                    '<span class="badge badge-success">Hoạt động</span>' : 
                    '<span class="badge badge-danger">Tạm ngưng</span>';
                
                html += `
                    <tr>
                        <td>
                            <strong>${method.display_name}</strong><br>
                            <small style="color: #666;">${method.name}</small>
                        </td>
                        <td>${statusBadge}</td>
                        <td>
                            <div class="actions">
                                <button class="btn btn-sm btn-warning" onclick="editMethod(${method.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm ${method.status ? 'btn-danger' : 'btn-success'}" 
                                        onclick="toggleMethodStatus(${method.id})">
                                    <i class="fas fa-${method.status ? 'pause' : 'play'}"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteMethod(${method.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function loadRecentTransactions() {
            const container = document.getElementById('transactionsList');
            let html = '<table class="table"><thead><tr><th>User ID</th><th>Phương thức</th><th>Số tiền</th><th>Trạng thái</th><th>Thời gian</th></tr></thead><tbody>';
            
            recentTransactions.forEach(transaction => {
                let statusBadge = '';
                switch(transaction.status) {
                    case 0:
                        statusBadge = '<span class="badge" style="background: #fff3cd; color: #856404;">Chờ duyệt</span>';
                        break;
                    case 1:
                        statusBadge = '<span class="badge badge-success">Thành công</span>';
                        break;
                    case 2:
                        statusBadge = '<span class="badge badge-danger">Thất bại</span>';
                        break;
                }
                
                html += `
                    <tr>
                        <td>UID${transaction.user_id}</td>
                        <td>${transaction.method}</td>
                        <td>${formatMoney(transaction.amount)}₫</td>
                        <td>${statusBadge}</td>
                        <td>${formatDateTime(transaction.created_at)}</td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function updateStats() {
            document.getElementById('totalMethods').textContent = paymentMethods.length;
            document.getElementById('activeMethods').textContent = paymentMethods.filter(m => m.status).length;
            document.getElementById('pendingTransactions').textContent = recentTransactions.filter(t => t.status === 0).length;
            
            const todayRevenue = recentTransactions
                .filter(t => t.status === 1)
                .reduce((sum, t) => sum + t.amount, 0);
            document.getElementById('todayRevenue').textContent = formatMoney(todayRevenue / 1000000, 1) + 'M';
        }

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Thêm Phương thức Thanh toán';
            document.getElementById('methodForm').reset();
            document.getElementById('methodId').value = '';
            document.getElementById('metadataContainer').innerHTML = '';
            addMetadataField();
            document.getElementById('methodModal').classList.add('show');
        }

        function editMethod(id) {
            const method = paymentMethods.find(m => m.id === id);
            if (!method) return;
            
            document.getElementById('modalTitle').textContent = 'Chỉnh sửa Phương thức Thanh toán';
            document.getElementById('methodId').value = method.id;
            document.getElementById('methodName').value = method.name;
            document.getElementById('displayName').value = method.display_name;
            document.getElementById('methodStatus').value = method.status;
            
            // Load metadata
            const container = document.getElementById('metadataContainer');
            container.innerHTML = '';
            method.metadata.forEach(meta => {
                addMetadataField(meta.key, meta.value);
            });
            
            document.getElementById('methodModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('methodModal').classList.remove('show');
        }

        function addMetadataField(key = '', value = '') {
            const container = document.getElementById('metadataContainer');
            const div = document.createElement('div');
            div.className = 'metadata-item';
            div.innerHTML = `
                <button type="button" class="metadata-remove" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <input type="text" class="form-input metadata-key" placeholder="Tên trường (VD: Số tài khoản)" value="${key}">
                    <input type="text" class="form-input metadata-value" placeholder="Giá trị (VD: 1234567890)" value="${value}">
                </div>
            `;
            container.appendChild(div);
        }

        function toggleMethodStatus(id) {
            const method = paymentMethods.find(m => m.id === id);
            if (method) {
                method.status = method.status ? 0 : 1;
                loadPaymentMethods();
                updateStats();
                showAlert(`Đã ${method.status ? 'kích hoạt' : 'tạm ngưng'} phương thức ${method.display_name}`, 'success');
            }
        }

        function deleteMethod(id) {
            if (confirm('Bạn có chắc chắn muốn xóa phương thức này?')) {
                const index = paymentMethods.findIndex(m => m.id === id);
                if (index !== -1) {
                    const methodName = paymentMethods[index].display_name;
                    paymentMethods.splice(index, 1);
                    loadPaymentMethods();
                    updateStats();
                    showAlert(`Đã xóa phương thức ${methodName}`, 'success');
                }
            }
        }

        function refreshTransactions() {
            // Simulate refresh
            showAlert('Đã làm mới danh sách giao dịch', 'info');
            loadRecentTransactions();
        }

        // Form submission
        document.getElementById('methodForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('methodId').value;
            const name = document.getElementById('methodName').value;
            const displayName = document.getElementById('displayName').value;
            const status = parseInt(document.getElementById('methodStatus').value);
            
            // Collect metadata
            const metadata = [];
            document.querySelectorAll('.metadata-item').forEach(item => {
                const key = item.querySelector('.metadata-key').value;
                const value = item.querySelector('.metadata-value').value;
                if (key && value) {
                    metadata.push({ key, value });
                }
            });
            
            if (id) {
                // Update existing method
                const method = paymentMethods.find(m => m.id === parseInt(id));
                if (method) {
                    method.name = name;
                    method.display_name = displayName;
                    method.status = status;
                    method.metadata = metadata;
                    showAlert('Đã cập nhật phương thức thanh toán', 'success');
                }
            } else {
                // Add new method
                const newId = Math.max(...paymentMethods.map(m => m.id)) + 1;
                paymentMethods.push({
                    id: newId,
                    name,
                    display_name: displayName,
                    status,
                    metadata
                });
                showAlert('Đã thêm phương thức thanh toán mới', 'success');
            }
            
            loadPaymentMethods();
            updateStats();
            closeModal();
        });

        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i>
                ${message}
            `;
            
            document.body.insertBefore(alertDiv, document.body.firstChild);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        function formatMoney(amount, decimals = 0) {
            return new Intl.NumberFormat('vi-VN', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(amount);
        }

        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('vi-VN');
        }

        // Close modal when clicking outside
        document.getElementById('methodModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>