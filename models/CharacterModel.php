<?php

include_once __DIR__ . "/../config/config.php";


class CharacterModel
{
    public static $AvailableMeta = [
        "telegram" => "Telegram",
        "facebook" => "Facebook",
        "twitter" => "Twitter",
        "phone" => "SĐT",
        "zalo" => "Zalo",
        "email" => "Email",
        "address" => "Địa chỉ",
        "real_name" => "Tên thật"
    ];

    public static function GetRandomImages($count = 10)
    {
        $db = Database::getInstance();
        return $db->pdo_query_column("SELECT `asset_url` FROM `characters_image` WHERE `type` = 1 LIMIT $count OFFSET 0"); //get random public image
    }

    public static function GetShortAll($limit = 10)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT `id`, `name` FROM `characters` WHERE `status` = 1 LIMIT $limit OFFSET 0"); //get random public image
    }

    public static function GetOne($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT * FROM `characters` WHERE `id` = '$id'");

        if ($data) {
            $meta = $db->pdo_query("SELECT * FROM `characters_metadata` WHERE `meta_id` = $id");
            foreach ($meta as $o) {
                $data[$o["meta_key"]] = $o["meta_value"];
            }
        }

        return $data;
    }

    public static function GetMetadata($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT * FROM `characters_metadata` WHERE `meta_id` = '$id'");
        foreach($data as &$d)
        {
            if(isset(self::$AvailableMeta[$d["meta_key"]]))
                $d["display_name"] = self::$AvailableMeta[$d["meta_key"]];
        }
        return $data;
    }

    public static function AddMetadata($data)
    {
        $db = Database::getInstance();
        return $db->insert('characters_metadata', $data);
    }

    public static function AddCharacterImage($data)
    {
        $db = Database::getInstance();
        return $db->insert('characters_image', $data);
    }

    public static function DeleteCharacterMetadata($id)
    {
        $db = Database::getInstance();
        return $db->pdo_query("DELETE FROM `characters_metadata` WHERE `id` = $id");
    }

    public static function GetNewCharacters($limit = 10)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `characters` WHERE `type` = 1 AND `status` = 1 LIMIT $limit OFFSET 0");
    }

    public static function GetCharacters($offset = 0, $limit = 15)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `characters` WHERE `status` = 1 LIMIT $limit OFFSET $offset");
    }

    public static function GetCharacterImages($id)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `characters_image` WHERE `character_id` = '$id'");
    }

    public static function GetMainCharacters($limit = 10)
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `characters` WHERE `type` = 0 AND `status` = 1 LIMIT $limit OFFSET 0");
    }

    public static function DeleteCharacter($id)
    {
        $db = Database::getInstance();
        return $db->pdo_execute("DELETE FROM `characters` WHERE `id` = '$id'");
    }

    public static function DeleteCharacterImage($id)
    {
        $db = Database::getInstance();
        return $db->pdo_execute("DELETE FROM `characters_image` WHERE `id` = '$id'");
    }

    public static function UpdateCharacterImage($id, $data)
    {
        $db = Database::getInstance();
        return $db->update("characters_image", $data, $id);
    }

    public static function GetOneNewCharacter($id)
    {
        try {
            $db = Database::getInstance();
            $data = $db->pdo_query_one("SELECT * FROM `characters` WHERE `id` = $id");
            if (empty($data)) return false;
            $images = $db->pdo_query("SELECT `type`, `asset_type`, `asset_url` FROM `characters_image` WHERE `character_id` = $id");
            $data["images"] = $images;
            $meta = $db->pdo_query("SELECT * FROM `characters_metadata` WHERE `meta_id` = $id");
            foreach ($meta as $o) {
                $data[$o["meta_key"]] = $o["meta_value"];
            }
            return $data;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function UpdateStatus($id, $status)
    {
        $user = self::GetOne($id);

        if (empty($user)) json_response(400, ["success" => false, "message" => "Character $id not found"]);

        $db = Database::getInstance();

        return $db->update("characters", ["status" => $status], $id);
    }

    public static function UpdateCharacter($id, $data)
    {
        $user = self::GetOne($id);

        if (empty($user)) json_response(400, ["success" => false, "message" => "Character $id not found"]);

        $db = Database::getInstance();

        $d = [];
        $d["name"] = $data["name"] != NULL ? $data["name"] : $user["name"];
        $d["desc"] = $data["desc"] != NULL ? $data["desc"] : $user["desc"];
        $d["image"] = $data["image"] != NULL ? $data["image"] : $user["image"];
        $d["measurements"] = $data["measurements"] != NULL ? $data["measurements"] : $user["measurements"];
        $d["age"] = $data["age"] != NULL ? $data["age"] : $user["age"];
        $d["type"] = $data["type"] != NULL ? $data["type"] : $user["type"];
        $d["location"] = $data["location"] != NULL ? $data["location"] : $user["location"];
        $d["status"] = $data["status"] != NULL ? $data["status"] : $user["status"];
        return $db->update("characters", $data, $id);
    }

    public static function Update($id, $data)
    {
        $user = self::GetOne($id);

        if (empty($user)) json_response(400, ["success" => false, "message" => "Character $id not found"]);

        $db = Database::getInstance();
        return $db->update("characters", $data, $id);
    }

    public static function CreateCharacter($data)
    {

        $db = Database::getInstance();

        return $db->insert("characters", $data);
    }
}
