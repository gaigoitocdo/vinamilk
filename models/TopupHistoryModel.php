<?php

include_once __DIR__."/../config/config.php";

class TopupHistoryModel {
    
    public static function Create($user_id, $topup_type_id, $amount, $proof_file, $topup_type_name = '') {
        $db = Database::getInstance();
        return $db->pdo_execute("INSERT INTO topup_history (user_id, topup_type_id, topup_type, amount, proof, status, date, created_at) VALUES (?, ?, ?, ?, ?, 0, ?, NOW())", 
            [$user_id, $topup_type_id, $topup_type_name, $amount, $proof_file, time()]);
    }
    
    public static function UpdateStatus($id, $status, $refuse_reason = '') {
        $db = Database::getInstance();
        return $db->pdo_execute("UPDATE topup_history SET status = ?, refuse_reason = ?, updated_at = NOW() WHERE id = ?", 
            [$status, $refuse_reason, $id]);
    }
    
    public static function GetById($id) {
        $db = Database::getInstance();
        $result = $db->pdo_query("SELECT th.*, u.username FROM topup_history th LEFT JOIN users u ON th.user_id = u.id WHERE th.id = ?", [$id]);
        return $result ? $result[0] : null;
    }
    
    public static function GetByUserId($user_id, $limit = 20) {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM topup_history WHERE user_id = ? ORDER BY id DESC LIMIT ?", [$user_id, $limit]);
    }
    
    public static function GetPending() {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT th.*, u.username FROM topup_history th LEFT JOIN users u ON th.user_id = u.id WHERE th.status = 0 ORDER BY th.id ASC");
    }
}
