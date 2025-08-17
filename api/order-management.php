<?php
// ========== COMPLETE ORDER MANAGEMENT API - NO AUTO SPIN VERSION ==========
// Fixed: Không tự động cấp vòng quay - chỉ admin mới cấp được
// Version: 4.1.0 - No Auto Spin Assignment
// Date: 2025-07-15

// Improved CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept');
header('Access-Control-Expose-Headers: Content-Length, X-JSON');
header('Access-Control-Max-Age: 86400'); // 24 hours
header('Content-Type: application/json; charset=utf-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    header('Content-Length: 0');
    exit();
}

// Additional headers for better compatibility
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 in production
ini_set('log_errors', 1);

// Database Configuration
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=127.0.0.1;dbname=coupang_db;charset=utf8mb4",
                "coupang_db", 
                "123123",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
            error_log("✅ Database connection established successfully");
        } catch (PDOException $e) {
            error_log("❌ Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Order Pool Manager - NO AUTO SPIN VERSION
class OrderPoolManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->initializeTables();
    }
    
    /**
     * ✅ FIXED: Initialize tables - NO AUTO SPIN ASSIGNMENT
     * Users start with 0 spins, only admin can assign spins
     */
    private function initializeTables() {
        try {
            error_log("🔧 Initializing database tables (No Auto Spin Mode)...");
            
            // Check and add spin_count column to users table
            $checkColumnSql = "SHOW COLUMNS FROM users LIKE 'spin_count'";
            $stmt = $this->db->query($checkColumnSql);
            
            if ($stmt->rowCount() === 0) {
                // ✅ CHANGED: Default spin_count = 0 (not 5)
                $addColumnSql = "ALTER TABLE users ADD COLUMN spin_count INT DEFAULT 0 NOT NULL";
                $this->db->exec($addColumnSql);
                error_log("✅ Added spin_count column to users table with default 0");
            }
            
            // ✅ CHANGED: Set default to 0 for all users with NULL spin_count
            $updateDefaultSql = "UPDATE users SET spin_count = 0 WHERE spin_count IS NULL";
            $affectedRows = $this->db->exec($updateDefaultSql);
            if ($affectedRows > 0) {
                error_log("✅ Set default spin_count = 0 for {$affectedRows} users");
            }
            
            // Create spin_logs table if not exists
            $createSpinLogsSql = "CREATE TABLE IF NOT EXISTS spin_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                action VARCHAR(50) NOT NULL,
                old_count INT NOT NULL,
                new_count INT NOT NULL,
                note TEXT DEFAULT NULL,
                admin_id INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_created_at (created_at),
                INDEX idx_action (action),
                INDEX idx_admin_id (admin_id)
            )";
            $this->db->exec($createSpinLogsSql);
            
            // Create claim_logs table if not exists
            $createClaimLogsSql = "CREATE TABLE IF NOT EXISTS claim_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                order_id INT NOT NULL,
                claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_order_id (order_id),
                INDEX idx_claimed_at (claimed_at)
            )";
            $this->db->exec($createClaimLogsSql);
            
            // Create system_flags table for preventing duplicate operations
            $checkFlagSql = "CREATE TABLE IF NOT EXISTS system_flags (
                flag_name VARCHAR(50) PRIMARY KEY,
                flag_value TEXT,
                description TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->db->exec($checkFlagSql);
            
            error_log("✅ All database tables initialized successfully (No Auto Spin Mode)");
            
        } catch (Exception $e) {
            error_log("❌ Initialize tables error: " . $e->getMessage());
            throw new Exception("Failed to initialize database tables: " . $e->getMessage());
        }
    }
    
    /**
     * ✅ Deduct spin count for user
     */
    public function deductSpinCount($userId) {
        try {
            error_log("🎯 Starting spin deduction for user: {$userId}");
            
            // Validate user ID
            if (!$userId || !is_numeric($userId) || $userId <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid user ID',
                    'error_code' => 'INVALID_USER_ID',
                    'remaining_spins' => 0
                ];
            }
            
            $this->db->beginTransaction();
            
            // Get current spin count with lock
            $sql = "SELECT id, spin_count, username, fullname FROM users WHERE id = ? FOR UPDATE";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([intval($userId)]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->db->rollBack();
                error_log("❌ User {$userId} not found");
                return [
                    'success' => false,
                    'message' => 'User không tồn tại',
                    'error_code' => 'USER_NOT_FOUND',
                    'remaining_spins' => 0
                ];
            }
            
            $currentSpins = intval($user['spin_count']);
            error_log("📊 Current spin count for user {$userId} ({$user['username']}): {$currentSpins}");
            
            // Check if user has spins left
            if ($currentSpins <= 0) {
                $this->db->rollBack();
                error_log("❌ User {$userId} has no spins left");
                return [
                    'success' => false,
                    'message' => 'Bạn không còn lượt quay! Liên hệ admin để được cấp thêm lượt.',
                    'error_code' => 'NO_SPINS_LEFT',
                    'remaining_spins' => 0
                ];
            }
            
            // Deduct 1 spin
            $newSpinCount = $currentSpins - 1;
            $updateSql = "UPDATE users SET spin_count = ? WHERE id = ?";
            $updateStmt = $this->db->prepare($updateSql);
            $updateResult = $updateStmt->execute([$newSpinCount, intval($userId)]);
            
            if (!$updateResult || $updateStmt->rowCount() === 0) {
                $this->db->rollBack();
                error_log("❌ Failed to update spin count for user {$userId}");
                return [
                    'success' => false,
                    'message' => 'Không thể cập nhật lượt quay',
                    'error_code' => 'UPDATE_FAILED',
                    'remaining_spins' => $currentSpins
                ];
            }
            
            // Log the deduction
            $logSql = "INSERT INTO spin_logs (user_id, action, old_count, new_count, note, created_at) 
                      VALUES (?, 'deduct', ?, ?, 'Spin wheel deduction', NOW())";
            $logStmt = $this->db->prepare($logSql);
            $logStmt->execute([$userId, $currentSpins, $newSpinCount]);
            
            $this->db->commit();
            
            error_log("✅ Spin deduction successful for user {$userId}: {$currentSpins} -> {$newSpinCount}");
            
            return [
                'success' => true,
                'message' => 'Đã trừ lượt quay thành công',
                'remaining_spins' => $newSpinCount,
                'previous_spins' => $currentSpins
            ];
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            error_log("❌ Fatal error in spin deduction for user {$userId}: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                'error_code' => 'DEDUCT_SPIN_ERROR',
                'remaining_spins' => 0
            ];
        }
    }
    
    /**
     * ✅ ADMIN ONLY: Reset all spin counts
     */
    public function resetAllSpinCounts($newCount = 0, $adminId = null) {
        try {
            error_log("🔄 Admin {$adminId} resetting all spin counts to: {$newCount}");
            
            $this->db->beginTransaction();
            
            // Log all current counts before reset
            $logSql = "INSERT INTO spin_logs (user_id, action, old_count, new_count, note, admin_id, created_at) 
                      SELECT id, 'admin_reset', spin_count, ?, 'Admin bulk reset', ?, NOW() 
                      FROM users WHERE spin_count != ?";
            $logStmt = $this->db->prepare($logSql);
            $logStmt->execute([$newCount, $adminId, $newCount]);
            
            // Reset all users
            $resetSql = "UPDATE users SET spin_count = ?";
            $resetStmt = $this->db->prepare($resetSql);
            $result = $resetStmt->execute([$newCount]);
            
            if ($result) {
                $affectedRows = $resetStmt->rowCount();
                $this->db->commit();
                
                error_log("✅ Reset completed by admin {$adminId}. Affected {$affectedRows} users");
                
                return [
                    'success' => true,
                    'message' => "Admin đã reset tất cả {$affectedRows} user về {$newCount} lượt quay",
                    'affected_rows' => $affectedRows,
                    'new_count' => $newCount
                ];
            } else {
                throw new Exception("Failed to reset spin counts");
            }
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("❌ Reset all spin counts error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi reset: ' . $e->getMessage(),
                'error_code' => 'RESET_ERROR'
            ];
        }
    }
    
    /**
     * ✅ ADMIN ONLY: Set spin count for specific user
     */
    public function setUserSpinCount($userId, $newCount, $adminId = null) {
        try {
            error_log("🎯 Admin {$adminId} setting user {$userId} spin count to: {$newCount}");
            
            $this->db->beginTransaction();
            
            // Get current count
            $getCurrentSql = "SELECT id, spin_count, username, fullname FROM users WHERE id = ?";
            $getCurrentStmt = $this->db->prepare($getCurrentSql);
            $getCurrentStmt->execute([$userId]);
            $current = $getCurrentStmt->fetch();
            
            if (!$current) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'User không tồn tại',
                    'error_code' => 'USER_NOT_FOUND'
                ];
            }
            
            $oldCount = intval($current['spin_count']);
            
            // Update spin count
            $updateSql = "UPDATE users SET spin_count = ? WHERE id = ?";
            $updateStmt = $this->db->prepare($updateSql);
            $result = $updateStmt->execute([$newCount, $userId]);
            
            if ($result && $updateStmt->rowCount() > 0) {
                // Log the change
                $logSql = "INSERT INTO spin_logs (user_id, action, old_count, new_count, note, admin_id, created_at) 
                          VALUES (?, 'admin_set', ?, ?, 'Admin manual set', ?, NOW())";
                $logStmt = $this->db->prepare($logSql);
                $logStmt->execute([$userId, $oldCount, $newCount, $adminId]);
                
                $this->db->commit();
                
                error_log("✅ Admin {$adminId} updated user {$userId} spin count: {$oldCount} -> {$newCount}");
                
                return [
                    'success' => true,
                    'message' => "Admin đã set user #{$userId} ({$current['username']}) từ {$oldCount} → {$newCount} lượt",
                    'old_count' => $oldCount,
                    'new_count' => $newCount,
                    'user_info' => [
                        'id' => $userId,
                        'username' => $current['username'],
                        'fullname' => $current['fullname']
                    ]
                ];
            } else {
                throw new Exception("No rows affected - possible concurrent modification");
            }
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("❌ Set user spin count error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi set spin count: ' . $e->getMessage(),
                'error_code' => 'SET_SPIN_ERROR'
            ];
        }
    }
    
    /**
     * ✅ ADMIN ONLY: Add spin count for user
     */
    public function addSpinCount($userId, $amount = 1, $adminId = null) {
        try {
            error_log("➕ Admin {$adminId} adding {$amount} spins to user {$userId}");
            
            $this->db->beginTransaction();
            
            // Get current spin count
            $sql = "SELECT id, spin_count, username, fullname FROM users WHERE id = ? FOR UPDATE";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'User không tồn tại',
                    'error_code' => 'USER_NOT_FOUND'
                ];
            }
            
            $currentSpins = intval($user['spin_count']);
            $newSpinCount = $currentSpins + intval($amount);
            
            // Update spin count
            $updateSql = "UPDATE users SET spin_count = ? WHERE id = ?";
            $updateStmt = $this->db->prepare($updateSql);
            $updateResult = $updateStmt->execute([$newSpinCount, $userId]);
            
            if (!$updateResult) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Không thể cập nhật lượt quay',
                    'error_code' => 'UPDATE_FAILED'
                ];
            }
            
            // Log the addition
            $logSql = "INSERT INTO spin_logs (user_id, action, old_count, new_count, note, admin_id, created_at) 
                      VALUES (?, 'admin_add', ?, ?, ?, ?, NOW())";
            $logStmt = $this->db->prepare($logSql);
            $logStmt->execute([$userId, $currentSpins, $newSpinCount, "Admin added {$amount} spins", $adminId]);
            
            $this->db->commit();
            
            error_log("✅ Admin {$adminId} added {$amount} spins to user {$userId}: {$currentSpins} -> {$newSpinCount}");
            
            return [
                'success' => true,
                'message' => "Admin đã thêm {$amount} lượt quay cho user #{$userId} ({$user['username']})",
                'remaining_spins' => $newSpinCount,
                'previous_spins' => $currentSpins,
                'added_spins' => intval($amount)
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("❌ Add spin count error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                'error_code' => 'ADD_SPIN_ERROR'
            ];
        }
    }
    
    /**
     * ✅ ADMIN ONLY: Bulk add spins to multiple users
     */
    public function bulkAddSpins($userIds, $amount = 1, $adminId = null) {
        try {
            error_log("➕ Admin {$adminId} bulk adding {$amount} spins to " . count($userIds) . " users");
            
            $results = [];
            $successCount = 0;
            $failCount = 0;
            
            foreach ($userIds as $userId) {
                $result = $this->addSpinCount($userId, $amount, $adminId);
                $results[] = [
                    'user_id' => $userId,
                    'success' => $result['success'],
                    'message' => $result['message'],
                    'remaining_spins' => $result['remaining_spins'] ?? 0
                ];
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            }
            
            error_log("✅ Bulk add completed: {$successCount} success, {$failCount} failed");
            
            return [
                'success' => true,
                'message' => "Admin bulk add completed: {$successCount} thành công, {$failCount} thất bại",
                'results' => $results,
                'summary' => [
                    'total' => count($userIds),
                    'success' => $successCount,
                    'failed' => $failCount
                ]
            ];
            
        } catch (Exception $e) {
            error_log("❌ Bulk add spins error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi bulk add: ' . $e->getMessage(),
                'error_code' => 'BULK_ADD_ERROR'
            ];
        }
    }
    
    /**
     * Create new order (available status for pool)
     */
    public function createOrder($data) {
        try {
            error_log("📦 Creating new order: " . json_encode($data));
            
            $sql = "INSERT INTO orders (
                        product_name, 
                        product_image, 
                        product_price_vnd, 
                        product_sale_price, 
                        vip_level, 
                        commission_amount, 
                        quantity, 
                        fullname, 
                        phone, 
                        status,
                        order_code,
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', ?, NOW())";
            
            // Generate unique order code
            $orderCode = 'BLX' . date('ymd') . rand(1000, 9999);
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['product_name'],
                $data['product_image'] ?? null,
                $data['product_price_vnd'],
                $data['product_sale_price'] ?? 0,
                $data['vip_level'] ?? 'VIP 1 (5%)',
                $data['commission_amount'] ?? 0,
                $data['quantity'] ?? 1,
                $data['fullname'] ?? 'Admin Created',
                $data['phone'] ?? '0000000000',
                $orderCode
            ]);
            
            if ($result) {
                $orderId = $this->db->lastInsertId();
                error_log("✅ Order created successfully: ID {$orderId}, Code {$orderCode}");
                
                return [
                    'success' => true,
                    'message' => 'Tạo đơn hàng thành công! Đã thêm vào pool sale.',
                    'order_id' => $orderId,
                    'order_code' => $orderCode,
                    'status' => 'available'
                ];
            } else {
                throw new Exception("Failed to create order");
            }
            
        } catch (Exception $e) {
            error_log("❌ Create order error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi tạo đơn hàng: ' . $e->getMessage(),
                'error_code' => 'CREATE_ORDER_ERROR'
            ];
        }
    }
    
    /**
     * Get pool orders count (available status)
     */
    public function getPoolOrdersCount() {
        try {
            $sql = "SELECT COUNT(*) as count FROM orders WHERE status = 'available'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return [
                'success' => true,
                'count' => intval($result['count']),
                'message' => 'Pool orders count retrieved successfully'
            ];
        } catch (Exception $e) {
            error_log("❌ Get pool orders count error: " . $e->getMessage());
            return [
                'success' => false,
                'count' => 0,
                'message' => $e->getMessage(),
                'error_code' => 'POOL_COUNT_ERROR'
            ];
        }
    }
    
    /**
     * Get random order from pool (for wheel spin)
     */
    public function getRandomPoolOrder() {
        try {
            $sql = "SELECT 
                        id,
                        COALESCE(order_code, CONCAT('BLX', id)) as order_code,
                        COALESCE(product_name, CONCAT('Sản phẩm Sale #', id)) as product_name,
                        product_image,
                        COALESCE(product_price_vnd, 0) as product_price_vnd,
                        COALESCE(product_sale_price, 0) as product_sale_price,
                        COALESCE(quantity, 1) as quantity,
                        COALESCE(vip_level, 'VIP 1 (5%)') as vip_level,
                        COALESCE(commission_amount, 0) as commission_amount,
                        status,
                        created_at
                    FROM orders 
                    WHERE status = 'available' 
                    ORDER BY RAND() 
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $order = $stmt->fetch();
            
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Không có sản phẩm đang sale! Admin cần thêm sản phẩm sale.',
                    'data' => null,
                    'error_code' => 'NO_AVAILABLE_PRODUCTS'
                ];
            }
            
            return [
                'success' => true,
                'data' => $order,
                'message' => 'Random order retrieved successfully'
            ];
            
        } catch (Exception $e) {
            error_log("❌ Get random pool order error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'error_code' => 'RANDOM_ORDER_ERROR'
            ];
        }
    }
    
    /**
     * Check user spin count
     */
    public function getUserSpinCount($userId) {
        try {
            $sql = "SELECT id, spin_count, username, fullname FROM users WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            
            if (!$result) {
                return [
                    'success' => false,
                    'message' => 'User không tồn tại',
                    'error_code' => 'USER_NOT_FOUND',
                    'spin_count' => 0
                ];
            }
            
            return [
                'success' => true,
                'spin_count' => intval($result['spin_count']),
                'message' => 'Spin count retrieved successfully',
                'user_info' => [
                    'id' => $result['id'],
                    'username' => $result['username'],
                    'fullname' => $result['fullname']
                ]
            ];
        } catch (Exception $e) {
            error_log("❌ Get user spin count error: " . $e->getMessage());
            return [
                'success' => false,
                'spin_count' => 0,
                'message' => $e->getMessage(),
                'error_code' => 'GET_SPIN_COUNT_ERROR'
            ];
        }
    }
    
    /**
     * User claims order from pool (available → pending)
     */
    public function claimPoolOrder($orderId, $userId, $skipSpinDeduction = false) {
        try {
            error_log("🎯 User {$userId} claiming order {$orderId}, skipSpinDeduction: " . ($skipSpinDeduction ? 'true' : 'false'));
            
            $this->db->beginTransaction();
            
            // 1. Check user exists
            $userSql = "SELECT id, spin_count, username, fullname FROM users WHERE id = ? FOR UPDATE";
            $userStmt = $this->db->prepare($userSql);
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch();
            
            if (!$user) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'User không tồn tại',
                    'error_code' => 'USER_NOT_FOUND'
                ];
            }
            
            // 2. Check if order is still available
            $checkSql = "SELECT * FROM orders WHERE id = ? AND status = 'available' FOR UPDATE";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$orderId]);
            $order = $checkStmt->fetch();
            
            if (!$order) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Sản phẩm này không còn available!',
                    'error_code' => 'ORDER_NOT_AVAILABLE'
                ];
            }
            
            // 3. Update order: available → pending
            $updateOrderSql = "UPDATE orders SET 
                status = 'pending',
                user_id = ?,
                updated_at = NOW()
                WHERE id = ? AND status = 'available'";
            
            $updateOrderStmt = $this->db->prepare($updateOrderSql);
            $orderResult = $updateOrderStmt->execute([$userId, $orderId]);
            
            if (!$orderResult || $updateOrderStmt->rowCount() === 0) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Không thể nhận sản phẩm. Có thể đã được user khác nhận.',
                    'error_code' => 'CLAIM_FAILED'
                ];
            }
            
            // 4. Only deduct spin if not skipped
            $newSpinCount = $user['spin_count'];
            if (!$skipSpinDeduction) {
                if ($user['spin_count'] <= 0) {
                    $this->db->rollBack();
                    return [
                        'success' => false,
                        'message' => 'Bạn đã hết lượt quay! Liên hệ admin để được cấp thêm lượt.',
                        'error_code' => 'NO_SPINS_LEFT',
                        'remaining_spins' => 0
                    ];
                }
                
                $newSpinCount = max(0, $user['spin_count'] - 1);
                $spinSql = "UPDATE users SET spin_count = ? WHERE id = ?";
                $spinStmt = $this->db->prepare($spinSql);
                $spinResult = $spinStmt->execute([$newSpinCount, $userId]);
                
                if (!$spinResult) {
                    $this->db->rollBack();
                    return [
                        'success' => false,
                        'message' => 'Lỗi trừ lượt quay',
                        'error_code' => 'SPIN_UPDATE_FAILED'
                    ];
                }
                
                // Log spin deduction for claim
                $logSql = "INSERT INTO spin_logs (user_id, action, old_count, new_count, note, created_at) 
                          VALUES (?, 'claim_deduct', ?, ?, ?, NOW())";
                $logStmt = $this->db->prepare($logSql);
                $logStmt->execute([$userId, $user['spin_count'], $newSpinCount, "Claim order {$orderId}"]);
            }
            
            // 5. Log claim order
            try {
                $claimSql = "INSERT INTO claim_logs (user_id, order_id, claimed_at) 
                           VALUES (?, ?, NOW())";
                $claimStmt = $this->db->prepare($claimSql);
                $claimStmt->execute([$userId, $orderId]);
            } catch (Exception $e) {
                error_log("⚠️ Claim order log error: " . $e->getMessage());
            }
            
            $this->db->commit();
            
            error_log("✅ Order {$orderId} claimed successfully by user {$userId}");
            
            return [
                'success' => true,
                'message' => 'Nhận sản phẩm thành công! Đơn hàng đã vào kho cá nhân.',
                'data' => $order,
                'remaining_spins' => $newSpinCount,
                'user_info' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'fullname' => $user['fullname']
                ]
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("❌ Claim pool order error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                'error_code' => 'SYSTEM_ERROR'
            ];
        }
    }
    
    /**
     * Get user orders (pending, delivered, cancelled)
     */
    public function getUserOrders($userId, $status = null, $limit = 20) {
        try {
            $sql = "SELECT 
                        id,
                        COALESCE(order_code, CONCAT('BLX', id)) as order_code,
                        COALESCE(product_name, CONCAT('Sản phẩm #', id)) as product_name,
                        product_image,
                        COALESCE(product_price_vnd, 0) as product_price_vnd,
                        COALESCE(commission_amount, 0) as commission_amount,
                        status,
                        created_at
                    FROM orders 
                    WHERE user_id = ?";
            
            $params = [$userId];
            
            // Filter by status if provided
            if ($status && $status !== 'all') {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ?";
            $params[] = intval($limit);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $orders,
                'count' => count($orders)
            ];
            
        } catch (Exception $e) {
            error_log("❌ Get user orders error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
                'error_code' => 'GET_USER_ORDERS_ERROR'
            ];
        }
    }
    
    /**
     * ADMIN: Get all orders with filters
     */
    public function getAllOrders($filters = []) {
        try {
            $sql = "SELECT 
                        id,
                        COALESCE(order_code, CONCAT('BLX', id)) as order_code,
                        COALESCE(product_name, CONCAT('Sản phẩm #', id)) as product_name,
                        product_image,
                        COALESCE(product_price_vnd, 0) as product_price_vnd,
                        COALESCE(commission_amount, 0) as commission_amount,
                        status,
                        user_id,
                        fullname,
                        phone,
                        vip_level,
                        quantity,
                        created_at
                    FROM orders 
                    WHERE 1=1";
            
            $params = [];
            
            // Filter by status
            if (isset($filters['status']) && $filters['status'] !== 'all') {
                if (strpos($filters['status'], ',') !== false) {
                    // Multiple statuses
                    $statuses = explode(',', $filters['status']);
                    $placeholders = str_repeat('?,', count($statuses) - 1) . '?';
                    $sql .= " AND status IN ($placeholders)";
                    $params = array_merge($params, $statuses);
                } else {
                    // Single status
                    $sql .= " AND status = ?";
                    $params[] = $filters['status'];
                }
            }
            
            // Filter by search
            if (isset($filters['search']) && !empty($filters['search'])) {
                $sql .= " AND (product_name LIKE ? OR order_code LIKE ? OR fullname LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            // Date filters
            if (isset($filters['date_from']) && !empty($filters['date_from'])) {
                $sql .= " AND DATE(created_at) >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (isset($filters['date_to']) && !empty($filters['date_to'])) {
                $sql .= " AND DATE(created_at) <= ?";
                $params[] = $filters['date_to'];
            }
            
            $sql .= " ORDER BY 
                        CASE 
                            WHEN status = 'available' THEN 1 
                            WHEN status = 'pending' THEN 2 
                            WHEN status = 'delivered' THEN 3
                            WHEN status = 'cancelled' THEN 4
                            ELSE 5 
                        END,
                        created_at DESC";
            
            // Add limit
            $limit = isset($filters['limit']) ? intval($filters['limit']) : 50;
            $sql .= " LIMIT ?";
            $params[] = $limit;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $orders,
                'count' => count($orders)
            ];
            
        } catch (Exception $e) {
            error_log("❌ Get all orders error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
                'error_code' => 'GET_ALL_ORDERS_ERROR'
            ];
        }
    }
    
    /**
     * ADMIN: Approve order (pending → delivered)
     */
    public function approveOrder($orderId, $adminId = 1) {
        try {
            $sql = "UPDATE orders SET status = 'delivered', updated_at = NOW() WHERE id = ? AND status = 'pending'";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$orderId]);
            
            if ($result && $stmt->rowCount() > 0) {
                error_log("✅ Order {$orderId} approved by admin {$adminId}");
                return [
                    'success' => true,
                    'message' => 'Đơn hàng đã được duyệt và hoàn thành!'
                ];
            } else {
                throw new Exception("Không tìm thấy đơn hàng hoặc đơn hàng đã được xử lý");
            }
            
        } catch (Exception $e) {
            error_log("❌ Approve order error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'APPROVE_ORDER_ERROR'
            ];
        }
    }
    
    /**
     * ADMIN: Reject order (pending → cancelled)
     */
    public function rejectOrder($orderId, $reason, $adminId = 1) {
        try {
            if (empty($reason)) {
                throw new Exception("Lý do từ chối không được để trống");
            }
            
            $sql = "UPDATE orders SET status = 'cancelled', reject_reason = ?, updated_at = NOW() WHERE id = ? AND status = 'pending'";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$reason, $orderId]);
            
            if ($result && $stmt->rowCount() > 0) {
                error_log("✅ Order {$orderId} rejected by admin {$adminId}: {$reason}");
                return [
                    'success' => true,
                    'message' => 'Đơn hàng đã được từ chối!'
                ];
            } else {
                throw new Exception("Không tìm thấy đơn hàng hoặc đơn hàng đã được xử lý");
            }
            
        } catch (Exception $e) {
            error_log("❌ Reject order error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'REJECT_ORDER_ERROR'
            ];
        }
    }
    
    /**
     * Get statistics
     */
    public function getStats() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_orders,
                        COUNT(CASE WHEN status = 'available' THEN 1 END) as available_orders,
                        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                        COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_orders,
                        COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders,
                        SUM(CASE WHEN status = 'delivered' THEN commission_amount ELSE 0 END) as total_commission
                    FROM orders";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return [
                'success' => true,
                'data' => [
                    'total_orders' => intval($result['total_orders']),
                    'available_orders' => intval($result['available_orders']),
                    'pending_orders' => intval($result['pending_orders']),
                    'delivered_orders' => intval($result['delivered_orders']),
                    'cancelled_orders' => intval($result['cancelled_orders']),
                    'total_commission' => floatval($result['total_commission']),
                    // Admin stats (for backward compatibility)
                    'approved_orders' => intval($result['delivered_orders']),
                    'rejected_orders' => intval($result['cancelled_orders'])
                ]
            ];
            
        } catch (Exception $e) {
            error_log("❌ Get stats error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'total_orders' => 0,
                    'pending_orders' => 0,
                    'approved_orders' => 0,
                    'rejected_orders' => 0,
                    'total_commission' => 0
                ],
                'error_code' => 'GET_STATS_ERROR'
            ];
        }
    }
    
    /**
     * Health check
     */
    public function health() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders");
            $result = $stmt->fetch();
            
            $poolStmt = $this->db->query("SELECT COUNT(*) as sale_count FROM orders WHERE status = 'available'");
            $poolResult = $poolStmt->fetch();
            
            $userStmt = $this->db->query("SELECT COUNT(*) as user_count, SUM(spin_count) as total_spins FROM users");
            $userResult = $userStmt->fetch();
            
            return [
                'success' => true,
                'message' => 'Sale system healthy (No Auto Spin Mode)',
                'data' => [
                    'total_orders' => intval($result['total']),
                    'sale_products' => intval($poolResult['sale_count']),
                    'total_users' => intval($userResult['user_count']),
                    'total_spins' => intval($userResult['total_spins']),
                    'database_status' => 'connected',
                    'auto_spin_mode' => false,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
                'error_code' => 'HEALTH_CHECK_FAILED'
            ];
        }
    }
    
    /**
     * Get spin logs for admin monitoring
     */
    public function getSpinLogs($userId = null, $limit = 50) {
        try {
            $sql = "SELECT 
                        sl.id,
                        sl.user_id,
                        sl.action,
                        sl.old_count,
                        sl.new_count,
                        sl.note,
                        sl.admin_id,
                        sl.created_at,
                        u.username,
                        u.fullname,
                        admin.username as admin_username
                    FROM spin_logs sl
                    LEFT JOIN users u ON sl.user_id = u.id
                    LEFT JOIN users admin ON sl.admin_id = admin.id
                    WHERE 1=1";
            
            $params = [];
            
            if ($userId) {
                $sql .= " AND sl.user_id = ?";
                $params[] = $userId;
            }
            
            $sql .= " ORDER BY sl.created_at DESC LIMIT ?";
            $params[] = intval($limit);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $logs = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $logs,
                'count' => count($logs)
            ];
            
        } catch (Exception $e) {
            error_log("❌ Get spin logs error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
                'error_code' => 'GET_SPIN_LOGS_ERROR'
            ];
        }
    }
    
    /**
     * Get all users with spin count (for admin management)
     */
    public function getAllUsersWithSpinCount($limit = 100) {
        try {
            $sql = "SELECT 
                        id,
                        username,
                        fullname,
                        spin_count,
                        created_at
                    FROM users 
                    ORDER BY spin_count DESC, created_at DESC 
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([intval($limit)]);
            $users = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $users,
                'count' => count($users)
            ];
            
        } catch (Exception $e) {
            error_log("❌ Get all users with spin count error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
                'error_code' => 'GET_USERS_SPIN_ERROR'
            ];
        }
    }
}

// ========== API HANDLER - NO AUTO SPIN VERSION ==========
try {
    error_log("🚀 API Handler starting (No Auto Spin Mode)...");
    $poolManager = new OrderPoolManager();
    
    $action = $_GET['action'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];
    
    error_log("📡 API Request: {$method} {$action}");
    
    switch ($action) {
        case 'health':
            echo json_encode($poolManager->health());
            break;
            
        // ========== SPIN MANAGEMENT ENDPOINTS (ADMIN ONLY) ==========
        case 'deduct_spin':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $userId = $data['user_id'] ?? null;
                
                if (!$userId) {
                    throw new Exception("User ID is required", 400);
                }
                
                echo json_encode($poolManager->deductSpinCount($userId));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'add_spin':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $userId = $data['user_id'] ?? null;
                $amount = $data['amount'] ?? 1;
                $adminId = $data['admin_id'] ?? null;
                
                if (!$userId) {
                    throw new Exception("User ID is required", 400);
                }
                
                echo json_encode($poolManager->addSpinCount($userId, $amount, $adminId));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'reset_all_spins':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $newCount = $data['count'] ?? 0;
                $adminId = $data['admin_id'] ?? null;
                
                echo json_encode($poolManager->resetAllSpinCounts($newCount, $adminId));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;

        case 'set_user_spin':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $userId = $data['user_id'] ?? null;
                $newCount = $data['count'] ?? 0;
                $adminId = $data['admin_id'] ?? null;
                
                if (!$userId) {
                    throw new Exception("User ID is required", 400);
                }
                
                echo json_encode($poolManager->setUserSpinCount($userId, $newCount, $adminId));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'get_user_spin_count':
            if ($method === 'GET') {
                $userId = $_GET['user_id'] ?? null;
                
                if (!$userId) {
                    throw new Exception("User ID is required", 400);
                }
                
                echo json_encode($poolManager->getUserSpinCount($userId));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'get_spin_logs':
            if ($method === 'GET') {
                $userId = $_GET['user_id'] ?? null;
                $limit = $_GET['limit'] ?? 50;
                
                echo json_encode($poolManager->getSpinLogs($userId, $limit));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'get_all_users_spins':
            if ($method === 'GET') {
                $limit = $_GET['limit'] ?? 100;
                echo json_encode($poolManager->getAllUsersWithSpinCount($limit));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        // ========== ORDER MANAGEMENT ENDPOINTS ==========
        case 'create_order':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!$data) {
                    throw new Exception("Invalid JSON data", 400);
                }
                
                echo json_encode($poolManager->createOrder($data));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'get_pool_count':
            echo json_encode($poolManager->getPoolOrdersCount());
            break;
            
        case 'get_random_order':
            if ($method === 'GET') {
                echo json_encode($poolManager->getRandomPoolOrder());
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'claim_order':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $orderId = $data['order_id'] ?? null;
                $userId = $data['user_id'] ?? null;
                $skipSpinDeduction = $data['skip_spin_deduction'] ?? false;
                
                if (!$orderId || !$userId) {
                    throw new Exception("Order ID and User ID are required", 400);
                }
                
                echo json_encode($poolManager->claimPoolOrder($orderId, $userId, $skipSpinDeduction));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'get_user_orders':
            if ($method === 'GET') {
                $userId = $_GET['user_id'] ?? null;
                $status = $_GET['status'] ?? null;
                $limit = $_GET['limit'] ?? 20;
                
                if (!$userId) {
                    throw new Exception("User ID is required", 400);
                }
                
                echo json_encode($poolManager->getUserOrders($userId, $status, $limit));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'get_orders':
            if ($method === 'GET') {
                $filters = [
                    'status' => $_GET['status'] ?? 'all',
                    'search' => $_GET['search'] ?? '',
                    'date_from' => $_GET['date_from'] ?? '',
                    'date_to' => $_GET['date_to'] ?? '',
                    'limit' => $_GET['limit'] ?? 50
                ];
                
                echo json_encode($poolManager->getAllOrders($filters));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'approve_order':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $orderId = $data['order_id'] ?? null;
                $adminId = $data['admin_id'] ?? 1;
                
                if (!$orderId) {
                    throw new Exception("Order ID is required", 400);
                }
                
                echo json_encode($poolManager->approveOrder($orderId, $adminId));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'reject_order':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $orderId = $data['order_id'] ?? null;
                $reason = $data['reason'] ?? null;
                $adminId = $data['admin_id'] ?? 1;
                
                if (!$orderId || !$reason) {
                    throw new Exception("Order ID and reason are required", 400);
                }
                
                echo json_encode($poolManager->rejectOrder($orderId, $reason, $adminId));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        case 'get_stats':
            if ($method === 'GET') {
                echo json_encode($poolManager->getStats());
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        // ========== BULK OPERATIONS (ADMIN ONLY) ==========
        case 'bulk_add_spins':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $users = $data['users'] ?? [];
                $amount = $data['amount'] ?? 1;
                $adminId = $data['admin_id'] ?? null;
                
                if (empty($users) || !is_array($users)) {
                    throw new Exception("Users array is required", 400);
                }
                
                echo json_encode($poolManager->bulkAddSpins($users, $amount, $adminId));
            } else {
                throw new Exception("Method not allowed", 405);
            }
            break;
            
        default:
            throw new Exception("Invalid action: " . $action, 400);
    }
    
} catch (Exception $e) {
    $code = $e->getCode() ?: 400;
    http_response_code($code);
    
    $errorResponse = [
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => 'API_ERROR',
        'timestamp' => date('Y-m-d H:i:s'),
        'debug_info' => [
            'action' => $action ?? 'unknown',
            'method' => $method ?? 'unknown',
            'auto_spin_mode' => false
        ]
    ];
    
    error_log("❌ API Error: " . json_encode($errorResponse));
    echo json_encode($errorResponse);
}

error_log("✅ API Handler completed successfully (No Auto Spin Mode)");
?>