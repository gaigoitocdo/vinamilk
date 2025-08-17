<?php
// ========== FILE: /api/products-api.php ==========
// Backend API cho quản lý sản phẩm nhập hàng

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers for API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database connection class
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=localhost;dbname=honey_db;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->pdo = new PDO($dsn, "honey_db", "123123", $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage() . " SQL: " . $sql);
            throw new Exception("Query execution failed");
        }
    }
    
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Execute error: " . $e->getMessage() . " SQL: " . $sql);
            throw new Exception("Execute failed");
        }
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    public function commit() {
        return $this->pdo->commit();
    }
    
    public function rollback() {
        return $this->pdo->rollback();
    }
}

// Product Management Class
class ProductManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create new product
     */
    public function createProduct($data) {
        try {
            $this->db->beginTransaction();
            
            // Validate data
            if (empty($data['product_name']) || empty($data['product_price']) || empty($data['quantity'])) {
                throw new Exception("Thiếu thông tin bắt buộc");
            }
            
            // Calculate commission based on VIP level
            $commissionRate = $this->getCommissionRate($data['vip_level']);
            $commissionAmount = $data['commission'] ?: round($data['product_price'] * $commissionRate / 100);
            
            // Calculate sale percentage
            $salePercentage = 0;
            if (!empty($data['product_sale_price']) && $data['product_sale_price'] > 0) {
                $salePercentage = round((($data['product_sale_price'] - $data['product_price']) / $data['product_sale_price']) * 100);
            }
            
            // Generate random sold percentage (15-80%)
            $soldPercentage = rand(15, 80);
            
            // Insert product
            $sql = "INSERT INTO nhap_hang_products (
                name, description, image_url, price_vnd, sale_price_vnd, 
                vip_level, commission_rate, commission_amount, quantity, 
                sale_percentage, sold_percentage, is_active, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            
            $params = [
                $data['product_name'],
                $data['description'] ?? '',
                $data['product_image'] ?? '',
                $data['product_price'],
                $data['product_sale_price'] ?: $data['product_price'],
                $data['vip_level'],
                $commissionRate,
                $commissionAmount,
                $data['quantity'],
                $salePercentage,
                $soldPercentage
            ];
            
            $this->db->execute($sql, $params);
            $productId = $this->db->lastInsertId();
            
            // Log activity
            $this->logActivity('create_product', $productId, "Tạo sản phẩm mới: " . $data['product_name']);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Tạo sản phẩm thành công',
                'data' => [
                    'product_id' => $productId,
                    'name' => $data['product_name'],
                    'price' => $data['product_price'],
                    'commission' => $commissionAmount,
                    'sold_percentage' => $soldPercentage
                ]
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Create product error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get all active products for frontend display
     */
    public function getActiveProducts($limit = 20) {
        try {
            $sql = "SELECT id, name, image_url, price_vnd, sale_price_vnd, 
                           sale_percentage, sold_percentage, vip_level, commission_amount,
                           created_at, updated_at
                    FROM nhap_hang_products 
                    WHERE is_active = 1 
                    ORDER BY created_at DESC 
                    LIMIT ?";
            
            $products = $this->db->query($sql, [$limit]);
            
            // Format products for frontend
            $formattedProducts = [];
            foreach ($products as $product) {
                $formattedProducts[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'image' => $product['image_url'] ?: 'https://via.placeholder.com/300x200',
                    'price_new' => number_format($product['price_vnd']) . ' VND',
                    'price_old' => number_format($product['sale_price_vnd']) . ' VND',
                    'sold_percentage' => $product['sold_percentage'] . '%',
                    'sold_width' => $product['sold_percentage'] . '%',
                    'vip_level' => $product['vip_level'],
                    'commission' => number_format($product['commission_amount']) . ' VND',
                    'is_sale' => $product['sale_price_vnd'] > $product['price_vnd']
                ];
            }
            
            return [
                'success' => true,
                'data' => $formattedProducts,
                'total' => count($formattedProducts)
            ];
            
        } catch (Exception $e) {
            error_log("Get products error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách sản phẩm'
            ];
        }
    }
    
    /**
     * Update product
     */
    public function updateProduct($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Check if product exists
            $existingProduct = $this->db->query("SELECT * FROM nhap_hang_products WHERE id = ?", [$id]);
            if (empty($existingProduct)) {
                throw new Exception("Sản phẩm không tồn tại");
            }
            
            // Calculate commission
            $commissionRate = $this->getCommissionRate($data['vip_level']);
            $commissionAmount = $data['commission'] ?: round($data['product_price'] * $commissionRate / 100);
            
            // Update product
            $sql = "UPDATE nhap_hang_products SET 
                    name = ?, image_url = ?, price_vnd = ?, sale_price_vnd = ?,
                    vip_level = ?, commission_rate = ?, commission_amount = ?, 
                    quantity = ?, updated_at = NOW()
                    WHERE id = ?";
            
            $params = [
                $data['product_name'],
                $data['product_image'] ?? '',
                $data['product_price'],
                $data['product_sale_price'] ?: $data['product_price'],
                $data['vip_level'],
                $commissionRate,
                $commissionAmount,
                $data['quantity'],
                $id
            ];
            
            $this->db->execute($sql, $params);
            
            // Log activity
            $this->logActivity('update_product', $id, "Cập nhật sản phẩm: " . $data['product_name']);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công'
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Update product error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete product
     */
    public function deleteProduct($id) {
        try {
            $this->db->beginTransaction();
            
            // Check if product exists
            $product = $this->db->query("SELECT name FROM nhap_hang_products WHERE id = ?", [$id]);
            if (empty($product)) {
                throw new Exception("Sản phẩm không tồn tại");
            }
            
            // Soft delete (set is_active = 0)
            $sql = "UPDATE nhap_hang_products SET is_active = 0, updated_at = NOW() WHERE id = ?";
            $this->db->execute($sql, [$id]);
            
            // Log activity
            $this->logActivity('delete_product', $id, "Xóa sản phẩm: " . $product[0]['name']);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Xóa sản phẩm thành công'
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Delete product error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get commission rate from VIP level
     */
    private function getCommissionRate($vipLevel) {
        $rates = [
            'VIP 1 (5%)' => 5,
            'VIP 2 (7%)' => 7,
            'VIP 3 (10%)' => 10,
            'VIP 4 (12%)' => 12,
            'VIP 5 (15%)' => 15
        ];
        
        return $rates[$vipLevel] ?? 5;
    }
    
    /**
     * Log activity
     */
    private function logActivity($action, $productId, $description) {
        try {
            $sql = "INSERT INTO activity_logs (user_id, action, product_id, description, ip_address, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $params = [
                $_SESSION['user_id'] ?? 1,
                $action,
                $productId,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ];
            
            $this->db->execute($sql, $params);
        } catch (Exception $e) {
            error_log("Log activity error: " . $e->getMessage());
        }
    }
}

// ========== ROUTE HANDLER ==========
try {
    $productManager = new ProductManager();
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create_product':
            // Create new product
            $data = [
                'product_name' => $_POST['product_name'] ?? '',
                'product_image' => $_POST['product_image'] ?? '',
                'product_price' => (int)($_POST['product_price'] ?? 0),
                'product_sale_price' => (int)($_POST['product_sale_price'] ?? 0),
                'vip_level' => $_POST['vip_level'] ?? 'VIP 1 (5%)',
                'commission' => (int)($_POST['commission'] ?? 0),
                'quantity' => (int)($_POST['quantity'] ?? 1),
                'description' => $_POST['description'] ?? ''
            ];
            
            $result = $productManager->createProduct($data);
            echo json_encode($result);
            break;
            
        case 'get_products':
            // Get products for frontend
            $limit = (int)($_GET['limit'] ?? 20);
            $result = $productManager->getActiveProducts($limit);
            echo json_encode($result);
            break;
            
        case 'update_product':
            // Update product
            $id = (int)($_POST['product_id'] ?? 0);
            if ($id) {
                $data = [
                    'product_name' => $_POST['product_name'] ?? '',
                    'product_image' => $_POST['product_image'] ?? '',
                    'product_price' => (int)($_POST['product_price'] ?? 0),
                    'product_sale_price' => (int)($_POST['product_sale_price'] ?? 0),
                    'vip_level' => $_POST['vip_level'] ?? 'VIP 1 (5%)',
                    'commission' => (int)($_POST['commission'] ?? 0),
                    'quantity' => (int)($_POST['quantity'] ?? 1)
                ];
                
                $result = $productManager->updateProduct($id, $data);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing product ID']);
            }
            break;
            
        case 'delete_product':
            // Delete product
            $id = (int)($_POST['product_id'] ?? 0);
            if ($id) {
                $result = $productManager->deleteProduct($id);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing product ID']);
            }
            break;
            
        case 'get_vip_levels':
            // Get VIP levels for dropdown
            try {
                $vipLevels = Database::getInstance()->query("SELECT * FROM vip_level ORDER BY level ASC");
                $formattedLevels = [];
                
                foreach ($vipLevels as $level) {
                    $formattedLevels[] = [
                        'value' => "VIP {$level['level']} ({$level['commission_rate']}%)",
                        'text' => "VIP {$level['level']} ({$level['commission_rate']}%)",
                        'rate' => $level['commission_rate']
                    ];
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $formattedLevels
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lỗi khi lấy danh sách VIP'
                ]);
            }
            break;
            
        case 'sync_to_frontend':
            // Sync latest products to frontend cache
            try {
                $products = $productManager->getActiveProducts(50);
                
                if ($products['success']) {
                    // Cache for frontend (could use Redis or file cache)
                    $cacheData = json_encode($products['data']);
                    file_put_contents('/tmp/nhap_hang_cache.json', $cacheData);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Đã đồng bộ dữ liệu thành công',
                        'products_count' => count($products['data'])
                    ]);
                } else {
                    echo json_encode($products);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lỗi khi đồng bộ dữ liệu'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }
    
} catch (Exception $e) {
    error_log("API error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error'
    ]);
}



?>