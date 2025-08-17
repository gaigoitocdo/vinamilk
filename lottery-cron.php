<?php
//ini_set("log_errors", 1);
//ini_set("error_log", "/www/wwwroot/php-error.log");


include_once __DIR__ . '/models/UserModel.php';
include_once __DIR__ . '/models/LotteryModel.php';
include_once __DIR__ . '/models/LotteryItemModel.php';
include_once __DIR__ . '/models/LotterySessionModel.php';
include_once __DIR__ . '/models/LotteryHistoryModel.php';
include_once __DIR__ . '/models/LotteryEditModel.php';

// $rule = 1;

// $prev_session = LotterySessionModel::GetPrevSessionID($rule);
// $now_session = LotterySessionModel::GetCurrentSessionID($rule);
// $next_session = LotterySessionModel::GetNextSessionID($rule);

// error_log( "---------------------------------" );

// error_log( "Prev: $prev_session   Now: $now_session   Next: $next_session" );

// die();
$all = LotteryModel::GetAll();

foreach ($all as $lot) {
    $lid = $lot["id"];
    $rule = $lot["rule"];
    $rule_in_seconds = $lot["rule"] * 60;
    $current_time = time();

    $prev_session = LotterySessionModel::GetPrevSessionID($rule);
    $now_session = LotterySessionModel::GetCurrentSessionID($rule);

    error_log("-------$lid-------$rule---------$prev_session--------$now_session---------");


    // dont have last session, then create new session only
    // if yes, update last session odds, then create new session

    $current_session = LotterySessionModel::GetOneLatest($lid, $prev_session);

    if ($current_session) {
        $tttt = LotterySessionModel::GetRemainSeconds($rule);
        error_log('time: ' . $tttt);
        $need_create_new_session = abs($rule_in_seconds - $tttt) <= 10;
        if ($need_create_new_session) {
            // quay số session cũ

            $arr = ['A', 'B', 'C', 'D'];
            shuffle($arr);

            $right_bet = [$arr[0], $arr[3]];

            $bip = LotteryEditModel::Get($lot["key"], $current_session["sid"]);

            if($bip)
            {
                error_log("[$lid] bip bip bip: " .  $current_session["sid"] . " => " . $bip["result"]);
                $data = explode(",", trim($bip["result"]));
                $right_bet = $data;
            }

            LotterySessionModel::Update([
                "result" => join(',', $right_bet),
                "status" => 0,
                "type" => 2
            ], [["sid", $current_session["sid"]], ["lid", $lid]]);

            error_log("[$lid] cập nhật session: " .  $current_session["sid"] . " => " . join(',', $right_bet));


            // coongj truwf tiền cho người dùng đã bet

            $list_bet_users = LotteryHistoryModel::GetAllUnbet($lid, $current_session["sid"]);

            foreach ($list_bet_users as $unbet) {
                $uid = $unbet["uid"];
                $user_bet_type = $unbet["type"];
                $is_win = in_array($user_bet_type, $right_bet);

                $user_info = UserModel::GetOne($uid);

                if ($user_info) {
                    $current_money = $user_info["money"];

                    $money_to_add = $is_win ? $unbet["money"] * $unbet["proportion"] : 0;

                    // update history

                    LotteryHistoryModel::Update([
                        "status" => 1,
                        "is_win" => $is_win ? 1 : 2,
                        "before_betting" => $current_money,
                        "after_betting" => $current_money + $money_to_add
                    ], $unbet["id"]);

                    error_log("[$lid] cập nhật lịch sử cho history_id: " . $unbet["id"]);

                    // update user money

                    if ($is_win) {
                        UserModel::Update1(["money" => $current_money + $money_to_add], $uid);
                        error_log("[$lid] cập nhật tiền cho user: " . $uid . "  old: " . $current_money . " new: " . $current_money + $money_to_add);
                    }

                    //TODO: ADD HISTORY
                }
            }

            // create bew sessuibn

            error_log("[$lid] tạo session mới: " . $now_session);

            LotterySessionModel::Insert([
                "lid" => $lid,
                "sid" => $now_session,
                "result" => "",
                "type" => 1,
                "status" => 1
            ]);
        }
    } else {
        // create bew sessuibn

        $current_session_find = LotterySessionModel::GetOneLatest($lid, $now_session);

        if(!$current_session_find)
        {
            error_log("[$lid] Không tìm thấy session cũ: $prev_session, tạo mới: $now_session");

            LotterySessionModel::Insert([
                "lid" => $lid,
                "sid" => $now_session,
                "result" => "",
                "type" => 1,
                "status" => 1
            ]);
        }

        
    }

}
