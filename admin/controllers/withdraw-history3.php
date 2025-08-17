<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên người rút</th>
                        <th>Số tiền</th>
                        <th>Mô tả</th>
                        <th>Ngày thực hiện</th>
                        <th>Thông tin rút tiền</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal" id="modalPreview" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-colorful">
                <h5 class="modal-title">Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="display: flex; justify-content: center;">
                <img style="height: calc(100% - 50px)" id="img-modal-preview" src="">
            </div>
        </div>
    </div>
</div>

<?php admin_footer(); ?>

<script>
    function render_status(s) {
        if (s == 2) return `<span class="badge badge-danger">Từ chối</span>`;
        if (s == 1) return `<span class="badge badge-success">Đã duyệt</span>`;
        if (s == 0) return `<span class="badge badge-warning">Chờ duyệt</span>`;
        return "Unknown";
    }

    $(document).ready(function() {
        const dbtable = $("#dataTable").DataTable({
            ajax: {
                url: "<?= route("ajax/withdraw-history-db.php") ?>",
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
                    data: 'money'
                },
                {
                    data: 'desc',
                    render: (data) => `<textarea>${data}</textarea>`
                },
                {
                    data: 'create_time',
                    render: (data, type, row) => `${moment.unix((data)).format("Y-MM-DD   HH:mm:ss")}`,
                },
                {
                    data: 'bankinfo',
                    render: (data, type, row) => `${row.bankinfo}<br>${row.bankid}<br>${row.bankname}`,
                },
                {
                    data: 'status',
                    render: (data, type, row) => render_status(data),
                },
                {
                    data: 'id',
                    render: (data, type, row) => {
                        if (row["status"] == 0) {
                            return `<div class="btn-group">
                            <button type="button" id="btn-refuse" data-id="${data}" class="btn btn-danger">Từ chối</button>
                            <button type="button" id="btn-accept" data-id="${data}" class="btn btn-success">Chấp nhận</button>
                        </div>`;
                        }
                        else if(row["status"] == 2)
                        {
                            return `<textarea>${row["desc"] ?? "Từ chối"}</textarea>`;
                        }
                        else return ""
                    },
                }
            ]
        });

        $(document).delegate("button[id='btn-refuse']", "click", function() {
            let id = $(this).data('id');
            let reason = prompt("Nhập lý do từ chối (có thể bỏ trống)");

            api({
                data: {
                    action_type: "refuse_withdraw",
                    id,
                    reason
                },
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Chỉnh sửa thành công");
                        dbtable.ajax.reload();
                    }
                    else
                    {
                        toastr.error(response.message ?? "Lỗi không xác định");
                    }
                }
            })
        })

        $(document).delegate("button[id='btn-accept']", "click", function() {
            let id = $(this).data('id');

            api({
                data: {
                    action_type: "accept_withdraw",
                    id
                },
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Chỉnh sửa thành công");
                        dbtable.ajax.reload();
                    }
                    else
                    {
                        toastr.error(response.message ?? "Lỗi không xác định");
                    }
                }
            })
        })
    });
</script>