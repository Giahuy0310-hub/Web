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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .main-user{
            margin-top: 90px;
        }
        .main-user h1{
            margin: 0;
        }
    </style>
</head>
<body>
<div class="navbar">
        <a href="../home.php"><img src="../images/logo.png" alt=""></a>
        <div class="navbar_list"></div>
        <?php include('dropdown.php'); ?>
    </div>
<div class="main-user">
    <h1>Trang Chủ</h1>
    <?php include(__DIR__ . '\menu.php');; ?>

</div>

</body>
</html>
