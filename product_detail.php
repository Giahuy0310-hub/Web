<?php
// Kết nối đến cơ sở dữ liệu (giống như bạn đã làm trong trang products.php)
$servername = "localhost";
$username = "root";
$password = "";
$database = "testt";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

// Trích xuất tham số product_id từ URL
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

// Truy vấn sản phẩm dựa trên product_id
if ($product_id) {
    $sqlProductDetail = "SELECT * FROM products WHERE ID = $product_id";
    $resultProductDetail = $conn->query($sqlProductDetail);

    // Kiểm tra xem sản phẩm có tồn tại không
    if ($resultProductDetail->num_rows > 0) {
        $productDetail = $resultProductDetail->fetch_assoc();
    } else {
        // Xử lý khi không tìm thấy sản phẩm
        echo "Sản phẩm không tồn tại.";
    }
} else {
    // Xử lý khi không có tham số product_id
    echo "Không có sản phẩm được chọn.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chi Tiết Sản Phẩm</title>
    <link rel="stylesheet" href="css/index.css">
    <style>
        /* CSS của bạn */
        .product-detail-container {
            text-align: center;
            margin-top: 20px;
        }

        .product-detail-image {
            max-width: 300px;
            margin: 0 auto;
        }

        .product-detail-price {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Chi Tiết Sản Phẩm</h1>
        </header>
        <ul>
            <li><a href="index.php">Trang chủ</a></li>
            <li>Liên hệ</li>
            <li>Giới thiệu</li>
        </ul>
        <div class="product-detail-container">
            <?php
            if (isset($productDetail)) {
                echo "<h2>" . $productDetail['TenSanPham'] . "</h2>";
                echo "<img class='product-detail-image' src='" . $productDetail['LinkHinhAnh'] . "' alt='" . $productDetail['TenSanPham'] . "'>";
                echo "<p class='product-detail-price'>Giá: " . $productDetail['Gia'] . "</p>";
            }
            ?>
        </div>
        <footer>
            <p>&copy; 2023 Website Bán Hàng</p>
        </footer>
    </div>
</body>
</html>
