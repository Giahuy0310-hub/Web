<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Dữ Liệu</title>
    <style>
                  @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,200&display=swap');
        body{
            font-family: 'Montserrat', sans-serif;
            background-image: linear-gradient(to right, rgb(50, 50, 50), rgb(120, 120, 120),rgb(200, 200, 200)) ;
            color: white; 
        }
        .tilte h2{
            color: rgb(255, 255, 255);
            padding-left: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid beige;
        }

        .content_input{
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
        }

        .content_input p{
            font-weight: 600;
            font-size: 15px;
        }

        .content_input input, .content_input select{
            height: 50px;
            width: 250px;
            box-sizing: border-box;
            border-radius: 5px;
            border: none;
            box-shadow:inset 0 0 5px 5px #dfdede;
            background: #ebebeb;
            padding: 5px 5px 5px 20px;
        }
        .button{
            width: 150px;
            height: 40px;
        }

        .button:hover{
            cursor: pointer;
            opacity: 60%;
            
        }
        #file-1-preview img
        ,#file-2-preview img,
        #file-3-preview img,
        #file-4-preview img,
        #file-5-preview img{
            width: 60px;
            height: 60px;
            position: absolute;
        }

        #file-1-preview div
        ,#file-2-preview div,
        #file-3-preview div,
        #file-4-preview div,
        #file-5-preview div{
            height: 15px;
            width: 60px;
            position: relative;
            top: 80%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #999;
        }

        .form_elements{
            width: 250px;
        }

        .form_elements input{
            display: block;
        }
    </style>
</head>
<body>
<div class="tilte">
        <h2>
            Tạo mới sản phẩm
        </h2>
    </div>

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

<div class="container">
        <div class="content_input">
            <div>
                <p>ID Product:</p>
                <input type="text" name="id_product" value="<?php echo $id_product; ?>">
            </div>
            <div>
                <p>ID Color:</p>
                <input type="text" name="id_color" value="<?php echo $id_color; ?>">
            </div>
            <div>
                <p>Tên Sản Phẩm: </p>
                <input type="text" name="ten_san_pham" value="<?php echo $ten_san_pham; ?>"><br>
            </div>
            <div>
                <p>Loại sản phẩm:</p>
                <input type="text" name="loaisanpham" value="<?php echo $loaisanpham; ?>"><br>
            </div>
            <div>
                <p>Giá:</p>
                <input type="text" name="gia" value="<?php echo $gia; ?>"><br>
            </div>
        </div>
        
        <div class="content_input">
            <div>
                <p>Danh mục: </p>
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
                </select>
            </div>
            <div>
                <p>Size S:</p>
                <input class="number" type="number" min="0" name="size_S" value="<?php echo $size_S; ?>"><br>
            </div>
            <div>
                <p>Size M: </p>
                <input class="number" type="number" min="0" name="size_M" value="<?php echo $size_M; ?>"><br>
            </div>
            <div>
                <p>Size L: </p>
                <input class="number" type="number" min="0" name="size_L" value="<?php echo $size_L; ?>"><br>
            </div>
            <div>
                <p>Size XL:  </p>
                <input class="number" type="number" min="0" name="size_XL" value="<?php echo $size_XL; ?>"><br>
            </div>
            
        </div>
        <div class="content_input">
            <div class="form_elements">
                <p>Hình ảnh: </p>
                <input style="color:black; padding-top: 15px;" id="file-1" type="file" name="link_hinh_anh" accept="image/*" value="<?php echo $link_hinh_anh; ?>">
            </div>
            <div class="form_elements">
                <p>Hình ảnh 1: </p>
                <input style="color:black; padding-top: 15px;" id="file-2" type="file" name="img1" accept="image/*" value="<?php echo $img1; ?>">
            </div>
            <div class="form_elements">
                <p>Hình ảnh 2: </p>
                <input style="color:black; padding-top: 15px;" id="file-3" type="file" name="img2" accept="image/*" value="<?php echo $img2; ?>">
            </div>
            <div class="form_elements">
                <p>Hình ảnh 3:</p>
                <input style="color:black; padding-top: 15px;" id="file-4" type="file" name="img3" accept="image/*" value="<?php echo $img3; ?>">
            </div>
            <div class="form_elements">
                <p>Hình ảnh 4: </p>
                <input style="color:black; padding-top: 15px;" id="file-5" type="file" name="img4" accept="image/*" value="<?php echo $img4; ?>">
            </div>
        </div>
            <input class="button" style="margin-top: 100px; margin-right: 50px; background-color: #444; float: right; color: white; border-radius: 10px;" type="submit" onclick="alert('Thêm sản phẩm thành công!!!')" value="Thêm Dữ Liệu">
    </div>
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
