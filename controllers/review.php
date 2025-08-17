<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Hệ thống Đánh giá VINAMILK</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'vinamilk-blue': '#0066CC',
            'vinamilk-light-blue': '#4A90E2',
            'vinamilk-red': '#E53E3E',
            'vinamilk-orange': '#FF8C00',
            'vinamilk-green': '#38A169',
            'vinamilk-yellow': '#F6E05E',
            'vinamilk-white': '#FFFFFF',
            'vinamilk-cream': '#FFF8DC'
          }
        }
      }
    };
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { 
      font-family: 'Inter', sans-serif; 
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    html, body {
      height: 100vh;
      overflow-x: hidden;
    }

    body {
      background: linear-gradient(135deg, #0066CC, #4A90E2, #E53E3E);
      color: #ffffff;
    }

    /* ===========================================
       MAIN LAYOUT STYLES - VINAMILK THEME
       =========================================== */
    
    .app-container {
      width: 100%;
      max-width: 500px;
      margin: 0 auto;
      min-height: 100vh;
      background: linear-gradient(135deg, #0066CC, #4A90E2, #E53E3E);
      box-shadow: 0 0 50px rgba(0, 102, 204, 0.3);
      position: relative;
    }

    /* SIDEBAR STYLES */
    .sidebar { 
      transition: transform 0.3s ease; 
      background: linear-gradient(135deg, #0066CC, #4A90E2);
      border-right: 1px solid rgba(0, 102, 204, 0.3);
    }
    .sidebar-open { transform: translateX(0); }
    .sidebar-closed { transform: translateX(-100%); }
    .hamburger span { transition: all 0.3s ease; }
    .hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
    .hamburger.active span:nth-child(2) { opacity: 0; }
    .hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(7px, -7px); }
    
    /* CARD STYLES */
    .vinamilk-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      border: 1px solid rgba(0, 102, 204, 0.3);
      transition: all 0.3s ease;
    }

    .vinamilk-card:hover {
      border-color: #0066CC;
      box-shadow: 0 8px 25px rgba(0, 102, 204, 0.25);
    }

    /* MAIN CONTENT STYLES */
    .main-content {
      background: linear-gradient(135deg, rgba(255, 248, 220, 0.95), rgba(255, 255, 255, 0.9));
      border-radius: 16px;
      margin: 1rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      overflow: hidden;
      border: 2px solid rgba(0, 102, 204, 0.2);
    }

    .main-content.hidden {
      opacity: 0;
      transform: translateY(-20px) scale(0.95);
      pointer-events: none;
      height: 0;
      margin: 0;
      padding: 0;
    }

    /* REVIEW GRID STYLES */
    .review-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .review-card {
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(255, 248, 220, 0.8));
      border-radius: 16px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      padding: 1.2rem;
      text-align: center;
      height: 180px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      border: 2px solid rgba(0, 102, 204, 0.2);
      box-shadow: 0 4px 15px rgba(0, 102, 204, 0.1);
    }

    .review-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 35px rgba(0, 102, 204, 0.25);
      border-color: #0066CC;
      background: linear-gradient(145deg, rgba(255, 255, 255, 1), rgba(255, 248, 220, 0.9));
    }

    .review-card.active {
      background: linear-gradient(145deg, #0066CC, #4A90E2);
      border-color: #0066CC;
      color: white;
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 16px 40px rgba(0, 102, 204, 0.4);
    }

    .review-image {
      width: 80px;
      height: 80px;
      border-radius: 12px;
      object-fit: cover;
      margin: 0 auto 12px auto;
      transition: all 0.3s ease;
      border: 3px solid rgba(0, 102, 204, 0.2);
    }

    .review-card:hover .review-image {
      border-color: #0066CC;
      transform: scale(1.05);
    }

    .review-name {
      font-weight: 700;
      font-size: 0.95rem;
      margin-bottom: 8px;
      text-align: center;
      color: #0066CC;
    }

    .review-card.active .review-name {
      color: #ffffff;
    }

    .review-status {
      font-weight: 500;
      font-size: 0.75rem;
      color: #4A90E2;
      text-align: center;
    }

    .review-card.active .review-status {
      color: #FFF8DC;
    }

    /* ===========================================
       SLIDER STYLES - VINAMILK THEME
       =========================================== */

    .slider-container {
      text-align: center;
      margin-bottom: 16px;
      position: relative;
      width: 100%;
      overflow: hidden;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0, 102, 204, 0.2);
      border: 3px solid rgba(0, 102, 204, 0.3);
    }

    .slider-wrapper {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }

    .slider-image {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: 12px;
      flex-shrink: 0;
    }

    .slider-buttons {
      position: absolute;
      top: 50%;
      width: 100%;
      display: flex;
      justify-content: space-between;
      transform: translateY(-50%);
      padding: 0 15px;
    }

    .slider-buttons button {
      background: linear-gradient(135deg, #0066CC, #4A90E2);
      border: none;
      color: white;
      padding: 12px 16px;
      cursor: pointer;
      font-size: 18px;
      font-weight: bold;
      border-radius: 50%;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
    }

    .slider-buttons button:hover {
      background: linear-gradient(135deg, #4A90E2, #E53E3E);
      transform: scale(1.1);
      box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
    }

    /* ===========================================
       PRODUCT GRID & INFO STYLES
       =========================================== */

    .product-grid {
      display: grid;
      gap: 12px;
      margin: 16px;
    }

    .grid-2 {
      grid-template-columns: 1fr 1fr;
    }

    .info-btn {
      padding: 16px;
      border-radius: 12px;
      background: linear-gradient(135deg, rgba(0, 102, 204, 0.1), rgba(74, 144, 226, 0.1));
      border: 2px solid rgba(0, 102, 204, 0.2);
      color: #0066CC;
      font-weight: 600;
      font-size: 14px;
      text-align: center;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }

    .info-btn:hover {
      background: linear-gradient(135deg, rgba(0, 102, 204, 0.2), rgba(74, 144, 226, 0.2));
      border-color: #0066CC;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 102, 204, 0.2);
    }

    .info-btn span {
      color: #0066CC;
      font-weight: 700;
    }

    /* ===========================================
       PROMOTION SECTION
       =========================================== */

    .promotion-section {
      padding: 20px;
      text-align: center;
      background: linear-gradient(135deg, rgba(255, 248, 220, 0.9), rgba(255, 255, 255, 0.8));
      border-radius: 16px;
      margin: 16px;
      border: 2px solid rgba(0, 102, 204, 0.2);
      backdrop-filter: blur(10px);
    }

    .promotion-text {
      font-weight: 700;
      font-size: 16px;
      color: #0066CC;
      margin: 12px 0;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .wheel-container {
      margin: 20px 0;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      background: linear-gradient(135deg, rgba(0, 102, 204, 0.1), rgba(229, 62, 62, 0.1));
      border-radius: 20px;
      border: 2px solid rgba(0, 102, 204, 0.2);
    }

    .wheel-img {
      width: 120px;
      height: 120px;
      animation: vinamilkSpin 4s linear infinite;
      filter: drop-shadow(0 8px 16px rgba(0, 102, 204, 0.3));
      transition: all 0.3s ease;
    }

    .wheel-container:hover .wheel-img {
      animation: vinamilkSpinFast 1s linear infinite;
      transform: scale(1.1);
    }

    @keyframes vinamilkSpin {
      from { 
        transform: rotate(0deg); 
      }
      to { 
        transform: rotate(360deg); 
      }
    }

    @keyframes vinamilkSpinFast {
      from { 
        transform: rotate(0deg) scale(1.1); 
      }
      to { 
        transform: rotate(360deg) scale(1.1); 
      }
    }

    .promotion-product {
      max-width: 200px;
      height: auto;
      border-radius: 12px;
      margin-top: 16px;
      border: 3px solid rgba(0, 102, 204, 0.2);
      transition: all 0.3s ease;
    }

    .promotion-product:hover {
      transform: scale(1.05);
      border-color: #0066CC;
      box-shadow: 0 8px 25px rgba(0, 102, 204, 0.3);
    }

    /* ===========================================
       DETAIL SECTION STYLES - VINAMILK THEME
       =========================================== */

    .detail-section {
      background: linear-gradient(135deg, #0066CC 0%, #4A90E2 50%, #E53E3E 100%);
      background-size: 400% 400%;
      animation: vinamilkGradientShift 8s ease infinite;
      border-radius: 24px 24px 0 0;
      margin-top: 1rem;
      padding: 2rem;
      color: #ffffff;
      position: relative;
      overflow: hidden;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .detail-section.show {
      opacity: 1;
      transform: translateY(0);
    }

    @keyframes vinamilkGradientShift {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    .detail-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #FFD700, #0066CC, #E53E3E, #4A90E2, #FFF8DC);
      animation: vinamilkColorFlow 3s ease-in-out infinite;
    }

    @keyframes vinamilkColorFlow {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    /* ===========================================
       HEADER STYLES
       =========================================== */

    .main-header {
      background: linear-gradient(135deg, #0066CC, #4A90E2);
      box-shadow: 0 4px 15px rgba(0, 102, 204, 0.2);
      border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .header-title {
      background: linear-gradient(45deg, #ffffff, #FFF8DC);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      font-weight: 800;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .premium-badge {
      background: linear-gradient(135deg, #FFD700, #FF8C00);
      color: #0066CC;
      font-weight: 700;
      box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
    }

    /* ===========================================
       RESPONSIVE DESIGN
       =========================================== */

    @media (max-width: 640px) {
      .app-container {
        max-width: 100%;
        border-radius: 0;
      }

      .main-content {
        margin: 0.5rem;
        border-radius: 12px;
      }

      .review-image {
        width: 70px;
        height: 70px;
      }

      .slider-image {
        height: 180px;
      }

      .wheel-img {
        width: 100px;
        height: 100px;
      }

      .promotion-text {
        font-size: 14px;
      }

      .info-btn {
        padding: 12px;
        font-size: 12px;
      }
    }

    /* ===========================================
       BUTTON STYLES - VINAMILK THEME
       =========================================== */

    .btn-vinamilk-primary {
      background: linear-gradient(135deg, #0066CC, #4A90E2);
      border: none;
      border-radius: 12px;
      padding: 12px 24px;
      color: white;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 102, 204, 0.2);
    }

    .btn-vinamilk-primary:hover {
      background: linear-gradient(135deg, #4A90E2, #E53E3E);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 102, 204, 0.3);
    }

    .btn-vinamilk-secondary {
      background: linear-gradient(135deg, #38A169, #68D391);
      border: none;
      border-radius: 12px;
      padding: 12px 24px;
      color: white;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    /* ===========================================
       BOTTOM SHEET STYLES - ENHANCED
       =========================================== */

    .bs-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(8px);
      z-index: 1000;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .bs-overlay.show {
      opacity: 1;
      pointer-events: all;
    }

    .bs-card {
      background: linear-gradient(145deg, #ffffff, #f8fafc);
      border-radius: 24px 24px 0 0;
      box-shadow: 0 -20px 50px rgba(0, 102, 204, 0.2);
      padding: 1.5rem;
      width: 100%;
      max-width: 500px;
      transform: translateY(100%);
      transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      border: 3px solid rgba(0, 102, 204, 0.3);
    }

    .bs-overlay.show .bs-card {
      transform: translateY(0);
    }

    .bs-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 2px solid rgba(0, 102, 204, 0.1);
    }

    .bs-header h3 {
      font-size: 1.5rem;
      font-weight: 800;
      background: linear-gradient(135deg, #0066CC, #E53E3E);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin: 0;
    }

    .bs-close {
      background: linear-gradient(135deg, #E53E3E, #FF6B6B);
      border: none;
      color: white;
      font-size: 1.5rem;
      font-weight: bold;
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
    }

    .bs-close:hover {
      transform: scale(1.1);
      box-shadow: 0 6px 16px rgba(229, 62, 62, 0.4);
    }

    .bs-body {
      margin-bottom: 1.5rem;
    }

    .bs-selection {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      padding: 0.75rem;
      background: linear-gradient(135deg, rgba(0, 102, 204, 0.1), rgba(74, 144, 226, 0.1));
      border-radius: 12px;
      border: 1px solid rgba(0, 102, 204, 0.2);
    }

    .bs-label {
      font-weight: 600;
      color: #0066CC;
      font-size: 0.9rem;
    }

    .bet-number {
      font-weight: 700;
      color: #E53E3E;
      font-size: 1rem;
    }

    .bs-input {
      margin-bottom: 1rem;
    }

    .bs-input label {
      display: block;
      margin-bottom: 0.5rem;
    }

    .bs-input input {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 2px solid rgba(0, 102, 204, 0.2);
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.9);
      font-size: 1rem;
      font-weight: 600;
      color: #0066CC;
      transition: all 0.3s ease;
    }

    .bs-input input:focus {
      outline: none;
      border-color: #0066CC;
      box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
      background: white;
    }

    .bs-input.has-caret input {
      background-image: linear-gradient(45deg, transparent 40%, #0066CC 50%, transparent 60%);
      background-size: 8px 8px;
      background-position: right 12px center;
      background-repeat: no-repeat;
    }

    .bs-summary {
      background: linear-gradient(135deg, rgba(255, 248, 220, 0.8), rgba(255, 255, 255, 0.9));
      border-radius: 12px;
      padding: 1rem;
      border: 2px solid rgba(0, 102, 204, 0.2);
    }

    .bs-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #0066CC;
    }

    .bs-row:last-child {
      margin-bottom: 0;
      font-size: 1.1rem;
      color: #E53E3E;
    }

    .bs-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .bs-confirm {
      background: linear-gradient(135deg, #0066CC, #4A90E2);
      border: none;
      color: white;
      padding: 1rem;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
    }

    .bs-confirm:hover {
      background: linear-gradient(135deg, #4A90E2, #E53E3E);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
    }

    .bs-cancel {
      background: linear-gradient(135deg, #6B7280, #9CA3AF);
      border: none;
      color: white;
      padding: 1rem;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
    }

    .bs-cancel:hover {
      background: linear-gradient(135deg, #9CA3AF, #D1D5DB);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(107, 114, 128, 0.4);
    }

    /* ===========================================
       MODAL STYLES - ENHANCED
       =========================================== */

    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(8px);
      z-index: 2000;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .modal-content {
      background: linear-gradient(145deg, #ffffff, #f8fafc);
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 102, 204, 0.2);
      width: 100%;
      max-width: 400px;
      border: 3px solid rgba(0, 102, 204, 0.3);
      overflow: hidden;
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      background: linear-gradient(135deg, rgba(0, 102, 204, 0.1), rgba(74, 144, 226, 0.1));
      border-bottom: 2px solid rgba(0, 102, 204, 0.2);
    }

    .modal-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: #0066CC;
      margin: 0;
    }

    .modal-close {
      background: linear-gradient(135deg, #E53E3E, #FF6B6B);
      border: none;
      color: white;
      font-size: 1.25rem;
      font-weight: bold;
      width: 2rem;
      height: 2rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .modal-close:hover {
      transform: scale(1.1);
    }

    .modal-body {
      padding: 1.5rem;
      max-height: 300px;
      overflow-y: auto;
    }

    .modal-body ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .modal-body li {
      background: linear-gradient(135deg, rgba(0, 102, 204, 0.05), rgba(255, 248, 220, 0.5));
      border: 1px solid rgba(0, 102, 204, 0.2);
      border-radius: 12px;
      padding: 1rem;
      margin-bottom: 0.75rem;
    }

    .modal-body li:last-child {
      margin-bottom: 0;
    }

    .modal-footer {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      padding: 1.5rem;
      background: linear-gradient(135deg, rgba(255, 248, 220, 0.3), rgba(255, 255, 255, 0.5));
      border-top: 2px solid rgba(0, 102, 204, 0.1);
    }

    .btn-cancel {
      background: linear-gradient(135deg, #6B7280, #9CA3AF);
      border: none;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-cancel:hover {
      background: linear-gradient(135deg, #9CA3AF, #D1D5DB);
      transform: translateY(-1px);
    }

    .btn-confirm {
      background: linear-gradient(135deg, #0066CC, #4A90E2);
      border: none;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-confirm:hover {
      background: linear-gradient(135deg, #4A90E2, #E53E3E);
      transform: translateY(-1px);
    }

    /* ===========================================
       NOTIFICATION STYLES - ENHANCED
       =========================================== */

    .notification-container {
      position: fixed;
      top: 1rem;
      right: 1rem;
      z-index: 10000;
      width: 90%;
      max-width: 20rem;
    }

    .notification {
      margin-bottom: 0.5rem;
      border-radius: 12px;
      padding: 1rem;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      backdrop-filter: blur(10px);
      border: 2px solid;
      animation: slideInNotification 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      font-weight: 600;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .notification::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      animation: shimmer 2s ease-in-out infinite;
    }

    .notification-success {
      background: linear-gradient(135deg, #10b981, #34d399);
      border-color: #059669;
    }

    .notification-error {
      background: linear-gradient(135deg, #ef4444, #f87171);
      border-color: #dc2626;
    }

    .notification-warning {
      background: linear-gradient(135deg, #f59e0b, #fbbf24);
      border-color: #d97706;
    }

    .notification-info {
      background: linear-gradient(135deg, #0066CC, #4A90E2);
      border-color: #0052A3;
    }

    @keyframes slideInNotification {
      0% { 
        transform: translateX(100%) scale(0.8); 
        opacity: 0; 
      }
      100% { 
        transform: translateX(0) scale(1); 
        opacity: 1; 
      }
    }

    @keyframes shimmer {
      0% { left: -100%; }
      100% { left: 100%; }
    }

    /* ===========================================
       ODDS ITEM PROGRESS STYLES
       =========================================== */

    .odds-progress {
      position: absolute;
      top: 0.75rem;
      left: 0.75rem;
      width: 2.5rem;
      height: 2.5rem;
    }

    .odds-progress-circle {
      transform: rotate(-90deg);
    }

    .odds-progress-circle circle {
      fill: none;
      stroke-width: 4;
      stroke-linecap: round;
    }

    .odds-progress-circle .bg {
      stroke: rgba(255, 255, 255, 0.3);
    }

    .odds-progress-circle .fg {
      stroke: #0066CC;
      transition: stroke-dashoffset 0.5s ease;
    }

    .odds-progress-text {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 0.55rem;
      font-weight: 600;
      color: white;
      text-shadow: 0 0 4px rgba(0, 0, 0, 0.5);
    }

    .odds-item.active {
      background: linear-gradient(145deg, #0066CC, #4A90E2);
      border-color: #0066CC;
      color: white;
      transform: translateY(-2px) scale(1.02);
      box-shadow: 0 12px 35px rgba(0, 102, 204, 0.35);
    }

    .odds-item.active::after {
      content: "✓";
      position: absolute;
      top: 0.75rem;
      right: 0.75rem;
      background: linear-gradient(135deg, #38A169, #68D391);
      color: white;
      width: 2rem;
      height: 2rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      font-weight: bold;
      animation: checkMarkBounce 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      box-shadow: 0 4px 12px rgba(56, 161, 105, 0.4);
    }

    @keyframes checkMarkBounce {
      0% { transform: scale(0) rotate(180deg); }
      50% { transform: scale(1.2) rotate(10deg); }
      100% { transform: scale(1) rotate(0deg); }
    }

    /* ===========================================
       RESPONSIVE DESIGN FOR BOTTOM SHEET
       =========================================== */

    @media (max-width: 640px) {
      .bs-card {
        border-radius: 16px 16px 0 0;
        padding: 1rem;
      }
      
      .bs-header h3 {
        font-size: 1.25rem;
      }
      
      .bs-actions {
        grid-template-columns: 1fr;
        gap: 0.75rem;
      }
      
      .modal-content {
        margin: 1rem;
        max-width: calc(100% - 2rem);
      }
    }

    /* ===========================================
       UTILITY CLASSES
       =========================================== */

    .modal-open {
      overflow: hidden;
    }

    body.modal-open {
      overflow: hidden;
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
      .bs-card,
      .modal-content {
        border-width: 4px;
      }
      
      .notification {
        border-width: 3px;
      }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
      .bs-overlay,
      .bs-card,
      .notification {
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
      }
    }

    /* ===========================================
       ANIMATION ENHANCEMENTS
       =========================================== */

    .fade-in {
      animation: fadeIn 0.5s ease-in-out;
    }

    .slide-up {
      animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from { 
        opacity: 0;
        transform: translateY(20px);
      }
      to { 
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Logo styling */
    .vinamilk-logo {
      filter: drop-shadow(0 2px 4px rgba(0, 102, 204, 0.3));
    }
  </style>
</head>

<body>
  <!-- Main Application Container -->
  <div class="app-container">
    <!-- Header -->
    <header id="mainHeader" class="main-header sticky top-0 z-20">
      <div class="px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button id="hamburger" class="hamburger p-2 text-white hover:text-vinamilk-yellow transition-colors" aria-label="Toggle navigation">
            <span class="block w-5 h-0.5 bg-current mb-1"></span>
            <span class="block w-5 h-0.5 bg-current mb-1"></span>
            <span class="block w-5 h-0.5 bg-current"></span>
          </button>
          <div class="flex items-center gap-2">
            <!-- Vinamilk Logo SVG -->
            <svg class="vinamilk-logo w-8 h-8" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="16" cy="16" r="16" fill="#ffffff"/>
              <path d="M8 12c0-2.2 1.8-4 4-4s4 1.8 4 4v8c0 2.2-1.8 4-4 4s-4-1.8-4-4v-8z" fill="#0066CC"/>
              <path d="M16 12c0-2.2 1.8-4 4-4s4 1.8 4 4v8c0 2.2-1.8 4-4 4s-4-1.8-4-4v-8z" fill="#E53E3E"/>
              <ellipse cx="16" cy="10" rx="3" ry="2" fill="#FFD700"/>
            </svg>
            <h1 id="mainTitle" class="header-title text-lg font-bold">VINAMILK</h1>
          </div>
        </div>
        <div class="premium-badge px-3 py-1 rounded-full text-xs font-medium">
          Premium
        </div>
      </div>
    </header>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black/50 hidden z-30"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar sidebar-closed fixed top-0 left-0 w-72 h-full shadow-xl z-40 overflow-y-auto">
      <div class="p-4">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-white">Danh mục đánh giá</h2>
          <button id="close-sidebar" class="text-2xl text-gray-400 hover:text-white transition-colors">×</button>
        </div>
        
        <!-- Progress Indicator -->
        <div class="mb-6">
          <h3 class="text-sm font-semibold text-gray-300 mb-3">Tiến độ đánh giá</h3>
          <div class="flex items-center justify-between mb-2">
            <div class="progress-step active w-8 h-8 rounded-full bg-vinamilk-blue flex items-center justify-center text-white text-xs font-bold">1</div>
            <div class="flex-1 h-1 bg-gray-600 mx-1"><div class="h-full bg-vinamilk-blue w-3/4"></div></div>
            <div class="progress-step w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-gray-400 text-xs font-bold">2</div>
            <div class="flex-1 h-1 bg-gray-600 mx-1"></div>
            <div class="progress-step w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-gray-400 text-xs font-bold">3</div>
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

    <!-- Main Content Area -->
    <div id="mainContentArea" class="transition-all duration-500">
      <!-- Main promotional content -->
      <div class="main-content">
        <div class="slider-container">
          <div class="slider-wrapper" id="slider">
            <img src="https://i.ibb.co/xnySfvB/5-BSu-EPTQW.jpg" alt="Banner 1" class="slider-image">
            <img src="https://i.ibb.co/8gLNgGTd/8-BO-Ez-Qob.webp" alt="Banner 2" class="slider-image">
          </div>
          <div class="slider-buttons">
            <button onclick="prevSlide()" aria-label="Previous slide">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
              </svg>
            </button>
            <button onclick="nextSlide()" aria-label="Next slide">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="product-grid grid-2">
          <div class="info-btn">
            <div class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              <span>Tổng lần nhập hôm nay: <span id="daily-orders">0</span></span>
            </div>
          </div>
          <div class="info-btn">
            <div class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
              </svg>
              <span>Tổng số lượng nhập: <span id="daily-products">0</span></span>
            </div>
          </div>
        </div>

        <div class="slider-container">
          <div class="slider-wrapper" id="slider2">
            <img src="https://i.ibb.co/gZQrXMpB/3-D5-UYeadq.jpg" alt="Banner 3" class="slider-image">
            <img src="https://i.ibb.co/nsnxDPjJ/1-BJ1q-RYXy.jpg" alt="Banner 4" class="slider-image">
          </div>
          <div class="slider-buttons">
            <button onclick="prevSlide2()" aria-label="Previous slide">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
              </svg>
            </button>
            <button onclick="nextSlide2()" aria-label="Next slide">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="promotion-section">
          <div class="promotion-text">
            <svg class="w-6 h-6 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            TRI ÂN KHÁCH HÀNG VỚI NHIỀU PHẦN QUÀ CÓ GIÁ TRỊ LỚN
          </div>
          <div class="wheel-container">
            <img src="https://i.ibb.co/21CgqhQK/vongquay-vo8v-Ji.png" alt="Vòng quay may mắn" class="wheel-img">
          </div>
          <div class="promotion-text">
            <svg class="w-6 h-6 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
            </svg>
            HỆ THỐNG ĐANG TỰ ĐỘNG TÌM KIẾM KHÁCH HÀNG MAY MẮN
          </div>
          <img src="https://i.ibb.co/HTCxbr3x/3in1-D7-PODvv8.png" alt="Sản phẩm 3 trong 1" class="promotion-product">
        </div>
      </div>

      <!-- Review Cards Grid -->
      <div class="p-4 space-y-4">
        <div class="vinamilk-card p-4">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-vinamilk-gradient text-xl font-bold flex items-center gap-2">
              <svg class="w-6 h-6 text-vinamilk-blue" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
              </svg>
              Danh sách đánh giá sản phẩm
            </h2>
            <div class="text-sm text-vinamilk-blue font-medium">
              <span id="itemCount">0</span> sản phẩm
            </div>
          </div>
          <div id="mainContent" class="review-grid"></div>
        </div>
      </div>
    </div>

    <!-- Detail Section - Hidden by default -->
    <div id="detailSection" class="detail-section hidden">
      <!-- Detail Header -->
      <div class="detail-header text-center mb-6">
        <div class="flex items-center justify-center mb-4">
          <svg class="w-8 h-8 mr-3 text-vinamilk-yellow" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
          </svg>
          <h3 id="lottery_title" class="text-2xl font-bold text-white">Chi tiết đánh giá</h3>
        </div>
        <button id="btn-close-detail" class="absolute top-4 right-4 text-white hover:text-vinamilk-yellow transition-colors" aria-label="Đóng chi tiết">
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
        </button>
      </div>

      <!-- Lottery Info Card -->
      <div class="vinamilk-glass-effect rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <img id="lottery_img" src="" alt="Lottery" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg">
          <div class="text-right">
            <div class="text-white text-sm font-medium flex items-center justify-end gap-2">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
              </svg>
              Phiên hiện tại
            </div>
            <b id="now-session" class="text-2xl text-vinamilk-yellow">-1</b>
          </div>
        </div>
        
        <div class="flex items-center justify-between text-sm mb-4">
          <div class="text-white font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
            </svg>
            Phiên trước <b id="prev-session" class="text-vinamilk-yellow">-1</b>
          </div>
          <div class="space-x-2">
            <span id="prev-odd-1" class="font-semibold text-white px-3 py-1 bg-vinamilk-green/30 rounded-lg text-sm border border-vinamilk-green/50">
              <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
              </svg>
            </span>
            <span id="prev-odd-2" class="font-semibold text-white px-3 py-1 bg-vinamilk-red/30 rounded-lg text-sm border border-vinamilk-red/50">
              <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
              </svg>
            </span>
          </div>
        </div>
      </div>

      <!-- Countdown Timer -->
      <div class="bg-gradient-to-r from-vinamilk-blue to-vinamilk-red text-white border-radius-12 p-4 text-center box-shadow-vinamilk mb-6 rounded-xl">
        <div class="flex items-center justify-center mb-2">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
          </svg>
          <span class="text-sm font-medium">Thời gian còn lại</span>
        </div>
        <div id="count-down" class="font-bold text-2xl tracking-wider">--:--:--</div>
      </div>

      <!-- Balance Info -->
      <div class="vinamilk-glass-effect rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-2 text-white">
            <svg class="w-5 h-5 text-vinamilk-red" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">Lựa chọn của bạn</span>
          </div>
          <div class="flex items-center space-x-2">
            <span class="text-white font-medium">Số dư</span>
            <svg class="w-5 h-5 text-vinamilk-yellow" fill="currentColor" viewBox="0 0 20 20">
              <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.469.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
            </svg>
            <span id="money" class="font-bold text-xl text-vinamilk-yellow">0</span>
          </div>
        </div>
      </div>

      <!-- Odds Grid -->
      <div id="value_odds" class="grid grid-cols-2 gap-4 mb-6"></div>
    </div>
  </div>

      <!-- Enhanced Bottom Sheet -->
    <div id="bottom-sheet" class="bs-overlay" role="dialog" aria-modal="true">
      <div class="bs-card" role="document">
        <div class="bs-header">
          <h3 id="bs-brand-title">VINAMILK</h3>
          <button id="btn-close-bottomsheet" class="bs-close" aria-label="Đóng">×</button>
        </div>
        <div class="bs-body">
          <div class="bs-selection">
            <span class="bs-label">Đang chọn:</span>
            <span class="bet-number">-</span>
          </div>
          <div class="bs-input has-caret">
            <label for="txt-lot-money" class="bs-label">Số điểm đánh giá:</label>
            <input type="number" id="txt-lot-money" min="1" value="1" placeholder="Nhập số điểm (1 = 1 VNĐ)">
          </div>
          <div class="bs-summary">
            <div class="bs-row">
              <span>Tổng lượt chọn:</span>
              <span id="totalBetOption">0</span>
            </div>
            <div class="bs-row">
              <span>Tổng số điểm:</span>
              <span id="totalBetMoney">0</span>
            </div>
          </div>
        </div>
        <div class="bs-actions">
          <button id="btn-submit" class="bs-confirm">Xác nhận đánh giá</button>
          <button id="bs-cancel" class="bs-cancel">Hủy bỏ</button>
        </div>
      </div>
    </div>

    <!-- Confirm Modal -->
    <div id="modalConfirm" class="modal-backdrop hidden">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Xác nhận đánh giá</h5>
          <button id="closeConfirm" class="modal-close" aria-label="Đóng">×</button>
        </div>
        <div class="modal-body">
          <ul id="bet-confirm-list"></ul>
        </div>
        <div class="modal-footer">
          <button id="noConfirm" class="btn-cancel">Không</button>
          <button id="btn-confirm" class="btn-confirm">Có</button>
        </div>
      </div>
    </div>

    <!-- JAVASCRIPT SECTION -->
   <script>
    // ===========================================
    // GLOBAL VARIABLES AND UTILITIES
    // ===========================================
    
    var items = [];
    var Odds = [];
    var UserInfo = {};
    var LotteryInfo = {};
    var SelectedOdds = [];
    var current_session = "";
    var countdownInterval;
    var pollingInterval;
    var remainingSeconds = 999;
    var isBottomSheetOpen = false;
    var isDetailOpen = false;
    var currentKey = '';
    var currentId = '';
    window.audioContextStarted = false;
    window.totalUnits = [0, 0, 0, 0];

    // URL Parameters
    const urlParams = new URLSearchParams(window.location.search);
    const pageKey = urlParams.get('key');
    const pageId = urlParams.get('id');
    
    // Rank key logic
    let rankKey;
    switch(pageId) {
      case '6': rankKey = 1; break;
      case '7': rankKey = 2; break;
      case '8': rankKey = 3; break;
      default: rankKey = 1;
    }
    window.RANK_KEY = rankKey;

    // ===========================================
    // UTILITY FUNCTIONS
    // ===========================================

    function formatMoneyJS(money) {
      try {
        let moneyInt = Math.floor(money);
        if (moneyInt < 1000) return moneyInt;
        let quotient = Math.floor(moneyInt / 1000);
        let remainder = moneyInt % 1000;
        let quotientStr = quotient.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        let remainderStr = remainder.toString().padStart(3, '0');
        return quotientStr + "." + remainderStr;
      } catch (e) {
        console.error('Error formatting money:', e);
        return money || 0;
      }
    }

    function convertSeconds(seconds) {
      try {
        let hours = Math.floor(seconds / 3600);
        if (hours < 10) hours = "0" + hours;
        let minutes = Math.floor((seconds % 3600) / 60);
        if (minutes < 10) minutes = "0" + minutes;
        let remainingSeconds = seconds % 60;
        if (remainingSeconds < 10) remainingSeconds = "0" + remainingSeconds;
        return `${hours}:${minutes}:${remainingSeconds}`;
      } catch (e) {
        console.error('Error converting seconds:', e);
        return "00:00:00";
      }
    }

    // ===========================================
    // DETAIL SECTION MANAGEMENT
    // ===========================================

    function openDetailSection(key, id) {
      try {
        isDetailOpen = true;
        currentKey = key;
        currentId = id;
        
        // Show detail section with animation
        $("#detailSection").removeClass("hidden").fadeIn(400);
        
        // Scroll to detail section
        setTimeout(() => {
          document.getElementById('detailSection').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
          });
        }, 200);
        
        // Initialize detail view
        initializeDetailView(key, id);
        
      } catch (e) {
        console.error('Error opening detail section:', e);
      }
    }

    function closeDetailSection() {
      try {
        // Just clean up, don't hide the section
        cleanupDetailView();
        isDetailOpen = false;
        
        // Reset to default state
        $("#lottery_title").text("Chọn một mục để xem chi tiết");
        $("#lottery_img").attr('src', '');
        $("#now-session").text('-');
        $("#prev-session").text('-');
        $("#prev-odd-1, #prev-odd-2").text('');
        $("#count-down").text('--:--:--');
        $("#money").text('0');
        $("#value_odds").empty();
        
        // Show default message
        $("#value_odds").html(`
          <div class="col-span-2 text-center py-8">
            <svg class="w-16 h-16 mx-auto mb-4 text-white/50" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-white/70 text-lg">Chọn một mục từ danh sách trên để bắt đầu đánh giá</p>
          </div>
        `);
        
        // Scroll back to top
        setTimeout(() => {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 200);
        
      } catch (e) {
        console.error('Error resetting detail section:', e);
      }
    }

    function cleanupDetailView() {
      try {
        if (countdownInterval) {
          clearInterval(countdownInterval);
          countdownInterval = null;
        }
        if (pollingInterval) {
          clearInterval(pollingInterval);
          pollingInterval = null;
        }
        SelectedOdds = [];
        $(".odds-item").removeClass("active");
        hideBottomSheet();
      } catch (e) {
        console.error('Error cleaning up detail view:', e);
      }
    }

    // ===========================================
    // MAIN VIEW FUNCTIONS
    // ===========================================

    function openReview(key, id) {
      // Update detail section with new data
      updateDetailSection(key, id);
    }

    function updateDetailSection(key, id) {
      try {
        // Clean up previous session
        cleanupDetailView();
        
        // Update current data
        currentKey = key;
        currentId = id;
        isDetailOpen = true;
        
        // Scroll to detail section smoothly
        setTimeout(() => {
          document.getElementById('detailSection').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
          });
        }, 100);
        
        // Initialize detail view with new data
        initializeDetailView(key, id);
        
      } catch (e) {
        console.error('Error updating detail section:', e);
      }
    }

    function showReview(id) {
      try {
        let need_to_render = id == -1 ? items : items.filter((v, i) => v.cate_id == id);
        $("#mainContent").empty();
        $("#itemCount").text(need_to_render.length);
        
        need_to_render.forEach(o => {
          let html = `
            <div class="review-card" onclick="openReview('${o.key}', ${o.id})">
              <img src="${o.image}" alt="${o.name}" class="review-image" 
                   onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMzIiIGZpbGw9IiM2MzY2ZjEiLz4KPHN2ZyB4PSIxNiIgeT0iMTYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJ3aGl0ZSI+CjxwYXRoIGQ9Ik0xMiAyQzYuNDggMiAyIDYuNDggMiAxMnM0LjQ4IDEwIDEwIDEwIDEwLTQuNDggMTAtMTBTMTcuNTIgMiAxMiAyem0wIDNjMS42NiAwIDMgMS4zNCAzIDNzLTEuMzQgMy0zIDMtMy0xLjM0LTMtMyAxLjM0LTMgMy0zem0wIDEzLjJjLTIuNSAwLTQuNzEtMS4yOC02LTMuMjIuMDMtMS45OSA0LTMuMDggNi0zLjA4czUuOTcgMS4wOSA2IDMuMDhjLTEuMjkgMS45NC0zLjUgMy4yMi02IDMuMjJ6Ii8+Cjwvc3ZnPgo8L3N2Zz4='" />
              <div class="review-name">${o.name}</div>
              <div class="review-status">Đang hoạt động</div>
            </div>
          `;
          $("#mainContent").append(html);
        });
      } catch (e) {
        console.error('Error showing review:', e);
      }
    }

    // ===========================================
    // DETAIL VIEW FUNCTIONS
    // ===========================================

    function initializeDetailView(key, id) {
      try {
        console.log("🚀 Starting detail view initialization...");
        
        // Reset detail view state
        SelectedOdds = [];
        remainingSeconds = 999;
        
          // Initialize progress simulation
        if (typeof window.initProgressSim === 'undefined') {
          window.initProgressSim = function(session) {
            console.log('Progress sim initialized for session:', session);
            if (!window.totalUnits) {
              window.totalUnits = [0, 0, 0, 0];
            }
          };
          
          window.drawProgressBars = function(seconds) {
            try {
              const oddsItems = document.querySelectorAll('.odds-item');
              if (!window.totalUnits || !Array.isArray(window.totalUnits) || oddsItems.length === 0) {
                console.warn('No totalUnits or odds items available');
                return;
              }
              const total = window.totalUnits.reduce((a, b) => a + b, 0) || 1;
              oddsItems.forEach((item, i) => {
                const percent = total > 0 ? Math.min(Math.round((window.totalUnits[i] / total) * 100), 100) : 0;
                const circle = item.querySelector('.fg');
                const text = item.querySelector('.odds-progress-text');
                if (circle) {
                  const circumference = 2 * Math.PI * 14;
                  const offset = circumference - (percent / 100) * circumference;
                  circle.style.strokeDasharray = `${circumference} ${circumference}`;
                  circle.style.strokeDashoffset = offset;
                }
                if (text) {
                  text.textContent = `${percent}% | ${window.totalUnits[i] || 0}`;
                }
              });
            } catch (e) {
              console.error('Error in drawProgressBars:', e);
            }
          };
        }
        
        // Load detail data
        GetLotteryInfo(id)
          .done((response) => {
            if (response && response.success) {
              LotteryInfo = response.data;
              window.totalUnits = LotteryInfo.totalUnits || [0, 0, 0, 0];
              RenderLotteryInfo(LotteryInfo);
            }
            console.log("✅ Lottery info loaded");
            GetOdds(key, id);
            GetUserInfo();
            countdownInterval = setInterval(updateTimer, 1000);
            startPolling(id);
            console.log("✅ Detail view initialized");
          })
          .fail(error => {
            console.error("❌ Error initializing detail view:", error);
            setTimeout(() => {
              if (isDetailOpen) {
                initializeDetailView(key, id);
              }
            }, 5000);
          });
        
      } catch (e) {
        console.error('Error in initializeDetailView:', e);
      }
    }

    function GetLotteryInfo(id) {
      return $.ajax({
        type: "POST",
        url: "<?= route("ajax/index.php") ?>",
        data: { action_type: "get_lottery", id: id || currentId },
        dataType: "json",
        timeout: 10000
      });
    }

    function GetOdds(key, id) {
      $.ajax({
        type: "POST",
        url: "<?= route("ajax/index.php") ?>",
        data: { action_type: "get_lottery_odd", key: key || currentKey, id: id || currentId },
        dataType: "json",
        timeout: 10000,
        success: function(response) {
          try {
            if (response && response.success) {
              Odds = response.data || [];
              RenderOdds(Odds);
              if (LotteryInfo.totalUnits) {
                window.totalUnits = LotteryInfo.totalUnits;
                window.drawProgressBars(remainingSeconds);
              }
            } else {
              console.error("Invalid odds response:", response);
            }
          } catch (e) {
            console.error("Error processing odds:", e);
          }
        },
        error: function(xhr, status, error) {
          console.error("Ajax error getting odds:", status, error);
        }
      });
    }

    function GetUserInfo() {
      $.ajax({
        type: "POST",
        url: "<?= route("ajax/index.php") ?>",
        data: { action_type: "get_user_info" },
        dataType: "json",
        timeout: 10000,
        success: function(response) {
          try {
            if (response && response.success) {
              UserInfo = response.data;
              RenderUserInfo(UserInfo);
            } else {
              console.error("Invalid user info response:", response);
            }
          } catch (e) {
            console.error("Error processing user info:", e);
          }
        },
        error: function(xhr, status, error) {
          console.error("Ajax error getting user info:", status, error);
        }
      });
    }

    function RenderLotteryInfo(data) {
      try {
        console.log("DEBUG: RenderLotteryInfo data:", data);

        $("#lottery_title").html(data.name);
        $("#lottery_img").attr('src', data.image);

        let nowNum = parseInt(data.now_session);
        if (isNaN(nowNum)) {
          console.warn("Warning: data.now_session not a number:", data.now_session);
          nowNum = data.now_session;
        }
        $("#now-session").text(nowNum);

        let prevNum;
        if (data.prev_session && data.prev_session != 0) {
          prevNum = parseInt(data.prev_session);
          if (isNaN(prevNum)) {
            console.warn("Warning: data.prev_session not a number:", data.prev_session);
            prevNum = data.prev_session;
          }
        } else {
          prevNum = (typeof nowNum === 'number' && !isNaN(nowNum)) ? nowNum - 1 : '';
        }
        $("#prev-session").text(prevNum);

        current_session = nowNum;
        remainingSeconds = parseInt(data.second) || 0;
        window.totalUnits = data.totalUnits || [0, 0, 0, 0];
        SelectedOdds = [];

        if (data.items && Array.isArray(data.items)) {
          for (let i = 0; i < Math.min(data.items.length, 2); i++) {
            $(`#prev-odd-${i + 1}`).html(data.items[i] || '');
          }
        }

        $("#count-down").text(convertSeconds(remainingSeconds));
        window.drawProgressBars(remainingSeconds);
      } catch (e) {
        console.error('Error rendering lottery info:', e);
      }
    }

    function RenderUserInfo(data) {
      try {
        if (!data) {
          console.error("No user data to render");
          return;
        }
        $("#money").text(formatMoneyJS(data.money || 0));
      } catch (e) {
        console.error("Error rendering user info:", e);
      }
    }

    function RenderOdds(odds) {
      try {
        const container = $("#value_odds");
        container.empty();
        if (!Array.isArray(odds)) {
          console.error("Odds is not an array:", odds);
          return;
        }
        odds.forEach((e, index) => {
          const type = (e.type || '').toLowerCase();
          const randomIndex = Math.floor(Math.random() * 3) + 1;
          const imgSrc = `/assets/image/${type}${randomIndex}.jpg`;
          let html = `
            <div class="odds-item cursor-pointer overflow-hidden border-2 border-white/20 hover:border-indigo-300 transition-all shadow-lg hover:shadow-xl"
                 data-odd='${type}' data-img='${imgSrc}' data-index='${index}' role="button" aria-label="Select ${e.name}">
              <div class="odds-progress" aria-label="Progress for ${e.name}">
                <svg class="odds-progress-circle" viewBox="0 0 36 36">
                  <circle class="bg" cx="18" cy="18" r="14"></circle>
                  <circle class="fg" cx="18" cy="18" r="14"></circle>
                </svg>
                <span class="odds-progress-text">0% | 0</span>
              </div>
              <img src="${imgSrc}" alt="${e.name || ''}" class="w-full aspect-square object-cover mb-1" loading="lazy" 
                   onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMzIiIGZpbGw9IiM2MzY2ZjEiLz4KPHN2ZyB4PSIxNiIgeT0iMTYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJ3aGl0ZSI+CjxwYXRoIGQ9Ik0xMiAyQzYuNDggMiAyIDYuNDggMiAxMnM0LjQ4IDEwIDEwIDEwIDEwLTQuNDggMTAtMTBTMTcuNTIgMiAxMiAyem0wIDNjMS42NiAwIDMgMS4zNCAzIDNzLTEuMzQgMy0zIDMtMy0xLjM0LTMtMyAxLjM0LTMgMy0zem0wIDEzLjJjLTIuNSAwLTQuNzEtMS4yOC02LTMuMjIuMDMtMS45OSA0LTMuMDggNi0zLjA4czUuOTcgMS4wOSA2IDMuMDhjLTEuMjkgMS45NC0zLjUgMy4yMi02IDMuMjJ6Ii8+Cjwvc3ZnPgo8L3N2Zz4=';">
              <p class="text-center font-semibold text-white text-sm px-2">${e.name || ''}</p>
              <p class="text-center text-xs text-gray-300 pb-2">${e.proportion || ''}</p>
            </div>
          `;
          container.append(html);
        });
        window.drawProgressBars(remainingSeconds);
      } catch (e) {
        console.error("Error rendering odds:", e);
      }
    }

    function startPolling(id) {
      try {
        if (pollingInterval) {
          clearInterval(pollingInterval);
        }
        pollingInterval = setInterval(() => {
          if (!isDetailOpen) return;
          
          $.ajax({
            type: "POST",
            url: "<?= route("ajax/index.php") ?>",
            data: { action_type: "get_lottery", id: id || currentId },
            dataType: "json",
            timeout: 8000,
            success: function(response) {
              try {
                if (response && response.success) {
                  LotteryInfo = response.data;
                  window.totalUnits = LotteryInfo.totalUnits || [0, 0, 0, 0];
                  window.drawProgressBars(remainingSeconds);
                }
              } catch (e) {
                console.error("Error in polling success:", e);
              }
            },
            error: function() {
              console.log("Polling error - will retry");
            }
          });
        }, 5000);
      } catch (e) {
        console.error("Error starting polling:", e);
      }
    }

    function updateTimer() {
      try {
        if (remainingSeconds > 0) {
          if (remainingSeconds == 57) {
            const prevResultEl = document.getElementById('prev-odd-1');
            if (prevResultEl) {
              const prevResult = prevResultEl.textContent.trim();
              if (prevResult && prevResult !== '') {
                setTimeout(() => {
                  showResultEffect(parseInt(current_session) - 1, prevResult);
                }, 500);
              }
            }
            if (typeof toastr !== 'undefined') {
              toastr.success("🎊 Kết quả phiên " + (parseInt(current_session) - 1) + " đã có!", '', {
                timeOut: 6000,
                positionClass: 'toast-top-center'
              });
            }
          }
          if (remainingSeconds == 5) {
            if (typeof toastr !== 'undefined') {
              toastr.warning(`⚡ Còn 5 giây phiên ${current_session}`, '', {
                timeOut: 4000,
                positionClass: 'toast-top-center'
              });
            }
          }
          $("#count-down").text(convertSeconds(remainingSeconds));
          remainingSeconds--;
          window.drawProgressBars(remainingSeconds);
        } else {
          fetchNewTimeAndRestart();
        }
      } catch (e) {
        console.error("Error in updateTimer:", e);
      }
    }

    async function fetchNewTimeAndRestart() {
      try {
        $("#count-down").css({ color: "white", fontSize: "20px" }).text("🔄 Đang cập nhật kỳ mới...");
        clearInterval(countdownInterval);
        clearInterval(pollingInterval);

        const response = await $.ajax({
          type: "POST",
          url: "<?= route("ajax/index.php") ?>",
          data: { action_type: "get_lottery", id: currentId },
          dataType: "json",
          timeout: 10000
        });

        if (response && response.success) {
          LotteryInfo = response.data;
          window.totalUnits = LotteryInfo.totalUnits || [0, 0, 0, 0];
          RenderLotteryInfo(LotteryInfo);
          remainingSeconds = parseInt(LotteryInfo.second) || 60;
          countdownInterval = setInterval(updateTimer, 1000);
          startPolling(currentId);
          if (typeof toastr !== 'undefined') {
            toastr.success(`🚀 PHIÊN MỚI: ${current_session} bắt đầu!`, '', {
              timeOut: 5000,
              positionClass: 'toast-top-center'
            });
          }
          const prevResultEl = document.getElementById('prev-odd-1');
          if (prevResultEl) {
            const prevResult = prevResultEl.textContent.trim();
            if (prevResult && prevResult !== '') {
              setTimeout(() => {
                showResultEffect(parseInt(current_session) - 1, prevResult);
              }, 2000);
            }
          }
        } else {
          if (typeof toastr !== 'undefined') {
            toastr.error("Không thể cập nhật kỳ mới, đang thử lại...");
          }
          setTimeout(fetchNewTimeAndRestart, 5000);
        }
      } catch (error) {
        console.error("Error when updating new period:", error);
        if (typeof toastr !== 'undefined') {
          toastr.error("Lỗi kết nối, đang thử lại...");
        }
        setTimeout(fetchNewTimeAndRestart, 5000);
      }
    }

    // ===========================================
    // SLIDER FUNCTIONALITY
    // ===========================================

    let currentSlide = 0;
    const totalSlides = 3;

    function nextSlide() {
      currentSlide = (currentSlide + 1) % totalSlides;
      updateSliderPosition();
    }

    function previousSlide() {
      currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
      updateSliderPosition();
    }

    function updateSliderPosition() {
      const sliderWrapper = document.getElementById('sliderWrapper');
      if (sliderWrapper) {
        sliderWrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
      }
    }

    // Auto-slide functionality
    function startAutoSlide() {
      setInterval(() => {
        nextSlide();
      }, 4000); // Change slide every 4 seconds
    }

    // Make functions globally accessible
    window.nextSlide = nextSlide;
    window.previousSlide = previousSlide;
    // RESULT EFFECT FUNCTIONS
    // ===========================================

    function showResultEffect(sessionNumber, result) {
      try {
        const overlay = document.getElementById('resultOverlay');
        const sessionEl = document.getElementById('resultSession');
        const mainEl = document.getElementById('resultMain');
        
        if (!overlay || !sessionEl || !mainEl) {
          console.error('Result overlay elements not found');
          return;
        }
        
        sessionEl.textContent = `🎊 Phiên #${sessionNumber}`;
        mainEl.textContent = result;
        
        createSparkles();
        createFireworks();
        playWinSound();
        
        overlay.classList.remove('hidden');
        overlay.classList.add('active');
        
        setTimeout(() => {
          overlay.classList.remove('active');
          setTimeout(() => {
            overlay.classList.add('hidden');
          }, 500);
        }, 4500);
      } catch (e) {
        console.error('Error showing result effect:', e);
      }
    }

    function createFireworks() {
      try {
        const overlay = document.getElementById('resultOverlay');
        if (!overlay) return;
        
        const fireworkCount = 15;
        for (let i = 0; i < fireworkCount; i++) {
          setTimeout(() => {
            const firework = document.createElement('div');
            firework.className = 'firework';
            
            const x = Math.random() * window.innerWidth;
            const y = Math.random() * window.innerHeight;
            firework.style.left = x + 'px';
            firework.style.top = y + 'px';
            
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffd93d', '#ff9800', '#e91e63', '#9c27b0'];
            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            firework.style.background = `radial-gradient(circle, ${randomColor}, ${randomColor}88)`;
            
            overlay.appendChild(firework);
            
            setTimeout(() => {
              if (firework.parentNode) {
                firework.parentNode.removeChild(firework);
              }
            }, 2000);
          }, i * 150);
        }
      } catch (e) {
        console.error('Error creating fireworks:', e);
      }
    }

    function createSparkles() {
      try {
        const overlay = document.getElementById('resultOverlay');
        if (!overlay) return;
        
        const sparkleCount = 30;
        for (let i = 0; i < sparkleCount; i++) {
          setTimeout(() => {
            const sparkle = document.createElement('div');
            sparkle.className = 'sparkle';
            const x = Math.random() * window.innerWidth;
            const y = Math.random() * window.innerHeight;
            const dx = (Math.random() - 0.5) * 200;
            const dy = (Math.random() - 0.5) * 200;
            sparkle.style.left = x + 'px';
            sparkle.style.top = y + 'px';
            sparkle.style.setProperty('--dx', dx + 'px');
            sparkle.style.setProperty('--dy', dy + 'px');
            overlay.appendChild(sparkle);
            setTimeout(() => {
              if (sparkle.parentNode) {
                sparkle.parentNode.removeChild(sparkle);
              }
            }, 1500);
          }, i * 50);
        }
      } catch (e) {
        console.error('Error creating sparkles:', e);
      }
    }

    function playWinSound() {
      try {
        if (window.audioContextStarted) {
          const audioContext = new (window.AudioContext || window.webkitAudioContext)();
          
          const frequencies = [523.25, 659.25, 783.99, 1046.5]; // C5, E5, G5, C6
          
          frequencies.forEach((freq, index) => {
            setTimeout(() => {
              const oscillator = audioContext.createOscillator();
              const gainNode = audioContext.createGain();
              
              oscillator.connect(gainNode);
              gainNode.connect(audioContext.destination);
              
              oscillator.frequency.setValueAtTime(freq, audioContext.currentTime);
              oscillator.type = 'sine';
              
              gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
              gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.8);
              
              oscillator.start(audioContext.currentTime);
              oscillator.stop(audioContext.currentTime + 0.8);
            }, index * 200);
          });
        }
      } catch (e) {
        console.log('Web Audio not supported');
      }
    }

    // ===========================================
    // BOTTOM SHEET FUNCTIONS
    // ===========================================

    function showBottomSheet() {
      try {
        const sheet = $("#bottom-sheet");
        sheet.addClass("show");
        isBottomSheetOpen = true;
      } catch (e) {
        console.error("Error showing bottom sheet:", e);
      }
    }

    function hideBottomSheet() {
      try {
        const sheet = $("#bottom-sheet");
        sheet.removeClass("show");
        isBottomSheetOpen = false;
      } catch (e) {
        console.error("Error hiding bottom sheet:", e);
      }
    }

    function resetInput() {
      try {
        hideBottomSheet();
        $(".bet-number").html(SelectedOdds.join(","));
        $("#totalBetOption").html(0);
        $("#txt-lot-money").val(1);
        $("#totalBetMoney").html(0);
      } catch (e) {
        console.error("Error resetting input:", e);
      }
    }

    // ===========================================
    // EVENT HANDLERS
    // ===========================================

    $(document).ready(function() {
      // Initialize main view
      if (pageKey && pageId) {
        // If URL has parameters, load items first then show detail
        $.ajax({
          type: "POST",
          url: "<?= route("ajax/index.php") ?>",
          data: { action_type: "get_lottery_items" },
          dataType: "json",
          success: function(response) {
            items = response.data;
            showReview(-1);
            // Initialize detail section with default state
            initializeDefaultDetailSection();
            // Then update with specific data
            setTimeout(() => {
              updateDetailSection(pageKey, pageId);
            }, 500);
          }
        });
      } else {
        // Load items for main view and initialize default detail section
        $.ajax({
          type: "POST",
          url: "<?= route("ajax/index.php") ?>",
          data: { action_type: "get_lottery_items" },
          dataType: "json",
          success: function(response) {
            items = response.data;
            showReview(-1);
            // Initialize detail section with default state
            initializeDefaultDetailSection();
          }
        });
      }

      function initializeDefaultDetailSection() {
        // Set default content for detail section
        $("#lottery_title").text("Chọn một mục để xem chi tiết");
        $("#lottery_img").attr('src', '');
        $("#now-session").text('-');
        $("#prev-session").text('-');
        $("#prev-odd-1, #prev-odd-2").text('');
        $("#count-down").text('--:--:--');
        $("#money").text('0');
        
        // Show default message in odds grid
        $("#value_odds").html(`
          <div class="col-span-2 text-center py-8">
            <svg class="w-16 h-16 mx-auto mb-4 text-white/50" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-white/70 text-lg font-medium">Chọn một mục từ danh sách trên để bắt đầu đánh giá</p>
            <p class="text-white/50 text-sm mt-2">Click vào bất kỳ card nào ở trên để xem chi tiết và bắt đầu đặt cược</p>
          </div>
        `);
      }

      // ===========================================
      // PRODUCT BUTTON HANDLERS
      // ===========================================

      $(document).on('click', '.product-btn.th-truemilk', function() {
        $(this).addClass('loading');
        if (typeof toastr !== 'undefined') {
          toastr.info('Đang tải sản phẩm TH TRUEMILK...', 'Thông báo', {
            timeOut: 2000,
            positionClass: 'toast-top-center'
          });
        }
        
        // Simulate loading and filter products
        setTimeout(() => {
          $(this).removeClass('loading');
          showReview(-1); // Show all items or filter by TH TRUEMILK
        }, 1000);
      });

      $(document).on('click', '.product-btn.vinamilk', function() {
        $(this).addClass('loading');
        if (typeof toastr !== 'undefined') {
          toastr.info('Đang tải sản phẩm VINAMILK...', 'Thông báo', {
            timeOut: 2000,
            positionClass: 'toast-top-center'
          });
        }
        
        // Simulate loading and filter products
        setTimeout(() => {
          $(this).removeClass('loading');
          showReview(-1); // Show all items or filter by VINAMILK
        }, 1000);
      });

      // ===========================================
      // WHEEL INTERACTION
      // ===========================================

      $(document).on('click', '.wheel-container', function() {
        const wheel = $(this).find('.wheel-img');
        
        // Add special spin effect
        wheel.css('animation', 'spinPulse 2s linear');
        
        // Reset animation after completion
        setTimeout(() => {
          wheel.css('animation', 'spin 3s linear infinite');
        }, 2000);
        
        if (typeof toastr !== 'undefined') {
          toastr.success('🎯 Vòng quay may mắn!', 'Chúc mừng!', {
            timeOut: 3000,
            positionClass: 'toast-top-center'
          });
        }
      });
      // MENU AND NAVIGATION HANDLERS
      // ===========================================

      $("#hamburger").click(function() {
        $("#sidebar").toggleClass("sidebar-closed sidebar-open");
        $("#overlay").toggleClass("hidden");
      });

      $("#overlay, #close-sidebar").click(function() {
        $("#sidebar").removeClass("sidebar-open").addClass("sidebar-closed");
        $("#overlay").addClass("hidden");
      });

      $("#btn-close-detail").on("click", function() {
        closeDetailSection();
      });

      // ===========================================
      // DETAIL VIEW EVENT HANDLERS
      // ===========================================

      $(document).on("click touchstart", ".odds-item", function(e) {
        if (!isDetailOpen) return;
        
        try {
          window.audioContextStarted = true;
          const type = $(this).data("odd").toUpperCase();
          if ($(this).hasClass("active")) {
            $(this).removeClass("active");
            SelectedOdds = SelectedOdds.filter(t => t !== type);
          } else {
            $(this).addClass("active");
            SelectedOdds.push(type);
          }
          
          if (SelectedOdds.length > 0 && !isBottomSheetOpen) {
            showBottomSheet();
          } else if (SelectedOdds.length === 0) {
            hideBottomSheet();
          }
          
          $(".bet-number").text(SelectedOdds.join(","));
          $("#totalBetOption").text(SelectedOdds.length);
          const currentMoney = $("#txt-lot-money").val() || 1;
          $("#totalBetMoney").text(SelectedOdds.length * currentMoney);
        } catch (e) {
          console.error("Error handling odds click:", e);
        }
      });

      $("#txt-lot-money").on("input", function(e) {
        try {
          let money = e.target.value || 1;
          $("#totalBetMoney").html(SelectedOdds.length * money);
        } catch (e) {
          console.error("Error updating bet money:", e);
        }
      });

      $("#btn-submit").on("click touchstart", function() {
        if (!isDetailOpen) return;
        
        try {
          if (SelectedOdds.length == 0) {
            if (typeof toastr !== 'undefined') {
              return toastr.error("❌ VUI LÒNG CHỌN LỰA CHỌN!", "Lỗi", {
                timeOut: 4000,
                positionClass: 'toast-top-center'
              });
            }
            return;
          }
          const mulcount = 1;
          let tiencuoc = $("#txt-lot-money").val() || 1;
          if (tiencuoc <= 0) {
            if (typeof toastr !== 'undefined') {
              return toastr.error("❌ SỐ ĐIỂM KHÔNG HỢP LỆ!", "Lỗi nhập liệu", {
                timeOut: 4000,
                positionClass: 'toast-top-center'
              });
            }
            return;
          }
          if (SelectedOdds.length * tiencuoc > (UserInfo.money || 0)) {
            if (typeof toastr !== 'undefined') {
              return toastr.error("💸 KHÔNG ĐỦ ĐIỂM ĐỂ ĐẶT!", "Số dư không đủ", {
                timeOut: 4000,
                positionClass: 'toast-top-center'
              });
            }
            return;
          }
          $("#bet-confirm-list").empty();
          SelectedOdds.forEach(e => {
            const oddData = Odds.find(a => a.type == e);
            const oddName = oddData ? oddData.name : e;
            let html = `<li class="bg-indigo-50 p-3 border border-indigo-200 rounded-lg">
              <div class="flex justify-between items-center">
                <div>
                  <p class="font-bold text-gray-800">${e}: ${oddName}</p>
                  <p class="text-sm text-gray-600">${mulcount} lần cược × ${tiencuoc} = ${mulcount * tiencuoc} điểm</p>
                </div>
                <div class="text-2xl">💎</div>
              </div>
            </li>`;
            $("#bet-confirm-list").append(html);
          });
          $("#modalConfirm").removeClass("hidden");
        } catch (e) {
          console.error("Error submitting bet:", e);
        }
      });

      $("#btn-close-bottomsheet").on("click touchstart", function() {
        hideBottomSheet();
      });

      $("#btn-confirm").on("click touchstart", function() {
        if (!isDetailOpen) return;
        
        try {
          $.ajax({
            type: "POST",
            url: "<?= route("ajax/index.php") ?>",
            data: {
              action_type: "do_bet",
              lid: currentId,
              money: $("#txt-lot-money").val() || 1,
              item: SelectedOdds.join(','),
              session: current_session
            },
            dataType: "json",
            timeout: 10000,
            success: function(response) {
              try {
                if (response && response.success) {
                  if (typeof toastr !== 'undefined') {
                    toastr.success("Thành công", {
                      timeOut: 5000,
                      positionClass: 'toast-top-center'
                    });
                  }
                  GetUserInfo();
                  $("#modalConfirm").addClass("hidden");
                  SelectedOdds = [];
                  $(".odds-item").removeClass("active");
                  resetInput();
                  GetLotteryInfo(currentId).done((res) => {
                    if (res && res.success) {
                      LotteryInfo = res.data;
                      window.totalUnits = LotteryInfo.totalUnits || [0, 0, 0, 0];
                      RenderLotteryInfo(LotteryInfo);
                    }
                  });
                } else {
                  if (typeof toastr !== 'undefined') {
                    toastr.error(response.message || "Lỗi không xác định");
                  }
                  setTimeout(() => {
                    window.location.reload();
                  }, 2000);
                }
              } catch (e) {
                console.error("Error processing bet response:", e);
              }
            },
            error: function() {
              console.error("Error submitting bet");
              if (typeof toastr !== 'undefined') {
                toastr.error("Lỗi kết nối, vui lòng thử lại");
              }
            }
          });
        } catch (e) {
          console.error("Error in bet confirm:", e);
        }
      });

      // ===========================================
      // MODAL HANDLERS
      // ===========================================

      $(document).on("click touchstart", "#closeConfirm, #noConfirm", function () {
        $("#modalConfirm").addClass("hidden");
      });

      // ===========================================
      // ADDITIONAL EVENT HANDLERS
      // ===========================================

      $("#lottery_img").on('error', function() {
        $(this).attr('src', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMzIiIGZpbGw9IiM2MzY2ZjEiLz4KPHN2ZyB4PSIxNiIgeT0iMTYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJ3aGl0ZSI+CjxwYXRoIGQ9Ik0xMiAyQzYuNDggMiAyIDYuNDggMiAxMnM0LjQ4IDEwIDEwIDEwIDEwLTQuNDggMTAtMTBTMTcuNTIgMiAxMiAyem0wIDNjMS42NiAwIDMgMS4zNCAzIDNzLTEuMzQgMy0zIDMtMy0xLjM0LTMtMyAxLjM0LTMgMy0zem0wIDEzLjJjLTIuNSAwLTQuNzEtMS4yOC02LTMuMjIuMDMtMS45OSA0LTMuMDggNi0zLjA4czUuOTcgMS4wOSA2IDMuMDhjLTEuMjkgMS45NC0zLjUgMy4yMi02IDMuMjJ6Ii8+Cjwvc3ZnPgo8L3N2Zz4=');
      });

      // Bottom Sheet Drag-to-Close
      let touchStartY = 0;
      let touchCurrentY = 0;
      $("#bottom-sheet").on("touchstart", function(e) {
        touchStartY = e.originalEvent.touches[0].clientY;
      });
      $("#bottom-sheet").on("touchmove", function(e) {
        touchCurrentY = e.originalEvent.touches[0].clientY;
        const diff = touchCurrentY - touchStartY;
        if (diff > 50) {
          hideBottomSheet();
        }
      });

      // Click overlay to close result
      $("#resultOverlay").on("click touchstart", function(e) {
        if (e.target === this) {
          $(this).removeClass('active');
          setTimeout(() => {
            $(this).addClass('hidden');
          }, 500);
        }
      });

      // Enable audio context on user interaction
      document.addEventListener('click touchstart', function(e) {
        try {
          window.audioContextStarted = true;
          if (e.target.matches('[data-odd]') || e.target.closest('[data-odd]')) {
            if (!isBottomSheetOpen && SelectedOdds.length > 0) {
              showBottomSheet();
            }
          }
        } catch (e) {
          console.error("Error in click handler:", e);
        }
      });

      // Smooth scroll behavior for internal links
      $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if( target.length ) {
          event.preventDefault();
          $('html, body').stop().animate({
            scrollTop: target.offset().top - 80
          }, 1000);
        }
      });

      // Close detail section when clicking outside
      $(document).on('click', function(e) {
        if (isDetailOpen && !$(e.target).closest('#detailSection, .review-card').length) {
          // Don't close if clicking on bottom sheet or modals
          if (!$(e.target).closest('#bottom-sheet, #modalConfirm, #resultOverlay').length) {
            // Optional: uncomment to close on outside click
            // closeDetailSection();
          }
        }
      });

      // Handle window resize
      $(window).on('resize', function() {
        if (isDetailOpen && window.innerWidth > 768) {
          // Adjust layout for larger screens if needed
        }
      });

      // Keyboard shortcuts
      $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
          if (isBottomSheetOpen) {
            hideBottomSheet();
          } else if (isDetailOpen) {
            closeDetailSection();
          }
        }
      });

      // Auto-hide notifications
      setTimeout(() => {
        $('.notification').fadeOut();
      }, 5000);

      // Add loading states for better UX
      $(document).ajaxStart(function() {
        $('body').addClass('loading');
      }).ajaxStop(function() {
        $('body').removeClass('loading');
      });

      // Handle network errors gracefully
      $(document).ajaxError(function(event, xhr, settings, thrownError) {
        if (xhr.status === 0) {
          console.log('Network error - please check your connection');
          if (typeof toastr !== 'undefined') {
            toastr.error('Kiểm tra kết nối mạng', 'Lỗi kết nối');
          }
        }
      });

      // Preload critical images
      const criticalImages = [
        '/assets/image/a1.jpg',
        '/assets/image/b1.jpg', 
        '/assets/image/c1.jpg',
        '/assets/image/d1.jpg'
      ];
      
      criticalImages.forEach(src => {
        const img = new Image();
        img.src = src;
      });

      // Initialize tooltips or help text if needed
      $('[data-toggle="tooltip"]').each(function() {
        // Custom tooltip implementation
      });

      // Performance monitoring
      if ('performance' in window) {
        window.addEventListener('load', function() {
          setTimeout(() => {
            const perfData = performance.getEntriesByType('navigation')[0];
            console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
          }, 0);
        });
      }

      // Intersection Observer for lazy loading (if needed)
      if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const img = entry.target;
              img.src = img.dataset.src;
              img.classList.remove('lazy');
              imageObserver.unobserve(img);
            }
          });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
          imageObserver.observe(img);
        });
      }
    });

    // ===========================================
    // UTILITY FUNCTIONS FOR EXTERNAL ACCESS
    // ===========================================

    // Make functions available globally if needed
    window.openReview = openReview;
    window.showReview = showReview;
    window.openDetailSection = openDetailSection;
    window.closeDetailSection = closeDetailSection;
    window.formatMoneyJS = formatMoneyJS;

    // Debug helpers (remove in production)
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      window.debugInfo = {
        items: () => items,
        odds: () => Odds,
        userInfo: () => UserInfo,
        lotteryInfo: () => LotteryInfo,
        selectedOdds: () => SelectedOdds,
        currentSession: () => current_session,
        isDetailOpen: () => isDetailOpen,
        remainingSeconds: () => remainingSeconds
      };
      console.log('Debug info available at window.debugInfo');
    }

    // Service Worker registration (if available)
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        // navigator.serviceWorker.register('/sw.js')
        //   .then(registration => console.log('SW registered'))
        //   .catch(error => console.log('SW registration failed'));
      });
    }

    // Enhanced error reporting
    window.addEventListener('error', function(e) {
      console.error('Global error:', e.error);
      // Could send to error reporting service
    });

    window.addEventListener('unhandledrejection', function(e) {
      console.error('Unhandled promise rejection:', e.reason);
      // Could send to error reporting service
    });

    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
      if (countdownInterval) clearInterval(countdownInterval);
      if (pollingInterval) clearInterval(pollingInterval);
    });

    // Add custom events for component communication
    $(document).ready(function() {
      // Custom event when detail section opens
      $(document).on('detailSectionOpened', function(e, data) {
        console.log('Detail section opened:', data);
      });

      // Custom event when bet is placed
      $(document).on('betPlaced', function(e, data) {
        console.log('Bet placed:', data);
      });

      // Custom event when result is announced
      $(document).on('resultAnnounced', function(e, data) {
        console.log('Result announced:', data);
      });
    });

    // PWA-like features
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
      // Show install button if desired
    });

    // Handle app install
    function installApp() {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
          if (choiceResult.outcome === 'accepted') {
            console.log('App installed');
          }
          deferredPrompt = null;
        });
      }
    }

    // Accessibility improvements
    $(document).ready(function() {
      // Add ARIA labels where needed
      $('.review-card').attr('role', 'button');
      $('.odds-item').attr('role', 'button');
      
      // Keyboard navigation
      $('.review-card, .odds-item').on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          $(this).click();
        }
      });

      // Focus management
      $('#detailSection').on('shown', function() {
        $(this).find('button:first').focus();
      });

      // Screen reader announcements
      function announceToScreenReader(message) {
        const announcement = $('<div>')
          .attr('aria-live', 'polite')
          .attr('aria-atomic', 'true')
          .addClass('sr-only')
          .text(message);
        
        $('body').append(announcement);
        setTimeout(() => announcement.remove(), 1000);
      }

      // Use announcements for important events
      $(document).on('detailSectionOpened', function() {
        announceToScreenReader('Chi tiết đánh giá đã mở');
      });

      $(document).on('betPlaced', function() {
        announceToScreenReader('Đặt cược thành công');
      });
    });

    // Final initialization message
    console.log('🎉 Hệ thống Đánh giá COUPANG đã sẵn sàng!');
  </script>

  <?php 
  if (function_exists('endpage')) {
    endpage(false); 
  } else {
    echo "<!-- endpage function not found -->";
  }
  ?>
</body>
</html>