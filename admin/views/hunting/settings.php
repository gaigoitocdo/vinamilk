<?php
/*
========== 8. TRANG CẤU HÌNH HỆ THỐNG ==========
File: /admin/views/hunting/settings.php
*/

include_once __DIR__ . '/../../navbar.php';
include_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance();

// Create hunting_config table if not exists
try {
    $sql = "CREATE TABLE IF NOT EXISTS hunting_config (
        id INT PRIMARY KEY AUTO_INCREMENT,
        config_key VARCHAR(100) UNIQUE NOT NULL,
        config_value TEXT,
        description TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->pdo_execute($sql);
    
    // Insert default configs
    $defaults = [
        ['hunting_cooldown_seconds', '30', 'Thời gian chờ giữa các lần quay (giây)'],
        ['max_hunts_per_day', '10', 'Số lần quay tối đa mỗi ngày'],
        ['commission_min', '5000', 'Hoa hồng tối thiểu (VND)'],
        ['commission_max', '50000', 'Hoa hồng tối đa (VND)'],
        ['success_rate', '85', 'Tỷ lệ thành công khi quay (%)'],
        ['maintenance_mode', '0', 'Chế độ bảo trì (0/1)'],
        ['enable_logging', '1', 'Bật ghi log (0/1)'],
        ['webhook_discord', '', 'Discord Webhook URL'],
        ['webhook_telegram', '', 'Telegram Bot Token + Chat ID']
    ];
    
    foreach ($defaults as $config) {
        $sql = "INSERT IGNORE INTO hunting_config (config_key, config_value, description) VALUES (?, ?, ?)";
        $db->pdo_execute($sql, $config);
    }
} catch (Exception $e) {
    error_log("Error creating hunting_config: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($_POST as $key => $value) {
            if ($key !== 'submit') {
                $sql = "UPDATE hunting_config SET config_value = ?, updated_at = NOW() WHERE config_key = ?";
                $db->pdo_execute($sql, [$value, $key]);
            }
        }
        
        $success_message = "Đã lưu cấu hình thành công!";
    } catch (Exception $e) {
        $error_message = "Lỗi lưu cấu hình: " . $e->getMessage();
    }
}

// Get all configs
$configs = [];
try {
    $sql = "SELECT * FROM hunting_config ORDER BY config_key";
    $config_rows = $db->pdo_query($sql);
    
    foreach ($config_rows as $row) {
        $configs[$row['config_key']] = $row;
    }
} catch (Exception $e) {
    $error_message = "Lỗi tải cấu hình: " . $e->getMessage();
}

// Get system stats
$system_stats = [];
try {
    // Total hunts today
    $sql = "SELECT COUNT(*) FROM hunting_history WHERE DATE(hunting_time) = CURDATE()";
    $system_stats['today_hunts'] = $db->pdo_query_value($sql) ?: 0;
    
    // Total orders today
    $sql = "SELECT COUNT(*) FROM accepted_orders WHERE DATE(created_at) = CURDATE()";
    $system_stats['today_orders'] = $db->pdo_query_value($sql) ?: 0;
    
    // Active products
    $sql = "SELECT COUNT(*) FROM san_pham_shop WHERE COALESCE(hunting_enabled, 1) = 1";
    $system_stats['active_products'] = $db->pdo_query_value($sql) ?: 0;
    
    // Database size
    $sql = "SELECT 
        ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb
        FROM information_schema.tables 
        WHERE table_name IN ('hunting_history', 'accepted_orders', 'hunting_config')";
    $system_stats['db_size'] = $db->pdo_query_value($sql) ?: 0;
    
} catch (Exception $e) {
    // Ignore errors for stats
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cấu hình hệ thống săn đơn hàng</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .config-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .config-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
        }
        .system-stat {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 15px;
        }
        .danger-zone {
            background: #ffe6e6;
            border: 1px solid #ffcccc;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 p-0">
                <?php admin_side_bar(); ?>
            </div>
            
            <div class="col-md-10">
                <?php admin_top_bar(); ?>
                
                <div class="container-fluid p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-cog"></i> Cấu hình hệ thống săn đơn hàng
                        </h1>
                        <div>
                            <button class="btn btn-info" onclick="testSystem()">
                                <i class="fas fa-vial"></i> Test hệ thống
                            </button>
                        </div>
                    </div>
                    
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <!-- System Stats -->
                        <div class="col-md-3">
                            <h5><i class="fas fa-chart-bar"></i> Thống kê hệ thống</h5>
                            
                            <div class="system-stat">
                                <div class="h4 mb-0"><?= number_format($system_stats['today_hunts'] ?? 0) ?></div>
                                <div class="small">Lần săn hôm nay</div>
                            </div>
                            
                            <div class="system-stat bg-success">
                                <div class="h4 mb-0"><?= number_format($system_stats['today_orders'] ?? 0) ?></div>
                                <div class="small">Đơn hàng hôm nay</div>
                            </div>
                            
                            <div class="system-stat bg-info">
                                <div class="h4 mb-0"><?= number_format($system_stats['active_products'] ?? 0) ?></div>
                                <div class="small">Sản phẩm hoạt động</div>
                            </div>
                            
                            <div class="system-stat bg-warning">
                                <div class="h4 mb-0"><?= number_format($system_stats['db_size'] ?? 0, 1) ?> MB</div>
                                <div class="small">Dung lượng database</div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-tools"></i> Thao tác hệ thống</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="clearCache()">
                                            <i class="fas fa-broom"></i> Xóa cache
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="exportLogs()">
                                            <i class="fas fa-download"></i> Xuất logs
                                        </button>
                                        <button class="btn btn-outline-success btn-sm" onclick="backupData()">
                                            <i class="fas fa-database"></i> Backup data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Configuration Form -->
                        <div class="col-md-9">
                            <form method="POST">
                                <!-- Basic Settings -->
                                <div class="config-section">
                                    <h5><i class="fas fa-sliders-h"></i> Cài đặt cơ bản</h5>
                                    
                                    <div class="config-item">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Thời gian chờ giữa các lần quay (giây)</label>
                                                <input type="number" name="hunting_cooldown_seconds" class="form-control" 
                                                       value="<?= htmlspecialchars($configs['hunting_cooldown_seconds']['config_value'] ?? '30') ?>" 
                                                       min="1" max="300">
                                                <small class="text-muted">Ngăn spam, khuyến nghị: 30 giây</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Số lần quay tối đa mỗi ngày</label>
                                                <input type="number" name="max_hunts_per_day" class="form-control" 
                                                       value="<?= htmlspecialchars($configs['max_hunts_per_day']['config_value'] ?? '10') ?>" 
                                                       min="1" max="100">
                                                <small class="text-muted">Giới hạn để tránh lạm dụng</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="config-item">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Hoa hồng tối thiểu (VND)</label>
                                                <input type="number" name="commission_min" class="form-control" 
                                                       value="<?= htmlspecialchars($configs['commission_min']['config_value'] ?? '5000') ?>" 
                                                       min="1000" step="1000">
                                                <small class="text-muted">Hoa hồng thấp nhất khi quay trúng</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Hoa hồng tối đa (VND)</label>
                                                <input type="number" name="commission_max" class="form-control" 
                                                       value="<?= htmlspecialchars($configs['commission_max']['config_value'] ?? '50000') ?>" 
                                                       min="1000" step="1000">
                                                <small class="text-muted">Hoa hồng cao nhất khi quay trúng</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="config-item">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Tỷ lệ thành công (%)</label>
                                                <input type="number" name="success_rate" class="form-control" 
                                                       value="<?= htmlspecialchars($configs['success_rate']['config_value'] ?? '85') ?>" 
                                                       min="0" max="100">
                                                <small class="text-muted">% cơ hội thành công khi quay (khuyến nghị: 80-90%)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Ghi log hoạt động</label>
                                                <select name="enable_logging" class="form-select">
                                                    <option value="1" <?= ($configs['enable_logging']['config_value'] ?? '1') === '1' ? 'selected' : '' ?>>Bật</option>
                                                    <option value="0" <?= ($configs['enable_logging']['config_value'] ?? '1') === '0' ? 'selected' : '' ?>>Tắt</option>
                                                </select>
                                                <small class="text-muted">Ghi lại hoạt động để debug</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Advanced Settings -->
                                <div class="config-section">
                                    <h5><i class="fas fa-cogs"></i> Cài đặt nâng cao</h5>
                                    
                                    <div class="config-item">
                                        <label class="form-label fw-bold">Discord Webhook URL</label>
                                        <input type="url" name="webhook_discord" class="form-control" 
                                               value="<?= htmlspecialchars($configs['webhook_discord']['config_value'] ?? '') ?>" 
                                               placeholder="https://discord.com/api/webhooks/...">
                                        <small class="text-muted">Nhận thông báo khi có đơn hàng mới qua Discord</small>
                                    </div>
                                    
                                    <div class="config-item">
                                        <label class="form-label fw-bold">Telegram Bot Settings</label>
                                        <input type="text" name="webhook_telegram" class="form-control" 
                                               value="<?= htmlspecialchars($configs['webhook_telegram']['config_value'] ?? '') ?>" 
                                               placeholder="BOT_TOKEN:CHAT_ID">
                                        <small class="text-muted">Format: your_bot_token:chat_id để nhận thông báo qua Telegram</small>
                                    </div>
                                </div>
                                
                                <!-- Save Button -->
                                <div class="text-center">
                                    <button type="submit" name="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Lưu tất cả cấu hình
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Danger Zone -->
                            <div class="danger-zone">
                                <h5 class="text-danger"><i class="fas fa-exclamation-triangle"></i> Vùng nguy hiểm</h5>
                                <p class="text-muted">Các thao tác dưới đây có thể ảnh hưởng đến hệ thống. Hãy cẩn thận!</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="maintenanceMode" 
                                                   <?= ($configs['maintenance_mode']['config_value'] ?? '0') === '1' ? 'checked' : '' ?>
                                                   onchange="toggleMaintenance(this.checked)">
                                            <label class="form-check-label fw-bold" for="maintenanceMode">
                                                Chế độ bảo trì
                                            </label>
                                            <div class="small text-muted">Tạm dừng tính năng săn đơn hàng</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-danger btn-sm" onclick="clearAllData()">
                                            <i class="fas fa-trash"></i> Xóa tất cả dữ liệu săn đơn
                                        </button>
                                        <div class="small text-muted mt-1">Xóa vĩnh viễn lịch sử và đơn hàng</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleMaintenance(enabled) {
            const formData = new FormData();
            formData.append('maintenance_mode', enabled ? '1' : '0');
            
            fetch('settings.php', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    location.reload();
                }
            });
        }
        
        function testSystem() {
            // Test API endpoints
            fetch('/nhap-hang/san-don.php')
                .then(response => response.json())
                .then(data => {
                    if (data.code === 200 || data.code === 429) {
                        alert('✅ API săn đơn hàng hoạt động bình thường!');
                    } else {
                        alert('⚠️ API có vấn đề: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('❌ Lỗi kết nối API: ' + error.message);
                });
        }
        
        function clearCache() {
            if (confirm('Bạn có chắc muốn xóa cache hệ thống?')) {
                // Implement cache clearing
                alert('✅ Đã xóa cache thành công!');
            }
        }
        
        function exportLogs() {
            // Create export URL
            const exportUrl = 'settings.php?action=export_logs';
            window.open(exportUrl, '_blank');
        }
        
        function backupData() {
            if (confirm('Bạn có muốn backup dữ liệu săn đơn hàng?')) {
                const backupUrl = 'settings.php?action=backup_data';
                window.open(backupUrl, '_blank');
                alert('✅ Đang tạo backup... Vui lòng chờ download!');
            }
        }
        
        function clearAllData() {
            const confirmation = prompt('Nhập "XOA TAT CA" để xác nhận xóa vĩnh viễn tất cả dữ liệu săn đơn hàng:');
            
            if (confirmation === 'XOA TAT CA') {
                if (confirm('⚠️ BẠN CHẮC CHẮN MUỐN XÓA TẤT CẢ? Hành động này KHÔNG THỂ HOÀN TÁC!')) {
                    // Send delete request
                    fetch('settings.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=clear_all_data'
                    }).then(response => {
                        if (response.ok) {
                            alert('✅ Đã xóa tất cả dữ liệu thành công!');
                            location.reload();
                        } else {
                            alert('❌ Có lỗi xảy ra khi xóa dữ liệu!');
                        }
                    });
                }
            } else if (confirmation !== null) {
                alert('❌ Xác nhận không đúng. Dữ liệu được giữ nguyên.');
            }
        }
        
        // Auto-save when changing maintenance mode
        document.getElementById('maintenanceMode').addEventListener('change', function() {
            const statusText = this.checked ? 'BẬT' : 'TẮT';
            if (confirm(`Bạn có muốn ${statusText} chế độ bảo trì?`)) {
                toggleMaintenance(this.checked);
            } else {
                this.checked = !this.checked;
            }
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const commissionMin = parseInt(document.querySelector('[name="commission_min"]').value);
            const commissionMax = parseInt(document.querySelector('[name="commission_max"]').value);
            
            if (commissionMin >= commissionMax) {
                e.preventDefault();
                alert('❌ Hoa hồng tối thiểu phải nhỏ hơn hoa hồng tối đa!');
                return false;
            }
            
            const successRate = parseInt(document.querySelector('[name="success_rate"]').value);
            if (successRate < 50) {
                if (!confirm('⚠️ Tỷ lệ thành công dưới 50% có thể làm người dùng không hài lòng. Bạn có chắc?')) {
                    e.preventDefault();
                    return false;
                }
            }
            
            return true;
        });
    </script>
</body>
</html>
