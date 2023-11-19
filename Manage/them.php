<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Dữ Liệu</title>
</head>
<body>

<?php
require_once('db_connection.php');

// Khởi tạo các biến với giá trị mặc định là rỗng
$id_product = $id_color = $tendanhmuc_selected = $ten_san_pham = $loaisanpham = $gia = $link_hinh_anh = $img1 = $img2 = $img3 = $img4 = $size_S = $size_M = $size_L = $size_XL = '';

// Kiểm tra xem form đã được gửi đi chưa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy giá trị từ form
    $id_product = $_POST["id_product"];
    $id_color = $_POST["id_color"];
    
    // Kiểm tra sự tồn tại của trường tendanhmuc trong mảng $_POST
    $tendanhmuc_selected = isset($_POST["tendanhmuc"]) ? $_POST["tendanhmuc"] : '';
    
    // Kiểm tra xem trường tendanhmuc có giá trị không
    if (!empty($tendanhmuc_selected)) {
        // Thực hiện truy vấn để lấy id_dm từ tendanhmuc
        $getCategoryIdQuery = "SELECT id_dm FROM categories WHERE tendanhmuc = '$tendanhmuc_selected'";
        $result = $conn->query($getCategoryIdQuery);
        
        // Kiểm tra xem truy vấn có thành công hay không
        if ($result && $result->num_rows > 0) {
            // Lấy id_dm từ kết quả truy vấn
            $row = $result->fetch_assoc();
            $id_dm = $row["id_dm"];

            // Thêm id_product vào bảng product_id
            $insertProductIdQuery = "INSERT INTO product_id (id_product) VALUES ('$id_product')";
            if ($conn->query($insertProductIdQuery) === TRUE) {
                // Tiếp tục lấy các giá trị khác từ form và thêm vào bảng products
                $ten_san_pham = $_POST["ten_san_pham"];
                $link_hinh_anh = $_POST["link_hinh_anh"];
                $loaisanpham = $_POST["loaisanpham"];
                $gia = $_POST["gia"];
                $img1 = $_POST["img1"];
                $img2 = $_POST["img2"];
                $img3 = $_POST["img3"];
                $img4 = $_POST["img4"];

                $size_S = $_POST["size_S"];
                $size_M = $_POST["size_M"];
                $size_L = $_POST["size_L"];
                $size_XL = $_POST["size_XL"];

                // Thực hiện truy vấn để thêm dữ liệu vào bảng products
                $insertDataQuery = "INSERT INTO products (id_product, id_color, id_dm, ten_san_pham, link_hinh_anh, loaisanpham, gia, img1, img2, img3, img4, size_S, size_M, size_L, size_XL)
                                    VALUES ('$id_product', '$id_color', '$id_dm', '$ten_san_pham', '$link_hinh_anh', '$loaisanpham', '$gia', '$img1', '$img2', '$img3', '$img4', '$size_S', '$size_M', '$size_L', '$size_XL')";

                if ($conn->query($insertDataQuery) === TRUE) {
                    echo "Dữ liệu đã được thêm thành công.";
                } else {
                    echo "Lỗi khi thêm dữ liệu vào bảng products: " . $conn->error;
                }
            } else {
                echo "Lỗi khi thêm id_product vào bảng product_id: " . $conn->error;
            }
        } else {
            echo "Lỗi khi lấy id_dm từ tendanhmuc: " . $conn->error;
        }
    } else {
        echo "Trường tendanhmuc không được để trống.";
    }
}

// Lấy danh sách id_dm và tendanhmuc từ bảng categories
$categoryQuery = "SELECT id_dm, tendanhmuc FROM categories";
$categoryResult = $conn->query($categoryQuery);
?>

<!-- Form để nhập dữ liệu -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <!-- Thêm các trường input cho từng cột trong bảng -->
    ID Product: <input type="text" name="id_product" value="<?php echo $id_product; ?>"><br>
    ID Color: <input type="text" name="id_color" value="<?php echo $id_color; ?>"><br>
    
    <!-- Sử dụng select box cho Tendanhmuc -->
    Tendanhmuc:
    <select name="tendanhmuc">
        <?php
            // Hiển thị danh sách tendanhmuc trong select box
            while($row = $categoryResult->fetch_assoc()) {
                $selected = ($row['tendanhmuc'] == $tendanhmuc_selected) ? 'selected' : '';
                echo "<option value='".$row['tendanhmuc']."' $selected>".$row['tendanhmuc']."</option>";
            }
            // Đặt con trỏ về đầu danh sách để sử dụng lại
            $categoryResult->data_seek(0);
        ?>
    </select><br>
    
    Tên Sản Phẩm: <input type="text" name="ten_san_pham" value="<?php echo $ten_san_pham; ?>"><br>
    Loại sản phẩm: <input type="text" name="loaisanpham" value="<?php echo $loaisanpham; ?>"><br>
    Giá: <input type="text" name="gia" value="<?php echo $gia; ?>"><br>
    Hình ảnh: <input type="text" name="link_hinh_anh" value="<?php echo $link_hinh_anh; ?>"><br>
    Hình ảnh 1: <input type="text" name="img1" value="<?php echo $img1; ?>"><br>
    Hình ảnh 2: <input type="text" name="img2" value="<?php echo $img2; ?>"><br>
    Hình ảnh 3: <input type="text" name="img3" value="<?php echo $img3; ?>"><br>
    Hình ảnh 4: <input type="text" name="img4" value="<?php echo $img4; ?>"><br>
    Size S: <input type="text" name="size_S" value="<?php echo $size_S; ?>"><br>
    Size M: <input type="text" name="size_M" value="<?php echo $size_M; ?>"><br>
    Size L: <input type="text" name="size_L" value="<?php echo $size_L; ?>"><br>
    Size XL: <input type="text" name="size_XL" value="<?php echo $size_XL; ?>"><br>

    <!-- Thêm các trường input cho các cột khác -->

    <input type="submit" value="Thêm Dữ Liệu">
</form>

<!-- JavaScript để chặn quay lại trang -->
<script>
    window.onload = function () {
        if (window.history && window.history.pushState) {
            window.history.pushState('forward', null);
            window.onpopstate = function (event) {
                // Kiểm tra xem có phải là request POST từ form hay không
                if (event.state && event.state === 'forward') {
                    window.history.pushState('forward', null, './#');
                }
            };
        }
    }
</script>

</body>
</html>
