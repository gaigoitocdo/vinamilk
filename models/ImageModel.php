<?php

include_once __DIR__."/../config/config.php";


class ImageModel
{
    public static function GetAllCategories()
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT vc.*, COUNT(v.id) AS img_num FROM `images_category` vc LEFT JOIN `images` v ON vc.id = v.cate_id GROUP BY vc.id ORDER BY `order` DESC;");
        return $data;
    }

    public static function GetOneCategory($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT * FROM `images_category` WHERE `id` = '$id'");
        return $data;
    }

    public static function GetAllFromCategory($cate)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT * FROM `images` WHERE `cate_id` = '$cate'");
        return $data;
    }

    public static function GetOneImage($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT v.*, vc.name as category FROM `images` v
INNER JOIN `images_category` vc ON v.cate_id = vc.id
WHERE v.id = '$id'");
        return $data;
    }

    public static function GetAllImages()
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT v.*, vc.name as category FROM `images` v
INNER JOIN `images_category` vc ON v.cate_id = vc.id
WHERE 1");
        return $data;
    }

    

    public static function UpdateCategory($id, $name, $order, $status)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("UPDATE `images_category` SET `name`='$name',`order`='$order',`status`='$status' WHERE `id` = '$id'");
        return $data;
    }

    public static function DeleteCategory($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("DELETE FROM `images_category` WHERE `id` = '$id'");
        $data = $db->pdo_execute("DELETE FROM `images` WHERE `cate_id` = '$id'");

        return $data;
    }

    public static function DeleteImage($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("DELETE FROM `images` WHERE `id` = '$id'");
        return $data;
    }

    public static function CreateCategory($name, $order)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("INSERT INTO `images_category`(`id`, `name`, `order`, `status`) VALUES (0, '$name', '$order', 1)");
        return $data;
    }

    public static function CreateImage($name, $img, $type, $cate)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("INSERT INTO `images`(`id`, `cate_id`, `name`, `type`, `url`)
        VALUES
        (0, '$cate', '$name', '$type', '$img')");
        return $data;
    }

    public static function UpdateImage($id, $data)
    {
        $db = Database::getInstance();

        $img = self::GetOneImage($id);

        if(empty($img)) json_response(400, ["success" => false, "message" => "Image not found"]);

        $d = [];
        $d["name"] = $data["name"] != NULL ? $data["name"] : $img["name"];
        $d["url"] = $data["url"] != NULL ? $data["url"] : $img["url"];
        $d["type"] = $data["type"] != NULL ? $data["type"] : $img["type"];
        $d["cate_id"] = $data["cate_id"] != NULL ? $data["cate_id"] : $img["cate_id"];

        return $db->update("images", $data, $id);
    }

}