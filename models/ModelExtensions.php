<?php
// models/ModelExtensions.php - Add these methods to your existing models

// UserModel Extensions
class UserModelExtensions {
    
    public static function UpdatePassword($uid, $hashedPassword) {
        global $db; // or however you access your database
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$hashedPassword, $uid]);
    }
    
    public static function UpdateField($uid, $field, $value) {
        global $db;
        $allowedFields = ['name', 'phone', 'gender'];
        if (!in_array($field, $allowedFields)) {
            return false;
        }
        
        $sql = "UPDATE users SET {$field} = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$value, $uid]);
    }
    
    public static function UpdateAvatar($uid, $avatarUrl) {
        global $db;
        $sql = "UPDATE users SET avatar = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$avatarUrl, $uid]);
    }
    
    public static function DeductMoney($uid, $amount) {
        global $db;
        $sql = "UPDATE users SET money = money - ? WHERE id = ? AND money >= ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$amount, $uid, $amount]);
    }
}

// BankModel Extensions
class BankModelExtensions {
    
    public static function Create($data) {
        global $db;
        $sql = "INSERT INTO banks (uid, bankinfo, bankid, name, create_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['uid'],
            $data['bankinfo'],
            $data['bankid'],
            $data['name'],
            $data['create_time']
        ]);
    }
}

// WithdrawModel Extensions
class WithdrawModelExtensions {
    
    public static function Create($data) {
        global $db;
        $sql = "INSERT INTO withdrawals (uid, money, bank_info, bank_id, account_name, status, create_time, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            $data['uid'],
            $data['money'],
            $data['bank_info'],
            $data['bank_id'],
            $data['account_name'],
            $data['status'],
            $data['create_time'],
            $data['note']
        ]);
        
        if ($result) {
            return $db->lastInsertId();
        }
        return false;
    }
    
    public static function GetPendingByUID($uid) {
        global $db;
        $sql = "SELECT * FROM withdrawals WHERE uid = ? AND status = 0 ORDER BY create_time DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$uid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// NotiModel Extensions
class NotiModelExtensions {
    
    public static function Create($data) {
        global $db;
        $sql = "INSERT INTO notifications (uid, title, content, color, time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            $data['uid'],
            $data['title'],
            $data['content'],
            $data['color'],
            $data['time']
        ]);
    }
}

// Usage: Add these methods to your existing models or create wrapper functions
// Example wrapper functions:

// In your UserModel.php, add these methods:
/*
public static function UpdatePassword($uid, $hashedPassword) {
    return UserModelExtensions::UpdatePassword($uid, $hashedPassword);
}

public static function UpdateField($uid, $field, $value) {
    return UserModelExtensions::UpdateField($uid, $field, $value);
}

public static function UpdateAvatar($uid, $avatarUrl) {
    return UserModelExtensions::UpdateAvatar($uid, $avatarUrl);
}

public static function DeductMoney($uid, $amount) {
    return UserModelExtensions::DeductMoney($uid, $amount);
}
*/

// In your BankModel.php, add this method:
/*
public static function Create($data) {
    return BankModelExtensions::Create($data);
}
*/

// In your WithdrawModel.php, add these methods:
/*
public static function Create($data) {
    return WithdrawModelExtensions::Create($data);
}

public static function GetPendingByUID($uid) {
    return WithdrawModelExtensions::GetPendingByUID($uid);
}
*/

// In your NotiModel.php, add this method:
/*
public static function Create($data) {
    return NotiModelExtensions::Create($data);
}
*/