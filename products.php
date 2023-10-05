<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "testt";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

// Trích xuất tham số category từ URL
$category = isset($_GET['category']) ? $_GET['category'] : null;

// Truy vấn sản phẩm dựa trên tham số category
if ($category) {
    $sqlProducts = "SELECT * FROM products WHERE ID_DM = $category";
    $resultProducts = $conn->query($sqlProducts);

    // Tạo danh sách sản phẩm
    $productList = [];
    if ($resultProducts->num_rows > 0) {
        while ($row = $resultProducts->fetch_assoc()) {
            $productList[] = [
                'ID_DM' => $row['ID_DM'],
                'TenSanPham' => $row['TenSanPham'],
            ];
        }
    }
} else {
    $productList = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Website Bán Hàng</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Website Bán Hàng</h1>
        </header>
        <ul>
            <li>Home</li>
            <li>Contact</li>
            <li>About</li>
        </ul>
        <div class="product-list">
            <!-- Container chứa các nút danh mục -->
            <div class="category-container">
                <a class="category-button" href="products.php?category=1">Quần</a>
                <a class="category-button" href="products.php?category=2">Áo</a>
                <a class="category-button" href="products.php?category=3">Giày</a>
            </div>
            <!-- Hiển thị danh sách sản phẩm -->
            <ul>
                <?php foreach ($productList as $product) : ?>
                    <li><?php echo $product['TenSanPham']; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <footer>
        <p>&copy; 2023 Website Bán Hàng</p>
    </footer>
</body>
</html>
