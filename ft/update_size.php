<?php
require_once('db_connection.php');


if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}if (isset($_POST['size']) && isset($_POST['itemId'])) {
    $size = $_POST['size'];
    $itemId = $_POST['itemId'];

echo 'itemId: ' . $itemId; 

// Cập nhật dữ liệu trong bảng giohang
$stmt = $conn->prepare("UPDATE giohang SET size = ? WHERE id = ?");
$stmt->bind_param("si", $size, $itemId);

// Thực hiện câu truy vấn và kiểm tra kết quả
if ($stmt->execute()) {
    // In ra câu truy vấn (nếu bạn muốn kiểm tra)
    echo "SQL Query: UPDATE giohang SET size = '$size' WHERE id = '$itemId'"; // Thêm dấu nháy đơn
    echo "Cập nhật dữ liệu thành công";
} else {
    echo "Lỗi khi cập nhật dữ liệu: " . $stmt->error;
}
} else {
    echo "Thiếu dữ liệu từ yêu cầu Ajax";
}
// Đóng prepared statement
$stmt->close();

// Đóng kết nối
$conn->close();
?>
