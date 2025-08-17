<?php

include_once __DIR__."/../config/config.php";

if(!is_login())
{
    redirect("/admin/login.php");
}

if(!is_admin())
{
    redirect("/admin/login.php");
}

include_once __DIR__."/views/index.php";

function req_admin_controller($name)
{
    $dir = __DIR__."/controllers/$name.php";
    if (file_exists($dir)) {
        require $dir;
    } else {
        return req_admin_controller("home");
    }
}

admin_header("Admin page");

begin_view();

req_admin_controller(field("controller", "home"));

end_view();