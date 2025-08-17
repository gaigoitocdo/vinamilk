<?php

include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/LotteryItemModel.php";
include_once __DIR__ . "/../models/LotteryModel.php";
include_once __DIR__ . "/../models/UserModel.php";
include_once __DIR__ . "/../models/LotterySessionModel.php";

class LotteryEditModel
{
    public static function Get($key, $s)
    {
        $db = Database::getInstance();
        return $db->pdo_query_one("SELECT * FROM `lottery_edit` WHERE `key` = '$key' AND `session` = '$s'");
    }

    public static function GetAll($key)
    {
        $v = LotteryModel::GetByKey($key);

        if (!$v) json_response(500, ["success" => false, "message" => "Cannot get information"]);

        $second = $v['rule'] * 60;
        $now_time = (date('H') * 60 * 60 + date('i') * 60);
        $now_expect = date('Ymd') . $v['rule'] . intval($now_time / $second);

        $now_info = LotterySessionModel::Get($v["id"], $now_expect);
        if (!$now_info) {
            $now_info = ['create_time' => time()];
        }

        $arr = [];

        for ($i = 0; $i < 50; $i++) {
            $lottery_edit = self::Get($key, $now_expect + $i);

            $next_time = (date('H') * 60 * 60 + date('i') * 60);
            if ($next_time + $second >= 86400) {
                $next_time = 86400 - $next_time + $second;
                $next_expect = date('Ymd') . $v['rule'] . intval($next_time / ($second * $i));
            } else {
                $next_expect = date('Ymd') . $v['rule'] . intval(($next_time + $second * $i) / $second);
            }

            if (!$lottery_edit) {
                // ✅ FIXED: Tạo mã 5 số thay vì A,B,C,D
                $random_code = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);

                $arr[] = [
                    'name' => $v['name'],
                    'key' => $v['key'],
                    'result' => $random_code, // ✅ Mã 5 số trực tiếp
                    'session' => $next_expect,
                    'create_time' => $now_info['create_time'] + $second * $i,
                ];
            } else {
                $u = UserModel::GetOne($lottery_edit["editor"]);
                $arr[] = [
                    'name' => $v['name'],
                    'key' => $v['key'],
                    'result' => $lottery_edit["result"], // ✅ Từ DB - đã là mã 5 số
                    'editor' => $u ? $u["username"] : "unknown",
                    'session' => $next_expect,
                    'create_time' => $now_info['create_time'] + $second * $i,
                    'edit_create_time' => $lottery_edit['create_time'],
                    'edit_update_time' => $lottery_edit['update_time'],
                ];
            }
        }

        echo json_response(200, ["success" => true, "data" => $arr]);
    }
}
