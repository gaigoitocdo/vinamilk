<?php

include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/LotteryItemModel.php";
include_once __DIR__ . "/../models/LotteryModel.php";

class LotterySessionModel
{
    private static $table = 'lottery_session';
    
    // ✅ ENHANCED: Generate and store 5-digit codes for session
    public static function GenerateSessionCodes($session_id) {
        $codes = [
            'a' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
            'b' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
            'c' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
            'd' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT)
        ];
        
        // Ensure all codes are unique
        while (count(array_unique($codes)) !== 4) {
            $codes = [
                'a' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'b' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'c' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'd' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT)
            ];
        }
        
        return $codes;
    }
    
    // ✅ ENHANCED: Get session codes with fallback generation
    public static function GetSessionCodes($lid, $sid) {
        $db = Database::getInstance();
        $session = $db->pdo_query_one(
            "SELECT session_codes FROM lottery_session WHERE lid = ? AND sid = ?", 
            $lid, $sid
        );
        
        if ($session && !empty($session['session_codes'])) {
            $codes = json_decode($session['session_codes'], true);
            if ($codes && count($codes) === 4) {
                return $codes;
            }
        }
        
        // Generate new codes if not found or invalid
        $codes = self::GenerateSessionCodes($sid);
        
        // Update session with new codes
        $db->pdo_execute(
            "UPDATE lottery_session SET session_codes = ? WHERE lid = ? AND sid = ?",
            json_encode($codes), $lid, $sid
        );
        
        return $codes;
    }
    
    // ✅ ENHANCED: Get winning code based on result
    public static function GetWinningCode($lid, $sid) {
        $db = Database::getInstance();
        $session = $db->pdo_query_one(
            "SELECT result, session_codes FROM lottery_session WHERE lid = ? AND sid = ? AND result != ''", 
            $lid, $sid
        );
        
        if (!$session || empty($session['result'])) {
            return null;
        }
        
        $result = $session['result'];
        $session_codes = json_decode($session['session_codes'] ?? '{}', true);
        
        // If result is already a 5-digit code, return it
        if (preg_match('/^\d{5}$/', $result)) {
            return [
                'code' => $result,
                'letter' => self::GetLetterFromCode($session_codes, $result),
                'all_codes' => [$result]
            ];
        }
        
        // Handle multiple codes (comma-separated)
        if (strpos($result, ',') !== false) {
            $codes = array_map('trim', explode(',', $result));
            $valid_codes = array_filter($codes, function($code) {
                return preg_match('/^\d{5}$/', $code);
            });
            
            if (!empty($valid_codes)) {
                return [
                    'code' => $valid_codes[0], // First winning code
                    'letter' => self::GetLetterFromCode($session_codes, $valid_codes[0]),
                    'all_codes' => $valid_codes
                ];
            }
        }
        
        // Legacy support: convert A,B,C,D to codes
        if (preg_match('/^[A-D](,[A-D])*$/i', $result)) {
            $letters = array_map('trim', explode(',', strtolower($result)));
            $codes = [];
            
            foreach ($letters as $letter) {
                if (isset($session_codes[$letter])) {
                    $codes[] = $session_codes[$letter];
                }
            }
            
            if (!empty($codes)) {
                return [
                    'code' => $codes[0],
                    'letter' => $letters[0],
                    'all_codes' => $codes
                ];
            }
        }
        
        return null;
    }
    
    // ✅ Helper: Get letter from code
    private static function GetLetterFromCode($session_codes, $target_code) {
        if (!$session_codes || !$target_code) return null;
        
        foreach ($session_codes as $letter => $code) {
            if ($code === $target_code) {
                return $letter;
            }
        }
        return null;
    }
    
    // ✅ ENHANCED: Insert with session codes
    public static function Insert($data) {
        $data["create_time"] = time();
        
        // Generate session codes if not provided
        if (!isset($data['session_codes'])) {
            $codes = self::GenerateSessionCodes($data['sid'] ?? '');
            $data['session_codes'] = json_encode($codes);
        }
        
        $db = Database::getInstance();
        return $db->insert(self::$table, $data);
    }
    
    // ✅ ENHANCED: Update with validation
    public static function Update($data, $id) {
        // Validate result format if provided
        if (isset($data['result']) && !empty($data['result'])) {
            $result = trim($data['result']);
            
            // Allow 5-digit codes or comma-separated 5-digit codes
            if (!preg_match('/^\d{5}(,\d{5})*$/', $result)) {
                // Also allow legacy A,B,C,D format for backward compatibility
                if (!preg_match('/^[A-D](,[A-D])*$/i', $result)) {
                    throw new Exception("Invalid result format. Use 5-digit codes (e.g., 12345) or comma-separated codes (e.g., 12345,67890)");
                }
            }
        }
        
        $db = Database::getInstance();
        return $db->update(self::$table, $data, $id);
    }

    // ============== EXISTING METHODS (Preserved) ==============
    
    public static function GetByLID($lid) {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `lottery_session` WHERE `lid` = $lid");
    }

    public static function GetByLID1($lid, $limit = 10) {
        $db = Database::getInstance();
        return $db->pdo_query("SELECT * FROM `lottery_session` WHERE `lid` = $lid ORDER BY `id` DESC LIMIT 0,$limit");
    }

    public static function GetOneLatest($lid, $sid) {
        $db = Database::getInstance();
        return $db->pdo_query_one("SELECT * FROM `lottery_session` WHERE `lid` = $lid AND `sid` = '$sid' AND `status` = 1 ORDER BY `create_time` DESC");
    }

    public static function Get($lid, $sid) {
        $db = Database::getInstance();
        return $db->pdo_query_one("SELECT * FROM `lottery_session` WHERE `lid` = $lid AND `sid` = '$sid' ORDER BY `create_time` DESC");
    }

    public static function GetOneLatestByLID($lid) {
        $db = Database::getInstance();
        return $db->pdo_query_one("
            SELECT *
            FROM lottery_session
            WHERE lid = $lid
              AND status = 0
            ORDER BY CAST(sid AS UNSIGNED) DESC
            LIMIT 1
        ");
    }
    
    public static function GetPrevSession($lid, $curr_sid) {
        $db = Database::getInstance();
        return $db->pdo_query_one(
            "SELECT * FROM lottery_session WHERE lid = ? AND sid < ? ORDER BY CAST(sid AS UNSIGNED) DESC LIMIT 1",
            [$lid, $curr_sid]
        );
    }

    public static function GetCurrentSessionID($rule) {
        $second = $rule * 60;
        $now_time = (date('H') * 60 * 60 + date('i') * 60);
        return date('Ymd') . $rule . intval($now_time / $second);
    }

    public static function GetNextSessionID($rule) {
        $second = $rule * 60;
        $now_time = (date('H') * 60 * 60 + date('i') * 60);
        if($now_time + $second >= 86400) {
            $now_time = 86400 - $now_time + $second;
        }
        return date('Ymd') . $rule . intval(($now_time + $second) / $second);
    }

    public static function GetPrevSessionID($rule) {
        $second = $rule * 60;
        $now_time = (date('H') * 60 * 60 + date('i') * 60);
        return date('Ymd') . $rule . intval(($now_time - $second) / $second);
    }

    public static function GetRemainSeconds($rule) {
        $second = $rule * 60;
        $now_time = (date('H') * 3600 + date('i') * 60 + date('s'));
        $current_start_time = intval($now_time / $second) * $second;
        $current_end_time = $current_start_time + $second;
        return $current_end_time - $now_time;
    }
    
    public static function EnsurePrevSessionExists($lottery_id, $rule) {
        $db = Database::getInstance();
        $sid = self::GetCurrentSessionID($rule);

        $check = $db->pdo_query_one("SELECT * FROM lottery_session WHERE sid = ? AND lid = ?", [$sid, $lottery_id]);

        if (!$check) {
            $codes = self::GenerateSessionCodes($sid);
            $db->insert("lottery_session", [
                "sid" => $sid,
                "lid" => $lottery_id,
                "status" => 1,
                "session_codes" => json_encode($codes),
                "create_time" => time()
            ]);
        }
    }
}