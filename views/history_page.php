<?php
// views/history_page.php

// Lấy tham số & chuẩn bị API
$id  = (int)($_GET['id'] ?? $_GET['lid'] ?? 0);
$api = function_exists('route') ? route('ajax/index.php') : '/ajax/index.php';

// Khung chung của site (đã có <html><head>...> trong header.php)
if (function_exists('view')) {
  view('header');
  layout_header();
  view('navbar'); // dùng đúng navbar có sẵn
  // Nếu bạn đã tách modal lịch sử ra partial, bật dòng dưới:
  // include __DIR__ . '/partials/history_modal.php';
}
?>

<style>
      * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            min-height: 100dvh;
            height: auto !important;
            overflow-x: hidden;
            overflow-y: auto;
            background: url('https://i.ibb.co/yFb4vr9j/BgPCBD.png') no-repeat center center fixed;
            background-size: cover;
        }

        .app-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            min-height: 100vh;
            background: linear-gradient(135deg, #0066CC, #4A90E2, #E53E3E);
            box-shadow: 0 0 50px rgba(0, 102, 204, 0.3);
            position: relative;
        }

        /* Header Styles */
        .vinamilk-header {
            background: linear-gradient(135deg, #0066CC 0%, #4A90E2 100%);
            padding: 1.5rem;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 20;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .vinamilk-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #FFFFFF, #FFF8DC);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .vinamilk-subheader {
            background: linear-gradient(135deg, #E53E3E 0%, #FF6B6B 100%);
            padding: 0.75rem;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 600;
            color: #FFFFFF;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        }

        /* Search Bar */
        .search-bar {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin: 1rem;
            border: 2px solid rgba(0, 102, 204, 0.2);
            backdrop-filter: blur(10px);
        }

        .search-bar input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid rgba(0, 102, 204, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.95);
            font-size: 0.9rem;
            font-weight: 600;
            color: #003D7A;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            border-color: #E53E3E;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.15);
        }

        /* Table Header */
        .table-header {
            background: linear-gradient(145deg, #FFFFFF 0%, #F3F4F6 100%);
            border-bottom: 2px solid #0066CC;
            padding: 0.75rem;
            display: grid;
            grid-template-columns: 1fr 2fr;
            font-size: 0.85rem;
            font-weight: 700;
            color: #003D7A;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Main Content */
        .main-content {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 248, 220, 0.9));
            border-radius: 16px;
            margin: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            border: 2px solid rgba(0, 102, 204, 0.2);
        }

        /* Table Styles */
        .history-table {
            width: 100%;
            border-collapse: collapse;
            background: #FFFFFF;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.1);
        }

        .history-table tr {
            border-bottom: 1px solid rgba(0, 102, 204, 0.1);
            transition: all 0.3s ease;
        }

        .history-table tr:hover {
            background: linear-gradient(145deg, #F3F4F6, #FFF8DC);
            transform: translateY(-2px);
        }

        .history-table td {
            padding: 1rem;
            font-size: 0.85rem;
            color: #003D7A;
            font-weight: 600;
            text-align: center;
        }

        .result-digit {
            display: inline-block;
            width: 28px;
            height: 28px;
            line-height: 28px;
            text-align: center;
            border-radius: 4px;
            background: linear-gradient(135deg, #00CC66, #059669);
            color: #FFFFFF;
            font-weight: 800;
            margin: 2px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .result-digit:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 204, 102, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #4A90E2;
            font-size: 1rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            border: 2px solid rgba(0, 102, 204, 0.2);
        }

        /* Loading Spinner */
        .loading-spinner {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #0066CC;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .loading-spinner svg {
            animation: vinamilk-spin 1s linear infinite;
        }

        @keyframes vinamilk-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes vinamilk-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .vinamilk-header h1 {
                font-size: 1.25rem;
            }

            .vinamilk-subheader {
                font-size: 0.8rem;
            }

            .table-header {
                font-size: 0.75rem;
            }

            .history-table td {
                font-size: 0.75rem;
                padding: 0.75rem;
            }

            .result-digit {
                width: 24px;
                height: 24px;
                line-height: 24px;
                font-size: 0.7rem;
            }

            .search-bar {
                margin: 0.75rem;
            }
        }
.gradient-bg {
  background: linear-gradient(135deg, #00a9ff 0%, #00aaff 100%);
}
.orange-header {
  background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
}
 .background{: #3498db;}
</style>

<!-- Header với gradient xanh -->
<div class="gradient-bg text-black text-center py-3 px-4" style="max-width: 500px; margin: 0 auto;">
  <h1 class="text-lg font-bold">Xử hướng</h1>
</div>

<!-- Orange header bar -->
<div class="orange-header text-white text-center py-2 px-4" style="max-width: 500px; margin: 0 auto;">
  <div class="text-black font-semibold">NUTIFOOD - TH TRUEMILK - VINAMILK</div>
</div>

<!-- Table Header -->
<div class="bg-gray-800 text-white" style="max-width: 500px; margin: 0 auto;">
  <div class="grid grid-cols-2 text-sm font-medium">
    <div class="py-2 px-3">Đơn số</div>
    <div class="py-2 px-3">Kết quả</div>
  </div>
</div>

<!-- TABLE -->
<main class="p-3" style="max-width:500px;margin:0 auto;background:#f6f8fb;">
  <div id="empty" class="text-center text-slate-500 py-10 hidden">Chưa có dữ liệu lịch sử.</div>

  <div id="wrap" class="rounded-lg overflow-hidden shadow">
    <table class="w-full text-sm">
      <tbody id="tbody" class="bg-white"></tbody>
    </table>
  </div>

  <div id="loading" class="text-center text-sky-600 py-4">
    <span class="inline-flex items-center gap-2">
      <svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-20"></circle>
        <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" class="opacity-80"></path>
      </svg>
      Đang tải dữ liệu...
    </span>
  </div>
</main>

<!-- Nếu header.php CHƯA có jQuery/Toastr/Tailwind thì nạp nhanh ở cuối body: -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link  href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
(function($){
  // Tránh lỗi token: luôn dùng json_encode từ PHP
  const API = <?= json_encode($api, JSON_UNESCAPED_UNICODE) ?>;
  let lid    = <?= json_encode($id,  JSON_UNESCAPED_UNICODE) ?>;

  // Fallback lấy từ query nếu chưa có
  if (!lid || lid === 0) {
    const qs = new URLSearchParams(location.search);
    lid = parseInt(qs.get('id') || qs.get('lid') || '0', 10) || 0;
  }

  const MAX_ROWS = 50;
  let cache = []; // dữ liệu hiện tại

// Hiển thị đẹp 5 số: "1 2 3 4 5"
function fmtDigits(s) {
  if (!s) return 'Chưa có';
  return s.split('').join(' ');
}

  function badge(sid, raw){
    const current = (window.current_session || localStorage.getItem('currentSession') || '').toString();
    if (current && String(sid) === String(current)) return '<span class="badge badge-now">Hiện tại</span>';
    const opened = raw && /\d/.test(raw) && raw.replace(/\D/g,'').length >= 5;
    if (opened) return '<span class="badge badge-open">Đã mở</span>';
    return '<span class="badge badge-wait">Chưa mở</span>';
  }

// Chuẩn hoá 1 item: lấy sid, map A/B/C/D -> mã 5 số trong session_codes
function norm(item) {
  const sid =
    item.sid ?? item.session ?? item.session_id ?? item.period ?? '';

  const rawRes = String(item.result ?? item.code ?? item.kq ?? item.numbers ?? '');
  const letters = (rawRes.match(/[A-D]/gi) || []).map(ch => ch.toLowerCase());

  // --- Lấy map mã 5 số ---
  let codeMap = null;
  let sc = item.session_codes ?? item.sessionCodes ?? item.codes ?? null;

  if (sc) {
    if (typeof sc === 'string') {
      // thử parse JSON trước
      try { sc = JSON.parse(sc); } catch (_) {
        // fallback: tách số từ chuỗi "a:12345,b:67890" hoặc "12345,67890,..."
        const nums = sc.match(/\d{3,}/g) || [];
        if (nums.length) {
          const keys = ['a','b','c','d','e','f'];
          codeMap = {};
          nums.forEach((n, i) => codeMap[keys[i]] = n.replace(/\D/g,'').slice(-5));
        }
      }
    }
    if (typeof sc === 'object' && sc) {
      codeMap = {};
      Object.keys(sc).forEach(k => {
        const v = '' + sc[k];
        const num = (v.match(/\d/g) || []).slice(-5).join('');
        if (num) codeMap[String(k).toLowerCase()] = num;
      });
    }
  }

  // --- dựng danh sách mã cần hiển thị ---
  let digitsList = [];
  if (codeMap && letters.length) {
    letters.forEach(l => { if (codeMap[l]) digitsList.push(codeMap[l]); });
  }

  // nếu không map được, thử bốc 5 số từ bất cứ nơi nào trong raw
  if (!digitsList.length) {
    const fromRaw = (rawRes.match(/\d/g) || []).slice(-5).join('');
    if (fromRaw) digitsList = [fromRaw];
  }

  return { sid, raw: rawRes, digitsList };
}

// Hiển thị
function render(rows){
  const $tb = $('#tbody').empty();
  if (!rows.length) {
    $('#wrap').addClass('hidden');
    $('#empty').removeClass('hidden');
    return;
  }
  $('#empty').addClass('hidden');
  $('#wrap').removeClass('hidden');

  rows.forEach(it=>{
    let show;
    if (it.digitsList && it.digitsList.length) {
      // Hiển thị mã số 5 chữ số
      show = it.digitsList.map(x => {
        const digits = x.split('').map(d => 
          `<span style="display:inline-block;width:24px;height:24px;line-height:24px;text-align:center;border:1px solid #e2e8f0;border-radius:3px;margin:1px;background:white;font-weight:600;font-size:12px;color:#1e293b;">${d}</span>`
        ).join('');
        return `<div style="margin-bottom:4px;">${digits}</div>`;
      }).join('');
    } else {
      // Nếu chưa có mã số, hiển thị đáp án gốc
      show = `<span class="font-semibold text-amber-600">${(it.raw || 'CHƯA CÓ').toUpperCase()}</span>`;
    }

    $tb.append(`
      <tr class="row border-b border-slate-100">
        <td class="px-3 py-2 font-semibold text-slate-700">${it.sid}</td>
        <td class="px-3 py-2">${show}</td>
      </tr>
    `);
  });
}

function applyFilter(){
  const q = ($('#q').val() || '').trim().toLowerCase();
  if (!q) return render(cache);
  const fil = cache.filter(it =>
    String(it.sid).toLowerCase().includes(q) ||
    (it.digitsList || []).join(',').toLowerCase().includes(q) ||
    String(it.raw).toLowerCase().includes(q)
  );
  render(fil);
}

  function showLoading(v){ $('#loading').toggleClass('hidden', !v); }

  // Tải lần đầu
  function loadInitial(){
    if (!lid){
      showLoading(false);
      $('#empty').removeClass('hidden').text('Thiếu mã sản phẩm (lid).');
      return;
    }
    showLoading(true);
    $.ajax({
      type: 'POST', url: API,
      data: { action_type: 'get_lottery_history', lid, limit: MAX_ROWS },
      dataType: 'json', timeout: 10000
    })
    .done(resp=>{
      if (resp && resp.success && Array.isArray(resp.data)){
        cache = resp.data.map(norm).filter(x=>x.sid);
        render(cache);
      } else {
        cache = []; render(cache);
        toastr?.info('Chưa có dữ liệu lịch sử');
      }
    })
    .fail(()=> toastr?.error('Lỗi kết nối khi tải lịch sử'))
    .always(()=> showLoading(false));
  }

  // Poll phiên mới hơn phiên đầu tiên
  function pollNew(){
    if (!lid || !cache.length) return;
    const firstSid = parseInt(String(cache[0].sid).replace(/\D/g,''),10) || 0;
    if (!firstSid) return;
    $.ajax({
      type: 'POST', url: API,
      data: { action_type: 'get_new_lottery_results', lid, last_session: firstSid },
      dataType: 'json', timeout: 8000
    })
    .done(resp=>{
      if (resp && resp.success && Array.isArray(resp.data) && resp.data.length){
        const items = resp.data.map(norm).filter(x=>x.sid);
        if (!items.length) return;
        cache = [...items, ...cache].slice(0, MAX_ROWS);
        applyFilter();
        toastr?.success(`Có ${items.length} phiên mới`);
      }
    });
  }

  // Events & init
  $('#btnRefresh').on('click', loadInitial);
  $('#q').on('input', applyFilter);
  
  // Tạo dữ liệu mẫu với mã số để test
  cache = [
    { sid: '20250816407', raw: 'CHƯA CÓ', digitsList: [] },
    { sid: '20250816406', raw: 'D,B', digitsList: ['15432', '89009'] },
    { sid: '20250816405', raw: 'A,D', digitsList: ['23278', '01543'] },
    { sid: '20250816404', raw: 'C,B', digitsList: ['86193', '23458'] },
    { sid: '20250816403', raw: 'B,C', digitsList: ['90123', '45123'] },
    { sid: '20250816402', raw: 'B,A', digitsList: ['98765', '01234'] },
    { sid: '20250816401', raw: 'B,C', digitsList: ['34375', '67562'] },
    { sid: '20250816400', raw: 'B,A', digitsList: ['25440', '76542'] },
    { sid: '20250816399', raw: 'D,C', digitsList: ['40254', '40876'] },
    { sid: '20250816398', raw: 'C,D', digitsList: ['77036', '83553'] },
    { sid: '20250816397', raw: 'B,A', digitsList: ['27892', '01143'] },
    { sid: '20250816396', raw: 'B,A', digitsList: ['85327', '71330'] },
    { sid: '20250816395', raw: 'A,C', digitsList: ['68175', '73455'] },
    { sid: '20250816394', raw: 'C,B', digitsList: ['71330', '37325'] },
    { sid: '20250816393', raw: 'B,C', digitsList: ['52695', '21652'] },
    { sid: '20250816392', raw: 'C,B', digitsList: ['68175', '41553'] },
    { sid: '20250816391', raw: 'B,C', digitsList: ['22152', '57223'] },
    { sid: '20250816390', raw: 'C,A', digitsList: ['73455', '10614'] },
    { sid: '20250816389', raw: 'A,B', digitsList: ['77127', '26286'] },
    { sid: '20250816388', raw: 'D,C', digitsList: ['37325', '65205'] },
    { sid: '20250816387', raw: 'C,D', digitsList: ['92545', '84726'] },
    { sid: '20250816386', raw: 'C,D', digitsList: ['52695', '17394'] },
    { sid: '20250816385', raw: 'D,A', digitsList: ['63852', '28174'] }
  ];
  render(cache);
  
  // loadInitial();
  setInterval(pollNew, 5000);
})(jQuery);
</script>

<?php if (function_exists('endpage')) endpage(false); ?>