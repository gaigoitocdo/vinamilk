<style>
    :root {
        --dark-bg: #1a1d29;
        --dark-surface: #242938;
        --dark-card: #2a2f42;
        --dark-border: #363b4d;
        --dark-hover: #3a4052;
        --text-primary: #ffffff;
        --text-secondary: #b8bcc8;
        --text-muted: #8b92a5;
        --text-accent: #e2e8f0;
        --primary-color: #6366f1;
        --primary-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --border-radius: 8px;
        --border-radius-sm: 6px;
        --transition: all 0.2s ease;
        --shadow-lg-dark: 0 8px 16px rgba(0, 0, 0, 0.2);
        --shadow-dark: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    body {
        background: var(--dark-bg);
        color: var(--text-primary);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        font-size: 14px;
    }

    .content-area {
        padding: 1rem;
        background: var(--dark-bg);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .stat-card {
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius-sm);
        padding: 0.75rem;
        text-align: center;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-gradient);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-dark);
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-icon {
        font-size: 1.5rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        opacity: 0.8;
    }

  

    .card {
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg-dark);
        transition: var(--transition);
        margin-bottom: 1rem;
    }

    .card-header {
        background: var(--dark-card);
        border-bottom: 1px solid var(--dark-border);
        padding: 0.75rem 1rem;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
    }

    .card-body {
        padding: 1rem;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.25rem;
        font-weight: 500;
        color: var(--text-accent);
        font-size: 0.8rem;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
      
        border: 1px solid var(--dark-border);
        color: var(--text-primary);
        border-radius: var(--border-radius-sm);
        font-size: 0.8rem;
        transition: var(--transition);
    }

    .form-control:focus, .form-select:focus {
        outline: none;
   
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
       
    }

    .form-control::placeholder {
        color: var(--text-muted);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: var(--border-radius-sm);
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: var(--transition);
        white-space: nowrap;
    }

    .btn-primary {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .select-all-btn {
        background: var(--dark-hover);
        color: var(--text-primary);
        border: none;
        padding: 0.25rem 0.5rem;
        border-radius: var(--border-radius-sm);
        font-size: 0.7rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .select-all-btn:hover {
        background: var(--primary-color);
    }

    .preview-area {
        background: var(--dark-card);
        border-radius: var(--border-radius);
        padding: 1rem;
        border: 1px solid var(--dark-border);
        position: sticky;
        top: 1rem;
    }

    .preview-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .notification-preview {
        background: var(--dark-surface);
        border-radius: var(--border-radius-sm);
        padding: 0.75rem;
        border-left: 3px solid var(--primary-color);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .notification-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .notification-icon {
        margin-right: 0.5rem;
        font-size: 1rem;
        color: var(--primary-color);
    }

    .notification-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-primary);
    }

    .notification-content {
        color: var(--text-secondary);
        line-height: 1.4;
        margin-bottom: 0.5rem;
        font-size: 0.8rem;
    }

    .notification-time {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    .user-selection {
        margin-bottom: 1rem;
    }

    .user-search-container {
        margin-bottom: 0.5rem;
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .user-search {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius-sm);
        font-size: 0.75rem;
        background: var(--dark-card);
        color: var(--text-primary);
    }

    .user-controls {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .selected-count {
        font-size: 0.7rem;
        color: var(--text-secondary);
        padding: 0.25rem 0.5rem;
        background: var(--dark-card);
        border-radius: var(--border-radius-sm);
        border: 1px solid var(--dark-border);
    }

    .user-list-container {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid var(--dark-border);
        border-radius: var(--border-radius-sm);
        background: var(--dark-surface);
    }

    .user-checkbox {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-bottom: 1px solid var(--dark-border);
        transition: var(--transition);
        cursor: pointer;
    }

    .user-checkbox:hover {
        background: rgba(99, 102, 241, 0.05);
    }

    .user-checkbox.selected {
        background: rgba(99, 102, 241, 0.1);
        border-left: 3px solid var(--primary-color);
    }

    .user-info {
        display: flex;
        align-items: center;
        flex-grow: 1;
        min-width: 0;
    }

    .user-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        margin-right: 0.5rem;
        font-size: 0.7rem;
        flex-shrink: 0;
    }

    .user-details {
        flex-grow: 1;
        min-width: 0;
    }

    .user-name {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.75rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email {
        font-size: 0.7rem;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-badges {
        display: flex;
        gap: 0.25rem;
        flex-shrink: 0;
    }

    .vip-badge {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: #333;
        padding: 0.125rem 0.375rem;
        border-radius: 8px;
        font-size: 0.6rem;
        font-weight: 600;
    }

    .status-badge {
        padding: 0.125rem 0.375rem;
        border-radius: 8px;
        font-size: 0.6rem;
        font-weight: 500;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.2);
        color: var(--success-color);
    }

    .status-inactive {
        background: rgba(239, 68, 68, 0.2);
        color: var(--danger-color);
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-bottom: 0.75rem;
    }

    .form-check input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--primary-color);
    }

    .form-check label {
        margin: 0;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 0.8rem;
    }

    .char-counter {
        color: var(--text-muted);
        font-size: 0.7rem;
        float: right;
        margin-top: 0.25rem;
    }

    .loading {
        text-align: center;
        padding: 1rem;
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    .no-users-found {
        text-align: center;
        padding: 1rem;
        color: var(--text-muted);
        font-style: italic;
        font-size: 0.8rem;
    }

    .api-status {
        position: fixed;
        top: 10px;
        right: 10px;
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 1000;
        background: #2a2f42;
        color: #f59e0b;
        border: 1px solid #f59e0b;
        cursor: pointer;
        transition: all 0.3s ease;
        max-width: 200px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .content-area {
            padding: 0.75rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .preview-area {
            position: static;
        }

        .api-status {
            position: relative;
            top: auto;
            right: auto;
            margin: 10px 0;
            display: block;
        }
    }
</style>
    
 <div class="content-area">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14"></path>
                </svg>
                Quản lý rút tiền
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-success" onclick="refreshTable()">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 4v6h6m16-2v6h-6m-10 1l3.59 3.59M17 13l-3.59-3.59"></path>
                    </svg>
                    Làm mới
                </button>
            </div>
        </div>
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
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalPreview" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem trước</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <img id="img-modal-preview" src="" alt="Preview" style="max-height: calc(100% - 50px); width: auto;">
            </div>
        </div>
    </div>
</div>

<?php admin_footer(); ?>
<style>
    .table tbody tr:hover {
        background-color: #edf2f7;
        transition: background-color 0.2s;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-weight: 500;
    }

    .badge-success {
        background-color: #2f855a;
        color: white;
    }

    .badge-warning {
        background-color: #d69e2e;
        color: white;
    }

    .badge-danger {
        background-color: #c53030;
        color: white;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-success {
        background-color: #2f855a;
        border-color: #2f855a;
    }

    .btn-success:hover {
        background-color: #276749;
        border-color: #276749;
    }

    .btn-danger {
        background-color: #c53030;
        border-color: #c53030;
    }

    .btn-danger:hover {
        background-color: #9b2c2c;
        border-color: #9b2c2c;
    }

    textarea {
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 8px;
        width: 100%;
        resize: vertical;
    }
</style>

<?php admin_footer(); ?>

<script>
   $(document).ready(function() {
    const dbtable = $("#dataTable").DataTable({
        ajax: {
            url: "<?= route('ajax/withdraw-history-db.php') ?>",
            dataSrc: 'data'
        },
        order: [[0, 'desc']],
        processing: true,
        serverSide: true,
        serverMethod: 'post',
        columns: [
            { data: 'id' },
            { data: 'username' },
            { 
              data: 'money'
            },
            { 
                data: 'desc',
                render: (data) => `<textarea class="form-control">${data || ''}</textarea>`
            },
            { 
                data: 'create_time',
                render: (data) => moment.unix(data).format("YYYY-MM-DD HH:mm:ss")
            },
            { 
                data: 'bankinfo',
                render: (data, type, row) => `${row.bankinfo}<br>${row.bankid}<br>${row.bankname}`
            },
            { 
                data: 'status',
                render: (data) => render_status(data)
            },
            { 
                data: 'id',
                render: (data, type, row) => {
                    if (row.status == 0) {
                        return `
                            <div class="btn-group">
                                <button type="button" id="btn-refuse" data-id="${data}" class="btn btn-danger">Từ chối</button>
                                <button type="button" id="btn-accept" data-id="${data}" data-money="${row.money}" data-user-id="${row.user_id}" class="btn btn-success">Chấp nhận</button>
                            </div>`;
                    } else if (row.status == 2) {
                        return `<textarea class="form-control">${row.desc || 'Từ chối'}</textarea>`;
                    }
                    return '';
                }
            }
        ]
    });

    function render_status(s) {
        if (s == 2) return `<span class="badge badge-danger">Từ chối</span>`;
        if (s == 1) return `<span class="badge badge-success">Đã duyệt</span>`;
        if (s == 0) return `<span class="badge badge-warning">Chờ duyệt</span>`;
        return 'Unknown';
    }

    $(document).on("click", "button[id='btn-refuse']", function() {
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
                    toastr.success("Từ chối thành công");
                    dbtable.ajax.reload();
                } else {
                    toastr.error(response.message || "Lỗi không xác định");
                }
            },
            onError: function() {
                toastr.error("Lỗi kết nối API");
            }
        });
    });

    $(document).on("click", "button[id='btn-accept']", function() {
        let id = $(this).data('id');
        let money = parseFloat($(this).data('money'));
        let userId = $(this).data('user-id');

        api({
            data: {
                action_type: "accept_withdraw",
                id,
                money,
                user_id: userId
            },
            onSuccess: function(response) {
                if (response.success) {
                    toastr.success("Chấp nhận rút tiền thành công");
                    dbtable.ajax.reload();
                } else {
                    toastr.error(response.message || "Lỗi không xác định");
                }
            },
            onError: function() {
                toastr.error("Lỗi kết nối API");
            }
        });
    });

    function refreshTable() {
        dbtable.ajax.reload();
        toastr.info("Đã làm mới dữ liệu");
    }
});
</script>