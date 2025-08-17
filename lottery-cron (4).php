<?php
// ✅ ENHANCED: lottery-cron.php with 5-digit code support

include_once __DIR__ . '/models/UserModel.php';
include_once __DIR__ . '/models/LotteryModel.php';
include_once __DIR__ . '/models/LotteryItemModel.php';
include_once __DIR__ . '/models/LotterySessionModel.php';
include_once __DIR__ . '/models/LotteryHistoryModel.php';
include_once __DIR__ . '/models/LotteryEditModel.php';

$all = LotteryModel::GetAll();

foreach ($all as $lot) {
    $lid = $lot["id"];
    $rule = $lot["rule"];
    $rule_in_seconds = $lot["rule"] * 60;
    $current_time = time();

    $prev_session = LotterySessionModel::GetPrevSessionID($rule);
    $now_session = LotterySessionModel::GetCurrentSessionID($rule);

    error_log("-------$lid-------$rule---------$prev_session--------$now_session---------");

    $current_session = LotterySessionModel::GetOneLatest($lid, $prev_session);

    if ($current_session) {
        $tttt = LotterySessionModel::GetRemainSeconds($rule);
        error_log('time: ' . $tttt);
        $need_create_new_session = abs($rule_in_seconds - $tttt) <= 10;
        
        if ($need_create_new_session) {
            // ✅ ENHANCED: Generate 5-digit codes as winners
            $session_codes = LotterySessionModel::GetSessionCodes($lid, $current_session["sid"]);
            
            // Check for admin edit first
            $admin_edit = LotteryEditModel::Get($lot["key"], $current_session["sid"]);
            $winning_codes = [];
            
            if ($admin_edit && !empty($admin_edit["result"])) {
                // Use admin-specified result
                error_log("[$lid] Admin result found: " . $admin_edit["result"]);
                
                if (preg_match('/^\d{5}(,\d{5})*$/', $admin_edit["result"])) {
                    // Already 5-digit codes
                    $winning_codes = array_map('trim', explode(',', $admin_edit["result"]));
                } else if (preg_match('/^[A-D](,[A-D])*$/i', $admin_edit["result"])) {
                    // Convert legacy A,B,C,D to codes
                    $letters = array_map('trim', explode(',', strtolower($admin_edit["result"])));
                    foreach ($letters as $letter) {
                        if (isset($session_codes[$letter])) {
                            $winning_codes[] = $session_codes[$letter];
                        }
                    }
                } else {
                    error_log("[$lid] Invalid admin result format: " . $admin_edit["result"]);
                    // Fallback to random
                    $options = ['a', 'b', 'c', 'd'];
                    shuffle($options);
                    $winners = array_slice($options, 0, rand(1, 2));
                    foreach ($winners as $winner) {
                        $winning_codes[] = $session_codes[$winner];
                    }
                }
            } else {
                // Generate random winners
                $options = ['a', 'b', 'c', 'd'];
                shuffle($options);
                $winners = array_slice($options, 0, rand(1, 2)); // 1-2 winners
                
                foreach ($winners as $winner) {
                    $winning_codes[] = $session_codes[$winner];
                }
                
                error_log("[$lid] Random winners: " . implode(',', $winners) . " => " . implode(',', $winning_codes));
            }

            // Update session with winning codes
            $final_result = implode(',', $winning_codes);
            LotterySessionModel::Update([
                "result" => $final_result,
                "status" => 0,
                "type" => 2
            ], [["sid", $current_session["sid"]], ["lid", $lid]]);

            error_log("[$lid] Updated session: " . $current_session["sid"] . " => " . $final_result);

            // ✅ ENHANCED: Process bets with 5-digit code logic
            $list_bet_users = LotteryHistoryModel::GetAllUnbet($lid, $current_session["sid"]);

            foreach ($list_bet_users as $unbet) {
                $uid = $unbet["uid"];
                $user_bet_type = $unbet["type"]; // This might be A,B,C,D or 5-digit code
                
                // Convert user bet to 5-digit code for comparison
                $user_bet_code = null;
                if (preg_match('/^\d{5}$/', $user_bet_type)) {
                    // Already a 5-digit code
                    $user_bet_code = $user_bet_type;
                } else if (preg_match('/^[A-D]$/i', $user_bet_type)) {
                    // Convert A,B,C,D to code
                    $letter = strtolower($user_bet_type);
                    if (isset($session_codes[$letter])) {
                        $user_bet_code = $session_codes[$letter];
                    }
                }
                
                // Check if user won
                $is_win = false;
                if ($user_bet_code) {
                    $is_win = in_array($user_bet_code, $winning_codes);
                }

                $user_info = UserModel::GetOne($uid);

                if ($user_info) {
                    $current_money = $user_info["money"];
                    $money_to_add = $is_win ? $unbet["money"] * $unbet["proportion"] : 0;

                    // ✅ ENHANCED: Update history with winning code
                    LotteryHistoryModel::Update([
                        "status" => 1,
                        "is_win" => $is_win ? 1 : 2,
                        "before_betting" => $current_money,
                        "after_betting" => $current_money + $money_to_add,
                        "winning_code" => $final_result, // Store the winning code(s)
                        "user_bet_code" => $user_bet_code // Store what user actually bet
                    ], $unbet["id"]);

                    error_log("[$lid] Updated history for history_id: " . $unbet["id"] . 
                             " | User bet: $user_bet_code | Winners: $final_result | Win: " . ($is_win ? 'YES' : 'NO'));

                    // Update user money if won
                    if ($is_win) {
                        UserModel::Update1(["money" => $current_money + $money_to_add], $uid);
                        error_log("[$lid] Paid user: $uid | old: $current_money | new: " . ($current_money + $money_to_add));
                    }
                }
            }

            // Create new session with fresh codes
            error_log("[$lid] Creating new session: " . $now_session);
            
            $new_codes = LotterySessionModel::GenerateSessionCodes($now_session);
            LotterySessionModel::Insert([
                "lid" => $lid,
                "sid" => $now_session,
                "result" => "",
                "type" => 1,
                "status" => 1,
                "session_codes" => json_encode($new_codes)
            ]);
            
            error_log("[$lid] New session codes: " . json_encode($new_codes));
        }
    } else {
        // Create new session if none exists
        $current_session_find = LotterySessionModel::GetOneLatest($lid, $now_session);

        if (!$current_session_find) {
            error_log("[$lid] No previous session found: $prev_session, creating new: $now_session");

            $codes = LotterySessionModel::GenerateSessionCodes($now_session);
            LotterySessionModel::Insert([
                "lid" => $lid,
                "sid" => $now_session,
                "result" => "",
                "type" => 1,
                "status" => 1,
                "session_codes" => json_encode($codes)
            ]);
            
            error_log("[$lid] Initial session codes: " . json_encode($codes));
        }
    }
}

error_log("✅ Enhanced lottery cron completed with 5-digit code support");
?>