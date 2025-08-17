<?php
/**
 * API: /ajax/lottery-session-history.php
 * -------------------------------------------------------------
 * Trả về 10 phiên (session) gần nhất đã khóa cho 1 bàn (lid)
 *   - type  = 2  (phiên đã hoàn thành)
 *   - status= 0  (đã xử lý)
 *   - result <> ''
 * Response: JSON Array [{sid, result} ...] tối đa 10 phần tử
 * -------------------------------------------------------------
 * Query string: ?lid=9 (id của lottery bàn)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/LotterySessionModel.php';

header('Content-Type: application/json; charset=utf-8');

$lid   = intval($_GET['lid'] ?? 0);
if ($lid <= 0) {
    echo json_encode([]);   // bàn không hợp lệ → trả mảng rỗng
    exit;
}

$db    = Database::getInstance();
$rows  = $db->pdo_query(
    "SELECT sid, result
       FROM   lottery_session
       WHERE  lid = ?
         AND  type = 2
         AND  status = 0
         AND  result <> ''
       ORDER BY sid DESC
       LIMIT 10",
    $lid, 10
);

echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
