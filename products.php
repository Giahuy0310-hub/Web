<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "testt";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

// Trích xuất tham số từ URL
$loaisanpham = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : null;
$id_dm = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;

if ($loaisanpham) {
    // Nếu có tham số loaisanpham, thực hiện truy vấn dựa trên loaisanpham
    $sqlProducts = "SELECT * FROM products WHERE loaisanpham = '$loaisanpham'";
} elseif ($id_dm) {
    // Nếu có tham số ID_DM, thực hiện truy vấn dựa trên ID_DM
    $sqlProducts = "SELECT * FROM products WHERE ID_DM = '$id_dm'";
} else {
    // Nếu không có tham số nào, hiển thị tất cả sản phẩm
    $sqlProducts = "SELECT * FROM products";
}

$resultProducts = $conn->query($sqlProducts);

$productList = [];
if ($resultProducts->num_rows > 0) {
    while ($row = $resultProducts->fetch_assoc()) {
        $productList[] = [
            'ID' => $row['ID'],
            'ID_DM' => $row['ID_DM'],
            'TenSanPham' => $row['ten_san_pham'],
            'LinkHinhAnh' => $row['link_hinh_anh'],
            'Gia' => $row['gia'],
        ];
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
    <style>
        
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Four men</h1>
        </header>
        <ul>
            <li>Home</li>
            <li>Contact</li>
            <li>About</li>
        </ul>
        <div class="category-container">
            <div class="dropdown">
                <a class="category-button" href="products.php?ID_DM=2">Áo</a>
                <div class="dropdown-menu">
                    <a href="products.php?loaisanpham=SM">Áo sơ mi</a>
                    <a href="products.php?loaisanpham=PL">Áo polo</a>
                    <a href="products.php?loaisanpham=AT">Áo thun</a>
                </div>
            </div>
            <div class="dropdown">
                <a class="category-button" href="products.php?ID_DM=1">Quần</a>
                <div class="dropdown-menu">
                    <a href="products.php?loaisanpham=QJ">Quần jean</a>
                    <a href="products.php?loaisanpham=QK">Quần kaki</a>
                    <a href="products.php?loaisanpham=QT">Quần thể thao</a>
                </div>
            </div>
            <div class="dropdown">
                <a class="category-button" href="products.php?ID_DM=3">Giày</a>
                <div class="dropdown-menu">
                    <a href="products.php?loaisanpham=GY">Giày thể thao</a>
                    <a href="products.php?loaisanpham=GL">Giày lười</a>
                    <a href="products.php?loaisanpham=GC">Giày cao gót</a>
                </div>
            </div>
        </div>
        
        <div id="product-info" style="white-space: nowrap; margin-top: 35px;">
        <div class="product-container">
    <?php
    foreach ($productList as $product) {
        echo "<div class='product'>";
        echo "<a href='product_detail.php?product_id=" . $product['ID'] . "'>";
        echo "<img src='" . $product['LinkHinhAnh'] . "' alt='" . $product['TenSanPham'] . "'>";
        echo "<p>" . $product['TenSanPham'] . "</p>";
        echo "<p>Giá: " . $product['Gia'] . "</p>";
        echo "</a>";
        echo "</div>";
    }
    ?>
</div>

        <footer>
            <p>&copy; 2023 Website Bán Hàng</p>
        </footer>
    </div>
</body>
</html>
