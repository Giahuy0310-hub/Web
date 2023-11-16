<?php
// Nếu có yêu cầu Ajax
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["quantity"]) && isset($_POST["itemId"])) {
    require_once('php/db_connection.php');

    $quantity = $_POST["quantity"];
    $itemId = $_POST["itemId"];

    // Cập nhật số lượng trong bảng giohang
    $stmtUpdateQuantity = $conn->prepare("UPDATE giohang SET quantity = ? WHERE id = ?");
    $stmtUpdateQuantity->bind_param("ii", $quantity, $itemId);

    if ($stmtUpdateQuantity->execute()) {
        echo "Cập nhật số lượng thành công";
    } else {
        echo "Lỗi khi cập nhật số lượng: " . $stmtUpdateQuantity->error;
    }

    $stmtUpdateQuantity->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
