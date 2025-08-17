<!-- FILE: lottery-history.php -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên người dùng</th>
                        <th>Tên</th>
                        <th>Phiên</th>
                        <th>Lựa chọn</th>
                        <th>Tiền cược</th>
                        <th>Trạng thái</th>
                        <th>Kết quả</th>
                        <th>Ngày cược</th>
                        <th>Trước khi cược</th>
                        <th>Sau khi cược</th>
                        <th>Tỉ lệ cược</th>
                        <th>Mã thắng</th>
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
        if (s == 1) return `<span class="badge badge-success">Đã quyết toán</span>`;
        if (s == 0) return `<span class="badge badge-danger">Chưa quyết toán</span>`;
        return "Unknown";
    }

    function render_iswin(s) {
        if (s == 1) return `<span class="badge badge-success">Thắng</span>`;
        if (s == 2) return `<span class="badge badge-danger">Thua</span>`;
        return `<span class="badge badge-warning">Chưa mở</span>`;
    }

    // ✅ Hàm render mã số 5 chữ số thay vì ABCD
    function render_result_code(code) {
        if (!code) return '<span class="text-muted">Chưa có</span>';
        if (code.length === 5 && /^\d{5}$/.test(code)) {
            return `<span class="badge badge-info" style="font-size: 12px; letter-spacing: 1px; font-family: monospace;">${code}</span>`;
        }
        return code; // Fallback cho format cũ
    }

    // ✅ Hàm render lựa chọn của người dùng (mã số thay vì ABCD)
    function render_user_choice(choice) {
        // Nếu là mã số 5 chữ số
        if (choice && choice.length === 5 && /^\d{5}$/.test(choice)) {
            return `<span class="badge badge-secondary" style="font-family: monospace; letter-spacing: 1px;">${choice}</span>`;
        }
        // Nếu vẫn là format cũ ABCD
        if (choice && /^[A-D]$/i.test(choice)) {
            return `<span class="badge badge-secondary">${choice.toUpperCase()}</span>`;
        }
        return choice || 'N/A';
    }

    // ✅ Hàm kiểm tra thắng/thua dựa trên mã số
    function check_win_status(userChoice, winningCode) {
        if (!userChoice || !winningCode) return 0; // Chưa mở
        
        // Nếu cả hai đều là mã số 5 chữ số
        if (userChoice.length === 5 && winningCode.length === 5) {
            return userChoice === winningCode ? 1 : 2; // 1: Thắng, 2: Thua
        }
        
        // Logic cũ cho ABCD (giữ để tương thích)
        if (/^[A-D]$/i.test(userChoice)) {
            // Logic xử lý ABCD dựa trên mã thắng...
            return 0; // Placeholder
        }
        
        return 0;
    }

    $(document).ready(function() {
        const dbtable = $("#dataTable").DataTable({
            ajax: {
                url: "<?= route("ajax/lottery-history-db.php") ?>",
                dataSrc: 'data'
            },
            order: [[0, 'desc']],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            columns: [
                {
                    data: 'id',
                },
                {
                    data: 'username'
                },
                {
                    data: 'lottery_name'
                },
                {
                    data: 'sid',
                },
                {
                    // ✅ Cột lựa chọn - hiển thị mã số thay vì ABCD
                    data: 'type',
                    render: (data, type, row) => {
                        return render_user_choice(data);
                    }
                },
                {
                    data: 'money',
                    render: (data, type, row) => {
                        return new Intl.NumberFormat('vi-VN').format(data) + ' VNĐ';
                    }
                },
                {
                    data: 'status',
                    render: (data, type, row) => render_status(data),
                },
                {
                    data: 'is_win',
                    render: (data, type, row) => render_iswin(data),
                },
                {
                    data: 'create_time',
                    render: (data, type, row) => `${moment.unix((data)).format("Y-MM-DD HH:mm:ss")}`,
                },
                {
                    data: 'before_betting',
                    render: (data, type, row) => {
                        return new Intl.NumberFormat('vi-VN').format(data) + ' VNĐ';
                    }
                },
                {
                    data: 'after_betting',
                    render: (data, type, row) => {
                        return new Intl.NumberFormat('vi-VN').format(data) + ' VNĐ';
                    }
                },
                {
                    data: 'proportion',
                    render: (data, type, row) => `x${data}`
                },
                {
                    // ✅ Cột mã thắng - hiển thị mã số 5 chữ số
                    data: 'winning_code',
                    render: (data, type, row) => render_result_code(data),
                    title: 'Mã thắng'
                }
            ]
        });
    });
</script>