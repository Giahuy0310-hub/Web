<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/chitiet.css">
    <title>Chi Tiết Đơn Hàng</title>

</head>
<body>

    <?php
    // Kết nối với cơ sở dữ liệu (điều này cần được cập nhật để phản ánh cấu trúc thực tế của cơ sở dữ liệu)
    require_once('php/db_connection.php');


    // Lấy id từ tham số truyền vào
    $id_donhang = $_GET['id'];

    // Truy vấn để lấy dữ liệu từ bảng chitietdohang dựa trên id_donhang
    $sql = "SELECT * FROM chitietdonhang WHERE id_donhang = $id_donhang";
    $result = $conn->query($sql);
    ?>

    <div class="container">
        <h2>Chi Tiết Đơn Hàng</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Đơn Hàng</th>
                    <th>Tên Sản Phẩm</th>
                    <th>ID Sản Phẩm</th>
                    <th>ID Màu Sắc</th>
                    <th>Giá</th>
                    <th>Size</th>
                    <th>Số Lượng</th>
                    <th>Link Hình Ảnh</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Hiển thị dữ liệu từ kết quả truy vấn
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id_donhang']}</td>";
                        echo "<td>{$row['ten_san_pham']}</td>";
                        echo "<td>{$row['id_product']}</td>";
                        echo "<td>{$row['id_color']}</td>";
                        echo "<td>{$row['gia']}</td>";
                        echo "<td>{$row['size']}</td>";
                        echo "<td>{$row['quantity']}</td>";
                        echo "<td><img class='thumbnail' src='{$row['link_hinh_anh']}' alt='Hình ảnh'></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Không có chi tiết nào cho đơn hàng này.</td></tr>";
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
