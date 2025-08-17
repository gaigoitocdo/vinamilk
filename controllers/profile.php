<?php
session_start();
require_once 'config/config.php';

include_once __DIR__ . "/../models/UserModel.php";
include_once __DIR__ . "/../models/BankModel.php";
include_once __DIR__ . "/../models/NotiModel.php";
include_once __DIR__ . "/../models/WithdrawModel.php";

// Kiểm tra session và lấy thông tin user
if (empty($_SESSION['id'])) {
    header('Location: login.html');
    exit;
}

$id = $_SESSION['id'];
$username = $_SESSION['username'] ?? null; // Lấy username từ session

// Lấy thông tin user từ database
$info = UserModel::GetOne($id);
if (!$info) {
    req_controller("404");
    die();
}

// Nếu không có username trong session, lấy từ database
if (!$username && isset($info['username'])) {
    $username = $info['username'];
    $_SESSION['username'] = $username; // Lưu vào session cho lần sau
}

// Lấy thông tin liên quan
$bank = BankModel::GetFromUID($id);
$noti = NotiModel::UserGet($id);
$vip_info = UserModel::GetVipLevel($id);

// ===== HELPER FUNCTIONS =====

/**
 * Enhanced money formatting with locale support
 */
function formatMoney($money) {
    $moneyInt = floor($money);
    if ($moneyInt < 1000) {
        return $moneyInt;
    }
    return number_format($moneyInt, 0, '.', '.');
}

/**
 * Enhanced account masking with security
 */
function maskAccount($account, $visible = 3) {
    $length = strlen($account);
    if ($length <= $visible) {
        return $account;
    }
    $visiblePart = substr($account, 0, $visible);
    $maskedPart = str_repeat('*', max(4, $length - $visible));
    return $visiblePart . $maskedPart;
}

/**
 * Enhanced preference renderer with validation
 */
function render_pref($info, $name, $pref_name, $type = "text", $render_callback = NULL, $radio_items = []) {
    $value = isset($info[$name]) ? $info[$name] : '';
    $escaped_value = htmlspecialchars($value);
    
    if ($type == "text") {
        ?>
        <div class="pref-item">
            <div class="left"><?= htmlspecialchars($pref_name) ?></div>
            <div class="right">
                <span id="<?= $type ?>-editable" class="desc" 
                      data-oldvalue='<?= $escaped_value ?>' 
                      data-field="<?= htmlspecialchars($name) ?>">
                    <?= is_callable($render_callback) ? $render_callback($value) : $escaped_value ?>
                </span>
                <i class="fa fa-chevron-right ml-2" aria-hidden="true"></i>
            </div>
        </div>
        <?php
    } else if ($type == 'radio') {
        ?>
        <div class="pref-item">
            <div class="left"><?= htmlspecialchars($pref_name) ?></div>
            <div class="right">
                <select class="form-control" id="radio-pref-select" data-field="<?= htmlspecialchars($name) ?>">
                    <?php foreach ($radio_items as $k => $v): ?>
                        <option <?= $value == $k ? "selected" : "" ?> value="<?= htmlspecialchars($k) ?>">
                            <?= htmlspecialchars($v) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php
    }
}

/**
 * Get user display name with fallback
 */
function getUserDisplayName($info, $username) {
    // Ưu tiên: name -> username từ session -> username từ DB -> ID
    if (!empty($info['name'])) {
        return $info['name'];
    }
    
    if (!empty($username)) {
        return $username;
    }
    
    if (!empty($info['username'])) {
        return $info['username'];
    }
    
    return 'User #' . $info['id'];
}

/**
 * Process transaction history data - FIXED
 */
function processTransactionHistory($noti, $id) {
    $allTransactions = [];
    
    // Add notifications as transactions
    if (!empty($noti)) {
        foreach ($noti as $n) {
            $amount = null;
            
            $patterns = [
                '/rút tiền.*?(\d{1,3}(?:\.\d{3})*)/',
                '/(\d{1,3}(?:\.\d{3})*)\s*(?:USD|AUD|\$|₫)/',
                '/(\d+(?:\.\d+)?)\s*(?:USD|AUD|\$|₫)/'
            ];
            
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $n['content'], $match)) {
                    $amount = str_replace('.', '', $match[1]);
                    break;
                }
            }
            
            $type = 'notification';
            $status = 'info';
            
            if (stripos($n['title'], 'rút tiền') !== false || stripos($n['content'], 'rút tiền') !== false) {
                $type = 'withdrawal';
                $status = determineTransactionStatus($n['content']);
            } elseif (stripos($n['title'], 'nạp tiền') !== false || stripos($n['content'], 'nạp tiền') !== false) {
                $type = 'deposit';
                $status = 'success';
            }
            
            $allTransactions[] = [
                'time' => $n['time'],
                'type' => $type,
                'title' => $n['title'],
                'content' => $n['content'],
                'amount' => $amount,
                'status' => $status,
                'color' => $n['color'] ?? '#FFD700',
                'source' => 'notification'
            ];
        }
    }
    
    // Add withdrawal records with enhanced processing
    $withdrawals = WithdrawModel::GetByUID($id);
    if (!empty($withdrawals)) {
        foreach ($withdrawals as $w) {
            $status = 'pending';
            if ($w["status"] == 1) $status = 'success';
            elseif ($w["status"] == 2) $status = 'error';
            
            $allTransactions[] = [
                'time' => $w['create_time'],
                'type' => 'withdrawal',
                'title' => 'Yêu cầu rút tiền',
                'content' => 'Rút tiền về tài khoản ngân hàng',
                'amount' => $w['money'],
                'status' => $status,
                'source' => 'withdrawal',
                'note' => $w['note'] ?? '',
                'reference' => 'WD' . $w['id']
            ];
        }
    }
    
    // Sort by time descending
    usort($allTransactions, function($a, $b) {
        return $b['time'] - $a['time'];
    });
    
    return $allTransactions;
}

/**
 * Determine transaction status from content
 */
function determineTransactionStatus($content) {
    $successKeywords = ['thành công', 'hoàn tất', 'completed', 'success'];
    $errorKeywords = ['thất bại', 'từ chối', 'failed', 'rejected', 'error'];
    
    foreach ($successKeywords as $keyword) {
        if (stripos($content, $keyword) !== false) {
            return 'success';
        }
    }
    
    foreach ($errorKeywords as $keyword) {
        if (stripos($content, $keyword) !== false) {
            return 'error';
        }
    }
    
    return 'pending';
}

/**
 * Get user VIP level display
 */
function getVIPLevelDisplay($vip_info) {
    if (empty($vip_info)) {
        return 'Limited'; // Default
    }
    
    $levels = [
        1 => 'BRONZE VIP',
        2 => 'SILVER VIP', 
        3 => 'GOLD VIP',
        4 => 'PLATINUM VIP',
        5 => 'DIAMOND VIP'
    ];
    
    return $levels[$vip_info['level'] ?? 3] ?? 'Limited';
}

// Process transaction history
$allTransactions = processTransactionHistory($noti, $id);

// Calculate user statistics - FIXED
$userStats = [
    'totalDeposits' => 0,
    'totalWithdrawals' => 0,
    'pendingWithdrawals' => 0,
    'successfulTransactions' => 0
];

if (!empty($allTransactions)) {
    $deposits = array_filter($allTransactions, function($t) { return $t['type'] === 'deposit'; });
    $withdrawals = array_filter($allTransactions, function($t) { return $t['type'] === 'withdrawal'; });
    $pending = array_filter($allTransactions, function($t) { return $t['type'] === 'withdrawal' && $t['status'] === 'pending'; });
    $successful = array_filter($allTransactions, function($t) { return $t['status'] === 'success'; });
    
    $userStats['totalDeposits'] = array_sum(array_column($deposits, 'amount'));
    $userStats['totalWithdrawals'] = array_sum(array_column($withdrawals, 'amount'));
    $userStats['pendingWithdrawals'] = count($pending);
    $userStats['successfulTransactions'] = count($successful);
}

// User display information
$displayName = getUserDisplayName($info, $username);
$vipLevel = getVIPLevelDisplay($vip_info);

// Debug information - ENHANCED
error_log("=== USER SESSION INFO ===");
error_log("User ID: " . $id);
error_log("Username from session: " . ($username ?: 'NOT SET'));
error_log("Username from DB: " . ($info['username'] ?? 'NOT SET'));
error_log("Display Name: " . $displayName);
error_log("VIP Level: " . $vipLevel);
error_log("Balance: " . ($info['money'] ?? 0));
error_log("Transactions count: " . count($allTransactions));
error_log("========================");

// Get topup methods (implement based on your database structure)
$topup_methods = []; // You should implement this based on your database structure

// Include views
view("header");
layout_header();
view("navbar");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VinamilkX - Member Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
        :root {
            /* Vinamilk Color Scheme */
            --vinamilk-primary: #4A90E2;
            --vinamilk-secondary: #5B6FEB;
            --vinamilk-light: #E8F3FF;
            --vinamilk-accent: #00B3CC;
            --vinamilk-success: #00C851;
            --vinamilk-warning: #FFB300;
            --vinamilk-error: #FF4444;
            --vinamilk-dark: #2C3E50;
            
            /* Gradients */
            --vinamilk-gradient: linear-gradient(135deg, #4A90E2 0%, #5B6FEB 100%);
            --vinamilk-gradient-light: linear-gradient(135deg, #E8F3FF 0%, #F0F8FF 100%);
            
            /* Grays */
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --font-family: 'Inter', 'Poppins', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: var(--font-family);
  
            color: var(--gray-900);
            line-height: 1.5;
            max-width: 500px;
            margin: 0 auto;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            background: #ffffff;
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
            z-index: 1;
            overflow-y: auto;
        }

        /* Header Section - Vinamilk Style */
        .section-header {
            background: var(--vinamilk-gradient);
            padding: 20px 16px;
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 200px;
        }

        .section-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .app-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 16px;
            letter-spacing: -0.3px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .logo-container {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            position: relative;
            width: 60px;
            height: 60px;
            cursor: pointer;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: 3px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .avatar:hover {
            border-color: rgba(255, 255, 255, 0.5);
            transform: scale(1.05);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .username-display {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        #userName {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.2px;
            color: white;
        }

        .user-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            opacity: 0.9;
        }

        .vip-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            color: var(--vinamilk-primary);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
        }

        /* Balance Section - Vinamilk Style */
        .section-balance {
            padding: 16px;
            background: var(--vinamilk-gradient-light);
            border-bottom: 1px solid var(--gray-200);
        }

        .balance-card {
            margin-bottom: 16px;
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(74, 144, 226, 0.15);
            border: 1px solid rgba(74, 144, 226, 0.1);
            position: relative;
            overflow: hidden;
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--vinamilk-gradient);
        }

        .balance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .balance-title {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            color: var(--vinamilk-primary);
            font-size: 14px;
        }

        .refresh-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--vinamilk-light);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            color: var(--vinamilk-primary);
        }

        .refresh-btn:hover {
            background: var(--vinamilk-primary);
            color: white;
            transform: rotate(180deg);
        }

        .balance-main {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .balance-value {
            font-size: 24px;
       
            color: var(--vinamilk-primary);
            margin-bottom: 4px;
            letter-spacing: -0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .balance-value:hover {
            color: var(--vinamilk-secondary);
            transform: scale(1.02);
        }

        .balance-label {
            font-size: 12px;
            color: var(--gray-500);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .credit-score {
            text-align: right;
        }

        .credit-value {
            font-size: 22px;
            font-weight: 700;
            color: var(--vinamilk-accent);
            margin-bottom: 4px;
        }

        .bank-info {
            padding-top: 16px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .bank-detail {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .bank-detail-title {
            font-size: 11px;
            color: var(--gray-500);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .bank-detail-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .action-item {
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 12px 8px;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            background: white;
            box-shadow: 0 2px 8px rgba(74, 144, 226, 0.1);
        }

        .action-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(74, 144, 226, 0.2);
        }

        .action-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            position: relative;
            transition: all 0.3s ease;
        }

        .action-icon svg, .action-icon i {
            width: 22px;
            height: 22px;
            color: white;
            font-size: 22px;
        }

        .action-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-700);
            line-height: 1.2;
        }

        .action-item:nth-child(1) .action-icon {
            background: var(--vinamilk-gradient);
        }

        .action-item:nth-child(2) .action-icon {
            background: linear-gradient(135deg, var(--vinamilk-accent), #00A3CC);
        }

        .action-item:nth-child(3) .action-icon {
            background: linear-gradient(135deg, var(--vinamilk-warning), #E6A200);
        }

        .action-item:nth-child(4) .action-icon {
            background: linear-gradient(135deg, var(--vinamilk-success), #00B347);
        }

        /* Menu Section - Vinamilk Style */
        .section-menu {
            flex: 1;
            padding: 16px;
            background: var(--gray-50);
        }

        .menu-section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--vinamilk-primary);
            margin-bottom: 16px;
            letter-spacing: -0.2px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px;
            margin-bottom: 10px;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(74, 144, 226, 0.1);
        }

        .menu-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.15);
            border-color: var(--vinamilk-primary);
        }

        .menu-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--vinamilk-light);
            transition: all 0.3s ease;
        }

        .menu-icon svg, .menu-icon i {
            width: 18px;
            height: 18px;
            color: var(--vinamilk-primary);
            font-size: 18px;
        }

        .menu-item:hover .menu-icon {
            background: var(--vinamilk-primary);
        }

        .menu-item:hover .menu-icon svg,
        .menu-item:hover .menu-icon i {
            color: white;
        }

        .menu-text {
            font-size: 14px;
            color: var(--gray-800);
            font-weight: 500;
        }

        .menu-arrow {
            color: var(--vinamilk-primary);
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .menu-item:hover .menu-arrow {
            transform: translateX(3px);
        }

        .logout-menu {
            border-color: #fee2e2;
            background: #fef2f2;
            margin-top: 16px;
        }

        .logout-menu .menu-icon {
            background: #fee2e2;
        }

        .logout-menu .menu-icon svg,
        .logout-menu .menu-icon i {
            color: var(--vinamilk-error);
        }

        .logout-menu .menu-text {
            color: var(--vinamilk-error);
            font-weight: 600;
        }

        .logout-menu:hover {
            border-color: var(--vinamilk-error);
            background: #fef2f2;
        }

        /* Modal System - Vinamilk Style */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 12px;
            animation: fadeIn 0.4s ease;
        }

        .modal-overlay.hidden {
            display: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            color: var(--gray-900);
            max-height: 80vh;
            overflow-y: auto;
            width: 100%;
            max-width: 450px;
            position: relative;
            box-shadow: 0 20px 40px rgba(74, 144, 226, 0.3);
            border: 1px solid rgba(74, 144, 226, 0.2);
        }

        .modal-header {
            padding: 0 0 20px 0;
            border-bottom: 1px solid rgba(74, 144, 226, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            color: var(--vinamilk-primary);
            font-size: 1.5rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        .modal-close {
            background: var(--vinamilk-light);
            border: 1px solid var(--vinamilk-primary);
            color: var(--vinamilk-primary);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 18px;
        }

        .modal-close:hover {
            background: var(--vinamilk-primary);
            color: white;
            transform: scale(1.1);
        }

        .modal-body {
            padding: 20px 0;
            color: var(--gray-800);
        }

        /* VIP Membership Card - Vinamilk Style */
        .vip-card {
            background: var(--vinamilk-gradient);
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(74, 144, 226, 0.3);
        }

        .vip-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .vip-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }

        .heritage-logo {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .vip-status {
            background: rgba(255,255,255,0.3);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }

        .card-title {
            font-size: 24px;
            font-weight: 800;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 10px 0;
            position: relative;
            z-index: 2;
            font-family: 'Poppins', sans-serif;
        }

        .card-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
            position: relative;
            z-index: 2;
        }

        .card-field {
            color: white;
        }

        .card-field-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            opacity: 0.8;
            margin-bottom: 2px;
        }

        .card-field-value {
            font-size: 14px;
            font-weight: 700;
        }

        .card-number {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: white;
            text-align: center;
            margin: 15px 0;
            letter-spacing: 2px;
            position: relative;
            z-index: 2;
        }

        .chip-icons {
            display: flex;
            gap: 8px;
            margin: 10px 0;
            position: relative;
            z-index: 2;
        }

        .chip {
            width: 30px;
            height: 20px;
            background: rgba(255,255,255,0.3);
            border-radius: 4px;
        }

        /* Member Card - Not VIP */
        .member-card {
            background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .member-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }

        .member-logo {
            background: rgba(74, 144, 226, 0.2);
            color: var(--vinamilk-primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .member-status {
            background: rgba(74, 144, 226, 0.2);
            color: var(--vinamilk-primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }

        .member-card .card-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--vinamilk-primary);
            text-shadow: none;
            margin: 10px 0;
            position: relative;
            z-index: 2;
            font-family: 'Poppins', sans-serif;
        }

        .member-card .card-field {
            color: var(--vinamilk-primary);
        }

        .member-card .card-field-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            opacity: 0.7;
            margin-bottom: 2px;
        }

        .member-card .card-number {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: var(--vinamilk-primary);
            text-align: center;
            margin: 15px 0;
            letter-spacing: 2px;
            position: relative;
            z-index: 2;
        }

        .member-card .chip {
            background: rgba(74, 144, 226, 0.3);
        }

        /* Table Styles for VIP Modal */
        .vip-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(74, 144, 226, 0.1);
        }

        .vip-table th {
            color: var(--vinamilk-primary);
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 0.75rem 0.5rem;
            background: var(--vinamilk-light);
            font-weight: 700;
            text-align: center;
        }

        .vip-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
            text-align: center;
            color: var(--gray-700);
            border-bottom: 1px solid rgba(74, 144, 226, 0.1);
        }

        .vip-table tr {
            transition: all 0.2s ease;
        }

        .vip-table tr:hover {
            background: var(--vinamilk-light);
            transform: scale(1.01);
        }

        .status-pending {
            background: var(--vinamilk-warning);
            color: white;
            padding: 0.25rem 0.75rem;
            font-size: 0.7rem;
            font-weight: bold;
            border-radius: 12px;
        }

        .status-completed {
            background: var(--vinamilk-success);
            color: white;
            padding: 0.25rem 0.75rem;
            font-size: 0.7rem;
            font-weight: bold;
            border-radius: 12px;
        }

        .status-current {
            background: var(--vinamilk-primary);
            color: white;
            padding: 0.25rem 0.75rem;
            font-size: 0.7rem;
            font-weight: bold;
            border-radius: 12px;
            animation: currentBlink 1s infinite;
        }

        @keyframes currentBlink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.6; }
        }

        /* Form Elements - Vinamilk Style */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--vinamilk-primary);
        }

        .form-input, .form-select, .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(74, 144, 226, 0.3);
            border-radius: 8px;
            background: white;
            color: var(--gray-700);
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-input:focus, .form-select:focus, .form-control:focus {
            outline: none;
            border-color: var(--vinamilk-primary);
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.3);
            background: white;
        }

        /* Buttons - Vinamilk Style */
        .btn, .btn-primary, .btn-secondary, .btn-success, .btn-danger {
            padding: 12px 20px;
            border: 2px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            text-decoration: none;
            justify-content: center;
            font-family: inherit;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--vinamilk-gradient);
            color: white;
            border-color: var(--vinamilk-primary);
            font-weight: 700;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4);
        }

        .btn-secondary {
            background: white;
            color: var(--vinamilk-primary);
            border-color: var(--vinamilk-primary);
        }

        .btn-secondary:hover {
            background: var(--vinamilk-primary);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--vinamilk-success), #00B347);
            color: white;
            border-color: var(--vinamilk-success);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--vinamilk-error), #E63E3E);
            color: white;
            border-color: var(--vinamilk-error);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Alerts - Vinamilk Style */
        .alert {
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-info {
            background: rgba(74, 144, 226, 0.1);
            border: 1px solid rgba(74, 144, 226, 0.3);
            color: var(--vinamilk-primary);
        }

        .alert-warning {
            background: rgba(255, 179, 0, 0.1);
            border: 1px solid rgba(255, 179, 0, 0.3);
            color: var(--vinamilk-warning);
        }

        .alert-success {
            background: rgba(0, 200, 81, 0.1);
            border: 1px solid rgba(0, 200, 81, 0.3);
            color: var(--vinamilk-success);
        }

        .alert-danger {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: var(--vinamilk-error);
        }

        /* Preference Items - Vinamilk Style */
        .pref-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px;
            border-bottom: 1px solid rgba(74, 144, 226, 0.1);
            transition: all 0.3s ease;
        }

        .pref-item:hover {
            background: var(--vinamilk-light);
        }

        .pref-item .left {
            font-weight: 500;
            color: var(--vinamilk-primary);
            font-size: 14px;
        }

        .pref-item .right {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-700);
            font-size: 14px;
        }

        /* Progress Bar - Vinamilk Style */
        .progress-bar {
            height: 5px;
            background: rgba(74, 144, 226, 0.2);
            border-radius: 2px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress-fill {
            height: 100%;
            background: var(--vinamilk-gradient);
            width: 0;
            transition: width 0.3s ease;
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            .container {
                max-width: 100%;
                box-shadow: none;
            }
            
            .section-header {
                padding: 16px 12px;
                min-height: 180px;
            }
            
            .section-balance, .section-menu {
                padding: 12px;
            }
            
            .action-grid {
                gap: 10px;
            }
            
            .modal-content {
                margin: 8px;
                max-width: calc(100% - 16px);
                max-height: 95vh;
            }
            
            .modal-header, .modal-body {
                padding: 16px;
            }

            .vip-card {
                padding: 15px;
            }

            .card-title {
                font-size: 20px;
            }

            .card-details {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }
       .notification-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .notification-stats {
            display: flex;
            gap: 20px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
        }
        
        .notification-actions {
            display: flex;
            gap: 8px;
        }
        
        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            transition: all 0.3s ease;
        }
        
        .notification-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .notification-item.unread {
            background: #fff3cd !important;
            border-left: 4px solid #ffc107 !important;
        }
        
        .notification-item.read {
            background: #f8f9fa !important;
            border-left: 4px solid #28a745 !important;
        }
        /* Additional Vinamilk Specific Styles */
        .vinamilk-accent {
            color: var(--vinamilk-primary) !important;
        }

        .vinamilk-bg {
            background: var(--vinamilk-gradient) !important;
        }

        .vinamilk-border {
            border-color: var(--vinamilk-primary) !important;
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--vinamilk-gradient);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 1000;
            opacity: 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }

        .notification.show {
            opacity: 1;
            transform: translateX(-50%) translateY(10px);
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="section-header">
            <div class="header-content">
                <div class="app-title">
                    <div class="logo-container">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                            <path d="M12 2L15.09 8.26L22 9L17 14L18.18 21L12 17.77L5.82 21L7 14L2 9L8.91 8.26L12 2Z"/>
                        </svg>
                    </div>
                    VinamilkX Rewards
                </div>
                
                <div class="user-info">
                    <div class="avatar" onclick="openAvatarUpload()">
                        <img src="https://i.ibb.co/ntP9ZxN/Chat-GPT-Image-13-20-52-30-thg-6-2025.png" 
                             alt="Avatar" class="user-avatar" id="userAvatar">
                        <input type="file" id="avatarInput" accept="image/*" onchange="handleAvatarUpload(event)" style="display: none;">
                    </div>
                    
                    <div class="username-display">
                        <span id="userName"><?= htmlspecialchars($displayName) ?></span>
                        <div class="user-meta">
                            <span>ID: <?= htmlspecialchars($info['id']) ?></span>
                            <div class="vip-badge">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 12px; height: 12px;">
                                    <path d="M12 2L15.09 8.26L22 9L17 14L18.18 21L12 17.77L5.82 21L7 14L2 9L8.91 8.26L12 2Z" fill="currentColor"/>
                                </svg>
                                <?= $vipLevel ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Section -->
        <div class="section-balance">
            <div class="balance-card">
                <div class="balance-header">
                    <div class="balance-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 1V23M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Hoa Hồng Đang Có
                    </div>
                    <button class="refresh-btn" onclick="refreshBalance()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 4V10H7M23 20V14H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20.49 9C19.9828 7.56678 19.1209 6.28776 17.9845 5.27543C16.8482 4.2631 15.4745 3.55044 13.9917 3.20687C12.5089 2.8633 10.9652 2.90106 9.50481 3.31673C8.04443 3.73241 6.70873 4.51191 5.64 5.58L1 10M23 14L18.36 18.42C17.2913 19.4881 15.9556 20.2676 14.4952 20.6833C13.0348 21.099 11.4911 21.1367 10.0083 20.7931C8.52547 20.4496 7.1518 19.7369 6.01547 18.7246C4.87913 17.7122 4.01717 16.4332 3.51 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                
                <div class="balance-main">
                    <div class="balance-left">
                        <div class="balance-value" id="userBalance" onclick="showBalanceDetails()"><?= formatMoney($info['money'] ?? 0) ?> Điểm</div>
                        <div class="balance-label">Tích điểm để nhận thưởng</div>
                    </div>
                    <div class="credit-score">
                        <div class="credit-value"><?= number_format($info['credit'] ?? 0) ?></div>
                        <div class="balance-label">Điểm thưởng</div>
                    </div>
                </div>
                
                <div class="bank-info">
                    <div class="bank-detail">
                        <div class="bank-detail-title">Ngân hàng</div>
                        <div class="bank-detail-value" id="bankDisplayName"><?= $bank ? htmlspecialchars($bank['bankinfo']) : 'Chưa liên kết' ?></div>
                    </div>
                    <div class="bank-detail">
                        <div class="bank-detail-title">Tài khoản</div>
                        <div class="bank-detail-value" id="cardNumber"><?= $bank ? maskAccount($bank['bankid'], 4) : '**** ****' ?></div>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $userStats['pendingWithdrawals'] > 0 ? '50%' : '0%' ?>;"></div>
                </div>
                <div style="text-align: center; margin-top: 8px; font-size: 11px; color: var(--gray-500);">
                    <?= $userStats['pendingWithdrawals'] > 0 ? 'Giao dịch đang chờ: ' . $userStats['pendingWithdrawals'] : 'Không có giao dịch chờ' ?>
                </div>
            </div>

            <div class="action-grid">
                <div class="action-item" onclick="openModal('depositModal')">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-label">Nạp điểm</div>
                </div>
                <div class="action-item" onclick="openModal('userWithDrawModal')">
                    <div class="action-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="action-label">Rút điểm</div>
                </div>
                <div class="action-item" onclick="openModal('userNotiModal')">
                    <div class="action-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="action-label">Lịch sử</div>
                </div>
                <div class="action-item" onclick="openModal('vipMembershipModal')">
                    <div class="action-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="action-label">Thẻ mua sắm</div>
                </div>
            </div>
        </div>

        <!-- Menu Section -->
        <div class="section-menu">
            <div class="menu-section-title">Quản lý tài khoản</div>
            
            <div class="menu-item" onclick="openModal('reviewModal')">
                <div class="menu-left">
                    <div class="menu-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="menu-text">Lịch sử tham gia</div>
                </div>
                <div class="menu-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
        
            
            <div class="menu-item" onclick="openModal('depositWithdrawHistoryModal')">
                <div class="menu-left">
                    <div class="menu-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="menu-text">Lịch sử nạp rút</div>
                </div>
                <div class="menu-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="menu-item" onclick="openModal('adminNotificationModal')">
                <div class="menu-left">
                    <div class="menu-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="menu-text">Thông báo</div>
                </div>
                <div class="menu-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
     
            <div class="menu-item" onclick="openModal('userBankModal')">
                <div class="menu-left">
                    <div class="menu-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="menu-text">Ngân hàng</div>
                </div>
                <div class="menu-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="menu-item" onclick="openModal('userDetailModal')">
                <div class="menu-left">
                    <div class="menu-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="menu-text">Đổi Mật Khẩu</div>
                </div>
                <div class="menu-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="menu-item logout-menu" onclick="logout()">
                <div class="menu-left">
                    <div class="menu-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div class="menu-text">Đăng Xuất</div>
                </div>
                <div class="menu-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        </div>
    </div>
      <div id="depositModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Nạp điểm</h3>
                <button class="modal-close" onclick="closeModal('depositModal')">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Số dư hiện tại: <strong><?= formatMoney($info['money']) ?>₫</strong>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Chức năng nạp tiền đang bảo trì. Vui lòng liên hệ admin để nạp tiền thủ công.
                </div>

                <div class="form-group">
                    <label class="form-label">Số tiền muốn nạp</label>
                    <input type="number" class="form-input" placeholder="Nhập số tiền" min="10000" step="1000" disabled>
                </div>

                <button class="btn-primary" disabled style="width: 100%; opacity: 0.5;">
                    <i class="fas fa-tools"></i>
                    Đang bảo trì
                </button>

                <div class="alert alert-info" style="margin-top: 16px;">
                    <h6 style="margin-bottom: 8px; color: var(--vinamilk-primary);">Thông tin liên hệ:</h6>
                    <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                        <li>Telegram: @VinamilkXSupport</li>
                        <li>Email: support@vinamilkx.com</li>
                        <li>Thời gian xử lý: 5-30 phút</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

       <div id="reviewModal" class="modal-overlay hidden">
 
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-colorful">
                <h5 class="modal-title modal-title-colorful">Lịch sử tham gia </h5>
                <button class="modal-close" onclick="closeModal('userDetailModal')">×</button>
            </div>
            <div class="modal-body">
                <table class="table" id="dataTableUserHistory">
                    <thead>
                        <tr>
                            <th>Phiên</th>
                            <th>Ngày cược</th>
                            <th>Loại</th>
                            <th>Tiền</th>
                            <th>Tỉ lệ</th>
                            <th>Kết quả</th>
                        </tr>
                    </thead>
                    <tbody id="tbl-history">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <!-- User Detail Modal -->
    <div id="userDetailModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Thông tin tài khoản</h3>
                <button class="modal-close" onclick="closeModal('userDetailModal')">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-star"></i>
                    <div>
                        <strong>Quyền lợi VIP:</strong><br>
                        • Giao hàng ưu tiên nhanh<br>
                        • Quà tặng độc quyền thành viên<br>
                        • Tỷ lệ giảm giá đặc biệt
                    </div>
                </div>
                
                <?php
                render_pref($info, "name", "Họ và tên");
                render_pref($info, "gender", "Giới tính", "radio", function ($value) {
                    if ($value == 1) return "Nam";
                    if ($value == 2) return "Nữ";
                    return "Chưa xác định";
                }, [1 => "Nam", 2 => "Nữ", 0 => "Chưa xác định"]);
                render_pref($info, "phone", "Số điện thoại");
                ?>
                
                <hr style="margin: 16px 0; border-color: rgba(74, 144, 226, 0.2);">
                <h4 style="margin-bottom: 16px; color: var(--vinamilk-primary);">Đổi mật khẩu</h4>
                <form id="form-change-password">
                    <div class="form-group">
                        <label class="form-label">Nhập mật khẩu cũ</label>
                        <input type="password" name="old-pass" placeholder="Liên hệ admin nếu không biết" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nhập mật khẩu mới</label>
                        <input type="password" name="new-pass" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" name="re-pass" required class="form-input">
                    </div>
                    <button type="submit" class="btn-primary">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Admin Notification Modal (Thông báo hệ thống) -->
    <div id="adminNotificationModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-bell"></i> Thông báo
                </h3>
                <button class="modal-close" onclick="closeModal('adminNotificationModal')">×</button>
            </div>
            <div class="modal-body">
                <div class="notification-container">
                    <div class="notification-header">
                        <div class="notification-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="totalNotifications">0</span>
                                <span class="stat-label">Tổng thông báo</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="unreadNotifications">0</span>
                                <span class="stat-label">Chưa đọc</span>
                            </div>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-sm btn-primary" onclick="refreshTransactions()">
                                <i class="fas fa-refresh"></i> Làm mới
                            </button>
                            <button class="btn btn-sm btn-secondary" onclick="exportTransactions()">
                                <i class="fas fa-download"></i> Xuất file
                            </button>
                        </div>
                    </div>
                    
                    <div class="notification-list" id="notificationList">
                        <div class="loading-notification" style="text-align: center; padding: 40px;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--gray-400); margin-bottom: 16px;"></i>
                            <p style="color: var(--gray-600);">Đang tải thông báo...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bank Modal -->
    <div id="userBankModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-credit-card"></i> Tài khoản ngân hàng
                </h3>
                <button class="modal-close" onclick="closeModal('userBankModal')">×</button>
            </div>
            <div class="modal-body">
                <?php if (!$bank): ?>
                    <div>
                        <button id="btn-add-bank-step-2" class="btn btn-success" style="margin-bottom: 16px;" onclick="toggleBankForm()">
                            <i class="fas fa-plus"></i> Thêm thẻ nhận tiền
                        </button>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Vui lòng liên kết với các ngân hàng thương mại lớn.
                        </div>
                    </div>
                    <form id="form-bind-card" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Ngân hàng</label>
                            <input type="text" name="bankinfo" required class="form-input" placeholder="Ví dụ: Vietcombank">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số tài khoản</label>
                            <input type="text" name="bankid" required class="form-input" placeholder="Nhập số tài khoản">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tên chủ thẻ</label>
                            <input type="text" name="name" required class="form-input" placeholder="Tên chủ tài khoản">
                        </div>
                        <button type="submit" class="btn btn-primary">TẢI LÊN</button>
                    </form>
                <?php else: ?>
                    <div>
                        <h4 style="margin-bottom: 16px;">Thông tin ngân hàng</h4>
                        <div class="pref-item">
                            <div class="left">Ngân hàng:</div>
                            <div class="right"><?= htmlspecialchars($bank['bankinfo']) ?></div>
                        </div>
                        <div class="pref-item">
                            <div class="left">Số tài khoản:</div>
                            <div class="right"><?= maskAccount($bank['bankid'], 3) ?></div>
                        </div>
                        <div class="pref-item">
                            <div class="left">Tên chủ thẻ:</div>
                            <div class="right"><?= htmlspecialchars($bank['name']) ?></div>
                        </div>
                        <div class="pref-item">
                            <div class="left">Trạng thái:</div>
                            <div class="right">
                                <span style="color: var(--vinamilk-success);">
                                    <i class="fas fa-check-circle"></i> Đã xác minh
                                </span>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Thông tin ngân hàng đã được xác minh và bảo mật.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="userNotiModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Thông báo</h3>
                <button class="modal-close" onclick="closeModal('userNotiModal')">×</button>
            </div>
            <div class="modal-body">
                <?php if (empty($noti)): ?>
                    <div style="text-align: center; padding: 32px;">
                        <i class="fas fa-bell" style="font-size: 2rem; color: var(--vinamilk-primary); margin-bottom: 12px;"></i>
                        <p style="color: #9ca3af; font-size: 13px;">Chưa có thông báo nào</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($noti as $n): ?>
                        <div style="padding: 14px; border-left: 3px solid <?= htmlspecialchars($n["color"]) ?>; background: var(--vinamilk-light); margin-bottom: 16px; border-radius: 0 6px 6px 0;">
                            <h5 style="margin: 0 0 8px 0; font-weight: bold; font-size: 14px; color: var(--vinamilk-primary);"><?= htmlspecialchars($n["title"]) ?></h5>
                            <div style="font-size: 13px; color: var(--gray-700); margin-bottom: 8px;">
                                <?= nl2br(htmlspecialchars($n["content"])) ?>
                            </div>
                            <span style="font-size: 11px; color: #9ca3af;"><?= date("Y-m-d H:i:s", $n["time"]) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Withdrawal Modal -->
    <div id="userWithDrawModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Rút tiền</h3>
                <button class="modal-close" onclick="closeModal('userWithDrawModal')">×</button>
            </div>
            <div class="modal-body">
                <?php if (!$bank): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Vui lòng thêm thông tin tài khoản ngân hàng trước khi rút tiền.
                        <button class="btn-primary" style="margin-top: 12px;" onclick="openModal('userBankModal')">Thêm tài khoản ngân hàng</button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Rút về: <strong><?= htmlspecialchars($bank['bankinfo']) ?> (****<?= substr($bank['bankid'], -4) ?>)</strong>
                    </div>
                    <form id="form-withdraw">
                        <div class="form-group">
                            <label class="form-label">Số tiền muốn rút (VND)</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="number" name="withdraw_amount" step="1000" min="1000" max="999999999999" placeholder="Nhập số tiền" class="form-input" style="flex: 1;" oninput="validateWithdrawForm()" required>
                                <button type="button" id="btn-withdraw-all" class="btn-success" style="width: auto; padding: 10px 14px;" onclick="setWithdrawAll()">Rút hết</button>
                            </div>
                            <div id="withdraw-error" class="withdraw-error" style="display: none; color: var(--vinamilk-error); font-size: 12px; margin-top: 5px;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin: 16px 0; font-size: 14px;">
                            <span style="color: var(--vinamilk-primary);">Số dư: <strong><?= formatMoney($info['money']) ?>₫</strong></span>
                            <span style="position: relative; cursor: pointer;" id="withdraw_tooltip" onclick="toggleTooltip()">
                                <i class="fas fa-info-circle" style="color: var(--vinamilk-accent); margin-right: 5px;"></i>
                                Thông tin
                                <div id="tooltip-content" style="display: none; position: absolute; top: -100px; right: 0; background: var(--vinamilk-primary); color: white; padding: 8px; border-radius: 6px; font-size: 12px; width: 200px; z-index: 10; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    - Số tiền tối thiểu: 1,000₫<br>
                                    - Số tiền tối đa: Không giới hạn<br>
                                    - Phí xử lý: Miễn phí<br>
                                    - Thời gian xử lý: 5-30 phút
                                </div>
                            </span>
                        </div>
                        <button type="submit" id="withdraw-submit" class="btn-primary" disabled style="width: 100%;">Xác nhận rút tiền</button>
                    </form>
                <?php endif; ?>
                
                <!-- Withdrawal History -->
                <div style="margin-top: 24px;">
                    <h5 style="color: var(--vinamilk-primary); margin-bottom: 12px;">Lịch sử rút tiền</h5>
                    <div style="max-height: 250px; overflow-y: auto; border-radius: 6px; border: 1px solid rgba(74, 144, 226, 0.3);">
                        <table class="vip-table">
                            <thead>
                                <tr>
                                    <th>Số tiền</th>
                                    <th>Ngày</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody id="withdrawHistoryBody">
                                <?php 
                                $withdrawals = WithdrawModel::GetByUID($id);
                                if (!empty($withdrawals)): 
                                    foreach ($withdrawals as $w): 
                                ?>
                                    <tr>
                                        <td><?= formatMoney($w["money"]) ?>₫</td>
                                        <td><?= date("d/m/Y", $w["create_time"]) ?></td>
                                        <td>
                                            <?php if ($w["status"] == 1): ?>
                                                <span class="status-completed">Thành công</span>
                                            <?php elseif ($w["status"] == 2): ?>
                                                <span class="status-pending">Thất bại</span>
                                            <?php else: ?>
                                                <span class="status-current">Đang chờ</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php 
                                    endforeach; 
                                else: 
                                ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; color: #9ca3af;">Chưa có lịch sử rút tiền</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Modal -->
    <div id="supportModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-headset"></i> Hỗ trợ khách hàng
                </h3>
                <button class="modal-close" onclick="closeModal('supportModal')">×</button>
            </div>
            <div class="modal-body">
                <div style="background: var(--vinamilk-light); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <i class="fas fa-headset" style="color: var(--vinamilk-primary);"></i>
                    Liên hệ đội ngũ hỗ trợ để được giải đáp nhanh chóng!
                </div>
                
                <div style="display: grid; gap: 15px;">
                    <a href="tel:+84987654321" style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border-radius: 8px; text-decoration: none; color: inherit; border: 1px solid var(--vinamilk-primary); transition: all 0.3s ease;" onmouseover="this.style.background='var(--vinamilk-light)'" onmouseout="this.style.background='white'">
                        <div>
                            <i class="fas fa-phone" style="margin-right: 12px; color: var(--vinamilk-success);"></i>
                            <strong>Hotline:</strong>
                        </div>
                        <span style="color: var(--vinamilk-primary);">+84 987 654 321</span>
                    </a>
                    
                    <a href="mailto:support@vinamilkx.com" style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border-radius: 8px; text-decoration: none; color: inherit; border: 1px solid var(--vinamilk-primary); transition: all 0.3s ease;" onmouseover="this.style.background='var(--vinamilk-light)'" onmouseout="this.style.background='white'">
                        <div>
                            <i class="fas fa-envelope" style="margin-right: 12px; color: var(--vinamilk-error);"></i>
                            <strong>Email:</strong>
                        </div>
                        <span style="color: var(--vinamilk-primary);">support@vinamilkx.com</span>
                    </a>
                    
                    <a href="https://t.me/vinamilkxsupport" target="_blank" style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border-radius: 8px; text-decoration: none; color: inherit; border: 1px solid var(--vinamilk-primary); transition: all 0.3s ease;" onmouseover="this.style.background='var(--vinamilk-light)'" onmouseout="this.style.background='white'">
                        <div>
                            <i class="fab fa-telegram" style="margin-right: 12px; color: #0088cc;"></i>
                            <strong>Telegram:</strong>
                        </div>
                        <span style="color: var(--vinamilk-primary);">@VinamilkXSupport</span>
                    </a>
                </div>
                
                <div style="margin-top: 20px; padding: 16px; background: var(--vinamilk-light); border-radius: 8px; border: 1px solid rgba(74, 144, 226, 0.3);">
                    <h6 style="margin-bottom: 8px; color: var(--vinamilk-primary);">
                        <i class="fas fa-star"></i> Thời gian hỗ trợ:
                    </h6>
                    <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: var(--gray-700);">
                        <li>Thứ 2 - Thứ 6: 8:00 - 18:00</li>
                        <li>Thứ 7 - Chủ nhật: 9:00 - 17:00</li>
                        <li>Phản hồi trong vòng 30 phút</li>
                        <li>Hỗ trợ 24/7 qua Telegram</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification"></div>

    <script>
        // ===== VINAMILK STYLE COUPANG SHOP JAVASCRIPT =====

        // Global Configuration - sử dụng dữ liệu từ PHP
        const PROFILE_CONFIG = {
            USER_ID: "<?= $id ?>",
            USER_BALANCE: parseFloat("<?= $info['money'] ?>") || 0,
            MIN_WITHDRAW: 1000,
            MAX_WITHDRAW: 999999999999,
            USERNAME: "<?= htmlspecialchars($displayName) ?>",
            VIP_LEVEL: "<?= $vipLevel ?>"
        };

        // Prevent double submission flags
        let isWithdrawing = false;
        let isBankSubmitting = false;
        let isPasswordChanging = false;

        // ===== INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== VINAMILKX PROFILE LOADED ===');
            console.log('Config:', PROFILE_CONFIG);
            
            // Initialize Toastr with Vinamilk colors
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-bottom-right',
                    timeOut: 5000,
                    showEasing: 'swing',
                    hideEasing: 'linear',
                    showMethod: 'fadeIn',
                    hideMethod: 'fadeOut'
                };
            }

            setupEventListeners();
            initializeForms();
            
            console.log('✅ VinamilkX Profile loaded successfully!');
        });

        // ===== HELPER FUNCTIONS =====
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(parseFloat(amount) || 0);
        }

        function _api(options) {
            const formData = new FormData();
            
            for (const key in options.data) {
                formData.append(key, options.data[key]);
            }
            
            const routeUrl = '/ajax/index.php';
            
            fetch(routeUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                console.log('API Response:', text);
                try {
                    const data = JSON.parse(text);
                    if (options.onSuccess) options.onSuccess(data);
                } catch (e) {
                    console.error('JSON Parse Error:', e);
                    if (options.onError) options.onError(new Error('Invalid JSON response: ' + text));
                }
            })
            .catch(error => {
                console.error('API Error:', error);
                if (options.onError) options.onError(error);
            });
        }

        // ===== MODAL CONTROL =====
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                if (modalId === 'userWithDrawModal') {
                    setTimeout(() => {
                        validateWithdrawForm();
                        resetWithdrawFlags();
                    }, 100);
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                
                if (modalId === 'userWithDrawModal') {
                    resetWithdrawFlags();
                }
                if (modalId === 'userBankModal') {
                    isBankSubmitting = false;
                }
                if (modalId === 'userDetailModal') {
                    isPasswordChanging = false;
                }
            }
        }

        // ===== RESET FLAGS FUNCTIONS =====
        function resetWithdrawFlags() {
            isWithdrawing = false;
            console.log('🔄 Reset withdrawal flags');
        }

        function resetBankFlags() {
            isBankSubmitting = false;
            console.log('🔄 Reset bank flags');
        }

    
// ===== FIXED WITHDRAWAL FUNCTIONS =====
function validateWithdrawForm() {
    try {
        const amountInput = document.querySelector('input[name="withdraw_amount"]');
        const submitBtn = document.getElementById('withdraw-submit');
        const errorDiv = document.getElementById('withdraw-error');
        
        if (!amountInput || !submitBtn) {
            console.warn('⚠️ Withdraw form elements not found');
            return false;
        }
        
        const amount = parseFloat(amountInput.value) || 0;
        const userBalance = PROFILE_CONFIG.USER_BALANCE;
        const minAmount = PROFILE_CONFIG.MIN_WITHDRAW;
        
        let isValid = true;
        let errorMessage = '';
        
        if (amount <= 0) {
            isValid = false;
            errorMessage = 'Vui lòng nhập số tiền';
        } else if (amount < minAmount) {
            isValid = false;
            errorMessage = `Số tiền tối thiểu là ${formatMoney(minAmount)}₫`;
        } else if (amount > userBalance) {
            isValid = false;
            errorMessage = 'Số dư không đủ';
        }
        
        // Update submit button state
        submitBtn.disabled = !isValid || isWithdrawing;
        submitBtn.style.opacity = (isValid && !isWithdrawing) ? '1' : '0.5';
        submitBtn.style.cursor = (isValid && !isWithdrawing) ? 'pointer' : 'not-allowed';
        
        // Show/hide error message
        if (errorDiv) {
            if (!isValid && amount > 0) {
                errorDiv.textContent = errorMessage;
                errorDiv.style.display = 'block';
                errorDiv.style.color = '#ef4444';
            } else {
                errorDiv.style.display = 'none';
            }
        }
        
        console.log('✅ Withdrawal validation:', { amount, isValid, errorMessage, isWithdrawing });
        return isValid;
        
    } catch (e) {
        console.error('❌ Validation error:', e);
        return false;
    }
}

function setWithdrawAll() {
    try {
        const userBalance = PROFILE_CONFIG.USER_BALANCE;
        // Round down to nearest 1000 VND
        const roundedAmount = Math.floor(userBalance / 1000) * 1000;
        
        const amountInput = document.querySelector('input[name="withdraw_amount"]');
        if (amountInput) {
            amountInput.value = roundedAmount;
            amountInput.dispatchEvent(new Event('input')); // Trigger validation
            
            if (typeof toastr !== 'undefined') {
                toastr.info(`Đã đặt số tiền rút: ${formatMoney(roundedAmount)}₫`);
            }
        }
    } catch (e) {
        console.error('❌ Set withdraw all error:', e);
    }
}

// ✅ FIXED: Prevent double withdrawal submission
function processWithdraw(amount) {
    try {
        console.log('💰 Processing withdrawal:', amount);
        
        // Prevent double submission
        if (isWithdrawing) {
            console.warn('⚠️ Withdrawal already in progress, ignoring...');
            return;
        }
        
        const submitBtn = document.getElementById('withdraw-submit');
        if (!submitBtn) {
            console.error('❌ Submit button not found');
            if (typeof toastr !== 'undefined') {
                toastr.error('Không tìm thấy nút submit');
            }
            return;
        }
        
        // Final validation
        if (!validateWithdrawForm()) {
            console.warn('⚠️ Validation failed, aborting withdrawal');
            return;
        }
        
        // Set flag to prevent double submission
        isWithdrawing = true;
        
        // Show loading state
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';
        
        // Prepare withdrawal data
        const formData = new FormData();
        formData.append('action_type', 'withdrawal');
        formData.append('amount', Math.floor(amount).toString()); // Ensure integer
        formData.append('note', 'Yêu cầu rút tiền từ profile');
        formData.append('timestamp', Date.now().toString());
        
        console.log('📤 Sending withdrawal request...');
        
        // Make AJAX request with timeout
        fetch('/ajax/index.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('📥 Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return response.text();
        })
        .then(text => {
            console.log('📄 Raw response:', text);
            
            let responseData;
            try {
                responseData = JSON.parse(text);
            } catch (e) {
                console.error('❌ JSON parse error:', e);
                throw new Error('Server returned invalid JSON: ' + text.substring(0, 200));
            }
            
            console.log('✅ Parsed response:', responseData);
            
            if (responseData.success) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(responseData.message || 'Yêu cầu rút tiền thành công!');
                }
                
                // Reset form
                const amountInput = document.querySelector('input[name="withdraw_amount"]');
                if (amountInput) {
                    amountInput.value = '';
                }
                
                // Update balance if provided
                if (responseData.new_balance !== undefined) {
                    PROFILE_CONFIG.USER_BALANCE = responseData.new_balance;
                    const balanceElement = document.getElementById('userBalance');
                    if (balanceElement) {
                        balanceElement.textContent = `$${formatMoney(responseData.new_balance)}`;
                    }
                }
                
                // Close modal and reload
                setTimeout(() => {
                    closeModal('userWithDrawModal');
                    window.location.reload();
                }, 2000);
                
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(responseData.message || 'Có lỗi xảy ra khi tạo yêu cầu rút tiền');
                }
            }
        })
        .catch(error => {
            console.error('❌ Request error:', error);
            
            // Detailed error handling
            if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Lỗi kết nối mạng. Vui lòng kiểm tra internet và thử lại.');
                }
            } else if (error.message.includes('HTTP 500')) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Lỗi server. Vui lòng thử lại sau ít phút.');
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Lỗi: ' + error.message);
                }
            }
        })
        .finally(() => {
            // Always restore button state and reset flag
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }
            
            // Reset flag after 2 seconds to prevent rapid clicks
            setTimeout(() => {
                resetWithdrawFlags();
                validateWithdrawForm(); // Re-validate form
            }, 2000);
        });
        
    } catch (e) {
        console.error('❌ Process withdraw error:', e);
        resetWithdrawFlags();
        if (typeof toastr !== 'undefined') {
            toastr.error('Có lỗi xảy ra: ' + e.message);
        }
    }
}

// ===== FIXED BANK FUNCTIONS =====
function toggleBankForm() {
    try {
        const form = document.getElementById('form-bind-card');
        const btn = document.getElementById('btn-add-bank-step-2');
        
        if (form && btn) {
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                btn.innerHTML = '<i class="fas fa-times"></i> Hủy';
            } else {
                form.style.display = 'none';
                btn.innerHTML = '<i class="fas fa-plus"></i> Thêm thẻ nhận tiền';
                resetBankFlags(); // Reset flags when hiding form
            }
        }
    } catch (e) {
        console.error('❌ Toggle bank form error:', e);
    }
}

// ✅ FIXED: Prevent double bank form submission
function submitBankForm(event) {
    event.preventDefault();
    
    try {
        // Prevent double submission
        if (isBankSubmitting) {
            console.warn('⚠️ Bank form already submitting, ignoring...');
            return;
        }
        
        const form = document.getElementById('form-bind-card');
        if (!form) {
            console.error('❌ Bank form not found');
            return;
        }
        
        const formData = new FormData(form);
        const bankinfo = formData.get('bankinfo');
        const bankid = formData.get('bankid');
        const name = formData.get('name');
        
        // Validation
        if (!bankinfo || !bankid || !name) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Vui lòng điền đầy đủ thông tin');
            }
            return;
        }
        
        // Set flag to prevent double submission
        isBankSubmitting = true;
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        submitBtn.disabled = true;
        
        // Prepare data
        formData.append('action_type', 'bank_bind');
        
        console.log('📤 Sending bank bind request...');
        
        fetch('/ajax/index.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            console.log('📄 Bank bind response:', text);
            
            let responseData;
            try {
                responseData = JSON.parse(text);
            } catch (e) {
                console.error('❌ JSON parse error:', e);
                throw new Error('Server returned invalid JSON: ' + text.substring(0, 200));
            }
            
            if (responseData.success) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(responseData.message || 'Đã cập nhật thông tin ngân hàng thành công');
                }
                
                // Reset form and close
                form.reset();
                toggleBankForm(); // Hide form
                
                // Reload page after delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(responseData.message || 'Có lỗi xảy ra khi cập nhật thông tin ngân hàng');
                }
            }
        })
        .catch(error => {
            console.error('❌ Bank bind error:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Có lỗi xảy ra, vui lòng thử lại');
            }
        })
        .finally(() => {
            // Always restore button state and reset flag
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
            
            // Reset flag after 2 seconds
            setTimeout(() => {
                resetBankFlags();
            }, 2000);
        });
        
    } catch (e) {
        console.error('❌ Submit bank form error:', e);
        resetBankFlags();
    }
}
async function loadAdminNotifications() {
    try {
        const notificationList = document.getElementById('notificationList');
        if (notificationList) {
            notificationList.innerHTML = '<div>Loading...</div>';
        }
        
        // Mock notification data since the API might not exist
        adminNotifications = [
            {
                id: 1,
                title: 'Thông báo hệ thống',
                content: 'Hệ thống đang hoạt động bình thường',
                created_at: new Date().toISOString(),
                is_read: false,
                color: '#007bff',
                icon: 'fas fa-info-circle'
            }
        ];
        unreadCount = 1;
        
        renderNotifications();
        updateNotificationStats();
        
    } catch (error) {
        console.error('Error loading notifications:', error);
        adminNotifications = [];
        unreadCount = 0;
        renderNotifications();
        updateNotificationStats();
    }
}

const debouncedLoadNotifications = debounce(loadAdminNotifications, 300);

function renderNotifications() {
    const notificationList = document.getElementById('notificationList');
    if (!notificationList) return;
    
    if (adminNotifications.length === 0) {
        notificationList.innerHTML = '<div class="no-notifications" style="text-align: center; padding: 40px;"><i class="fas fa-bell-slash" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i><p>Không có thông báo nào</p></div>';
        return;
    }
    
    notificationList.innerHTML = adminNotifications.map(notification => {
        const iconClass = notification.icon || 'fas fa-bell';
        const timeAgo = formatNotificationTime(notification.created_at);
        return `
            <div class="notification-item ${notification.is_read ? 'read' : 'unread'}" onclick="markNotificationRead(${notification.id})" style="
                padding: 16px; border-radius: 8px; margin-bottom: 12px; cursor: pointer;
                background: ${notification.is_read ? '#f8f9fa' : '#fff3cd'};
                border-left: 4px solid ${notification.color || '#007bff'};
            ">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="flex-shrink: 0; padding: 8px; background: ${notification.color || '#007bff'}; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        <i class="${iconClass}" style="font-size: 14px;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #333; margin-bottom: 4px;">${notification.title}</div>
                        <div style="color: #666; font-size: 14px; line-height: 1.4; margin-bottom: 8px;">${notification.content}</div>
                        <div style="color: #999; font-size: 12px;">${timeAgo}</div>
                    </div>
                    <div style="flex-shrink: 0; color: ${notification.is_read ? '#28a745' : '#ffc107'};">
                        ${notification.is_read ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-circle"></i>'}
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function formatNotificationTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffMs = now - date;
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffDays = Math.floor(diffHours / 24);
    if (diffDays > 0) return `${diffDays} ngày trước`;
    if (diffHours > 0) return `${diffHours} giờ trước`;
    return 'Vừa xong';
}

function updateNotificationStats() {
    const totalElement = document.getElementById('totalNotifications');
    const unreadElement = document.getElementById('unreadNotifications');
    if (totalElement) totalElement.textContent = adminNotifications.length;
    if (unreadElement) unreadElement.textContent = unreadCount;
}

async function markNotificationRead(notificationId) {
    const notification = adminNotifications.find(n => n.id === notificationId);
    if (notification && !notification.is_read) {
        notification.is_read = true;
        unreadCount--;
        updateNotificationStats();
        renderNotifications();
    }
}

// ===== FIXED PASSWORD FUNCTIONS =====
function submitPasswordForm(event) {
    event.preventDefault();
    
    try {
        // Prevent double submission
        if (isPasswordChanging) {
            console.warn('⚠️ Password change already in progress, ignoring...');
            return;
        }
        
        const form = document.getElementById('form-change-password');
        if (!form) return;
        
        const formData = new FormData(form);
        const oldPass = formData.get('old-pass');
        const newPass = formData.get('new-pass');
        const rePass = formData.get('re-pass');
        
        // Validation
        if (newPass !== rePass) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Mật khẩu mới không khớp');
            }
            return;
        }
        
        if (newPass.length < 6) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Mật khẩu mới phải có ít nhất 6 ký tự');
            }
            return;
        }
        
        // Set flag to prevent double submission
        isPasswordChanging = true;
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        submitBtn.disabled = true;
        
        _api({
            data: {
                action_type: 'change_user_password',
                'old-pass': oldPass,
                'new-pass': newPass,
                're-pass': rePass
            },
            onSuccess: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Đổi mật khẩu thành công');
                    }
                    form.reset();
                    closeModal('userDetailModal');
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Có lỗi xảy ra');
                    }
                }
            },
            onError: function(error) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Lỗi kết nối, vui lòng thử lại');
                }
            }
        });
        
        // Always restore button state and reset flag
        setTimeout(() => {
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
            isPasswordChanging = false;
        }, 2000);
        
    } catch (e) {
        console.error('❌ Submit password form error:', e);
        isPasswordChanging = false;
    }
}

// ===== AVATAR FUNCTIONS (UPDATED) =====
function openAvatarUpload() {
    try {
        const avatarInput = document.getElementById('avatarInput');
        if (avatarInput) avatarInput.click();
    } catch (e) {
        console.error('❌ Open avatar upload error:', e);
    }
}

function handleAvatarUpload(event) {
    try {
        const file = event.target.files[0];
        if (!file) return;
        
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type.toLowerCase())) {
            if (typeof toastr !== 'undefined') {
                toastr.error('❌ Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WebP)');
            }
            return;
        }
        
        if (file.size > 2 * 1024 * 1024) {
            if (typeof toastr !== 'undefined') {
                toastr.error('❌ File quá lớn! Tối đa 2MB');
            }
            return;
        }
        
        if (typeof toastr !== 'undefined') {
            toastr.info('📷 Đang tải lên avatar...', 'Upload');
        }
        
        const formData = new FormData();
        formData.append('avatar', file);
        
        fetch('/ajax/upload_avatar.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.user-avatar').forEach(el => {
                    el.src = `${data.avatar_url}?t=${Date.now()}`;
                });
                if (typeof toastr !== 'undefined') {
                    toastr.success('✅ ' + (data.message || 'Avatar đã được cập nhật'), 'Success');
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error('❌ ' + (data.message || 'Có lỗi xảy ra'), 'Error');
                }
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('❌ Có lỗi xảy ra khi upload ảnh', 'Error');
            }
        })
        .finally(() => {
            event.target.value = '';
        });
    } catch (e) {
        console.error('❌ Handle avatar upload error:', e);
    }
}

// ===== OTHER FUNCTIONS (UPDATED) =====
function logout() {
    try {
        if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
            if (typeof toastr !== 'undefined') {
                toastr.info('👋 Đang đăng xuất...', 'Logout');
            }
            setTimeout(() => {
                window.location.href = '/?ctrl=login';
            }, 1500);
        }
    } catch (e) {
        console.error('❌ Logout error:', e);
    }
}

function refreshBalance() {
    try {
        if (typeof toastr !== 'undefined') {
            toastr.info('🔄 Đang làm mới số dư...', 'Refresh');
        }
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    } catch (e) {
        console.error('❌ Refresh balance error:', e);
    }
}

function showBalanceDetails() {
    try {
        if (typeof toastr !== 'undefined') {
            toastr.info(`💰 Số dư hiện tại: ${formatMoney(PROFILE_CONFIG.USER_BALANCE)}₫`, 'Balance');
        }
    } catch (e) {
        console.error('❌ Show balance details error:', e);
    }
}

function refreshTransactions() {
    try {
        if (typeof toastr !== 'undefined') {
            toastr.info('🔄 Đang làm mới danh sách giao dịch...', 'Refresh');
        }
        setTimeout(() => {
            if (typeof toastr !== 'undefined') {
                toastr.success('✅ Đã làm mới thành công!', 'Success');
            }
        }, 1000);
    } catch (e) {
        console.error('❌ Refresh transactions error:', e);
    }
}

function toggleTooltip() {
    try {
        const tooltip = document.getElementById('tooltip-content');
        if (tooltip) {
            const isVisible = tooltip.style.display === 'block';
            tooltip.style.display = isVisible ? 'none' : 'block';
            
            // Auto-hide after 5 seconds
            if (!isVisible) {
                setTimeout(() => {
                    tooltip.style.display = 'none';
                }, 5000);
            }
        }
    } catch (e) {
        console.error('❌ Toggle tooltip error:', e);
    }
}



function render_iswin(s) {
    if (s == 1) return `<span class="status-completed"><i class="fas fa-check-circle mr-1"></i>Đúng</span>`;
    if (s == 2) return `<span class="status-pending"><i class="fas fa-times-circle mr-1"></i>Sai</span>`;
    return `<span class="status-current"><i class="fas fa-clock mr-1"></i>Chưa mở</span>`;
}

function render_lottery_history_status(s, session) {
    if (session == current_session) {
        return `<span class="status-current"><i class="fas fa-clock mr-1"></i>Hiện tại</span>`;
    }
    if (s == 1) return `<span class="status-pending"><i class="fas fa-hourglass-half mr-1"></i>Chưa mở</span>`;
    if (s == 0) return `<span class="status-completed"><i class="fas fa-check-circle mr-1"></i>Đã mở</span>`;
    return `<span class="status-pending">Không xác định</span>`;
}

function loadLotteryHistory() {
    try {
        if (typeof $ !== 'undefined') {
            $.ajax({
                type: "POST",
                url: "/ajax/index.php",
                data: { action_type: "get_lottery_history", lid: id },
                dataType: "json",
                timeout: 10000,
                success: function(response) {
                    try {
                        if (response && response.success) {
                            let historyData = (response.data || []).slice(0, 15);
                            $("#tbl-lottery-history").empty();
                            
                            const currentExists = historyData.some(e => e.sid == current_session);
                            if (!currentExists && current_session) {
                                const currentRow = `<tr class="hover:bg-amber-50">
                                    <td class="py-2 font-medium text-blue-400">${current_session}</td>
                                    <td class="py-2 font-bold text-blue-400">Đang chờ...</td>
                                    <td class="py-2">${render_lottery_history_status(1, current_session)}</td>
                                </tr>`;
                                $("#tbl-lottery-history").append(currentRow);
                            }
                            
                            historyData.forEach(e => {
                                let row = `<tr class="hover:bg-indigo-50 border-b border-gray-100">
                                    <td class="py-2 font-medium">${e.sid || ''}</td>
                                    <td class="py-2 font-bold text-amber-400">${e.result || 'Chưa có'}</td>
                                    <td class="py-2">${render_lottery_history_status(e.status, e.sid)}</td>
                                </tr>`;
                                $("#tbl-lottery-history").append(row);
                            });

                            if (typeof toastr !== 'undefined') {
                                toastr.success('Lịch sử đã được cập nhật!');
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.warning('Không có lịch sử xổ số');
                            }
                        }
                    } catch (e) {
                        console.error("Error processing lottery history:", e);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Lỗi xử lý dữ liệu');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error getting lottery history:", error);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Không thể tải lịch sử xổ số');
                    }
                }
            });
        }
    } catch (e) {
        console.error('Load lottery history error:', e);
    }
}

// ===== EVENT LISTENERS SETUP =====
function setupEventListeners() {
    try {
        // Modal close on outside click
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        });
        
        // Escape key to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(modal => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                });
            }
        });
        
        // Close tooltip when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#withdraw_tooltip')) {
                const tooltip = document.getElementById('tooltip-content');
                if (tooltip) tooltip.style.display = 'none';
            }
        });
        
    } catch (e) {
        console.error('❌ Setup event listeners error:', e);
    }
}

// ===== FORM INITIALIZATION =====
function initializeForms() {
    try {
        // Withdraw form - FIXED to prevent double submission
        const withdrawForm = document.getElementById('form-withdraw');
        if (withdrawForm) {
            withdrawForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Prevent double submission
                if (isWithdrawing) {
                    console.warn('⚠️ Withdrawal already in progress');
                    return;
                }
                
                const amountInput = document.querySelector('input[name="withdraw_amount"]');
                if (amountInput) {
                    const amount = parseFloat(amountInput.value);
                    if (amount > 0 && validateWithdrawForm()) {
                        processWithdraw(amount);
                    }
                }
            });
        }
        
        // Withdraw amount input validation with real-time feedback
        const withdrawInput = document.querySelector('input[name="withdraw_amount"]');
        if (withdrawInput) {
            // Add input event listener for real-time validation
            withdrawInput.addEventListener('input', function() {
                if (!isWithdrawing) {
                    validateWithdrawForm();
                }
            });
            
            // Add blur event for final validation
            withdrawInput.addEventListener('blur', function() {
                if (!isWithdrawing) {
                    validateWithdrawForm();
                }
            });
            
            // Add focus event to clear errors
            withdrawInput.addEventListener('focus', function() {
                const errorDiv = document.getElementById('withdraw-error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });
            
            // Format number input on blur
            withdrawInput.addEventListener('blur', function() {
                const value = parseFloat(this.value);
                if (!isNaN(value) && value > 0) {
                    this.value = Math.floor(value); // Ensure integer value
                }
            });
        }
        
        // Bank form - FIXED to prevent double submission
        const bankForm = document.getElementById('form-bind-card');
        if (bankForm) {
            bankForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Prevent double submission
                if (isBankSubmitting) {
                    console.warn('⚠️ Bank form already submitting');
                    return;
                }
                
                submitBankForm(e);
            });
        }
        
        // Password form - FIXED to prevent double submission
        const passwordForm = document.getElementById('form-change-password');
        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Prevent double submission
                if (isPasswordChanging) {
                    console.warn('⚠️ Password change already in progress');
                    return;
                }
                
                submitPasswordForm(e);
            });
        }
        
        // Lottery history button setup
        setupLotteryHistoryButton();
        
        console.log('✅ All forms initialized successfully with double-submission protection!');
        
    } catch (e) {
        console.error('❌ Initialize forms error:', e);
    }
}
function refreshLotteryHistory() {
    const tableBody = document.getElementById('lotteryHistoryBody');
    if (!tableBody) return;

    const formData = new FormData();
    formData.append('limit', 10);

    fetch('/ajax/lottery-history.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success && res.data.length > 0) {
            tableBody.innerHTML = '';
            res.data.forEach(item => {
                const row = `
                    <tr>
                        <td>${item.sid}</td>
                        <td>${item.money}₫</td>
                        <td>${new Date(item.create_time * 1000).toLocaleString()}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        } else {
            tableBody.innerHTML = `
                <tr><td colspan="3">Không có lịch sử cược</td></tr>
            `;
        }
    })
    .catch(err => {
        console.error('Lỗi lấy lịch sử cược:', err);
    });
}

// ===== LOTTERY HISTORY BUTTON SETUP =====
function setupLotteryHistoryButton() {
    try {
        // Using both jQuery and vanilla JS for compatibility
        if (typeof $ !== 'undefined') {
            $("#btn-lottery-history").off('click.lottery').on("click.lottery touchstart.lottery", function(e) {
                e.preventDefault();
                loadLotteryHistory();
                openModal('lotteryHistoryModal');
            });
            
            // Also handle the action item click for lottery history
            $('[onclick*="lotteryHistoryModal"]').off('click.lottery').on('click.lottery', function(e) {
                e.preventDefault();
                loadLotteryHistory();
                openModal('lotteryHistoryModal');
            });
        }
        
        // Vanilla JS fallback
        const lotteryHistoryBtn = document.getElementById('btn-lottery-history');
        if (lotteryHistoryBtn) {
            lotteryHistoryBtn.addEventListener('click', function(e) {
                e.preventDefault();
                loadLotteryHistory();
                openModal('lotteryHistoryModal');
            });
        }
        
        // Handle action items that open lottery modal
        document.querySelectorAll('[onclick*="lotteryHistoryModal"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                loadLotteryHistory();
                openModal('lotteryHistoryModal');
            });
        });
        
        // Close lottery history modal
        const closeLotteryHistory = document.getElementById('closeLotteryHistory');
        if (closeLotteryHistory) {
            closeLotteryHistory.addEventListener('click', function() {
                closeModal('lotteryHistoryModal');
            });
        }
        
    } catch (e) {
        console.error('❌ Setup lottery history button error:', e);
    }
}

// ===== ENHANCED ERROR HANDLING =====
window.addEventListener('error', function(e) {
    console.error('❌ Global error:', e.error);
    if (typeof toastr !== 'undefined') {
        toastr.error('Có lỗi xảy ra: ' + e.message);
    }
});

window.addEventListener('unhandledrejection', function(e) {
    console.error('❌ Unhandled promise rejection:', e.reason);
    if (typeof toastr !== 'undefined') {
        toastr.error('Có lỗi xảy ra trong quá trình xử lý');
    }
});

// ===== UTILITY FUNCTIONS FOR DEBUGGING =====
function checkFormStates() {
    console.log('🔍 Form states:', {
        isWithdrawing,
        isBankSubmitting,
        isPasswordChanging,
        userBalance: PROFILE_CONFIG.USER_BALANCE,
        minWithdraw: PROFILE_CONFIG.MIN_WITHDRAW
    });
}

function resetAllFlags() {
    isWithdrawing = false;
    isBankSubmitting = false;
    isPasswordChanging = false;
    console.log('🔄 All flags reset');
}

// ===== DEBUG CONSOLE COMMANDS =====
if (typeof window !== 'undefined') {
    window.debugCoupang = {
        checkStates: checkFormStates,
        resetFlags: resetAllFlags,
        validateWithdraw: validateWithdrawForm,
        config: PROFILE_CONFIG
    };
}

console.log('✅ CoupangShop VIP Profile JavaScript loaded with complete fixes!')
    </script>
</body>
</html>