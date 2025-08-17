<?php

include_once __DIR__."/../config/config.php";

define("TOPUP_NOTI", -999);
define("ADMIN_NOTI", -888);


class NotiModel
{
    private static $table = 'user_notification';

    public static function UserGet($to, $limit = 50)
    {
        return Database::getInstance()->pdo_query("SELECT * FROM `user_notification` WHERE `to` = '$to' ORDER BY `time` DESC LIMIT 0, $limit");
    }

   public static function Send($from, $to, $title, $content, $color = "green", $amount_refused = null)
{
    $data = [
      "color"   => $color,
      "from"    => $from,
      "to"      => $to,
      "title"   => $title,
      "content" => $content,
      "time"    => time()
    ];
    if ($amount_refused !== null) {
        $data['amount_refused'] = $amount_refused;
    }
    return Database::getInstance()->insert(self::$table, $data);
}

     
}