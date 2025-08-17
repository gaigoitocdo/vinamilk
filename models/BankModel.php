<?php

include_once __DIR__."/../config/config.php";



class BankModel
{
    private static $table = 'bank_bind';

    public static function GetFromUID($id)
    {
        return Database::getInstance()->pdo_query_one("SELECT * FROM `bank_bind` WHERE `uid` = '$id'");
    }

    public static function Get($id)
    {
        return Database::getInstance()->pdo_query_one("SELECT * FROM `bank_bind` WHERE `id` = '$id'");
    }

    public static function Insert($data)
    {
        return Database::getInstance()->insert(self::$table, $data);
    }

    public static function Delete($id)
    {
        return Database::getInstance()->pdo_execute("DELETE FROM `bank_bind` WHERE `id` = '$id'");
    }

    public static function Update($id, $data)
    {
        $info = self::Get($id);
        if(!$info) json_response(500, ["success" => false, "message" => "???????"]);

        $data["bankid"] = $data["bankid"] != NULL ? $data["bankid"] : $info["bankid"];
        $data["bankinfo"] = $data["bankinfo"] != NULL ? $data["bankinfo"] : $info["bankinfo"];
        $data["name"] = $data["name"] != NULL ? $data["name"] : $info["name"];

        return Database::getInstance()->update(self::$table, $data, $id);
    }
}