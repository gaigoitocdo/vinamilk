<?php

include_once __DIR__."/../config/config.php";
include_once __DIR__."/../models/NotiModel.php";


class UserModel
{
    public static function GetAll()
    {
        $db = Database::getInstance();
        // $db->insert("users", [
        //     "username" => "admin",
        //     "password" => md5("123456"),
        //     "email" => "admin@gmail.com",
        //     "name" => "Huy",
        //     "gender" => "0",
        //     "phone" => "0123456789",
        //     "type" => "3",
        //     "status" => "1",
        //     "money" => "999.91",
        //     "credit" => "100",
        //     "vip" => "10",
        //     "num" => "10",
        //     "min" => "1",
        //     "max" => "1000",
        //     "reg_date" => time(),
        //     "last_ip" => "127.0.0.1",
        //     "last_login" => time(),
        // ]);
        $data = $db->pdo_query("SELECT * FROM `users` WHERE 1");
        return $data;
    }

    public static function GetOne($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT * FROM `users` WHERE `id` = '$id'");
        return $data;
    }

    
    public static function Login($username, $password)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password'");
        return $data;
    }

    public static function Register($data)
    {
        $db = Database::getInstance();
        $data = $db->insert("users", $data);
        return $data;
    }


    public static function GetOneFromUsername($username)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query_one("SELECT * FROM `users` WHERE `username` = '$username'");
        return $data;
    }

    public static function GetLimit($start, $limit)
    {
        $db = Database::getInstance();
        $data = $db->pdo_query("SELECT * FROM `users` WHERE 1 LIMIT $limit OFFSET $start");
        return $data;
    }

    public static function Delete($id)
    {
        $db = Database::getInstance();
        $data = $db->pdo_execute("DELETE FROM `users` WHERE `id` = '$id'");
        return $data;
    }

    public static function Create($data)
    {
        $u = self::GetOneFromUsername($data["username"]);

        if(!empty($u)) json_response(400, ["success" => false, "message" => "Username already exist"]);
        
        $d = [];
        $d["username"] = $data["username"];
        $d["password"] = md5($data["password"]);
        $d["money"] = $data["money"];
        $d["credit"] = $data["credit"];
        $d["vip"] = $data["vip"];
        $d["name"] = $data["name"];
        $d["num"] = $data["num"];
        $d["min"] = $data["min"];
        $d["max"] = $data["max"];
        $d["reg_date"] = time();
        $d["last_ip"] = "127.0.0.1";
        $d["last_login"] = time();

        $db = Database::getInstance();
        return $db->insert("users", $d);
    }

    public static function Update($id, $data)
    {

        $user = self::GetOne($id);

        if(empty($user)) json_response(400, ["success" => false, "message" => "User not found"]);
        

        $d = [];
        $d["password"] = $data["password"] != NULL ? md5($data["password"]) : $user["password"];
        $d["money"] = $data["money"] != NULL ? $data["money"] : $user["money"];
        $d["credit"] = $data["credit"] != NULL ? $data["credit"] : $user["credit"];
        $d["member_code"] = $data["member_code"] != NULL ? $data["member_code"] : NULL;
        $d["vip"] = $data["vip"] != NULL ? $data["vip"] : $user["vip"];
        $d["name"] = $data["name"] != NULL ? $data["name"] : $user["name"];
        $d["num"] = $data["num"] != NULL ? $data["num"] : $user["num"];
        $d["min"] = $data["min"] != NULL ? $data["min"] : $user["min"];
        $d["max"] = $data["max"] != NULL ? $data["max"] : $user["max"];
        $d["type"] = $data["type"] != NULL ? $data["type"] : $user["type"];
        $d["total_topup"] = $data["total_topup"] != NULL ? $data["total_topup"] : $user["total_topup"];

        $db = Database::getInstance();
        return $db->update("users", $d, $id);
    }

    public static function Update2($id, $data)
    {
        $user = self::GetOne($id);

        if(empty($user)) json_response(400, ["success" => false, "message" => "User not found"]);

        $db = Database::getInstance();
        return $db->update("users", $data, $id);
    }

 public static function UpdateMoney($uid, $amount, $description = "", $type = "System") {
        $db = Database::getInstance();
        
        // Lấy số dư hiện tại
        $user = self::GetOne($uid);
        if (!$user) {
            return false;
        }
        
        $old_balance = floatval($user['money']);
        $new_balance = $old_balance + $amount;
        
        // Cập nhật số dư
        $success = $db->update('users', ['money' => $new_balance], $uid);
        
        if ($success) {
            // Log transaction
            $log_data = [
                'uid' => $uid,
                'amount' => $amount,
                'old_balance' => $old_balance,
                'new_balance' => $new_balance,
                'description' => $description,
                'type' => $type,
                'create_time' => time()
            ];
            
            $db->insert('money_logs', $log_data);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Lấy lịch sử giao dịch
     */
    public static function GetMoneyLogs($uid, $limit = 50) {
        $db = Database::getInstance();
        return $db->pdo_query("
            SELECT * FROM money_logs 
            WHERE uid = ? 
            ORDER BY create_time DESC 
            LIMIT ?
        ", $uid, $limit);
    }
    public static function UpdateStatus($id, $status)
    {
        $user = self::GetOne($id);

        if(empty($user)) json_response(400, ["success" => false, "message" => "User $id not found"]);

        $db = Database::getInstance();

        return $db->update("users", ["status" => $status], $id);
    }

    public static function Update1($data, $id)
    {
        $db = Database::getInstance();
        return $db->update("users", $data, $id);
    }

    public static function GetVipLevel($id)
    {
        $db = Database::getInstance();

        $need = get_config("calc_vip_using_total_topup") == 1;

        $info = self::GetOne($id);

        $result = NULL;

        if($need)
        {
            $total = $info["total_topup"];

            $list_vip = $db->pdo_query("SELECT * FROM `vip_level` WHERE 1 ORDER BY `min` ASC");

            foreach($list_vip as $v)
            {
                if($total >= $v["min"])
                {
                    $result = $v;
                }
            }
        }
        else
        {
            $result = [
                "level" => "VIP ".$info["vip"],
                "logo" => asset_link("image", "vip-badge.png"),
                "min" => 9999999,
                "desc" => $info["vip"]
            ];
        }

        return $result;
    }
}