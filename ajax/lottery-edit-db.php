<?php

include_once __DIR__ . "/config/config.php";

include_once __DIR__ . "/models/LotterySessionModel.php";
include_once __DIR__ . "/models/LotteryModel.php";

$key = field("key");

$lottery_info = LotteryModel::GetByKey($key);

var_dump($lottery_info);

die();

$rule = 1;

$current = LotterySessionModel::GetCurrentSessionID($rule);

$s = LotterySessionModel::Get(6, $current);


for($i = 1; $i < 50; $i++)
{
    echo $current + $i . " - " . date("Y-m-d H:i:s", $s["create_time"] + $i * (60 * $rule))."<br>";
}
