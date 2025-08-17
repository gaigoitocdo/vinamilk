<?php

include_once __DIR__ . "/../../models/LotteryModel.php";

$cats = LotteryModel::GetAllCategories();

$items = LotteryModel::GetAll();


?>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button type="button" id="btn-create" class="btn btn-primary" data-toggle="modal" data-target="#createModel">Tạo mục mới</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Mô tả</th>
                        <th>Tỉ lệ cược</th>
                        <th>Ảnh</th>
                        <th>Key</th>
                        <th>Luật chơi</th>
                        <th>Điều kiện</th>
                        <th>Trạng thái</th>
                        <th>Danh mục</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $key): ?>
                        <tr data-id='<?= $key["id"] ?>'>
                            <td><?= $key["id"] ?></td>
                            <td><textarea><?= $key["name"] ?></textarea></td>
                            <td><textarea><?= $key["desc"] ?></textarea></td>
                            <td><button type="button" data-id="<?= $key["id"] ?>" id="btn-show-lottery-item" class="btn btn-primary">Xem</button></td>

                            <td><a href="<?= $key["image"] ?>">Xem</a></td>
                            <td><?= $key["key"] ?></td>
                            <td><?= $key["rule"] ?> phút 1 kỳ</td>
                            <td>Tối thiểu <?= $key["condition"] ?></td>
                            <td><?= $key["status"]  == 1 ? "Mở" : "Đóng" ?></td>
                            <td><?= $key["cate_name"]  ?></td>

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
                <h5 class="modal-title">Tạo mới</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-create">
                    <div class="form-group">
                        <label for="">Danh mục</label>
                        <select class="form-control" name="cate_id">
                            <?php foreach ($cats as $key): ?>
                                <option value="<?= $key["id"] ?>"><?= $key["name"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tên đánh giá</label>
                        <input type="text"
                            class="form-control" name="name" placeholder="" required>
                    </div>
                    <div class="form-group">
                        <label for="">Mô tả đánh giá</label>
                        <input type="text"
                            class="form-control" name="desc" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Ảnh bìa</label>
                        <input type="text"
                            class="form-control" name="image" placeholder="">
                    </div>
                    <div id="lotteryCreateImageUploader"></div>

                    <div class="form-group">
                        <label for="">Mã định danh đánh giá</label>
                        <input type="text"
                            class="form-control" name="key" placeholder="Vui lòng nhập mã định danh đánh giá của bạn (không trùng lặp, đề nghị đặt theo viết tắt tiếng Anh của tên cửa)" required>
                    </div>
                    <div class="form-group">
                        <label for="">Chơi đánh giá (thời gian mở)</label>
                        <input type="number"
                            class="form-control" value='1' name="rule" placeholder="Vui lòng nhập cách chơi đánh giá của bạn (nhập số, ví dụ nhập 1 thì đại diện cho 1 phút 1 kỳ)" required>
                    </div>
                    <div class="form-group">
                        <label for="">Yêu cầu số tiền đặt cược tối thiểu</label>
                        <input type="number" step="0.1"
                            class="form-control" name="condition" placeholder="" value="0">
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="hot" value="0">
                            Phổ biển (hot)
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">Create</button>
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
                        <label for="">Danh mục</label>
                        <select class="form-control" name="cate_id">
                            <?php foreach ($cats as $key): ?>
                                <option value="<?= $key["id"] ?>"><?= $key["name"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Trạng thái</label>
                        <select class="form-control" name="status">
                            <option value="0">Đóng</option>
                            <option value="1">Mở</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tên đánh giá</label>
                        <input type="text"
                            class="form-control" name="name" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Mô tả đánh giá</label>
                        <input type="text"
                            class="form-control" name="desc" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="">Ảnh bìa</label>
                        <input type="text"
                            class="form-control" name="image" placeholder="">
                    </div>

                    <div id="lotteryEditImageUploader"></div>
                    <div class="form-group">
                        <label for="">Mã định danh đánh giá</label>
                        <input type="text"
                            class="form-control" readonly name="key" placeholder="Vui lòng nhập mã định danh đánh giá của bạn (không trùng lặp, đề nghị đặt theo viết tắt tiếng Anh của tên cửa)" required>
                    </div>
                    <div class="form-group">
                        <label for="">Chơi đánh giá (thời gian mở)</label>
                        <input type="number"
                            class="form-control" value='1' name="rule" placeholder="Vui lòng nhập cách chơi đánh giá của bạn (nhập số, ví dụ nhập 1 thì đại diện cho 1 phút 1 kỳ)" required>
                    </div>
                    <div class="form-group">
                        <label for="">Yêu cầu số tiền đặt cược tối thiểu</label>
                        <input type="number" step="0.1"
                            class="form-control" name="condition" placeholder="">
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="hot" value="0">
                            Phổ biển (hot)
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">Edit</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Lottery Item Modal -->
<div class="modal fade" id="modalLotItem" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lottery Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Cấu hình tỉ lệ cược</h6>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Nhấp vào ô để chỉnh sửa</strong>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Cách chơi</th>
                            <th>Tỷ lệ cược</th>
                        </tr>
                    </thead>
                    <tbody id="lottery-item-tbl">
                        <tr>
                            <td scope="row"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php admin_footer(); ?>

<script>
    var current_edit_id = -1;

    $(document).ready(function() {

        $("#lotteryCreateImageUploader").uploadFile({
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

        $("#lotteryEditImageUploader").uploadFile({
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

        $(document).delegate("button[id='btn-delete']", "click", function() {
            let id = $(this).parent().parent().parent().data("id");
            if (confirm("Bạn có muốn xoá mục này: " + id)) {
                api({
                    data: {
                        action_type: "delete_lottery",
                        id
                    }
                });
            }
        })

        $(document).delegate("button[id='btn-edit']", "click", function() {
            let id = $(this).parent().parent().parent().data("id");

            api({
                data: {
                    action_type: "get_lottery",
                    id
                },
                onSuccess: function(response) {
                    if (response.success) {
                        current_edit_id = id;
                        let {
                            data
                        } = response;
                        $("#modal-edit-title").html("Edit: " + data.name);
                        $("#form-edit").trigger("reset");

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

        $("#form-create").on("submit", function(e) {
            e.preventDefault();

            var data = {};

            data.action_type = "add_lottery";

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data
            });
        })

        $("#form-edit").on("submit", function(e) {
            e.preventDefault();

            var data = {};

            data.action_type = "edit_lottery";
            data.id = current_edit_id;

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data
            });
        })

        $(document).delegate('button[id="btn-show-lottery-item"]', 'click', function() {
            let id = $(this).data("id");
            api({
                data: {
                    action_type: "get_lottery_item",
                    lid: id
                },
                onSuccess: function(resp) {
                    if (resp.success) {
                        console.log(resp.data)
                        $("#lottery-item-tbl").empty();
                        resp.data.forEach(element => {
                            let html = `<tr data-id='${element.id}'>
                                <td scope="row" id="td-litem-editable" data-field='name' >${element.name}</td>
                                <td>${element.type}</td>
                                <td id="td-litem-editable" data-field='proportion'>${element.proportion}</td>
                            </tr>`
                            $("#lottery-item-tbl").append(html);
                        });
                        $("#modalLotItem").modal("show");
                    } else toastr(resp.message ?? "Lỗi không xác định");
                }
            });
        })

        $(document).delegate('td[id="td-litem-editable"]', 'click', function() {
            if (!$(this).is('[contenteditable]')) {
                $(this).attr('contenteditable', true).focus();
            }
        })

        $(document).delegate('td[id="td-litem-editable"]', 'keypress', function(e) {
            if (e.which == 13) {
                $(this).blur();
            }
        })

        $(document).delegate('td[id="td-litem-editable"]', 'blur', function() {
            let field = $(this).data('field');
            let id = $(this).parent().data("id");

            var newValue = $(this).text();
            let data = {
                action_type: "edit_lottery_item",
                id,
            }
            data[field] = newValue;
            api({
                data,
                onSuccess: function(r) {
                    toastr[r.success ? "success" : "error"](r.success ? "Thao tác thành công" : r.message);
                }
            })
        })

    });
</script>