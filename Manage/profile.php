<?php
session_start();

require_once('db_connection.php');

// Kiểm tra nếu người dùng đã đăng nhập
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Kiểm tra nếu người dùng chưa đăng nhập, chuyển hướng đến trang đăng nhập
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

$userInfo = []; // Mảng để lưu thông tin người dùng

// Lưu thông tin người dùng vào mảng
if ($resultCheckUser->num_rows > 0) {
    $userInfo = $resultCheckUser->fetch_assoc();
}

$stmtCheckUser->close();
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="css/menu.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
    <link rel="stylesheet" href="css/pf.css">
    <style>
        .main{
            width: 100%;
        }
        .right-column{
            width: 50%;
            margin-left: 50px;
        }
        a{
            border: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../home.php"><img src="../images/logo.png" alt=""></a>
        <div class="navbar_list">
            
        </div>
        <?php include('dropdown.php'); ?>
    </div>


    <main>
        <div class="left-column">
            <?php include('menu.php'); ?>
        </div>

        <div class="right-column">
            <h1>Thông tin cá nhân</h1>

            <?php
            if (!empty($userInfo)) {
                echo "<p class='right-column'><strong>ID:</strong> " . $userInfo['id'] . "</p>";
                echo "<p class='right-column'><strong>Full Name:</strong> " . $userInfo['fullname'] . "</p>";
                echo "<p class='right-column'><strong>Phone Number:</strong> " . $userInfo['phone_number'] . "</p>";
                echo "<p class='right-column'><strong>Email:</strong> " . $userInfo['email'] . "</p>";
            } else {
                echo "<p class='user-info'>Không có dữ liệu.</p>";
            }
            ?>
        </div>
    </main>
</body>

</html>
