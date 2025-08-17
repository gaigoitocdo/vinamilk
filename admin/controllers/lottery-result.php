<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Số phiên</th>
                        <th>Chu kỳ</th>
                        <th>Kết quả</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thời gian kế tiếp</th>
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
    function render_status(s) {
        if(s == 1) return `<span class="badge badge-danger">Chưa mở</span>`;
        if(s == 0) return `<span class="badge badge-success">Đã mở</span>`;
        return "Unknown";
    }

    // ✅ NEW: Function to render 5-digit result with winner info
    function render_result_with_winner(result, winner_option) {
        if (!result) return '<span class="text-muted">Chưa có</span>';
        
        let html = '';
        if (result.length === 5 && /^\d{5}$/.test(result)) {
            html += `<div><span class="badge badge-primary" style="font-size: 14px; letter-spacing: 1px;">${result}</span></div>`;
            if (winner_option) {
                html += `<small class="text-success">Thắng: <strong>${winner_option}</strong></small>`;
            }
        } else {
            html = result; // Fallback for old format
        }
        return html;
    }

    $(document).ready(function() {
        const dbtable = $("#dataTable").DataTable({
            ajax: {
                url: "<?= route("ajax/lottery-result-db.php") ?>",
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
                    data: 'lottery_name'
                },
                {
                    data: 'sid'
                },
                {
                    data: 'rule',
                    render: (data, type, row) => `${data} phút 1 kỳ`,
                },
                {
                    data: 'result',
                    render: (data, type, row) => {
                        // ✅ NEW: Display 5-digit code with winner info
                        return render_result_with_winner(data, row.winner_option);
                    },
                },
                {
                    data: 'status',
                    render: (data, type, row) => render_status(data),
                },
                {
                    data: 'create_time',
                    render: (data, type, row) => `${moment.unix(data).format("Y-MM-DD HH:mm:ss")}`,
                },
                {
                    data: 'create_time',
                    render: (data, type, row) => `${moment.unix((data + row["rule"] * 60)).format("Y-MM-DD HH:mm:ss")}`,
                }
            ]
        });
    });
</script>