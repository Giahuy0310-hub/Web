<?php
require_once('db_connection.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu từ yêu cầu POST
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Lấy user_id từ phiên đăng nhập
    $id_product = $_POST['id_product'] ?? null;
    $ten_san_pham = $_POST['ten_san_pham'] ?? null;
    $gia = $_POST['gia'] ?? null;
    $id_color = $_POST['id_color'] ?? null;
    $link_hinh_anh = $_POST['link_hinh_anh'] ?? null;
    $size = $_POST['size'] ?? null;
    $quantity = $_POST['quantity'] ?? null;

    if ($user_id && $id_product && $ten_san_pham && is_numeric($gia) && $id_color && $link_hinh_anh && $size && is_numeric($quantity)) {
        try {
            // Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng của người dùng hay chưa
            $existingProductQuery = "SELECT * FROM giohang WHERE nguoidung_id = ? AND id_product = ? AND id_color = ? AND size = ?";
            $stmtExistingProduct = $conn->prepare($existingProductQuery);
            $stmtExistingProduct->bind_param('isss', $user_id, $id_product, $id_color, $size);
            $stmtExistingProduct->execute();
            $resultExistingProduct = $stmtExistingProduct->get_result();

            if ($resultExistingProduct->num_rows > 0) {
                // Sản phẩm đã tồn tại trong giỏ hàng, cập nhật số lượng
                $existingProduct = $resultExistingProduct->fetch_assoc();
                $newQuantity = $existingProduct['quantity'] + $quantity;

                $updateQuantityQuery = "UPDATE giohang SET quantity = ? WHERE nguoidung_id = ? AND id_product = ? AND id_color = ? AND size = ?";
                $stmtUpdateQuantity = $conn->prepare($updateQuantityQuery);
                $stmtUpdateQuantity->bind_param('iisss', $newQuantity, $user_id, $id_product, $id_color, $size);
                $stmtUpdateQuantity->execute();
                $stmtUpdateQuantity->close();

                echo "Số lượng sản phẩm đã được cập nhật trong giỏ hàng.";
            } else {
                // Sản phẩm chưa tồn tại trong giỏ hàng, thêm mới
                $insertProductQuery = "INSERT INTO giohang (nguoidung_id, id_product, ten_san_pham, gia, id_color, link_hinh_anh, size, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtInsertProduct = $conn->prepare($insertProductQuery);
                $stmtInsertProduct->bind_param('issdsssi', $user_id, $id_product, $ten_san_pham, $gia, $id_color, $link_hinh_anh, $size, $quantity);
                $stmtInsertProduct->execute();
                $stmtInsertProduct->close();

                echo "Sản phẩm đã được thêm vào giỏ hàng.";
            }

            $stmtExistingProduct->close();
        } catch (Exception $e) {
            echo "Có lỗi xảy ra: " . $e->getMessage();
        }
    } else {
        echo "Dữ liệu không hợp lệ.";
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>
