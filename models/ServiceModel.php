<?php

include_once __DIR__."/../config/config.php";


class ServiceModel
{
    public static function GetAll()
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `services` WHERE 1 ORDER BY `order` DESC");
    }

    public static function Get($id)
    {
        $db = Database::getInstance();
        return $db->pdo_query_one("SELECT * FROM `services` WHERE `id` =  '$id'");
    }

    public static function Update($id, $data)
    {
        $i = self::Get($id);

        if(!$i) return json_response(400, ["success" => false, "message" => "invalid parameters"]);

        $data["name"] = $data["name"] == NULL ? $i["name"] : $data["name"];
        $data["order"] = $data["order"] == NULL ? $i["order"] : $data["order"];
        $data["desc"] = $data["desc"] == NULL ? $i["desc"] : $data["desc"];
        $data["end"] = $data["end"] == NULL ? $i["end"] : $data["end"];
        $data["time"] = $data["time"] == NULL ? $i["time"] : $data["time"];
        $data["price"] = $data["price"] == NULL ? $i["price"] : $data["price"];
        $data["images"] = $data["images"] == NULL ? $i["images"] : $data["images"];
        $data["redirect_url"] = $data["redirect_url"] == NULL ? $i["redirect_url"] : $data["redirect_url"];

        $db = Database::getInstance();
        return $db->update('services', $data, $id);
    }

    public static function Delete($id)
    {
        $db = Database::getInstance();
        return $db->pdo_execute("DELETE FROM `services` WHERE `id` =  '$id'");
    }
}