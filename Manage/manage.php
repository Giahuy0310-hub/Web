<?php
session_start();

require_once('db_connection.php');

// Kiểm tra nếu người dùng đã đăng nhập
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    header("Location: login.html");
    exit;
}

// Kiểm tra người dùng trong bảng login
$sqlCheckUser = "SELECT * FROM login WHERE id = ?";
$stmtCheckUser = $conn->prepare($sqlCheckUser);
$stmtCheckUser->bind_param('i', $user_id);
$stmtCheckUser->execute();
$resultCheckUser = $stmtCheckUser->get_result();

if ($resultCheckUser->num_rows > 0) {

} else {
    echo "Người dùng không tồn tại.";

    exit;
}

$stmtCheckUser->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
</head>
<body>

<div>
    <h1>Trang Chủ</h1>
    <?php include(__DIR__ . '\menu.php');; ?>

</div>

</body>
</html>
