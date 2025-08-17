<?php

include_once __DIR__ . "/../function.php";

function admin_header($title = "")
{
?>

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?= $title ?></title>

        <!-- Custom fonts for this template-->
        <link href="<?= admin_asset_link("vendor", "fontawesome-free/css/all.min.css") ?>" rel="stylesheet" type="text/css">
        <link href="<?= admin_asset_link("css", "toastr.css") ?>" rel="stylesheet" type="text/css">

        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">

        <!-- Custom styles for this template-->
        <link href="<?= admin_asset_link("css", "sb-admin-2.min.css") ?>" rel="stylesheet">
        <link href="<?= admin_asset_link("css", "datatables.css") ?>" rel="stylesheet">
        <link href="<?= asset_link("css", "bootstrap4-extend.css") ?>" rel="stylesheet">
        <link href="<?= asset_link("css", "jqupload.css") ?>" rel="stylesheet">
        <link href="<?= asset_link("css", "viewer.css") ?>" rel="stylesheet">
        <!-- <link href="<?= asset_link("js", "ckeditor/contents.css") ?>" rel="stylesheet"> -->
        <script src="<?= asset_link("js", "ckeditor/ckeditor.js") ?>"></script>
    </head>
<?php
}

function admin_footer()
{
?>

    <script src="<?= admin_asset_link("vendor", "jquery/jquery.min.js") ?>"></script>
    <script src="<?= admin_asset_link("vendor", "bootstrap/js/bootstrap.bundle.min.js") ?>"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= admin_asset_link("vendor", "jquery-easing/jquery.easing.min.js") ?>"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= admin_asset_link("js", "sb-admin-2.min.js") ?>"></script>
    <script src="<?= admin_asset_link("js", "toastr.js") ?>"></script>

    <script src="<?= admin_asset_link("js", "jquery.dataTables.js") ?>"></script>
    <script src="<?= admin_asset_link("js", "datatables.js") ?>"></script>

    <!-- Page level plugins -->
    <script src="<?= admin_asset_link("vendor", "chart.js/Chart.min.js") ?>"></script>
    <script src="<?= admin_asset_link("js", "moment.js") ?>"></script>

    <script src="<?= admin_asset_link("js", "admin.js") ?>"></script>
    <script src="<?= asset_link("js", "jqupload.js") ?>"></script>
    <script src="<?= asset_link("js", "viewer.js") ?>"></script>

    <script>
        $(document).delegate("div[class='upload-drag']", "click", function () {
            console.log($(this).parent().find("input[type='file']").click());
        })
        // $("div[class='upload-drag']").on("click", function () {
            
        // })
        function api({
            data,
            onError,
            onSuccess
        }) {
            $.ajax({
                type: "POST",
                url: "<?= route("ajax/admin.php") ?>",
                data: data,
                dataType: "json",
                success: onSuccess ?? function(response) {
                    handle_json_res(response);
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
