<?php
// Kết nối đến cơ sở dữ liệu
require_once('php/db_connection.php');

// Kiểm tra xem có yêu cầu POST từ Ajax không
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["value"]) && isset($_POST["itemId"])) {
    $value = $_POST["value"];
    $itemId = $_POST["itemId"];

    // Tùy thuộc vào loại dữ liệu bạn cần cập nhật (size hoặc quantity), thay đổi câu truy vấn SQL
    $sql = "UPDATE giohang SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $value, $itemId);

    if ($stmt->execute()) {
        echo "Cập nhật dữ liệu thành công";
    } else {
        echo "Lỗi khi cập nhật dữ liệu: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
