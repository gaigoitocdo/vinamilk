<?php
// api/index.php - API Router cho Nginx
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get route from query string or parse from URI
$route = $_GET['route'] ?? '';
if (empty($route)) {
    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);
    if (preg_match('#^/api/(.+)$#', $path, $matches)) {
        $route = $matches[1];
    }
}

// Remove query parameters from route
$route = strtok($route, '?');
$route_parts = explode('/', trim($route, '/'));

error_log("API Route: " . $route);
error_log("Route parts: " . json_encode($route_parts));

try {
    // Route to appropriate handler
    switch ($route_parts[0]) {
        case 'categories':
            handleCategories();
            break;
            
        case 'products':
            if (isset($route_parts[1]) && is_numeric($route_parts[1])) {
                handleSingleProduct($route_parts[1]);
            } else {
                handleProducts();
            }
            break;
            
        case 'banners':
            handleBanners();
            break;
            
        case 'coupons':
            handleCoupons();
            break;
            
        case '':
        case 'status':
            // API status endpoint
            echo json_encode([
                'success' => true,
                'message' => 'Coupang API v1.0 is running',
                'version' => '1.0',
                'timestamp' => date('Y-m-d H:i:s'),
                'endpoints' => [
                    'GET /api/categories' => 'Lấy danh sách danh mục',
                    'GET /api/products' => 'Lấy danh sách sản phẩm',
                    'GET /api/products/{id}' => 'Lấy chi tiết sản phẩm',
                    'GET /api/banners' => 'Lấy banners',
                    'GET /api/coupons' => 'Lấy coupons'
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'API endpoint not found: ' . $route,
                'error_code' => 'ENDPOINT_NOT_FOUND',
                'available_endpoints' => [
                    '/api/categories',
                    '/api/products',
                    '/api/products/{id}',
                    '/api/banners',
                    '/api/coupons'
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;
    }
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error_code' => 'INTERNAL_ERROR',
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

function handleCategories() {
    $categories = [
        [
            'id' => 1,
            'name' => 'Quần áo thời trang',
            'slug' => 'quan-ao-thoi-trang',
            'icon' => 'fas fa-tshirt',
            'product_count' => 150,
            'avg_rating' => 4.3,
            'sort_order' => 1,
            'is_active' => true,
            'description' => 'Thời trang nam nữ, phụ kiện'
        ],
        [
            'id' => 2,
            'name' => 'Sắc đẹp',
            'slug' => 'sac-dep',
            'icon' => 'fas fa-magic',
            'product_count' => 85,
            'avg_rating' => 4.5,
            'sort_order' => 2,
            'is_active' => true,
            'description' => 'Mỹ phẩm, chăm sóc da'
        ],
        [
            'id' => 3,
            'name' => 'Sinh/Trẻ sơ sinh',
            'slug' => 'sinh-tre-so-sinh',
            'icon' => 'fas fa-baby',
            'product_count' => 65,
            'avg_rating' => 4.7,
            'sort_order' => 3,
            'is_active' => true,
            'description' => 'Đồ dùng cho bé và mẹ'
        ],
        [
            'id' => 4,
            'name' => 'Đồ ăn',
            'slug' => 'do-an',
            'icon' => 'fas fa-apple-alt',
            'product_count' => 120,
            'avg_rating' => 4.2,
            'sort_order' => 4,
            'is_active' => true,
            'description' => 'Thực phẩm tươi sống, đồ khô'
        ],
        [
            'id' => 5,
            'name' => 'Đồ dùng nhà bếp',
            'slug' => 'do-dung-nha-bep',
            'icon' => 'fas fa-utensils',
            'product_count' => 95,
            'avg_rating' => 4.4,
            'sort_order' => 5,
            'is_active' => true,
            'description' => 'Nồi niêu, dao kéo, dụng cụ'
        ],
        [
            'id' => 6,
            'name' => 'Điện tử',
            'slug' => 'dien-tu',
            'icon' => 'fas fa-laptop',
            'product_count' => 200,
            'avg_rating' => 4.6,
            'sort_order' => 6,
            'is_active' => true,
            'description' => 'Laptop, điện thoại, phụ kiện'
        ],
        [
            'id' => 7,
            'name' => 'Thể thao',
            'slug' => 'the-thao',
            'icon' => 'fas fa-running',
            'product_count' => 75,
            'avg_rating' => 4.3,
            'sort_order' => 7,
            'is_active' => true,
            'description' => 'Dụng cụ, trang phục thể thao'
        ],
        [
            'id' => 8,
            'name' => 'Sách',
            'slug' => 'sach',
            'icon' => 'fas fa-book',
            'product_count' => 180,
            'avg_rating' => 4.8,
            'sort_order' => 8,
            'is_active' => true,
            'description' => 'Sách giáo khoa, tiểu thuyết, tham khảo'
        ],
        [
            'id' => 9,
            'name' => 'Đồ chơi',
            'slug' => 'do-choi',
            'icon' => 'fas fa-toy-brick',
            'product_count' => 90,
            'avg_rating' => 4.5,
            'sort_order' => 9,
            'is_active' => true,
            'description' => 'Đồ chơi trẻ em, board game'
        ],
        [
            'id' => 10,
            'name' => 'Gia dụng',
            'slug' => 'gia-dung',
            'icon' => 'fas fa-home',
            'product_count' => 110,
            'avg_rating' => 4.2,
            'sort_order' => 10,
            'is_active' => true,
            'description' => 'Đồ gia dụng, nội thất'
        ]
    ];

    echo json_encode([
        'success' => true,
        'data' => $categories,
        'total' => count($categories),
        'message' => 'Categories loaded successfully',
        'cache_timestamp' => time()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

function handleProducts() {
    // Try to include the existing products.php logic
    if (file_exists(__DIR__ . '/products.php')) {
        ob_start();
        include __DIR__ . '/products.php';
        ob_end_clean();
        return;
    }
    
    // Fallback to mock data
    $limit = min((int)($_GET['limit'] ?? 20), 100);
    $featured = $_GET['featured'] ?? null;
    $special_deal = $_GET['special_deal'] ?? null;
    $trending = $_GET['trending'] ?? null;
    
    $products = generateMockProducts($limit, [
        'featured' => $featured,
        'special_deal' => $special_deal,
        'trending' => $trending
    ]);
    
    echo json_encode([
        'success' => true,
        'data' => $products,
        'pagination' => [
            'page' => 1,
            'limit' => $limit,
            'total' => $limit,
            'total_pages' => 1
        ],
        'message' => 'Products loaded (mock data)',
        'cache_timestamp' => time()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

function handleSingleProduct($id) {
    if (!is_numeric($id)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ], JSON_UNESCAPED_UNICODE);
        return;
    }
    
    // Mock single product
    $product = generateMockProducts(1)[0];
    $product['id'] = (int)$id;
    
    echo json_encode([
        'success' => true,
        'data' => $product,
        'message' => 'Product details (mock data)'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

function handleBanners() {
    $banners = [
        [
            'id' => 1,
            'title' => 'Sale 50% toàn bộ sản phẩm',
            'image_url' => 'https://via.placeholder.com/1200x400/ff4444/ffffff?text=SALE+50%25',
            'link_url' => '/products?sale=1',
            'is_active' => true
        ],
        [
            'id' => 2,
            'title' => 'Miễn phí ship toàn quốc',
            'image_url' => 'https://via.placeholder.com/1200x400/3366ff/ffffff?text=FREE+SHIPPING',
            'link_url' => '/shipping-info',
            'is_active' => true
        ]
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $banners
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

function handleCoupons() {
    $coupons = [
        [
            'id' => 1,
            'code' => 'WELCOME50',
            'title' => 'Giảm 50K cho đơn hàng đầu tiên',
            'value' => 50000,
            'type' => 'fixed',
            'min_order_amount' => 200000,
            'is_active' => true
        ],
        [
            'id' => 2,
            'code' => 'SAVE20',
            'title' => 'Giảm 20% tối đa 100K',
            'value' => 20,
            'type' => 'percentage',
            'min_order_amount' => 100000,
            'is_active' => true
        ]
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $coupons
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

function generateMockProducts($limit, $filters = []) {
    $products = [];
    $categories = ['Quần áo', 'Điện tử', 'Sách', 'Thể thao', 'Gia dụng', 'Mỹ phẩm'];
    $adjectives = ['Cao cấp', 'Chất lượng', 'Thời trang', 'Tiện dụng', 'Hiện đại', 'Sang trọng'];
    
    for ($i = 1; $i <= $limit; $i++) {
        $originalPrice = rand(100000, 3000000);
        $discount = rand(10, 60);
        $salePrice = round($originalPrice * (100 - $discount) / 100);
        
        $product = [
            'id' => $i,
            'title' => $adjectives[array_rand($adjectives)] . ' ' . $categories[array_rand($categories)] . ' #' . $i,
            'name' => $adjectives[array_rand($adjectives)] . ' ' . $categories[array_rand($categories)] . ' #' . $i,
            'primary_image' => 'https://picsum.photos/240/200?random=' . $i,
            'hinh_anh' => 'https://picsum.photos/240/200?random=' . $i,
            'gia_goc' => $originalPrice,
            'gia_sale' => $salePrice,
            'original_price' => $originalPrice,
            'sale_price' => $salePrice,
            'final_price' => $salePrice,
            'phan_tram_sale' => $discount,
            'discount_percent' => $discount,
            'danh_gia_sao' => round(3.5 + (rand(0, 15) / 10), 1),
            'rating' => round(3.5 + (rand(0, 15) / 10), 1),
            'review_count' => rand(50, 1200),
            'view_count' => rand(100, 8000),
            'is_featured' => isset($filters['featured']) ? true : (rand(0, 1) == 1),
            'category_id' => rand(1, 6),
            'created_at' => date('Y-m-d H:i:s', time() - rand(0, 86400 * 30)),
            'mo_ta' => 'Sản phẩm chất lượng cao với thiết kế hiện đại và tính năng vượt trội.',
            'tu_khoa' => 'chất lượng, tiện dụng, hiện đại'
        ];
        
        // Apply filters
        if (isset($filters['special_deal']) && $filters['special_deal']) {
            $product['discount_percent'] = rand(30, 70);
            $product['phan_tram_sale'] = $product['discount_percent'];
        }
        
        if (isset($filters['trending']) && $filters['trending']) {
            $product['view_count'] = rand(1000, 10000);
        }
        
        $products[] = $product;
    }
    
    return $products;
}
?>