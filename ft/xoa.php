<?php


require_once('db_connection.php');

header('Content-Type: application/json'); // Thêm dòng này để chỉ định loại nội dung là JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $productIdToDelete = isset($_POST['id_product_to_delete']) ? $_POST['id_product_to_delete'] : null;
    $colorIdToDelete = isset($_POST['id_color_to_delete']) ? $_POST['id_color_to_delete'] : null;
    $sizeToDelete = isset($_POST['size_to_delete']) ? $_POST['size_to_delete'] : null;

    // Kiểm tra xem có đủ thông tin cần thiết hay không
    if ($productIdToDelete !== null && $colorIdToDelete !== null && $sizeToDelete !== null) {
        // Sử dụng câu lệnh SQL DELETE với prepared statements để xóa sản phẩm
        $sql = "DELETE FROM giohang WHERE id_product = ? AND id_color = ? AND size = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('iis', $productIdToDelete, $colorIdToDelete, $sizeToDelete);

            if ($stmt->execute()) {
                // Xóa thành công
                echo json_encode(["status" => "success"]);
                exit();
            } else {
                // Xóa thất bại, log thông báo lỗi
                error_log("Lỗi SQL: " . $stmt->error);
                echo json_encode(["status" => "error", "message" => "Lỗi: " . $stmt->error]);
                exit();
            }
        } else {
            // Prepare statement failed
            echo json_encode(["status" => "error", "message" => "Lỗi: Prepare statement failed."]);
            exit();
        }
    } else {
        // Thông tin cần thiết không đủ
        echo json_encode(["status" => "error", "message" => "Lỗi: Thông tin cần thiết không đủ."]);
        exit();
    }
}
?>
