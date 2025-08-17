<?php

include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/LotteryItemModel.php";
include_once __DIR__ . "/../models/LotterySessionModel.php";

class LotteryModel
{
    public static function GetAllCategories()
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT vc.*, COUNT(v.id) AS lottery_num FROM `lottery_category` vc LEFT JOIN `lottery` v ON vc.id = v.cate_id GROUP BY vc.id;");
        return $data;
    }

    public static function CreateCategory($data)
    {
        $data["create_time"] = time();
        $data["update_time"] = time();

        $db = Database::getInstance();
        return $db->insert("lottery_category", $data);
    }

    public static function GetCategory($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT * FROM `lottery_category` WHERE `id` = '$id'");
        return $data;
    }

    public static function DeleteCategory($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("DELETE FROM `lottery_category` WHERE `id` = '$id'");
        return $data;
    }

    public static function UpdateCategory($id, $data)
    {
        $cat = self::GetCategory($id);

        if (empty($cat)) json_response(400, ["success" => false, "message" => "Category $id not found"]);

        $d = [];
        $d["name"] = $data["name"] != NULL ? $data["name"] : $cat['name'];
        $d["status"] = $data["status"] != NULL ? $data["status"] : $cat['status'];
        $d["order"] = $data["order"] != NULL ? $data["order"] : $cat['order'];

        $db = Database::getInstance();
        $data = $db->update("lottery_category", $d, $id);
        return $data;
    }

    public static function GetAll()
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT l.*, lc.name AS cate_name FROM `lottery` l INNER JOIN `lottery_category` lc ON l.cate_id = lc.id WHERE 1");
        return $data;
    }

    public static function Get($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT * FROM `lottery` WHERE `id` = '$id'");

        if ($data) {
            $s = LotterySessionModel::GetOneLatestByLID($data["id"]);
            $data["items"] = [];
            $data["prev_session"] = LotterySessionModel::GetPrevSessionID($data["rule"]);

            if ($s && !empty($s["result"])) {//
                $data["prev_session"] = $s["sid"];
                $data["items"] = explode(",", $s["result"]);
            }
            //
            $data["now_session"] = LotterySessionModel::GetCurrentSessionID($data["rule"]);
            $data["next_session"] = LotterySessionModel::GetNextSessionID($data["rule"]);
            $data["second"] = LotterySessionModel::GetRemainSeconds($data["rule"]);
        }

        return $data;
    }

    public static function GetByKey($key)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT * FROM `lottery` WHERE `key` = '$key'");
        return $data;
    }

    public static function Create($data)
    {
        if (!empty(self::GetByKey($data["key"])))
            return json_response(400, ["success" => false, "message" => "Lottery key already exist, choose another key"]);

        $db = Database::getInstance();
        $id = $db->insert("lottery", $data);
        if ($id) {
            LotteryItemModel::Register($id, 1.1);
        }
    }

    public static function Update($id, $data)
    {
        $l = self::Get($id);

        if (empty($l)) json_response(400, ["success" => false, "message" => "Lottery $id not found"]);

        $d = [];
        $d["name"] = $data["name"] != NULL ? $data["name"] : $l['name'];
        $d["status"] = $data["status"] != NULL ? $data["status"] : $l['status'];
        $d["cate_id"] = $data["cate_id"] != NULL ? $data["cate_id"] : $l['cate_id'];
        $d["desc"] = $data["desc"] != NULL ? $data["desc"] : $l['desc'];
        $d["image"] = $data["image"] != NULL ? $data["image"] : $l['image'];
        $d["rule"] = $data["rule"] != NULL ? $data["rule"] : $l['rule'];
        $d["condition"] = $data["condition"] != NULL ? $data["condition"] : $l['condition'];
        $d["hot"] = $data["hot"] != NULL ? $data["hot"] : $l['hot'];

        $db = Database::getInstance();
        $data = $db->update("lottery", $d, $id);
        return $data;
    }

    public static function Delete($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("DELETE FROM `lottery` WHERE `id` = '$id'");
        return $data;
    }

    public static function GetOdd($lid)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT * FROM `lottery_item` WHERE `lid` = '$lid'");
        return $data;
    }
}
