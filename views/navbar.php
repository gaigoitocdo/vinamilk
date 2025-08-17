<?php
// helpers cho navbar (đặt đầu file navbar.php)
$current_lid = (int)($_GET['id'] ?? $_GET['lid'] ?? 0);

$href = $href ?? function (string $name, array $params = []): string {
    // map alias -> ctrl
    $map = [
        'home'         => '?ctrl=home',
        'profile'      => '?ctrl=profile',
        'history'      => '?ctrl=history',
        'history_page' => '?ctrl=history', // alias cũ -> mới
    ];
    $url = $map[$name] ?? $name;
    if ($params) {
        $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($params);
    }
    return $url;
};

$isActive = $isActive ?? function (string $name): string {
    $ctrl = $_GET['ctrl'] ?? 'home';
    // coi history_page và history là một
    $isHistory = ($name === 'history') && in_array($ctrl, ['history','history_page'], true);
    return ($ctrl === $name || $isHistory) ? ' active' : '';
};


// Guard: tránh render trùng nếu view("navbar") bị gọi nhiều lần
if (!defined('VM_NAVBAR_RENDERED')) {
  define('VM_NAVBAR_RENDERED', true);

  // Lấy ctrl hiện tại + giữ id/lid để khi chuyển trang không mất context
  $ctrl = $ctrl ?? ($_GET['ctrl'] ?? 'home');
  $id   = isset($id) ? (int)$id : (int)($_GET['id'] ?? ($_GET['lid'] ?? 0));

  // Helpers
  $isActive = function(string $name) use ($ctrl) { return $ctrl === $name ? ' active' : ''; };
  $href     = function(string $name) use ($id)   { return '?ctrl='.$name.($id ? '&id='.$id : ''); };
  ?>
  <style>
    :root{
      --vm-blue:#0B5BB5; --vm-blue-dark:#093f86; --vm-blue-100:#EAF3FF;
      --vm-ink:#0b3766; --vm-border:rgba(11, 91, 181, .12);
    }
    /* chừa chỗ cho thanh nav dưới cùng */
    body{ padding-bottom: calc(76px + env(safe-area-inset-bottom, 0px) + 20px); }

    .bottom-nav{
      position:fixed; left:0; right:0; bottom:0; margin:0 auto;
      width:100%; max-width:500px;
      height: calc(64px + env(safe-area-inset-bottom, 0px));
      background:#fff; color:var(--vm-blue-dark); z-index:1000;
      border-top:1px solid var(--vm-border);
      box-shadow:0 -6px 20px rgba(11, 91, 181, .12);
      backdrop-filter:saturate(160%) blur(8px);
      overflow:hidden; padding-bottom: env(safe-area-inset-bottom, 0px);
    }
    .nav-menu{ display:grid; grid-template-columns:repeat(5,1fr); height:100%; }
    .nav-item{
      display:flex; flex-direction:column; align-items:center; justify-content:center;
      gap:6px; text-decoration:none; color:var(--vm-ink);
      font-size:11px; font-weight:600; letter-spacing:.2px; position:relative;
      transition:background .25s, color .25s, transform .15s; isolation:isolate;
    }
    .nav-item svg{ width:22px; height:22px; fill:var(--vm-ink); transition:fill .25s, transform .15s; }
    .nav-item:hover, .nav-item.active{ background:var(--vm-blue-100); color:var(--vm-blue); }
    .nav-item:hover svg, .nav-item.active svg{ fill:var(--vm-blue); transform:translateY(-1px); }
    .nav-item.active::after{
      content:''; position:absolute; top:0; left:50%; width:28px; height:3px; transform:translateX(-50%);
      background:var(--vm-blue); border-bottom-left-radius:3px; border-bottom-right-radius:3px;
    }
    .nav-item::before{
      content:''; position:absolute; inset:0; margin:auto; width:0; height:0; border-radius:50%;
      background:rgba(11, 91, 181, .12); transition:width .35s, height .35s; z-index:-1;
    }
    .nav-item:active::before{ width:120px; height:120px; }
    .nav-item:focus-visible{ outline:2px solid var(--vm-blue-100); outline-offset:2px; border-radius:12px; }
    @media (max-width:400px){ .nav-item{ font-size:10px; gap:4px; } .nav-item svg{ width:20px; height:20px; } }
  </style>

  <div class="bottom-nav">
    <nav class="nav-menu" role="navigation" aria-label="Thanh điều hướng">
      <a href="<?= htmlspecialchars($href('home')) ?>" class="nav-item<?= $isActive('home') ?>" data-nav="home" aria-label="Trang chủ">
        <svg viewBox="0 0 24 24"><path d="m12 5.69 5 4.5V18h-2v-6H9v6H7v-7.81l5-4.5M12 3 2 12h3v8h6v-6h2v6h6v-8h3L12 3z"/></svg>
        <span>Trang chủ</span>
      </a>

      <a href="https://www.tiktok.com" target="_blank" rel="noopener" class="nav-item" data-nav="tiktok" aria-label="Tiktok">
        <svg viewBox="0 0 24 24"><path d="M21 7.917v4.034a9.948 9.948 0 0 1 -5 -1.951v4.5a6.5 6.5 0 1 1 -8 -6.326v4.326a2.5 2.5 0 1 0 4 2v-11.5h4.083a6.005 6.005 0 0 0 4.917 4.917z"/></svg>
        <span>Tiktok</span>
      </a>

<a href="?ctrl=history&id=15"
   class="nav-item<?= $isActive('history') ?>" data-nav="review" aria-label="Xu hướng">
  <svg viewBox="0 0 24 24"><path d="m16 6 2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
  <span>Xu hướng</span>
</a>


      <a href="<?= htmlspecialchars($href('profile')) ?>" class="nav-item<?= $isActive('profile') ?>" data-nav="profile" aria-label="Trang cá nhân">
        <svg viewBox="0 0 24 24"><path d="M18.39 14.56C16.71 13.7 14.53 13 12 13s-4.71.7-6.39 1.56C4.61 15.07 4 16.1 4 17.22V20h16v-2.78c0-1.12-.61-2.15-1.61-2.66zM18 18H6v-.78c0-.38.2-.72.52-.88C7.71 15.73 9.63 15 12 15c2.37 0 4.29.73 5.48 1.34.32.16.52.5.52.88V18zm-8-6h4c1.66 0 3-1.34 3-3 0-.73-.27-1.4-.71-1.92.13-.33.21-.7.21-1.08 0-1.25-.77-2.32-1.86-2.77C14 2.48 13.06 2 12 2s-2 .48-2.64 1.23C8.27 3.68 7.5 4.75 7.5 6c0 .38.08.75.21 1.08C7.27 7.6 7 8.27 7 9c0 1.66 1.34 3 3 3z"/></svg>
        <span>Trang cá nhân</span>
      </a>

      <a href="#" class="nav-item" data-nav="cskh" onclick="openLiveChat();return false;" aria-label="CSKH">
        <svg viewBox="0 0 24 24"><path d="M19 14v4h-2v-4h2M7 14v4H6c-.55 0-1-.45-1-1v-3h2m5-13c-4.97 0-9 4.03-9 9v7c0 1.66 1.34 3 3 3h3v-8H5v-2c0-3.87 3.13-7 7-7s7 3.13 7 7v2h-4v8h4v1h-7v2h6c1.66 0 3-1.34 3-3V10c0-4.97-4.03-9-9-9z"/></svg>
        <span>CSKH</span>
      </a>
    </nav>
  </div>

  <?php
  // LiveChat — nhúng 1 lần với guard riêng
  if (!defined('VM_LIVECHAT_EMBEDDED')) {
    define('VM_LIVECHAT_EMBEDDED', true); ?>
    <script>
      // LiveChat embed
      window.__lc = window.__lc || {};
      window.__lc.license = 19268394;
      window.__lc.integration_name = "manual_channels";
      window.__lc.product_name = "livechat";
      ;(function(n,t,c){
        function i(n){return e._h?e._h.apply(null,n):e._q.push(n)}
        var e={_q:[],_h:null,_v:"2.0",
          on:function(){i(["on",c.call(arguments)])},
          once:function(){i(["once",c.call(arguments)])},
          off:function(){i(["off",c.call(arguments)])},
          get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can't use getters before load.");return i(["get",c.call(arguments)])},
          call:function(){i(["call",c.call(arguments)])},
          init:function(){var n=t.createElement("script");n.async=!0;n.type="text/javascript";n.src="https://cdn.livechatinc.com/tracking.js";t.head.appendChild(n)}
        };
        !n.__lc.asyncInit && e.init();
        n.LiveChatWidget = n.LiveChatWidget || e;
      })(window,document,[].slice);

      function openLiveChat(){
        try{
          if (window.LiveChatWidget){
            LiveChatWidget.on('ready', function(){ LiveChatWidget.call('maximize'); });
            LiveChatWidget.call && LiveChatWidget.call('maximize');
          } else {
            window.open('https://www.livechat.com/chat-with/19268394/','_blank');
          }
        }catch(_){
          window.open('https://www.livechat.com/chat-with/19268394/','_blank');
        }
      }

      // căn vị trí livechat theo khung 500px
      function calcSideGap(){
        const maxW = 500;
        const used = Math.min(window.innerWidth, maxW);
        return (window.innerWidth - used)/2 + 12;
      }
      function placeLiveChat(){
        const rightGap = calcSideGap();
        const bottomGap = 64 + 12;
        const iframes = Array.from(document.querySelectorAll('iframe[src*="livechatinc.com"]'));
        iframes.forEach(iframe=>{
          try{
            iframe.style.position = 'fixed';
            iframe.style.right = rightGap + 'px';
            iframe.style.bottom = bottomGap + 'px';
            iframe.style.zIndex = 1100;
          }catch(_){}
        });
        const panels = document.querySelectorAll('[id^="lc-"], [class*="lc-"]');
        panels.forEach(el=>{
          const s = window.getComputedStyle(el);
          if (s.position === 'fixed'){
            el.style.right = rightGap + 'px';
            el.style.bottom = bottomGap + 'px';
            el.style.zIndex = 1100;
          }
        });
      }
      (function waitLC(){
        if (window.LiveChatWidget && typeof LiveChatWidget.on==='function'){
          LiveChatWidget.on('ready', placeLiveChat);
        } else {
          setTimeout(waitLC, 200);
        }
      })();
      window.addEventListener('resize', placeLiveChat);
      window.addEventListener('load', placeLiveChat);
    </script>
  <?php } // end VM_LIVECHAT_EMBEDDED guard ?>

<?php } // end VM_NAVBAR_RENDERED guard
