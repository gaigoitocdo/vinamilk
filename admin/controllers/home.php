<?php

include __DIR__ . "/../../models/LotteryModel.php";

$cats = LotteryModel::GetAllCategories();

$items = LotteryModel::GetAll();


?>


<div class="card shadow mb-4">
    <div class="card-body">
        <button id="btn-create-invitecode" type="button" class="btn btn-primary">Thêm mã mời đăng ký</button>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã mời</th>
                        <th>Sử dụng</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php admin_footer(); ?>


<script>
    $(document).ready(function() {

        function render_status(s) {
            if (s.max_count <= s.used_count) return `<span class="badge badge-danger">Hết lượt</span>`;
            else return `<span class="badge badge-danger">Còn lượt</span>`;
            //if(s == 0) return `<span class="badge badge-success">Chờ phê </span>`;
            return "Unknown";
        }

        const dbtable = $("#dataTable").DataTable({
            ajax: {
                url: "<?= route("ajax/invitecode-db.php") ?>",
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
                    data: 'invite_id'
                },
                {
                    data: 'used_count',
                    render: (data, _, row) => `${data}/${row.max_count}`
                },
                {
                    data: 'used_count',
                    render: (data, type, row) => render_status(row),
                }
            ]
        });

        $("#btn-create-invitecode").click(function() {
            let invite = prompt("Nhập mã mời tuỳ chỉnh của bạn (để trống sẽ tạo mã mời ngẫu nhiên 6 chữ số)");
            let max_use = prompt("Nhập số lượt dùng của mã, mặc định là 1");

            api({
                data: {
                    action_type: "create-invitecode",
                    invite,
                    max_use
                },
                onSuccess: (e) => {
                    if (e.success) {
                        toastr.success("Tạo thành công");
                        dbtable.ajax.reload();
                    } else {
                        toastr.error(e.message ?? "Tạo thất bại");
                    }
                }
            })
        })
    });
</script>