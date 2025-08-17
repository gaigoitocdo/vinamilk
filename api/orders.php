<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

class OrderAPI {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function createOrder($data) {
        $required_fields = ['customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'items'];
        
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return ['success' => false, 'message' => "Missing required field: {$field}"];
            }
        }
        
        try {
            $this->db->beginTransaction();
            
            // Generate order number
            $order_number = 'HN' . date('Ymd') . sprintf('%06d', rand(1, 999999));
            
            // Calculate totals
            $subtotal = 0;
            $valid_items = [];
            
            foreach ($data['items'] as $item) {
                $product = $this->db->pdo_query_one(
                    "SELECT * FROM san_pham_shop WHERE id = ? AND is_active = 1", 
                    $item['product_id']
                );
                
                if (!$product) {
                    throw new Exception("Product not found: " . $item['product_id']);
                }
                
                if ($product['stock_quantity'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for product: " . $product['title']);
                }
                
                $price = $product['gia_sale'] ?? $product['gia_goc'];
                $item_total = $price * $item['quantity'];
                $subtotal += $item_total;
                
                $valid_items[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product['title'],
                    'product_image' => $product['hinh_anh'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $price,
                    'total_price' => $item_total
                ];
            }
            
            $shipping_fee = $data['shipping_fee'] ?? 30000; // Default shipping fee
            $discount_amount = $data['discount_amount'] ?? 0;
            $total_amount = $subtotal + $shipping_fee - $discount_amount;
            
            // Insert order
            $order_id = $this->db->insert('orders', [
                'order_number' => $order_number,
                'user_id' => $_SESSION['user_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'payment_method' => $data['payment_method'] ?? 'cod',
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping_fee,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
                'notes' => $data['notes'] ?? ''
            ]);
            
            if (!$order_id) {
                throw new Exception("Failed to create order");
            }
            
            // Insert order items
            foreach ($valid_items as $item) {
                $item_id = $this->db->insert('order_items', [
                    'order_id' => $order_id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_image' => $item['product_image'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price']
                ]);
                
                if (!$item_id) {
                    throw new Exception("Failed to add order item");
                }
                
                // Update product stock and sold count
                $this->db->pdo_execute(
                    "UPDATE san_pham_shop SET stock_quantity = stock_quantity - ?, sold_count = sold_count + ? WHERE id = ?",
                    [$item['quantity'], $item['quantity'], $item['product_id']]
                );
            }
            
            // Clear cart if session_id provided
            if (isset($data['session_id'])) {
                $this->db->pdo_execute(
                    "DELETE FROM cart_items WHERE session_id = ?",
                    [$data['session_id']]
                );
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $order_id,
                    'order_number' => $order_number,
                    'total_amount' => $total_amount
                ]
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Order creation failed: ' . $e->getMessage()];
        }
    }
    
    public function getOrder($order_number) {
        $order = $this->db->pdo_query_one(
            "SELECT * FROM orders WHERE order_number = ?", 
            $order_number
        );
        
        if (!$order) {
            return ['success' => false, 'message' => 'Order not found'];
        }
        
        // Get order items
        $items = $this->db->pdo_query(
            "SELECT * FROM order_items WHERE order_id = ?", 
            $order['id']
        ) ?: [];
        
        $order['items'] = $items;
        
        return ['success' => true, 'data' => $order];
    }
    
    public function updateOrderStatus($order_id, $status) {
        $valid_statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'];
        
        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        $result = $this->db->update('orders', ['status' => $status], $order_id);
        
        return $result ? 
            ['success' => true, 'message' => 'Order status updated'] :
            ['success' => false, 'message' => 'Failed to update order status'];
    }
    
    public function getUserOrders($user_id, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $orders = $this->db->pdo_query(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
            $user_id, $limit, $offset
        ) ?: [];
        
        // Get total count
        $total = $this->db->pdo_query_value(
            "SELECT COUNT(*) FROM orders WHERE user_id = ?",
            $user_id
        ) ?? 0;
        
        return [
            'success' => true,
            'data' => $orders,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ];
    }
}

// Handle Order API requests
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

$order_api = new OrderAPI();

try {
    if ($method === 'POST') {
        $result = $order_api->createOrder($input);
    } elseif ($method === 'GET' && isset($_GET['order_number'])) {
        $result = $order_api->getOrder($_GET['order_number']);
    } elseif ($method === 'GET' && isset($_GET['user_id'])) {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $result = $order_api->getUserOrders($_GET['user_id'], $page, $limit);
    } elseif ($method === 'PUT' && isset($input['order_id']) && isset($input['status'])) {
        $result = $order_api->updateOrderStatus($input['order_id'], $input['status']);
    } else {
        $result = ['success' => false, 'message' => 'Invalid request'];
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