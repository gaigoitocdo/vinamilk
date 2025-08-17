<?php

include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/LotteryItemModel.php";
include_once __DIR__ . "/../models/LotteryModel.php";

class LotterySessionModel
{
    private static $table = 'lottery_session';
    // status: 1: đã mở, 0: chưa mở
    public static function GetByLID($lid)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `lottery_session` WHERE `lid` = $lid");
    }

    public static function GetByLID1($lid, $limit = 10)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `lottery_session` WHERE `lid` = $lid ORDER BY `id` DESC LIMIT 0,$limit");
    }

    public static function GetOneLatest($lid, $sid)
    {
        $db = Database::getInstance();
        return $db->pdo_query_one("SELECT * FROM `lottery_session` WHERE `lid` = $lid AND `sid` = '$sid' AND `status` = 1 ORDER BY `create_time` DESC");
    }

    public static function Get($lid, $sid)
    {
        $db = Database::getInstance();
        return $db->pdo_query_one("SELECT * FROM `lottery_session` WHERE `lid` = $lid AND `sid` = '$sid' ORDER BY `create_time` DESC");
    }

   public static function GetOneLatestByLID($lid)
{
    $db = Database::getInstance();
    // sửa ORDER BY: ép sid về kiểu số & LIMIT 1
    return $db->pdo_query_one("
        SELECT *
        FROM lottery_session
        WHERE lid = $lid
          AND status = 0
        ORDER BY CAST(sid AS UNSIGNED) DESC
        LIMIT 1
    ");
}
public static function GetPrevSession($lid, $curr_sid) {
    $db = Database::getInstance();
    // Lấy kỳ trước cùng lid, sid nhỏ hơn kỳ hiện tại, lấy lớn nhất trong các sid nhỏ hơn
    return $db->pdo_query_one(
        "SELECT * FROM lottery_session WHERE lid = ? AND sid < ? ORDER BY CAST(sid AS UNSIGNED) DESC LIMIT 1",
        [$lid, $curr_sid]
    );
}


    public static function Insert($data)
    {
        $data["create_time"] = time();
        $db = Database::getInstance();
        return $db->insert(self::$table, $data);
    }

    public static function Update($data, $id)
    {
        $db = Database::getInstance();
        return $db->update(self::$table, $data, $id);
    }

    public static function GetCurrentSessionID($rule)
    {
        $second = $rule * 60;
        $now_time = (date('H') * 60 * 60 + date('i') * 60);
        return date('Ymd') . $rule . intval($now_time / $second);
    }

    public static function GetNextSessionID($rule)
    {
        $second = $rule * 60;
        $now_time = (date('H') * 60 * 60 + date('i') * 60);
        if($now_time + $second >= 86400) {
            $now_time = 86400 - $now_time + $second;
        }
        return date('Ymd') . $rule . intval(($now_time + $second) / $second);
    }

    public static function GetPrevSessionID($rule)
    {
        $second = $rule * 60;
        $now_time = (date('H') * 60 * 60 + date('i') * 60);
        return date('Ymd') . $rule . intval(($now_time - $second) / $second);
    }

    public static function GetRemainSeconds($rule)
    {
        $second = $rule * 60;
        $now_time = (date('H') * 3600 + date('i') * 60 + date('s'));
        $current_start_time = intval($now_time / $second) * $second;
        $current_end_time = $current_start_time + $second;
        return $current_end_time - $now_time;
    }
    public static function EnsurePrevSessionExists($lottery_id, $rule)
{
    $db = Database::getInstance();
    $sid = self::GetCurrentSessionID($rule);

    // Kiểm tra xem session có tồn tại chưa
    $check = $db->pdo_query_one("SELECT * FROM lottery_session WHERE sid = ? AND lid = ?", [$sid, $lottery_id]);

    if (!$check) {
        $db->pdo_insert("lottery_session", [
            "sid" => $sid,
            "lid" => $lottery_id,
            "status" => 1,
            "create_time" => time()
        ]);
    }
}

}
