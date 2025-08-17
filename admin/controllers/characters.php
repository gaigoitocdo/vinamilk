<?php

include __DIR__ . "/../../models/UserModel.php";
include __DIR__ . "/../../models/CharacterModel.php";

?>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button type="button" id="btn-create" class="btn btn-primary" data-toggle="modal" data-target="#createModel">Tạo user</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Mô tả</th>
                        <th>Ảnh</th>
                        <th>Danh sách ảnh</th>
                        <th>Thông tin phụ</th>
                        <th>Tuổi</th>
                        <th>Rate</th>
                        <th>Trạng thái</th>
                        <th>Loại</th>
                        <th>Hành Động</th>
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
                <h5 class="modal-title">Tạo user</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-create">
                    <div class="form-group">
                        <label for="">Tên</label>
                        <input type="text"
                            class="form-control" name="name">
                    </div>
                    <div class="form-group">
                        <label for="">Mô tả</label>
                        <input type="text"
                            class="form-control" name="desc">
                    </div>
                    <div class="form-group">
                        <label for="">Ảnh nền</label>
                        <input type="text"
                            class="form-control" name="image">
                    </div>
                    <div id="createCharacterImageUploader"></div>
                    <div class="form-group">
                        <label for="">Số đo</label>
                        <input type="text"
                            class="form-control" name="measurements">
                    </div>
                    <div class="form-group">
                        <label for="">Tuổi</label>
                        <input type="text"
                            class="form-control" name="age">
                    </div>

                    <div class="form-group">
                        <label for="">Loại</label>
                        <select class="form-control" name="type">
                            <option value="0">Ẩn</option>
                            <option value="1">Hoạt động</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Địa điểm</label>
                        <input type="text"
                            class="form-control" name="location">
                    </div>
                    <div class="form-group">
                        <label for="">Support count</label>
                        <input type="text"
                            class="form-control" name="support_count">
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
                        <label for="">Tên</label>
                        <input type="text"
                            class="form-control" name="name">
                    </div>
                    <div class="form-group">
                        <label for="">Mô tả</label>
                        <input type="text"
                            class="form-control" name="desc">
                    </div>
                    <div class="form-group">
                        <label for="">Ảnh nền</label>
                        <input type="text"
                            class="form-control" name="image">
                    </div>
                    <div id="editCharacterImageUploader"></div>

                    <div class="form-group">
                        <label for="">Số đo</label>
                        <input type="text"
                            class="form-control" name="measurements">
                    </div>
                    <div class="form-group">
                        <label for="">Tuổi</label>
                        <input type="text"
                            class="form-control" name="age">
                    </div>

                    <div class="form-group">
                        <label for="">Trạng thái</label>
                        <select class="form-control" name="status">
                            <option value="0">Ẩn</option>
                            <option value="1">Hiện</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Loại</label>
                        <select class="form-control" name="type">
                            <option value="0">Bận</option>
                            <option value="1">Hoạt động</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Địa điểm</label>
                        <input type="text"
                            class="form-control" name="location">
                    </div>
                    <div class="form-group">
                        <label for="">Support count</label>
                        <input type="text"
                            class="form-control" name="support_count">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Edit</button>

                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="modalListImg" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Danh sách ảnh</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <button type="button" id="btn-create-image" class="btn btn-primary mb-3">
                    Tạo mới
                </button>

                <form id="form-image-create" class="mb-3" style="display: none;">
                    <div class="form-group">
                        <label for="">Đường dẫn ảnh</label>
                        <input type="text"
                            class="form-control" name="url">
                    </div>

                    <div id="imageCreateUploader"></div>

                    <div class="form-group">
                        <label for="">Trạng thái</label>
                        <select class="form-control" name="type">
                            <option value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create</button>

                </form>

                <table class="table table-bordered" id="ListImgTbl" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Loại</th>
                            <th>Link</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbl-listimg">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalListMeta" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Danh sách thông tin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <button type="button" id="btn-create-meta" class="btn btn-primary mb-3">Tạo mới</button>

                <form id="form-meta-create" class="mb-3" style="display: none;">
                    <div class="form-group">
                        <label for="">Loại dữ liệu</label>
                        <select class="form-control" name="key">
                            <?php foreach (CharacterModel::$AvailableMeta as $meta => $name): ?>
                                <option value="<?= $meta ?>"><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Giá trị</label>
                        <input type="text"
                            class="form-control" name="value">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create</button>

                </form>

                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Key</th>
                            <th>Giá trị</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbl-listmeta">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="modalImage" tabindex="-2" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="img-viewer" style="width: 100%; height: auto; max-width: 100%;" src="#">
            </div>
        </div>
    </div>
</div>

<?php admin_footer(); ?>

<script>
    var current_edit_id = -1;

    $("#imageCreateUploader").uploadFile({
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
                $("#form-image-create").find("input[name='url']").val(data.data);
            } else {
                toastr.error(data.message ?? "Lỗi khi upload file");
            }
            //$("#eventsmessage").html($("#eventsmessage").html() + "<br/>Success for: " + JSON.stringify(data));
        }
    });

    $("#createCharacterImageUploader").uploadFile({
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
                $("#form-create").find("input[name='image']").val(data.data);
            } else {
                toastr.error(data.message ?? "Lỗi khi upload file");
            }
            //$("#eventsmessage").html($("#eventsmessage").html() + "<br/>Success for: " + JSON.stringify(data));
        }
    });

    $("#editCharacterImageUploader").uploadFile({
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
                $("#form-edit").find("input[name='image']").val(data.data);
            } else {
                toastr.error(data.message ?? "Lỗi khi upload file");
            }
            //$("#eventsmessage").html($("#eventsmessage").html() + "<br/>Success for: " + JSON.stringify(data));
        }
    });

    function render_type(type) {
        if (type == 0) return `<span class="badge badge-secondary">Bình thường</span>`;
        if (type == 1) return `<span class="badge badge-danger">Mới</span>`;
        return "Không rõ";
    }

    function render_status(type, id) {
        if (type == 0) return `<a href="#" id="btn-fast-togglestatus" data-type=${type} data-id='${id}' class="badge badge-danger">Ẩn</a>`;
        if (type == 1) return `<a href="#" id="btn-fast-togglestatus" data-type=${type} data-id='${id}' class="badge badge-success">Hiện</a>`;
        return "Không rõ";
    }

    function render_char_image_type(type) {
        if (type == 0) return `<a href="#" id="btn-char-image-fast-toggletype" data-type=${type} class="badge badge-danger">Ẩn</a>`;
        if (type == 1) return `<a href="#" id="btn-char-image-fast-toggletype" data-type=${type} class="badge badge-success">Công khai</a>`;
        return "Không rõ";
    }

    function viewer(url) {
        $("#img-viewer").attr('src', url);
        $("#modalImage").modal('show');
    }

    $(document).ready(function() {
        const dbtable = $("#dataTable").DataTable({
            ajax: {
                url: "<?= route("ajax/characters-db.php") ?>",
                dataSrc: 'data'
            },
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            columns: [{
                    data: 'id',
                },
                {
                    data: 'name',
                    render: (data) => `<textarea>${data}</textarea>`
                },
                {
                    data: 'desc',
                    render: (data) => `<textarea>${data}</textarea>`
                },
                {
                    data: 'image',
                    render: (data, _, row) => `<button class="btn btn-primary" onclick="viewer('${data}')">Xem</button>`
                }, {
                    data: 'id',
                    render: (data, _, row) => `<button class="btn btn-primary" id="btn-list-img" data-id="${data}">Xem</button>`
                }, {
                    data: 'id',
                    render: (data, _, row) => `<button class="btn btn-primary" id="btn-list-metadata" data-id="${data}">Xem</button>`
                },
                {
                    data: 'age'
                },
                {
                    data: 'rating',
                    render: (data, _, row) => `${data} ⭐`
                },
                {
                    data: 'status',
                    render: (data, _, row) => render_status(data, row["id"])
                },
                {
                    data: 'type',
                    render: (data, _, row) => render_type(data)
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

        $("#form-create").on("submit", function(e) {
            e.preventDefault();

            var data = {};

            data.action_type = "add_character";

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data,
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Thêm mới thành công");
                        $("#createModel").modal("hide");
                        dbtable.ajax.reload();
                    } else {
                        toastr.error(response.message ?? "Lỗi không xác định");
                    }
                }
            });
        })

        $("#form-meta-create").on("submit", function(e) {
            e.preventDefault();

            var data = {};
            data.id = current_edit_id;
            data.action_type = "add_character_metadata";

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data,
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Thêm mới thành công");
                        $("#modalListMeta").modal("hide");
                    } else {
                        toastr.error(response.message ?? "Lỗi không xác định");
                    }
                }
            });
        })

        $("#form-image-create").on("submit", function(e) {
            e.preventDefault();

            var data = {};
            data.id = current_edit_id;
            data.action_type = "add_character_image";

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data,
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Thêm mới thành công");
                        $("#modalListMeta").modal("hide");
                    } else {
                        toastr.error(response.message ?? "Lỗi không xác định");
                    }
                }
            });
        })

        $("#btn-create-meta").on("click", function() {
            let c = $("#form-meta-create").css("display");
            $("#form-meta-create").css("display", c == "block" ? "none" : "block");
        });

        $("#btn-create-image").on("click", function() {
            let c = $("#form-image-create").css("display");
            $("#form-image-create").css("display", c == "block" ? "none" : "block");
        });

        $("#form-edit").on("submit", function(e) {
            e.preventDefault();

            var data = {};

            data.action_type = "edit_character";
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
                        dbtable.ajax.reload();
                    }
                }
            });
        })


        $(document).delegate("button[id='btn-list-img']", "click", function() {
            let id = $(this).data("id");
            current_edit_id = id;
            api({
                data: {
                    action_type: "get_character_images",
                    id,
                },
                onSuccess: function(response) {
                    if (response.success) {
                        let {
                            data
                        } = response;
                        $("#tbl-listimg").empty();

                        data.forEach(e => {
                            let html = `
                                <tr data-id="${e.id}">
                                    <td>${e.id}</td>
                                    <td>${render_char_image_type(e.type)}</td>
                                    <td><button onclick="viewer('${e.asset_url}')">Xem</button></td>
                                    <td><div class="btn-group" role="group" aria-label="">
                                    <button type="button" id="btn-img-delete" data-id='${e.id}' class="btn btn-danger">Xoá</button>
                                </div></td>
                                </tr>
                            `
                            $("#tbl-listimg").append(html);

                        })
                        $("#modalListImg").modal('show');
                    }

                }
            })
        })

        $(document).delegate("button[id='btn-list-metadata']", "click", function() {
            let id = $(this).data("id");
            current_edit_id = id;
            api({
                data: {
                    action_type: "get_character_metadata",
                    id,
                },
                onSuccess: function(response) {
                    if (response.success) {
                        let {
                            data
                        } = response;
                        $("#tbl-listmeta").empty();

                        data.forEach(e => {
                            let html = `
                                <tr data-id="${e.id}">
                                    <td>${e.id}</td>
                                    <td>${e.display_name ?? e.meta_key}</td>
                                    <td>${e.meta_value}</td>
                                    <td><button type="button" id="btn-meta-delete" data-id='${e.id}' class="btn btn-danger">Xoá</button></td>
                                </div></td>
                                </tr>
                            `
                            $("#tbl-listmeta").append(html);

                        })
                        $("#modalListMeta").modal('show');
                    }

                }
            })
        })

        $(document).delegate("a[id='btn-fast-togglestatus']", "click", function() {
            let id = $(this).data("id");
            let type = $(this).data("type");

            api({
                data: {
                    action_type: "update_character_status",
                    id,
                    status: type == 0 ? 1 : 0
                },
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Cập nhật thành công");
                        dbtable.ajax.reload(null, false);
                    }

                }
            })
        })

        $(document).delegate("button[id='btn-delete']", "click", function() {
            let id = $(this).data("id");
            if (confirm("Bạn có muốn xoá character này: " + id)) {
                api({
                    data: {
                        action_type: "delete_character",
                        id
                    }
                });
            }
        })

        $(document).delegate("button[id='btn-meta-delete']", "click", function() {
            let id = $(this).data("id");
            if (confirm("Bạn có muốn xoá meta này: " + id)) {
                api({
                    data: {
                        action_type: "delete_character_metadata",
                        id
                    }
                });
            }
        })

        $(document).delegate("button[id='btn-img-delete']", "click", function() {
            let id = $(this).data("id");
            if (confirm("Bạn có muốn xoá ảnh này: " + id)) {
                api({
                    data: {
                        action_type: "delete_character_image",
                        id
                    }
                });
            }
        })

        $(document).delegate("a[id='btn-char-image-fast-toggletype']", "click", function() {
            let id = $(this).parent().parent().data("id");
            let ttype = $(this).data("type");

            api({
                data: {
                    action_type: "update_image_character_status",
                    id,
                    type: ttype == 0 ? 1 : 0
                },
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Cập nhật thành công");
                        //dbtable.ajax.reload(null, false);
                    }

                }
            })
        })

        $(document).delegate("button[id='btn-edit']", "click", function() {
            let id = $(this).data("id");

            api({
                data: {
                    action_type: "get_character",
                    id
                },
                onSuccess: function(response) {
                    if (response.success) {
                        current_edit_id = id;
                        let {
                            data
                        } = response;

                        $("#form-edit").trigger("reset");

                        $("#modal-edit-title").html("Edit: " + data.username);

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