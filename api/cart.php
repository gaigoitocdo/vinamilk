<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

class CartAPI {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    private function getSessionId() {
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = uniqid('cart_', true);
        }
        return $_SESSION['cart_session_id'];
    }
    
    private function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function addToCart($data) {
        $product_id = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;
        $user_id = $this->getUserId();
        
        if (!$product_id) {
            return ['success' => false, 'message' => 'Product ID is required'];
        }
        
        // Check if product exists
        $product = $this->db->pdo_query_one("SELECT * FROM san_pham_shop WHERE id = ? AND is_active = 1", $product_id);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        // Check stock
        if ($product['stock_quantity'] < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        $session_id = $this->getSessionId();
        
        // Check if item already in cart
        $where_condition = "product_id = ? AND session_id = ?";
        $params = [$product_id, $session_id];
        
        if ($user_id) {
            $where_condition .= " AND user_id = ?";
            $params[] = $user_id;
        }
        
        $existing_item = $this->db->pdo_query_one(
            "SELECT * FROM cart_items WHERE $where_condition",
            ...$params
        );
        
        if ($existing_item) {
            // Update quantity
            $new_quantity = $existing_item['quantity'] + $quantity;
            $result = $this->db->update('cart_items', ['quantity' => $new_quantity], $existing_item['id']);
        } else {
            // Add new item
            $result = $this->db->insert('cart_items', [
                'user_id' => $user_id,
                'session_id' => $session_id,
                'product_id' => $product_id,
                'quantity' => $quantity
            ]);
        }
        
        return $result ? 
            ['success' => true, 'message' => 'Item added to cart'] :
            ['success' => false, 'message' => 'Failed to add item to cart'];
    }
    
    public function getCart() {
        $session_id = $this->getSessionId();
        $user_id = $this->getUserId();
        
        $where_condition = "ci.session_id = ?";
        $params = [$session_id];
        
        if ($user_id) {
            $where_condition .= " AND (ci.user_id = ? OR ci.user_id IS NULL)";
            $params[] = $user_id;
        }
        
        $query = "
            SELECT 
                ci.*,
                p.title as product_name,
                p.gia_goc as original_price,
                p.gia_sale as sale_price,
                p.phan_tram_sale as discount_percent,
                COALESCE(p.gia_sale, p.gia_goc) as current_price,
                p.hinh_anh as product_image,
                p.stock_quantity
            FROM cart_items ci
            JOIN san_pham_shop p ON ci.product_id = p.id
            WHERE $where_condition AND p.is_active = 1
            ORDER BY ci.added_at DESC
        ";
        
        $items = $this->db->pdo_query($query, ...$params) ?: [];
        
        $total = 0;
        $total_quantity = 0;
        
        foreach ($items as &$item) {
            $item['subtotal'] = $item['current_price'] * $item['quantity'];
            $total += $item['subtotal'];
            $total_quantity += $item['quantity'];
        }
        
        return [
            'success' => true,
            'data' => [
                'items' => $items,
                'total' => $total,
                'count' => $total_quantity,
                'item_count' => count($items)
            ]
        ];
    }
    
    public function updateCartItem($item_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeCartItem($item_id);
        }
        
        $session_id = $this->getSessionId();
        $user_id = $this->getUserId();
        
        // Verify ownership
        $where_condition = "id = ? AND session_id = ?";
        $params = [$item_id, $session_id];
        
        if ($user_id) {
            $where_condition .= " AND (user_id = ? OR user_id IS NULL)";
            $params[] = $user_id;
        }
        
        $item = $this->db->pdo_query_one("SELECT * FROM cart_items WHERE $where_condition", ...$params);
        
        if (!$item) {
            return ['success' => false, 'message' => 'Cart item not found'];
        }
        
        $result = $this->db->update('cart_items', ['quantity' => $quantity], $item_id);
        
        return $result ? 
            ['success' => true, 'message' => 'Cart updated'] : 
            ['success' => false, 'message' => 'Failed to update cart'];
    }
    
    public function removeCartItem($item_id) {
        $session_id = $this->getSessionId();
        $user_id = $this->getUserId();
        
        // Verify ownership
        $where_condition = [
            ['id', '=', $item_id],
            ['session_id', '=', $session_id]
        ];
        
        if ($user_id) {
            $where_condition[] = ['user_id', '=', $user_id];
        }
        
        $result = $this->db->delete('cart_items', $where_condition);
        
        return $result ? 
            ['success' => true, 'message' => 'Item removed'] : 
            ['success' => false, 'message' => 'Failed to remove item'];
    }
    
    public function clearCart() {
        $session_id = $this->getSessionId();
        $user_id = $this->getUserId();
        
        $where_condition = [['session_id', '=', $session_id]];
        
        if ($user_id) {
            $where_condition[] = ['user_id', '=', $user_id];
        }
        
        $result = $this->db->delete('cart_items', $where_condition);
        
        return $result !== false ? 
            ['success' => true, 'message' => 'Cart cleared'] : 
            ['success' => false, 'message' => 'Failed to clear cart'];
    }
    
    public function getCartCount() {
        $session_id = $this->getSessionId();
        $user_id = $this->getUserId();
        
        $where_condition = "ci.session_id = ?";
        $params = [$session_id];
        
        if ($user_id) {
            $where_condition .= " AND (ci.user_id = ? OR ci.user_id IS NULL)";
            $params[] = $user_id;
        }
        
        $query = "
            SELECT COALESCE(SUM(ci.quantity), 0) as total_count
            FROM cart_items ci
            JOIN san_pham_shop p ON ci.product_id = p.id
            WHERE $where_condition AND p.is_active = 1
        ";
        
        $result = $this->db->pdo_query_one($query, ...$params);
        $count = $result['total_count'] ?? 0;
        
        return ['success' => true, 'count' => $count];
    }
}

// Handle Cart API requests
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

$cart_api = new CartAPI();

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['action']) && $_GET['action'] === 'count') {
                $result = $cart_api->getCartCount();
            } else {
                $result = $cart_api->getCart();
            }
            break;
            
        case 'POST':
            $result = $cart_api->addToCart($input);
            break;
            
        case 'PUT':
            $item_id = $input['item_id'] ?? null;
            $quantity = $input['quantity'] ?? null;
            if ($item_id && $quantity !== null) {
                $result = $cart_api->updateCartItem($item_id, $quantity);
            } else {
                $result = ['success' => false, 'message' => 'Missing parameters'];
            }
            break;
            
        case 'DELETE':
            if (isset($input['clear']) && $input['clear'] === true) {
                $result = $cart_api->clearCart();
            } else {
                $item_id = $input['item_id'] ?? null;
                if ($item_id) {
                    $result = $cart_api->removeCartItem($item_id);
                } else {
                    $result = ['success' => false, 'message' => 'Item ID required'];
                }
            }
            break;
            
        default:
            $result = ['success' => false, 'message' => 'Method not allowed'];
    }
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
