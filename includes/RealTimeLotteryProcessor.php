<?php
/**
 * Enhanced Real-time Lottery Processor - COMPLETELY FIXED VERSION
 * File: includes/RealTimeLotteryProcessor.php
 */

class RealTimeLotteryProcessor {
    private static $cache = [];
    private static $processing = false;
    private static $lastProcessTime = 0;
    private static $lockTimeout = 30; // 30 seconds lock timeout
    
    /**
     * Initialize processor
     */
    public static function init() {
        // Clear any stuck locks on startup
        self::clearStuckLocks();
        
        // Set error handling
        set_error_handler([self::class, 'handleError']);
        
        // Register shutdown function
        register_shutdown_function([self::class, 'shutdown']);
    }
    
    /**
     * Process lottery in real-time with improved error handling
     */
    public static function processLottery($lottery_id, $force = false) {
        try {
            // Validate input
            if (!$lottery_id || !is_numeric($lottery_id)) {
                throw new Exception("Invalid lottery ID: " . $lottery_id);
            }
            
            // Check for concurrent processing with timeout
            if (self::$processing && !$force) {
                if ((time() - self::$lastProcessTime) > self::$lockTimeout) {
                    self::$processing = false; // Clear stuck lock
                    error_log("[LOTTERY] Cleared stuck processing lock");
                } else {
                    return self::createResponse(false, 'System is processing, please wait...');
                }
            }
            
            // Rate limiting - only process every 2 seconds (reduced from 3)
            if (!$force && (time() - self::$lastProcessTime) < 2) {
                return self::createResponse(false, 'Too many requests, please wait...');
            }
            
            // Set processing lock
            self::$processing = true;
            self::$lastProcessTime = time();
            
            // Get database connection with retry
            $db = self::getDatabaseConnection();
            
            // Get lottery info with validation
            $lottery = self::getLotteryInfo($lottery_id);
            
            // Get session information
            $session_info = self::getSessionInfo($lottery_id);
            
            // Process completed sessions if needed
            if ($session_info['remaining_seconds'] <= 10) {
                self::processCompletedSessions($lottery_id, $lottery['rule']);
            }
            
            // Update betting statistics
            $bet_stats = self::calculateBettingStats($lottery_id, $session_info['current_session']);
            
            // Update cache
            self::updateCache($lottery_id, [
                'current_session' => $session_info['current_session'],
                'remaining_seconds' => $session_info['remaining_seconds'],
                'bet_stats' => $bet_stats,
                'last_update' => time()
            ]);
            
            return self::createResponse(true, 'Success', [
                'current_session' => $session_info['current_session'],
                'remaining_seconds' => $session_info['remaining_seconds'],
                'bet_stats' => $bet_stats,
                'timestamp' => time()
            ]);
            
        } catch (Exception $e) {
            error_log("[LOTTERY ERROR] " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());
            return self::createResponse(false, 'System error: ' . $e->getMessage());
        } finally {
            self::$processing = false;
        }
    }
    
    /**
     * Get database connection with retry mechanism
     */
    private static function getDatabaseConnection($retries = 3) {
        $attempts = 0;
        while ($attempts < $retries) {
            try {
                $db = Database::getInstance();
                
                // Test connection
                $db->pdo_query_one("SELECT 1");
                return $db;
                
            } catch (Exception $e) {
                $attempts++;
                if ($attempts >= $retries) {
                    throw new Exception("Database connection failed after {$retries} attempts: " . $e->getMessage());
                }
                usleep(100000); // Wait 100ms before retry
            }
        }
    }
    
    /**
     * Get lottery info with validation
     */
    private static function getLotteryInfo($lottery_id) {
        try {
            $lottery = LotteryModel::Get($lottery_id);
            if (!$lottery) {
                throw new Exception("Lottery not found: " . $lottery_id);
            }
            
            // Validate lottery data
            if (empty($lottery['rule'])) {
                throw new Exception("Invalid lottery rule for ID: " . $lottery_id);
            }
            
            return $lottery;
            
        } catch (Exception $e) {
            throw new Exception("Failed to get lottery info: " . $e->getMessage());
        }
    }
    
    /**
     * Get session information with error handling
     */
    public static function getSessionInfo($lottery_id) {
        try {
            $lottery = self::getLotteryInfo($lottery_id);
            
            $current_session = LotterySessionModel::GetCurrentSessionID($lottery['rule']);
            $prev_session = LotterySessionModel::GetPrevSessionID($lottery['rule']);
            $remaining_seconds = LotterySessionModel::GetRemainSeconds($lottery['rule']);
            
            // Ensure current session exists
            self::ensureSessionExists($lottery_id, $current_session);
            
            return [
                'current_session' => $current_session,
                'prev_session' => $prev_session,
                'remaining_seconds' => max(0, $remaining_seconds) // Ensure non-negative
            ];
            
        } catch (Exception $e) {
            error_log("[SESSION INFO ERROR] " . $e->getMessage());
            return [
                'current_session' => date('YmdHi'),
                'prev_session' => date('YmdHi', strtotime('-1 minute')),
                'remaining_seconds' => 0
            ];
        }
    }
    
    /**
     * Ensure session exists in database
     */
    private static function ensureSessionExists($lottery_id, $session_id) {
        try {
            $session_data = LotterySessionModel::Get($lottery_id, $session_id);
            
            if (!$session_data) {
                // Create new session
                $session_data = [
                    'lid' => $lottery_id,
                    'sid' => $session_id,
                    'status' => 1, // Active
                    'type' => 1,   // Regular
                    'result' => '',
                    'create_time' => time()
                ];
                
                $result = LotterySessionModel::Insert($session_data);
                if (!$result) {
                    throw new Exception("Failed to create session: " . $session_id);
                }
                
                error_log("[LOTTERY] New session created: {$session_id}");
            }
            
        } catch (Exception $e) {
            error_log("[SESSION CREATE ERROR] " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Process completed sessions with improved error handling
     */
    private static function processCompletedSessions($lottery_id, $rule) {
        try {
            $db = self::getDatabaseConnection();
            
            // Get previous session
            $prev_session = LotterySessionModel::GetPrevSessionID($rule);
            $session_data = LotterySessionModel::Get($lottery_id, $prev_session);
            
            if ($session_data && empty($session_data['result'])) {
                // Start transaction
                $db->beginTransaction();
                
                try {
                    // Generate result for completed session
                    $result = self::generateResult($lottery_id, $prev_session);
                    
                    // Update session with result
                    $update_result = LotterySessionModel::Update([
                        'result' => $result,
                        'status' => 0, // Completed
                        'update_time' => time()
                    ], $session_data['id']);
                    
                    if (!$update_result) {
                        throw new Exception("Failed to update session result");
                    }
                    
                    // Process all bets for this session
                    self::processBets($lottery_id, $prev_session, $result);
                    
                    // Commit transaction
                    $db->commit();
                    
                    error_log("[LOTTERY] Session {$prev_session} completed with result: {$result}");
                    
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            }
            
        } catch (Exception $e) {
            error_log("[PROCESS COMPLETED ERROR] " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Generate lottery result with improved logic
     */
    private static function generateResult($lottery_id, $session_id) {
        try {
            // Check if there's a manual edit
            $edit_result = self::getEditResult($lottery_id, $session_id);
            if ($edit_result) {
                return $edit_result;
            }
            
            // Get betting statistics for this session
            $bet_stats = self::getBettingStatsForGeneration($lottery_id, $session_id);
            
            // Generate result based on betting patterns (house edge protection)
            $options = ['A', 'B', 'C', 'D'];
            $weights = [];
            
            foreach ($options as $option) {
                $bet_money = 0;
                
                if ($bet_stats && isset($bet_stats[$option])) {
                    $bet_money = $bet_stats[$option]['money'];
                }
                
                // Lower weight for heavily bet options (house edge)
                $weights[$option] = max(1, 100 - ($bet_money / 1000));
            }
            
            // Randomly select based on weights
            $result = self::weightedRandomSelect($weights);
            
            // Always return 2 results as per original logic
            $results = [$result];
            
            // Add second result (different from first)
            $remaining_options = array_diff($options, [$result]);
            if (!empty($remaining_options)) {
                $results[] = $remaining_options[array_rand($remaining_options)];
            } else {
                $results[] = $options[array_rand($options)];
            }
            
            return implode(',', $results);
            
        } catch (Exception $e) {
            error_log("[GENERATE RESULT ERROR] " . $e->getMessage());
            // Fallback to random result
            $options = ['A', 'B', 'C', 'D'];
            $result1 = $options[array_rand($options)];
            $remaining = array_diff($options, [$result1]);
            $result2 = $remaining[array_rand($remaining)];
            return $result1 . ',' . $result2;
        }
    }
    
    /**
     * Get betting statistics for result generation
     */
    private static function getBettingStatsForGeneration($lottery_id, $session_id) {
        try {
            $db = self::getDatabaseConnection();
            
            $stats = $db->pdo_query("
                SELECT type, COUNT(*) as count, SUM(money) as total_money 
                FROM lottery_history 
                WHERE lid = ? AND sid = ? AND status = 0
                GROUP BY type
            ", $lottery_id, $session_id);
            
            $result = [];
            if ($stats) {
                foreach ($stats as $stat) {
                    $result[$stat['type']] = [
                        'count' => (int)$stat['count'],
                        'money' => (float)$stat['total_money']
                    ];
                }
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("[BETTING STATS GENERATION ERROR] " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get manual edit result if exists
     */
    private static function getEditResult($lottery_id, $session_id) {
        try {
            $db = self::getDatabaseConnection();
            $lottery = self::getLotteryInfo($lottery_id);
            
            $edit = $db->pdo_query_one(
                "SELECT * FROM lottery_edit WHERE `key` = ? AND `session` = ?",
                $lottery['key'], $session_id
            );
            
            return $edit ? $edit['result'] : null;
            
        } catch (Exception $e) {
            error_log("[GET EDIT RESULT ERROR] " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Weighted random selection with improved logic
     */
    private static function weightedRandomSelect($weights) {
        try {
            $total = array_sum($weights);
            if ($total <= 0) {
                return array_keys($weights)[0];
            }
            
            $random = mt_rand(1, $total);
            
            $current = 0;
            foreach ($weights as $option => $weight) {
                $current += $weight;
                if ($random <= $current) {
                    return $option;
                }
            }
            
            return array_keys($weights)[0]; // Fallback
            
        } catch (Exception $e) {
            error_log("[WEIGHTED RANDOM ERROR] " . $e->getMessage());
            $keys = array_keys($weights);
            return $keys[array_rand($keys)];
        }
    }
    
    /**
     * Process all bets for a completed session
     */
    private static function processBets($lottery_id, $session_id, $result) {
        try {
            $db = self::getDatabaseConnection();
            
            // Get all unprocessed bets for this session
            $bets = LotteryHistoryModel::GetAllUnbet($lottery_id, $session_id);
            
            if (empty($bets)) {
                return; // No bets to process
            }
            
            $winning_options = explode(',', $result);
            $processed_count = 0;
            
            foreach ($bets as $bet) {
                try {
                    $is_win = in_array($bet['type'], $winning_options);
                    $win_amount = 0;
                    
                    if ($is_win) {
                        // Calculate winnings
                        $win_amount = $bet['money'] * floatval($bet['proportion']);
                        
                        // Update user balance
                        $user = UserModel::GetOne($bet['uid']);
                        if ($user) {
                            $new_balance = $user['money'] + $win_amount;
                            UserModel::Update($bet['uid'], [
                                'money' => $new_balance
                            ]);
                        }
                    }
                    
                    // Update bet record
                    $update_result = LotteryHistoryModel::Update([
                        'status' => 1,        // Processed
                        'is_win' => $is_win ? 1 : 2,  // 1 = win, 2 = loss
                        'win_amount' => $win_amount,
                        'update_time' => time()
                    ], $bet['id']);
                    
                    if ($update_result) {
                        $processed_count++;
                    }
                    
                } catch (Exception $e) {
                    error_log("[PROCESS BET ERROR] Bet ID {$bet['id']}: " . $e->getMessage());
                }
            }
            
            error_log("[LOTTERY] Processed {$processed_count} bets for session {$session_id}");
            
        } catch (Exception $e) {
            error_log("[PROCESS BETS ERROR] " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Calculate real-time betting statistics
     */
    private static function calculateBettingStats($lottery_id, $session_id) {
        try {
            $db = self::getDatabaseConnection();
            
            // Get betting statistics
            $stats = $db->pdo_query("
                SELECT type, COUNT(*) as count, SUM(money) as total_money 
                FROM lottery_history 
                WHERE lid = ? AND sid = ? AND status = 0
                GROUP BY type
            ", $lottery_id, $session_id);
            
            $total_bets = 0;
            $total_money = 0;
            $option_stats = [
                'A' => ['count' => 0, 'money' => 0],
                'B' => ['count' => 0, 'money' => 0],
                'C' => ['count' => 0, 'money' => 0],
                'D' => ['count' => 0, 'money' => 0]
            ];
            
            if ($stats) {
                foreach ($stats as $stat) {
                    $count = (int)$stat['count'];
                    $money = (float)$stat['total_money'];
                    
                    $total_bets += $count;
                    $total_money += $money;
                    
                    if (isset($option_stats[$stat['type']])) {
                        $option_stats[$stat['type']] = [
                            'count' => $count,
                            'money' => $money
                        ];
                    }
                }
            }
            
            // Calculate percentages
            foreach ($option_stats as $option => &$stats) {
                $stats['percentage'] = $total_bets > 0 ? 
                    round(($stats['count'] / $total_bets) * 100, 1) : 0;
            }
            
            return [
                'total_bets' => $total_bets,
                'total_money' => $total_money,
                'options' => $option_stats
            ];
            
        } catch (Exception $e) {
            error_log("[CALCULATE BETTING STATS ERROR] " . $e->getMessage());
            return [
                'total_bets' => 0,
                'total_money' => 0,
                'options' => [
                    'A' => ['count' => 0, 'money' => 0, 'percentage' => 0],
                    'B' => ['count' => 0, 'money' => 0, 'percentage' => 0],
                    'C' => ['count' => 0, 'money' => 0, 'percentage' => 0],
                    'D' => ['count' => 0, 'money' => 0, 'percentage' => 0]
                ]
            ];
        }
    }
    
    /**
     * Update cache
     */
    private static function updateCache($lottery_id, $data) {
        try {
            self::$cache[$lottery_id] = $data;
        } catch (Exception $e) {
            error_log("[UPDATE CACHE ERROR] " . $e->getMessage());
        }
    }
    
    /**
     * Get cache
     */
    public static function getLotteryCache($lottery_id) {
        return self::$cache[$lottery_id] ?? null;
    }
    
    /**
     * Clear cache
     */
    public static function clearCache($lottery_id = null) {
        if ($lottery_id) {
            unset(self::$cache[$lottery_id]);
        } else {
            self::$cache = [];
        }
    }
    
    /**
     * Get system status
     */
    public static function getStatus() {
        return [
            'processing' => self::$processing,
            'last_process_time' => self::$lastProcessTime,
            'cache_count' => count(self::$cache),
            'memory_usage' => memory_get_usage(true),
            'uptime' => time() - (self::$lastProcessTime ?: time())
        ];
    }
    
    /**
     * Health check
     */
    public static function healthCheck() {
        try {
            $db = self::getDatabaseConnection();
            
            return [
                'status' => 'healthy',
                'database' => 'connected',
                'processing' => self::$processing ? 'active' : 'idle',
                'cache_size' => count(self::$cache),
                'timestamp' => time()
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => time()
            ];
        }
    }
    
    /**
     * Process lottery on demand (lightweight version)
     */
    public static function processOnDemand($lottery_id) {
        try {
            if (!$lottery_id || !is_numeric($lottery_id)) {
                return self::createResponse(false, 'Invalid lottery ID');
            }
            
            $lottery = self::getLotteryInfo($lottery_id);
            $session_info = self::getSessionInfo($lottery_id);
            
            return self::createResponse(true, 'Success', [
                'current_session' => $session_info['current_session'],
                'remaining_seconds' => $session_info['remaining_seconds'],
                'processed' => true
            ]);
            
        } catch (Exception $e) {
            error_log("[PROCESS ON DEMAND ERROR] " . $e->getMessage());
            return self::createResponse(false, $e->getMessage());
        }
    }
    
    /**
     * Get betting statistics for display
     */
    public static function getBettingStats($lottery_id, $session_id) {
        try {
            $db = self::getDatabaseConnection();
            
            // Try to get from lottery_units table first
            try {
                $units = $db->pdo_query_one("
                    SELECT option_a, option_b, option_c, option_d 
                    FROM lottery_units 
                    WHERE session_id = ?
                ", $session_id);
                
                if ($units) {
                    return [
                        (int)$units['option_a'],
                        (int)$units['option_b'],
                        (int)$units['option_c'],
                        (int)$units['option_d']
                    ];
                }
            } catch (Exception $e) {
                // Table doesn't exist, continue with fallback
            }
            
            // Fallback: calculate from lottery_history
            $stats = $db->pdo_query("
                SELECT type, COUNT(*) as count 
                FROM lottery_history 
                WHERE lid = ? AND sid = ? AND status = 0
                GROUP BY type
            ", $lottery_id, $session_id);
            
            $result = [0, 0, 0, 0]; // A, B, C, D
            $mapping = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
            
            if ($stats) {
                foreach ($stats as $stat) {
                    if (isset($mapping[$stat['type']])) {
                        $result[$mapping[$stat['type']]] = (int)$stat['count'];
                    }
                }
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("[GET BETTING STATS ERROR] " . $e->getMessage());
            return [0, 0, 0, 0];
        }
    }
    
    /**
     * Create standardized response
     */
    private static function createResponse($success, $message, $data = null) {
        $response = [
            'success' => $success,
            'message' => $message,
            'timestamp' => time()
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        return $response;
    }
    
    /**
     * Handle errors
     */
    public static function handleError($severity, $message, $file, $line) {
        error_log("[LOTTERY PHP ERROR] {$message} in {$file} line {$line}");
        return true;
    }
    
    /**
     * Clear stuck processing locks
     */
    private static function clearStuckLocks() {
        if (self::$processing && (time() - self::$lastProcessTime) > self::$lockTimeout) {
            self::$processing = false;
            error_log("[LOTTERY] Cleared stuck processing lock on init");
        }
    }
    
    /**
     * Shutdown handler
     */
    public static function shutdown() {
        self::$processing = false;
        error_log("[LOTTERY] Processor shutdown completed");
    }
    
    /**
     * Force process a specific session
     */
    public static function forceProcessSession($lottery_id, $session_id) {
        try {
            $db = self::getDatabaseConnection();
            
            // Get session
            $session = LotterySessionModel::Get($lottery_id, $session_id);
            if (!$session) {
                return false;
            }
            
            // If already processed, return true
            if (!empty($session['result'])) {
                return true;
            }
            
            // Start transaction
            $db->beginTransaction();
            
            try {
                // Generate result
                $result = self::generateResult($lottery_id, $session_id);
                
                // Update session
                $update_result = LotterySessionModel::Update([
                    'result' => $result,
                    'status' => 0,
                    'update_time' => time()
                ], $session['id']);
                
                if (!$update_result) {
                    throw new Exception("Failed to update session");
                }
                
                // Process bets
                self::processBets($lottery_id, $session_id, $result);
                
                // Commit transaction
                $db->commit();
                
                return true;
                
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("[FORCE PROCESS SESSION ERROR] " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the processor
RealTimeLotteryProcessor::init();
?>