<?php
header('Content-Type: application/json');
require_once __DIR__ . '/path/to/Database.php'; // Thay đổi đường dẫn phù hợp

$db = Database::getInstance();

$action_type = $_POST['action_type'] ?? '';

$response = ['success' => false, 'message' => 'Hành động không hợp lệ'];

switch ($action_type) {
    case 'create_vip_level':
        $level = $_POST['level'] ?? 0;
        $vip_name = $_POST['vip_name'] ?? '';
        $min = $_POST['min'] ?? 0;
        $commission_rate = $_POST['commission_rate'] ?? 0;
        $desc = $_POST['desc'] ?? '';
        $logo = $_POST['logo'] ?? '';

        try {
            $db->pdo_query(
                "INSERT INTO vip_level (level, vip_name, min, commission_rate, `desc`, logo) VALUES (?, ?, ?, ?, ?, ?)",
                [$level, $vip_name, $min, $commission_rate, $desc, $logo]
            );
            $response = ['success' => true, 'message' => 'Thêm cấp độ thành công'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }
        break;

    case 'edit_vip_level':
        $id = $_POST['id'] ?? 0;
        $level = $_POST['level'] ?? 0;
        $vip_name = $_POST['vip_name'] ?? '';
        $min = $_POST['min'] ?? 0;
        $commission_rate = $_POST['commission_rate'] ?? 0;
        $desc = $_POST['desc'] ?? '';
        $logo = $_POST['logo'] ?? '';

        if ($id <= 0) {
            $response = ['success' => false, 'message' => 'ID không hợp lệ'];
            break;
        }

        try {
            $db->pdo_query(
                "UPDATE vip_level SET level = ?, vip_name = ?, min = ?, commission_rate = ?, `desc` = ?, logo = ? WHERE id = ?",
                [$level, $vip_name, $min, $commission_rate, $desc, $logo, $id]
            );
            $response = ['success' => true, 'message' => 'Cập nhật thành công'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }
        break;

    case 'delete_vip_level':
        $id = $_POST['id'] ?? 0;

        if ($id <= 0) {
            $response = ['success' => false, 'message' => 'ID không hợp lệ'];
            break;
        }

        try {
            $db->pdo_query("DELETE FROM vip_level WHERE id = ?", [$id]);
            $response = ['success' => true, 'message' => 'Xoá cấp độ thành công'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }
        break;
}

echo json_encode($response);
?>