<?php

include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/LotteryItemModel.php";
include_once __DIR__ . "/../models/LotteryModel.php";
include_once __DIR__ . "/../models/UserModel.php";
include_once __DIR__ . "/../models/LotterySessionModel.php";

class LotteryEditModel
{
    public static function Get($key, $s)
    {
        $db = Database::getInstance();
        return $db->pdo_query_one("SELECT * FROM `lottery_edit` WHERE `key` = '$key' AND `session` = '$s'");
    }

    // ✅ ENHANCED: Generate 5-digit codes instead of A,B,C,D
    public static function GenerateRandomResult() {
        // Generate 1-4 random 5-digit codes
        $num_winners = rand(1, 2); // Usually 1-2 winners
        $codes = [];
        
        for ($i = 0; $i < $num_winners; $i++) {
            $code = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
            // Ensure unique codes
            while (in_array($code, $codes)) {
                $code = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
            }
            $codes[] = $code;
        }
        
        return implode(',', $codes);
    }

    // ✅ ENHANCED: Validate result format
    public static function ValidateResult($result) {
        if (empty($result)) {
            return false;
        }
        
        $result = trim($result);
        
        // Check if it's 5-digit codes (single or comma-separated)
        if (preg_match('/^\d{5}(,\d{5})*$/', $result)) {
            return true;
        }
        
        // Legacy support: Check if it's A,B,C,D format
        if (preg_match('/^[A-D](,[A-D])*$/i', $result)) {
            return true;
        }
        
        return false;
    }

    // ✅ ENHANCED: Convert legacy A,B,C,D to 5-digit codes
    public static function ConvertLegacyResult($result, $session_codes) {
        if (!$result || !$session_codes) {
            return $result;
        }
        
        // If already 5-digit format, return as is
        if (preg_match('/^\d{5}(,\d{5})*$/', $result)) {
            return $result;
        }
        
        // Convert A,B,C,D to codes
        if (preg_match('/^[A-D](,[A-D])*$/i', $result)) {
            $letters = array_map('trim', explode(',', strtolower($result)));
            $codes = [];
            
            foreach ($letters as $letter) {
                if (isset($session_codes[$letter])) {
                    $codes[] = $session_codes[$letter];
                }
            }
            
            return implode(',', $codes);
        }
        
        return $result;
    }

    public static function GetAll($key)
    {
        $v = LotteryModel::GetByKey($key);

        if (!$v) json_response(500, ["success" => false, "message" => "Cannot get information"]);

        $second = $v['rule'] * 60;
        $now_time = (date('H') * 60 * 60 + date('i') * 60);
        $now_expect = date('Ymd') . $v['rule'] . intval($now_time / $second);

        $now_info = LotterySessionModel::Get($v["id"], $now_expect);
        if (!$now_info) {
            $now_info = ['create_time' => time()];
        }

        $arr = [];

        for ($i = 0; $i < 50; $i++) {
            $lottery_edit = self::Get($key, $now_expect + $i);

            $next_time = (date('H') * 60 * 60 + date('i') * 60);
            if ($next_time + $second >= 86400) {
                $next_time = 86400 - $next_time + $second;
                $next_expect = date('Ymd') . $v['rule'] . intval($next_time / ($second * $i));
            } else {
                $next_expect = date('Ymd') . $v['rule'] . intval(($next_time + $second * $i) / $second);
            }

            if (!$lottery_edit) {
                // ✅ ENHANCED: Generate random 5-digit code(s) instead of A,D
                $random_result = self::GenerateRandomResult();

                $arr[] = [
                    'name' => $v['name'],
                    'key' => $v['key'],
                    'result' => $random_result, // 5-digit code(s) instead of "A,D"
                    'session' => $next_expect,
                    'create_time' => $now_info['create_time'] + $second * $i,
                ];
            } else {
                $u = UserModel::GetOne($lottery_edit["editor"]);
                
                // ✅ ENHANCED: Convert legacy results if needed
                $result = $lottery_edit["result"];
                
                // Get session codes for conversion if needed
                $session_info = LotterySessionModel::Get($v["id"], $next_expect);
                if ($session_info && !empty($session_info['session_codes'])) {
                    $session_codes = json_decode($session_info['session_codes'], true);
                    $result = self::ConvertLegacyResult($result, $session_codes);
                }
                
                $arr[] = [
                    'name' => $v['name'],
                    'key' => $v['key'],
                    'result' => $result, // Should be 5-digit code(s)
                    'editor' => $u ? $u["username"] : "unknown",
                    'session' => $next_expect,
                    'create_time' => $now_info['create_time'] + $second * $i,
                    'edit_create_time' => $lottery_edit['create_time'],
                    'edit_update_time' => $lottery_edit['update_time'],
                ];
            }
        }

        echo json_response(200, ["success" => true, "data" => $arr]);
    }

    // ✅ NEW: Update result with validation
    public static function UpdateResult($key, $session, $result, $editor_id) {
        // Validate result format
        if (!self::ValidateResult($result)) {
            throw new Exception("Invalid result format. Use 5-digit codes (e.g., 12345) or comma-separated codes (e.g., 12345,67890)");
        }

        $cur = self::Get($key, $session);
        $db = Database::getInstance();

        if ($cur) {
            return $db->update('lottery_edit', [
                "result" => $result, 
                'update_time' => time(), 
                'editor' => $editor_id
            ], $cur["id"]);
        } else {
            return $db->insert('lottery_edit', [
                "key" => $key,
                "session" => $session,
                "result" => $result,
                'editor' => $editor_id,
                'create_time' => time(),
                'update_time' => time()
            ]);
        }
    }

    // ✅ NEW: Delete result
    public static function DeleteResult($key, $session) {
        $db = Database::getInstance();
        return $db->pdo_execute("DELETE FROM `lottery_edit` WHERE `key` = ? AND `session` = ?", $key, $session);
    }

    // ✅ NEW: Get winning statistics
    public static function GetWinningStats($key, $limit = 10) {
        $db = Database::getInstance();
        $lottery = LotteryModel::GetByKey($key);
        
        if (!$lottery) return [];
        
        return $db->pdo_query("
            SELECT 
                ls.sid,
                ls.result,
                ls.session_codes,
                le.result as admin_result,
                le.editor,
                u.username as editor_name,
                ls.create_time
            FROM lottery_session ls
            LEFT JOIN lottery_edit le ON le.session = ls.sid
            LEFT JOIN users u ON u.id = le.editor
            WHERE ls.lid = ? 
              AND ls.result != ''
              AND ls.status = 0
            ORDER BY CAST(ls.sid AS UNSIGNED) DESC
            LIMIT ?
        ", $lottery['id'], $limit);
    }
}