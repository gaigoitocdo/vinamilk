<?php

include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/LotteryItemModel.php";
include_once __DIR__ . "/../models/LotteryModel.php";

class LotteryHistoryModel {
    private static $table = 'lottery_history';

    //status: 0: chưa giải quyết, 1: đã giải quyết
    //is_win: 0: không, 1: thắng, 2: thua

    public static function GetAllUnbet($lid, $sid)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `lottery_history` WHERE `lid` = $lid AND `sid` = '$sid' AND `status` = 0");
    }

    public static function GetAll()
    {
        $db = Database::getInstance();
        return $db->pdo_query_one("SELECT * FROM `lottery_history` WHERE 1");
    }

    public static function Insert($data)
    {
        $data["create_time"] = time();
        $db = Database::getInstance();
        return $db->insert(self::$table, $data);
    }

    public static function GetByUID($uid, $lid, $limit = 10)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `lottery_history` WHERE `uid` = '$uid' AND `lid` = '$lid' ORDER BY `id` DESC LIMIT 0,$limit");
    }

    public static function GetByUID1($uid, $limit = 10)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `lottery_history` WHERE `uid` = '$uid' ORDER BY `id` DESC LIMIT 0,$limit");
    }

    public static function Update($data, $id)
    {
        $db = Database::getInstance();
        return $db->update(self::$table, $data, $id);
    }
}