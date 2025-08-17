<?php

include_once __DIR__."/../config/config.php";


class BookingModel
{
    public static function GetAll()
    {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `booking_history` WHERE 1");
    }

    public static function Get($id)
    {
        $db = Database::getInstance();
        return $db->pdo_query_one("SELECT * FROM `booking_history` WHERE `id` =  '$id'");
    }

    public static function Insert($data)
    {
        $db = Database::getInstance();
        return $db->insert('booking_history', $data);
    }

    public static function Delete($id)
    {
        $db = Database::getInstance();
        return $db->pdo_execute("DELETE FROM `booking_history` WHERE `id` =  '$id'");
    }
}