<?php

include_once __DIR__ . "/../models/LotteryModel.php";

$cate = LotteryModel::GetAllCategories();

view("header");
layout_header();

view("navbar");


?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Review </title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'coupang-blue': '#0074E4',
            'coupang-orange': '#FF6B00',
            'coupang-red': '#FF3333',
            'coupang-gray': '#F7F8FA',
            'coupang-dark': '#1A1A1A'
          }
        }
      }
    };
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Inter', sans-serif; }
    
    body {
      margin: 0 auto;
      background: linear-gradient(135deg, #1e1e2f, #252540);
      color: #ffffff;
    }
    
    .sidebar { transition: transform 0.3s ease; }
    .sidebar-open { transform: translateX(0); }
    .sidebar-closed { transform: translateX(-100%); }
    .hamburger span { transition: all 0.3s ease; }
    .hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
    .hamburger.active span:nth-child(2) { opacity: 0; }
    .hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(7px, -7px); }
    
    .lottery-card { 
      transition: all 0.3s ease;
      border: 2px solid transparent;
      background: linear-gradient(135deg, #252540, #2d2d44);
    }
    .lottery-card:hover { 
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 116, 228, 0.15);
      border-color: #0074E4;
    }
    
    .progress-step {
      transition: all 0.3s ease;
    }
    .progress-step.active {
      background: linear-gradient(135deg, #0074E4, #FF6B00);
    }
    
    .chart-container {
      background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }
    
    .coupang-card {
      background: linear-gradient(135deg, #252540, #2d2d44);
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0, 116, 228, 0.08);
      
    }

    .rectangle.large {
      background: linear-gradient(145deg, #252540, #2d2d44);
    
      border-radius: 12px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      padding: 1rem;
      text-align: center;
      height: 170px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .rectangle.large:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 116, 228, 0.15);
      border-color: #0074E4;
    }

    .rectangle.large.active {
      background: linear-gradient(145deg, #0074E4, #0066CC);
      border-color: #0074E4;
      color: white;
      transform: translateY(-2px) scale(1.02);
      box-shadow: 0 12px 35px rgba(0, 116, 228, 0.25);
    }

    .rectangle.large.active .odd-text {
      color: #FFD700 !important;
    }

    .progress-bar {
      background: linear-gradient(90deg, #e5e7eb, #f3f4f6);
      border-radius: 6px;
      overflow: hidden;
      position: relative;
      height: 8px;
    }

    .progress-fill {
      height: 100%;
      border-radius: 6px;
      transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    .countdown-timer {
      background: linear-gradient(135deg, #1e3a8a, #3b82f6);
      color: white;
      border-radius: 12px;
      padding: 1rem;
      text-align: center;
      box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
    }

    .bottom-sheet {
      background: linear-gradient(135deg, #252540, #2d2d44);
      border-top-left-radius: 24px;
      border-top-right-radius: 24px;
      box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.12);
      transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal-backdrop {
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
    }

    .modal-content {
      background: linear-gradient(135deg, #252540, #2d2d44);
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      color: white;
    }

    .btn-primary {
      background: linear-gradient(135deg, #0074E4, #0066CC);
      border: none;
      border-radius: 12px;
      padding: 12px 24px;
      color: white;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #0066CC, #0052A3);
      transform: translateY(-1px);
      box-shadow: 0 8px 25px rgba(0, 116, 228, 0.3);
    }

    .btn-success {
      background: linear-gradient(135deg, #10B981, #059669);
      border: none;
      border-radius: 12px;
      padding: 12px 24px;
      color: white;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-danger {
      background: linear-gradient(135deg, #EF4444, #DC2626);
      border: none;
      border-radius: 12px;
      padding: 12px 24px;
      color: white;
      font-weight: 600;
      transition: all 0.3s ease;
    }
  .odd-img {
        width: 150px;
      height: 150px;
      font-weight: 700;
      font-size: 1.1rem;
      color: #FF6B00;
      text-align: center;
    }
    .review-image {
      width: 100px;
      height:100px;
      border-radius: 12px;
      object-fit: cover;
      margin: 0 auto 12px auto;
    
      transition: all 0.3s ease;
    }

    .name-text {
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 8px;
      text-align: center;
      color: #ffffff;
    }

    .odd-text {
      font-weight: 500;
      font-size: 0.7rem;
      color: #00ff5c;
      text-align: center;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      padding: 4px 8px;
      border-radius: 10px;
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

  .badge-success {
    background: #00ff58;
    color: #000000;
}
    .badge-danger { background: #f30000; color: #000000; }
    .badge-warning { background: #f3ee00; color: #000000; }

    #bar0 { background: linear-gradient(90deg, #EF4444, #DC2626); }
    #bar1 { background: linear-gradient(90deg, #3B82F6, #2563EB); }
    #bar2 { background: linear-gradient(90deg, #F59E0B, #D97706); }
    #bar3 { background: linear-gradient(90deg, #10B981, #059669); }

    .balance-value {
      font-size: 1.25rem;
      font-weight: 700;
      color: #10B981;
    }

    #txt-lot-money {
      border: 2px solid rgba(0, 116, 228, 0.2);
      border-radius: 12px;
      padding: 0.75rem 1rem;
      background: linear-gradient(135deg, #252540, #2d2d44);
      transition: all 0.3s ease;
      color: white;
    }

    #txt-lot-money:focus {
      outline: none;
      border-color: #0074E4;
      box-shadow: 0 0 0 3px rgba(0, 116, 228, 0.1);
    }

    .hidden { display: none !important; }
    .translate-y-full { transform: translateY(100%); }

    .heart {
      position: fixed;
      top: 100vh;
      animation: heartFloat 8s linear infinite;
      pointer-events: none;
      z-index: 1000;
    }

    @keyframes heartFloat {
      0% { top: 100vh; opacity: 1; }
      100% { top: -10vh; opacity: 0; }
    }

    .period {
      display: flex;
      align-items: center;
      padding: 15px;
      background: linear-gradient(135deg, #252540, #2d2d44);
      border-radius: 12px;
      margin-bottom: 15px;
      border: 1px solid rgba(0, 116, 228, 0.1);
    }

    .period-number {
      flex: 1;
      font-size: 18px;
      font-weight: 600;
      color: #0074E4;
    }

    .count-down {
      font-size: 20px;
      font-weight: bold;
      color: #10B981;
    }

    .recent {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      background: linear-gradient(135deg, #252540, #2d2d44);
      border-radius: 12px;
      margin-bottom: 15px;
      border: 1px solid rgba(0, 116, 228, 0.1);
    }

    .kuaisan-ball {
      flex: 1;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .res-des {
      padding: 4px 8px;
      border-radius: 6px;
      font-weight: 700;
      font-size: 14px;
      background: linear-gradient(135deg, #0074E4, #0066CC);
      color: white;
    }

    .bar {
      display: flex;
      align-items: center;
      padding: 0 15px;
      height: 60px;
      background: linear-gradient(135deg, #252540, #2d2d44);
      border-top: 1px solid rgba(0, 116, 228, 0.1);
    }

    .bar .left {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .bar .mid {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 10px;
    }

    .bar .right {
      background: linear-gradient(135deg, #0074E4, #0066CC);
      color: white;
      padding: 5px 7px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .bar .right:hover {
      background: linear-gradient(135deg, #0066CC, #0052A3);
      transform: translateY(-1px);
    }

    .wrapper .wrapper {
      background: linear-gradient(135deg, #252540, #2d2d44);
      border-radius: 12px 12px 0 0;
      padding: 20px;
      transition: transform 0.3s ease;
    }

    .wrapper .wrapper.active {
      transform: translateY(-100%);
    }

    .bet-number {
      color: #0074E4;
      font-weight: 700;
      font-size: 16px;
    }

    .amount-wrapper input {
      background: linear-gradient(135deg, #1e1e2f, #252540);
      border: 1px solid rgba(0, 116, 228, 0.2);
      border-radius: 8px;
      padding: 8px 12px;
      color: white;
      text-align: center;
    }

    .amount-wrapper input:focus {
      outline: none;
      border-color: #0074E4;
    }

    .item {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 15px;
    }

    .item .label {
      font-weight: 600;
      color: #ffffff;
      min-width: 120px;
    }

    .item .bet-number {
      flex: 1;
    }

    .fa {
      cursor: pointer;
      color: #0074E4;
      font-size: 18px;
    }
  </style>
</head>
<body class="bg-gray-900 min-h-screen font-sans">
  <div id="app" class="w-full max-w-[500px] mx-auto min-h-screen bg-gray-900 shadow-lg">
    <!-- Header -->
    <header id="mainHeader" class="bg-gray-800 shadow-sm border-b border-gray-700 sticky top-0 z-20">
      <div class="px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button id="backButton" class="hidden text-coupang-blue hover:text-coupang-orange transition-colors" onclick="goBackToMain()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </button>
          <button id="hamburger" class="hamburger p-2 text-coupang-blue" aria-label="Toggle navigation">
            <span class="block w-5 h-0.5 bg-current mb-1"></span>
            <span class="block w-5 h-0.5 bg-current mb-1"></span>
            <span class="block w-5 h-0.5 bg-current"></span>
          </button>
          <h1 class="text-lg font-bold text-white">Hệ thống đánh giá COUPANG</h1>
        </div>
        <div class="bg-coupang-blue text-white px-2 py-1 rounded-full text-xs font-medium">
          Premium
        </div>
      </div>
    </header>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black/50 hidden z-30"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar sidebar-closed fixed top-0 left-0 w-72 h-full bg-gray-800 shadow-xl z-40 overflow-y-auto">
      <div class="p-4">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-white">Danh mục</h2>
          <button id="close-sidebar" class="text-2xl text-gray-400">×</button>
        </div>
        
        <!-- Progress Indicator -->
        <div class="mb-6">
          <h3 class="text-sm font-semibold text-gray-400 mb-3">Tiến độ đánh giá</h3>
          <div class="flex items-center justify-between mb-2">
            <div class="progress-step active w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold">1</div>
            <div class="flex-1 h-1 bg-gray-600 mx-1"><div class="h-full bg-coupang-blue w-3/4"></div></div>
            <div class="progress-step w-6 h-6 rounded-full bg-gray-600 flex items-center justify-center text-gray-400 text-xs font-bold">2</div>
            <div class="flex-1 h-1 bg-gray-600 mx-1"></div>
            <div class="progress-step w-6 h-6 rounded-full bg-gray-600 flex items-center justify-center text-gray-400 text-xs font-bold">3</div>
          </div>
          <div class="flex justify-between text-xs text-gray-400">
            <span>Chọn</span>
            <span>Đánh giá</span>
            <span>Hoàn thành</span>
          </div>
        </div>

        <!-- Categories -->
        <nav class="space-y-2" id="category-list">
          <!-- Categories will be populated by JavaScript -->
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <div id="mainView" class="p-4 space-y-4">
      <!-- Chart Section -->
      <div class="chart-container rounded-xl p-4 text-white">
        <div class="flex items-center justify-between mb-3">
          <div>
            <h2 class="text-lg font-bold">Biểu đồ kết quả</h2>
            <p class="text-blue-200 text-sm">30 phiên gần nhất</p>
          </div>
          <div class="flex gap-2 text-xs">
            <div class="flex items-center gap-1">
              <div class="w-2 h-2 bg-red-400 rounded-full"></div>
              <span>A</span>
            </div>
            <div class="flex items-center gap-1">
              <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
              <span>B</span>
            </div>
            <div class="flex items-center gap-1">
              <div class="w-2 h-2 bg-green-400 rounded-full"></div>
              <span>C</span>
            </div>
            <div class="flex items-center gap-1">
              <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
              <span>D</span>
            </div>
          </div>
        </div>
        <div class="h-48">
          <canvas id="resultChart"></canvas>
        </div>
      </div>

      <!-- Lottery Cards Grid -->
      <div class="coupang-card p-4">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-white">Danh sách đánh giá</h2>
          <div class="text-sm text-gray-400">
            <span id="itemCount">0</span> mục
          </div>
        </div>
        <div id="mainContent" class="grid grid-cols-2 gap-3"></div>
      </div>

      <!-- History Table -->
      <div class="coupang-card p-4">
        <h3 class="text-lg font-bold text-white mb-3">Lịch sử đánh giá</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="border-b border-gray-600">
                <th class="text-left py-2 px-2 font-semibold text-gray-300">Phiên</th>
                <th class="text-left py-2 px-2 font-semibold text-gray-300">Loại</th>
                <th class="text-left py-2 px-2 font-semibold text-gray-300">Điểm</th>
                <th class="text-left py-2 px-2 font-semibold text-gray-300">Tỷ lệ</th>
                <th class="text-left py-2 px-2 font-semibold text-gray-300">Kết quả</th>
              </tr>
            </thead>
            <tbody id="tbl-history" class="divide-y divide-gray-600"></tbody>
          </table>
        </div>
      </div>
    </div>

   


<?php

endpage();

?>

<script>
    var items = [];

    function openReview(key, id) {
        window.location.href = `review-detail.html?key=${key}&id=${id}`
    }

    function showReview(id) {
        let need_to_render = id == -1 ? items : items.filter((v, i) => v.cate_id == id)

        $("#mainContent").empty();

        need_to_render.forEach(o => {
            let html = `<div
                class="review-card"
                onclick="openReview('${o.key}', ${o.id})">
                <img
                    src="${o.image}"
                    alt="${o.name}" />
                <span>${o.name}</span>
            </div>`;
            $("#mainContent").append(html);
        });
    }

    $(document).ready(function() {
        $.ajax({
            type: "POST",
            url: "<?= route("ajax/index.php") ?>",
            data: {
                action_type: "get_lottery_items"
            },
            dataType: "json",
            success: function(response) {
                items = response.data;
                showReview(-1);
            }
        });

        function render_iswin(s) {
            if (s == 1) return `<span class="badge badge-success">Đúng</span>`;
            if (s == 2) return `<span class="badge badge-danger">Sai</span>`;
            return `<span class="badge badge-warning">Chưa mở</span>`;
        }

        function render_lottery_history_status(s) {
            if (s == 1) return `<span class="badge badge-danger">Chưa mở</span>`;
            if (s == 0) return `<span class="badge badge-success">Đã mở</span>`;
            return "Unknown";
        }

        $.ajax({
            type: "POST",
            url: "<?= route("ajax/index.php") ?>",
            data: {
                action_type: "my_lottery_history_1",
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    let {
                        data
                    } = response;
                    $("#tbl-history").empty();

                    data.forEach(e => {
                        let html = `<tr>
                            <td scope="row">${e.sid}</td>
                            <td>${e.type}</td>
                            <td>${e.money}</td>
                            <td>${e.proportion}</td>
                          <td><span class="font-bold">${render_iswin(e.is_win)}</span></td>

                        </tr>`
                        /*<th>Phiên</th>
                            <th>Loại</th>
                            <th>Tiền</th>
                            <th>Tỉ lệ</th>
                            <th>Kết quả</th>
                            <th>Trước khi cược</th>
                            <th>Sau khi cược</th> */
                        $("#tbl-history").append(html);
                    })

                    $("#modalUserHistory").modal("show");
                }
            }
        });
    });
    
    // Cập nhật hệ thống để sử dụng tbl-lottery-history
class LotteryHistoryManager {
    constructor() {
        this.init();
    }

    init() {
        this.loadLotteryHistory();
        this.startRealTimeHistoryUpdates();
        this.setupHistoryModal();
    }

    // Tải lịch sử kết quả lottery
    loadLotteryHistory() {
        $.ajax({
            type: "POST",
            url: "<?= route('ajax/index.php') ?>",
            data: {
                action_type: "get_lottery_history"
            },
            dataType: "json",
            success: (response) => {
                if (response.success) {
                    this.updateLotteryHistoryTable(response.data);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error loading lottery history:', error);
            }
        });
    }

    // Cập nhật bảng lịch sử lottery
    updateLotteryHistoryTable(data) {
        const tbody = $('#tbl-lottery-history');
        tbody.empty();

        data.forEach(item => {
            const html = `
                <tr>
                    <td class="py-2 px-2 text-white">${item.session}</td>
                    <td class="py-2 px-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-${this.getResultColor(item.result)}-100 text-${this.getResultColor(item.result)}-800">
                            ${item.result}
                        </span>
                    </td>
                    <td class="py-2 px-2">
                        ${this.renderLotteryStatus(item.status)}
                    </td>
                </tr>
            `;
            tbody.append(html);
        });
    }

    // Lấy màu cho kết quả
    getResultColor(result) {
        const colors = {
            'A': 'red',
            'B': 'blue', 
            'C': 'green',
            'D': 'yellow'
        };
        return colors[result] || 'gray';
    }

    // Render trạng thái lottery
    renderLotteryStatus(status) {
        const statusConfig = {
            0: { class: 'badge-success', text: 'Đã mở', color: 'green' },
            1: { class: 'badge-danger', text: 'Chưa mở', color: 'red' },
            2: { class: 'badge-warning', text: 'Đang chờ', color: 'yellow' }
        };

        const config = statusConfig[status] || { class: 'badge-secondary', text: 'Không xác định', color: 'gray' };
        
        return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-${config.color}-100 text-${config.color}-800">
                    ${config.text}
                </span>`;
    }

    // Cập nhật realtime
    startRealTimeHistoryUpdates() {
        setInterval(() => {
            this.checkForNewResults();
        }, 10000); // Cập nhật mỗi 10 giây
    }

    // Kiểm tra kết quả mới
    checkForNewResults() {
        const lastSession = this.getLastSessionFromTable();
        
        $.ajax({
            type: "POST",
            url: "<?= route('ajax/index.php') ?>",
            data: {
                action_type: "get_new_lottery_results",
                last_session: lastSession
            },
            dataType: "json",
            success: (response) => {
                if (response.success && response.data.length > 0) {
                    this.addNewHistoryResults(response.data);
                    this.showNewResultNotification(response.data);
                }
            }
        });
    }

    // Lấy session cuối cùng từ bảng
    getLastSessionFromTable() {
        const firstRow = $('#tbl-lottery-history tr:first-child td:first-child');
        return firstRow.length ? parseInt(firstRow.text()) : 0;
    }

    // Thêm kết quả mới vào đầu bảng
    addNewHistoryResults(newResults) {
        const tbody = $('#tbl-lottery-history');
        
        newResults.forEach(item => {
            const html = `
                <tr class="new-result animate-pulse">
                    <td class="py-2 px-2 text-white font-bold">${item.session}</td>
                    <td class="py-2 px-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-${this.getResultColor(item.result)}-100 text-${this.getResultColor(item.result)}-800">
                            ${item.result}
                        </span>
                    </td>
                    <td class="py-2 px-2">
                        ${this.renderLotteryStatus(item.status)}
                    </td>
                </tr>
            `;
            tbody.prepend(html);
        });

        // Xóa hiệu ứng sau 3 giây
        setTimeout(() => {
            $('.new-result').removeClass('animate-pulse new-result');
        }, 3000);

        // Giữ chỉ 50 dòng gần nhất
        if (tbody.find('tr').length > 50) {
            tbody.find('tr:gt(49)').remove();
        }
    }

    // Hiển thị thông báo kết quả mới
    showNewResultNotification(results) {
        results.forEach(result => {
            toastr.success(`Kết quả phiên ${result.session}: ${result.result}`, 'Kết quả mới!', {
                timeOut: 5000,
                positionClass: 'toast-top-right'
            });
        });
    }

    // Thiết lập modal lịch sử
    setupHistoryModal() {
        // Mở modal khi click button
        $('#btn-lottery-history').click(() => {
            $('#modalLotteryHistory').removeClass('hidden');
            this.loadDetailedHistory();
        });

        // Đóng modal
        $('#closeLotteryHistory').click(() => {
            $('#modalLotteryHistory').addClass('hidden');
        });

        // Đóng modal khi click overlay
        $('#modalLotteryHistory').click((e) => {
            if (e.target.id === 'modalLotteryHistory') {
                $('#modalLotteryHistory').addClass('hidden');
            }
        });
    }

    // Tải lịch sử chi tiết cho modal
    loadDetailedHistory() {
        $.ajax({
            type: "POST",
            url: "<?= route('ajax/index.php') ?>",
            data: {
                action_type: "get_detailed_lottery_history",
                limit: 100
            },
            dataType: "json",
            success: (response) => {
                if (response.success) {
                    this.updateDetailedHistoryTable(response.data);
                }
            }
        });
    }

    // Cập nhật bảng lịch sử chi tiết trong modal
    updateDetailedHistoryTable(data) {
        const tbody = $('#tbl-lottery-history');
        tbody.empty();

        data.forEach(item => {
            const html = `
                <tr class="hover:bg-gray-700 transition-colors">
                    <td class="py-3 px-2 text-white font-medium">${item.session}</td>
                    <td class="py-3 px-2">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 bg-${this.getResultColor(item.result)}-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                ${item.result}
                            </span>
                            <span class="text-gray-300">${item.result_value || 'N/A'}</span>
                        </div>
                    </td>
                    <td class="py-3 px-2">
                        ${this.renderLotteryStatus(item.status)}
                    </td>
                </tr>
            `;
            tbody.append(html);
        });
    }

    // Lấy thống kê từ lịch sử
    getHistoryStats() {
        const rows = $('#tbl-lottery-history tr');
        const stats = { A: 0, B: 0, C: 0, D: 0, total: 0 };

        rows.each(function() {
            const result = $(this).find('td:nth-child(2)').text().trim();
            if (result && stats.hasOwnProperty(result)) {
                stats[result]++;
                stats.total++;
            }
        });

        return stats;
    }

    // Cập nhật thống kê hiển thị
    updateStatsDisplay() {
        const stats = this.getHistoryStats();
        
        if (stats.total > 0) {
            Object.keys(stats).forEach(key => {
                if (key !== 'total') {
                    const percentage = Math.round((stats[key] / stats.total) * 100);
                    $(`#stats-${key}`).text(`${stats[key]} (${percentage}%)`);
                }
            });
        }
    }

    // Export lịch sử ra CSV
    exportToCSV() {
        const data = [];
        $('#tbl-lottery-history tr').each(function() {
            const row = [];
            $(this).find('td').each(function() {
                row.push($(this).text().trim());
            });
            if (row.length > 0) {
                data.push(row);
            }
        });

        const csvContent = [
            ['Phiên', 'Kết quả', 'Trạng thái'],
            ...data
        ].map(row => row.join(',')).join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `lottery_history_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    // Lọc lịch sử theo kết quả
    filterByResult(result) {
        const rows = $('#tbl-lottery-history tr');
        
        if (result === 'all') {
            rows.show();
        } else {
            rows.each(function() {
                const rowResult = $(this).find('td:nth-child(2)').text().trim();
                if (rowResult === result) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    }

    // Tìm kiếm theo session
    searchBySession(sessionId) {
        const rows = $('#tbl-lottery-history tr');
        
        if (!sessionId) {
            rows.show();
            return;
        }

        rows.each(function() {
            const rowSession = $(this).find('td:first-child').text().trim();
            if (rowSession.includes(sessionId)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
}

// Khởi tạo khi trang load
$(document).ready(function() {
    const lotteryHistory = new LotteryHistoryManager();
    
    // Thêm các event listeners
    $('#export-history').click(() => {
        lotteryHistory.exportToCSV();
    });

    $('#filter-result').change(function() {
        lotteryHistory.filterByResult($(this).val());
    });

    $('#search-session').on('input', function() {
        lotteryHistory.searchBySession($(this).val());
    });

    // Cập nhật thống kê mỗi 30 giây
    setInterval(() => {
        lotteryHistory.updateStatsDisplay();
    }, 30000);
});

// Hàm tiện ích để render badge
function renderLotteryHistoryStatus(status) {
    const statusMap = {
        0: '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Đã mở</span>',
        1: '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Chưa mở</span>',
        2: '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Đang chờ</span>'
    };
    
    return statusMap[status] || '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Không xác định</span>';
}
   </script>

<script>
var items = [];

function openReview(key, id) {
    window.location.href = `review-detail.html?key=${key}&id=${id}`;
}

$(document).ready(function() {
    // Khởi tạo biểu đồ với dữ liệu có sẵn
    initChart();
    
    // Cập nhật kết quả thật mỗi 3 phút (180 giây)
    setInterval(updateRealResult, 180000);
    
    // Cập nhật countdown mỗi giây
    setInterval(updateCountdown, 1000);
    
    // Load danh sách items
    $.ajax({
        type: "POST",
        url: "<?= route("ajax/index.php") ?>",
        data: { action_type: "get_lottery_items" },
        dataType: "json",
        success: function(response) {
            items = response.data;
            showReview(-1);
        }
    });
    
    // Load lịch sử
    $.ajax({
        type: "POST",
        url: "<?= route("ajax/index.php") ?>",
        data: { action_type: "my_lottery_history_1" },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                $("#tbl-history").empty();
                response.data.forEach(e => {
                    let html = `<tr>
                        <td>${e.sid}</td>
                        <td>${e.type}</td>
                        <td>${e.money}</td>
                        <td>${e.proportion}</td>
                        <td>${render_iswin(e.is_win)}</td>
                    </tr>`;
                    $("#tbl-history").append(html);
                });
            }
        }
    });
});

// Countdown timer
let timeRemaining = 180; // 3 phút = 180 giây
let currentSession = 1;

function updateCountdown() {
    if (timeRemaining > 0) {
        timeRemaining--;
        
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        
        $('#count-down').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}:00`);
        $('#now-session').text(currentSession);
    } else {
        // Hết thời gian, tạo kết quả mới
        updateRealResult();
        timeRemaining = 180; // Reset về 3 phút
        currentSession++;
    }
}

function initChart() {
    const ctx = document.getElementById('resultChart').getContext('2d');
    
    // Tạo dữ liệu lịch sử có sẵn (30 kết quả cũ)
    const sessions = [];
    const dataA = [];
    const dataB = [];
    const dataC = [];
    const dataD = [];
    
    for (let i = 1; i <= 30; i++) {
        sessions.push(`S${i}`);
        dataA.push(Math.random() * 3 + 1);
        dataB.push(Math.random() * 3 + 1);
        dataC.push(Math.random() * 3 + 1);
        dataD.push(Math.random() * 3 + 1);
    }
    
    window.resultChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: sessions,
            datasets: [{
                label: 'A',
                data: dataA,
                borderColor: '#EF4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                borderWidth: 2,
                fill: false,
                pointRadius: 0,
                pointHoverRadius: 3
            }, {
                label: 'B',
                data: dataB,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                borderWidth: 2,
                fill: false,
                pointRadius: 0,
                pointHoverRadius: 3
            }, {
                label: 'C',
                data: dataC,
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                borderWidth: 2,
                fill: false,
                pointRadius: 0,
                pointHoverRadius: 3
            }, {
                label: 'D',
                data: dataD,
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4,
                borderWidth: 2,
                fill: false,
                pointRadius: 0,
                pointHoverRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4,
                    grid: { 
                        color: 'rgba(255, 255, 255, 0.1)',
                        lineWidth: 0.5
                    },
                    ticks: { 
                        color: 'rgba(255, 255, 255, 0.7)',
                        stepSize: 0.5
                    }
                },
                x: {
                    grid: { 
                        color: 'rgba(255, 255, 255, 0.1)',
                        lineWidth: 0.5
                    },
                    ticks: { 
                        color: 'rgba(255, 255, 255, 0.7)',
                        maxTicksLimit: 20 // Giới hạn số label hiển thị
                    }
                }
            },
            elements: {
                point: {
                    radius: 0, // Ẩn các điểm để giống Image 1
                    hoverRadius: 3
                }
            }
        }
    });
}

function updateRealResult() {
    if (window.resultChart) {
        // Tạo kết quả mới sau 3 phút
        const newA = Math.random() * 3 + 1;
        const newB = Math.random() * 3 + 1;
        const newC = Math.random() * 3 + 1;
        const newD = Math.random() * 3 + 1;
        
        // Thêm vào biểu đồ
        window.resultChart.data.datasets[0].data.push(newA);
        window.resultChart.data.datasets[1].data.push(newB);
        window.resultChart.data.datasets[2].data.push(newC);
        window.resultChart.data.datasets[3].data.push(newD);
        
        const newSession = `S${window.resultChart.data.labels.length + 1}`;
        window.resultChart.data.labels.push(newSession);
        
        // Giữ chỉ 50 kết quả gần nhất
        if (window.resultChart.data.labels.length > 50) {
            window.resultChart.data.labels.shift();
            window.resultChart.data.datasets.forEach(dataset => {
                dataset.data.shift();
            });
        }
        
        window.resultChart.update();
        
        // Cập nhật bảng lịch sử
        updateHistoryTable(newSession, newA, newB, newC, newD);
        
        // Hiển thị thông báo kết quả mới
        showNewResultNotification(newSession, newA, newB, newC, newD);
    }
}

function showNewResultNotification(session, a, b, c, d) {
    const values = { A: a, B: b, C: c, D: d };
    const winner = Object.keys(values).reduce((a, b) => values[a] > values[b] ? a : b);
    
    // Hiển thị toast notification
    if (typeof toastr !== 'undefined') {
        toastr.success(`Kết quả ${session}: ${winner} thắng!`, 'Kết quả mới!', {
            timeOut: 10000,
            positionClass: 'toast-top-right'
        });
    } else {
        // Fallback alert
        alert(`Kết quả mới ${session}: ${winner} thắng!`);
    }
}

function updateHistoryTable(session, a, b, c, d) {
    const values = { A: a, B: b, C: c, D: d };
    const winner = Object.keys(values).reduce((a, b) => values[a] > values[b] ? a : b);
    
    const newRow = `
        <tr>
            <td class="py-2 px-2 text-white">${session}</td>
            <td class="py-2 px-2 text-white">${winner}</td>
            <td class="py-2 px-2 text-white">${Math.round(Math.random() * 100)}</td>
            <td class="py-2 px-2 text-white">${Math.round(Math.random() * 100)}%</td>
            <td class="py-2 px-2">
                <span class="badge badge-success">Đã mở</span>
            </td>
        </tr>
    `;
    
    $('#tbl-history').prepend(newRow);
    if ($('#tbl-history tr').length > 10) {
        $('#tbl-history tr:last').remove();
    }
    
    $('#tbl-lottery-history').prepend(newRow);
    if ($('#tbl-lottery-history tr').length > 20) {
        $('#tbl-lottery-history tr:last').remove();
    }
}

function showReview(id) {
    let need_to_render = id == -1 ? items : items.filter((v, i) => v.cate_id == id);
    $("#mainContent").empty();
    $("#itemCount").text(need_to_render.length);
    
    need_to_render.forEach(o => {
        let html = `
            <div class="rectangle large" onclick="openReview('${o.key}', ${o.id})">
                <img src="${o.image}" alt="${o.name}" class="review-image" />
                <div class="name-text">${o.name}</div>
                <div class="odd-text">Đang hoạt động</div>
            </div>
        `;
        $("#mainContent").append(html);
    });
}

function render_iswin(s) {
    if (s == 1) return `<span class="badge badge-success">Đúng</span>`;
    if (s == 2) return `<span class="badge badge-danger">Sai</span>`;
    return `<span class="badge badge-warning">Chưa mở</span>`;
}

// Menu toggle
$("#hamburger").click(function() {
    $("#sidebar").toggleClass("sidebar-closed sidebar-open");
    $("#overlay").toggleClass("hidden");
});

$("#overlay, #close-sidebar").click(function() {
    $("#sidebar").removeClass("sidebar-open").addClass("sidebar-closed");
    $("#overlay").addClass("hidden");
});

// Modal controls
$("#btn-lottery-history").click(function() {
    $("#modalLotteryHistory").removeClass("hidden");
});

$("#closeLotteryHistory").click(function() {
    $("#modalLotteryHistory").addClass("hidden");
});
</script>
