
<?php
    // Kết nối với cơ sở dữ liệu (điều này cần được cập nhật để phản ánh cấu trúc thực tế của cơ sở dữ liệu)
    require_once('db_connection.php');


    // Truy vấn để lấy dữ liệu từ bảng đơn hàng
    $sql = "SELECT donhang.id, donhang.hoten, donhang.sodienthoai, donhang.email, donhang.sonha_duong, donhang.phuong_xa, 
            donhang.quan_huyen, donhang.tinh_thanh, donhang.ghichu, donhang.totalPrice, donhang.date, chitietdonhang.ten_san_pham
            FROM donhang
            LEFT JOIN chitietdonhang ON donhang.id = chitietdonhang.id_donhang ";
    $result = $conn->query($sql);
    ?>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/order.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
</head>
<body>


    <div class="container">
        <h2>Đơn Hàng</h2>
        <table>
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
    $conn->close();
    ?>

</body>
</html>
