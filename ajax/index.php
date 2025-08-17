<?php
session_start();
date_default_timezone_set('Asia/Seoul');

header("Content-Type: application/json");

// Include các file cấu hình và models cần thiết
include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/CharacterModel.php";
include_once __DIR__ . "/../models/ServiceModel.php";
include_once __DIR__ . "/../models/TopupModel.php";
include_once __DIR__ . "/../models/LotteryModel.php";
include_once __DIR__ . "/../models/UserModel.php";
include_once __DIR__ . "/../models/LotteryHistoryModel.php";
include_once __DIR__ . "/../models/LotterySessionModel.php";
include_once __DIR__ . "/../models/BookingModel.php";
include_once __DIR__ . "/../models/BankModel.php";
include_once __DIR__ . "/../models/VideoModel.php";
include_once __DIR__ . "/../models/LotteryItemModel.php";
include_once __DIR__ . "/../models/WithdrawModel.php";
include_once __DIR__ . "/../models/NotiModel.php";
// Nếu chưa có định nghĩa hàm get_config(), hãy định nghĩa ngay tại đây
if (!function_exists('get_config')) {
    /**
     * Lấy giá trị cấu hình từ một biến toàn cục hoặc nguồn dữ liệu khác
     *
     * @param string $key Khóa của cấu hình cần lấy
     * @param mixed $default Giá trị mặc định nếu không tìm thấy
     * @return mixed Giá trị của cấu hình
     */
    function get_config($key, $default = null) {
        global $CONFIG;
        return isset($CONFIG[$key]) ? $CONFIG[$key] : $default;
    }
}

// Định nghĩa hàm lấy dữ liệu từ request (ví dụ từ POST)
if (!function_exists('field')) {
    /**
     * Lấy dữ liệu đầu vào theo tên field từ $_POST
     *
     * @param string $name Tên field
     * @param mixed $default Giá trị mặc định nếu không có
     * @param bool $is_required Nếu bắt buộc phải có giá trị
     * @param bool $sanitize Nếu cần làm sạch giá trị đầu vào
     * @return mixed Giá trị của field
     */
    function field($name, $default = null, $is_required = false, $sanitize = false) {
        $value = isset($_POST[$name]) ? $_POST[$name] : $default;
        if ($is_required && ($value === null || $value === '')) {
            json_response(400, ['success' => false, 'message' => "Thiếu thông tin: $name"]);
            exit;
        }
        if ($sanitize && is_string($value)) {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $value;
    }
}

// Hàm trả về phản hồi JSON
if (!function_exists('json_response')) {
    /**
     * Gửi phản hồi dưới dạng JSON
     *
     * @param int $code Mã HTTP response
     * @param array $data Dữ liệu cần trả về
     */
    function json_response($code, $data) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}

// Kiểm tra trạng thái đăng nhập
if (!function_exists('is_login')) {
    function is_login() {
        return isset($_SESSION["username"]);
    }
}

// Lấy loại hành động từ yêu cầu
$type = field("action_type");

// Xử lý các hành động theo giá trị của $type
if ($type == "login") {
    $username = field("username", null, true, true);
    $password = field("password", null, true, true);
    $hash = md5($password);
    
    $data = UserModel::Login($username, $hash);
    if (!$data) {
        return json_response(403, ["success" => false, "message" => "Thông tin đăng nhập không chính xác!"]);
    }
    if ($data["type"] != 3 && $data["status"] == 0) {
        return json_response(400, ["success" => false, "message" => "Tài khoản của bạn đã bị đình chỉ!"]);
    }
    
    $_SESSION["username"]  = $username;
    $_SESSION["id"]        = $data["id"];
    $_SESSION["user_type"] = $data["type"];
    
    // ✅ FIX: Đảm bảo response có đầy đủ thông tin cần thiết
    $response_data = [
        "success" => true,
        "user_id" => $data["id"],        // ← THÊM user_id
        "id" => $data["id"],             // ← THÊM id (backup)
        "username" => $username,
        "name" => $data["name"] ?? $username,  // ← THÊM name
        "avatar" => $data["avatar"] ?? null,
        "money" => $data["money"] ?? 0,
        "credit" => $data["credit"] ?? 0,
        "type" => $data["type"],
        "status" => $data["status"],
        "data" => $data  // ← Giữ data gốc để tương thích
    ];
    
    json_response(200, $response_data);

} else if ($type == "signup") {
    $username   = field("username", null, true, true);
    $password   = field("password", null, true, true);
    $invitecode = field("invitecode", null);
    
    if (get_config("allow_register") != 1) {
        return json_response(403, ["success" => false, "message" => "Tính năng đăng ký đã đóng!"]);
    }
    
    $db = Database::getInstance();
    
    if (get_config("reg_invitecode") == 1) {
        $d = $db->pdo_query_one("SELECT * FROM `invite_code` WHERE `invite_id` = '$invitecode'");
        if (!$d) {
            return json_response(403, ["success" => false, "message" => "Mã mời không đúng!"]);
        }
        if ($d["used_count"] >= $d["max_count"]) {
            return json_response(403, ["success" => false, "message" => "Mã mời không sử dụng được nữa!"]);
        }
    }
    
    $data = UserModel::GetOneFromUsername($username);
    if ($data) {
        return json_response(403, ["success" => false, "message" => "Tên người dùng đã tồn tại!"]);
    }
    
    // ✅ FIX: Đảm bảo user mới có role = 3 (User thường)
    UserModel::Register([
        "username" => $username,
        "password" => md5($password),
        "vip" => 0,      // Default VIP level
        "type" => 3,     // ✅ QUAN TRỌNG: Role 3 = User thường (không phải Admin)
        "status" => 1    // ✅ Active user
    ]);
    
    $data = UserModel::GetOneFromUsername($username);
    if ($data) {
        if (isset($d)) {
            $db->update("invite_code", ["used_count" => $d["used_count"] + 1], $d["id"]);
        }
        
        $_SESSION["username"]    = $username;
        $_SESSION["id"]          = $data["id"];
        $_SESSION["user_id"]     = $data["id"];
        $_SESSION["name"]        = $data["name"] ?? $username;
        $_SESSION["user_type"]   = $data["type"];  // ✅ Sẽ là 3
        $_SESSION["vip_level"]   = $data["vip"] ?? 0;
    }
    
    // ✅ Response với role đúng
    $response_data = [
        "success" => true,
        "user_id" => $data["id"],
        "id" => $data["id"],
        "username" => $username,
        "name" => $data["name"] ?? $username,
        "avatar" => $data["avatar"] ?? null,
        "money" => $data["money"] ?? 0,
        "credit" => $data["credit"] ?? 0,
        "vip" => $data["vip"] ?? 0,
        "type" => 3,  // ✅ Đảm bảo type = 3 (User thường)
        "status" => 1,
        "member_code" => $data["member_code"] ?? null,
        "data" => $data
    ];
    
    json_response(200, $response_data);
}


else if ($type == "purchase_vip" && is_login()) {
    $uid = $_SESSION["id"];
    $vip_level = field("vip_level", 0, true, true);
    
    // Define VIP prices
    $vip_prices = [
        10 => 10.00,   // Bronze
        25 => 25.00,   // Silver  
        50 => 50.00,   // Gold
        100 => 100.00  // Diamond
    ];
    
    if (!isset($vip_prices[$vip_level])) {
        return json_response(400, ["success" => false, "message" => "Invalid VIP level"]);
    }
    
    $price = $vip_prices[$vip_level];
    
    // Get user info
    $user_info = UserModel::GetOne($uid);
    if (!$user_info) {
        return json_response(400, ["success" => false, "message" => "User not found"]);
    }
    
    // Check if user already has this VIP level or higher
    if ($user_info["vip"] >= $vip_level) {
        return json_response(400, ["success" => false, "message" => "You already have this VIP level or higher"]);
    }
    
    // Check balance
    if (floatval($user_info["money"]) < $price) {
        return json_response(400, ["success" => false, "message" => "Insufficient balance. Required: $" . $price]);
    }
    
    // Deduct money and upgrade VIP
    UserModel::UpdateMoney($uid, -1 * $price, "", "VIP Upgrade to Level " . $vip_level);
    UserModel::Update($uid, ["vip" => $vip_level]);
    
    // Log VIP purchase
    $db = Database::getInstance();
    $db->insert('vip_purchases', [
        'user_id' => $uid,
        'vip_level' => $vip_level,
        'amount' => $price,
        'purchase_date' => time(),
        'status' => 1
    ]);
    
    return json_response(200, [
        "success" => true, 
        "message" => "VIP upgraded successfully",
        "vip_level" => $vip_level,
        "amount_charged" => $price
    ]);

} else if ($type == "get_vip_info" && is_login()) {
    $uid = $_SESSION["id"];
    $user_info = UserModel::GetOne($uid);
    
    if (!$user_info) {
        return json_response(400, ["success" => false, "message" => "User not found"]);
    }
    
    $vip_level = intval($user_info["vip"]);
    $user_type = intval($user_info["type"]);
    
    // Calculate VIP status
    $vip_status = "Regular User";
    $vip_benefits = ["Standard Features"];
    $vip_color = "gray";
    
    if ($user_type == 1) {
        $vip_status = "Admin";
        $vip_benefits = ["All Features", "Admin Access", "Priority Support"];
        $vip_color = "red";
    } else if ($user_type == 2) {
        $vip_status = "Staff";
        $vip_benefits = ["Staff Access", "Priority Support"];
        $vip_color = "purple";
    } else if ($vip_level >= 100) {
        $vip_status = "VIP Diamond";
        $vip_benefits = ["Priority Chat", "Exclusive Content", "50% Bonus", "No Ads"];
        $vip_color = "blue";
    } else if ($vip_level >= 50) {
        $vip_status = "VIP Gold";
        $vip_benefits = ["Priority Chat", "25% Bonus", "Reduced Ads"];
        $vip_color = "yellow";
    } else if ($vip_level >= 25) {
        $vip_status = "VIP Silver";
        $vip_benefits = ["Priority Support", "10% Bonus"];
        $vip_color = "gray";
    } else if ($vip_level >= 10) {
        $vip_status = "VIP Bronze";
        $vip_benefits = ["Basic VIP Benefits"];
        $vip_color = "orange";
    }
    
    return json_response(200, [
        "success" => true,
        "vip_level" => $vip_level,
        "vip_status" => $vip_status,
        "vip_benefits" => $vip_benefits,
        "vip_color" => $vip_color,
        "user_type" => $user_type,
        "balance" => floatval($user_info["money"])
    ]);

} else if ($type == "get_vip_history" && is_login()) {
    $uid = $_SESSION["id"];
    $db = Database::getInstance();
    
    $history = $db->pdo_query("
        SELECT * FROM vip_purchases 
        WHERE user_id = ? 
        ORDER BY purchase_date DESC 
        LIMIT 10
    ", $uid);
    
    return json_response(200, [
        "success" => true,
        "history" => $history
    ]);

} else if ($type == "check_vip_access" && is_login()) {
    $uid = $_SESSION["id"];
    $required_level = field("required_level", 0, true, true);
    
    $user_info = UserModel::GetOne($uid);
    if (!$user_info) {
        return json_response(400, ["success" => false, "message" => "User not found"]);
    }
    
    $user_vip = intval($user_info["vip"]);
    $user_type = intval($user_info["type"]);
    
    // Admin/Staff always have access
    $has_access = false;
    if ($user_type <= 2) {
        $has_access = true;
    } else {
        $has_access = $user_vip >= $required_level;
    }
    
    return json_response(200, [
        "success" => true,
        "has_access" => $has_access,
        "user_vip" => $user_vip,
        "required_level" => $required_level
    ]);

} else if ($type == "vip_bonus_claim" && is_login()) {
    $uid = $_SESSION["id"];
    $user_info = UserModel::GetOne($uid);
    
    if (!$user_info) {
        return json_response(400, ["success" => false, "message" => "User not found"]);
    }
    
    $vip_level = intval($user_info["vip"]);
    
    // Check if VIP
    if ($vip_level < 10) {
        return json_response(400, ["success" => false, "message" => "VIP membership required"]);
    }
    
    // Check last claim (daily bonus)
    $db = Database::getInstance();
    $last_claim = $db->pdo_query_one("
        SELECT * FROM vip_bonus_claims 
        WHERE user_id = ? AND DATE(claim_date) = CURDATE()
    ", $uid);
    
    if ($last_claim) {
        return json_response(400, ["success" => false, "message" => "Daily VIP bonus already claimed"]);
    }
    
    // Calculate bonus based on VIP level
    $bonus_amount = 0;
    if ($vip_level >= 100) {
        $bonus_amount = 50; // Diamond
    } else if ($vip_level >= 50) {
        $bonus_amount = 25; // Gold
    } else if ($vip_level >= 25) {
        $bonus_amount = 10; // Silver
    } else if ($vip_level >= 10) {
        $bonus_amount = 5;  // Bronze
    }
    
    // Give bonus
    UserModel::UpdateMoney($uid, $bonus_amount, "", "Daily VIP Bonus");
    
    // Record claim
    $db->insert('vip_bonus_claims', [
        'user_id' => $uid,
        'bonus_amount' => $bonus_amount,
        'vip_level' => $vip_level,
        'claim_date' => date('Y-m-d H:i:s')
    ]);
    
    return json_response(200, [
        "success" => true,
        "bonus_amount" => $bonus_amount,
        "message" => "VIP bonus claimed successfully!"
    ]);
}

// ✅ UPDATED: Enhanced login response với VIP data
if ($type == "login") {
    $username = field("username", null, true, true);
    $password = field("password", null, true, true);
    $hash = md5($password);
    
    $data = UserModel::Login($username, $hash);
    if (!$data) {
        return json_response(403, ["success" => false, "message" => "Thông tin đăng nhập không chính xác!"]);
    }
    if ($data["type"] != 3 && $data["status"] == 0) {
        return json_response(400, ["success" => false, "message" => "Tài khoản của bạn đã bị đình chỉ!"]);
    }
    
    $_SESSION["username"]    = $username;
    $_SESSION["id"]          = $data["id"];
    $_SESSION["user_id"]     = $data["id"];
    $_SESSION["name"]        = $data["name"] ?? $username;
    $_SESSION["user_type"]   = $data["type"];
    $_SESSION["vip_level"]   = $data["vip"] ?? 0;
    
    // ✅ ENHANCED: Response with full VIP data
    $response_data = [
        "success" => true,
        "user_id" => $data["id"],
        "id" => $data["id"],
        "username" => $username,
        "name" => $data["name"] ?? $username,
        "avatar" => $data["avatar"] ?? null,
        "money" => $data["money"] ?? 0,
        "credit" => $data["credit"] ?? 0,
        "vip" => $data["vip"] ?? 0,                    // ✅ VIP level
        "type" => $data["type"],
        "status" => $data["status"],
        "member_code" => $data["member_code"] ?? null,
        "total_topup" => $data["total_topup"] ?? 0,
        "reg_date" => $data["reg_date"] ?? time(),
        "data" => $data
    ];
    
    json_response(200, $response_data);

} else if ($type == "signup") {
    $username   = field("username", null, true, true);
    $password   = field("password", null, true, true);
    $invitecode = field("invitecode", null);
    
    if (get_config("allow_register") != 1) {
        return json_response(403, ["success" => false, "message" => "Tính năng đăng ký đã đóng!"]);
    }
    
    $db = Database::getInstance();
    
    if (get_config("reg_invitecode") == 1) {
        $d = $db->pdo_query_one("SELECT * FROM `invite_code` WHERE `invite_id` = '$invitecode'");
        if (!$d) {
            return json_response(403, ["success" => false, "message" => "Mã mời không đúng!"]);
        }
        if ($d["used_count"] >= $d["max_count"]) {
            return json_response(403, ["success" => false, "message" => "Mã mời không sử dụng được nữa!"]);
        }
    }
    
    $data = UserModel::GetOneFromUsername($username);
    if ($data) {
        return json_response(403, ["success" => false, "message" => "Tên người dùng đã tồn tại!"]);
    }
    
    UserModel::Register([
        "username" => $username,
        "password" => md5($password),
        "vip" => 0,  // ✅ Default VIP level
    ]);
    
    $data = UserModel::GetOneFromUsername($username);
    if ($data) {
        if (isset($d)) {
            $db->update("invite_code", ["used_count" => $d["used_count"] + 1], $d["id"]);
        }
        
        $_SESSION["username"]    = $username;
        $_SESSION["id"]          = $data["id"];
        $_SESSION["user_id"]     = $data["id"];
        $_SESSION["name"]        = $data["name"] ?? $username;
        $_SESSION["user_type"]   = $data["type"];
        $_SESSION["vip_level"]   = $data["vip"] ?? 0;
    }
    
    // ✅ ENHANCED: Signup response with VIP data
    $response_data = [
        "success" => true,
        "user_id" => $data["id"],
        "id" => $data["id"],
        "username" => $username,
        "name" => $data["name"] ?? $username,
        "avatar" => $data["avatar"] ?? null,
        "money" => $data["money"] ?? 0,
        "credit" => $data["credit"] ?? 0,
        "vip" => $data["vip"] ?? 0,
        "type" => $data["type"] ?? 3,
        "member_code" => $data["member_code"] ?? null,
        "data" => $data
    ];
    
    json_response(200, $response_data);
}

// ✅ UPDATED: Enhanced user info with VIP
else if ($type == "get_user_info" && is_login()) {
    if (!isset($_SESSION["username"])) {
        return json_response(403, ["success" => false, "message" => "Chưa đăng nhập"]);
    }
    $username = $_SESSION["username"];
    $data = UserModel::GetOneFromUsername($username);
    
    // ✅ Add VIP status calculation
    $vip_level = intval($data["vip"] ?? 0);
    $user_type = intval($data["type"] ?? 3);
    
    $vip_status_info = [
        "level" => $vip_level,
        "name" => "Regular User",
        "color" => "gray",
        "benefits" => ["Standard Features"]
    ];
    
    if ($user_type == 1) {
        $vip_status_info = [
            "level" => 999,
            "name" => "Admin",
            "color" => "red",
            "benefits" => ["All Features", "Admin Access", "Priority Support"]
        ];
    } else if ($user_type == 2) {
        $vip_status_info = [
            "level" => 998,
            "name" => "Staff",
            "color" => "purple", 
            "benefits" => ["Staff Access", "Priority Support"]
        ];
    } else if ($vip_level >= 100) {
        $vip_status_info = [
            "level" => 100,
            "name" => "VIP Diamond",
            "color" => "blue",
            "benefits" => ["Priority Chat", "Exclusive Content", "50% Bonus", "No Ads"]
        ];
    } else if ($vip_level >= 50) {
        $vip_status_info = [
            "level" => 50,
            "name" => "VIP Gold",
            "color" => "yellow",
            "benefits" => ["Priority Chat", "25% Bonus", "Reduced Ads"]
        ];
    } else if ($vip_level >= 25) {
        $vip_status_info = [
            "level" => 25,
            "name" => "VIP Silver",
            "color" => "gray",
            "benefits" => ["Priority Support", "10% Bonus"]
        ];
    } else if ($vip_level >= 10) {
        $vip_status_info = [
            "level" => 10,
            "name" => "VIP Bronze", 
            "color" => "orange",
            "benefits" => ["Basic VIP Benefits"]
        ];
    }
    
    $enhanced_data = array_merge($data, [
        "vip_status" => $vip_status_info
    ]);
    
    return json_response(200, ["success" => true, "data" => $enhanced_data]);
}

else if ($type == "get_new_characters") {
    $limit = field("limit", 15);
    $data  = CharacterModel::GetNewCharacters($limit);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_characters") {
    $offset = field("offset", 0);
    $limit  = field("limit", 15);
    $data   = CharacterModel::GetCharacters($offset, $limit);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_character_detail") {
    $id = field("id", null, true, true);
    $info = CharacterModel::GetOne($id);
    if (!$info) {
        return json_response(500, ["success" => false, "message" => "Nhân vật không tồn tại"]);
    }
    $imgs = CharacterModel::GetCharacterImages($id);
    return json_response(200, ["success" => true, "data" => ["info" => $info, "images" => $imgs]]);

} else if ($type == "get_new_characters_detail") {
    $id   = field("id", 9999, true);
    $data = CharacterModel::GetOneNewCharacter($id);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_main_characters") {
    $limit = field("limit", 50);
    $data  = CharacterModel::GetMainCharacters($limit);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_all_services") {
    $data = ServiceModel::GetAll();
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_all_topups") {
    $data = TopupModel::GetAllWithDetails();
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_one_topup") {
    $id   = field("id", null, true);
    $data = TopupModel::GetOneWithDetails($id);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_lottery_items") {
    $data = LotteryModel::GetAll();
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_lottery_odd" && is_login()) {
    $id   = field("id", null, true);
    $data = LotteryModel::GetOdd($id);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_lottery" && is_login()) {
    $id   = field("id", null, true);
    $data = LotteryModel::Get($id);

    // Lấy tổng số đơn vị từ bảng lottery_units
    $db = Database::getInstance();
    $session_id = $data['now_session'];
    $units = $db->pdo_query_one("SELECT option_a, option_b, option_c, option_d FROM lottery_units WHERE session_id = ?", $session_id);

    if ($units) {
        $data['totalUnits'] = [
            (int)$units['option_a'],
            (int)$units['option_b'],
            (int)$units['option_c'],
            (int)$units['option_d']
        ];
    } else {
        $data['totalUnits'] = [0, 0, 0, 0];
    }

    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "do_bet" && is_login()) {
    $uid     = $_SESSION["id"];
    $lid     = field("lid", null, true, true);
    $items   = explode(",", field("item", null, true, true));
    $session = field("session", null, true, true);
    $money   = field("money", 0, true, true);
    
    $linfo = LotteryModel::Get($lid);
    if (!$linfo) {
        return json_response(400, ["success" => false, "message" => "Lottery không tồn tại"]);
    }
    
$check_valid_session = LotterySessionModel::Get($lid, $session);
if (!$check_valid_session || $check_valid_session["status"] != 1) {
    return json_response(400, ["success" => false, "message" => "Phiên $session không tồn tại hoặc đã đóng."]);
}

    
    $odds = LotteryItemModel::GetFromLotteryID($lid);
    $tien_tru_truoc_khi_bet = $money * count($items);
    UserModel::UpdateMoney($uid, -1 * $tien_tru_truoc_khi_bet, "", "Trừ tiền");
    
    // Cập nhật tổng số đơn vị trong bảng lottery_units
    $db = Database::getInstance();
    
    // Kiểm tra xem kỳ này đã có trong bảng lottery_units chưa
    $existing = $db->pdo_query_one("SELECT * FROM lottery_units WHERE session_id = ?", $session);
    if (!$existing) {
        // Nếu chưa có, tạo mới
        $db->insert('lottery_units', [
            'session_id' => $session,
            'option_a' => 0,
            'option_b' => 0,
            'option_c' => 0,
            'option_d' => 0
        ]);
    }

    // Tăng số đơn vị cho các lựa chọn
    $updates = ['option_a' => 0, 'option_b' => 0, 'option_c' => 0, 'option_d' => 0];
    foreach ($items as $item) {
        $key = 'option_' . strtolower($item);
        $updates[$key] = 1; // Tăng thêm 1 cho lựa chọn này
    }

    $db->pdo_query(
        "UPDATE lottery_units SET option_a = option_a + ?, option_b = option_b + ?, option_c = option_c + ?, option_d = option_d + ? WHERE session_id = ?",
        $updates['option_a'],
        $updates['option_b'],
        $updates['option_c'],
        $updates['option_d'],
        $session
    );

    // Lưu lịch sử đặt cược
    foreach ($items as $key) {
        foreach ($odds as $o) {
            if ($o["type"] == $key) {
                $data = [];
                $data["lid"]       = $lid;
                $data["uid"]       = $uid;
                $data["money"]     = $money;
                $data["sid"]       = $session;
                $data["type"]      = $key;
                $data["proportion"]= $o["proportion"];
                $r = LotteryHistoryModel::Insert($data);
                break;
            }
        }
    }
    return json_response(200, ["success" => true, "data" => $r]);

} else if ($type == "my_lottery_history" && is_login()) {
    $uid   = $_SESSION["id"];
    $lid   = field("lid", null, true, true);
    $limit = field("limit", 10);
    $data  = LotteryHistoryModel::GetByUID($uid, $lid, $limit);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "my_lottery_history_1" && is_login()) {
    $uid   = $_SESSION["id"];
    $limit = field("limit", 10);
    $data  = LotteryHistoryModel::GetByUID1($uid, $limit);
    return json_response(200, ["success" => true, "data" => $data]);

// ======================= LỊCH SỬ PHIÊN / KẾT QUẢ =========================
} else if ($type == "get_lottery_history") {
    $lid   = (int) field("lid", null, true, true);
    $limit = (int) field("limit", 50);

    if ($lid <= 0) {
        return json_response(400, ["success"=>false, "message"=>"Missing lid"]);
    }

    $db = Database::getInstance();

    // sid có thể là chuỗi; ép về số để order đúng khi sid là số, nếu không thì fallback theo id
    $rows = $db->pdo_query("
        SELECT sid, result
          FROM lottery_session
         WHERE lid = ?
      ORDER BY 
        CASE WHEN sid REGEXP '^[0-9]+$' THEN CAST(sid AS UNSIGNED) ELSE id END DESC
         LIMIT " . (int)$limit,
        $lid
    );

    return json_response(200, ["success" => true, "data" => $rows]);

} else if ($type == "get_new_lottery_results") {
    $lid          = (int) field("lid", null, true, true);
    $lastSessionN = (int) field("last_session", 0, true, true);

    if ($lid <= 0) {
        return json_response(400, ["success"=>false, "message"=>"Missing lid"]);
    }

    $db = Database::getInstance();

    // Khi sid là số: so sánh theo CAST(sid)
    // Khi sid không phải số: so sánh theo id (đổi cách lấy last_session trên FE nếu cần)
    $rows = $db->pdo_query("
        SELECT sid, result
          FROM lottery_session
         WHERE lid = ?
           AND (
                (sid REGEXP '^[0-9]+$' AND CAST(sid AS UNSIGNED) > ?)
                OR (NOT (sid REGEXP '^[0-9]+$') AND id > ?)
               )
      ORDER BY 
        CASE WHEN sid REGEXP '^[0-9]+$' THEN CAST(sid AS UNSIGNED) ELSE id END DESC
         LIMIT 50",
        $lid, $lastSessionN, $lastSessionN
    );

    return json_response(200, ["success" => true, "data" => $rows]);



} else if ($type == "hot_edit_user" && is_login()) {
    $uid   = $_SESSION["id"];
    $key   = field("key", null, true, true);
    $value = field("value", null, true, true);
    
    if (in_array($key, ["email", "name", "gender", "phone"])) {
        $data = UserModel::Update2($uid, [$key => $value]);
        return json_response(200, ["success" => true, "data" => $data]);
    } else {
        return json_response(500, ["success" => false, "data" => "Key không hợp lệ"]);
    }

} else if ($type == "save_booking" && is_login()) {
    $data = [];
    $data['uid']         = $_SESSION["id"];
    $data['cid']         = field("character", null, true, true);
    $data['location']    = field("location", null, true, true);
    $data['status']      = 1;
    $data['create_time'] = time();
    $data['time']        = field("time", null, true, true);
    $data['service']     = field("service", null, true, true);
    $data['member_code'] = field("member_code", null, true, true);
    
    $info = UserModel::GetOne($data['uid']);
    if (!$info) {
        return json_response(500, ["success" => false, "message" => "User không tồn tại"]);
    }
    
    if (get_config("require_member_code_booking") && (empty($info["member_code"]) || $data['member_code'] != $info["member_code"])) {
        return json_response(200, ["success" => false, "message" => "Mã thành viên không chính xác"]);
    }
    
    BookingModel::Insert($data);
    $character_meta = CharacterModel::GetMetadata($data['cid']);
    $character_info = CharacterModel::GetOne($data['cid']);
    CharacterModel::Update($data["cid"], ["support_count" => ($character_info["support_count"] ?? 0) + 1]);
    return json_response(200, ["success" => true, "data" => ["metadata" => $character_meta, "info" => $character_info]]);

} else if ($type == "add_topup_history" && is_login()) {
    $data = [];
    $data['user_id']    = $_SESSION["id"];
    $data['amount']     = field("amount", 0, true, true);
    $data['topup_type'] = field("topup_type", 0, true, true);
    $data['status']     = 0;
    $data['date']       = time();
    $data['proof']      = field("proof", null, true, true);
    
    return json_response(200, ["success" => true, "data" => TopupModel::InsertHistory($data)]);

} else if ($type == "change_user_password" && is_login()) {
    $uid = $_SESSION["id"];
    $o   = field("old-pass", null, true, true);
    $n   = field("new-pass", null, true, true);
    $r   = field("re-pass", null, true, true);
    
    $oh = md5($o);
    $nh = md5($n);
    
    $userData = UserModel::GetOne($uid);
    if (!$userData) {
        return json_response(400, ["success" => false, "message" => "Xác thực thất bại"]);
    }
    if ($userData["password"] != $oh) {
        return json_response(400, ["success" => false, "message" => "Mật khẩu cũ không đúng"]);
    }
    if ($r != $n) {
        return json_response(400, ["success" => false, "message" => "Mật khẩu xác nhận không đúng"]);
    }
    return json_response(200, ["success" => true, "message" => UserModel::Update1(["password" => $nh], $uid)]);

} else if ($type == "bank_bind" && is_login()) {
    $data = [];
    $data['uid']         = $_SESSION["id"];
    $data["bankinfo"]    = field("bankinfo", null, true, true);
    $data["bankid"]      = field("bankid", null, true, true);
    $data["name"]        = field("name", null, true, true);
    $data["create_time"] = time();
    
    $p = BankModel::GetFromUID($data["uid"]);
    if ($p) {
        return json_response(200, ["success" => true, "message" => "error 012"]);
    }
    
    return json_response(200, ["success" => true, "message" => BankModel::Insert($data)]);

} else if ($type == "submit_member_code" && is_login()) {
    $id   = $_SESSION["id"];
    $code = field("code", null, true, true);
    
    $info = UserModel::GetOne($id);
    if (!$info) {
        return json_response(500, ["success" => false, "message" => "error 013"]);
    }
    if (empty($info["member_code"])) {
        return json_response(500, ["success" => false, "message" => "Tài khoản chưa được cấp thẻ mã thành viên."]);
    }
    if ($info["member_code"] != $code) {
        return json_response(500, ["success" => false, "message" => "Sai mã thẻ thành viên."]);
    }
    
    $_SESSION["member_code"] = $code;
    return json_response(200, ["success" => true, "data" => $code]);

} else if ($type === "refuse_withdraw") {
    // 1. Lấy ID lệnh rút và lý do (có thể bỏ trống)
    $id     = field("id", null, true, true);
    $reason = field("reason", "", false, true);

    // 2. Cập nhật lịch sử: status = 2 (thất bại), lưu luôn note
    WithdrawModel::UpdateHistory($id, [
        'status' => 2,
        'note'   => $reason
    ]);

    // 3. Lấy bản ghi để có số tiền & uid
    $w      = WithdrawModel::GetOneHistory($id);
    $amount = (int)$w['money'];

    // 4. Gửi notification kèm số điểm bị từ chối
    NotiModel::Send(
      $_SESSION['admin_id'],      // hoặc $adminId nếu bạn gán biến từ session
      $w['money'],
      'Từ chối rút tiền', 
      "Bạn đã bị từ chối rút số điểm {$amount}. Lý do: {$reason}",
      'red',
      $amount                    // ← tham số thứ 6 để ghi amount_refused
    );

    // 5. Trả về JSON success
    json_response(200, ['success' => true]);
    
} else if ($type === "accept_withdraw") {
    $id = (int) field("id", null, true, true);
    WithdrawModel::UpdateHistory($id, ['status' => 1]);
    json_response(200, ['success' => true]);

} else if ($type == "withdrawal" && is_login()) {
    $uid = $_SESSION["id"];
    $amount = field("amount", 0, true, true);
    $note = field("note", "Yêu cầu rút tiền");
    
    // Validate amount
    if ($amount <= 0) {
        return json_response(400, ["success" => false, "message" => "Số tiền không hợp lệ"]);
    }
    
    if ($amount < 100) {
        return json_response(400, ["success" => false, "message" => "Số tiền tối thiểu là 100"]);
    }
    
    if ($amount > 50000000) {
        return json_response(400, ["success" => false, "message" => "Số tiền tối đa là 99999999"]);
    }
    
    // Get user info
    $user_info = UserModel::GetOne($uid);
    if (!$user_info) {
        return json_response(400, ["success" => false, "message" => "Người dùng không tồn tại"]);
    }
    
    // Check balance
    if (floatval($user_info["money"]) < $amount) {
        return json_response(400, ["success" => false, "message" => "Số dư không đủ để rút tiền"]);
    }
    
    // Check if user has bank info
    $bank_info = BankModel::GetFromUID($uid);
    if (!$bank_info) {
        return json_response(400, ["success" => false, "message" => "Bạn chưa liên kết tài khoản ngân hàng"]);
    }
    
    // Create withdrawal record
    $withdrawal_data = [
        'uid' => $uid,
        'money' => $amount,
        'bankinfo' => $bank_info["bankinfo"],
        'bankid' => $bank_info["bankid"],
        'bankname' => $bank_info["name"],
        'create_time' => time(),
        'status' => 0, // Pending
        'note' => $note
    ];
    
    $db = Database::getInstance();
    
    // Insert withdrawal request
    $withdrawal_id = $db->insert('withdraw_history', $withdrawal_data);
    
    if ($withdrawal_id) {
        // Deduct money from user account
        UserModel::UpdateMoney($uid, -1 * $amount, "", "Yêu cầu rút tiền - ID: " . $withdrawal_id);
        
        // Log the transaction
        error_log("Withdrawal created: ID={$withdrawal_id}, UID={$uid}, Amount={$amount}");
        
        return json_response(200, [
            "success" => true, 
            "message" => "Yêu cầu rút tiền đã được gửi thành công! Vui lòng chờ admin xử lý.",
            "withdrawal_id" => $withdrawal_id,
            "amount" => $amount
        ]);
    } else {
        return json_response(500, ["success" => false, "message" => "Có lỗi xảy ra khi tạo yêu cầu rút tiền"]);
    }

// ✅ FIX: THÊM DẤNG NGOẶC NHỌN ĐÓNG BỊ THIẾU
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $action = $input['action_type'] ?? '';

    if ($action === 'purchase_diamonds') {
        $userId = intval($input['user_id'] ?? 0);
        $cost = floatval($input['cost'] ?? 0);
        $diamonds = intval($input['diamonds'] ?? 0);

        if ($userId <= 0 || $cost <= 0 || $diamonds <= 0) {
            json_response(400, ['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        }

        $user = UserModel::Get($userId);
        if (!$user) {
            json_response(404, ['success' => false, 'message' => 'Người dùng không tồn tại']);
        }

        if ($user['money'] < $cost) {
            json_response(400, ['success' => false, 'message' => 'Số dư không đủ']);
        }

        // Trừ tiền và cộng kim cương
        $newMoney = $user['money'] - $cost;
        $newDiamonds = $user['total_orders'] + $diamonds;

        UserModel::Update($userId, [
            'money' => $newMoney,
            'total_orders' => $newDiamonds
        ]);

        json_response(200, ['success' => true]);
    }

} else {
    // ✅ DEFAULT CASE: Unknown action
    json_response(400, ["success" => false, "message" => "unknown parameters"]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $action = $input['action_type'] ?? '';

    if ($action === 'purchase_diamonds') {
        $userId = intval($input['user_id'] ?? 0);
        $cost = floatval($input['cost'] ?? 0);
        $diamonds = intval($input['diamonds'] ?? 0);

        if ($userId <= 0 || $cost <= 0 || $diamonds <= 0) {
            json_response(400, ['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        }

        $user = UserModel::Get($userId);
        if (!$user) {
            json_response(404, ['success' => false, 'message' => 'Người dùng không tồn tại']);
        }

        if ($user['money'] < $cost) {
            json_response(400, ['success' => false, 'message' => 'Số dư không đủ']);
        }

        // Trừ tiền và cộng kim cương
        $newMoney = $user['money'] - $cost;
        $newDiamonds = $user['total_orders'] + $diamonds;

        UserModel::Update($userId, [
            'money' => $newMoney,
            'total_orders' => $newDiamonds
        ]);

        json_response(200, ['success' => true]);
    }
}

?>