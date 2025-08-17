<?php

function layout_header($title = "")
{
    if ($title == "") $title = isset($GLOBALS["title"]) ? $GLOBALS["title"] : "";
?>
<script src="/assets/js/unified-answer-system.js"></script>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <title><?= $title ?></title>
        <link rel="icon" type="image/png" sizes="512x512" href="<?= asset_link("image", "favicon-512x512.png") ?>">
        
        <!-- Font Awesome cho icon -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
         <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR&display=swap" rel="stylesheet">
         <!-- Thêm link Font Awesome (nếu chưa có) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#e91e63">

<!-- iOS -->
<link rel="apple-touch-icon" href="/assets/Images/icon-192x192.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">



         <script src="https://cdn.tailwindcss.com"></script>
      
        <link href="<?= asset_link("css", "main.css") ?>" rel="stylesheet" />
        <link href="<?= asset_link("css", "mainpage.css") ?>" rel="stylesheet" />

        <script
            type="module"
            src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script
            nomodule
            src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
    />
        <link href="<?= asset_link("css", "toastr.css") ?>" rel="stylesheet" />
        <link href="<?= asset_link("css", "owl.carousel.min.css") ?>" rel="stylesheet" />
        <link href="<?= asset_link("css", "owl.theme.default.min.css") ?>" rel="stylesheet" />
        <link href="<?= asset_link("css", "viewer.css") ?>" rel="stylesheet" />
        <link href="<?= asset_link("css", "jqupload.css") ?>" rel="stylesheet" />
        <style>
    .center-500 {
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
}

body {
    
  width: 100%;
    
  margin: 0 auto;     /* ← dòng này giờ không còn tác dụng vì body luôn 100% */
  background: #f5f5f5;
  font-family: 'Noto Sans KR', sans-serif;
}

        </style>
    </head>
<?php
}

function css($url)
{
?>
    <link href="<?= asset_link("css", $url) ?>" rel="stylesheet" />
<?php
}

function script($url)
{
?>
    <script src="<?= asset_link("js", $url) ?>"></script>
<?php
}


function endpage($need_footer = true)
{
?>

    <?php if ($need_footer): ?>

  


    <?php endif; ?>


    <?php
    script("jquery-1.12.4.min.js");
    script("popper.js");
    script("bootstrap.min.js");
    //script("bootstrap5.js");

    script("toastr.js");
    script("owl.carousel.min.js");
    script("viewer.js");
    script("jquery-viewer.min.js");
    script("moment.js");
    script("jqupload.js");

    script("utils.js");

    ?>
    <script>
        function _api({
            data,
            onError,
            onSuccess
        }) {
            $.ajax({
                type: "POST",
                url: "<?= route("ajax/index.php") ?>",
                data: data,
                dataType: "json",
                success: onSuccess ?? function(response) {
                    _handle_json_res(response);
                },
                error: onError ?? function(request, status, error) {
                    if (request.responseJSON && request.responseJSON.message) {
                        toastr.error(`${request.status}: ${request.responseJSON.message}`);

                    } else
                        toastr.error(`${request.status}: ${request.responseText}`);
                }
            });
        }
    </script>
<?php

}


function logo()
{
?>
    <div style="height: 0px;"></div>
    <!-- <div class="logo-top1">
        <img src="<?= asset_link("image", "logo.png") ?>" alt="Logo">
    </div> -->
<?php
}
