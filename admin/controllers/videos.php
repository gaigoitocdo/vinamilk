<?php

include __DIR__ . "/../../models/VideoModel.php";

$cats = VideoModel::GetAllCategories();
$videos = VideoModel::GetAllVideos();


?>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button type="button" id="btn-create" class="btn btn-primary" data-toggle="modal" data-target="#createModel">Tạo video mới</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Thời lượng</th>
                        <th>Lượt xem</th>
                        <th>Trạng thái</th>
                        <th>Hot</th>
                        <th>Link</th>
                        <th>Danh mục</th>
                        <th>Thời gian thêm</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $key): ?>
                        <tr data-id='<?= $key["id"] ?>'>
                            <td><?= $key["name"] ?></td>
                            <td><?= $key["duration"] ?></td>
                            <td><?= $key["views"] ?></td>
                            <td><?= $key["status"] == 1 ? "Bật" : "Tắt" ?></td>
                            <td><?= $key["hot"] == 1 ? "Có" : "Không" ?></td>
                            <td><a class="btn btn-primary" href="<?= $key["video_url"] ?>" role="button">Xem</a></td>
                            <td><?= $key["category"] ?></td>
                            <td><?= date("Y-m-d H:i:s", $key["upload_time"]) ?></td>
                            <td>
                                <div class="btn-group" role="group" aria-label="">
                                    <button type="button" id="btn-edit" class="btn btn-primary">Chỉnh sửa</button>
                                    <button type="button" id="btn-delete" class="btn btn-danger">Xoá</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Create Modal -->
<div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo video mới</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Tên video</label>
                    <input type="text"
                        class="form-control" id="txt-create-name" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">Đường dẫn ảnh bìa</label>
                    <input type="text"
                        class="form-control" id="txt-create-imgurl" aria-describedby="helpId" placeholder="">
                </div>
                <div id="createUploader"></div>

                <div class="form-group">
                    <label for="">Loại video</label>
                    <select class="form-control" id="video_type">
                        <option value="1">Embed frame</option>
                        <option value="0">URL trực tiếp đến video</option>
                        <option value="2">Chuyển hướng URL</option>

                    </select>
                </div>

                <div class="form-group">
                    <label for="">Đường dẫn video</label>
                    <input type="text"
                        class="form-control" id="txt-create-vidurl" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">Thời lượng</label>
                    <input type="text"
                        class="form-control" value="00:00" id="txt-create-time" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">Lượt xem</label>
                    <input type="number"
                        class="form-control" value="0" id="txt-create-views" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">Danh mục</label>
                    <select class="form-control" id="select-create-cate">
                        <?php foreach ($cats as $key): ?>
                            <option value="<?= $key["id"] ?>"><?= $key["name"] ?> (<?= $key["vid_num"] ?> videos)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="btn-create-video" class="btn btn-primary">Create</button>
            </div>
        </div>
    </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-edit-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Tên video</label>
                    <input type="text"
                        class="form-control" id="txt-edit-name" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">Đường dẫn ảnh bìa</label>
                    <input type="text"
                        class="form-control" id="txt-edit-imgurl" aria-describedby="helpId" placeholder="">
                </div>

                <div id="editUploader"></div>

                <div class="form-group">
                    <label for="">Loại video</label>
                    <select class="form-control" id="edit_video_type">
                        <option value="1">Embed frame</option>
                        <option value="0">URL trực tiếp đến video</option>
                        <option value="2">Chuyển hướng URL</option>

                    </select>
                </div>

                <div class="form-group">
                    <label for="">Đường dẫn video</label>
                    <input type="text"
                        class="form-control" id="txt-edit-vidurl" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">Trạng thái</label>
                    <select class="form-control" id="select-edit-status">
                        <option value="0">Tắt</option>
                        <option value="1">Bật</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Hot</label>
                    <select class="form-control" id="select-edit-hot">
                        <option value="0">Không</option>
                        <option value="1">Có</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Thời lượng</label>
                    <input type="text"
                        class="form-control" value="00:00" id="txt-edit-time" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">Lượt xem</label>
                    <input type="number"
                        class="form-control" value="0" id="txt-edit-views" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">Danh mục</label>
                    <select class="form-control" id="select-edit-cate">
                        <?php foreach ($cats as $key): ?>
                            <option value="<?= $key["id"] ?>"><?= $key["name"] ?> (<?= $key["vid_num"] ?> videos)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="btn-edit-submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<?php admin_footer(); ?>

<script>
    var current_edit_id = -1;

    $("#createUploader").uploadFile({
        url: "<?= route("ajax/uploader.php") ?>",
        formData: {
            "type": "character_image"
        },
        fileName: "file",
        //multiple:true,
        returnType: "json",
        acceptFiles: "image/*",
        onSuccess: function(files, data, xhr, pd) {
            if (data.success) {
                $("input[id='txt-create-imgurl']").val(data.data);
            } else {
                toastr.error(data.message ?? "Lỗi khi upload file");
            }
            //$("#eventsmessage").html($("#eventsmessage").html() + "<br/>Success for: " + JSON.stringify(data));
        }
    });

    $("#editUploader").uploadFile({
        url: "<?= route("ajax/uploader.php") ?>",
        formData: {
            "type": "character_image"
        },
        fileName: "file",
        //multiple:true,
        returnType: "json",
        acceptFiles: "image/*",
        onSuccess: function(files, data, xhr, pd) {
            if (data.success) {
                $("input[id='txt-edit-imgurl']").val(data.data);
            } else {
                toastr.error(data.message ?? "Lỗi khi upload file");
            }
            //$("#eventsmessage").html($("#eventsmessage").html() + "<br/>Success for: " + JSON.stringify(data));
        }
    });

    $(document).ready(function() {
        $("#dataTable").DataTable();
        $("#btn-create-video").on("click", function() {
            let name = $("#txt-create-name").val();
            let imgurl = $("#txt-create-imgurl").val();
            let vidurl = $("#txt-create-vidurl").val();
            let time = $("#txt-create-time").val();
            let views = $("#txt-create-views").val();
            let cate = $("#select-create-cate").val();
            let video_url_type = $("#video_type").val();

            if (views == '') views = '0'
            if (time == '') time = '30:00'

            if (name != '' && vidurl != '' && cate != '') {
                api({
                    data: {
                        action_type: "create_video",
                        name,
                        imgurl,
                        vidurl,
                        video_url_type,
                        time,
                        views,
                        cate
                    },
                });
            } else {
                toastr.error("Vui lòng nhập đầy đủ thông tin!");
            }
        })

        $(document).delegate("button[id='btn-delete']", "click", function() {
            let id = $(this).parent().parent().parent().data("id");
            if (confirm("Bạn có muốn xoá mục này: " + id)) {
                api({
                    data: {
                        action_type: "delete_video",
                        id
                    }
                });
            }
        })

        $(document).delegate("button[id='btn-edit']", "click", function() {
            let id = $(this).parent().parent().parent().data("id");

            api({
                data: {
                    action_type: "get_video",
                    id
                },
                onSuccess: function(response) {
                    if (response.success) {
                        current_edit_id = id;
                        let {
                            data
                        } = response;
                        $("#modal-edit-title").html("Chỉnh sửa: " + data.name);
                        $("#txt-edit-name").val(data.name);
                        $("#txt-edit-imgurl").val(data.bg_image);
                        $("#txt-edit-vidurl").val(data.video_url);
                        $("#txt-edit-time").val(data.duration);
                        $("#txt-edit-views").val(data.views);
                        $("#select-edit-cate").val(data.cate_id);
                        $("#select-edit-status").val(data.status);
                        $("#select-edit-hot").val(data.hot);
                        $("#edit_video_type").val(data.video_url_type);

                        $("#editModel").modal("show");
                    }
                }
            })
        })

        $("#btn-edit-submit").on("click", function() {
            let name = $("#txt-edit-name").val();
            let imgurl = $("#txt-edit-imgurl").val();
            let vidurl = $("#txt-edit-vidurl").val();
            let time = $("#txt-edit-time").val();
            let views = $("#txt-edit-views").val();
            let cate = $("#select-edit-cate").val();

            let hot = $("#select-edit-hot").val();
            let status = $("#select-edit-status").val();
            let video_url_type = $("#edit_video_type").val();

            if (views == '') views = '0'
            if (time == '') time = '30:00'


            if (current_edit_id != -1 && name != '' && vidurl != '' && cate != '') {
                api({
                    data: {
                        action_type: "update_video",
                        id: current_edit_id,
                        name,
                        imgurl,
                        vidurl,
                        video_url_type,
                        time,
                        views,
                        cate,
                        hot,
                        status
                    }
                })
            } else {
                toastr.error("Vui lòng nhập đầy đủ thông tin!");
            }
        });
    });
</script>