<?php
include_once __DIR__ . '/../../../config/database.php';
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;

    $data = [
        'title' => $_POST['title'] ?? '',
        'gia_cu' => $_POST['gia_cu'] ?? 0,
        'gia_sale' => $_POST['gia_sale'] ?? 0,
        'phan_tram_sale' => $_POST['phan_tram_sale'] ?? 0,
        'tong_so' => $_POST['tong_so'] ?? 0,
        'da_ban' => $_POST['da_ban'] ?? 0,
        'phan_tram_con_lai' => $_POST['phan_tram_con_lai'] ?? 0,
        'danh_gia_sao' => $_POST['danh_gia_sao'] ?? 0,
        'tu_khoa' => $_POST['tu_khoa'] ?? '',
    ];

    $upload_dir = __DIR__ . '/../../../uploads/';
    $hinh_anh = '';

    if (isset($_FILES['hinh_anh_file']) && $_FILES['hinh_anh_file']['error'] === 0) {
        $filename = uniqid() . '_' . basename($_FILES['hinh_anh_file']['name']);
        $path = $upload_dir . $filename;
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        if (move_uploaded_file($_FILES['hinh_anh_file']['tmp_name'], $path)) {
            $hinh_anh = 'uploads/' . $filename;
        }
    }

    if ($action === 'update' && !$hinh_anh && isset($_POST['hinh_anh_old'])) {
        $hinh_anh = $_POST['hinh_anh_old'];
    }

    $data['hinh_anh'] = $hinh_anh;

    if ($action === 'add') {
        $sql = "INSERT INTO san_pham_shop (title, hinh_anh, gia_cu, gia_sale, phan_tram_sale, tong_so, da_ban, phan_tram_con_lai, danh_gia_sao, tu_khoa)
                VALUES (:title, :hinh_anh, :gia_cu, :gia_sale, :phan_tram_sale, :tong_so, :da_ban, :phan_tram_con_lai, :danh_gia_sao, :tu_khoa)";
        $db->pdo_execute($sql, $data);
    } elseif ($action === 'update' && $id) {
        $sql = "UPDATE san_pham_shop SET title=:title, hinh_anh=:hinh_anh, gia_cu=:gia_cu, gia_sale=:gia_sale, phan_tram_sale=:phan_tram_sale,
                tong_so=:tong_so, da_ban=:da_ban, phan_tram_con_lai=:phan_tram_con_lai, danh_gia_sao=:danh_gia_sao, tu_khoa=:tu_khoa WHERE id=:id";
        $data['id'] = $id;
        $db->pdo_execute($sql, $data);
    } elseif ($action === 'delete' && $id) {
        $sql = "DELETE FROM san_pham_shop WHERE id=:id";
        $db->pdo_execute($sql, ['id' => $id]);
    }

    header("Location: admin-products.php");
    exit;
}

$products = $db->pdo_query("SELECT * FROM san_pham_shop ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm</title>
    <style>
        table { border-collapse: collapse; width: 100%; font-family: Arial; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        th { background: #f0f0f0; }
        input[type="text"], input[type="number"] { width: 100%; box-sizing: border-box; padding: 4px; }
        button { padding: 5px 10px; margin: 0 2px; }
        form.inline { display: inline; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        img { max-height: 60px; margin-top: 4px; }
    </style>
</head>
<body>

<h2>🛠️ Quản lý sản phẩm (Thêm / Sửa / Xóa)</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add">
    <table>
        <tr>
            <th>Tiêu đề</th>
            <th>Hình ảnh</th>
            <th>Giá cũ</th>
            <th>Giá sale</th>
            <th>% Sale</th>
            <th>Tổng số</th>
            <th>Đã bán</th>
            <th>% còn lại</th>
            <th>★ Đánh giá</th>
            <th>Từ khóa</th>
            <th>Thêm</th>
        </tr>
        <tr>
            <td><input type="text" name="title"></td>
            <td><input type="file" name="hinh_anh_file"></td>
            <td><input type="number" name="gia_cu"></td>
            <td><input type="number" name="gia_sale"></td>
            <td><input type="number" name="phan_tram_sale"></td>
            <td><input type="number" name="tong_so"></td>
            <td><input type="number" name="da_ban"></td>
            <td><input type="number" name="phan_tram_con_lai"></td>
            <td><input type="number" step="0.1" name="danh_gia_sao"></td>
            <td><input type="text" name="tu_khoa"></td>
            <td><button type="submit">➕ Lưu</button></td>
        </tr>
    </table>
</form>

<br>

<table>
    <tr>
        <th>Tiêu đề</th>
        <th>Hình ảnh</th>
        <th>Giá cũ</th>
        <th>Giá sale</th>
        <th>% Sale</th>
        <th>Tổng số</th>
        <th>Đã bán</th>
        <th>% còn lại</th>
        <th>★ Đánh giá</th>
        <th>Từ khóa</th>
        <th>Thao tác</th>
    </tr>
    <?php foreach ($products as $sp): ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $sp['id'] ?>">
            <input type="hidden" name="hinh_anh_old" value="<?= $sp['hinh_anh'] ?>">
            <tr>
                <td><input type="text" name="title" value="<?= htmlspecialchars($sp['title']) ?>"></td>
                <td>
                    <input type="file" name="hinh_anh_file">
                    <?php if (!empty($sp['hinh_anh'])): ?>
                        <br><img src="../../../<?= $sp['hinh_anh'] ?>">
                    <?php endif; ?>
                </td>
                <td><input type="number" name="gia_cu" value="<?= $sp['gia_cu'] ?>"></td>
                <td><input type="number" name="gia_sale" value="<?= $sp['gia_sale'] ?>"></td>
                <td><input type="number" name="phan_tram_sale" value="<?= $sp['phan_tram_sale'] ?>"></td>
                <td><input type="number" name="tong_so" value="<?= $sp['tong_so'] ?>"></td>
                <td><input type="number" name="da_ban" value="<?= $sp['da_ban'] ?>"></td>
                <td><input type="number" name="phan_tram_con_lai" value="<?= $sp['phan_tram_con_lai'] ?>"></td>
                <td><input type="number" step="0.1" name="danh_gia_sao" value="<?= $sp['danh_gia_sao'] ?>"></td>
                <td><input type="text" name="tu_khoa" value="<?= htmlspecialchars($sp['tu_khoa']) ?>"></td>
                <td>
                    <button type="submit">💾</button>
        </form>
        <form method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $sp['id'] ?>">
            <button type="submit">🗑️</button>
        </form>
                </td>
            </tr>
    <?php endforeach; ?>
</table>

</body>
</html>