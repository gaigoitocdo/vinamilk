<?php

include __DIR__ . "/../../models/ImageModel.php";

$cats = ImageModel::GetAllCategories();

?>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button type="button" id="btn-create" class="btn btn-primary" data-toggle="modal" data-target="#createModel">Tạo image mới</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Loại</th>
                        <th>Link</th>
                        <th>Danh mục</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

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
                <h5 class="modal-title">Tạo image mới</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-create">
                    <div class="form-group">
                        <label for="">Đường dẫn image</label>
                        <input type="text"
                            class="form-control" name="url" aria-describedby="helpId" placeholder="" required>
                    </div>

                    <div class="form-group">
                        <label>
                            Hoặc upload từ thiết bị
                        </label>
                        <input type="hidden"
                            class="form-control" name="upload_img" aria-describedby="helpId" placeholder="">
                        <div id="fileuploader">Tải lên ảnh mới</div>

                    </div>
                    <div class="form-group">
                        <label for="">Loại ảnh</label>
                        <select class="form-control" name="type" id="">
                            <option value="1">Public (ai ai cũng thấy)</option>
                            <option value="0">Ẩn (đăng nhập mới thấy)</option>
                            <option value="2">Private (mập mờ)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Danh mục</label>
                        <select class="form-control" name="cate_id">
                            <?php foreach ($cats as $key): ?>
                                <option value="<?= $key["id"] ?>"><?= $key["name"] ?> (<?= $key["img_num"] ?> images)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create</button>

                </form>
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
                <form id="form-edit">
                    <div class="form-group">
                        <label for="">Tên image</label>
                        <input type="text"
                            class="form-control" name="name" aria-describedby="helpId" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Đường dẫn image</label>
                        <input type="text"
                            class="form-control" name="url" aria-describedby="helpId" placeholder="">
                    </div>
                    <div id="edituploader"></div>
                    <div class="form-group">
                        <label for="">Loại ảnh</label>
                        <select class="form-control" name="type" id="">
                            <option value="1">Public (ai ai cũng thấy)</option>
                            <option value="0">Ẩn (đăng nhập mới thấy)</option>
                            <option value="2">Private (mập mờ)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Danh mục</label>
                        <select class="form-control" name="cate_id">
                            <?php foreach ($cats as $key): ?>
                                <option value="<?= $key["id"] ?>"><?= $key["name"] ?> (<?= $key["img_num"] ?> images)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Edit</button>

                </form>
            </div>
        </div>
    </div>
</div>

<?php admin_footer(); ?>

<script>
    var current_edit_id = -1;

    function render_type(id) {
        if (id == 0) return `<span class="badge badge-warning">Ẩn</a>`;
        if (id == 1) return `<span class="badge badge-success">Public</a>`;
        if (id == 2) return `<span class="badge badge-danger">Riêng tư</a>`;
        return "Không rõ";

    }

    $(document).ready(function() {
        $("#fileuploader").uploadFile({
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
                    $("#form-create").find("input[name='upload_img']").val(data.data);
                    $("#form-create").find("input[name='url']").val(data.data);
                } else {
                    toastr.error(data.message ?? "Lỗi khi upload file");
                }
                //$("#eventsmessage").html($("#eventsmessage").html() + "<br/>Success for: " + JSON.stringify(data));
            }
        });

        $("#edituploader").uploadFile({
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
                    $("#form-edit").find("input[name='url']").val(data.data);
                } else {
                    toastr.error(data.message ?? "Lỗi khi upload file");
                }
                //$("#eventsmessage").html($("#eventsmessage").html() + "<br/>Success for: " + JSON.stringify(data));
            }
        });
        const dbtable = $("#dataTable").DataTable({
            ajax: {
                url: "<?= route("ajax/images-db.php") ?>",
                dataSrc: 'data'
            },
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            columns: [{
                    data: 'name',
                },
                {
                    data: 'type',
                    render: (data, type, row) => render_type(data)

                },
                {
                    data: 'url',
                    render: (data, type, row) => `<a href='${data}' class="btn btn-primary">Xem</a>`
                },
                {
                    data: 'cate_name',
                },
                {
                    data: 'id',
                    render: (data, type, row) => `<div class="btn-group" role="group" aria-label="">
                                    <button type="button" id="btn-edit" data-id='${data}' class="btn btn-primary">Chỉnh sửa</button>
                                    <button type="button" id="btn-delete" data-id='${data}' class="btn btn-danger">Xoá</button>
                                </div>`,
                }
            ]
        });


        $(document).delegate("button[id='btn-delete']", "click", function() {
            let id = $(this).data("id");
            if (confirm("Bạn có muốn xoá mục này: " + id)) {
                api({
                    data: {
                        action_type: "delete_image",
                        id
                    },
                    onSuccess: function(response) {
                        if (response.success) {
                            toastr.success("Xoá thành công");
                            dbtable.ajax.reload(null, false);
                        } else {
                            toastr.error(response.message ?? "Lỗi không xác định");
                        }
                    }
                });
            }
        })

        $("#form-create").on("submit", function(e) {
            e.preventDefault();

            var data = {};

            data.action_type = "create_image";

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data,
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Thêm mới thành công");
                        $("#createModel").modal("hide");
                        dbtable.ajax.reload(null, false);
                    }
                }
            });
        })

        $("#form-edit").on("submit", function(e) {
            e.preventDefault();

            var data = {};

            data.action_type = "update_image";
            data.id = current_edit_id;

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data,
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Chỉnh sửa thành công");
                        $("#editModel").modal("hide");
                        dbtable.ajax.reload(null, false);
                    }
                }
            });
        })

        $(document).delegate("button[id='btn-edit']", "click", function() {
            let id = $(this).data("id");

            api({
                data: {
                    action_type: "get_image",
                    id
                },
                onSuccess: function(response) {
                    if (response.success) {
                        current_edit_id = id;
                        let {
                            data
                        } = response;
                        $("#form-edit").trigger("reset");

                        $("#modal-edit-title").html("Edit: " + data.name);

                        $('#form-edit').find('input').each(function() {
                            var name = $(this).attr('name'); // Lấy name của input
                            if (data[name] && name != 'password') { // Kiểm tra xem JSON có key trùng với name không
                                $(this).val(data[name]); // Gán giá trị từ JSON vào input
                            }
                        });

                        $('#form-edit').find('select').each(function() {
                            var name = $(this).attr('name'); // Lấy name của input
                            if (data[name] && name != 'password') { // Kiểm tra xem JSON có key trùng với name không
                                $(this).val(data[name]); // Gán giá trị từ JSON vào input
                            }
                        });

                        $("#editModel").modal('show');
                    }
                }
            })
        })
    });
</script>