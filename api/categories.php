<?php
// api/categories.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

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
?>