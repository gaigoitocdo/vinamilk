<?php
$key = field("key", NULL, true, true);
$id = field("id", NULL, true, true);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once __DIR__ . "/../includes/RealTimeLotteryProcessor.php";
view("header");
layout_header();
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Riview Detail💝</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
   * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100vh;
      overflow-x: hidden;
      margin: 0;
      padding: 0;
    }

    /* Body - Fill đầy màn hình */
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
      background-size: 400% 400%;
      animation: gradientShift 8s ease infinite;
      font-family: -apple-system, BlinkMacSystemFont, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 0.5rem;
    }

    @keyframes gradientShift {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    /* Main Container - Cân đối hoàn hảo */
    .main-container {
      width: 100%;
      max-width: 500px;
      height: calc(100vh - 1rem);
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
  
      box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.2),
        0 0 0 1px rgba(255, 255, 255, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      position: relative;
    }

    /* Header - Fixed height */
    .header-section {
      flex-shrink: 0;
      padding: 1.25rem 1.5rem;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
    }

    /* Content Area - Scrollable middle */
    .content-section {
      flex: 1;
      overflow-y: auto;
      padding: 1rem 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    /* Bottom Section - Fixed height */
    .bottom-section {
      flex-shrink: 0;
      padding: 1rem 1.5rem;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
      border-top: 1px solid rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
    }

    /* ===========================================
       ENHANCED RESULT EFFECTS
       =========================================== */

  /* Result Overlay - Siêu đẹp, nâng cấp */
.result-overlay {
  position: fixed;
  inset: 0;
  background: radial-gradient(circle at center, rgba(0, 0, 0, 0.85), rgba(15, 15, 45, 0.95));
  backdrop-filter: blur(20px);
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: all 0.7s cubic-bezier(0.25, 0.1, 0.25, 1.5);
}

.result-overlay.active {
  opacity: 1;
  pointer-events: all;
}

/* Result Content - Animation đỉnh cao */
.result-content {
  text-align: center;
  color: white;
  transform: scale(0.05) rotateY(180deg) rotateX(60deg);
  animation: resultEpicReveal 1.4s cubic-bezier(0.2, 0.85, 0.4, 1.6) forwards;
  padding: 3.5rem 2.5rem;
  background: linear-gradient(160deg, 
    rgba(75, 80, 225, 0.45), 
    rgba(120, 70, 230, 0.45),
    rgba(200, 50, 130, 0.45)
  );
  border: 4px solid rgba(255, 255, 255, 0.5);
  border-radius: 40px;
  backdrop-filter: blur(30px);
  position: relative;
  overflow: hidden;
  box-shadow: 
    0 60px 120px rgba(0, 0, 0, 0.6),
    0 0 100px rgba(75, 80, 225, 0.4),
    inset 0 3px 0 rgba(255, 255, 255, 0.3);
}

/* Shimmer effect siêu ngầu */
.result-content::before {
  content: '';
  position: absolute;
  top: -120%;
  left: -120%;
  width: 400%;
  height: 400%;
  background: linear-gradient(60deg, 
    transparent, 
    rgba(255, 255, 255, 0.4), 
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  animation: epicShimmer 3.5s ease-in-out infinite;
  pointer-events: none;
}

/* Result Text - Gradient bá đạo */
.result-main {
  font-size: 7rem;
  font-weight: 900;
  margin-bottom: 2rem;
  background: linear-gradient(60deg, 
    #ff4040, #00d4ff, #ffaa00, #00ffcc, #ff00ff, #ff4500
  );
  background-size: 800% 800%;
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  animation: 
    epicGradientShift 4s ease-in-out infinite,
    resultEpicPulse 2.5s ease-in-out infinite,
    resultEpicFloat 3.5s ease-in-out infinite;
  text-shadow: 
    0 0 50px rgba(255, 255, 255, 0.9),
    0 0 100px rgba(75, 80, 225, 0.7);
  filter: drop-shadow(0 0 25px rgba(255, 255, 255, 0.8));
  position: relative;
  z-index: 1;
}

.result-session {
  font-size: 2.2rem;
  font-weight: 800;
  margin-bottom: 2rem;
  color: #ffaa00;
  text-shadow: 
    0 0 25px rgba(255, 170, 0, 1),
    0 0 50px rgba(255, 170, 0, 0.8);
  animation: resultEpicGlow 2.5s ease-in-out infinite alternate;
  position: relative;
  z-index: 1;
}

.result-subtitle {
  font-size: 1.6rem;
  font-weight: 700;
  opacity: 0.98;
  animation: resultEpicFloat 3s ease-in-out infinite;
  color: #d1d5db;
  text-shadow: 0 0 20px rgba(209, 213, 219, 0.6);
  position: relative;
  z-index: 1;
}

/* Enhanced Animations */
@keyframes resultEpicReveal {
  0% { transform: scale(0.05) rotateY(180deg) rotateX(60deg); opacity: 0; }
  25% { transform: scale(0.7) rotateY(90deg) rotateX(30deg); opacity: 0.8; }
  50% { transform: scale(1.3) rotateY(30deg) rotateX(10deg); opacity: 0.95; }
  75% { transform: scale(0.98) rotateY(0deg) rotateX(-5deg); opacity: 1; }
  100% { transform: scale(1) rotateY(0deg) rotateX(0deg); opacity: 1; }
}

@keyframes epicShimmer {
  0% { transform: translateX(-200%) translateY(-200%) rotate(60deg); }
  50% { transform: translateX(60%) translateY(60%) rotate(60deg); }
  100% { transform: translateX(300%) translateY(300%) rotate(60deg); }
}

@keyframes epicGradientShift {
  0%, 100% { background-position: 0% 50%; }
  25% { background-position: 100% 0%; }
  50% { background-position: 50% 100%; }
  75% { background-position: 0% 0%; }
}

@keyframes resultEpicPulse {
  0%, 100% { transform: scale(1) rotate(0deg); }
  25% { transform: scale(1.08) rotate(2deg); }
  50% { transform: scale(1.12) rotate(0deg); }
  75% { transform: scale(1.08) rotate(-2deg); }
}

@keyframes resultEpicGlow {
  0% { opacity: 1; text-shadow: 0 0 25px rgba(255, 170, 0, 1), 0 0 50px rgba(255, 170, 0, 0.8); }
  100% { opacity: 0.9; text-shadow: 0 0 40px rgba(255, 170, 0, 1.2), 0 0 70px rgba(255, 170, 0, 1), 0 0 100px rgba(255, 170, 0, 0.7); }
}

@keyframes resultEpicFloat {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  33% { transform: translateY(-10px) rotate(2deg); }
  66% { transform: translateY(6px) rotate(-2deg); }
}
    /* Enhanced Fireworks */
    .mega-firework {
      position: absolute;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      animation: megaFireworkExplode 3s ease-out infinite;
    }

    /* Multiple firework colors */
    .mega-firework:nth-child(1) { 
      background: radial-gradient(circle, #ff6b6b, #ff1744);
      animation-delay: 0s; 
      left: 15%; top: 20%;
    }
    .mega-firework:nth-child(2) { 
      background: radial-gradient(circle, #4ecdc4, #00bcd4);
      animation-delay: 0.4s; 
      left: 80%; top: 30%;
    }
    .mega-firework:nth-child(3) { 
      background: radial-gradient(circle, #45b7d1, #2196f3);
      animation-delay: 0.8s; 
      left: 20%; top: 70%;
    }
    .mega-firework:nth-child(4) { 
      background: radial-gradient(circle, #96ceb4, #4caf50);
      animation-delay: 1.2s; 
      left: 75%; top: 75%;
    }
    .mega-firework:nth-child(5) { 
      background: radial-gradient(circle, #ffd93d, #ff9800);
      animation-delay: 1.6s; 
      left: 50%; top: 15%;
    }
    .mega-firework:nth-child(6) { 
      background: radial-gradient(circle, #e91e63, #ad1457);
      animation-delay: 2s; 
      left: 85%; top: 60%;
    }

    @keyframes megaFireworkExplode {
      0% { 
        transform: translateY(0px) scale(1); 
        opacity: 1; 
        box-shadow: 0 0 0 0 currentColor;
      }
      25% {
        transform: translateY(-40px) scale(1.5);
        opacity: 0.9;
        box-shadow: 0 0 30px 15px currentColor;
      }
      50% {
        transform: translateY(-80px) scale(2);
        opacity: 0.7;
        box-shadow: 0 0 50px 25px currentColor;
      }
      75% {
        transform: translateY(-120px) scale(2.5);
        opacity: 0.4;
        box-shadow: 0 0 70px 35px transparent;
      }
      100% { 
        transform: translateY(-160px) scale(3); 
        opacity: 0; 
        box-shadow: 0 0 100px 50px transparent;
      }
    }

    /* Enhanced Sparkles */
    .mega-sparkle {
      position: absolute;
      width: 8px;
      height: 8px;
      background: white;
      border-radius: 50%;
      animation: megaSparkleMove 2s linear infinite;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.9);
    }

    @keyframes megaSparkleMove {
      0% { 
        transform: translate(0, 0) scale(0) rotate(0deg); 
        opacity: 1; 
      }
      25% {
        transform: translate(var(--dx), var(--dy)) scale(1.5) rotate(90deg); 
        opacity: 1; 
      }
      50% { 
        transform: translate(calc(var(--dx) * 1.5), calc(var(--dy) * 1.5)) scale(2) rotate(180deg); 
        opacity: 0.8; 
      }
      75% {
        transform: translate(calc(var(--dx) * 1.8), calc(var(--dy) * 1.8)) scale(1.5) rotate(270deg); 
        opacity: 0.4; 
      }
      100% { 
        transform: translate(calc(var(--dx) * 2), calc(var(--dy) * 2)) scale(0) rotate(360deg); 
        opacity: 0; 
      }
    }

    /* ===========================================
       EXISTING STYLES - IMPROVED
       =========================================== */

    /* Notification Container */
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
      padding: 0.75rem;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      animation: slideInNotification 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      font-weight: 600;
      color: white;
    }

    .notification-success {
      background: linear-gradient(135deg, #10b981, #34d399);
    }

    .notification-error {
      background: linear-gradient(135deg, #ef4444, #f87171);
    }

    .notification-warning {
      background: linear-gradient(135deg, #f59e0b, #fbbf24);
    }

    .notification-special {
      background: linear-gradient(135deg, #8b5cf6, #a78bfa);
    }

    .notification-info {
      background: linear-gradient(135deg, #3b82f6, #60a5fa);
    }

    .notification-highlight {
      background: linear-gradient(135deg, #ec4899, #f472b6);
    }

    .notification-alert {
      background: linear-gradient(135deg, #f97316, #fb923c);
    }

    @keyframes slideInNotification {
      0% { transform: translateX(100%) scale(0.8); opacity: 0; }
      100% { transform: translateX(0) scale(1); opacity: 1; }
    }

    /* Odds Item Enhancements */
    .odds-item {
      position: relative;
      cursor: pointer;
      border-radius: 16px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: 2px solid rgba(99, 102, 241, 0.2);
      background: white;
      overflow: hidden;
    }

    .odds-item::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(45deg, transparent, rgba(99, 102, 241, 0.05), transparent);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .odds-item:hover::before {
      opacity: 1;
    }

    .odds-item.active {
      border: 3px solid #6366f1;
      transform: scale(1.02);
      box-shadow: 
        0 10px 25px rgba(99, 102, 241, 0.3),
        0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .odds-item.active::after {
      content: "✓";
      position: absolute;
      top: 0.75rem;
      right: 0.75rem;
      background: linear-gradient(135deg, #8b5cf6, #6366f1);
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
      box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
    }

    @keyframes checkMarkBounce {
      0% { transform: scale(0) rotate(180deg); }
      50% { transform: scale(1.2) rotate(10deg); }
      100% { transform: scale(1) rotate(0deg); }
    }

    /* Progress Indicator */
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
      stroke: #10b981;
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

    /* Bottom Sheet Enhanced */
    .bottom-sheet {
      position: fixed;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%) translateY(100%);
      background: linear-gradient(145deg, #1a1a1a, #2a2a2a);
      padding: 1.5rem;
      box-shadow:
        0 -20px 50px rgba(0, 0, 0, 0.4),
        0 -5px 25px rgba(99, 102, 241, 0.3);
      transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      z-index: 1000;
      max-width: 500px;
      width: calc(100% - 1rem);
      margin: 0 auto;
      overflow: hidden;
      backdrop-filter: blur(15px);
      border: 2px solid rgba(99, 102, 241, 0.3);
 
    }

    .bottom-sheet.show {
      transform: translateX(-50%) translateY(0);
    }

    .sheet-handle {
      width: 50px;
      height: 4px;
      background: linear-gradient(135deg, #6366f1, #8b5cf6);
      border-radius: 8px;
      margin: 0 auto 1rem;
      cursor: grab;
      transition: all 0.3s ease;
    }

    .sheet-handle:hover {
      box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
      transform: scaleY(1.5);
    }

    /* Modal Enhancements */
    .fixed.inset-0 {
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(8px);
    }

    /* Custom Scrollbar */
    .content-section::-webkit-scrollbar {
      width: 6px;
    }

    .content-section::-webkit-scrollbar-track {
      background: rgba(0, 0, 0, 0.1);
      border-radius: 3px;
    }

    .content-section::-webkit-scrollbar-thumb {
      background: linear-gradient(135deg, #6366f1, #8b5cf6);
      border-radius: 3px;
    }

    .content-section::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(135deg, #4f46e5, #7c3aed);
    }

    /* Responsive Design */
    @media (max-width: 640px) {
      body {
        padding: 0.25rem;
      }

      .main-container {
        max-width: 100%;
        height: calc(100vh - 0.5rem);
        border-radius: 16px;
      }

      .header-section,
      .bottom-section {
        padding: 1rem;
      }

      .content-section {
        padding: 0.75rem 1rem;
      }

      .result-main {
        font-size: 4rem;
      }

      .result-session {
        font-size: 1.4rem;
      }

      .result-subtitle {
        font-size: 1.1rem;
      }

      .result-content {
        padding: 2rem 1.5rem;
        border-radius: 24px;
      }

      .odds-item img {
        height: 100px !important;
      }

      .bottom-sheet {
        max-width: 100%;
        width: calc(100% - 0.5rem);
        border-radius: 16px 16px 0 0;
      }
    }

    /* Performance Optimizations */
    .odds-item,
    .result-content,
    .notification,
    .bottom-sheet {
      will-change: transform, opacity;
    }

    /* Reduce motion for accessibility */
    @media (prefers-reduced-motion: reduce) {
      * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
      }
    }

    /* VIP History Modal */
    #modalLotteryHistory {
      background: rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(8px);
    }

    #modalLotteryHistory .modal-content {
      background: linear-gradient(135deg, #1f2937, #111827);
      border: 2px solid #f59e0b;
      border-radius: 0; /* Góc vuông */
      padding: 1.5rem;
      max-height: 80vh;
      overflow-y: auto;
    }

    #modalLotteryHistory h5 {
      color: #f59e0b;
      font-size: 1.5rem;
      text-shadow: 0 0 8px rgba(245, 158, 11, 0.5);
    }

    #modalLotteryHistory table {
      color: #e5e7eb;
    }

    #modalLotteryHistory th {
      color: #f59e0b;
      font-size: 0.8rem;
      text-transform: uppercase;
      padding: 0.75rem 0.5rem;
    }

    #modalLotteryHistory tr {
      transition: all 0.2s ease;
    }

    #modalLotteryHistory tr:hover {
      background: rgba(245, 158, 11, 0.1);
      transform: scale(1.02);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    #modalLotteryHistory td {
      padding: 0.75rem 0.5rem;
      font-size: 0.8rem;
    }

    #modalLotteryHistory .status-pending {
      background: #f59e0b;
      color: #000;
      padding: 0.25rem 0.75rem;
      font-size: 0.7rem;
      font-weight: bold;
    }

    #modalLotteryHistory .status-completed {
      background: #10b981;
      color: white;
      padding: 0.25rem 0.75rem;
      font-size: 0.7rem;
      font-weight: bold;
    }

    #modalLotteryHistory .status-current {
      background: #3b82f6;
      color: white;
      padding: 0.25rem 0.75rem;
      font-size: 0.7rem;
      font-weight: bold;
      animation: currentBlink 1s infinite;
    }

    @keyframes currentBlink {
      0%, 50% { opacity: 1; }
      51%, 100% { opacity: 0.6; }
    }

    /* Loading States */
    .loading {
      opacity: 0.7;
      pointer-events: none;
    }

    .loading::after {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(255, 255, 255, 0.1);
      animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    /* Additional utility classes */
    .text-gradient {
      background: linear-gradient(135deg, #6366f1, #8b5cf6);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .glass-effect {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
  </style>
</head>

<body>
  <!-- Notification Container -->
  <div id="notificationContainer" class="notification-container"></div>

  <!-- Enhanced Result Overlay -->
  <div id="resultOverlay" class="result-overlay hidden">
    <div class="result-content">
      <div id="resultSession" class="result-session">🎊 Phiên #12345</div>
      <div id="resultMain" class="result-main">A</div>
      <div class="result-subtitle">🏆 Kết quả đã công bố!</div>
      
      <!-- Enhanced Fireworks -->
      <div class="mega-firework"></div>
      <div class="mega-firework"></div>
      <div class="mega-firework"></div>
      <div class="mega-firework"></div>
      <div class="mega-firework"></div>
      <div class="mega-firework"></div>
    </div>
  </div>

  <!-- Main Container - Perfect Balance -->
  <div class="main-container">
    <!-- Header Section -->
    <div class="header-section">
      <div class="flex items-center justify-between mb-4">
        <button id="btn-back" class="text-gray-600 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-300 rounded-lg p-2 transition-all" aria-label="Go back">
          <i class="fa-solid fa-chevron-left text-lg"></i>
        </button>
        <span id="lottery_title" class="text-lg sm:text-xl font-bold text-gradient flex items-center">
          <i class="fas fa-diamond mr-2"></i>Lottery Title
        </span>
        <div class="w-8"></div> <!-- Spacer for balance -->
      </div>
    </div>

    <!-- Content Section - Scrollable -->
    <div class="content-section">
      <!-- Lottery Info -->
      <div class="glass-effect rounded-2xl p-4 mb-4">
        <div class="flex items-center justify-between mb-4">
          <img id="lottery_img" src="" alt="Lottery" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full object-cover border-2 border-indigo-300 shadow-lg">
          <div class="text-right">
            <div class="text-gray-600 text-xs font-medium">
              <i class="fas fa-play-circle mr-1"></i>Kỳ hiện tại
            </div>
            <b id="now-session" class="text-base sm:text-lg text-indigo-600">-1</b>
          </div>
        </div>
        
        <div class="flex items-center justify-between text-xs sm:text-sm mb-4">
          <div class="text-gray-700 font-medium">
            <i class="fas fa-history mr-1"></i>Kỳ trước <b id="prev-session" class="text-indigo-600">-1</b>
          </div>
          <div class="space-x-2">
            <span id="prev-odd-1" class="font-semibold text-emerald-600 px-2 py-1 bg-emerald-100 rounded-lg text-xs">
              <i class="fas fa-trophy mr-1"></i>
            </span>
            <span id="prev-odd-2" class="font-semibold text-violet-600 px-2 py-1 bg-violet-100 rounded-lg text-xs">
              <i class="fas fa-medal mr-1"></i>
            </span>
          </div>
        </div>

        <!-- Countdown Timer -->
        <div class="text-center bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-3 sm:p-4 rounded-xl">
          <div class="flex items-center justify-center mb-1">
            <i class="fas fa-hourglass-half mr-2"></i>
            <span class="text-xs sm:text-sm font-medium">Thời gian còn lại</span>
          </div>
          <div id="count-down" class="font-bold text-lg sm:text-xl tracking-wider">--:--:--</div>
        </div>
      </div>

      <!-- History Button -->
      <div class="mb-4">
        <button id="btn-lottery-history"
                class="w-full px-3 py-2 text-white font-semibold text-sm bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 transition-all rounded-xl shadow-lg hover:shadow-xl">
          <i class="fa fa-history mr-1"></i>Lịch sử
        </button>
      </div>

      <!-- Odds Grid with Integrated Progress -->
      <div id="value_odds" class="grid grid-cols-2 gap-3 sm:gap-4 mb-4"></div>
    </div>

  <div id="bottom-sheet" class="bs-overlay" role="dialog" aria-modal="true">
        <div class="bs-card" role="document">
            <div class="bs-header">
                <h3 id="bs-brand-title">VINAMILK</h3>
                <button id="btn-close-bottomsheet" class="bs-close" aria-label="Đóng">×</button>
            </div>
            <div class="bs-input has-caret">
                <input type="number" id="txt-lot-money" min="1" value="1" placeholder="Nhập số lượng (1 = 1 VNĐ)">
            </div>
            <div class="bs-actions">
                <button id="btn-submit" class="bs-confirm">Nhập hàng</button>
                <button id="bs-cancel" class="bs-cancel">Đóng</button>
            </div>
        </div>
    </div>

  <!-- Enhanced Bottom Sheet -->
  <div id="bottom-sheet" class="bottom-sheet" role="dialog" aria-label="Bet selection">
    <div class="sheet-handle"></div>
    <div class="space-y-4">
      <div class="flex justify-between items-center">
        <span class="text-white font-bold text-sm sm:text-base">
          <i class="fas fa-crosshairs mr-2"></i>Đang chọn
        </span>
        <div class="bet-number font-bold text-white text-sm"></div>
        <button id="btn-close-bottomsheet" class="text-white text-lg sm:text-xl focus:outline-none focus:ring-2 focus:ring-white rounded-lg p-1" aria-label="Close bottom sheet">
          <i class="fas fa-times-circle"></i>
        </button>
      </div>
      <div class="flex justify-between items-center">
        <span class="text-white font-bold text-sm sm:text-base">
          <i class="fas fa-heart text-red-300 mr-2"></i>Số điểm 
        </span>
        <input type="number" id="txt-lot-money" class="border-0 px-2 py-1 w-20 sm:w-24 text-right bg-white/90 text-gray-800 font-bold text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 rounded-lg" step="0.1" placeholder="Nhập số" value="1">
      </div>
      <div class="flex justify-between items-center">
        <span class="text-white font-bold text-sm sm:text-base">
          <i class="fas fa-list-check mr-2"></i>Tổng lượt chọn
        </span>
        <span id="totalBetOption" class="font-bold text-white text-base sm:text-lg">0</span>
      </div>
      <div class="flex justify-between items-center">
        <span class="text-white font-bold text-sm sm:text-base">
          <i class="fas fa-calculator mr-2"></i>Tổng Số điểm
        </span>
        <span id="totalBetMoney" class="font-bold text-white text-base sm:text-lg">0</span>
      </div>
      <button id="btn-submit" class="w-full py-2 bg-white text-indigo-600 hover:bg-gray-100 font-bold rounded-xl shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-indigo-300" aria-label="Confirm bet">
        <i class="fas fa-paper-plane mr-2"></i>XÁC NHẬN
      </button>
    </div>
  </div>

  <!-- Enhanced Confirm Modal -->
  <div id="modalConfirm" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-4 sm:p-6 w-11/12 max-w-sm mx-auto">
      <div class="flex justify-between items-center mb-4">
        <h5 class="font-bold text-base sm:text-lg text-gray-800">
          <i class="fas fa-shield-check text-emerald-500 mr-2"></i>Xác nhận?
        </h5>
        <button id="closeConfirm" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 rounded-lg p-1" aria-label="Close confirmation modal">
          <i class="fas fa-times-circle text-xl"></i>
        </button>
      </div>
      <ul id="bet-confirm-list" class="space-y-2 text-xs sm:text-sm"></ul>
      <div class="flex justify-end space-x-2 mt-4">
        <button id="noConfirm" class="py-2 px-4 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all">KHÔNG</button>
        <button id="btn-confirm" class="py-2 px-4 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold rounded-xl focus:outline-none focus:ring-2 focus:ring-green-300 transition-all">CÓ</button>
      </div>
    </div>
  </div>

  <!-- Enhanced VIP History Modal -->
  <div id="modalLotteryHistory" class="fixed inset-0 hidden flex items-center justify-center z-50">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl w-11/12 max-w-sm mx-auto border-2 border-amber-400">
      <div class="p-4">
        <div class="flex justify-between items-center mb-3">
          <h5 class="font-bold text-center w-full text-amber-400 text-lg">
            <i class="fas fa-crown mr-2"></i>Lịch sử 
          </h5>
          <button id="closeLotteryHistory" class="text-amber-400 hover:text-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-300 rounded-lg p-1" aria-label="Close VIP history modal">
            <i class="fas fa-times-circle text-xl"></i>
          </button>
        </div>
        <div class="overflow-x-auto max-h-96 overflow-y-auto">
          <table class="w-full text-white">
            <thead>
              <tr class="text-left border-b-2 border-amber-500/30">
                <th class="font-bold py-2 text-amber-400 text-sm"><i class="fas fa-ticket-alt mr-1"></i>Phiên</th>
                <th class="font-bold py-2 text-amber-400 text-sm"><i class="fas fa-trophy mr-1"></i>Kết quả</th>
                <th class="font-bold py-2 text-amber-400 text-sm"><i class="fas fa-info-circle mr-1"></i>Trạng thái</th>
              </tr>
            </thead>
            <tbody id="tbl-lottery-history"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script>
    // Progress Simulation
    if (typeof window.initProgressSim === 'undefined') {
      window.initProgressSim = function(session) {
        console.log('Progress sim initialized for session:', session);
        // Initialize totalUnits if not set
        if (!window.totalUnits) {
          window.totalUnits = [0, 0, 0, 0]; // Default to 4 odds items
        }
      };
      window.drawProgressBars = function(seconds) {
        try {
          const oddsItems = document.querySelectorAll('.odds-item');
          if (!window.totalUnits || !Array.isArray(window.totalUnits) || oddsItems.length === 0) {
            console.warn('No totalUnits or odds items available');
            return;
          }
          const total = window.totalUnits.reduce((a, b) => a + b, 0) || 1; // Avoid division by zero
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

    // URL Parameters and Rank Key
    const params = new URLSearchParams(window.location.search);
    const pageId = params.get('id');
    let rankKey;
    switch(pageId) {
      case '6': rankKey = 1; break;
      case '7': rankKey = 2; break;
      case '8': rankKey = 3; break;
      default: rankKey = 1;
    }
    window.RANK_KEY = rankKey;

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

    var key = '<?= $key ?>';
    var id = '<?= $id ?>';
    var Odds = [];
    var UserInfo = {};
    var LotteryInfo = {};
    var SelectedOdds = [];
    var current_session = "";
    var countdownInterval;
    var pollingInterval;
    var remainingSeconds = 999;
    window.audioContextStarted = false;

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
        
        // Tạo nhiều firework hơn với hiệu ứng đẹp hơn
        const fireworkCount = 15;
        for (let i = 0; i < fireworkCount; i++) {
          setTimeout(() => {
            const firework = document.createElement('div');
            firework.className = 'firework';
            
            // Random position
            const x = Math.random() * window.innerWidth;
            const y = Math.random() * window.innerHeight;
            firework.style.left = x + 'px';
            firework.style.top = y + 'px';
            
            // Random color
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
          
          // Tạo âm thanh phức tạp hơn
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
        console.log('Web Audio không được hỗ trợ');
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

    function GetLotteryInfo() {
      return $.ajax({
        type: "POST",
        url: "<?= route("ajax/index.php") ?>",
        data: { action_type: "get_lottery", id },
        dataType: "json",
        timeout: 10000
      });
    }

    function GetOdds() {
      $.ajax({
        type: "POST",
        url: "<?= route("ajax/index.php") ?>",
        data: { action_type: "get_lottery_odd", key, id },
        dataType: "json",
        timeout: 10000,
        success: function(response) {
          try {
            if (response && response.success) {
              Odds = response.data || [];
              RenderOdds(Odds);
              // Update totalUnits with actual data if available
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
      console.log("DEBUG: RenderLotteryInfo data:", data);

      $("#lottery_title").html(data.name);
      $("#lottery_img").attr('src', data.image);

      let nowNum = parseInt(data.now_session);
      if (isNaN(nowNum)) {
        console.warn("Warning: data.now_session không phải số:", data.now_session);
        nowNum = data.now_session;
      }
      $("#now-session").text(nowNum);

      let prevNum;
      if (data.prev_session && data.prev_session != 0) {
        prevNum = parseInt(data.prev_session);
        if (isNaN(prevNum)) {
          console.warn("Warning: data.prev_session không phải số:", data.prev_session);
          prevNum = data.prev_session;
        }
      } else {
        prevNum = (typeof nowNum === 'number' && !isNaN(nowNum)) ? nowNum - 1 : '';
      }
      $("#prev-session").text(prevNum);

      if (typeof nowNum === 'number' && !isNaN(nowNum) && typeof prevNum === 'number' && nowNum - prevNum !== 1) {
        console.warn("⚠️ Đang cập nhật! Điều chỉnh prev-session về", nowNum - 1);
        $("#prev-session").text(nowNum - 1);
      }

      current_session = nowNum;
      remainingSeconds = parseInt(data.second) || 0;
      window.totalUnits = data.totalUnits || [0, 0, 0, 0]; // Initialize totalUnits
      SelectedOdds = [];
      try {
        localStorage.setItem("currentSession", nowNum.toString());
      } catch (e) {
        console.warn("Không lưu localStorage được:", e);
      }

      if (data.items && Array.isArray(data.items)) {
        for (let i = 0; i < Math.min(data.items.length, 2); i++) {
          $(`#prev-odd-${i + 1}`).html(data.items[i] || '');
        }
      }

      $("#count-down").text(convertSeconds(remainingSeconds));
      window.drawProgressBars(remainingSeconds);
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
            <div class="odds-item cursor-pointer overflow-hidden border-2 border-gray-200 hover:border-indigo-300 transition-all bg-white shadow-lg hover:shadow-xl"
                 data-odd='${type}' data-img='${imgSrc}' data-index='${index}' role="button" aria-label="Select ${e.name}">
              <div class="odds-progress" aria-label="Progress for ${e.name}">
                <svg class="odds-progress-circle" viewBox="0 0 36 36">
                  <circle class="bg" cx="18" cy="18" r="14"></circle>
                  <circle class="fg" cx="18" cy="18" r="14"></circle>
                </svg>
                <span class="odds-progress-text">0% | 0</span>
              </div>
              <img src="${imgSrc}" alt="${e.name || ''}" class="w-full aspect-square object-cover mb-1" loading="lazy" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMzIiIGZpbGw9IiM2MzY2ZjEiLz4KPHN2ZyB4PSIxNiIgeT0iMTYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJ3aGl0ZSI+CjxwYXRoIGQ9Ik0xMiAyQzYuNDggMiAyIDYuNDggMiAxMnM0LjQ4IDEwIDEwIDEwIDEwLTQuNDggMTAtMTBTMTcuNTIgMiAxMiAyem0wIDNjMS42NiAwIDMgMS4zNCAzIDNzLTEuMzQgMy0zIDMtMy0xLjM0LTMtMyAxLjM0LTMgMy0zem0wIDEzLjJjLTIuNSAwLTQuNzEtMS4yOC02LTMuMjIuMDMtMS45OSA0LTMuMDggNi0zLjA4czUuOTcgMS4wOSA2IDMuMDhjLTEuMjkgMS45NC0zLjUgMy4yMi02IDMuMjJ6Ii8+Cjwvc3ZnPgo8L3N2Zz4=';">
              <p class="text-center font-semibold text-gray-800 text-sm px-2">${e.name || ''}</p>
              <p class="text-center text-xs text-gray-500 pb-2">${e.proportion || ''}</p>
            </div>
          `;
          container.append(html);
        });
        window.drawProgressBars(remainingSeconds);
      } catch (e) {
        console.error("Error rendering odds:", e);
      }
    }

    $(document).on("click touchstart", "#closeConfirm, #noConfirm", function () {
      $("#modalConfirm").addClass("hidden");
    });

    $(document).on("click touchstart", "#closeLotteryHistory", function () {
      $("#modalLotteryHistory").addClass("hidden");
    });

    let isBottomSheetOpen = false;
    $(document).on("click touchstart", ".odds-item", function(e) {
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
        
        // Show/hide bottom sheet với animation đẹp
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

    function startPolling() {
      try {
        if (pollingInterval) {
          clearInterval(pollingInterval);
        }
        pollingInterval = setInterval(() => {
          $.ajax({
            type: "POST",
            url: "<?= route("ajax/index.php") ?>",
            data: { action_type: "get_lottery", id },
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
              const prevResult = prevResultEl.textContent.replace('🏆 ', '').trim();
              if (prevResult && prevResult !== '') {
                setTimeout(() => {
                  showResultEffect(parseInt(current_session) - 1, prevResult);
                }, 500);
              }
            }
            if (typeof toastr !== 'undefined') {
              toastr.success("🎊  Kết quả phiên " + (parseInt(current_session) - 1) + " đã có!", '', {
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
          data: { action_type: "get_lottery", id },
          dataType: "json",
          timeout: 10000
        });

        if (response && response.success) {
          LotteryInfo = response.data;
          window.totalUnits = LotteryInfo.totalUnits || [0, 0, 0, 0];
          RenderLotteryInfo(LotteryInfo);
          remainingSeconds = parseInt(LotteryInfo.second) || 60;
          countdownInterval = setInterval(updateTimer, 1000);
          startPolling();
          if (typeof toastr !== 'undefined') {
            toastr.success(`🚀 PHIÊN MỚI: ${current_session} bắt đầu!`, '', {
              timeOut: 5000,
              positionClass: 'toast-top-center'
            });
          }
          const prevResultEl = document.getElementById('prev-odd-1');
          if (prevResultEl) {
            const prevResult = prevResultEl.textContent.replace('🏆 ', '').trim();
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
        console.error("Lỗi khi cập nhật kỳ mới:", error);
        if (typeof toastr !== 'undefined') {
          toastr.error("Lỗi kết nối, đang thử lại...");
        }
        setTimeout(fetchNewTimeAndRestart, 5000);
      }
    }

    function render_iswin(s) {
      if (s == 1) return `<span class="px-2 py-1 bg-green-500 text-white text-xs font-bold">Đúng</span>`;
      if (s == 2) return `<span class="px-2 py-1 bg-red-500 text-white text-xs font-bold">Sai</span>`;
      return `<span class="px-2 py-1 bg-yellow-400 text-white text-xs font-bold">Chưa mở</span>`;
    }

    function render_lottery_history_status(s, session) {
      // Kiểm tra nếu đây là phiên hiện tại
      if (session == current_session) {
        return `<span class="status-current"><i class="fas fa-clock mr-1"></i>Hiện tại</span>`;
      }
      if (s == 1) return `<span class="status-pending"><i class="fas fa-hourglass-half mr-1"></i>Chưa mở</span>`;
      if (s == 0) return `<span class="status-completed"><i class="fas fa-check-circle mr-1"></i>Đã mở</span>`;
      return `<span class="px-2 py-1 bg-gray-500 text-white font-medium text-xs">Unknown</span>`;
    }

    function resetinput() {
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

    $(document).ready(function() {
      $("#btn-lottery-history").on("click touchstart", function() {
        $.ajax({
          type: "POST",
          url: "<?= route("ajax/index.php") ?>",
          data: { action_type: "get_lottery_history", lid: id },
          dataType: "json",
          timeout: 10000,
          success: function(response) {
            try {
              if (response && response.success) {
                let historyData = (response.data || []).slice(0, 10);
                $("#tbl-lottery-history").empty();
                
                // Thêm phiên hiện tại vào đầu danh sách nếu chưa có
                const currentExists = historyData.some(e => e.sid == current_session);
                if (!currentExists && current_session) {
                  const currentRow = `<tr class="hover:bg-indigo-50 border-b border-blue-200">
                    <td scope="row" class="py-2 font-medium text-blue-400">${current_session}</td>
                    <td class="py-2 font-bold text-blue-400">Đang chờ...</td>
                    <td class="py-2">${render_lottery_history_status(1, current_session)}</td>
                  </tr>`;
                  $("#tbl-lottery-history").append(currentRow);
                }
                
                historyData.forEach(e => {
                  let row = `<tr class="hover:bg-indigo-50 border-b border-gray-100">
                    <td scope="row" class="py-2 font-medium">${e.sid || ''}</td>
                    <td class="py-2 font-bold text-amber-400">${e.result || 'Chưa có'}</td>
                    <td class="py-2">${render_lottery_history_status(e.status, e.sid)}</td>
                  </tr>`;
                  $("#tbl-lottery-history").append(row);
                });
                $("#modalLotteryHistory").removeClass("hidden");
              }
            } catch (e) {
              console.error("Error processing lottery history:", e);
            }
          },
          error: function() {
            console.error("Error getting lottery history");
          }
        });
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
            let html = `<li class="bg-indigo-50 p-3 border border-indigo-200">
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

      $("#btn-back").on("click touchstart", function() {
        try {
          history.back();
        } catch (e) {
          window.location.href = '/';
        }
      });

      $("#btn-confirm").on("click touchstart", function() {
        try {
          $.ajax({
            type: "POST",
            url: "<?= route("ajax/index.php") ?>",
            data: {
              action_type: "do_bet",
              lid: id,
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
                  resetinput();
                  GetLotteryInfo().done((res) => {
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

      function initializeSystem() {
        try {
          console.log("🚀 Bắt đầu khởi tạo hệ thống...");
          GetLotteryInfo()
            .done((response) => {
              if (response && response.success) {
                LotteryInfo = response.data;
                window.totalUnits = LotteryInfo.totalUnits || [0, 0, 0, 0];
                RenderLotteryInfo(LotteryInfo);
              }
              console.log("✅ Lottery info loaded");
              GetOdds();
              GetUserInfo();
              countdownInterval = setInterval(updateTimer, 1000);
              startPolling();
              console.log("✅ Countdown timer đã khởi động");
            })
            .fail(error => {
              console.error("❌ Lỗi khởi tạo (ajax fail):", error);
              setTimeout(() => {
                if (typeof updateTimer === 'function') {
                  initializeSystem();
                }
              }, 5000);
            });
        } catch (e) {
          console.error("Error initializing system:", e);
        }
      }

      initializeSystem();
    });
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