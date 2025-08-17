<?php

include_once __DIR__ . "/../../models/ServiceModel.php";

$cats = ServiceModel::GetAll();
?>


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button type="button" id="btn-create" class="btn btn-primary" data-toggle="modal" data-target="#createModel">Tạo service mới</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Thứ tự</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cats as $key): ?>
                        <tr data-id='<?= $key["id"] ?>'>
                            <td><?= $key["name"] ?></td>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-create">
                    <div class="form-group">
                        <label for="">Tên service</label>
                        <input type="text"
                            class="form-control" name="name" aria-describedby="helpId" placeholder="" required>
                    </div>
                    <div class="form-group">
                        <label for="">Số thứ tự</label>
                        <input type="number"
                            class="form-control" value="0" name="order" placeholder="">
                        <small id="helpId" class="form-text text-muted">Số càng cao thì càng được sắp xếp ưu tiên</small>
                    </div>
                    <div class="form-group">
                        <label for="">Mỗ tả 1</label>
                        <textarea class="form-control" name="desc" id="" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Mỗ tả 2</label>
                        <textarea class="form-control" name="end" id="" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Thời gian</label>
                        <input type="text"
                            class="form-control" name="time" id="" aria-describedby="helpId" placeholder="">
                        <small id="helpId" class="form-text text-muted">Ví dụ: 1 tiếng, 30 phút, 1 ngày...</small>
                    </div>

                    <div class="form-group">
                        <label for="">Giá thành</label>
                        <input type="text"
                            class="form-control" name="price" id="" aria-describedby="helpId" placeholder="">
                        <small id="helpId" class="form-text text-muted">Ví dụ: 100$, 300cent...</small>
                    </div>

                    <div class="form-group">
                        <label for="">Ảnh đi kèm (mỗi link cách 1 dòng)</label>
                        <textarea class="form-control" name="images" id="" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">URL Chuyển hướng (nếu có)</label>
                        <input type="text"
                            class="form-control" name="redirect_url" id="" aria-describedby="helpId" placeholder="">
                        <small id="helpId" class="form-text text-muted">Nếu không có, user khi bấm vào sẽ chuyển sang trang booking.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create</button>
                </form>
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
                <form id="form-edit">
                    <div class="form-group">
                        <label for="">Tên service</label>
                        <input type="text"
                            class="form-control" name="name" aria-describedby="helpId" placeholder="" required>
                    </div>
                    <div class="form-group">
                        <label for="">Số thứ tự</label>
                        <input type="number"
                            class="form-control" value="0" name="order" placeholder="">
                        <small id="helpId" class="form-text text-muted">Số càng cao thì càng được sắp xếp ưu tiên</small>
                    </div>
                    <div class="form-group">
                        <label for="">Mỗ tả 1</label>
                        <textarea class="form-control" name="desc" id="" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Mỗ tả 2</label>
                        <textarea class="form-control" name="end" id="" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Thời gian</label>
                        <input type="text"
                            class="form-control" name="time" id="" aria-describedby="helpId" placeholder="">
                        <small id="helpId" class="form-text text-muted">Ví dụ: 1 tiếng, 30 phút, 1 ngày...</small>
                    </div>

                    <div class="form-group">
                        <label for="">Giá thành</label>
                        <input type="text"
                            class="form-control" name="price" id="" aria-describedby="helpId" placeholder="">
                        <small id="helpId" class="form-text text-muted">Ví dụ: 100$, 300cent...</small>
                    </div>

                    <div class="form-group">
                        <label for="">Ảnh đi kèm (mỗi link cách 1 dòng)</label>
                        <textarea class="form-control" name="images" id="" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">URL Chuyển hướng (nếu có)</label>
                        <input type="text"
                            class="form-control" name="redirect_url" id="" aria-describedby="helpId" placeholder="">
                        <small id="helpId" class="form-text text-muted">Nếu không có, user khi bấm vào sẽ chuyển sang trang booking.</small>
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

    $(document).ready(function() {

        $(document).delegate("button[id='btn-delete']", "click", function() {
            let id = $(this).parent().parent().parent().data("id");
            if (confirm("Bạn có muốn xoá danh mục này: " + id)) {
                api({
                    data: {
                        action_type: "delete_service",
                        id
                    }
                });
            }
        })

        $(document).delegate("button[id='btn-edit']", "click", function() {
            let id = $(this).parent().parent().parent().data("id");

            api({
                data: {
                    action_type: "get_service",
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

                        $('#form-edit').find('textarea').each(function() {
                            var name = $(this).attr('name'); // Lấy name của input
                            if (data[name] && name != 'password') { // Kiểm tra xem JSON có key trùng với name không
                                $(this).text(data[name]); // Gán giá trị từ JSON vào input
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

            data.action_type = "create_service";

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

            data.action_type = "edit_service";
            data.id = current_edit_id;

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data
            });
        })

    });
</script>