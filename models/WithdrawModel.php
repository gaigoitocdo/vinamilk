<?php

include_once __DIR__."/../config/config.php";


class WithdrawModel
{

    public static function InsertHistory($data)
    {
        $db = Database::getInstance();
        $data = $db->insert('withdraw_history', $data);
        return $data;
    }

    public static function UpdateHistory($id, $data)
    {
        $db = Database::getInstance();
        $data = $db->update('withdraw_history', $data, $id);
        return $data;
    }

    public static function GetOneHistory($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT * FROM `withdraw_history` WHERE `id` = '$id'");
        return $data;
    }

    public static function GetByUID($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT * FROM `withdraw_history` WHERE `uid` = '$id' ORDER BY `create_time` DESC");
        return $data;
    }
}