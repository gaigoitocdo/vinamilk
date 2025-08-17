<?php
// enhanced-topup-admin.php - Cải thiện trang admin với sửa lỗi
include_once __DIR__ . "/../../models/TopupModel.php";

$topups = TopupModel::GetAll() ?: []; // Ngăn lỗi nếu không có dữ liệu
?>

<style>
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    background-color: #F8F9FA;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.card-header {
    background-color: #4E73DF;
    color: #FFFFFF;
    border-radius: 12px 12px 0 0 !important;
    border: none;
    padding: 15px;
}

.btn-primary {
    background-color: #4E73DF;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #3B5BDC;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
}

.btn-success {
    background-color: #28A745;
    border: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background-color: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
}

.btn-danger {
    background-color: #DC3545;
    border: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    background-color: #C82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
}

.table {
    border-radius: 8px;
    overflow: hidden;
    background-color: #FFFFFF;
}

.table thead th {
    background-color: #4E73DF;
    color: #FFFFFF;
    border: none;
    font-weight: 600;
    padding: 15px;
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background-color: #F1F3F5;
    transform: scale(1.01);
}

.table tbody td {
    padding: 15px;
    border-bottom: 1px solid #E9ECEF;
    vertical-align: middle;
}

.form-control {
    border-radius: 8px;
    border: 2px solid #E9ECEF;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #4E73DF;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.metadata-section {
    background-color: #F1F3F5;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
    border: 2px dashed #4E73DF;
}

.metadata-title {
    color: #4E73DF;
    font-weight: 600;
    margin-bottom: 15px;
}

.btn-group {
    gap: 5px;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background-color: #17A2B8;
    color: #FFFFFF;
}

.status-inactive {
    background-color: #DC3545;
    color: #FFFFFF;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #F3F3F3;
    border-top: 4px solid #4E73DF;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="card shadow mb-4 animate-fade-in">
    <div class="card-header py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-credit-card me-2"></i>
                Quản lý phương thức thanh toán
            </h6>
            <button type="button" id="btn-create-topup" class="btn btn-primary icon-button">
                <i class="fas fa-plus"></i>
                Tạo phương thức mới
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-2"></i>ID</th>
                        <th><i class="fas fa-tag me-2"></i>Tên phương thức</th>
                        <th><i class="fas fa-info-circle me-2"></i>Mô tả</th>
                        <th><i class="fas fa-toggle-on me-2"></i>Trạng thái</th>
                        <th><i class="fas fa-cogs me-2"></i>Hành động</th>
                    </tr>
                </thead>
                <tbody id="topup-methods-tbody">
                    <?php foreach ($topups as $key): ?>
                        <tr data-id='<?= htmlspecialchars($key["id"] ?? '') ?>' class="metadata-row">
                            <td>
                                <strong>#<?= htmlspecialchars($key["id"] ?? 'N/A') ?></strong>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($key["name"] ?? 'Chưa đặt tên') ?></strong>
                            </td>
                            <td>
                                <span class="text-muted">
                                    <?= htmlspecialchars($key["display_name"] ?? 'Chưa có mô tả') ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?= isset($key["status"]) && $key["status"] ? 'status-active' : 'status-inactive' ?>">
                                    <?= isset($key["status"]) && $key["status"] ? 'Hoạt động' : 'Tạm dừng' ?>
                                </span>
                            </td>
                            <td class="table-actions">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary btn-sm icon-button btn-edit" 
                                            data-id="<?= htmlspecialchars($key["id"] ?? '') ?>" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm icon-button btn-preview" 
                                            data-id="<?= htmlspecialchars($key["id"] ?? '') ?>" title="Xem trước">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm icon-button btn-delete" 
                                            data-id="<?= htmlspecialchars($key["id"] ?? '') ?>" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="edit_topup_info" class="card shadow mb-4" style="display: none;">
    <div class="card-header py-3" style="background-color: #4E73DF;">
        <h6 id="edit_topup_info_title" class="m-0 font-weight-bold text-white">
            <i class="fas fa-edit me-2"></i>Chỉnh sửa thông tin
        </h6>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label fw-bold">
                        <i class="fas fa-tag me-2"></i>Tên phương thức
                    </label>
                    <input type="text" class="form-control" id="form-edit-name" placeholder="Nhập tên phương thức">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Mô tả hiển thị
                    </label>
                    <input type="text" class="form-control" id="form-edit-desc" placeholder="Mô tả cho người dùng">
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <button type="button" id="btn-save-info" class="btn btn-success icon-button">
                <i class="fas fa-save"></i>
                Lưu thông tin cơ bản
            </button>
        </div>

        <div class="metadata-section">
            <h6 class="metadata-title">
                <i class="fas fa-database me-2"></i>Thông tin thanh toán chi tiết
            </h6>
            <p class="text-muted mb-3">Các thông tin này sẽ hiển thị cho người dùng khi thanh toán</p>
            
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead style="background-color: #E9ECEF;">
                        <tr>
                            <th><i class="fas fa-key me-2"></i>Tên trường</th>
                            <th><i class="fas fa-edit me-2"></i>Giá trị</th>
                            <th><i class="fas fa-cogs me-2"></i>Hành động</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-center">
                                <button type="button" id="btn-meta-add" class="btn btn-success w-100 icon-button">
                                    <i class="fas fa-plus"></i>
                                    Thêm thông tin mới
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody id="tbl_meta">
                        <!-- Metadata rows will be populated here -->
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info mt-3">
                <h6><i class="fas fa-lightbulb me-2"></i>Gợi ý các trường thông tin:</h6>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li><strong>account_number:</strong> Số tài khoản</li>
                            <li><strong>account_holder:</strong> Tên chủ tài khoản</li>
                            <li><strong>bank_name:</strong> Tên ngân hàng</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="mb-0">
                            <li><strong>phone:</strong> Số điện thoại</li>
                            <li><strong>qr_code:</strong> Link ảnh QR</li>
                            <li><strong>note:</strong> Ghi chú thêm</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <button type="button" class="btn btn-secondary" onclick="hideEditSection()">
                <i class="fas fa-times me-2"></i>Đóng
            </button>
            <button type="button" id="btn-test-payment" class="btn btn-info" style="background-color: #17A2B8;">
                <i class="fas fa-eye me-2"></i>Xem trước giao diện user
            </button>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #F8F9FA;">
            <div class="modal-header" style="background-color: #4E73DF; color: #FFFFFF;">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Xem trước giao diện người dùng
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be populated here -->
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="spinner"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Enhanced JavaScript for existing functionality (unchanged, only styling adjustments)
var current_topup_id = 0;

$(document).ready(function() {
    $("#btn-create-topup").on('click', function() {
        Swal.fire({
            title: 'Tạo phương thức thanh toán mới',
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên phương thức:</label>
                        <input type="text" id="swal-name" class="form-control" placeholder="VD: Hana Bank, MoMo, ZaloPay">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả hiển thị:</label>
                        <input type="text" id="swal-desc" class="form-control" placeholder="VD: Chuyển khoản Hana Bank">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-plus"></i> Tạo',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#4E73DF',
            preConfirm: () => {
                const name = document.getElementById('swal-name').value;
                const desc = document.getElementById('swal-desc').value;
                
                if (!name.trim()) {
                    Swal.showValidationMessage('Vui lòng nhập tên phương thức');
                    return false;
                }
                
                return { name: name.trim(), display_name: desc.trim() };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                createTopupMethod(result.value.name, result.value.display_name);
            }
        });
    });

    function createTopupMethod(name, display_name) {
        Swal.fire({
            title: 'Đang tạo...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            type: "POST",
            url: "<?= route('ajax/admin.php') ?>",
            data: {
                action_type: "create_topup",
                name,
                display_name
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: 'Tạo phương thức thanh toán thành công',
                        confirmButtonColor: '#4E73DF'
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: response.message || 'Có lỗi xảy ra'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Không thể kết nối đến server'
                });
            }
        });
    }

    $("#btn-save-info").on("click", function() {
        let topup_id = $("#form-edit-name").data("tid");
        let name = $("#form-edit-name").val().trim();
        let display_name = $("#form-edit-desc").val().trim();
        
        if (!name) {
            toastr.error("Tên phương thức không được để trống");
            $("#form-edit-name").focus();
            return;
        }

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');

        $.ajax({
            type: "POST",
            url: "<?= route('ajax/admin.php') ?>",
            data: {
                action_type: "update_topup_info",
                topup_id,
                name,
                display_name
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    toastr.success("Cập nhật thành công!");
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    toastr.error(response.message || "Có lỗi xảy ra");
                    $("#btn-save-info").prop('disabled', false).html('<i class="fas fa-save"></i> Lưu thông tin cơ bản');
                }
            },
            error: function() {
                toastr.error("Không thể kết nối đến server");
                $("#btn-save-info").prop('disabled', false).html('<i class="fas fa-save"></i> Lưu thông tin cơ bản');
            }
        });
    });

    $(document).delegate("button[id='btn-edit']", 'click', function() {
        let id = $(this).parent().parent().parent().attr("data-id");
        
        $(this).html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        $.ajax({
            type: "POST",
            url: "<?= route('ajax/index.php') ?>",
            data: {
                action_type: "get_one_topup",
                id
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    let { data } = response;
                    current_topup_id = id;
                    
                    $("#edit_topup_info_title").html(`<i class="fas fa-edit me-2"></i>Chỉnh sửa: ${data.name}`);
                    $("#form-edit-name").val(data.name).attr("data-tid", id);
                    $("#form-edit-desc").val(data.display_name || data.desc || '');

                    populateMetadataTable(data.metadata || []);
                    $("#edit_topup_info").show();
                    $('html, body').animate({
                        scrollTop: $("#edit_topup_info").offset().top - 100
                    }, 500);
                } else {
                    toastr.error(response.message || "Không thể tải dữ liệu");
                }
            },
            error: function() {
                toastr.error("Không thể kết nối đến server");
            },
            complete: function() {
                $("button[id='btn-edit']").html('<i class="fas fa-edit"></i>').prop('disabled', false);
            }
        });
    });

    function populateMetadataTable(metadata) {
        let tbl = $("#tbl_meta");
        tbl.empty();
        
        metadata.forEach(o => {
            let html = `
                <tr data-parent='${o.topup_id}' data-id='${o.id}' class="metadata-row">
                    <td><strong>${o.meta_key}</strong></td>
                    <td>${o.meta_value}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" id="btn-meta-edit" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" id="btn-meta-delete" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbl.append(html);
        });
    }

    $(document).delegate("button[id='btn-delete']", 'click', function() {
        let topup_id = $(this).parent().parent().parent().attr("data-id");
        let name = $(this).parent().parent().parent().find('td:first').text().trim();
        
        Swal.fire({
            title: 'Xác nhận xóa',
            html: `Bạn có chắc muốn xóa phương thức <strong>"${name}"</strong>?<br><small class="text-danger">Hành động này không thể hoàn tác!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC3545',
            cancelButtonColor: '#6C757D',
            confirmButtonText: '<i class="fas fa-trash"></i> Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteTopupMethod(topup_id);
            }
        });
    });

    function deleteTopupMethod(topup_id) {
        Swal.fire({
            title: 'Đang xóa...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            type: "POST",
            url: "<?= route('ajax/admin.php') ?>",
            data: {
                action_type: "delete_topup",
                topup_id
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã xóa!',
                        text: 'Phương thức thanh toán đã được xóa',
                        confirmButtonColor: '#4E73DF'
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: response.message || 'Không thể xóa'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Không thể kết nối đến server'
                });
            }
        });
    }

    $(document).delegate("button[id='btn-preview']", 'click', function() {
        let id = $(this).parent().parent().parent().attr("data-id");
        showPreview(id);
    });

    function showPreview(topup_id) {
        $.ajax({
            type: "POST",
            url: "<?= route('ajax/index.php') ?>",
            data: {
                action_type: "get_one_topup",
                id: topup_id
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    let data = response.data;
                    let metadata = data.metadata || [];
                    
                    let previewHtml = generateUserPreview(data, metadata);
                    $('#previewContent').html(previewHtml);
                    $('#previewModal').modal('show');
                }
            }
        });
    }

    function generateUserPreview(data, metadata) {
        let metaInfo = '';
        metadata.forEach(meta => {
            metaInfo += `<div class="row mb-2">
                <div class="col-4"><strong>${meta.meta_key}:</strong></div>
                <div class="col-8">${meta.meta_value}</div>
            </div>`;
        });

        return `
            <div class="payment-option border rounded p-3" style="background-color: #FFFFFF;">
                <h5 class="text-primary mb-3">
                    <i class="fas fa-credit-card me-2"></i>
                    ${data.display_name || data.name}
                </h5>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Vui lòng thanh toán theo thông tin bên dưới và upload ảnh xác nhận
                </div>

                ${metadata.length > 0 ? `
                    <div class="card bg-light">
                        <div class="card-header" style="background-color: #E9ECEF;">
                            <strong>Thông tin thanh toán:</strong>
                        </div>
                        <div class="card-body">
                            ${metaInfo}
                        </div>
                    </div>
                ` : '<p class="text-muted">Chưa có thông tin thanh toán chi tiết</p>'}

                <div class="mt-3">
                    <label class="form-label">Số tiền cần nạp</label>
                    <input type="number" class="form-control" placeholder="Nhập số tiền" disabled>
                </div>

                <div class="mt-3">
                    <label class="form-label">Upload ảnh xác nhận</label>
                    <div class="border-dashed border-2 border-secondary rounded p-4 text-center text-muted">
                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                        <div>Kéo thả file hoặc click để chọn</div>
                    </div>
                </div>

                <button class="btn btn-success w-100 mt-3" disabled>
                    <i class="fas fa-check me-2"></i>Xác nhận thanh toán
                </button>
            </div>
        `;
    }
});
</script>

<?php admin_footer(); ?>