<?php

$db = Database::getInstance();

$data = $db->pdo_query("SELECT * FROM `vip_level` WHERE 1 ORDER BY `min` ASC");

?>

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
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button type="button" id="btn-create" class="btn btn-primary" data-toggle="modal" data-target="#createModel">Tạo level mới</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Mức nạp tối thiểu</th>
                        <th>Chú thích</th>
                        <th>Logo</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $key): ?>
                        <tr data-id='<?= $key["id"] ?>' data-level="<?= $key["level"] ?>" data-min="<?= $key["min"] ?>" data-desc="<?= $key["desc"] ?>" data-logo="<?= $key["logo"] ?>">
                            <td><?= $key["level"] ?></td>
                            <td><?= $key["min"] ?></td>
                            <td><textarea><?= $key["desc"] ?></textarea></td>
                            <td><img src="<?= $key["logo"] ?>" width="50"></td>
                            <td>
                                <div class="btn-group" role="group" aria-label="">
                                    <button type="button" id="btn-edit" class="btn btn-primary">Chỉnh sửa</button>
                                    <button type="button" id="btn-delete" class="btn btn-danger">Xoá</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Create Modal -->
<div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-create">
                    <div class="form-group">
                        <label for="">Tên cấp độ</label>
                        <input type="text"
                            class="form-control" name="level" required>
                        <small id="helpId" class="form-text text-muted">Ví dụ: VIP 1, VIP 2, SUPER...</small>
                    </div>

                    <div class="form-group">
                        <label for="">Chú thích</label>
                        <input type="text"
                            class="form-control" name="desc">
                        <small id="helpId" class="form-text text-muted">Ví dụ: VIP 1 sẽ nhận được quyền lợi...</small>
                    </div>

                    <div class="form-group">
                        <label for="">Tổng số tiền tối thiểu cần để đạt cấp</label>
                        <input type="number" step="0.1"
                            class="form-control" name="min" required>
                    </div>

                    <div class="form-group">
                        <label for="">Logo</label>
                        <input type="text"
                            class="form-control" name="logo">
                    </div>

                    <div id="createUpload"></div>
                    <button type="submit" class="btn btn-primary w-100">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-edit-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-edit">
                    <input type="hidden"
                        class="form-control" name="id">
                    <div class="form-group">
                        <label for="">Tên cấp độ</label>
                        <input type="text"
                            class="form-control" name="level" required>
                        <small id="helpId" class="form-text text-muted">Ví dụ: VIP 1, VIP 2, SUPER...</small>
                    </div>

                    <div class="form-group">
                        <label for="">Chú thích</label>
                        <input type="text"
                            class="form-control" name="desc">
                        <small id="helpId" class="form-text text-muted">Ví dụ: VIP 1 sẽ nhận được quyền lợi...</small>
                    </div>

                    <div class="form-group">
                        <label for="">Tổng số tiền tối thiểu cần để đạt cấp</label>
                        <input type="number" step="0.1"
                            class="form-control" name="min" required>
                    </div>

                    <div class="form-group">
                        <label for="">Logo</label>
                        <input type="text"
                            class="form-control" name="logo">
                    </div>

                    <div id="editUpload"></div>
                    <button type="submit" class="btn btn-primary w-100">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php admin_footer(); ?>

<script>
    var current_edit_id = -1;

    $(document).ready(function() {

        $("#createUpload").uploadFile({
            url: "<?= route("ajax/uploader.php") ?>",
            formData: {
                "type": "vip_badge"
            },
            fileName: "file",
            //multiple:true,
            returnType: "json",
            acceptFiles: "image/*",
            onSuccess: function(files, data, xhr, pd) {
                if (data.success) {
                    $("#form-create").find("input[name='logo']").val(data.data);
                } else {
                    toastr.error(data.message ?? "Lỗi khi upload file");
                }
                //$("#eventsmessage").html($("#eventsmessage").html() + "<br/>Success for: " + JSON.stringify(data));
            }
        });

        $("#editUpload").uploadFile({
            url: "<?= route("ajax/uploader.php") ?>",
            formData: {
                "type": "vip_badge"
            },
            fileName: "file",
            //multiple:true,
            returnType: "json",
            acceptFiles: "image/*",
            onSuccess: function(files, data, xhr, pd) {
                if (data.success) {
                    $("#form-edit").find("input[name='logo']").val(data.data);
                } else {
                    toastr.error(data.message ?? "Lỗi khi upload file");
                }
                //$("#eventsmessage").html($("#eventsmessage").html() + "<br/>Success for: " + JSON.stringify(data));
            }
        });

        $("#form-create").on("submit", function(e) {
            e.preventDefault();

            var data = {};

            data.action_type = "edit_vip_level";

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data
            });
        })

        $(document).delegate("button[id='btn-delete']", "click", function() {
            let id = $(this).parent().parent().parent().data("id");
            if (confirm("Bạn có muốn xoá mục này: " + id)) {
                api({
                    data: {
                        action_type: "delete_vip_level",
                        id
                    }
                });
            }
        })

        $(document).delegate("button[id='btn-edit']", "click", function() {
            let id = $(this).parent().parent().parent().data("id");
            let level = $(this).parent().parent().parent().data("level");
            let min = $(this).parent().parent().parent().data("min");
            let desc = $(this).parent().parent().parent().data("desc");
            let logo = $(this).parent().parent().parent().data("logo");

            let data = {
                id,
                level,
                min,
                desc,
                logo
            };

            $("#modal-edit-title").html("Edit: " + level);
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

            $("#editModel").modal('show');
        })

        $("#form-edit").on("submit", function(e) {
            e.preventDefault();

            var data = {};

            data.action_type = "edit_vip_level";
            data.id = current_edit_id;

            $(this).serializeArray().forEach(element => {
                data[element.name] = element.value;
            });

            api({
                data
            });
        })

    });
</script>