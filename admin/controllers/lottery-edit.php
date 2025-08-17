<?php

include_once __DIR__ . "/../../models/LotteryModel.php";

$a = LotteryModel::GetAll();

?>

<div class="card shadow mb-4">
    <div class="card-body">

        <form class="form-inline">
            <div class="form-group">
                <label for="">Tên xở số</label>
                <select class="form-control ml-2" id="select-current-lottery">
                    <?php foreach ($a as $k): ?>
                        <option value="<?= $k["key"] ?>"><?= $k["name"] ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="btn-search" class="btn btn-primary ml-2">Tìm kiếm</button>
            </div>
        </form>

        <button class="btn btn-warning" id="btn-refresh">Làm mới</button>

        <div class="table-responsive mt-3">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tên sổ xố</th>
                        <th>Mã định danh</th>
                        <th>Số kỳ</th>
                        <th>Kết quả dự kiến</th>
                        <th>Người thực hiện</th>
                        <th>Bắt đầu</th>
                        <th>Thời gian cập nhật</th>
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
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa kết quả</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Mã kết quả</label>
                    <input type="text" 
                           class="form-control" 
                           id="txt-result-code" 
                           placeholder="Nhập mã 5 chữ số (VD: 12345)"
                           maxlength="5"
                           pattern="\d{5}">
                    <small class="form-text text-muted">
                        Nhập mã gồm 5 chữ số. Hệ thống sẽ tự động xác định người thắng dựa trên mã này.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" id="btn-save" class="btn btn-primary">Lưu</button>
            </div>
        </div>
    </div>
</div>


<?php admin_footer(); ?>

<script>
    function render_status(s) {
        if (s == 1) return `<span class="badge badge-danger">Chưa mở</span>`;
        if (s == 0) return `<span class="badge badge-success">Đã mở</span>`;
        return "Unknown";
    }

   // ✅ FIXED: Admin Frontend JavaScript
$(document).ready(function() {
    // Initialize DataTable with proper error handling
    const dbtable = $("#dataTable").DataTable({
        ajax: {
            url: "<?= route('ajax/admin.php') ?>",
            type: "POST",
            data: function(d) {
                d.action_type = 'get_lottery_edit';
                d.key = $("#select-current-lottery").val();
            },
            dataSrc: function(json) {
                // Error handling
                if (!json.success) {
                    toastr.error(json.message || "Lỗi tải dữ liệu");
                    return [];
                }
                return json.data || [];
            },
            error: function(xhr, error, thrown) {
                console.error("DataTable AJAX Error:", error, thrown);
                toastr.error("Lỗi kết nối server");
            }
        },
        processing: true,
        serverSide: false,
        pageLength: 25,
        order: [[2, 'asc']], // Sort by session
        columns: [
            {
                data: 'name',
                title: 'Tên sổ xố'
            },
            {
                data: 'key',
                title: 'Mã định danh'
            },
            {
                data: 'session',
                title: 'Số kỳ',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return `<span class="badge badge-info">${data}</span>`;
                    }
                    return data;
                }
            },
            {
                data: 'result',
                title: 'Kết quả dự kiến',
                render: function(data, type, row) {
                    if (!data) {
                        return '<span class="text-muted">Chưa chỉnh sửa</span>';
                    }
                    
                    // Validate 5-digit code
                    if (data.length === 5 && /^\d{5}$/.test(data)) {
                        return `<span class="badge badge-primary" style="font-size: 14px; letter-spacing: 1px;">${data}</span>`;
                    }
                    
                    // Fallback for invalid format
                    return `<span class="badge badge-warning">${data}</span>`;
                }
            },
            {
                data: 'editor',
                title: 'Người thực hiện',
                render: function(data, type, row) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'create_time',
                title: 'Bắt đầu',
                render: function(data, type, row) {
                    if (data && moment.unix) {
                        return moment.unix(data).format("DD/MM/YYYY HH:mm:ss");
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'edit_update_time',
                title: 'Thời gian cập nhật',
                render: function(data, type, row) {
                    if (data && moment.unix) {
                        return moment.unix(data).format("DD/MM/YYYY HH:mm:ss");
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            {
                data: null,
                title: 'Hành động',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm" role="group" 
                             data-key='${row.key}' 
                             data-session='${row.session}' 
                             data-result='${row.result || ""}'>
                            <button type="button" class="btn btn-success btn-edit" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <button type="button" class="btn btn-danger btn-cancel" title="Xóa kết quả">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Vietnamese.json"
        }
    });

    // Refresh button
    $("#btn-refresh").click(function(e) {
        e.preventDefault();
        dbtable.ajax.reload(null, false); // Keep current page
        toastr.info("Đã làm mới dữ liệu");
    });

    // Search button
    $("#btn-search").click(function(e) {
        e.preventDefault();
        dbtable.ajax.reload();
        toastr.info("Đang tìm kiếm...");
    });

    // Auto refresh every 30 seconds
    setInterval(function() {
        dbtable.ajax.reload(null, false);
    }, 30000);

    // Save button with validation
    $("#btn-save").click(function() {
        const key = $(this).data("key");
        const session = $(this).data("session");
        const result_code = $("#txt-result-code").val().trim();

        // Validation
        if (!key || !session) {
            toastr.error("Thiếu thông tin key hoặc session");
            return;
        }

        if (!result_code) {
            toastr.error("Vui lòng nhập mã kết quả");
            $("#txt-result-code").focus();
            return;
        }

        if (!/^\d{5}$/.test(result_code)) {
            toastr.error("Mã kết quả phải là 5 chữ số (VD: 12345)");
            $("#txt-result-code").focus().select();
            return;
        }

        // Disable button to prevent double click
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');

        // API call with proper error handling
        $.ajax({
            url: "<?= route('ajax/admin.php') ?>",
            type: "POST",
            data: {
                action_type: "update_lottery_edit",
                key: key,
                session: session,
                result: result_code
            },
            dataType: "json",
            timeout: 10000,
            success: function(response) {
                if (response && response.success) {
                    toastr.success("Lưu kết quả thành công!");
                    dbtable.ajax.reload(null, false);
                    $("#modalEdit").modal("hide");
                } else {
                    toastr.error(response.message || "Lưu thất bại");
                }
            },
            error: function(xhr, status, error) {
                console.error("Save error:", status, error);
                let errorMsg = "Lỗi kết nối server";
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (status === "timeout") {
                    errorMsg = "Timeout - vui lòng thử lại";
                }
                
                toastr.error(errorMsg);
            },
            complete: function() {
                // Re-enable button
                $("#btn-save").prop('disabled', false).html('Lưu');
            }
        });
    });

    // Edit button handler
    $(document).on("click", ".btn-edit", function() {
        const $btnGroup = $(this).closest('.btn-group');
        const key = $btnGroup.data("key");
        const session = $btnGroup.data("session");
        const result = $btnGroup.data("result");

        // Set modal data
        $("#btn-save").data("key", key).data("session", session);

        // Pre-fill result if exists
        if (result && result.length === 5 && /^\d{5}$/.test(result)) {
            $("#txt-result-code").val(result);
        } else {
            $("#txt-result-code").val('');
        }

        // Show modal and focus input
        $("#modalEdit").modal("show");
        setTimeout(() => {
            $("#txt-result-code").focus().select();
        }, 500);
    });

    // Delete button handler
    $(document).on("click", ".btn-cancel", function() {
        const $btnGroup = $(this).closest('.btn-group');
        const key = $btnGroup.data("key");
        const session = $btnGroup.data("session");

        if (!confirm("Bạn có chắc chắn muốn xóa kết quả này?\nHành động này không thể hoàn tác.")) {
            return;
        }

        // Disable button
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "<?= route('ajax/admin.php') ?>",
            type: "POST",
            data: {
                action_type: "delete_lottery_edit",
                key: key,
                session: session
            },
            dataType: "json",
            timeout: 10000,
            success: function(response) {
                if (response && response.success) {
                    toastr.success("Xóa kết quả thành công!");
                    dbtable.ajax.reload(null, false);
                } else {
                    toastr.error(response.message || "Xóa thất bại");
                }
            },
            error: function(xhr, status, error) {
                console.error("Delete error:", status, error);
                let errorMsg = "Lỗi kết nối server";
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                toastr.error(errorMsg);
            },
            complete: function() {
                // Re-enable all delete buttons
                $(".btn-cancel").prop('disabled', false).html('<i class="fas fa-trash"></i> Xóa');
            }
        });
    });

    // Input validation in modal
    $("#txt-result-code").on("input", function() {
        const value = $(this).val();
        const isValid = /^\d{0,5}$/.test(value);
        
        if (!isValid) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
            $(this).after('<div class="invalid-feedback">Chỉ được nhập số, tối đa 5 chữ số</div>');
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Auto-refresh lottery select change
    $("#select-current-lottery").on("change", function() {
        dbtable.ajax.reload();
    });
});
</script>