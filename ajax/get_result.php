<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/LotteryModel.php';
require_once __DIR__ . '/../models/LotterySessionModel.php';

$rule = isset($_GET['rule']) ? intval($_GET['rule']) : 5;
$lottery_id = 1;

try {
    LotterySessionModel::EnsurePrevSessionExists($lottery_id, $rule);
    $session_id = LotterySessionModel::GetCurrentSessionID($rule);
    $result = LotteryModel::CalculateResult($session_id);

    echo json_encode([
        "success" => true,
        "rule" => $rule,
        "session_id" => $session_id,
        "result" => $result
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Lỗi xử lý: " . $e->getMessage()
    ]);
}
