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

// Kiểm tra người dùng trong bảng login (sử dụng prepared statements)
$sqlCheckUser = "SELECT * FROM login WHERE id = ?";
$stmtCheckUser = $conn->prepare($sqlCheckUser);
$stmtCheckUser->bind_param('i', $user_id);
$stmtCheckUser->execute();
$resultCheckUser = $stmtCheckUser->get_result();

$userInfo = []; // Mảng để lưu thông tin người dùng

// Truy vấn để lấy dữ liệu từ bảng đơn hàng (sử dụng prepared statements)
$sql = "SELECT donhang.id, donhang.hoten, donhang.sodienthoai, donhang.email, donhang.sonha_duong, donhang.phuong_xa, 
        donhang.quan_huyen, donhang.tinh_thanh, donhang.ghichu, donhang.totalPrice, donhang.date
        FROM donhang
        where donhang.id_nguoidung = ?;
        -- LEFT JOIN chitietdonhang ct ON donhang.id = ct.id_donhang
        -- join color c on c.id_color = ct.id_color 
        -- join giohang gh on gh.id_color = c.id_color 
        -- join login lg on lg.id = gh.nguoidung_id
        -- WHERE donhang.id_nguoidung = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/order.css">

    <!-- Bao gồm Bootstrap và jQuery -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"> -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div class="container">
        <h2>Đơn Hàng</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ Tên</th>
                    <th>Số Điện Thoại</th>
                    <th>Email</th>
                    <th>Số Nhà, Đường</th>
                    <th>Phường, Xã</th>
                    <th>Quận, Huyện</th>
                    <th>Tỉnh, Thành</th>
                    <th>Ghi Chú</th>
                    <th>Tổng Giá</th>
                    <th>Ngày Đặt</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Hiển thị dữ liệu từ kết quả truy vấn
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['hoten']}</td>";
                        echo "<td>{$row['sodienthoai']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['sonha_duong']}</td>";
                        echo "<td>{$row['phuong_xa']}</td>";
                        echo "<td>{$row['quan_huyen']}</td>";
                        echo "<td>{$row['tinh_thanh']}</td>";
                        echo "<td>{$row['ghichu']}</td>";
                        echo "<td>{$row['totalPrice']}</td>";
                        echo "<td>{$row['date']}</td>";
                        echo "<td><a href='chitiet.php?id={$row['id']}'>Chi tiết</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='12'>Không có đơn hàng nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php
    // Đóng kết nối
    $stmtCheckUser->close();
    $stmt->close();
    $conn->close();
    ?>

</body>

</html>
