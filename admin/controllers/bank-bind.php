<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Bank ID</th>
                        <th>Bank Info</th>
                        <th>Bank Name</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-edit">
                    <input type="hidden" class="form-control" name="id">
                    <div class="form-group">
                        <label for="">BANK ID</label>
                        <input type="text" class="form-control" name="bankid">
                    </div>
                    <div class="form-group">
                        <label for="">BANK INFO</label>
                        <input type="text" class="form-control" name="bankinfo">
                    </div>
                    <div class="form-group">
                        <label for="">BANK NAME</label>
                        <input type="text" class="form-control" name="name">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php admin_footer(); ?>

<script>
    $(document).ready(function() {

        const dbtable = $("#dataTable").DataTable({
            ajax: {
                url: "<?= route("ajax/bank-bind-db.php") ?>",
                dataSrc: 'data'
            },
            order: [
                [0, 'desc']
            ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            columns: [{
                    data: 'id',
                },
                {
                    data: 'username'
                },
                {
                    data: 'bankid'
                },
                {
                    data: 'bankinfo',
                },
                {
                    data: 'name',
                },
                {
                    data: 'create_time',
                    render: (data, type, row) => `${moment.unix(data).format("Y-MM-DD   HH:mm:ss")}`,
                },
                {
                    data: 'id',
                    render: (data, type, row) => `<div class="btn-group">
                            <button type="button" id="btn-edit" data-id="${data}" class="btn btn-primary">Edit</button>
                            <button type="button" id="btn-delete" data-id="${data}" class="btn btn-danger">Delete</button>
                        </div>`,
                },
            ]
        });

        $(document).delegate("#btn-edit", "click", function() {
            let id = $(this).data("id");

            api({
                data: {
                    action_type: "get_bank_bind",
                    id
                },
                onSuccess: (r) => {
                    if (r.success) {
                        let {
                            data
                        } = r;
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

                        $("#editModal").modal('show');
                    } else {
                        toastr.error(r.message ?? "Lỗi không xác định");
                    }
                }
            })
        })

        $(document).delegate("#btn-delete", "click", function() {
            let id = $(this).data("id");

            if (confirm("bạn có muốn xoá mục này: " + id)) {
                api({
                    data: {
                        action_type: "delete_bank_bind",
                        id
                    },
                    onSuccess: function(response) {
                        if (response.success) {
                            toastr.success("Xoá thành công");
                            dbtable.ajax.reload();
                        } else {
                            toastr.error(response.message ?? "Lỗi không xác định");
                        }
                    }
                });
            }
        })

        $("#form-edit").on("submit", function(e) {
            e.preventDefault();

            var data = {};

            data.action_type = "edit_bank_bind";

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data,
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Chỉnh sửa thành công");
                        $("#editModal").modal("hide");
                        dbtable.ajax.reload();
                    } else {
                        toastr.error(response.message ?? "Lỗi không xác định");
                    }
                }
            });
        })

    });
</script>