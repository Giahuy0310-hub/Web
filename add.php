<?php
require_once('php/db_connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_product = isset($_POST['id_product']) ? $_POST['id_product'] : null;
    $ten_san_pham = isset($_POST['ten_san_pham']) ? $_POST['ten_san_pham'] : null;
    $gia = isset($_POST['gia']) ? $_POST['gia'] : null;
    $id_color = isset($_POST['id_color']) ? $_POST['id_color'] : null;
    $link_hinh_anh = isset($_POST['link_hinh_anh']) ? $_POST['link_hinh_anh'] : null;
    $size = isset($_POST['size']) ? $_POST['size'] : null;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : null;

    if ($id_product && $ten_san_pham && $gia !== null && $id_color !== null && $link_hinh_anh && $size && $quantity !== null) {
        // Perform database insertion here
        $sql = "INSERT INTO giohang5 (id_product, ten_san_pham, gia, id_color, link_hinh_anh, size, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssisssi', $id_product, $ten_san_pham, $gia, $id_color, $link_hinh_anh, $size, $quantity);

        if ($stmt->execute()) {
            echo "Sản phẩm đã được thêm vào giỏ hàng.";
        } else {
            echo "Lỗi khi thêm sản phẩm vào giỏ hàng: " . mysqli_error($conn);
        }

        $stmt->close();
    } else {
        echo "Dữ liệu không hợp lệ.";
    }
} else {
    echo "Invalid request.";
}

?>
