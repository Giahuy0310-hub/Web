<?php
require_once('php/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_san_pham = isset($_POST['ten_san_pham']) ? $_POST['ten_san_pham'] : null;
    $gia = isset($_POST['gia']) ? $_POST['gia'] : null;
    $id_color = isset($_POST['id_color']) ? $_POST['id_color'] : null;
    $link_hinh_anh = isset($_POST['link_hinh_anh']) ? $_POST['link_hinh_anh'] : null;

    if ($ten_san_pham && $gia && $id_color && $link_hinh_anh) {
        // Perform database insertion here
        $sql = "INSERT INTO giohang1 (ten_san_pham, gia, id_color, link_hinh_anh) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('siss', $ten_san_pham, $gia, $id_color, $link_hinh_anh);

        if ($stmt->execute()) {
            echo "Sản phẩm đã được thêm vào giỏ hàng.";
        } else {
            echo "Lỗi khi thêm sản phẩm vào giỏ hàng: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Dữ liệu không hợp lệ.";
    }
} else {
    echo "Invalid request.";
}
?>
