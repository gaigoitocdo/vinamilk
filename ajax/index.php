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

// Utility functions
if (!function_exists('get_config')) {
    function get_config($key, $default = null) {
        global $CONFIG;
        return isset($CONFIG[$key]) ? $CONFIG[$key] : $default;
    }
}

if (!function_exists('field')) {
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

if (!function_exists('json_response')) {
    function json_response($code, $data) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}

if (!function_exists('is_login')) {
    function is_login() {
        return isset($_SESSION["username"]);
    }
}

// Lấy loại hành động từ yêu cầu
$type = field("action_type");

// ================================================================
// AUTHENTICATION SECTION
// ================================================================

if ($type == "login") {
    $username = field("username", null, true, true);
    $password = field("password", null, true, true);
    $hash = md5($password);
    
    $data = UserModel::Login($username, $hash);
    if (!$data) {
        return json_response(403, ["success" => false, "message" => "Thông tin đăng nhập không chính xác!"]);
    }
    if ($data["type"] > 3 && $data["status"] == 0) {
        return json_response(400, ["success" => false, "message" => "Tài khoản của bạn đã bị đình chỉ!"]);
    }
    
    $_SESSION["username"] = $username;
    $_SESSION["id"] = $data["id"];
    $_SESSION["user_type"] = $data["type"];
    
    // Enhanced response với VIP data
    $response_data = [
        "success" => true,
        "user_id" => $data["id"],
        "id" => $data["id"],
        "username" => $username,
        "name" => $data["name"] ?? $username,
        "avatar" => $data["avatar"] ?? null,
        "money" => $data["money"] ?? 0,
        "credit" => $data["credit"] ?? 0,
        "vip" => min(max($data["vip"] ?? 0, 0), 3), // VIP chỉ từ 0-3
        "type" => $data["type"],
        "status" => $data["status"],
        "member_code" => $data["member_code"] ?? null,
        "total_topup" => $data["total_topup"] ?? 0,
        "reg_date" => $data["reg_date"] ?? time(),
        "data" => $data
    ];
    
    json_response(200, $response_data);

} else if ($type == "signup") {
    $username = field("username", null, true, true);
    $password = field("password", null, true, true);
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
        "vip" => 0, // Default VIP level
        "type" => 3, // User thường
        "status" => 1
    ]);
    
    $data = UserModel::GetOneFromUsername($username);
    if ($data) {
        if (isset($d)) {
            $db->update("invite_code", ["used_count" => $d["used_count"] + 1], $d["id"]);
        }
        
        $_SESSION["username"] = $username;
        $_SESSION["id"] = $data["id"];
        $_SESSION["user_id"] = $data["id"];
        $_SESSION["name"] = $data["name"] ?? $username;
        $_SESSION["user_type"] = $data["type"];
        $_SESSION["vip_level"] = min($data["vip"] ?? 0, 3);
    }
    
    $response_data = [
        "success" => true,
        "user_id" => $data["id"],
        "id" => $data["id"],
        "username" => $username,
        "name" => $data["name"] ?? $username,
        "avatar" => $data["avatar"] ?? null,
        "money" => $data["money"] ?? 0,
        "credit" => $data["credit"] ?? 0,
        "vip" => 0, // Mặc định VIP 0
        "type" => 3,
        "status" => 1,
        "member_code" => $data["member_code"] ?? null,
        "data" => $data
    ];
    
    json_response(200, $response_data);

// ================================================================
// VIP SYSTEM - SIMPLIFIED (1,2,3 only)
// ================================================================

} else if ($type == "purchase_vip" && is_login()) {
    $uid = $_SESSION["id"];
    $vip_level = (int)field("vip_level", 0, true, true);
    
    // VIP prices - chỉ level 1,2,3
    $vip_prices = [
        1 => 50000,   // VIP Bronze
        2 => 150000,  // VIP Silver  
        3 => 500000   // VIP Gold
    ];
    
    if (!isset($vip_prices[$vip_level]) || $vip_level < 1 || $vip_level > 3) {
        return json_response(400, ["success" => false, "message" => "VIP level không hợp lệ (chỉ 1,2,3)"]);
    }
    
    $price = $vip_prices[$vip_level];
    
    $user_info = UserModel::GetOne($uid);
    if (!$user_info) {
        return json_response(400, ["success" => false, "message" => "User not found"]);
    }
    
    // Check if user already has this VIP level or higher
    if ((int)$user_info["vip"] >= $vip_level) {
        return json_response(400, ["success" => false, "message" => "Bạn đã có VIP level này hoặc cao hơn"]);
    }
    
    // Check balance
    if (floatval($user_info["money"]) < $price) {
        return json_response(400, ["success" => false, "message" => "Số dư không đủ. Cần: " . number_format($price) . " VNĐ"]);
    }
    
    // Deduct money and upgrade VIP
    UserModel::UpdateMoney($uid, -1 * $price, "", "Nâng cấp VIP Level " . $vip_level);
    UserModel::Update($uid, ["vip" => $vip_level]);
    
    return json_response(200, [
        "success" => true, 
        "message" => "Nâng cấp VIP thành công",
        "vip_level" => $vip_level,
        "amount_charged" => $price
    ]);

} else if ($type == "get_vip_info" && is_login()) {
    $uid = $_SESSION["id"];
    $user_info = UserModel::GetOne($uid);
    
    if (!$user_info) {
        return json_response(400, ["success" => false, "message" => "User not found"]);
    }
    
    $vip_level = min(max((int)$user_info["vip"], 0), 3); // Giới hạn 0-3
    $user_type = (int)$user_info["type"];
    
    // VIP status mapping
    $vip_configs = [
        0 => ["name" => "Thành viên thường", "color" => "gray", "benefits" => ["Tính năng cơ bản"]],
        1 => ["name" => "VIP Bronze", "color" => "#CD7F32", "benefits" => ["Ưu tiên hỗ trợ", "Giảm 5% phí giao dịch"]],
        2 => ["name" => "VIP Silver", "color" => "#C0C0C0", "benefits" => ["Ưu tiên cao", "Giảm 10% phí", "Bonus 5%"]],
        3 => ["name" => "VIP Gold", "color" => "#FFD700", "benefits" => ["Ưu tiên tối đa", "Miễn phí giao dịch", "Bonus 15%"]]
    ];
    
    $vip_status_info = $vip_configs[$vip_level];
    
    // Admin/Staff override
    if ($user_type == 1) {
        $vip_status_info = ["name" => "Admin", "color" => "red", "benefits" => ["Toàn quyền truy cập"]];
    } else if ($user_type == 2) {
        $vip_status_info = ["name" => "Staff", "color" => "purple", "benefits" => ["Quyền quản lý"]];
    }
    
    return json_response(200, [
        "success" => true,
        "vip_level" => $vip_level,
        "vip_status" => $vip_status_info["name"],
        "vip_benefits" => $vip_status_info["benefits"],
        "vip_color" => $vip_status_info["color"],
        "user_type" => $user_type,
        "balance" => floatval($user_info["money"]),
        "vip_prices" => [
            1 => 50000,
            2 => 150000,
            3 => 500000
        ]
    ]);

} else if ($type == "check_vip_access" && is_login()) {
    $uid = $_SESSION["id"];
    $required_level = min(max((int)field("required_level", 0, true, true), 0), 3);
    
    $user_info = UserModel::GetOne($uid);
    if (!$user_info) {
        return json_response(400, ["success" => false, "message" => "User not found"]);
    }
    
    $user_vip = min(max((int)$user_info["vip"], 0), 3);
    $user_type = (int)$user_info["type"];
    
    // Admin/Staff always have access
    $has_access = ($user_type <= 2) || ($user_vip >= $required_level);
    
    return json_response(200, [
        "success" => true,
        "has_access" => $has_access,
        "user_vip" => $user_vip,
        "required_level" => $required_level
    ]);

// ================================================================
// USER INFO & MANAGEMENT
// ================================================================

} else if ($type == "get_user_info" && is_login()) {
    if (!isset($_SESSION["username"])) {
        return json_response(403, ["success" => false, "message" => "Chưa đăng nhập"]);
    }
    
    $username = $_SESSION["username"];
    $data = UserModel::GetOneFromUsername($username);
    
    // Enhanced với VIP status
    $vip_level = min(max((int)($data["vip"] ?? 0), 0), 3);
    $user_type = (int)($data["type"] ?? 3);
    
    $vip_configs = [
        0 => ["name" => "Thành viên thường", "color" => "gray"],
        1 => ["name" => "VIP Bronze", "color" => "#CD7F32"],
        2 => ["name" => "VIP Silver", "color" => "#C0C0C0"],
        3 => ["name" => "VIP Gold", "color" => "#FFD700"]
    ];
    
    $vip_status_info = $vip_configs[$vip_level];
    $vip_status_info["level"] = $vip_level;
    
    if ($user_type == 1) {
        $vip_status_info = ["level" => 999, "name" => "Admin", "color" => "red"];
    } else if ($user_type == 2) {
        $vip_status_info = ["level" => 998, "name" => "Staff", "color" => "purple"];
    }
    
    $enhanced_data = array_merge($data, [
        "vip" => $vip_level,
        "vip_status" => $vip_status_info
    ]);
    
    return json_response(200, ["success" => true, "data" => $enhanced_data]);

// ================================================================
// LOTTERY SYSTEM - ENHANCED
// ================================================================

} else if ($type == "get_lottery_items") {
    $data = LotteryModel::GetAll();
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_lottery_odd" && is_login()) {
    $id = field("id", null, true);
    $data = LotteryModel::GetOdd($id);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_lottery" && is_login()) {
    $id = field("id", null, true);
    $brand = field("brand", "TH", false);
    
    $data = LotteryModel::Get($id);
    
    if (!$data) {
        return json_response(404, ["success" => false, "message" => "Lottery not found"]);
    }

    // ✅ ENHANCED: Get session codes for current session
    $current_session = $data['now_session'];
    $session_codes = LotterySessionModel::GetSessionCodes($id, $current_session);
    
    // Add session codes to response
    $data['session_codes'] = $session_codes;

    // Get lottery units data
    $db = Database::getInstance();
    $units = $db->pdo_query_one("SELECT option_a, option_b, option_c, option_d FROM lottery_units WHERE session_id = ?", $current_session);

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

    // ✅ ENHANCED: Get previous session result with 5-digit codes
    if (isset($data['prev_session']) && $data['prev_session']) {
        $prev_result = LotterySessionModel::GetWinningCode($id, $data['prev_session']);
        if ($prev_result) {
            $data['prev_result'] = $prev_result;
            $data['prev_winning_code'] = $prev_result['code'];
            $data['prev_winning_letter'] = $prev_result['letter'];
        }
    }

    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "do_bet" && is_login()) {
    $uid = $_SESSION["id"];
    $lid = field("lid", null, true, true);
    $items = explode(",", field("item", null, true, true));
    $session = field("session", null, true, true);
    $money = field("money", 0, true, true);
    $brand = field("brand", "TH", false);
    
    $linfo = LotteryModel::Get($lid);
    if (!$linfo) {
        return json_response(400, ["success" => false, "message" => "Lottery không tồn tại"]);
    }
    
    // Check session validity
    $check_valid_session = LotterySessionModel::Get($lid, $session);
    if (!$check_valid_session || $check_valid_session["status"] != 1) {
        return json_response(400, ["success" => false, "message" => "Phiên $session không tồn tại hoặc đã đóng."]);
    }

    // ✅ ENHANCED: Get session codes for validation and conversion
    $session_codes = LotterySessionModel::GetSessionCodes($lid, $session);
    
    $odds = LotteryItemModel::GetFromLotteryID($lid);
    $total_cost = $money * count($items);
    
    // Deduct money first
    UserModel::UpdateMoney($uid, -1 * $total_cost, "", "Đặt cược");
    
    // Update lottery units
    $db = Database::getInstance();
    
    $existing = $db->pdo_query_one("SELECT * FROM lottery_units WHERE session_id = ?", $session);
    if (!$existing) {
        $db->insert('lottery_units', [
            'session_id' => $session,
            'option_a' => 0,
            'option_b' => 0,
            'option_c' => 0,
            'option_d' => 0
        ]);
    }

    // Update units based on user selections
    $updates = ['option_a' => 0, 'option_b' => 0, 'option_c' => 0, 'option_d' => 0];
    foreach ($items as $item) {
        $key = 'option_' . strtolower($item);
        if (isset($updates[$key])) {
            $updates[$key] = 1;
        }
    }

    $db->pdo_query(
        "UPDATE lottery_units SET option_a = option_a + ?, option_b = option_b + ?, option_c = option_c + ?, option_d = option_d + ? WHERE session_id = ?",
        $updates['option_a'],
        $updates['option_b'],
        $updates['option_c'],
        $updates['option_d'],
        $session
    );

    // ✅ ENHANCED: Save betting history with enhanced data
    foreach ($items as $key) {
        // Convert letter to 5-digit code for storage
        $user_bet_code = null;
        if (preg_match('/^[A-D]$/i', $key)) {
            $letter = strtolower($key);
            if (isset($session_codes[$letter])) {
                $user_bet_code = $session_codes[$letter];
            }
        } else if (preg_match('/^\d{5}$/', $key)) {
            $user_bet_code = $key;
        }
        
        foreach ($odds as $o) {
            if ($o["type"] == $key) {
                $data = [];
                $data["lid"] = $lid;
                $data["uid"] = $uid;
                $data["money"] = $money;
                $data["sid"] = $session;
                $data["type"] = $key;
                $data["user_bet_code"] = $user_bet_code;
                $data["proportion"] = $o["proportion"];
                $data["brand"] = $brand;
                $r = LotteryHistoryModel::Insert($data);
                break;
            }
        }
    }
    
    return json_response(200, ["success" => true, "data" => $r]);

} else if ($type == "my_lottery_history" && is_login()) {
    $uid = $_SESSION["id"];
    $lid = field("lid", null, true, true);
    $limit = field("limit", 10);
    $data = LotteryHistoryModel::GetByUID($uid, $lid, $limit);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "my_lottery_history_1" && is_login()) {
    $uid = $_SESSION["id"];
    $limit = field("limit", 10);
    $data = LotteryHistoryModel::GetByUID1($uid, $limit);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_lottery_history") {
    $lid = (int) field("lid", null, true, true);
    $limit = (int) field("limit", 50);

    if ($lid <= 0) {
        return json_response(400, ["success" => false, "message" => "Missing lid"]);
    }

    $db = Database::getInstance();

    // Get sessions with results
    $rows = $db->pdo_query("
        SELECT 
            ls.sid, 
            ls.result,
            ls.session_codes,
            ls.create_time,
            le.result as admin_result,
            le.editor
        FROM lottery_session ls
        LEFT JOIN lottery_edit le ON le.session = ls.sid
        WHERE ls.lid = ?
          AND ls.result != ''
        ORDER BY 
            CASE WHEN ls.sid REGEXP '^[0-9]+$' THEN CAST(ls.sid AS UNSIGNED) ELSE ls.id END DESC
        LIMIT " . (int)$limit,
        $lid
    );

    // ✅ ENHANCED: Process results to include winning details
    $processed_results = [];
    foreach ($rows as $row) {
        $result_data = [
            'sid' => $row['sid'],
            'result' => $row['result'],
            'create_time' => $row['create_time']
        ];
        
        // Add winning code details
        if ($row['result']) {
            $codes = explode(',', $row['result']);
            $result_data['winning_codes'] = $codes;
            $result_data['primary_winner'] = $codes[0];
            
            // Add session codes for context
            if ($row['session_codes']) {
                $session_codes = json_decode($row['session_codes'], true);
                $result_data['session_codes'] = $session_codes;
                
                // Find winning letter
                foreach ($session_codes as $letter => $code) {
                    if ($code === $codes[0]) {
                        $result_data['winning_letter'] = $letter;
                        break;
                    }
                }
            }
        }
        
        // Add admin edit info
        if ($row['admin_result']) {
            $result_data['admin_edited'] = true;
            $result_data['admin_result'] = $row['admin_result'];
            $result_data['editor'] = $row['editor'];
        }
        
        $processed_results[] = $result_data;
    }

    return json_response(200, ["success" => true, "data" => $processed_results]);

} else if ($type == "get_result") {
    // ✅ ENHANCED: Public API to get session result
    $session = field("session", null, true);
    $brand = field("brand", "TH", false);
    $lid = field("lid", null, true);
    
    if (!$lid) {
        return json_response(400, ["success" => false, "message" => "Missing lottery ID"]);
    }
    
    $result = LotterySessionModel::GetWinningCode($lid, $session);
    
    if ($result) {
        return json_response(200, ["success" => true, "data" => $result]);
    } else {
        return json_response(404, ["success" => false, "message" => "No result found"]);
    }

} else if ($type == "get_session_codes") {
    // ✅ NEW: API to get session codes for frontend
    $lid = field("lid", null, true);
    $session = field("session", null, true);
    
    if (!is_login()) {
        return json_response(403, ["success" => false, "message" => "Authentication required"]);
    }
    
    try {
        $codes = LotterySessionModel::GetSessionCodes($lid, $session);
        return json_response(200, ["success" => true, "data" => $codes]);
    } catch (Exception $e) {
        return json_response(500, ["success" => false, "message" => $e->getMessage()]);
    }

// ================================================================
// CHARACTER & SERVICE APIS
// ================================================================

} else if ($type == "get_new_characters") {
    $limit = field("limit", 15);
    $data = CharacterModel::GetNewCharacters($limit);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_characters") {
    $offset = field("offset", 0);
    $limit = field("limit", 15);
    $data = CharacterModel::GetCharacters($offset, $limit);
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_character_detail") {
    $id = field("id", null, true, true);
    $info = CharacterModel::GetOne($id);
    if (!$info) {
        return json_response(500, ["success" => false, "message" => "Nhân vật không tồn tại"]);
    }
    $imgs = CharacterModel::GetCharacterImages($id);
    return json_response(200, ["success" => true, "data" => ["info" => $info, "images" => $imgs]]);

} else if ($type == "get_all_services") {
    $data = ServiceModel::GetAll();
    return json_response(200, ["success" => true, "data" => $data]);

} else if ($type == "get_all_topups") {
    $data = TopupModel::GetAllWithDetails();
    return json_response(200, ["success" => true, "data" => $data]);

// ================================================================
// USER ACTIONS
// ================================================================

} else if ($type == "hot_edit_user" && is_login()) {
    $uid = $_SESSION["id"];
    $key = field("key", null, true, true);
    $value = field("value", null, true, true);
    
    if (in_array($key, ["email", "name", "gender", "phone"])) {
        $data = UserModel::Update2($uid, [$key => $value]);
        return json_response(200, ["success" => true, "data" => $data]);
    } else {
        return json_response(500, ["success" => false, "data" => "Key không hợp lệ"]);
    }

} else if ($type == "change_user_password" && is_login()) {
    $uid = $_SESSION["id"];
    $o = field("old-pass", null, true, true);
    $n = field("new-pass", null, true, true);
    $r = field("re-pass", null, true, true);
    
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

// ================================================================
// BOOKING & BANKING
// ================================================================

} else if ($type == "save_booking" && is_login()) {
    $data = [];
    $data['uid'] = $_SESSION["id"];
    $data['cid'] = field("character", null, true, true);
    $data['location'] = field("location", null, true, true);
    $data['status'] = 1;
    $data['create_time'] = time();
    $data['time'] = field("time", null, true, true);
    $data['service'] = field("service", null, true, true);
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

} else if ($type == "bank_bind" && is_login()) {
    $data = [];
    $data['uid'] = $_SESSION["id"];
    $data["bankinfo"] = field("bankinfo", null, true, true);
    $data["bankid"] = field("bankid", null, true, true);
    $data["name"] = field("name", null, true, true);
    $data["create_time"] = time();
    
    $p = BankModel::GetFromUID($data["uid"]);
    if ($p) {
        return json_response(200, ["success" => true, "message" => "error 012"]);
    }
    
    return json_response(200, ["success" => true, "message" => BankModel::Insert($data)]);

// ================================================================
// FINANCIAL TRANSACTIONS
// ================================================================

} else if ($type == "add_topup_history" && is_login()) {
    $data = [];
    $data['user_id'] = $_SESSION["id"];
    $data['amount'] = field("amount", 0, true, true);
    $data['topup_type'] = field("topup_type", 0, true, true);
    $data['status'] = 0;
    $data['date'] = time();
    $data['proof'] = field("proof", null, true, true);
    
    return json_response(200, ["success" => true, "data" => TopupModel::InsertHistory($data)]);

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
        return json_response(400, ["success" => false, "message" => "Số tiền tối đa là 50,000,000"]);
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
        
        return json_response(200, [
            "success" => true, 
            "message" => "Yêu cầu rút tiền đã được gửi thành công! Vui lòng chờ admin xử lý.",
            "withdrawal_id" => $withdrawal_id,
            "amount" => $amount
        ]);
    } else {
        return json_response(500, ["success" => false, "message" => "Có lỗi xảy ra khi tạo yêu cầu rút tiền"]);
    }

} else if ($type == "submit_member_code" && is_login()) {
    $id = $_SESSION["id"];
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

// ================================================================
// UNKNOWN ACTION
// ================================================================

} else {
    json_response(400, ["success" => false, "message" => "unknown parameters: $type"]);
}
?>