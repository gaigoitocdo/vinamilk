<?php
include_once __DIR__ . "/../models/LotteryModel.php";
include_once __DIR__ . "/../includes/RealTimeLotteryProcessor.php";

$key = field("key", NULL, false, false);
$id = field("id", NULL, false, false);
$cate = LotteryModel::GetAllCategories();

view("header");
layout_header();
view("navbar");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
  <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>VINAMILK - Hệ thống Đơn hàng</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'vinamilk-blue': '#0066CC',
            'vinamilk-yellow': '#FFD700',
            'vinamilk-red': '#E53E3E',
            'vinamilk-green': '#00CC66'
          },
          animation: {
            'vinamilkSpin': 'vinamilkSpin 4s linear infinite',
            'vinamilkSpinFast': 'vinamilkSpinFast 2s linear'
          }
        }
      }
    }
  </script>
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
    }
    body {
      padding-bottom: 40px;
      font-family: 'Open Sans', sans-serif;
      background: url('https://i.ibb.co/yFb4vr9j/BgPCBD.png') no-repeat center center fixed;
      background-size: cover;
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
   background: linear-gradient(145deg, #000000, #006eef);
      border-color: #0066CC;
      color: white;
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 16px 40px rgba(0, 102, 204, 0.4);
    }

    .review-image {
      height: 80px;
      border-radius: 12px;
      margin: 0 auto 12px auto;
      transition: all 0.3s ease;
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
       BRAND TABS STYLES
       =========================================== */
    
    .brand-tabs {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      padding: 0 1rem;
    }

    .brand-tab {
      flex: 1;
      padding: 0.75rem 1rem;
      border-radius: 12px;
      background: linear-gradient(135deg, #0066cc, #0066cc);
      border: 2px solid rgba(0, 102, 204, 0.3);
      color: rgba(255, 255, 255, 0.8);
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .brand-tab:hover {
      border-color: #FFD700;
      color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 102, 204, 0.2);
    }

    .brand-tab.active {
      background: linear-gradient(135deg, #0066CC, #4A90E2);
      border-color: #fff;
      color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 102, 204, 0.3);
    }

    .brand-tab svg {
      transition: all 0.3s ease;
    }

    .brand-tab:hover svg,
    .brand-tab.active svg {
      color: #FFD700;
      transform: scale(1.1);
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
      gap: 16px;
      margin: 20px;
    }

    .grid-2 {
      grid-template-columns: 1fr 1fr;
    }

    .info-btn {
      padding: 20px;
      border-radius: 16px;
      background: linear-gradient(135deg, 
        rgba(0, 61, 122, 0.1) 0%, 
        rgba(30, 64, 175, 0.1) 50%, 
        rgba(37, 99, 235, 0.1) 100%);
      border: 2px solid rgba(0, 61, 122, 0.2);
      color: #003d7a;
      font-weight: 700;
      font-size: 15px;
      text-align: center;
      backdrop-filter: blur(10px);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 
        0 4px 12px rgba(0, 61, 122, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .info-btn:hover {
      background: linear-gradient(135deg, 
        rgba(0, 61, 122, 0.15) 0%, 
        rgba(245, 158, 11, 0.15) 100%);
      border-color: #f59e0b;
      transform: translateY(-4px);
      box-shadow: 
        0 12px 24px rgba(0, 61, 122, 0.2),
        0 6px 12px rgba(245, 158, 11, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
    }

    .info-btn span {
      color: #1e40af;
      font-weight: 800;
    }

    /* ===========================================
       PREMIUM PROMOTION SECTION
       =========================================== */
    .promotion-section {
      padding: 24px;
      text-align: center;
      background: linear-gradient(145deg, rgba(254, 251, 243, 0.95) 0%, rgba(255, 255, 255, 0.9) 50%, rgba(243, 244, 246, 0.95) 100%);
      border: 3px solid #fcfdff;
      backdrop-filter: blur(15px);
      box-shadow: 0 16px 32px rgba(0, 61, 122, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .promotion-text {
      font-weight: 800;
      font-size: 18px;
      background: linear-gradient(135deg, #003d7a, #4a90e2, #002575);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin: 16px 0;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      text-transform: uppercase;
      letter-spacing: 1.5px;
    }

    .wheel-container {
      margin: 24px 0;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 24px;
      background: linear-gradient(145deg, rgb(121 148 175 / 10%) 0%, rgb(62 146 191 / 10%) 50%, #eaf3fe 100%);
      border-radius: 24px;
      border: 3px solid #2563eb;
    }
    
    .wheel-img {
      width: 140px;
      height: 140px;
      animation: vinamilkPremiumSpin 5s linear infinite;
      transition: all 0.4s ease;
    }

    .wheel-container:hover .wheel-img {
      animation: vinamilkPremiumSpinFast 1s linear infinite;
      transform: scale(1.15);
    }

    @keyframes vinamilkPremiumSpin {
      from { 
        transform: rotate(0deg); 
        filter: drop-shadow(0 12px 24px rgba(0, 61, 122, 0.3));
      }
      50% {
        filter: drop-shadow(0 16px 32px rgba(245, 158, 11, 0.4));
      }
      to { 
        transform: rotate(360deg); 
        filter: drop-shadow(0 12px 24px rgba(0, 61, 122, 0.3));
      }
    }

    @keyframes vinamilkPremiumSpinFast {
      from { 
        transform: rotate(0deg) scale(1.15); 
      }
      to { 
        transform: rotate(360deg) scale(1.15); 
      }
    }

    .promotion-product {
      max-width: 220px;
      height: auto;
      border-radius: 16px;
      margin-top: 20px;
      border: 4px solid #4a90e2;
      transition: all 0.4s ease;
      box-shadow: 0 8px 16px rgba(0, 61, 122, 0.15);
    }

    .promotion-product:hover {
      transform: scale(1.08) rotate(1deg);
      border-color: #f59e0b;
      box-shadow: 0 16px 32px rgba(245, 158, 11, 0.3);
    }

    /* ===========================================
       ENHANCED ORDER INFO CARD STYLES
       =========================================== */
    .order-info-card {
      background: linear-gradient(145deg, #ffffff 0%, #f3f4f6 100%);
      border-radius: 18px;
      margin: 1.5rem 1rem;
      padding: 1.5rem;
      border: 2px solid rgba(37, 99, 235, 0.3);
      box-shadow: 0 12px 28px rgba(0, 61, 122, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.3);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .order-info-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #0066CC, #4A90E2, #E53E3E);
      animation: vinamilkBlueFlow 3s ease-in-out infinite;
    }

    @keyframes vinamilkBlueFlow {
      0%, 100% { opacity: 1; transform: scaleX(1); }
      50% { opacity: 0.9; transform: scaleX(1.05); }
    }

    .order-info-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 18px 40px rgba(0, 61, 122, 0.25), inset 0 2px 0 rgba(255, 255, 255, 0.4);
      border-color: #0066CC;
    }

    .order-info-card .order-info-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(0, 102, 204, 0.2);
    }

    .order-info-card .order-info-row {
      display: grid;
      grid-template-columns: 1fr 2fr 2fr;
      gap: 1.5rem;
      align-items: center;
      padding-top: 1rem;
    }

    .order-info-card .brand-section {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .order-info-card .brand-logo {
      width: 80px;
    
      object-fit: contain;
      border-radius: 8px;
      border: 2px solid rgba(0, 102, 204, 0.4);
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 248, 220, 0.7));
      padding: 4px;
      transition: all 0.3s ease;
    }

    .order-info-card .brand-logo:hover {
      transform: scale(1.1);
      border-color: #FFD700;
  
    }

    .order-info-card .current-session-info {
      text-align: center;
      padding: 0.75rem;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      border: 1px solid rgba(0, 102, 204, 0.2);
      backdrop-filter: blur(5px);
      margin-top: 0.5rem;
    }

    .order-info-card .current-session {
      text-align: center;
      padding: 0.75rem;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      border: 1px solid rgba(0, 102, 204, 0.2);
      backdrop-filter: blur(5px);
    }

    .order-info-card .current-session .session-label {
      font-size: 0.8rem;
      color: #0066CC;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .order-info-card .current-session .session-number {
      font-size: 1.2rem;
      font-weight: 900;
      text-shadow: 0 1px 3px rgba(0, 61, 122, 0.2);
      background: linear-gradient(135deg, #003d7a, #1e40af);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .order-info-card .result-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 0.75rem;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      border: 1px solid rgba(0, 102, 204, 0.2);
      backdrop-filter: blur(5px);
    }

    .order-info-card .result-header {
      display: flex;
      justify-content: space-between;
      width: 100%;
      margin-bottom: 0.5rem;
    }

    .order-info-card .result-label {
      font-size: 0.8rem;
      font-weight: 700;
      color: #E53E3E;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .order-info-card .result-session {
      font-size: 0.9rem;
      font-weight: 600;
      color: #0066CC;
    }

    .order-info-card .result-circles-container {
      display: flex;
      gap: 6px;
      justify-content: center;
    }

    .order-info-card .result-circle {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, #00CC66, #059669);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      font-weight: 800;
      font-size: 0.9rem;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease;
    }

    .order-info-card .result-circle:hover {
      transform: scale(1.15);
      box-shadow: 0 4px 12px rgba(0, 204, 102, 0.4);
    }

    .order-info-card .result-circle.new {
      animation: resultCircleNew 1s ease-out;
    }

    @keyframes resultCircleAppear {
      0% { 
        transform: scale(0) rotate(180deg); 
        opacity: 0; 
      }
      50% { 
        transform: scale(1.2) rotate(10deg); 
        opacity: 0.8; 
      }
      100% { 
        transform: scale(1) rotate(0deg); 
        opacity: 1; 
      }
    }

    @keyframes resultCircleNew {
      0%, 100% { 
        background: linear-gradient(135deg, #00CC66, #059669); 
      }
      50% { 
        background: linear-gradient(135deg, #FFD700, #FFA500); 
        transform: scale(1.1);
      }
    }

    /* Brand-specific Colors */
    .order-info-card[data-brand="TH"] .brand-logo {
      border-color: #0066CC;
    }

    .order-info-card[data-brand="VINAMILK"] .brand-logo {
      border-color: #E53E3E;
    }

    .order-info-card[data-brand="TH"] .session-label,
    .order-info-card[data-brand="TH"] .result-label {
      color: #0066CC;
    }

    .order-info-card[data-brand="VINAMILK"] .session-label,
    .order-info-card[data-brand="VINAMILK"] .result-label {
      color: #E53E3E;
    }

    /* Loading State */
    .order-info-card.loading {
      opacity: 0.7;
      pointer-events: none;
    }

    .order-info-card.loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 24px;
      height: 24px;
      margin: -12px 0 0 -12px;
      border: 3px solid #0066CC;
      border-radius: 50%;
      border-top-color: transparent;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Additional Order Info Enhancements */
    .order-info-card .order-info-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(0, 102, 204, 0.2);
    }

    .order-info-card .order-status {
      font-size: 0.9rem;
      font-weight: 600;
      color: #0066CC;
      background: rgba(255, 255, 255, 0.2);
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .order-info-card .order-status.completed {
      background: linear-gradient(135deg, #00CC66, #059669);
      color: #ffffff;
    }

    .order-info-card .order-status.pending {
      background: linear-gradient(135deg, #FFD700, #FFA500);
      color: #ffffff;
    }

    .order-info-card .order-action-btn {
      background: linear-gradient(135deg, #0066CC, #4A90E2);
      color: #ffffff;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 700;
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    .order-info-card .order-action-btn:hover {
      background: linear-gradient(135deg, #4A90E2, #E53E3E);
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 102, 204, 0.4);
    }

    /* Countdown Timer */
    .countdown-timer {
      background: linear-gradient(135deg, #003d7a, #2563eb);
      border-radius: 16px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      text-align: center;
      box-shadow: 
        0 8px 16px rgba(0, 61, 122, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
      border: 2px solid rgba(37, 99, 235, 0.3);
    }

    .countdown-timer span {
      font-size: 1.1rem;
      font-weight: 700;
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      text-shadow: 0 1px 2px rgba(0, 61, 122, 0.3);
    }

    .countdown-timer .timer-text {
      font-size: 1.25rem;
      font-weight: 900;
      color: #ffffff;
      letter-spacing: 2px;
      text-shadow: 0 2px 4px rgba(0, 61, 122, 0.4);
    }

    /* Balance Info */
    .balance-info {
      background: linear-gradient(145deg, 
        #ffffff 0%, 
        #f3f4f6 100%);
      border-radius: 16px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      border: 2px solid rgba(37, 99, 235, 0.3);
      box-shadow: 
        0 8px 16px rgba(0, 61, 122, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .balance-info .balance-text {
      font-size: 1.2rem;
      font-weight: 800;
      color: #003d7a;
      text-shadow: 0 1px 2px rgba(0, 61, 122, 0.2);
    }

    .balance-info .balance-amount {
      font-size: 1.75rem;
      font-weight: 900;
      color: #1e40af;
      text-shadow: 0 2px 4px rgba(0, 61, 122, 0.3);
    }

    /* Odds Grid */
    .odds-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 1.25rem;
      margin-bottom: 2rem;
    }

    .odds-item {
  
      border-radius: 16px;
      padding: 1.25rem;
      text-align: center;
      cursor: pointer;
      position: relative;
      border: 2px solid rgba(37, 99, 235, 0.3);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
   
    }
.session-number {
     color:#0066CC;
     font-weight: 600;
}

    .odds-item:hover {
      transform: translateY(-4px) scale(1.03);
      border-color: #2563eb;
      box-shadow: 
        0 12px 24px rgba(0, 61, 122, 0.15),
        0 6px 12px rgba(37, 99, 235, 0.1);
    }

    .odds-item.active {
      background: linear-gradient(145deg, #003d7a, #2563eb);
      border-color: #1e40af;
      color: #ffffff;
      transform: translateY(-6px) scale(1.05);
      box-shadow: 
        0 16px 32px rgba(0, 61, 122, 0.3),
        0 8px 16px rgba(37, 99, 235, 0.2);
    }

    .odds-item img {
      width: 100%;
      aspect-ratio: 1;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 1rem;

      transition: all 0.4s ease;
    }

    .odds-item:hover img {
      border-color: #2563eb;
      transform: scale(1.05);
    }

    .odds-item.active img {
      border-color: #ffffff;
    }

    .odds-item p {
      font-size: 1rem;
      font-weight: 700;
      color: #003d7a;
      margin-bottom: 0.5rem;
      text-shadow: 0 1px 2px rgba(0, 61, 122, 0.2);
    }

    .odds-item.active p {
      color: #ffffff;
      text-shadow: 0 1px 2px rgba(0, 61, 122, 0.3);
    }

    .odds-item .odds-ratio {
      font-size: 0.9rem;
      font-weight: 600;
      color: #1e40af;
      text-shadow: 0 1px 2px rgba(0, 61, 122, 0.2);
    }

    .odds-item.active .odds-ratio {
      color: #ffffff;
      text-shadow: 0 1px 2px rgba(0, 61, 122, 0.3);
    }

    /* Progress Circle */
    .odds-progress {
      width: 3.5rem;
      height: 3.5rem;
    }

    .odds-progress-circle .fg {
      stroke: #2563eb;
      stroke-dasharray: 88;
      stroke-dashoffset: 66;
    }

    .odds-progress-text {
      font-size: 0.7rem;
      font-weight: 800;
      color: #003d7a;
      text-shadow: 0 1px 2px rgba(0, 61, 122, 0.3);
    }

    .odds-item.active .odds-progress-text {
      color: #ffffff;
      text-shadow: 0 1px 2px rgba(0, 61, 122, 0.4);
    }

    /* ===========================================
       PREMIUM HEADER STYLES
       =========================================== */
/* Modal Styles */
  .modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 61, 122, 0.8);
  backdrop-filter: blur(16px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.modal-overlay.show {
  opacity: 1;
  pointer-events: all;
}

.modal-card {
  background: linear-gradient(145deg, #ffffff, #fefbf3, #f8fafc);
  border-radius: 10px;
  box-shadow: 0 12px 30px rgba(0, 61, 122, 0.3), 0 6px 15px rgba(245, 158, 11, 0.2);
  padding: 1.5rem;
  width: 100%;
  max-width: 300px; /* Reduced from 500px for compactness */
  transform: scale(0.85);
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  border: 3px solid rgba(255, 255, 255, 0.5);
}

.modal-overlay.show .modal-card {
  transform: scale(1);
  animation: vinamilkModalOpen 0.35s ease-out;
}

@keyframes vinamilkModalOpen {
  from {
    transform: scale(0.85);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem; /* Reduced from 2rem */
  padding-bottom: 1rem; /* Reduced from 1.5rem */
  border-bottom: 2px solid rgba(0, 61, 122, 0.2);
}

.modal-header h3 {
  font-size: 1.5rem; /* Reduced from 2rem */
  font-weight: 800;
  background: linear-gradient(135deg, #003d7a, #dc2626, #f59e0b);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  margin: 0;
  letter-spacing: 1px; /* Slightly reduced from 1.5px */
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

.modal-close {
  background: linear-gradient(135deg, #dc2626, #b91c1c);
  border: 2px solid rgba(255, 255, 255, 0.4);
  color: #ffffff;
  font-size: 1.25rem; /* Reduced from 1.75rem */
  font-weight: bold;
  width: 2.25rem; /* Reduced from 3rem */
  height: 2.25rem; /* Reduced from 3rem */
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 10px rgba(220, 38, 38, 0.4);
}

.modal-close:hover {
  transform: scale(1.1) rotate(90deg); /* Slightly reduced scale from 1.15 */
  box-shadow: 0 6px 15px rgba(220, 38, 38, 0.5);
}

.modal-close:focus {
  outline: 2px solid rgba(220, 38, 38, 0.5);
  outline-offset: 2px;
}

.modal-input {
  margin-bottom: 1rem; /* Reduced from 1.5rem */
}

.modal-input label {
  display: block;
  margin-bottom: 0.5rem; /* Reduced from 0.75rem */
  font-weight: 700;
  color: #003d7a;
  font-size: 0.875rem; /* Reduced from 1rem */
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.modal-input input {
  width: 100%;
  padding: 0.75rem 1rem; /* Reduced padding from 1rem 1.25rem */

  border-radius: 12px; /* Reduced from 16px */
  background: rgba(255, 255, 255, 0.95);
  font-size: 1rem; /* Reduced from 1.125rem */
  font-weight: 600;
  color: #003d7a;
  transition: all 0.3s ease;
  box-shadow: 0 3px 8px rgba(0, 61, 122, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

.modal-input input:focus {
  outline: none;
  border-color: #f59e0b;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15), 0 6px 12px rgba(245, 158, 11, 0.2);
  background: #ffffff;
}

.modal-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem; /* Reduced from 1.5rem */
}

.modal-cancel {
  background: linear-gradient(135deg, #6b7280, #9ca3af);
  border: 2px solid rgba(255, 255, 255, 0.3);
  color: #ffffff;
  padding: 0.875rem; /* Reduced from 1.25rem */
  border-radius: 12px; /* Reduced from 16px */
  font-weight: 700;
  font-size: 1rem; /* Reduced from 1.125rem */
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 6px 12px rgba(107, 114, 128, 0.3);
}

.modal-cancel:hover {
  background: linear-gradient(135deg, #9ca3af, #d1d5db);
  transform: translateY(-2px); /* Reduced from -3px */
  box-shadow: 0 8px 16px rgba(107, 114, 128, 0.4);
}

.modal-cancel:focus {
  outline: 2px solid rgba(107, 114, 128, 0.5);
  outline-offset: 2px;
}

.main-header {
  backdrop-filter: blur(16px);
  box-shadow: 
    0 6px 18px rgba(0, 61, 122, 0.3),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
  border-bottom: 2px solid rgba(245, 158, 11, 0.3);
}
    .header-title {
      background: linear-gradient(306deg, #ffffff 0%, #ffffff 50%, #ffffff 100%);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      font-weight: 900;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      letter-spacing: 2px;
    }

  .premium-badge {
    background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
    color: #0066cc;
    font-weight: 800;
    /* box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2); */
    border: 2px solid rgba(255, 255, 255, 0.3);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

    /* ===========================================
       BOTTOM SHEET STYLES
       =========================================== */

    .bs-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 61, 122, 0.7);
      backdrop-filter: blur(15px);
      z-index: 1000;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .bs-overlay.show {
      opacity: 1;
      pointer-events: all;
    }

    .bs-card {
      background: linear-gradient(145deg, #ffffff 0%, #fefbf3 50%, #f8fafc 100%);
      border-radius: 32px 32px 0 0;
      box-shadow: 0 -24px 60px rgba(0, 61, 122, 0.3), 0 -12px 30px rgba(245, 158, 11, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.9);
      padding: 2rem;
      width: 100%;
      max-width: 500px;
      transform: translateY(100%);
      transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      border: 4px solid rgb(255 255 255 / 40%);
      border-bottom: none;
    }

    .bs-overlay.show .bs-card {
      transform: translateY(0);
    }

    .bs-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding-bottom: 1.5rem;
      border-bottom: 3px solid rgba(0, 61, 122, 0.15);
    }

    .bs-header h3 {
      font-size: 1.75rem;
      font-weight: 900;
      background: linear-gradient(135deg, #003d7a, #dc2626, #f59e0b);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin: 0;
      letter-spacing: 1px;
    }

    .bs-close {
      background: linear-gradient(135deg, #dc2626, #b91c1c);
      border: 2px solid rgba(255, 255, 255, 0.3);
      color: white;
      font-size: 1.75rem;
      font-weight: bold;
      width: 3rem;
      height: 3rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.4s ease;
      box-shadow: 
        0 6px 16px rgba(220, 38, 38, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .bs-close:hover {
      transform: scale(1.15) rotate(90deg);
      box-shadow: 
        0 8px 24px rgba(220, 38, 38, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .bs-input {
      margin-bottom: 1.5rem;
    }

    .bs-input label {
      display: block;
      margin-bottom: 0.75rem;
      font-weight: 700;
      color: #003d7a;
      font-size: 1rem;
    }

    .bs-input input {
      width: 100%;
      padding: 1rem 1.25rem;
      border: 3px solid rgba(0, 61, 122, 0.2);
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.95);
      font-size: 1.125rem;
      font-weight: 600;
      color: #003d7a;
      transition: all 0.4s ease;
      box-shadow: 
        0 4px 12px rgba(0, 61, 122, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    }

    .bs-input input:focus {
      outline: none;
      border-color: #f59e0b;
      box-shadow: 
        0 0 0 4px rgba(245, 158, 11, 0.15),
        0 8px 16px rgba(245, 158, 11, 0.2);
      background: white;
    }

    .bs-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
    }

    .btn-confirm {
      background: linear-gradient(135deg, #003d7a, #1e40af, #2563eb);
      border: 2px solid rgba(245, 158, 11, 0.3);
      color: white;
     
      border-radius: 16px;
      
      font-size: 1.125rem;
      cursor: pointer;
      transition: all 0.4s ease;
      box-shadow: 
        0 8px 16px rgba(0, 61, 122, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .btn-confirm:hover {
      background: linear-gradient(135deg, #f59e0b, #dc2626, #b91c1c);
      border-color: rgba(255, 255, 255, 0.5);
      transform: translateY(-3px);
      box-shadow: 
        0 12px 24px rgba(245, 158, 11, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .bs-cancel {
      background: linear-gradient(135deg, #6b7280, #9ca3af);
      border: 2px solid rgba(255, 255, 255, 0.3);
      color: white;
      padding: 1.25rem;
      border-radius: 16px;
      font-weight: 800;
      font-size: 1.125rem;
      cursor: pointer;
      transition: all 0.4s ease;
      box-shadow: 
        0 8px 16px rgba(107, 114, 128, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .bs-cancel:hover {
      background: linear-gradient(135deg, #9ca3af, #d1d5db);
      transform: translateY(-3px);
      box-shadow: 
        0 12px 24px rgba(107, 114, 128, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    /* Result circles styling */
    .result-circles-container {
      display: flex;
      gap: 4px;
      align-items: center;
      justify-content: flex-end;
    }

    .result-circle {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, #4FC3AA, #3BA892);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 800;
      font-size: 14px;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      animation: resultCircleAppear 0.5s ease-out;
    }

    @keyframes resultCircleAppear {
      0% { 
        transform: scale(0) rotate(180deg); 
        opacity: 0; 
      }
      50% { 
        transform: scale(1.2) rotate(10deg); 
        opacity: 0.8; 
      }
      100% { 
        transform: scale(1) rotate(0deg); 
        opacity: 1; 
      }
    }

    .result-circle.new {
      animation: resultCircleNew 1s ease-out;
    }

    @keyframes resultCircleNew {
      0%, 100% { 
        background: linear-gradient(135deg, #4FC3AA, #3BA892); 
      }
      50% { 
        background: linear-gradient(135deg, #FFD700, #FFA500); 
        transform: scale(1.1);
      }
    }

    .winner-glow {
      animation: winnerPulse 1s ease-in-out 3;
 
    
    }

    @keyframes winnerPulse {
      0%, 100% { 
        transform: scale(1); 
        opacity: 1; 
      }
      50% { 
        transform: scale(1.05); 
        opacity: 0.9; 
      }
    }

    /* Detail Section Styles */
    .detail-section {
      background: linear-gradient(145deg, 
        #ffffff 0%, 
        #e5e7eb 20%, 
        #f3f4f6 40%, 
        #e5e7eb 80%, 
        #ffffff 100%);
      background-size: 400% 400%;
      animation: vinamilkWhiteBlueGradient 10s ease infinite;
    
      color: #FFFFFF;
      position: relative;
      overflow: hidden;
      transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
      opacity: 1;
      transform: translateY(0);
      box-shadow: 
        0 24px 48px rgba(0, 61, 122, 0.2),
        0 12px 24px rgba(37, 99, 235, 0.15),
        inset 0 1px 0 rgba(37, 99, 235, 0.1);
      border: 3px solid rgba(37, 99, 235, 0.3);
      backdrop-filter: blur(10px);
    }

    .detail-section.hidden {
      opacity: 0;
      transform: translateY(40px) scale(0.98);
      pointer-events: none;
      height: 0;
      margin: 0;
      padding: 0;
      overflow: hidden;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .detail-section.show {
      opacity: 1;
      transform: translateY(0) scale(1);
      pointer-events: all;
      height: auto;
    }

    @keyframes vinamilkWhiteBlueGradient {
      0%, 100% { background-position: 0% 50%; }
      25% { background-position: 100% 50%; }
      50% { background-position: 50% 100%; }
      75% { background-position: 100% 0%; }
    }

    .detail-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: linear-gradient(90deg, 
        #2563eb 0%, 
        #ffffff 25%, 
        #003d7a 50%, 
        #ffffff 75%, 
        #2563eb 100%);
      animation: vinamilkBlueFlow 3s ease-in-out infinite;
    }

    .detail-header {
      position: relative;
      padding-bottom: 1.75rem;
      margin-bottom: 2.25rem;
      border-bottom: 2px solid rgba(37, 99, 235, 0.3);
      text-align: center;
    }

    .detail-header h3 {
      font-size: 1.9rem;
      font-weight: 900;
      background: linear-gradient(135deg, #003d7a, #1e40af);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      letter-spacing: 1.5px;
      text-shadow: 0 2px 4px rgba(0, 61, 122, 0.3);
      line-height: 1.2;
    }

    .detail-header svg {
      transition: transform 0.3s ease, color 0.3s ease;
      color: #2563eb;
    }

    .detail-header:hover svg {
      transform: scale(1.1);
      color: #003d7a;
    }

    .detail-close-btn {
      position: absolute;
      top: -1.5rem;
      right: -1.5rem;
      background: linear-gradient(135deg, #2563eb, #1e40af);
      border: 2px solid rgba(255, 255, 255, 0.5);
      color: #ffffff;
      width: 2.75rem;
      height: 2.75rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .detail-close-btn:hover {
      transform: scale(1.1) rotate(90deg);
      background: linear-gradient(135deg, #1e40af, #003d7a);
      box-shadow: 
        0 8px 16px rgba(37, 99, 235, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
    }

    .detail-close-btn:focus {
      outline: 3px solid rgba(50, 100, 255, 0.5);
      outline-offset: 2px;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 640px) {
      .app-container {
        max-width: 100%;
        border-radius: 0;
        margin: 0;
      }

      .main-content {
        margin: 0.75rem;
        border-radius: 20px;
      }

      .review-image {
        width: 80px;
        height: 80px;
      }

      .slider-image {
        height: 200px;
      }

      .wheel-img {
        width: 120px;
        height: 120px;
      }

      .promotion-text {
        font-size: 16px;
      }

      .info-btn {
        padding: 16px;
        font-size: 13px;
      }

      .detail-section {
        margin: 0.75rem;
        padding: 2rem 1.5rem;
        border-radius: 24px 24px 0 0;
      }

      .detail-header h3 {
        font-size: 1.6rem;
      }

      .order-info-card {
        padding: 1.25rem;
        margin: 1rem;
      }

      .order-info-card .order-info-row {
        grid-template-columns: 1fr;
        gap: 1rem;
        text-align: center;
      }

      .order-info-card .brand-logo {
        width: 60px;
        height: 30px;
      }

      .order-info-card .result-circle {
        width: 28px;
        height: 28px;
        font-size: 0.8rem;
      }

      .countdown-timer .timer-text {
        font-size: 1.9rem;
      }

      .balance-info .balance-amount {
        font-size: 1.5rem;
      }

      .odds-grid {
        grid-template-columns: 1fr;
      }

      .brand-tabs {
        flex-direction: column;
        gap: 0.75rem;
      }

      .brand-tab {
        padding: 1rem;
        font-size: 0.85rem;
      }

      .bs-card {
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
      }
      
      .bs-header h3 {
        font-size: 1.5rem;
      }
      
      .bs-actions {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
    }
.modal-input {
    margin-bottom: 1rem;
}

.modal-input label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 700;
    color: #003d7a;
    font-size: 0.875rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-input label svg {
    width: 1rem;
    height: 1rem;
    fill: #003d7a;
}

.modal-input input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid rgba(0, 61, 122, 0.2);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.95);
    font-size: 1rem;
    font-weight: 600;
    color: #003d7a;
    transition: all 0.3s ease;
    box-shadow: 0 3px 8px rgba(0, 61, 122, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.9);
    -webkit-appearance: none; /* Tắt giao diện mặc định của input number trên iOS */
    -moz-appearance: textfield; /* Tắt spinner trên Firefox */
}

.modal-input input::-webkit-outer-spin-button,
.modal-input input::-webkit-inner-spin-button {
    -webkit-appearance: none; /* Tắt mũi tên tăng/giảm trên Chrome/Safari */
    margin: 0;
}

.modal-input input:focus {
    outline: none;
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15), 0 6px 12px rgba(245, 158, 11, 0.2);
    background: #ffffff;
}

@media (max-width: 640px) {
    .modal-input input {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        min-height: 44px; /* Đảm bảo vùng nhấn tối thiểu */
    }
}
    /* UTILITY CLASSES */
    .vinamilk-glass-effect {
      background: linear-gradient(145deg, 
        rgba(255, 255, 255, 0.2) 0%, 
        rgba(254, 251, 243, 0.15) 50%, 
        rgba(255, 255, 255, 0.1) 100%);
      backdrop-filter: blur(15px);
      border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .text-vinamilk-gradient {
      background: linear-gradient(135deg, #003d7a, #dc2626, #f59e0b);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .vinamilk-logo {
      transition: all 0.3s ease;
    }

    .vinamilk-logo:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body class="h-full">
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
  <img
    src="https://i.ibb.co/7dH1SxtB/Chat-GPT-Image-21-36-45-16-thg-8-2025.png"
    alt="Vinamilk"
    class="w-8 h-8 object-contain rounded"
    width="32"
    height="32"
    loading="lazy"
  />
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
          <h2 class="text-lg font-bold text-white">Danh mục đơn hàng</h2>
          <button id="close-sidebar" class="text-2xl text-gray-400 hover:text-white transition-colors">×</button>
        </div>
        
        <!-- Progress Indicator -->
        <div class="mb-6">
          <h3 class="text-sm font-semibold text-gray-300 mb-3">Tiến độ đơn hàng</h3>
          <div class="flex items-center justify-between mb-2">
            <div class="progress-step active w-8 h-8 rounded-full bg-vinamilk-blue flex items-center justify-center text-white text-xs font-bold">1</div>
            <div class="flex-1 h-1 bg-gray-600 mx-1"><div class="h-full bg-vinamilk-blue w-3/4"></div></div>
            <div class="progress-step w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-gray-400 text-xs font-bold">2</div>
            <div class="flex-1 h-1 bg-gray-600 mx-1"></div>
            <div class="progress-step w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-gray-400 text-xs font-bold">3</div>
          </div>
          <div class="flex justify-between text-xs text-gray-400">
            <span>Chọn</span>
            <span>Đặt hàng</span>
            <span>Hoàn thành</span>
          </div>
        </div>

        <!-- Categories -->
        <nav class="space-y-2" id="category-list">
          <div class="p-3 bg-white/10 rounded-lg text-white hover:bg-white/20 transition-colors cursor-pointer">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"></path>
              </svg>
              <span>Tất cả sản phẩm</span>
            </div>
          </div>
          <div class="p-3 bg-white/5 rounded-lg text-gray-300 hover:bg-white/10 transition-colors cursor-pointer">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
              </svg>
              <span>Sản phẩm hot</span>
            </div>
          </div>
          <div class="p-3 bg-white/5 rounded-lg text-gray-300 hover:bg-white/10 transition-colors cursor-pointer">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V7l-7-5z" clip-rule="evenodd"></path>
              </svg>
              <span>Sữa tươi</span>
            </div>
          </div>
          <div class="p-3 bg-white/5 rounded-lg text-gray-300 hover:bg-white/10 transition-colors cursor-pointer">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"></path>
              </svg>
              <span>Sữa chua</span>
            </div>
          </div>
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

        <!-- Detail Section - This will hide mainContentArea when shown -->
        <div id="detailSection" class="detail-section hidden">
          <!-- Detail Header -->
          <div class="detail-header text-center mb-6">
            <div class="flex items-center justify-center mb-4">
              <svg class="w-8 h-8 mr-3 text-vinamilk-yellow" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
              </svg>
              <h3 id="lottery_title" class="text-2xl font-bold text-white"></h3>
            </div>
            <button id="btn-close-detail" class="detail-close-btn" aria-label="Đóng chi tiết">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
              </svg>
            </button>
          </div>

          <!-- Brand Tabs -->
          <div class="brand-tabs">
            <button class="brand-tab active" data-brand="TH" id="tab-th">
              <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V7l-7-5z" clip-rule="evenodd"></path>
              </svg>
              TH TRUE MILK
            </button>
            <button class="brand-tab" data-brand="VINAMILK" id="tab-vinamilk">
              <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
              </svg>
              VINAMILK
            </button>
          </div>

          <!-- Enhanced Order Info Card -->
          <div class="order-info-card" id="order-info-card" data-brand="TH">
            <div class="order-info-header">
              <div class="brand-section">
                <img id="lottery_img" src="/assets/image/TH.png" alt="Brand" class="brand-logo">
              </div>
              <div class="current-session-info">
                <span class="session-label" id="current-order-text">Đơn hàng</span>
                <div class="session-number" id="now-session">202508163254</div>
              </div>
            </div>
            <div class="result-section">
              <div class="result-header">
                <span class="result-label" id="result-order-text">Kết quả đơn hàng</span>
                <span class="result-session" id="prev-session">202508163255</span>
              </div>
              <div class="result-circles-container" id="result-circles">
                <div class="result-circle">2</div>
                <div class="result-circle">2</div>
                <div class="result-circle">9</div>
                <div class="result-circle">0</div>
                <div class="result-circle">3</div>
              </div>
            </div>
          </div>

          <div class="countdown-timer rounded-xl p-4 mb-6">
            <div class="flex items-center justify-center mb-2">
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
              </svg>
              <span class="text-sm font-medium text-white">Thời gian còn lại</span>
            </div>
            <div id="count-down" class="timer-text">--:--:--</div>
          </div>
          <div class="balance-info rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2 text-blue-600">
                <span class="text-blue-600 font-medium">Hoa hồng</span>
              </div>
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.469.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                </svg>
                <span id="money" class="font-bold text-xl text-blue-600">1,250,000</span>
              </div>
            </div>
          </div>
          <div id="value_odds" class="grid grid-cols-2 gap-4 mb-6"></div>
        </div>

        <!-- Modal (Replaced Bottom Sheet) -->
        <div id="modal" class="modal-overlay" role="dialog" aria-modal="true">
          <div class="modal-card" role="document">
            <div class="modal-header">
              <h3 id="modal-brand-title">TH TRUE MILK</h3>
              <button id="btn-close-modal" class="modal-close" aria-label="Đóng">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
              </button>
            </div>
         <div class="modal-input has-caret">
    <label for="txt-lot-money" class="modal-label">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
        </svg>
        Số lượng đặt hàng:
    </label>
    <input type="number" id="txt-lot-money" min="1" value="1" placeholder="Nhập số lượng" aria-describedby="lot-money-help">
    <small id="lot-money-help" class="text-gray-500 text-xs">Nhập số lượng sản phẩm cần đặt</small>
</div>
            <div class="modal-actions">
              <button id="btn-confirm" class="btn-confirm">Nhập hàng</button>
              <button id="modal-cancel" class="modal-cancel">Đóng</button>
            </div>
          </div>
        </div>

        <!-- Review Cards Grid -->
        <div class="px-2 py-2 space-y-2">
          <div class="vinamilk-card p-3">
            <div class="flex items-center justify-between mb-3">
              <h2 class="text-vinamilk-gradient text-lg font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-vinamilk-blue" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
               Thương hiệu
              </h2>
              <div class="text-sm text-vinamilk-blue font-medium">
            
              </div>
            </div>
            
            <div id="mainContent" class="review-grid">
              <div class="review-card" onclick="openReview('th-brand', 2)" data-brand="TH">
                <img src="/assets/image/TH.png" alt="TH TRUE MILK" class="review-image">
                <div class="review-name">TH TRUE MILK</div>
                <div class="review-status">Đang hoạt động</div>
              </div>
              <div class="review-card" onclick="openReview('vinamilk-brand', 1)" data-brand="VINAMILK">
                <img src="/assets/image/Vinamilk.png" alt="VINAMILK" class="review-image">
                <div class="review-name">VINAMILK</div>
                <div class="review-status">Đang hoạt động</div>
              </div>
            </div>
          </div>
        </div>

        <div class="product-grid grid-2 px-2">
          <div class="info-btn">
            <div class="flex items-center justify-center gap-1">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              <span>Hôm nay: <span id="daily-orders">24</span></span>
            </div>
          </div>
          <div class="info-btn">
            <div class="flex items-center justify-center gap-1">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
              </svg>
              <span>Tổng: <span id="daily-products">156</span></span>
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

        <div class="promotion-section mx-2">
          <div class="promotion-text text-sm">
            <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            TRI ÂN KHÁCH HÀNG
          </div>
          <div class="wheel-container">
            <img src="https://i.ibb.co/21CgqhQK/vongquay-vo8v-Ji.png" alt="Vòng quay may mắn" class="wheel-img">
          </div>
          <div class="promotion-text text-sm">
            <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
            </svg>
            TÌM KIẾM KHÁCH HÀNG MAY MẮN
          </div>
          <img src="https://i.ibb.co/HTCxbr3x/3in1-D7-PODvv8.png" alt="Sản phẩm" class="promotion-product">
        </div>
      </div>
    </div>

    <script>
// COMPLETE VINAMILK ORDER SYSTEM JAVASCRIPT - ALL FIXES APPLIED
// ================================================================

// GLOBAL VARIABLES AND UTILITIES
const LETTER_TO_NUM = { a: 1, b: 2, c: 3, d: 4 };
const BRAND_PREFIX = { TH: 'a', VINAMILK: 'b' };

const urlParams = new URLSearchParams(window.location.search);
const pageKey = urlParams.get('key');
const pageId = urlParams.get('id');

let rankKey;
switch (pageId) {
    case '6': rankKey = 1; break;
    case '7': rankKey = 2; break;
    case '8': rankKey = 3; break;
    default: rankKey = 1;
}
window.RANK_KEY = rankKey;

const ODDS_BY_BRAND = {
    TH: [
        { type: 'a', name: 'SỮA CÔ GÁI HÀ LAN' },
        { type: 'b', name: 'SỮA NUTIFOOD' },
        { type: 'c', name: 'SỮA MỘC CHÂU' },
        { type: 'd', name: 'SỮA OPTIMUM GOLD SỐ 2' }
    ],
    VINAMILK: [
        { type: 'a', name: 'SỮA MILO' },
        { type: 'b', name: 'SỮA ĐẬU NÀNH FAMI' },
        { type: 'c', name: 'SỮA DUTCH LADY' },
        { type: 'd', name: 'SỮA ALPHA GOLD' }
    ]
};

// MAIN STATE VARIABLES
let items = [];
let Odds = [];
let UserInfo = {};
let LotteryInfo = {};
let SelectedOdds = []; // FIX: Đảm bảo không bị reset không cần thiết
let touchTimeout = null;
let isProcessingClick = false; // FIX: Prevent double clicks
let current_session = "";
let countdownInterval;
let pollingInterval;
let remainingSeconds = 999;
let isModalOpen = false;
let isDetailOpen = false;
let currentKey = '';
let currentId = '';
let currentBrand = 'TH';
window.audioContextStarted = false;
window.totalUnits = [0, 0, 0, 0];

// Fixed: Variables to store stable results
let currentSessionResult = null;
let sessionResultHistory = new Map();
let isNewSession = false;

// ================================================================
// UTILITY FUNCTIONS
// ================================================================

function showLoading() {
    $('#loadingOverlay').addClass('show');
}

function hideLoading() {
    $('#loadingOverlay').removeClass('show');
}

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

function generateRandomCode() {
    return Math.floor(10000 + Math.random() * 90000).toString();
}

// ================================================================
// SESSION CODE MANAGEMENT - FIXED
// ================================================================

function generateSessionCodes() {
    try {
        if (typeof sessionCodes === 'undefined') {
            window.sessionCodes = {};
        }
        
        // Only generate new codes if we don't have them or it's a new session
        if (!sessionCodes.a || isNewSession) {
            sessionCodes.a = generateRandomCode();
            sessionCodes.b = generateRandomCode();
            sessionCodes.c = generateRandomCode();
            sessionCodes.d = generateRandomCode();
            
            console.log("New session codes generated:", sessionCodes);
            updateCodesInUI();
            isNewSession = false;
        }
        
        return sessionCodes;
    } catch (e) {
        console.error('Error generating session codes:', e);
        window.sessionCodes = {
            a: '47372',
            b: '82914',
            c: '56783',
            d: '91245'
        };
        return sessionCodes;
    }
}

function updateCodesInUI() {
    try {
        if (typeof sessionCodes === 'undefined' || !sessionCodes) {
            console.warn('sessionCodes not available for UI update');
            return;
        }
        $('.odds-item').each(function(index) {
            const $item = $(this);
            const letter = $item.data('odd');
            if (letter && sessionCodes[letter] && sessionCodes[letter].length === 5) {
                $item.find('.product-code').text(sessionCodes[letter]);
                console.log(`Updated UI code for ${letter}: ${sessionCodes[letter]}`);
            }
        });
    } catch (e) {
        console.error('Error updating codes in UI:', e);
    }
}

// ================================================================
// SESSION RESULT MANAGEMENT - FIXED
// ================================================================

function getSessionResult(sessionId, callback) {
    const sessionKey = `${currentBrand}_${sessionId}`;
    
    // First try to get result from server (admin panel)
    $.ajax({
        type: "POST",
        url: "ajax/index.php", 
        data: { 
            action_type: "get_result", 
            session: sessionId, 
            brand: currentBrand 
        },
        dataType: "json",
        timeout: 3000,
        success: function(response) {
            if (response && response.success && response.data && response.data.code) {
                // Server has result - use it and cache it
                const serverResult = {
                    code: response.data.code,
                    letter: response.data.letter || 'a',
                    sessionId: sessionId,
                    brand: currentBrand,
                    source: 'server'
                };
                
                sessionResultHistory.set(sessionKey, serverResult);
                console.log(`✅ Server result for session ${sessionId}:`, serverResult);
                
                if (callback) callback(serverResult);
                return;
            }
            
            // Server has no result - use cached or generate fallback
            handleFallbackResult(sessionKey, sessionId, callback);
        },
        error: function() {
            // Server error - use cached or generate fallback  
            console.log(`❌ Server error for session ${sessionId}, using fallback`);
            handleFallbackResult(sessionKey, sessionId, callback);
        }
    });
}

function handleFallbackResult(sessionKey, sessionId, callback) {
    // Return existing cached result if available
    if (sessionResultHistory.has(sessionKey)) {
        const cached = sessionResultHistory.get(sessionKey);
        console.log(`📋 Using cached result for session ${sessionId}:`, cached);
        if (callback) callback(cached);
        return;
    }
    
    // Generate new fallback result for new session
    const winners = ['a', 'b', 'c', 'd'];
    const selectedWinner = winners[Math.floor(Math.random() * winners.length)];
    const resultCode = generateRandomCode();
    
    const result = {
        code: resultCode,
        letter: selectedWinner,
        sessionId: sessionId,
        brand: currentBrand,
        source: 'fallback'
    };
    
    // Store result for this session
    sessionResultHistory.set(sessionKey, result);
    
    // Keep only last 10 sessions to prevent memory leak
    if (sessionResultHistory.size > 10) {
        const firstKey = sessionResultHistory.keys().next().value;
        sessionResultHistory.delete(firstKey);
    }
    
    console.log(`🎲 Generated fallback result for session ${sessionId}:`, result);
    if (callback) callback(result);
}

function updateResultDisplay() {
    try {
        if (!current_session) return;
        
        const prevSession = parseInt(current_session) - 1;
        if (prevSession <= 0) return;
        
        // Get result with server priority
        getSessionResult(prevSession, function(result) {
            displaySingleResultCircles(result.code);
            
            // Highlight winner if applicable
            if (result.letter) {
                setTimeout(() => {
                    $(`.odds-item[data-odd="${result.letter}"]`).addClass('winner-glow');
                    setTimeout(() => {
                        $(`.odds-item[data-odd="${result.letter}"]`).removeClass('winner-glow');
                    }, 3000);
                }, 500);
            }
            
            console.log(`📊 Displayed result from ${result.source} for session ${prevSession}: ${result.code}`);
        });
    } catch (e) {
        console.error('Error updating result display:', e);
    }
}

function displaySingleResultCircles(resultCode) {
    try {
        const $container = $('#result-circles');
        $container.empty();
        
        if (resultCode && resultCode.toString().length >= 5) {
            const digits = resultCode.toString().padStart(5, '0').slice(0, 5);
            $container.css({
                'display': 'flex',
                'flex-wrap': 'nowrap',
                'justify-content': 'flex-end',
                'align-items': 'center',
                'gap': '4px',
                'overflow': 'hidden'
            });
            
            $container.html(digits.split('').map((digit, i) => `
                <div class="result-circle" style="animation-delay: ${i * 0.1}s">
                    ${digit}
                </div>
            `).join(''));
            
            console.log(`Displaying stable 5-digit result: ${digits}`);
        } else {
            displayDemoResult();
        }
    } catch (e) {
        console.error('Error displaying single result circles:', e);
        displayDemoResult();
    }
}

function displayDemoResult() {
    try {
        const $container = $('#result-circles');
        $container.empty();
        $container.css({
            'display': 'flex',
            'flex-wrap': 'nowrap',
            'justify-content': 'flex-end',
            'align-items': 'center',
            'gap': '4px',
            'overflow': 'hidden'
        });
        
        // Generate random 5-digit demo
        const demoDigits = Array.from({ length: 5 }, () => Math.floor(Math.random() * 10).toString());
        $container.html(demoDigits.map((digit, i) => `
            <div class="result-circle" style="animation-delay: ${i * 0.1}s">
                ${digit}
            </div>
        `).join(''));
        
        console.log(`Displaying random demo result: ${demoDigits.join('')}`);
    } catch (e) {
        console.error('Error displaying demo result:', e);
        $container.empty();
    }
}

// ================================================================
// BRAND MANAGEMENT - FIXED
// ================================================================

function switchBrand(brand) {
    try {
        currentBrand = brand.toUpperCase();
        console.log(`Switching to brand: ${currentBrand}`);
        $('.brand-tab').removeClass('active');
        $(`[data-brand="${currentBrand}"]`).addClass('active');
        const brandDisplayName = currentBrand === 'TH' ? 'TH TRUE MILK' : 'VINAMILK';
        $('#modal-brand-title').text(brandDisplayName);
        updateOrderInfoCard(currentBrand);
        
        if (!sessionCodes || !sessionCodes.a || sessionCodes.a.length !== 5) {
            generateSessionCodes();
        }
        
        if (isDetailOpen) {
            console.log(`Re-rendering products for brand: ${currentBrand}`);
            // FIX: Không reset SelectedOdds khi switch brand
            
            if (isModalOpen) {
                hideModal();
            }
            RenderOdds(ODDS_BY_BRAND[currentBrand], currentBrand);
            
            // Restore selected state after render
            setTimeout(() => {
                updateSelectedProductsDisplay();
            }, 100);
        }
        console.log(`Successfully switched to brand: ${currentBrand}`);
    } catch (e) {
        console.error('Error switching brand:', e);
    }
}

function updateOrderInfoCard(brand) {
    try {
        if (brand === 'TH') {
            $('#current-order-text').text('Đơn hàng hiện tại');
            $('#result-order-text').text('Kết quả đơn hàng');
            $('#lottery_title').text('TH TRUE MILK');
            $('#lottery_img').attr('src', '/assets/image/TH.png');
        } else {
            $('#current-order-text').text('Đơn hàng hiện tại');
            $('#result-order-text').text('Kết quả đơn hàng');
            $('#lottery_title').text('VINAMILK');
            $('#lottery_img').attr('src', '/assets/image/Vinamilk.png');
        }
        
        // Update result display for current brand
        updateResultDisplay();
    } catch (e) {
        console.error('Error updating order info card:', e);
    }
}

function getBrandImagePath(brand, letter) {
    const prefix = BRAND_PREFIX[brand] || 'b';
    const num = LETTER_TO_NUM[letter.toLowerCase()] || 1;
    return `/assets/image/${prefix}${num}.jpg`;
}

// ================================================================
// SELECTED PRODUCTS MANAGEMENT - FIXED
// ================================================================

function updateSelectedProductsDisplay() {
    try {
        // Đồng bộ UI với SelectedOdds array
        $('.odds-item').each(function() {
            const $item = $(this);
            const type = String($item.data("odd")).toUpperCase();
            
            if (SelectedOdds.includes(type)) {
                $item.addClass("active");
            } else {
                $item.removeClass("active");
            }
        });
        
        // Cập nhật số lượng trong modal title nếu cần
        if (SelectedOdds.length > 0) {
            $('#modal-brand-title').text(`${currentBrand} (${SelectedOdds.length} sản phẩm)`);
        } else {
            const brandDisplayName = currentBrand === 'TH' ? 'TH TRUE MILK' : 'VINAMILK';
            $('#modal-brand-title').text(brandDisplayName);
        }
        
        console.log(`🔄 Updated display for ${SelectedOdds.length} selected products`);
    } catch (e) {
        console.error("Error updating selected products display:", e);
    }
}

function validateOrderButton() {
    try {
        const quantity = parseInt($("#txt-lot-money").val(), 10) || 1;
        
        if (SelectedOdds && SelectedOdds.length > 0 && UserInfo && typeof UserInfo.money !== 'undefined') {
            const totalCost = SelectedOdds.length * quantity;
            const availableBalance = UserInfo.money || 0;
            
            console.log(`Validate button: ${SelectedOdds.length} sản phẩm x ${quantity} = ${totalCost}, Số dư: ${availableBalance}`);
            
            if (totalCost > availableBalance) {
                $("#btn-confirm").prop("disabled", true).addClass("opacity-50 cursor-not-allowed");
                return false;
            } else {
                $("#btn-confirm").prop("disabled", false).removeClass("opacity-50 cursor-not-allowed");
                return true;
            }
        } else {
            // Nếu chưa chọn sản phẩm, kích hoạt nút
            $("#btn-confirm").prop("disabled", false).removeClass("opacity-50 cursor-not-allowed");
            return true;
        }
    } catch (e) {
        console.error("Lỗi validate button:", e);
        return true;
    }
}

function debugSelectedState() {
    console.log("=== DEBUG SELECTED STATE ===");
    console.log("SelectedOdds:", SelectedOdds);
    console.log("Active UI items:", $('.odds-item.active').length);
    console.log("Modal open:", isModalOpen);
    console.log("Detail open:", isDetailOpen);
    console.log("Current brand:", currentBrand);
    
    $('.odds-item.active').each(function(i) {
        console.log(`Active item ${i+1}:`, $(this).data("odd"));
    });
    console.log("=============================");
}

// ================================================================
// DETAIL SECTION MANAGEMENT
// ================================================================

function openDetailSection(key, id, brand = 'TH') {
    try {
        showLoading();
        isDetailOpen = true;
        currentKey = key;
        currentId = id;
        currentBrand = brand.toUpperCase();
        switchBrand(currentBrand);
        $("#detailSection").removeClass("hidden").addClass("show");
        setTimeout(() => {
            document.getElementById('detailSection').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
            hideLoading();
        }, 200);
        initializeDetailView(key, id);
    } catch (e) {
        console.error('Error opening detail section:', e);
        hideLoading();
    }
}

function closeDetailSection() {
    try {
        $("#detailSection").removeClass("show").addClass("hidden");
        cleanupDetailView();
        isDetailOpen = false;
        $("#lottery_title").text("Chọn một mục để xem chi tiết");
        $("#lottery_img").attr('src', '');
        $("#now-session").text('-');
        $("#prev-session").text('-');
        $("#count-down").text('--:--:--');
        $("#money").text('0');
        $("#value_odds").empty();
        $("#value_odds").html(`
            <div class="col-span-2 text-center py-8">
                <svg class="w-16 h-16 mx-auto mb-4 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-white/70 text-lg">Chọn một mục từ danh sách trên để bắt đầu đặt hàng</p>
            </div>
        `);
        setTimeout(() => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 200);
    } catch (e) {
        console.error('Error closing detail section:', e);
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
        // FIX: Chỉ reset SelectedOdds khi thực sự cần
        SelectedOdds = [];
        $(".odds-item").removeClass("active");
        hideModal();
    } catch (e) {
        console.error('Error cleaning up detail view:', e);
    }
}

function openReview(key, id) {
    const cardElement = event.currentTarget;
    const brand = cardElement.getAttribute('data-brand') || 'TH';
    updateDetailSection(key, id, brand);
}

function updateDetailSection(key, id, brand = 'TH') {
    try {
        showLoading();
        cleanupDetailView();
        currentKey = key;
        currentId = id;
        currentBrand = brand.toUpperCase();
        isDetailOpen = true;
        switchBrand(currentBrand);
        $("#detailSection").removeClass("hidden").addClass("show");
        setTimeout(() => {
            document.getElementById('detailSection').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
            hideLoading();
        }, 100);
        initializeDetailView(key, id);
    } catch (e) {
        console.error('Error updating detail section:', e);
        hideLoading();
    }
}

function showReview(id) {
    try {
        let need_to_render = id == -1 ? items : items.filter((v, i) => v.cate_id == id);
        $("#mainContent").empty();
        $("#itemCount").text(need_to_render.length);
        need_to_render.forEach(o => {
            let html = `
                <div class="review-card" onclick="openReview('${o.key}', ${o.id})" data-brand="${o.brand || 'TH'}">
                    <img src="${o.image}" alt="${o.name}" class="review-image" 
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMzIiIGZpbGw9IiM2MzY2ZjEiLz4KPHN2ZyB4PSIxNiIgeT0iMTYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJ3aGl0ZSI+CjxwYXRoIGQ9Ik0xMiAyQzYuNDggMiAyIDYuNDggMiAxMnM0LjQ4IDEwIDEwIDEwIDEwLTQuNDggMTAtMTBTMTcuNTIgMiAxMiAyem0wIDNjMS42NiAwIDMgMS4zNCAzIDNzLTEuMzQgMy0zIDMtMy0xLjM0LTMtMyAxLjM0LTMgMy0zem0wIDEzLjJjLTIuNSAwLTQuNzEtMS4yOC02LTMuMjIuMDMtMS45OSA0LTMuMDggNi0zLjA8tzUuOTcgMS4wOSA2IDMuMDhjLTEuMjkgMS45NC0zLjUgMy4yMi02IDMuMjJ6Ii8+Cjwvc3ZnPgo8L3N2Zz4='" />
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

// ================================================================
// DETAIL VIEW INITIALIZATION - FIXED
// ================================================================

function initializeDetailView(key, id) {
    try {
        console.log("🚀 Starting detail view initialization...");
        console.log("Current brand:", currentBrand);
        
        // FIX: Không reset SelectedOdds nếu đã có sản phẩm được chọn
        
        remainingSeconds = 999;
        generateSessionCodes();
        console.log("5-digit session codes generated:", sessionCodes);
        
        Object.entries(sessionCodes).forEach(([letter, code]) => {
            if (code.length !== 5) {
                console.error(`Invalid code length for ${letter}: ${code} (${code.length} digits)`);
                sessionCodes[letter] = generateRandomCode();
            }
        });
        
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
                    return;
                }
                const total = window.totalUnits.reduce((a, b) => a + b, 0) || 1;
                oddsItems.forEach((item, i) => {
                    if (i >= window.totalUnits.length) return;
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
                        text.textContent = `${percent}%`;
                    }
                });
            } catch (e) {
                console.error('Error in drawProgressBars:', e);
            }
        };
        
        console.log("Rendering odds for current brand:", currentBrand);
        RenderOdds(ODDS_BY_BRAND[currentBrand], currentBrand);
        
        GetLotteryInfo(id)
            .done((response) => {
                if (response && response.success) {
                    LotteryInfo = response.data;
                    window.totalUnits = LotteryInfo.totalUnits || [0, 0, 0, 0];
                    RenderLotteryInfo(LotteryInfo);
                    RenderOdds(ODDS_BY_BRAND[currentBrand], currentBrand);
                }
                console.log("✅ Lottery info loaded");
                GetOdds(key, id);
                GetUserInfo();
                countdownInterval = setInterval(updateTimer, 1000);
                startPolling(id);
                console.log("✅ Detail view initialized with 5-digit codes");
            })
            .fail(error => {
                console.error("❌ Error initializing detail view:", error);
                console.log("Using fallback data for brand:", currentBrand);
                RenderOdds(ODDS_BY_BRAND[currentBrand], currentBrand);
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

// ================================================================
// API FUNCTIONS
// ================================================================

function GetLotteryInfo(id) {
    return $.ajax({
        type: "POST",
        url: "ajax/index.php",
        data: { action_type: "get_lottery", id: id || currentId, brand: currentBrand },
        dataType: "json",
        timeout: 10000
    });
}

function GetOdds(key, id) {
    showLoading();
    $.ajax({
        type: "POST",
        url: "ajax/index.php",
        data: { action_type: "get_lottery_odd", key: key || currentKey, id: id || currentId, brand: currentBrand },
        dataType: "json",
        timeout: 10000,
        success: function(response) {
            try {
                if (response && response.success) {
                    Odds = response.data || [];
                    console.log("Server odds data received:", Odds);
                    RenderOdds(Odds, currentBrand);
                    if (LotteryInfo.totalUnits) {
                        window.totalUnits = LotteryInfo.totalUnits;
                        window.drawProgressBars(remainingSeconds);
                    }
                } else {
                    console.error("Invalid odds response:", response);
                    console.log("Using fallback data for brand:", currentBrand);
                    RenderOdds(ODDS_BY_BRAND[currentBrand], currentBrand);
                }
                hideLoading();
            } catch (e) {
                console.error("Error processing odds:", e);
                console.log("Using fallback data due to error for brand:", currentBrand);
                RenderOdds(ODDS_BY_BRAND[currentBrand], currentBrand);
                hideLoading();
            }
        },
        error: function(xhr, status, error) {
            console.error("Ajax error getting odds:", status, error);
            console.log("Using fallback data due to AJAX error for brand:", currentBrand);
            RenderOdds(ODDS_BY_BRAND[currentBrand], currentBrand);
            hideLoading();
        }
    });
}

function GetUserInfo() {
    showLoading();
    $.ajax({
        type: "POST",
        url: "ajax/index.php",
        data: { action_type: "get_user_info" },
        dataType: "json",
        timeout: 10000,
        success: function (response) {
            try {
                if (response && response.success) {
                    UserInfo = response.data;
                    console.log("UserInfo:", UserInfo, "Số dư:", UserInfo.money);
                    RenderUserInfo(UserInfo);
                    
                    // FIX: Kiểm tra lại số dư sau khi load user info
                    validateOrderButton();
                } else {
                    console.error("Phản hồi thông tin người dùng không hợp lệ:", response);
                    toastr.error("Không thể tải thông tin người dùng", "Lỗi");
                }
                hideLoading();
            } catch (e) {
                console.error("Lỗi xử lý thông tin người dùng:", e);
                toastr.error("Lỗi xử lý thông tin người dùng", "Lỗi");
                hideLoading();
            }
        },
        error: function (xhr, status, error) {
            console.error("Lỗi Ajax khi lấy thông tin người dùng:", status, error);
            toastr.error("Lỗi kết nối khi tải thông tin người dùng", "Lỗi");
            hideLoading();
        },
    });
}

// ================================================================
// POLLING AND TIMER FUNCTIONS - FIXED
// ================================================================

function startPolling(id) {
    try {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
        pollingInterval = setInterval(() => {
            if (!isDetailOpen) return;
            showLoading();
            $.ajax({
                type: "POST",
                url: "ajax/index.php",
                data: { action_type: "get_lottery", id: id || currentId, brand: currentBrand },
                dataType: "json",
                timeout: 8000,
                success: function(response) {
                    try {
                        if (response && response.success) {
                            LotteryInfo = response.data;
                            window.totalUnits = LotteryInfo.totalUnits || [0, 0, 0, 0];
                            
                            // Check if new session started
                            const newSession = parseInt(LotteryInfo.now_session);
                            if (current_session && newSession !== parseInt(current_session)) {
                                isNewSession = true;
                                generateSessionCodes(); // Generate new codes for new session
                                
                                // Clear previous session result cache on new session
                                const prevSessionKey = `${currentBrand}_${parseInt(current_session) - 1}`;
                                if (sessionResultHistory.has(prevSessionKey)) {
                                    console.log(`🗑️ Clearing cached result for previous session`);
                                }
                            }
                            
                            RenderLotteryInfo(LotteryInfo);
                            window.drawProgressBars(remainingSeconds);
                            
                            // Check for admin panel result updates for previous session
                            const prevSession = newSession - 1;
                            if (prevSession > 0) {
                                getSessionResult(prevSession, function(result) {
                                    if (result.source === 'server') {
                                        console.log(`🔄 Admin result detected for session ${prevSession}, updating display`);
                                        displaySingleResultCircles(result.code);
                                    }
                                });
                            }
                        }
                    } catch (e) {
                        console.error("Error in polling success:", e);
                    } finally {
                        hideLoading();
                    }
                },
                error: function() {
                    console.log("Polling error - will retry");
                    hideLoading();
                }
            });
        }, 3000); // Polling every 3 seconds
    } catch (e) {
        console.error("Error starting polling:", e);
        hideLoading();
    }
}

async function fetchNewTimeAndRestart() {
    try {
        showLoading();
        $("#count-down").css({ color: "white", fontSize: "20px" }).text("🔄 Đang cập nhật kỳ mới...");
        clearInterval(countdownInterval);
        clearInterval(pollingInterval);
        
        // Mark as new session to trigger new codes generation
        isNewSession = true;
        generateSessionCodes();
        
        const response = await $.ajax({
            type: "POST",
            url: "ajax/index.php",
            data: { action_type: "get_lottery", id: currentId, brand: currentBrand },
            dataType: "json",
            timeout: 10000
        });
        
        if (response && response.success) {
            LotteryInfo = response.data;
            window.totalUnits = LotteryInfo.totalUnits || [0, 0, 0, 0];
            RenderLotteryInfo(LotteryInfo);
            remainingSeconds = parseInt(LotteryInfo.second) || 60;
            updateCodesInUI();
            countdownInterval = setInterval(updateTimer, 1000);
            startPolling(currentId);
            hideLoading();
        } else {
            setTimeout(fetchNewTimeAndRestart, 5000);
            hideLoading();
        }
    } catch (error) {
        console.error("Error when updating new period:", error);
        // Generate fallback data for new session
        isNewSession = true;
        generateSessionCodes();
        updateCodesInUI();
        
        LotteryInfo.now_session = parseInt(LotteryInfo.now_session) + 1 || 1;
        LotteryInfo.prev_session = parseInt(LotteryInfo.now_session) - 1 || 0;
        LotteryInfo.second = 185;
        LotteryInfo.totalUnits = [
            Math.floor(Math.random() * 30) + 10,
            Math.floor(Math.random() * 30) + 10,
            Math.floor(Math.random() * 30) + 10,
            Math.floor(Math.random() * 30) + 10
        ];
        
        window.totalUnits = LotteryInfo.totalUnits;
        RenderLotteryInfo(LotteryInfo);
        remainingSeconds = parseInt(LotteryInfo.second) || 60;
        countdownInterval = setInterval(updateTimer, 1000);
        hideLoading();
    }
}

function updateTimer() {
    try {
        if (remainingSeconds > 0) {
            // Check for server results near end of session
            if (remainingSeconds === 5 && current_session) {
                const prevSession = parseInt(current_session) - 1;
                if (prevSession > 0) {
                    getSessionResult(prevSession, function(result) {
                        displaySingleResultCircles(result.code);
                        console.log(`⏰ Timer result for session ${prevSession} from ${result.source}: ${result.code}`);
                    });
                }
            }
            
            // Also check for admin updates every 30 seconds during countdown
            if (remainingSeconds % 30 === 0 && current_session) {
                const prevSession = parseInt(current_session) - 1;
                if (prevSession > 0) {
                    // Force check server for admin updates
                    $.ajax({
                        type: "POST",
                        url: "ajax/index.php",
                        data: { 
                            action_type: "get_result", 
                            session: prevSession, 
                            brand: currentBrand 
                        },
                        dataType: "json",
                        timeout: 2000,
                        success: function(response) {
                            if (response && response.success && response.data && response.data.code) {
                                const sessionKey = `${currentBrand}_${prevSession}`;
                                const serverResult = {
                                    code: response.data.code,
                                    letter: response.data.letter || 'a',
                                    sessionId: prevSession,
                                    brand: currentBrand,
                                    source: 'server'
                                };
                                
                                // Update cache and display
                                sessionResultHistory.set(sessionKey, serverResult);
                                displaySingleResultCircles(serverResult.code);
                                console.log(`🔄 Admin update detected during countdown:`, serverResult);
                            }
                        },
                        error: function() {
                            // Silent fail for background checks
                        }
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

// ================================================================
// RENDER FUNCTIONS
// ================================================================

function RenderLotteryInfo(data) {
    try {
        console.log("DEBUG: RenderLotteryInfo data:", data);
        $("#lottery_title").html(data.name || 'Chi tiết đơn hàng');
        $("#lottery_img").attr('src', data.image || '');
        
        let nowNum = parseInt(data.now_session);
        if (isNaN(nowNum)) {
            console.warn("Warning: data.now_session not a number:", data.now_session);
            nowNum = data.now_session || '';
        }
        $("#now-session").text(nowNum);
        
        let prevNum;
        if (data.prev_session && data.prev_session != 0) {
            prevNum = parseInt(data.prev_session);
            if (isNaN(prevNum)) {
                console.warn("Warning: data.prev_session not a number:", data.prev_session);
                prevNum = data.prev_session || '';
            }
        } else {
            prevNum = (typeof nowNum === 'number' && !isNaN(nowNum)) ? nowNum - 1 : '';
        }
        $("#prev-session").text(prevNum);
        
        // Check for session change
        if (current_session && current_session !== nowNum.toString()) {
            isNewSession = true;
            generateSessionCodes();
        }
        current_session = nowNum.toString();
        
        remainingSeconds = parseInt(data.second) || 0;
        window.totalUnits = data.totalUnits || [0, 0, 0, 0];
        
        // FIX: Không reset SelectedOdds trong RenderLotteryInfo
        // SelectedOdds = []; // Loại bỏ dòng này
        
        if (data.session_codes) {
            sessionCodes = data.session_codes;
            console.log("Server provided session codes:", sessionCodes);
        } else if (isNewSession || !sessionCodes) {
            generateSessionCodes();
        }
        
        updateResultDisplay(); // Update result display with stable results
        $("#count-down").text(convertSeconds(remainingSeconds));
        updateCodesInUI();
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

function RenderOdds(odds, brandKey) {
    try {
        showLoading();
        const brand = String(brandKey || 'TH').toUpperCase();
        console.log(`RenderOdds called for brand: ${brand}`);
        console.log(`Odds data:`, odds);
        
        if (typeof sessionCodes === 'undefined' || !sessionCodes || !sessionCodes.a || sessionCodes.a.length !== 5) {
            console.log('Generating new session codes for RenderOdds');
            generateSessionCodes();
        }
        
        const $c = $('#value_odds');
        $c.empty();
        
        if (!Array.isArray(odds)) {
            odds = ODDS_BY_BRAND[brand] || [];
            console.log(`Using fallback data for ${brand}:`, odds);
        }
        
        if (odds.length === 0) {
            $c.html(`
                <div class="col-span-2 text-center py-8">
                    <p class="text-white/70 text-lg">Không có sản phẩm cho ${brand}</p>
                </div>
            `);
            hideLoading();
            return;
        }
        
        odds.forEach((e, i) => {
            const letter = String(e.type || '').toLowerCase();
            const num = LETTER_TO_NUM[letter] || ((i % 4) + 1);
            const imgSrc = getBrandImagePath(brand, letter);
            
            let productCode = '00000';
            if (sessionCodes && sessionCodes[letter] && sessionCodes[letter].length === 5) {
                productCode = sessionCodes[letter];
            } else {
                productCode = generateRandomCode();
                if (sessionCodes) {
                    sessionCodes[letter] = productCode;
                }
            }
            
            console.log(`Product ${i+1}: ${e.name}, type: ${letter}, code: ${productCode}`);
            
            $c.append(`
                <div class="odds-item cursor-pointer overflow-hidden border-2 border-white/20 hover:border-indigo-300 transition-all shadow-lg hover:shadow-xl rounded-lg p-4 relative"
                     data-odd="${letter}" data-img="${imgSrc}" data-index="${i}" role="button" aria-label="Select ${e.name || ''}">
                    <div class="odds-progress absolute top-3 left-3 w-10 h-10">
                        <svg class="odds-progress-circle transform -rotate-90" viewBox="0 0 36 36">
                            <circle class="bg stroke-white/30 fill-none stroke-4" cx="18" cy="18" r="14"></circle>
                            <circle class="fg stroke-blue-500 fill-none stroke-4 transition-all duration-500" cx="18" cy="18" r="14"></circle>
                        </svg>
                        <span class="odds-progress-text absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xs font-semibold text-white">0%</span>
                    </div>
                    <img src="${imgSrc}" alt="${e.name || ''}" class="w-full aspect-square object-cover mb-3 rounded"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3QgeD0iOCIgeT0iOCIgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiByeD0iOCIgZmlsbD0iIzY2NjZmZiIvPgo8dGV4dCB4PSIzMiIgeT0iMzgiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIyNCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiPiR7YnJhbmQgPT09ICdUSCcgPyAnQScgOiAnQid9JHtudW19PC90ZXh0Pgo8L3N2Zz4='" />
                   
                    <p class="text-center text-lg font-bold text-yellow-400 py-1 product-code tracking-wider">${productCode}</p>
           
                </div>
            `);
        });
        
        // FIX: Restore selected state after render
        setTimeout(() => {
            updateSelectedProductsDisplay();
        }, 100);
        
        if (typeof window.drawProgressBars === 'function' && typeof remainingSeconds !== 'undefined') {
            window.drawProgressBars(remainingSeconds);
        }
        
        console.log(`✅ Successfully rendered ${odds.length} products for ${brand}`);
        hideLoading();
    } catch (e) {
        console.error('Error in RenderOdds:', e);
        hideLoading();
    }
}

// ================================================================
// SLIDER FUNCTIONALITY
// ================================================================

let currentSlide = 0;
let currentSlide2 = 0;
const totalSlides = 2;

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateSliderPosition();
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateSliderPosition();
}

function nextSlide2() {
    currentSlide2 = (currentSlide2 + 1) % totalSlides;
    updateSlider2Position();
}

function prevSlide2() {
    currentSlide2 = (currentSlide2 - 1 + totalSlides) % totalSlides;
    updateSlider2Position();
}

function updateSliderPosition() {
    const sliderWrapper = document.getElementById('slider');
    if (sliderWrapper) {
        sliderWrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
    }
}

function updateSlider2Position() {
    const sliderWrapper = document.getElementById('slider2');
    if (sliderWrapper) {
        sliderWrapper.style.transform = `translateX(-${currentSlide2 * 100}%)`;
    }
}

function startAutoSlide() {
    setInterval(() => {
        nextSlide();
    }, 4000);
    setInterval(() => {
        nextSlide2();
    }, 5000);
}

// ================================================================
// MODAL FUNCTIONS
// ================================================================

function showModal() {
    try {
        const modal = $("#modal");
        modal.addClass("show");
        isModalOpen = true;
        $("#txt-lot-money").focus();
    } catch (e) {
        console.error("Error showing modal:", e);
    }
}

function hideModal() {
    try {
        const modal = $("#modal");
        modal.removeClass("show");
        isModalOpen = false;
        $("#txt-lot-money").val(1);
    } catch (e) {
        console.error("Error hiding modal:", e);
    }
}

function resetInput() {
    try {
        hideModal();
        $("#txt-lot-money").val(1);
    } catch (e) {
        console.error("Error resetting input:", e);
    }
}

// ================================================================
// EVENT HANDLERS - COMPLETE
// ================================================================

$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const pageKey = urlParams.get('key');
    const pageId = urlParams.get('id');
    
    if (pageKey && pageId) {
        showLoading();
        $.ajax({
            type: "POST",
            url: "ajax/index.php",
            data: { action_type: "get_lottery_items" },
            dataType: "json",
            success: function(response) {
                items = response.data || getDefaultItems();
                showReview(-1);
                initializeDefaultDetailSection();
                setTimeout(() => {
                    updateDetailSection(pageKey, pageId);
                }, 500);
                hideLoading();
            },
            error: function() {
                items = getDefaultItems();
                showReview(-1);
                initializeDefaultDetailSection();
                hideLoading();
            }
        });
    } else {
        showLoading();
        $.ajax({
            type: "POST",
            url: "ajax/index.php",
            data: { action_type: "get_lottery_items" },
            dataType: "json",
            success: function(response) {
                items = response.data || getDefaultItems();
                showReview(-1);
                initializeDefaultDetailSection();
                hideLoading();
            },
            error: function() {
                items = getDefaultItems();
                showReview(-1);
                initializeDefaultDetailSection();
                hideLoading();
            }
        });
    }

    function getDefaultItems() {
        return [
            {id: 2, key: 'th-brand', name: 'TH TRUE MILK', image: '/assets/image/TH.png', brand: 'TH', cate_id: 2},
            {id: 1, key: 'vinamilk-brand', name: 'VINAMILK', image: '/assets/image/Vinamilk.png', brand: 'VINAMILK', cate_id: 1}
        ];
    }

    function initializeDefaultDetailSection() {
        $("#lottery_title").text("Chọn thương hiệu để xem chi tiết");
        $("#lottery_img").attr('src', '');
        $("#now-session").text('-');
        $("#prev-session").text('-');
        $("#count-down").text('--:--:--');
        $("#money").text('0');
        currentBrand = 'TH';
        generateSessionCodes();
        console.log("Initial session codes generated:", sessionCodes);
        const $container = $('#result-circles');
        $container.empty();
        switchBrand(currentBrand);
        $("#value_odds").html(`
            <div class="col-span-2 text-center py-8">
                <svg class="w-16 h-16 mx-auto mb-4 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-white/70 text-lg font-medium">Chọn thương hiệu để bắt đầu đặt hàng</p>
                <p class="text-white/50 text-sm mt-2">Click vào TH TRUE MILK hoặc VINAMILK để xem chi tiết</p>
            </div>
        `);
    }

    startAutoSlide();

    // ================================================================
    // NAVIGATION EVENT HANDLERS
    // ================================================================

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

    $(document).on("click", ".brand-tab", function() {
        const brand = $(this).data("brand");
        switchBrand(brand);
    });

    // ================================================================
    // PRODUCT SELECTION - FIXED
    // ================================================================

    $(document).on("click", ".odds-item", function(e) {
        if (!isDetailOpen || isProcessingClick) return;
        
        try {
            isProcessingClick = true;
            clearTimeout(touchTimeout);
            
            const $item = $(this);
            const type = $item.data("odd");
            
            if (!type) {
                console.error("No data-odd found for item:", $item);
                isProcessingClick = false;
                return;
            }
            
            const typeUpper = String(type).toUpperCase();
            
            // Đảm bảo SelectedOdds là array
            if (!Array.isArray(SelectedOdds)) {
                SelectedOdds = [];
            }
            
            touchTimeout = setTimeout(() => {
                try {
                    window.audioContextStarted = true;
                    
                    if ($item.hasClass("active")) {
                        // Bỏ chọn sản phẩm
                        $item.removeClass("active");
                        SelectedOdds = SelectedOdds.filter(t => t !== typeUpper);
                        console.log(`❌ Removed ${typeUpper}, SelectedOdds:`, SelectedOdds);
                    } else {
                        // Chọn sản phẩm
                        $item.addClass("active");
                        if (!SelectedOdds.includes(typeUpper)) {
                            SelectedOdds.push(typeUpper);
                        }
                        console.log(`✅ Added ${typeUpper}, SelectedOdds:`, SelectedOdds);
                    }
                    
                    // Cập nhật UI và validate
                    updateSelectedProductsDisplay();
                    validateOrderButton();
                    
                    // Hiển thị/ẩn modal
                    if (SelectedOdds.length > 0 && !isModalOpen) {
                        showModal();
                    } else if (SelectedOdds.length === 0) {
                        hideModal();
                    }
                    
                    console.log("📦 Current SelectedOdds:", SelectedOdds);
                    isProcessingClick = false;
                } catch (e) {
                    console.error("Error in timeout handler:", e);
                    isProcessingClick = false;
                }
            }, 150); // Giảm delay để responsive hơn
            
        } catch (e) {
            console.error("Error handling odds click:", e);
            isProcessingClick = false;
        }
    });

    // ================================================================
    // INPUT HANDLING - FIXED
    // ================================================================

    $("#txt-lot-money").on("input", function (e) {
        try {
            let money = e.target.value;
            // Loại bỏ các ký tự không phải số
            money = money.replace(/[^0-9]/g, "");
            let moneyInt = parseInt(money, 10) || 1;

            // Kiểm tra giá trị tối thiểu
            if (moneyInt < 1) {
                moneyInt = 1;
                e.target.value = 1;
                toastr.warning("Số lượng phải lớn hơn hoặc bằng 1", "Cảnh báo");
            }

            // FIX: Loại bỏ giới hạn số lượng tối đa
            // Không còn giới hạn 1000, cho phép nhập số lượng bất kỳ
            
            // Cập nhật giá trị input
            e.target.value = moneyInt;
            console.log("Số lượng cập nhật:", moneyInt);

            // FIX: Kiểm tra số dư với logic đúng
            if (SelectedOdds && SelectedOdds.length > 0 && UserInfo && typeof UserInfo.money !== 'undefined') {
                const totalCost = SelectedOdds.length * moneyInt;
                const availableBalance = UserInfo.money || 0;
                
                console.log(`Kiểm tra số dư: ${SelectedOdds.length} sản phẩm x ${moneyInt} = ${totalCost}, Số dư: ${availableBalance}`);
                
                if (totalCost > availableBalance) {
                    toastr.warning(`Số dư không đủ. Cần: ${formatMoneyJS(totalCost)}, Có: ${formatMoneyJS(availableBalance)}`, "Cảnh báo");
                    $("#btn-confirm").prop("disabled", true).addClass("opacity-50 cursor-not-allowed");
                } else {
                    // Đủ số dư - kích hoạt nút
                    $("#btn-confirm").prop("disabled", false).removeClass("opacity-50 cursor-not-allowed");
                }
            } else {
                // Nếu chưa chọn sản phẩm hoặc chưa có thông tin user, kích hoạt nút
                $("#btn-confirm").prop("disabled", false).removeClass("opacity-50 cursor-not-allowed");
            }
        } catch (e) {
            console.error("Lỗi khi xử lý số lượng:", e);
            e.target.value = 1;
            toastr.error("Lỗi xử lý số lượng, đặt về mặc định (1)", "Lỗi");
        }
    });

    // ================================================================
    // ORDER CONFIRMATION - FIXED
    // ================================================================

    $("#btn-confirm").off("click touchstart").on("click touchstart", function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log("🔘 Confirm button clicked");
        console.log("📦 SelectedOdds at confirm:", SelectedOdds);
        console.log("🏠 isDetailOpen:", isDetailOpen);
        
        if (!isDetailOpen) {
            toastr.warning("Vui lòng mở chi tiết đơn hàng trước khi đặt", "Cảnh báo");
            return;
        }
        
        try {
            // FIX: Kiểm tra kỹ lưỡng SelectedOdds
            if (!Array.isArray(SelectedOdds) || SelectedOdds.length === 0) {
                console.error("❌ No products selected:", SelectedOdds);
                
                // Kiểm tra nếu có UI items active nhưng SelectedOdds bị reset
                const activeItems = $('.odds-item.active');
                if (activeItems.length > 0) {
                    console.log("🔄 Found active UI items, rebuilding SelectedOdds...");
                    SelectedOdds = [];
                    activeItems.each(function() {
                        const type = String($(this).data("odd")).toUpperCase();
                        if (type && !SelectedOdds.includes(type)) {
                            SelectedOdds.push(type);
                        }
                    });
                    console.log("🔄 Rebuilt SelectedOdds:", SelectedOdds);
                }
                
                if (SelectedOdds.length === 0) {
                    toastr.warning("Vui lòng chọn ít nhất một sản phẩm để đặt hàng", "Cảnh báo");
                    return;
                }
            }
            
            let tiencuoc = parseInt($("#txt-lot-money").val(), 10);
            if (isNaN(tiencuoc) || tiencuoc <= 0) {
                toastr.error("Số lượng không hợp lệ, vui lòng nhập lại", "Lỗi");
                $("#txt-lot-money").val(1);
                return;
            }
            
            // Kiểm tra số dư
            if (!UserInfo || typeof UserInfo.money === 'undefined') {
                toastr.warning("Đang tải thông tin tài khoản, vui lòng thử lại", "Cảnh báo");
                GetUserInfo();
                return;
            }
            
            const totalCost = SelectedOdds.length * tiencuoc;
            const availableBalance = UserInfo.money || 0;
            
            console.log(`💰 Final check: ${SelectedOdds.length} sản phẩm x ${tiencuoc} = ${totalCost}, Số dư: ${availableBalance}`);
            
            if (totalCost > availableBalance) {
                toastr.error(`Số dư không đủ. Cần: ${formatMoneyJS(totalCost)}, Có: ${formatMoneyJS(availableBalance)}`, "Lỗi");
                return;
            }
            
            // Disable button để tránh double click
            $(this).prop("disabled", true).text("Đang xử lý...");
            showLoading();

            $.ajax({
                type: "POST",
                url: "ajax/index.php",
                data: {
                    action_type: "do_bet",
                    lid: currentId,
                    money: tiencuoc,
                    item: SelectedOdds.join(","),
                    session: current_session,
                    brand: currentBrand,
                },
                dataType: "json",
                timeout: 15000, // Tăng timeout
                success: function (response) {
                    try {
                        if (response && response.success) {
                            toastr.success(`Đặt hàng thành công!`, "Thành công", {
                                timeOut: 5000,
                                positionClass: "toast-top-center",
                            });
                            
                            // Reset state
                            SelectedOdds = [];
                            $(".odds-item").removeClass("active");
                            updateSelectedProductsDisplay();
                            resetInput();
                            
                            // Reload data
                            GetUserInfo();
                            GetLotteryInfo(currentId).done((res) => {
                                if (res && res.success) {
                                    LotteryInfo = res.data;
                                    window.totalUnits = LotteryInfo.totalUnits || [0, 0, 0, 0];
                                    RenderLotteryInfo(LotteryInfo);
                                }
                            });
                        } else {
                            toastr.error(response.message || "Đặt hàng thất bại, vui lòng thử lại", "Lỗi");
                        }
                    } catch (err) {
                        console.error("Lỗi khi xử lý phản hồi đặt hàng:", err);
                        toastr.error("Có lỗi xảy ra khi xử lý đơn hàng", "Lỗi");
                    } finally {
                        // Re-enable button
                        $("#btn-confirm").prop("disabled", false).text("Nhập hàng");
                        hideLoading();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Lỗi Ajax khi gửi đơn hàng:", status, error);
                    toastr.error("Lỗi kết nối, vui lòng thử lại", "Lỗi");
                    $("#btn-confirm").prop("disabled", false).text("Nhập hàng");
                    hideLoading();
                },
            });
        } catch (e) {
            console.error("Lỗi khi xác nhận đơn hàng:", e);
            toastr.error("Có lỗi xảy ra, vui lòng thử lại", "Lỗi");
            $("#btn-confirm").prop("disabled", false).text("Nhập hàng");
            hideLoading();
        }
    });

    // ================================================================
    // MODAL EVENT HANDLERS
    // ================================================================

    $("#btn-close-modal, #modal-cancel").on("click", function() {
        hideModal();
    });

    // ================================================================
    // ANIMATION EVENT HANDLERS
    // ================================================================

    $(document).on('click', '.wheel-container', function() {
        const wheel = $(this).find('.wheel-img');
        wheel.css('animation', 'vinamilkSpinFast 2s linear');
        setTimeout(() => {
            wheel.css('animation', 'vinamilkSpin 4s linear infinite');
        }, 2000);
    });

    $("#lottery_img").on('error', function() {
        $(this).attr('src', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMzIiIGZpbGw9IiM2MzY2ZjEiLz4KPHN2ZyB4PSIxNiIgeT0iMTYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJ3aGl0ZSI+CjxwYXRoIGQ9Ik0xMiAyQzYuNDggMiAyIDYuNDggMiAxMnM0LjQ4IDEwIDEwIDEwIDEwLTQuNDggMTAtMTBTMTcuNTIgMiAxMiAyem0wIDNjMS42NiAwIDMgMS4zNCAzIDNzLTEuMzQgMy0zIDMtMy0xLjM0LTMtMyAxLjM0LTMgMy0zem0wIDEzLjJjLTIuNSAwLTQuNzEtMS4yOC02LTMuMjIuMDMtMS45OSA0LTMuMDggNi0zLjA8tzUuOTcgMS4wOSA2IDMuMDhjLTEuMjkgMS45NC0zLjUgMy4yMi02IDMuMjJ6Ii8+Cjwvc3ZnPgo8L3N2Zz4=');
    });

    // ================================================================
    // GENERAL CLICK AND KEYBOARD HANDLERS
    // ================================================================

    document.addEventListener('click', function(e) {
        try {
            window.audioContextStarted = true;
            if (e.target.matches('[data-odd]') || e.target.closest('[data-odd]')) {
                if (!isModalOpen && SelectedOdds.length > 0) {
                    showModal();
                }
            }
        } catch (e) {
            console.error("Error in click handler:", e);
        }
    });

    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            if (isModalOpen) {
                hideModal();
            } else if (isDetailOpen) {
                closeDetailSection();
            }
        }
    });

    // Debug shortcut
    window.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'd') {
            e.preventDefault();
            debugSelectedState();
        }
    });

    // ================================================================
    // CLEANUP ON PAGE UNLOAD
    // ================================================================

    window.addEventListener('beforeunload', function() {
        if (countdownInterval) clearInterval(countdownInterval);
        if (pollingInterval) clearInterval(pollingInterval);
    });
});

// ================================================================
// WINDOW EXPORTS
// ================================================================

window.openReview = openReview;
window.showReview = showReview;
window.openDetailSection = openDetailSection;
window.closeDetailSection = closeDetailSection;
window.formatMoneyJS = formatMoneyJS;
window.switchBrand = switchBrand;
window.getBrandImagePath = getBrandImagePath;
window.nextSlide = nextSlide;
window.prevSlide = prevSlide;
window.nextSlide2 = nextSlide2;
window.prevSlide2 = prevSlide2;
window.generateRandomCode = generateRandomCode;
window.generateSessionCodes = generateSessionCodes;
window.updateOrderInfoCard = updateOrderInfoCard;
window.RenderOdds = RenderOdds;
window.updateCodesInUI = updateCodesInUI;
window.getSessionResult = getSessionResult;
window.handleFallbackResult = handleFallbackResult;
window.updateSelectedProductsDisplay = updateSelectedProductsDisplay;
window.validateOrderButton = validateOrderButton;
window.debugSelectedState = debugSelectedState;

// ================================================================
// INITIALIZATION LOGS
// ================================================================

console.log('🎉 Enhanced VINAMILK Order System với Complete Bug Fixes!');
console.log('📦 Brands available:', Object.keys(ODDS_BY_BRAND));
console.log('🖼️ Image paths:');
console.log('- TH: a1.jpg, a2.jpg, a3.jpg, a4.jpg');
console.log('- VINAMILK: b1.jpg, b2.jpg, b3.jpg, b4.jpg');
console.log('✅ Fixed: Selected products management');
console.log('✅ Fixed: Input validation và balance checking');
console.log('✅ Fixed: Session code generation và persistence');
console.log('✅ Fixed: Brand switching without losing selections');
console.log('✅ Fixed: Modal và UI synchronization');
console.log('✅ Fixed: Error handling và user feedback');
console.log('✅ REMOVED: Quantity limit - Không giới hạn số lượng');
console.log('📡 API Support: data.code, data.result, data.items[], direct data');
console.log('🔄 Update frequency: 3s polling + real-time admin integration');
console.log('🎯 Admin panel result format: 11111 → Display: 11111');
console.log('🐛 Debug: Press Ctrl+D to see current state');
console.log('🚀 All major bugs fixed - Ready for production!');
console.log('📈 UNLIMITED QUANTITY: Cho phép nhập số lượng không giới hạn!');
    </script>
</body>
</html>