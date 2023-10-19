<?php
require_once('db_connection.php');

// Truy vấn để lấy top 8 sản phẩm bán chạy
$sql = "SELECT p.id_product, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
        FROM products p
        LEFT JOIN color c ON p.id_color = c.id_color
        ORDER BY p.so_luong_da_ban DESC
        LIMIT 8";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$productList = [];

while ($row = $result->fetch_assoc()) {
    $productList[] = [
        'id_product' => $row['id_product'],
        'ten_san_pham' => $row['ten_san_pham'],
        'link_hinh_anh' => $row['link_hinh_anh'],
        'gia' => $row['gia'],
        'tenmau' => $row['tenmau'],
        'hex_color' => $row['hex_color'],
    ];
}

// Truy vấn để lấy top 8 sản phẩm giá cao
$sql = "SELECT p.id_product, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
        FROM products p
        LEFT JOIN color c ON p.id_color = c.id_color
        ORDER BY p.gia DESC
        LIMIT 8";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$expensiveProductList = [];

while ($row = $result->fetch_assoc()) {
    $expensiveProductList[] = [
        'id_product' => $row['id_product'],
        'ten_san_pham' => $row['ten_san_pham'],
        'link_hinh_anh' => $row['link_hinh_anh'],
        'gia' => $row['gia'],
        'tenmau' => $row['tenmau'],
        'hex_color' => $row['hex_color'],
    ];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/top.css">



</head>
<body>
    <h1>Sản phẩm bán chạy</h1>
    <div class="product-container" id="bestSellingContainer">
        <?php
        foreach ($productList as $product) {
            echo "<div class='product'>";
            echo "<img src='" . $product['link_hinh_anh'] . "' alt='" . $product['ten_san_pham'] . "'>";
            echo "<p>" . $product['ten_san_pham'] . "</p>";
            echo "<p class='product-price'>Giá: " . $product['gia'] . "</p>";
            $colorHex = $product['hex_color'];
            echo "<div class='color-option' style='background-color: $colorHex;'></div>";
            echo "</div>";
        }
        ?>
        <div class="pagination-container">
            <a class="prev" onclick="plusSlides(-1, 'bestSellingContainer')">&#10094; </a>
            <a class="next" onclick="plusSlides(1, 'bestSellingContainer')"> &#10095;</a>
        </div>
    </div>

    <h1>Sản phẩm giá cao</h1>
    <div class="product-container" id="expensiveContainer">
        <?php
        foreach ($expensiveProductList as $product) {
            echo "<div class='product'>";
            echo "<img src='" . $product['link_hinh_anh'] . "' alt='" . $product['ten_san_pham'] . "'>";
            echo "<p>" . $product['ten_san_pham'] . "</p>";
            echo "<p class='product-price'>Giá: " . $product['gia'] . "</p>";
            $colorHex = $product['hex_color'];
            echo "<div class='color-option' style='background-color: $colorHex;'></div>";
            echo "</div>";
        }
        ?>
        <div class="pagination-container">
            <a class="prev" onclick="plusSlides(-1, 'expensiveContainer')">&#10094; </a>
            <a class="next" onclick="plusSlides(1, 'expensiveContainer')"> &#10095;</a>
        </div>
    </div>
    <script src="js/top.js"></script>

</body>
</html>
