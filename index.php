<?php
include_once __DIR__ . "/config/config.php";

$controller = "home";


$controllers_array = [
    "home" => ["Trang chủ", true],
    "review" => ["Đánh giá", true],
    "review-detail" => ["Đánh giá", true],
    "profile" => ["Trang cá nhân", true],
    "history" => ["Xu hướng", true],
      "nhaphang" => ["Nhập hàng 1", true],

    "all_products" => ["Nhập hàng", true],
    "check" => ["Đăng kí", true],
    //
    "404" => ["404 NOT FOUND", false],
    "login" => ["Đăng nhập", false],
    "logout" => ["Đăng xuất", false],
    "register" => ["Đăng ký", false],
    "forgot" => ["Quên mật khẩu", false],
];

if (get_config("maintain_mode") == 1 && !is_admin()) {
    req_controller("maintain");
    exit;
}

$c = $_GET["ctrl"] ?? "home";
if (isset($controllers_array[$c])) {
    $title = $controllers_array[$c][0];
    $required_login = $controllers_array[$c][1];
    if ($required_login && !is_login()) //require login
    {
        $_SESSION["redirect_after_login"] = get_url();
        redirect("/index.php?ctrl=login");
        exit;
    } else {
        req_controller($c, $title);
    }
} else {
    req_controller('404');
}
