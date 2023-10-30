<?php
require_once('php/db_connection.php');

$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? urldecode($_GET['loaisanpham']) : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'asc';

$productsPerPage = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

$sortParam = '&sort=' . $sortOrder;

// Xây dựng câu truy vấn
$sqlCountProducts = "SELECT COUNT(DISTINCT p.id_product) AS total_products FROM products p WHERE 1=1";
$sqlProducts = "SELECT p.id_product, p.id_dm, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
              FROM products p
              LEFT JOIN color c ON p.id_color = c.id_color
              WHERE 1=1";

$bindParams = [];
$bindTypes = "";

if (!empty($selectedCategory)) {
    $sqlCountProducts .= " AND p.id_dm = ?";
    $sqlProducts .= " AND p.id_dm = ?";
    $bindParams[] = $selectedCategory;
    $bindTypes .= 'i';
}

if (!empty($selectedSubcategory)) {
    $sqlCountProducts .= " AND p.loaisanpham = ?";
    $sqlProducts .= " AND p.loaisanpham = ?";
    $bindParams[] = $selectedSubcategory;
    $bindTypes .= 's';
}

if (!empty($color_id)) {
    $sqlCountProducts .= " AND p.id_color = ?";
    $sqlProducts .= " AND p.id_color = ?";
    $bindParams[] = $color_id;
    $bindTypes .= 'i';
}

// Sắp xếp
$sqlProductsSorting = " ORDER BY p.gia " . ($sortOrder === 'desc' ? 'DESC' : 'ASC');

// Lấy số lượng sản phẩm
$stmtCount = $conn->prepare($sqlCountProducts);
$stmtCount->bind_param($bindTypes, ...$bindParams);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$totalProducts = 0;

if ($resultCount->num_rows > 0) {
    $row = $resultCount->fetch_assoc();
    $totalProducts = $row['total_products'];
}

// Lấy dữ liệu sản phẩm cho trang hiện tại
$startIndex = ($page - 1) * $productsPerPage;

// Câu truy vấn lấy sản phẩm đã sửa đổi để sử dụng sắp xếp và giới hạn kết quả
$sqlProducts .= " GROUP BY p.id_product" . $sqlProductsSorting . " LIMIT ?, ?";
$stmt = $conn->prepare($sqlProducts);

// Thêm kiểu dữ liệu 'ii' cho integer và bind tham số startIndex và productsPerPage
$bindTypes .= 'ii';
$bindParams[] = $startIndex;
$bindParams[] = $productsPerPage;

// Bây giờ bạn có thể bind tất cả các tham số cần thiết
$stmt->bind_param($bindTypes, ...$bindParams);
$stmt->execute();
$resultProducts = $stmt->get_result();

$productList = [];

while ($row = $resultProducts->fetch_assoc()) {
    $productId = $row['id_product'];
    $colors = getColorsForProduct($conn, $productId);
    $product = [
        'id_product' => $row['id_product'],
        'id_dm' => $row['id_dm'],
        'ten_san_pham' => $row['ten_san_pham'],
        'link_hinh_anh' => $row['link_hinh_anh'],
        'gia' => $row['gia'],
        'tenmau' => $row['tenmau'],
        'hex_color' => $row['hex_color'],
        'colors' => $colors,
    ];

    $productList[] = $product;
}

$totalPages = ceil($totalProducts / $productsPerPage);

function getColorsForProduct($conn, $productId) {
    $sqlColorsForProduct = "SELECT DISTINCT c.id_color, c.tenmau, c.hex_color, p.link_hinh_anh
                            FROM color c
                            JOIN products p ON c.id_color = p.id_color
                            WHERE p.id_product = ?";

    $stmt = $conn->prepare($sqlColorsForProduct);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $resultColorsForProduct = $stmt->get_result();
    $colors = [];

    while ($row = $resultColorsForProduct->fetch_assoc()) {
        $colors[] = [
            'id_color' => $row['id_color'],
            'tenmau' => $row['tenmau'],
            'hex_color' => $row['hex_color'],
            'link_hinh_anh' => $row['link_hinh_anh'],
        ];
    }

    return $colors;
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Website Bán Hàng</title>
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="js/products.js"></script>
</head>

<body>
<div class="navbar">
    <a href="home.php"><img src="images/logo.png" alt=""></a>
    <div class="navbar_list"></div>
    <?php include('php/dropdown.php'); ?>
<div id="product-info">
    <div class="product-container">
        <?php
        foreach ($productList as $product) {
            $productId = $product['id_product'];
            $colors = $product['colors'];
            echo '<div class="product">';
            echo '<a href="product_detail.php?id_product=' . $productId . '&color_id=' . $colors[0]['id_color'] . '">';
            echo '<img id="product-image-' . $productId . '" src="' . $colors[0]['link_hinh_anh'] . '" alt="' . $product['ten_san_pham'] . '">';
            echo '<p>' . $product['ten_san_pham'] . '</p>';
            echo '<p class="product-price">Giá: ' . $product['gia'] . '</p>';

            echo '<div class="color-options">';
            foreach ($colors as $color) {
                $colorHex = $color['hex_color'];
                $colorId = $color['id_color'];
                echo '<a href="product_detail.php?id_product=' . $productId . '&color_id=' . $colorId . '">';
                echo '<div class="color-option" style="background-color: ' . $colorHex . ';" onmouseover="changeProductImage(' . $productId . ', \'' . $color['link_hinh_anh'] . '\')" onmouseout="resetProductImage(' . $productId . ', \'' . $colors[0]['link_hinh_anh'] . '\')"></div>';
                echo '</a>';
            }
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
        ?>
    </div>
</div>
<div class="pagination">
    <ul class="pagination-list">
        <?php
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);

        if ($page > 1) {
            ?>
            <li class="pagination-item prev-page">
                <a href="products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=1<?= $sortParam ?>">&laquo; Trang đầu</a>
            </li>
        <?php } else { ?>
            <li class="pagination-item prev-page disabled">
                <a href="#">&laquo; Trang đầu</a>
            </li>
        <?php }

        for ($i = $startPage; $i <= $endPage; $i++) {
            ?>
            <li class="pagination-item <?= $i == $page ? 'active' : '' ?>">
                <a href="products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=<?= $i . $sortParam ?>"><?= $i ?></a>
            </li>
    <?php }

    if ($page < $totalPages) {
    ?>
        <li class="pagination-item next-page">
            <a href="products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=<?= $totalPages . $sortParam ?>">Trang cuối &raquo;</a>
        </li>
    <?php } else { ?>
        <li class="pagination-item next-page disabled">
            <a href="#">Trang cuối &raquo;</a>
        </li>
    <?php } ?>
</ul>
    </div>
</div>
</body>
</html>
