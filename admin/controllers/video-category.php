<?php

include __DIR__ . "/../../models/VideoModel.php";

$cats = VideoModel::GetAllCategories();


?>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button type="button" id="btn-create" class="btn btn-primary" data-toggle="modal" data-target="#createModel">Tạo danh mục mới</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Số lượng video</th>
                        <th>Trạng thái</th>
                        <th>Thứ tự</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cats as $key): ?>
                        <tr data-id='<?= $key["id"] ?>'>
                            <td><?= $key["name"] ?></td>
                            <td><?= $key["vid_num"] ?></td>
                            <td><?= $key["status"] == 1 ? "Bật" : "Tắt" ?></td>

                            <td><?= $key["order"] ?></td>
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo danh mục</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Tên danh mục</label>
                    <input type="text"
                        class="form-control" id="txt-create-name" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">Số thứ tự</label>
                    <input type="number"
                        class="form-control" value="0" id="txt-create-order" aria-describedby="helpId" placeholder="">
                    <small id="helpId" class="form-text text-muted">Số càng cao thì càng được sắp xếp ưu tiên</small>
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-edit-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Tên danh mục</label>
                    <input type="text"
                        class="form-control" id="txt-edit-name" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group">
                  <label for="">Trạng thái</label>
                  <select class="form-control" id="select-edit-status">
                    <option value="0">Tắt</option>
                    <option value="1">Bật</option>
                  </select>
                </div>
                <div class="form-group">
                    <label for="">Số thứ tự</label>
                    <input type="number"
                        class="form-control" value="0" id="txt-edit-order" aria-describedby="helpId" placeholder="">
                    <small id="helpId" class="form-text text-muted">Số càng cao thì càng được sắp xếp ưu tiên</small>
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

    $(document).ready(function() {
        
        $("#btn-create-video").on("click", function() {
            let name = $("#txt-create-name").val();
            let order = $("#txt-create-order").val();

            if (name != '') {
                api({
                    data: {
                        action_type: "create_video_category",
                        name,
                        order
                    },
                });
            } else {
                toastr.error("Vui lòng nhập đầy đủ thông tin!");
            }
        })

        $(document).delegate("button[id='btn-delete']", "click", function() {
            let id = $(this).parent().parent().parent().data("id");
            if (confirm("Bạn có muốn xoá danh mục này: " + id)) {
                api({
                    data: {
                        action_type: "delete_video_category",
                        id
                    }
                });
            }
        })

        $(document).delegate("button[id='btn-edit']", "click", function() {
            let id = $(this).parent().parent().parent().data("id");

            api({
                data: {
                    action_type: "get_video_category",
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
                        $("#txt-edit-order").val(data.order);
                        $("#select-edit-status").val(data.status);
                        $("#editModel").modal("show");
                    }
                }
            })
        })

        $("#btn-edit-submit").on("click", function () {
            let name = $("#txt-edit-name").val();
            let order = $("#txt-edit-order").val();
            let status = $("#select-edit-status").val();
            console.log(order);
            if(current_edit_id != -1 && name != '' && order != '')
            {
                api({
                    data: {
                        action_type: "update_video_category",
                        id: current_edit_id,
                        name, order, status
                    }
                })
            }
            else
            {
                toastr.error("Vui lòng nhập đầy đủ thông tin!");
            }
        });
    });
</script>