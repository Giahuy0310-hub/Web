<?php
require_once('php/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu từ yêu cầu POST
    $id_product = isset($_POST['id_product']) ? $_POST['id_product'] : null;
    $ten_san_pham = isset($_POST['ten_san_pham']) ? $_POST['ten_san_pham'] : null;
    $gia = isset($_POST['gia']) ? $_POST['gia'] : null;
    $id_color = isset($_POST['id_color']) ? $_POST['id_color'] : null;
    $link_hinh_anh = isset($_POST['link_hinh_anh']) ? $_POST['link_hinh_anh'] : null;
    $size = isset($_POST['size']) ? $_POST['size'] : null;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : null;

    if ($id_product && $ten_san_pham && $gia !== null && $id_color !== null && $link_hinh_anh && $size && $quantity !== null) {
        // Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng hay chưa
        $existingProductQuery = "SELECT * FROM giohang WHERE id_product = ? AND id_color = ? and size = ?";
        $stmtExistingProduct = $conn->prepare($existingProductQuery);
        $stmtExistingProduct->bind_param('sss', $id_product, $id_color, $size);
        $stmtExistingProduct->execute();
        $resultExistingProduct = $stmtExistingProduct->get_result();

        if ($resultExistingProduct->num_rows > 0) {
            // Sản phẩm đã tồn tại trong giỏ hàng, cập nhật số lượng
            $existingProduct = $resultExistingProduct->fetch_assoc();
            $newQuantity = $existingProduct['quantity'] + $quantity;

            $updateQuantityQuery = "UPDATE giohang SET quantity = ? WHERE id_product = ? AND id_color = ? and size = ?";
            $stmtUpdateQuantity = $conn->prepare($updateQuantityQuery);
            $stmtUpdateQuantity->bind_param('isss', $newQuantity, $id_product, $id_color, $size);

            if ($stmtUpdateQuantity->execute()) {
                echo "Số lượng sản phẩm đã được cập nhật trong giỏ hàng.";
            } else {
                echo "Lỗi khi cập nhật số lượng sản phẩm trong giỏ hàng: " . mysqli_error($conn);
            }

            $stmtUpdateQuantity->close();
        } else {
            // Sản phẩm chưa tồn tại trong giỏ hàng, thêm mới
            $insertProductQuery = "INSERT INTO giohang (id_product, ten_san_pham, gia, id_color, link_hinh_anh, size, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsertProduct = $conn->prepare($insertProductQuery);
            $stmtInsertProduct->bind_param('ssdsssi', $id_product, $ten_san_pham, $gia, $id_color, $link_hinh_anh, $size, $quantity);

            if ($stmtInsertProduct->execute()) {
                echo "Sản phẩm đã được thêm vào giỏ hàng.";
            } else {
                echo "Lỗi khi thêm sản phẩm vào giỏ hàng: " . mysqli_error($conn);
            }

            $stmtInsertProduct->close();
        }

        $stmtExistingProduct->close();
    } else {
        echo "Dữ liệu không hợp lệ.";
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>
