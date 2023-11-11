<?php
require_once('php/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $productIdToDelete = $_POST['id_product_to_delete'];
    $colorIdToDelete = $_POST['id_color_to_delete'];
    
    $sizeToDelete = isset($_POST['size_to_delete']) ? $_POST['size_to_delete'] : null;

    // Kết nối CSDL và thực hiện truy vấn SQL để xóa sản phẩm
    require_once('php/db_connection.php');

    // Sử dụng câu lệnh SQL DELETE để xóa sản phẩm dựa vào 'id_product', 'id_color', và 'size'
    if ($sizeToDelete !== null) {
        $sql = "DELETE FROM giohang5 WHERE id_product = ? AND id_color = ? AND size = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('iis', $productIdToDelete, $colorIdToDelete, $sizeToDelete);

            if ($stmt->execute()) {
                // Xóa thành công, không cần trả về bất kỳ dữ liệu nào
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
        // Size not provided
        echo json_encode(["status" => "error", "message" => "Lỗi: Size not provided."]);
        exit();
    }
}
?>
