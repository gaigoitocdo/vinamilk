<?php

header("content-type: application/json");

include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/CharacterModel.php";
include_once __DIR__ . "/../models/ServiceModel.php";
include_once __DIR__ . "/../models/TopupModel.php";
include_once __DIR__ . "/../models/VideoModel.php";
include_once __DIR__ . "/../models/ImageModel.php";

include_once __DIR__ . "/../models/UserModel.php";
include_once __DIR__ . "/../models/LotteryModel.php";
include_once __DIR__ . "/../models/LotteryItemModel.php";
include_once __DIR__ . "/../models/NotiModel.php";
include_once __DIR__ . "/../models/BankModel.php";
include_once __DIR__ . "/../models/WithdrawModel.php";
include_once __DIR__ . "/../models/LotteryEditModel.php";


if (!is_login()) {
    json_response(403, ["success" => false, "message" => "invalid access"]);
}

$type = field("action_type");

if ($type == "update_topup_meta") {
    $topup_id = field("topup_id", 15, true);
    $meta_id = field("meta_id", 15, true);
    $key = field("key", 15, true);
    $value = field("value", 15, true);
    $data = TopupModel::UpdateMeta($topup_id, $meta_id, $key, $value);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "update_topup_info") {
    $topup_id = field("topup_id", 15, true, true);
    $name = field("name", 15, true);
    $display_name = field("display_name", 15, true);
    $data = TopupModel::UpdateInfo($topup_id, $name, $display_name);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "create-invitecode") {
    $invite = field("invite", NULL);
    $max_use = field("max_use", 1);

    if (empty($max_use)) $max_use = 1;
    if (empty($invite)) $invite = random_string(6);

    $data = Database::getInstance()->insert("invite_code", [
        "invite_id" => $invite,
        "max_count" => $max_use
    ]);

    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_topup") {
    $topup_id = field("topup_id", 15, true);
    $data = TopupModel::Delete($topup_id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "refuse_topup") {
    $id = field("id", 15, true);
    $reason = field("reason", NULL);

    $info = TopupModel::GetOneHistory($id);

    if ($info) {
        NotiModel::Send(TOPUP_NOTI, $info["user_id"], "Từ chối nạp tiền", "Bạn đã bị từ chối nạp tiền. Lý do: $reason", "red");

        $data = TopupModel::UpdateHistory($id, ["refuse_reason" => $reason, "status" => 2]);
        return json_response(200, ["success" => true, "data" => $data]);
    }
} else if ($type == "accept_topup") {
    $id = field("id", 15, true);

    $info = TopupModel::GetOneHistory($id);

    if (!$info) return json_response(500, ["success" => false, "message" => "History $id not found"]);

    $data = TopupModel::UpdateHistory($id, ["status" => 1]);

    UserModel::UpdateMoney($info["user_id"], $info["amount"], "cộng tiền thông qua thanh toán ngân hàng");
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_topup_meta") {
    $topup_id = field("topup_id", 15, true);
    $meta_id = field("meta_id", 15, true);
    $data = TopupModel::DeleteMeta($topup_id, $meta_id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "create_topup") {
    $name = field("name", 15, true, true);
    $display_name = field("display_name", 15, true);
    $data = TopupModel::Create($name, $display_name);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "create_topup_meta") {
    $topup_id = field("topup_id", 15, true, true);
    $key = field("key", 15, true, true);
    $value = field("value", 15, true, true);
    $data = TopupModel::CreateMeta($topup_id, $key, $value);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "create_video_category") {
    $name = field("name", 15, true, true);
    $order = field("order", 1);

    $data = VideoModel::CreateCategory($name, $order);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_video_category") {
    $id = field("id", 15, true, true);

    $data = VideoModel::DeleteCategory($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_video_category") {
    $id = field("id", 15, true, true);

    $data = VideoModel::GetOneCategory($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "update_video_category") {
    $id = field("id", 15, true, true);
    $name = field("name", 15, true, true);
    $order = field("order", 15, true, true);
    $status = field("status", 15, true, true);

    $data = VideoModel::UpdateCategory($id, $name, $order, $status);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "create_video") {
    $name = field("name", 15, true, true);
    $img = field("imgurl", 15, true, true);
    $vid = field("vidurl", 15, true, true);

    $time = field("time", 15, true, true);
    $views = field("views", 15, true, true);
    $cate = field("cate", 15, true, true);
    $video_url_type = field("video_url_type", 0);

    $data = VideoModel::CreateVideo($name, $img, $vid, $time, $views, $cate, $video_url_type);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_video") {
    $id = field("id", 15, true, true);

    $data = VideoModel::DeleteVideo($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_video") {
    $id = field("id", 15, true, true);

    $data = VideoModel::GetOneVideo($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "update_video") {
    $id = field("id", 15, true, true);
    $name = field("name", 15, true, true);
    $img = field("imgurl", 15, true, true);
    $vid = field("vidurl", 15, true, true);

    $time = field("time", 15, true, true);

    $status = field("status", 15, true, true);
    $hot = field("hot", 15, true, true);

    $views = field("views", 15, true, true);
    $cate = field("cate", 15, true, true);
    $video_url_type = field("video_url_type", 15, true, true);
    $data = VideoModel::UpdateVideo($id, $name, $img, $vid, $status, $hot, $time, $views, $cate, $video_url_type);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "add_user") {

    $data = [];
    $data["username"] = field("username", "", true, true);
    $data["password"] = field("password", "", true, true);
    $data["money"] = field("money", 0);
    $data["credit"] = field("credit", 100);
    $data["vip"] = field("vip", 1);
    $data["name"] = field("name", "", true, true);
    $data["num"] = field("num", 10);
    $data["min"] = field("min", 10);
    $data["max"] = field("max", 100);

    $data = UserModel::Create($data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_user") {

    $id = field("id", 0, true, true);

    $data = UserModel::GetOne($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_user") {

    $id = field("id", 0, true, true);

    $data = UserModel::Delete($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "update_user_money") {

    $id = field("id", 0, true, true);
    $num = field("num", 0);
    $type = field("charge_type", "Topup");
    $desc = field("desc");

    $data = UserModel::UpdateMoney($id, $num, $type, $desc);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "update_user_status") {

    $id = field("id", 0, true, true);
    $status = field("status", 0);

    $data = UserModel::UpdateStatus($id, $status);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "edit_user") {

    $data = [];
    $id = field("id", 0, true, true);
    $data["password"] = field("password", NULL);
    $data["money"] = field("money", NULL);
    $data["credit"] = field("credit", NULL);
    $data["member_code"] = field("member_code", NULL);

    $data["vip"] = field("vip", NULL);
    $data["name"] = field("name", NULL);
    $data["num"] = field("num", NULL);
    $data["min"] = field("min", NULL);
    $data["max"] = field("max", NULL);
    $data["total_topup"] = field("total_topup", NULL);

    $data["type"] = field("type", NULL);
    $data = UserModel::Update($id, $data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "create_image_category") {
    $name = field("name", 15, true, true);
    $order = field("order", 1);

    $data = ImageModel::CreateCategory($name, $order);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_image_category") {
    $id = field("id", 15, true, true);

    $data = ImageModel::DeleteCategory($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_image_category") {
    $id = field("id", 15, true, true);

    $data = ImageModel::GetOneCategory($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "update_image_category") {
    $id = field("id", 15, true, true);
    $name = field("name", 15, true, true);
    $order = field("order", 15, true, true);
    $status = field("status", 15, true, true);

    $data = ImageModel::UpdateCategory($id, $name, $order, $status);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "create_image") {
    $name = field("name", "Picture");
    $img = field("url", 15, true, true);
    $type = field("type", 1);
    $cate = field("cate_id", 15, true, true);

    $data = ImageModel::CreateImage($name, $img, $type, $cate);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_character_images") {
    $id = field("id", 50, true);

    $data = CharacterModel::GetCharacterImages($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_character_image") {
    $id = field("id", 50, true);

    $data = CharacterModel::DeleteCharacterImage($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_character_metadata") {
    $id = field("id", 50, true);

    $data = CharacterModel::DeleteCharacterMetadata($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "add_character_metadata") {
    $id = field("id", 50, true);
    $data = [];
    $data["meta_key"] = field("key", 0, true, true);
    $data["meta_value"] = field("value", 0, true, true);
    $data["meta_id"] = $id;

    $data = CharacterModel::AddMetadata($data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "update_image_character_status") {
    $id = field("id", 50, true);
    $type = field("type", 50, true);

    $data = CharacterModel::UpdateCharacterImage($id, ["type" => $type]);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_image") {
    $id = field("id", 15, true, true);

    $data = ImageModel::DeleteImage($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "add_character_image") {
    $id = field("id", 15, true, true);
    $data = [];
    $data["type"] = field("type", 1);
    $data["asset_type"] = field("asset_type", 1);

    $data["asset_url"] = field("url", 0, true, true);
    $data["character_id"] = $id;


    $data = CharacterModel::AddCharacterImage($data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_image") {
    $id = field("id", 15, true, true);

    $data = ImageModel::GetOneImage($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "update_image") {
    $id = field("id", 15, true, true);
    $name = field("name", NULL);
    $img = field("url", NULL);
    $type = field("type", NULL);
    $cate = field("cate_id", NULL);

    $data = [];
    $data["name"] = $name;
    $data["url"] = $img;
    $data["type"] = $type;
    $data["cate_id"] = $cate;
    $data = ImageModel::UpdateImage($id, $data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "update_character_status") {

    $id = field("id", 0, true, true);
    $status = field("status", 0);

    $data = CharacterModel::UpdateStatus($id, $status);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_character") {

    $id = field("id", 0, true, true);

    $data = CharacterModel::GetOne($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_character") {

    $id = field("id", 0, true, true);

    $data = CharacterModel::DeleteCharacter($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_character_metadata") {

    $id = field("id", 0, true, true);

    $data = CharacterModel::GetMetadata($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "add_character") {
    $data = [];
    $data["name"] = field("name", NULL, true, true);
    $data["desc"] = field("desc", NULL);
    $data["image"] = field("image", NULL, true, true);
    $data["measurements"] = field("measurements", NULL);
    $data["age"] = field("age", NULL);
    $data["type"] = field("type", 0);
    $data["location"] = field("location", "Earth");
    $data["support_count"] = field("support_count", 0);
    $data["joined_date"] = time();
    $data["rating"] = 5;
    $data = CharacterModel::CreateCharacter($data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "edit_character") {
    $data = [];
    $id = field("id", NULL, true, true);
    $data["name"] = field("name", NULL);
    $data["desc"] = field("desc", NULL);
    $data["image"] = field("image", NULL);
    $data["measurements"] = field("measurements", NULL);
    $data["age"] = field("age", NULL);
    $data["type"] = field("type", NULL);
    $data["location"] = field("location", NULL);
    $data["status"] = field("status", NULL);
    $data["support_count"] = field("support_count", NULL);

    $data = CharacterModel::UpdateCharacter($id, $data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "add_lottery_category") {
    $data = [];
    $data["name"] = field("name", NULL, true, true);
    $data["order"] = field("order", 0);

    LotteryModel::CreateCategory($data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_lottery_category") {
    $id = field("id", NULL, true, true);

    $data = LotteryModel::GetCategory($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "edit_lottery_category") {
    $id = field("id", NULL, true, true);
    $data = [];
    $data["name"] = field("name", NULL, true, true);
    $data["status"] = field("status", NULL);
    $data["order"] = field("order", NULL);

    $data = LotteryModel::UpdateCategory($id, $data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_lottery_category") {
    $id = field("id", NULL, true, true);

    $data = LotteryModel::DeleteCategory($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "add_lottery") {
    $data = [];
    $data["name"] = field("name", NULL, true, true);
    $data["cate_id"] = field("cate_id", NULL, true, true);
    $data["desc"] = field("desc", NULL);
    $data["image"] = field("image", NULL);
    $data["key"] = field("key", NULL, true, true);
    $data["rule"] = field("rule", 1);
    $data["condition"] = field("condition", 0);
    $data["hot"] = field("hot", 0);

    $data = LotteryModel::Create($data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_lottery") {
    $id = field("id", NULL, true, true);

    $data = LotteryModel::Get($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "edit_lottery") {
    $id = field("id", NULL, true, true);

    $data = [];
    $data["name"] = field("name", NULL);
    $data["cate_id"] = field("cate_id", NULL);
    $data["desc"] = field("desc", NULL);
    $data["image"] = field("image", NULL);
    $data["key"] = field("key", NULL);
    $data["rule"] = field("rule", NULL);
    $data["condition"] = field("condition", NULL);
    $data["hot"] = field("hot", NULL);
    $data["status"] = field("status", NULL);

    $data = LotteryModel::Update($id, $data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_lottery") {
    $id = field("id", NULL, true, true);

    $data = LotteryModel::Delete($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_lottery_item") {
    $id = field("lid", NULL, true, true);

    $data = LotteryItemModel::GetFromLotteryID($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "edit_lottery_item") {
    $id = field("id", NULL, true, true);
    $name = field("name", NULL);
    $proportion = field("proportion", NULL);

    $data = [];
    $data["name"] = $name;
    $data["proportion"] = $proportion;

    $data = LotteryItemModel::Update($id, $data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_service") {
    $id = field("id", NULL, true, true);

    $data = ServiceModel::Get($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "create_service") {
    $data = [];
    $data["name"] = field("name", 0, true, true);
    $data["order"] = field("order", 0);
    $data["desc"] = field("desc", "");
    $data["end"] = field("end", "");
    $data["time"] = field("time", "");
    $data["price"] = field("price", "");
    $data["images"] = field("images", "");
    $data["redirect_url"] = field("redirect_url", "");


    $data = Database::getInstance()->insert('services', $data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "edit_service") {
    $data = [];
    $id = field("id", 0, true, true);


    $data["name"] = field("name", NULL);
    $data["order"] = field("order", NULL);
    $data["desc"] = field("desc", NULL);
    $data["end"] = field("end", NULL);
    $data["time"] = field("time", NULL);
    $data["price"] = field("price", NULL);
    $data["images"] = field("images", NULL);
    $data["redirect_url"] = field("redirect_url", NULL);


    $data = ServiceModel::Update($id, $data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "delete_service") {
    $data = [];
    $id = field("id", 0, true, true);
    $data = ServiceModel::Delete($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_bank_bind") {
    $id = field("id", 0, true, true);
    $data = BankModel::Get($id);
    return json_response(200, ["success" => !empty($data), "data" => $data]);
} else if ($type == "delete_bank_bind") {
    $id = field("id", 0, true, true);
    $data = BankModel::Delete($id);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "edit_bank_bind") {
    $id = field("id", 0, true, true);
    $data = [];
    $data["bankid"] = field("bankid", NULL);
    $data["bankinfo"] = field("bankinfo", NULL);
    $data["name"] = field("name", NULL);

    $data = BankModel::Update($id, $data);
    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "refuse_withdraw") {
    $id = field("id", 15, true);
    $reason = field("reason", NULL);

    $info = WithdrawModel::GetOneHistory($id);

    if ($info) {
        // ✅ Cộng lại tiền nếu từ chối
        UserModel::UpdateMoney($info["uid"], (string)$info["money"], "출금 거부", "귀하의 포인트가 귀하의 계정으로 환불되었습니다..");

        NotiModel::Send(
            TOPUP_NOTI,
            $info["uid"],
            "출금 거부",
            "출금이 거부되었습니다. {$info["money"]}. 이유: $reason",
            "red",
            (int)$info["money"]
        );

        $data = WithdrawModel::UpdateHistory($id, ["desc" => $reason, "status" => 2]);
        return json_response(200, ["success" => true, "data" => $data]);
    }

    return json_response(200, ["success" => false, "message" => "History invalid"]);


    
} else if ($type == "accept_withdraw") {
    $id = field("id", 15, true);

    $info = WithdrawModel::GetOneHistory($id);

    if (!$info) return json_response(500, ["success" => false, "message" => "History $id not found"]);

    $data = WithdrawModel::UpdateHistory($id, ["status" => 1]);

    UserModel::UpdateMoney($info["uid"], $info["money"] * -1, "출금 성공 ");

    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "update_config") {
    $data = field("data", 15, true);

    $db = Database::getInstance();

    foreach ($data as $key => $val) {
        $db->update("config", ["value" => $val], [["name", $key]]);
    }

    return json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_lottery_edit") {
    $key = field("key", 15, true);

    LotteryEditModel::GetAll($key);
} else if ($type == "update_lottery_edit") {
    $key = field("key", 15, true);
    $session = field("session", 15, true);
    $result = field("result", 15, true);

    // ✅ VALIDATE: Đảm bảo result là mã 5 số hoặc nhiều mã phân cách bởi dấu phẩy
    if (!preg_match('/^\d{5}(,\d{5})*$/', $result)) {
        return json_response(400, [
            "success" => false, 
            "message" => "Kết quả phải là mã 5 số (VD: 12345 hoặc 12345,67890)"
        ]);
    }

    $cur = LotteryEditModel::Get($key, $session);

    $db = Database::getInstance();

    if ($cur) {
        $data = $db->update('lottery_edit', ["result" => $result, 'update_time' => time(), 'editor' => $_SESSION["id"]], $cur["id"]);
    } else {
        $data = $db->insert('lottery_edit', [
            "key" => $key,
            "session" => $session,
            "result" => $result,
            'editor' => $_SESSION["id"],
            'create_time' => time(),
            'update_time' => time()
        ]);
    }
    json_response(200, ["success" => true, "data" => $data]);
} else if ($type == "get_result") {
    // ✅ NEW API: Admin panel lấy kết quả cho session cụ thể
    $session = field("session", null, true);
    $brand = field("brand", "TH", false);
    
    $db = Database::getInstance();
    
    // Tìm lottery theo brand
    $lottery = null;
    if ($brand === "TH") {
        $lottery = $db->pdo_query_one("SELECT * FROM lottery WHERE name LIKE '%TH%' OR `key` LIKE '%th%' LIMIT 1");
    } else {
        $lottery = $db->pdo_query_one("SELECT * FROM lottery WHERE name LIKE '%VINAMILK%' OR `key` LIKE '%vinamilk%' LIMIT 1");
    }
    
    if (!$lottery) {
        return json_response(404, ["success" => false, "message" => "Brand not found"]);
    }
    
    // Tìm kết quả trong lottery_session
    $sessionResult = $db->pdo_query_one(
        "SELECT result, session_codes FROM lottery_session WHERE lid = ? AND sid = ? AND result != ''",
        $lottery['id'], $session
    );
    
    if ($sessionResult && !empty($sessionResult['result'])) {
        $codes = explode(',', $sessionResult['result']);
        $sessionCodes = json_decode($sessionResult['session_codes'] ?? '{}', true);
        
        // Map ngược từ codes về letters
        $letter = 'a'; // default
        if ($sessionCodes) {
            foreach ($sessionCodes as $l => $code) {
                if (in_array($code, $codes)) {
                    $letter = $l;
                    break;
                }
            }
        }
        
        return json_response(200, [
            "success" => true,
            "data" => [
                "code" => $codes[0], // Code đầu tiên
                "letter" => $letter,
                "all_codes" => $codes,
                "session_codes" => $sessionCodes
            ]
        ]);
    }
    
    return json_response(404, ["success" => false, "message" => "No result found"]);
} else if ($type == "delete_lottery_edit") {
    $key = field("key", 15, true);
    $session = field("session", 15, true);

    $cur = LotteryEditModel::Get($key, $session);

    $db = Database::getInstance();

    if (!$cur) {
        return json_response(400, ["success" => false, "message" => "Bạn chưa lưu kết quả này!"]);
    } else {
        $data = $db->pdo_execute("DELETE FROM `lottery_edit` WHERE `key` = '$key' AND `session` = '$session'");
        return json_response(200, ["success" => true, "data" => $data]);
    }
} else if ($type == "edit_vip_level") {
    
    $data = [];

    $id = field("id", -1);

    $data["level"] = field("level", "", true, true);
    $data["desc"] = field("desc", "");
    $data["min"] = field("min", 0, true, true);
    $data["logo"] = field("logo", "");


    $db = Database::getInstance();


    $cur = $db->pdo_query_one("SELECT * FROM `vip_level` WHERE `level` = '".$data["level"]."'");

    if($cur || $id != -1)
    {
        $r = $db->update('vip_level', $data,  ($id != -1) ? $id : $cur["id"]);
    }
    else
    {
        $r = $db->insert('vip_level', $data);
    }

    return json_response(200, ["success" => true, "data" => $r]);

} else if ($type == "delete_vip_level") {
    
    $id = field("id", 0, true, true);

    $db = Database::getInstance();

    $cur = $db->pdo_execute("DELETE FROM `vip_level` WHERE `id` = '$id'");

    return json_response(200, ["success" => true, "data" => $cur]);

} else {
    json_response(400, ["success" => false, "message" => "unknown parameters $type"]);
}
