<?php
require_once('php/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $productIdToDelete = $_POST['id_product_to_delete'];
    $colorIdToDelete = $_POST['id_color_to_delete']; // Lấy giá trị id_color từ yêu cầu

    // Kết nối CSDL và thực hiện truy vấn SQL để xóa sản phẩm
    require_once('php/db_connection.php');

    // Sử dụng câu lệnh SQL DELETE để xóa sản phẩm dựa vào 'id_product' và 'id_color'
    $sql = "DELETE FROM giohang5 WHERE id_product = ? AND id_color = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $productIdToDelete, $colorIdToDelete); // Giả định 'id_product' và 'id_color' là kiểu INTEGER

    if ($stmt->execute()) {
        // Xóa thành công, không cần trả về bất kỳ dữ liệu nào
        echo "success";
    } else {
        // Xóa thất bại, xử lý lỗi nếu cần
        echo "Lỗi: " . $stmt->error;
    }


    // Sau khi xóa sản phẩm thành công
    // Truy vấn CSDL để lấy thông tin giỏ hàng cập nhật
    $sql = "SELECT * FROM giohang5";
    $result = $conn->query($sql);

    $cartItems = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cartItems[] = $row;
        }
    }

    // Trả về thông tin giỏ hàng mới dưới dạng JSON
    echo json_encode($cartItems);
}

?>
