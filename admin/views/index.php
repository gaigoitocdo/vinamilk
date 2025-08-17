<?php

include_once __DIR__ . "/header.php";
include_once __DIR__ . "/navbar.php";

function begin_view()
{
?>

    <body id="page-top">
        <div id="wrapper">
            <?php admin_side_bar(); ?>
            <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php admin_top_bar(); ?>
                <div class="container-fluid">
                

    <?php
}


function end_view()
{
    ?>
                </div>
            </div>
            </div>
        </div>
    </body>
    <?php
}