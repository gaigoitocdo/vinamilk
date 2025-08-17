<?php
// ✅ ENHANCED: lottery-config.php - Configuration for 5-digit lottery system

class LotteryConfig {
    
    // ✅ Code generation settings
    const CODE_LENGTH = 5;
    const MIN_CODE = 10000;
    const MAX_CODE = 99999;
    const MAX_GENERATION_ATTEMPTS = 100;
    
    // ✅ Winning rules
    const DEFAULT_WINNERS_COUNT = 1; // Usually 1 winner per session
    const MAX_WINNERS_COUNT = 2;     // Maximum 2 winners per session
    const MIN_WINNERS_COUNT = 1;     // Minimum 1 winner per session
    
    // ✅ Brand configurations
    const BRANDS = [
        'TH' => [
            'name' => 'TH TRUE MILK',
            'key' => 'th-brand',
            'color' => '#0066CC',
            'image_prefix' => 'a',
            'products' => [
                'a' => 'SỮA CÔ GÁI HÀ LAN',
                'b' => 'SỮA NUTIFOOD', 
                'c' => 'SỮA MỘC CHÂU',
                'd' => 'SỮA OPTIMUM GOLD SỐ 2'
            ]
        ],
        'VINAMILK' => [
            'name' => 'VINAMILK',
            'key' => 'vinamilk-brand', 
            'color' => '#E53E3E',
            'image_prefix' => 'b',
            'products' => [
                'a' => 'SỮA MILO',
                'b' => 'SỮA ĐẬU NÀNH FAMI',
                'c' => 'SỮA DUTCH LADY', 
                'd' => 'SỮA ALPHA GOLD'
            ]
        ]
    ];
    
    // ✅ Validation patterns
    const RESULT_PATTERNS = [
        'single_code' => '/^\d{5}$/',
        'multiple_codes' => '/^\d{5}(,\d{5})+$/',
        'legacy_letters' => '/^[A-D](,[A-D])*$/i',
        'valid_codes' => '/^\d{5}(,\d{5})*$/'
    ];
    
    // ✅ Session settings
    const SESSION_CODE_CACHE_TIME = 3600; // 1 hour
    const RESULT_ANNOUNCEMENT_DELAY = 5;   // 5 seconds before session ends
    const AUTO_RESULT_GENERATION = true;   // Auto-generate results if admin doesn't set
    
    // ✅ Betting limits
    const MIN_BET_AMOUNT = 1;
    const MAX_BET_AMOUNT = 1000000;
    const MAX_SELECTIONS_PER_BET = 4; // Can bet on all 4 options
    
    // ✅ Payout settings
    const DEFAULT_PAYOUT_RATIO = 1.95;
    const HOUSE_EDGE = 0.05; // 5% house edge
    
    /**
     * Generate unique 5-digit codes for a session
     */
    public static function generateSessionCodes($session_id = null) {
        $codes = [];
        $attempts = 0;
        
        while (count($codes) < 4 && $attempts < self::MAX_GENERATION_ATTEMPTS) {
            $code = str_pad(rand(self::MIN_CODE, self::MAX_CODE), self::CODE_LENGTH, '0', STR_PAD_LEFT);
            
            if (!in_array($code, $codes)) {
                $codes[] = $code;
            }
            $attempts++;
        }
        
        if (count($codes) < 4) {
            throw new Exception("Failed to generate unique codes after " . self::MAX_GENERATION_ATTEMPTS . " attempts");
        }
        
        return [
            'a' => $codes[0],
            'b' => $codes[1], 
            'c' => $codes[2],
            'd' => $codes[3]
        ];
    }
    
    /**
     * Validate result format
     */
    public static function validateResult($result) {
        if (empty($result)) return false;
        
        $result = trim($result);
        
        // Check for valid 5-digit codes
        if (preg_match(self::RESULT_PATTERNS['valid_codes'], $result)) {
            $codes = explode(',', $result);
            
            // Check for duplicates
            if (count($codes) !== count(array_unique($codes))) {
                return false;
            }
            
            // Check winners count
            if (count($codes) > self::MAX_WINNERS_COUNT) {
                return false;
            }
            
            return true;
        }
        
        // Allow legacy format for backward compatibility
        if (preg_match(self::RESULT_PATTERNS['legacy_letters'], $result)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Convert legacy A,B,C,D format to 5-digit codes
     */
    public static function convertLegacyResult($legacy_result, $session_codes) {
        if (!$legacy_result || !$session_codes) {
            return $legacy_result;
        }
        
        // If already in new format, return as is
        if (preg_match(self::RESULT_PATTERNS['valid_codes'], $legacy_result)) {
            return $legacy_result;
        }
        
        // Convert A,B,C,D to codes
        if (preg_match(self::RESULT_PATTERNS['legacy_letters'], $legacy_result)) {
            $letters = array_map('trim', explode(',', strtolower($legacy_result)));
            $codes = [];
            
            foreach ($letters as $letter) {
                if (isset($session_codes[$letter])) {
                    $codes[] = $session_codes[$letter];
                }
            }
            
            return implode(',', $codes);
        }
        
        return $legacy_result;
    }
    
    /**
     * Get brand configuration
     */
    public static function getBrandConfig($brand_key) {
        $brand_key = strtoupper($brand_key);
        return self::BRANDS[$brand_key] ?? self::BRANDS['TH'];
    }
    
    /**
     * Get image path for brand option
     */
    public static function getImagePath($brand, $letter) {
        $config = self::getBrandConfig($brand);
        $letter_num = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
        $num = $letter_num[strtolower($letter)] ?? 1;
        
        return "/assets/image/{$config['image_prefix']}{$num}.jpg";
    }
    
    /**
     * Calculate payout amount
     */
    public static function calculatePayout($bet_amount, $payout_ratio = null) {
        $ratio = $payout_ratio ?? self::DEFAULT_PAYOUT_RATIO;
        return $bet_amount * $ratio;
    }
    
    /**
     * Generate random winners for auto-result
     */
    public static function generateRandomWinners($session_codes = null) {
        $options = ['a', 'b', 'c', 'd'];
        shuffle($options);
        
        $winners_count = rand(self::MIN_WINNERS_COUNT, self::MAX_WINNERS_COUNT);
        $winners = array_slice($options, 0, $winners_count);
        
        if ($session_codes) {
            $winning_codes = [];
            foreach ($winners as $winner) {
                if (isset($session_codes[$winner])) {
                    $winning_codes[] = $session_codes[$winner];
                }
            }
            return implode(',', $winning_codes);
        }
        
        return implode(',', $winners);
    }
    
    /**
     * Get winning statistics
     */
    public static function getWinningStats($lottery_id, $days = 7) {
        $db = Database::getInstance();
        
        $stats = $db->pdo_query("
            SELECT 
                DATE(FROM_UNIXTIME(ls.create_time)) as date,
                COUNT(*) as total_sessions,
                SUM(CASE WHEN ls.admin_edited = 1 THEN 1 ELSE 0 END) as admin_edited_sessions,
                SUM(CASE WHEN ls.result REGEXP '^[0-9]{5}(,[0-9]{5})*$' THEN 1 ELSE 0 END) as new_format_results
            FROM lottery_session ls
            WHERE ls.lid = ?
              AND ls.create_time >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL ? DAY))
              AND ls.result != ''
            GROUP BY DATE(FROM_UNIXTIME(ls.create_time))
            ORDER BY date DESC
        ", $lottery_id, $days);
        
        return $stats;
    }
    
    /**
     * Get system health check
     */
    public static function systemHealthCheck() {
        $db = Database::getInstance();
        
        $health = [
            'status' => 'ok',
            'checks' => []
        ];
        
        // Check session codes coverage
        $session_check = $db->pdo_query_one("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN session_codes IS NOT NULL AND session_codes != '{}' THEN 1 ELSE 0 END) as with_codes
            FROM lottery_session
            WHERE create_time >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 24 HOUR))
        ");
        
        $health['checks']['session_codes'] = [
            'status' => $session_check['with_codes'] == $session_check['total'] ? 'ok' : 'warning',
            'total' => $session_check['total'],
            'with_codes' => $session_check['with_codes']
        ];
        
        // Check result formats
        $result_check = $db->pdo_query_one("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN result REGEXP '^[0-9]{5}(,[0-9]{5})*$' THEN 1 ELSE 0 END) as new_format,
                SUM(CASE WHEN result REGEXP '^[A-D](,[A-D])*$' THEN 1 ELSE 0 END) as legacy_format
            FROM lottery_session
            WHERE result != '' 
              AND create_time >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 24 HOUR))
        ");
        
        $health['checks']['result_formats'] = [
            'status' => 'ok',
            'total' => $result_check['total'],
            'new_format' => $result_check['new_format'],
            'legacy_format' => $result_check['legacy_format']
        ];
        
        // Check for orphaned bets
        $orphan_check = $db->pdo_query_one("
            SELECT COUNT(*) as orphaned_bets
            FROM lottery_history lh
            LEFT JOIN lottery_session ls ON lh.lid = ls.lid AND lh.sid = ls.sid
            WHERE ls.id IS NULL
              AND lh.create_time >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 24 HOUR))
        ");
        
        $health['checks']['orphaned_bets'] = [
            'status' => $orphan_check['orphaned_bets'] == 0 ? 'ok' : 'error',
            'count' => $orphan_check['orphaned_bets']
        ];
        
        // Overall status
        foreach ($health['checks'] as $check) {
            if ($check['status'] === 'error') {
                $health['status'] = 'error';
                break;
            } elseif ($check['status'] === 'warning' && $health['status'] === 'ok') {
                $health['status'] = 'warning';
            }
        }
        
        return $health;
    }
    
    /**
     * Log lottery event
     */
    public static function logEvent($type, $data, $user_id = null) {
        $db = Database::getInstance();
        
        try {
            $db->insert('lottery_logs', [
                'event_type' => $type,
                'event_data' => json_encode($data),
                'user_id' => $user_id,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'created_at' => time()
            ]);
        } catch (Exception $e) {
            error_log("Failed to log lottery event: " . $e->getMessage());
        }
    }
}

// ✅ Initialize configuration constants
if (!defined('LOTTERY_CONFIG_LOADED')) {
    define('LOTTERY_CONFIG_LOADED', true);
    
    // Event types for logging
    define('LOTTERY_EVENT_SESSION_CREATED', 'session_created');
    define('LOTTERY_EVENT_RESULT_SET', 'result_set');
    define('LOTTERY_EVENT_BET_PLACED', 'bet_placed');
    define('LOTTERY_EVENT_PAYOUT_PROCESSED', 'payout_processed');
    define('LOTTERY_EVENT_ADMIN_EDIT', 'admin_edit');
    define('LOTTERY_EVENT_CODE_GENERATED', 'code_generated');
    
    // Cache keys
    define('CACHE_KEY_SESSION_CODES', 'lottery_session_codes_');
    define('CACHE_KEY_LOTTERY_CONFIG', 'lottery_config_');
    define('CACHE_KEY_WINNING_STATS', 'lottery_winning_stats_');
}
?>