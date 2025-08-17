<?php

include_once __DIR__."/../config/config.php";

class LotteryItemModel {
    public static function GetFromLotteryID($id)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `lottery_item` WHERE `lid` = '$id'");
    }
    public static function Get($id)
    {
        $db = Database::getInstance();
        return $db->pdo_query_one("SELECT * FROM `lottery_item` WHERE `id` = '$id'");
    }
    public static function Register($lid, $rate = 1.1)
    {
        $db = Database::getInstance();
        $data = ["A", "B", "C", "D"];

        foreach($data as $key)
        {
            $d = [];
            $d["lid"] = $lid;
            $d["name"] = $key;
            $d["type"] = $key;
            $d["proportion"] = $rate;
            $d["update_time"] = time();
            $db->insert("lottery_item", $d);
        }
    }

    public static function Update($id, $data)
    {
        $i = self::Get($id);

        if(empty($i)) return json_response(400, ["success" => false, "message" => "Invalid lottery item $id"]);

        $d = [];

        $d["name"] = $data["name"] != NULL ? $data["name"] : $i["name"];
        $d["proportion"] = $data["proportion"] != NULL ? $data["proportion"] : $i["proportion"];

        
        $db = Database::getInstance();

        return $db->update("lottery_item", $d, $id);
    }
}