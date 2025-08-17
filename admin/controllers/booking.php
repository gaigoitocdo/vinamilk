<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên người book</th>
                        <th>Nhân vật</th>
                        <th>Địa điểm</th>
                        <th>Dịch vụ</th>
                        <th>Status</th>
                        <th>Ngày tạo</th>
                        <th>Thời gian booking</th>
                        <th>Mã thành viên</th>
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
    function render_status(s)
    {
        if(s == 1) return `<span class="badge badge-danger">Đã phê duyệt</span>`;
        //if(s == 0) return `<span class="badge badge-success">Chờ phê </span>`;
        return "Unknown";
    }

    $(document).ready(function() {

        const dbtable = $("#dataTable").DataTable({
            ajax: {
                url: "<?= route("ajax/booking-history-db.php") ?>",
                dataSrc: 'data'
            },
            order: [[0, 'desc']],
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
                    data: 'character_name',
                    render: (data, type, row) => `<textarea>${data}</textarea>`,
                },
                {
                    data: 'location',
                    render: (data, type, row) => `<textarea>${data}</textarea>`,
                },
                {
                    data: 'service',
                },
                {
                    data: 'status',
                    render: (data, type, row) => render_status(data),
                },
                {
                    data: 'create_time',
                    render: (data, type, row) => `${moment.unix(data).format("Y-MM-DD   HH:mm:ss")}`,
                },
                {
                    data: 'time',
                    render: (data, type, row) => `${data}`,
                },
                {
                    data: 'member_code',
                    render: (data, type, row) => `${data}`,
                }
            ]
        });

    });
</script>