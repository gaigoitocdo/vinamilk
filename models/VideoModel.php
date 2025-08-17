<?php

include_once __DIR__."/../config/config.php";


class VideoModel
{
    public static function GetAllCategories()
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT vc.*, COUNT(v.id) AS vid_num FROM `videos_category` vc LEFT JOIN `videos` v ON vc.id = v.cate_id GROUP BY vc.id;");
        return $data;
    }

    public static function GetOneCategory($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT * FROM `videos_category` WHERE `id` = '$id'");
        return $data;
    }

    public static function GetOneVideo($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT v.*, vc.name as category FROM `videos` v
INNER JOIN `videos_category` vc ON v.cate_id = vc.id
WHERE v.id = '$id'");
        return $data;
    }

    public static function GetAllVideos()
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT v.*, vc.name as category FROM `videos` v
INNER JOIN `videos_category` vc ON v.cate_id = vc.id
WHERE 1");
        return $data;
    }

    public static function GetAllFromCategory($cid)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT v.*, vc.name as category FROM `videos` v
INNER JOIN `videos_category` vc ON v.cate_id = vc.id
WHERE v.cate_id = '$cid'");
        return $data;
    }

    public static function UpdateCategory($id, $name, $order, $status)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("UPDATE `videos_category` SET `name`='$name',`order`='$order',`status`='$status' WHERE `id` = '$id'");
        return $data;
    }

    public static function DeleteCategory($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("DELETE FROM `videos_category` WHERE `id` = '$id'");
        return $data;
    }

    public static function DeleteVideo($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("DELETE FROM `videos` WHERE `id` = '$id'");
        return $data;
    }

    public static function CreateCategory($name, $order)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("INSERT INTO `videos_category`(`id`, `name`, `order`, `status`) VALUES (0, '$name', '$order', 1)");
        return $data;
    }

    public static function CreateVideo($name, $img, $vid, $time, $views, $cate, $video_url_type)
    {
        $db = Database::getInstance();
        $t = time();
        $data = $db->pdo_execute("INSERT INTO `videos`(`id`, `cate_id`, `name`, `bg_image`, `video_url`, `status`, `hot`, `upload_time`, `duration`, `views`, `video_url_type`)
        VALUES
        (0, '$cate', '$name', '$img', '$vid', '1', '0', '$t', '$time', $views, $video_url_type)");
        return $data;
    }

    public static function UpdateVideo($id, $name, $img, $vid, $status, $hot, $time, $views, $cate, $video_url_type)
    {
        $t = time();
        $db = Database::getInstance();
        $data = $db->pdo_execute("UPDATE `videos` SET `cate_id`='$cate',`name`='$name',`bg_image`='$img',`video_url`='$vid',`status`='$status',`hot`='$hot',`upload_time`='$t',`duration`='$time',`views`='$views', `video_url_type` = '$video_url_type' WHERE `id` = '$id'");
        return $data;
    }

}