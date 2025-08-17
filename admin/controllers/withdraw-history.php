<?php
// Include admin header if you have it
// admin_header();
?>

<!-- Add required CSS and JS libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>

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

<?php
// Include admin footer if you have it
// admin_footer();
?>

<script>
    // Configure toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };

    function render_status(s) {
        if (s == 2) return `<span class="badge badge-danger">Từ chối</span>`;
        if (s == 1) return `<span class="badge badge-success">Đã duyệt</span>`;
        if (s == 0) return `<span class="badge badge-warning">Chờ duyệt</span>`;
        return "Unknown";
    }

    // API helper function
    function api(options) {
        const defaultOptions = {
            url: '/ajax/withdraw-history-db.php',
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                // Show loading if needed
            },
            success: function(response) {
                if (options.onSuccess) {
                    options.onSuccess(response);
                }
            },
            error: function(xhr, status, error) {
                console.error('API Error:', error);
                toastr.error('Lỗi kết nối server: ' + error);
                if (options.onError) {
                    options.onError(xhr, status, error);
                }
            }
        };

        // Merge options
        const ajaxOptions = Object.assign({}, defaultOptions, options);
        
        // Make AJAX call
        $.ajax(ajaxOptions);
    }

    // Wait for document ready
    $(document).ready(function() {
        console.log('jQuery loaded successfully');
        
        const dbtable = $("#dataTable").DataTable({
            ajax: {
                url: "/ajax/withdraw-history-db.php",
                type: 'POST',
                dataSrc: function(json) {
                    if (json.error) {
                        console.error('DataTables error:', json.error);
                        toastr.error('Lỗi tải dữ liệu: ' + json.error);
                        return [];
                    }
                    return json.data || [];
                },
                error: function(xhr, error, thrown) {
                    console.error('AJAX error:', error, thrown);
                    toastr.error('Lỗi kết nối server');
                }
            },
            order: [
                [0, 'desc']
            ],
            processing: true,
            serverSide: true,
            serverMethod: 'post',
            pageLength: 25,
            language: {
                processing: "Đang xử lý...",
                search: "Tìm kiếm:",
                lengthMenu: "Hiển thị _MENU_ mục",
                info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                infoEmpty: "Hiển thị 0 đến 0 của 0 mục",
                infoFiltered: "(lọc từ _MAX_ mục)",
                paginate: {
                    first: "Đầu",
                    last: "Cuối",
                    next: "Tiếp",
                    previous: "Trước"
                },
                emptyTable: "Không có dữ liệu",
                zeroRecords: "Không tìm thấy kết quả nào"
            },
            columns: [
                {
                    data: 'id',
                    render: function(data, type, row) {
                        return '#' + data;
                    }
                },
                {
                    data: 'username',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
               {
                    data: 'money'
                },
                {
                    data: 'desc',
                    render: function(data, type, row) {
                        if (!data || data === 'null') return '';
                        return `<textarea readonly style="width:100%;border:none;background:transparent;resize:none;" rows="2">${data}</textarea>`;
                    }
                },
                {
                    data: 'create_time',
                    render: function(data, type, row) {
                        return moment.unix(data).format("DD/MM/YYYY HH:mm:ss");
                    }
                },
                {
                    data: 'bankinfo',
                    render: function(data, type, row) {
                        return `${row.bankinfo || ''}<br>${row.bankid || ''}<br>${row.bankname || ''}`;
                    }
                },
                {
                    data: 'status',
                    render: function(data, type, row) {
                        return render_status(data);
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        if (row.status == 0) {
                            return `<div class="btn-group">
                                <button type="button" class="btn btn-danger btn-sm btn-refuse" data-id="${data}">Từ chối</button>
                                <button type="button" class="btn btn-success btn-sm btn-accept" data-id="${data}">Chấp nhận</button>
                            </div>`;
                        }
                        else if (row.status == 2) {
                            return `<textarea readonly style="width:100%;border:none;background:transparent;resize:none;" rows="2">${row.desc || "Từ chối"}</textarea>`;
                        }
                        else return '<small class="text-success">Đã duyệt</small>';
                    }
                }
            ]
        });

        // Handle refuse button click
        $(document).on('click', '.btn-refuse', function() {
            let id = $(this).data('id');
            let reason = prompt("Nhập lý do từ chối (có thể bỏ trống):");

            if (reason === null) return; // User cancelled

            api({
                data: {
                    action_type: "refuse_withdraw",
                    id: id,
                    reason: reason
                },
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Đã từ chối yêu cầu rút tiền");
                        dbtable.ajax.reload();
                    }
                    else {
                        toastr.error(response.message || "Lỗi không xác định");
                    }
                }
            });
        });

        // Handle accept button click
        $(document).on('click', '.btn-accept', function() {
            let id = $(this).data('id');

            if (!confirm('Bạn có chắc chắn muốn chấp nhận yêu cầu rút tiền này?')) {
                return;
            }

            api({
                data: {
                    action_type: "accept_withdraw",
                    id: id
                },
                onSuccess: function(response) {
                    if (response.success) {
                        toastr.success("Đã chấp nhận yêu cầu rút tiền");
                        dbtable.ajax.reload();
                    }
                    else {
                        toastr.error(response.message || "Lỗi không xác định");
                    }
                }
            });
        });
    });
</script>
<style>
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
    </style>