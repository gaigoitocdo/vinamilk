<?php
view("header");
layout_header();
view("navbar");
include_once __DIR__ . "/../models/TopupModel.php";
$topups = TopupModel::GetAllWithDetails();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>결제 - 현대적 결제 인터페이스</title>
  <!-- Font Awesome cho icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <!-- Google Fonts: Roboto -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    /* Reset và cấu hình cơ bản */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
      color: #333;
      padding: 20px;
      min-height: 100vh;
      font-family: "Roboto", sans-serif;
    }
    .container {
      max-width: 800px;
      margin: 0 auto 120px;
      padding: 20px;
      background: rgba(255, 255, 255, 0.9);
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    h3 {
      background: linear-gradient(90deg, #6a82fb, #fc5c7d);
      text-align: center;
      font-size: 1.8rem;
      margin-bottom: 20px;
      color: #ffffff;
      border-radius: 8px;
      padding: 10px;
    }
    /* Style cho card thanh toán */
    .payment-option {
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-bottom: 20px;
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
      background: #f9f9f9;
    }
    .payment-option:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .payment-option a {
      display: block;
      padding: 15px 20px;
      background: #e9ecef;
      color: #333;
      text-decoration: none;
      font-size: 1.2rem;
      cursor: pointer;
    }
    .payment-option a:hover {
      background: #dee2e6;
    }
    .payment-details {
      padding: 15px 20px;
      font-size: 1rem;
      background: #fff;
      color: #333;
      /* Loại bỏ display: none để Bootstrap Collapse quản lý */
    }
    .payment-details p {
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.95rem;
    }
    .payment-details button {
      padding: 4px 8px;
      background: #ff3535;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 0.9rem;
      cursor: pointer;
      transition: background 0.3s;
    }
    .payment-details button:hover {
      background: #ff6666;
    }
    .payment-details input[type="number"],
    .payment-details input[type="file"] {
      width: 100%;
      margin: 8px 0;
      padding: 8px;
      border-radius: 4px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }
    .submit-btn {
      display: block;
      width: 100%;
      text-align: center;
      padding: 12px;
      background: linear-gradient(135deg, #ffc107, #ffdd57);
      border: none;
      border-radius: 4px;
      font-size: 1.1rem;
      color: #000;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .submit-btn:hover {
      transform: scale(1.03);
      box-shadow: 0 6px 12px rgba(255, 193, 7, 0.5);
    }
    /* Thanh điều hướng và các style khác giữ nguyên */
    :root {
      --nav-bg: #e9ecef;
      --accent: #007bff;
      --text-color: #333;
      --hover-bg: #ced4da;
    }
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background-color: var(--nav-bg);
      box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-around;
      gap: 10px;
      z-index: 20;
      overflow-x: auto;
      scroll-behavior: smooth;
      height: 80px;
    }
    .bottom-nav::-webkit-scrollbar {
      display: none;
    }
    .nav-item {
      text-decoration: none;
      flex: 1;
      text-align: center;
      cursor: pointer;
      padding: 8px 12px;
      transition: transform 0.2s ease, background-color 0.2s ease;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-width: 70px;
      color: var(--text-color);
    }
    .nav-item:hover {
      background-color: var(--hover-bg);
      transform: translateY(-4px);
    }
    .nav-item.active {
      background-color: var(--hover-bg);
    }
    .nav-item i {
      font-size: 24px;
      margin-bottom: 4px;
      transition: color 0.3s ease;
      color: var(--text-color);
    }
    .nav-item span {
      font-size: 12px;
      font-weight: 500;
      transition: color 0.3s ease;
      color: var(--text-color);
    }
    .nav-item.active i,
    .nav-item.active span {
      color: var(--accent);
      font-weight: 700;
    }
    @media screen and (min-width: 1025px) {
      .bottom-nav {
        height: 60px;
        gap: 5px;
      }
      .nav-item i {
        font-size: 18px;
      }
      .nav-item span {
        font-size: 10px;
      }
    }
    /* Hiệu ứng tuyết rơi */
    .snowflakes {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 10;
    }
    .snowflake {
      position: absolute;
      color: #fff;
      font-size: 1em;
      animation: fall linear infinite;
    }
    @keyframes fall {
      0% {
        transform: translateY(-100vh);
        opacity: 1;
      }
      100% {
        transform: translateY(100vh);
        opacity: 0;
      }
    }
    .logo-top {
      width: 400px;
      height: 120px;
      background: url("https://i.ibb.co/DDXDyYK9/Chat-GPT-Image-00-49-41-24-thg-4-2025.png") no-repeat center/contain;
      background-size: cover;
      margin: 0 auto 10px;
      padding: 5px;
      backdrop-filter: blur(5px);
      transition: transform 0.3s ease;
    }
    .logo-top:hover {
      transform: scale(1.05);
    }
    @media (max-width: 600px) {
      h3 {
        font-size: 1.5rem;
      }
      .payment-option a {
        font-size: 1rem;
        padding: 12px 15px;
      }
      .payment-details {
        font-size: 0.9rem;
        padding: 10px 15px;
      }
      .submit-btn {
        font-size: 1rem;
        padding: 10px;
      }
    }
  </style>
</head>
<body>
  <!-- Logo (giả định hàm logo() trả về HTML) -->
  <?= logo() ?>
  <!-- Hiệu ứng tuyết rơi -->
  <div class="snowflakes" aria-hidden="true">
    <div class="snowflake">❄</div>
    <div class="snowflake">❄</div>
    <div class="snowflake">❄</div>
    <div class="snowflake">❄</div>
    <div class="snowflake">❄</div>
    <div class="snowflake">❄</div>
  </div>
  <div class="container">
    <h3>결제 방법 선택</h3>
    <div id="accordion">
      <?php if (empty($topups)): ?>
        <p>결제수단을 사용할 수 없습니다.</p>
      <?php else: ?>
        <?php foreach ($topups as $o): ?>
          <div class="card payment-option">
            <div class="card-header">
              <h5 class="mb-0">
                <a class="btn w-100 collapsed" data-bs-toggle="collapse" data-bs-target="#collapse<?= $o['id'] ?>" aria-expanded="false">
                 지불하다 <?= htmlspecialchars($o['name']) ?>
                </a>
              </h5>
            </div>
            <div id="collapse<?= $o['id'] ?>" class="collapse payment-details">
              <p><strong>결제 금액:</strong></p>
              <input type="number" id="amountPaid<?= $o['id'] ?>" placeholder="지불하신 금액을 입력하세요" required />
              <p><strong>결제 방법:</strong>확인을 위해 송장을 제출하세요</p>
              <label for="invoiceUpload<?= $o['id'] ?>">송장 보내기:</label>
              <input type="file" id="invoiceUpload<?= $o['id'] ?>" accept=".pdf,.jpg,.jpeg,.png" />
              <button class="submit-btn" onclick="confirmPayment('<?= $o['id'] ?>')">결제 확인</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
      <!-- Option Visa -->
      <div class="card payment-option">
        <div class="card-header">
          <h5 class="mb-0">
            <a class="btn w-100 collapsed" data-bs-toggle="collapse" data-bs-target="#visaDetails" aria-expanded="false">
            하나페이먼트
            </a>
          </h5>
        </div>
        <div id="visaDetails" class="collapse payment-details">
          <p><strong>결제 금액:</strong></p>
          <input type="number" id="amountPaidVisa" placeholder="지불하신 금액을 입력하세요" required />
          <p><strong>결제 방법:</strong> 확인을 위해 송장을 제출하세요</p>
          <label for="invoiceUploadVisa">송장 보내기:</label>
          <input type="file" id="invoiceUploadVisa" accept=".pdf,.jpg,.jpeg,.png" />
          <button class="submit-btn" onclick="confirmPayment('visa')">결제 확인</button>
        </div>
      </div>
    </div>
  </div>
  <?php endpage(); ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function confirmPayment(method) {
      const amountInputId = method === 'visa' ? 'amountPaidVisa' : `amountPaid${method}`;
      const invoiceInputId = `invoiceUpload${method === 'visa' ? 'Visa' : method}`;
      const amount = document.getElementById(amountInputId).value;
      const invoice = document.getElementById(invoiceInputId).files[0];

      if (!amount || amount <= 0) {
        alert("Vui lòng nhập số tiền thanh toán hợp lệ.");
        return;
      }
      if (!invoice) {
        alert("Vui lòng tải lên hóa đơn.");
        return;
      }

      // Tạm thời hiển thị thông báo thành công, bạn có thể thay bằng AJAX gửi lên server
      alert(`Thanh toán thành công! Số tiền: ${amount} với phương thức ${method}`);
      // Logic gửi dữ liệu đến server (ví dụ: FormData)
      let formData = new FormData();
      formData.append("method", method);
      formData.append("amount", amount);
      formData.append("invoice", invoice);

      $.ajax({
        url: "<?= route('ajax/topup.php') ?>", // Thay bằng endpoint thực tế
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
          if (response.success) {
            alert("Thanh toán đã được xử lý!");
            // Reset form
            document.getElementById(amountInputId).value = "";
            document.getElementById(invoiceInputId).value = "";
            $(`#${method === 'visa' ? 'visaDetails' : 'collapse' + method}`).collapse('hide');
          } else {
            alert("Lỗi: " + (response.message || "Không thể xử lý thanh toán."));
          }
        },
        error: function() {
          alert("Lỗi kết nối server.");
        }
      });
    }

    // Hiệu ứng tuyết rơi
    document.addEventListener("DOMContentLoaded", () => {
      const snowContainer = document.querySelector(".snowflakes");
      const maxSnowflakes = 10;
      let currentSnowflakes = 0;

      function createSnowflake() {
        if (currentSnowflakes >= maxSnowflakes) return;
        const snowflake = document.createElement("div");
        snowflake.classList.add("snowflake");
        snowflake.innerHTML = "❄";
        snowflake.style.left = Math.random() * 100 + "vw";
        snowflake.style.animationDuration = Math.random() * 5 + 8 + "s";
        snowflake.style.fontSize = Math.random() * 0.4 + 0.6 + "em";
        snowContainer.appendChild(snowflake);
        currentSnowflakes++;

        snowflake.addEventListener("animationend", () => {
          snowflake.remove();
          currentSnowflakes--;
          setTimeout(createSnowflake, 2000);
        });
      }

      for (let i = 0; i < maxSnowflakes; i++) {
        setTimeout(createSnowflake, i * 1000);
      }
    });

    // Vô hiệu hóa context menu và F12 (khuyến nghị xem xét lại vì có thể ảnh hưởng UX)
    document.addEventListener("contextmenu", e => e.preventDefault());
    document.addEventListener("keydown", e => {
      if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === "I")) {
        e.preventDefault();
      }
    });
  </script>
  
<script>
   // Vô hiệu hóa chuột phải
  document.addEventListener("contextmenu", function (e) {
    e.preventDefault();
  });

  // Vô hiệu hóa phím F12 và các tổ hợp phím kiểm tra
  document.addEventListener("keydown", function (e) {
    if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === "I")) {
      e.preventDefault();
    }

  });
      </script>
</body>