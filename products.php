<?php

require_once('db_connection.php');

$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedCategoryy = isset($_GET['TenDanhMuc']) ? $_GET['TenDanhMuc'] : null;

$selectedSubcategory = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : null;
$id_product = isset($_GET['id_product']) ? $_GET['id_product'] : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;

$productsPerPage = 8; // Số sản phẩm trên mỗi trang
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

$sqlCategories = "SELECT ID_DM, TenDanhMuc FROM categories";
$stmt = $conn->prepare($sqlCategories); 
$stmt->execute();
$resultCategories = $stmt->get_result();
$categoryList = [];

if ($resultCategories->num_rows > 0) {
    while ($row = $resultCategories->fetch_assoc()) {
        $categoryID = $row['ID_DM'];
        $categoryName = $row['TenDanhMuc'];
        $isActive = $categoryID == $selectedCategory ? 'active' : '';

        $sqlSubcategories = "SELECT DISTINCT loaisanpham FROM products WHERE id_dm = ?";
        $stmtSubcategories = $conn->prepare($sqlSubcategories);
        $stmtSubcategories->bind_param('i', $categoryID);
        $stmtSubcategories->execute();
        $resultSubcategories = $stmtSubcategories->get_result();

        $subcategories = [];
        if ($resultSubcategories->num_rows > 0) {
            while ($rowSubcategory = $resultSubcategories->fetch_assoc()) {
                $subcategories[] = $rowSubcategory['loaisanpham'];
            }
        }

        $categoryList[] = [
            'ID_DM' => $categoryID,
            'TenDanhMuc' => $categoryName,
            'LoaiSanPham' => $subcategories,
        ];
    }
}

$sqlCountProducts = "SELECT COUNT(DISTINCT p.id_product) AS total_products FROM products p WHERE 1=1";
$sqlProducts = "SELECT DISTINCT p.id_product, p.id_dm, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
              FROM products p
              LEFT JOIN color c ON p.id_color = c.id_color
              WHERE 1=1";

if (!empty($selectedCategory)) {
    $sqlCountProducts .= " AND p.id_dm = ?";
    $sqlProducts .= " AND p.id_dm = ?";
}

if (!empty($selectedSubcategory)) {
    $sqlCountProducts .= " AND p.loaisanpham = ?";
    $sqlProducts .= " AND p.loaisanpham = ?";
}

// Kiểm tra và thêm color_id vào câu truy vấn SQL nếu có giá trị
if (!empty($color_id)) {
    $sqlCountProducts .= " AND p.id_color = ?";
    $sqlProducts .= " AND p.id_color = ?";
}

$stmt = $conn->prepare($sqlProducts);

// Dựa vào các điều kiện được chọn (category, subcategory, color), bạn cần bind các giá trị tương ứng
if (!empty($selectedCategory) && !empty($selectedSubcategory) && !empty($color_id)) {
    $stmt->bind_param('iii', $selectedCategory, $selectedSubcategory, $color_id);
} elseif (!empty($selectedCategory) && !empty($selectedSubcategory)) {
    $stmt->bind_param('ii', $selectedCategory, $selectedSubcategory);
} elseif (!empty($selectedCategory) && !empty($color_id)) {
    $stmt->bind_param('ii', $selectedCategory, $color_id);
} elseif (!empty($selectedSubcategory) && !empty($color_id)) {
    $stmt->bind_param('ii', $selectedSubcategory, $color_id);
} elseif (!empty($selectedCategory)) {
    $stmt->bind_param('i', $selectedCategory);
} elseif (!empty($selectedSubcategory)) {
    $stmt->bind_param('i', $selectedSubcategory);
} elseif (!empty($color_id)) {
    $stmt->bind_param('i', $color_id);
}

$stmt->execute();
$result = $stmt->get_result();
$productList = [];

while ($row = $result->fetch_assoc()) {
    $productId = $row['id_product'];
    $colors = getColorsForProduct($conn, $productId);
    $productList[] = [
        'id_product' => $productId,
        'ten_san_pham' => $row['ten_san_pham'],
        'link_hinh_anh' => $row['link_hinh_anh'],
        'gia' => $row['gia'],
        'tenmau' => $row['tenmau'],
        'hex_color' => $row['hex_color'],
        'colors' => $colors,
    ];
}

$stmtCount = $conn->prepare($sqlCountProducts);

if (!empty($selectedCategory) && !empty($selectedSubcategory)) {
    $stmtCount->bind_param('ii', $selectedCategory, $selectedSubcategory);
} elseif (!empty($selectedCategory)) {
    $stmtCount->bind_param('i', $selectedCategory);
} elseif (!empty($selectedSubcategory)) {
    $stmtCount->bind_param('i', $selectedSubcategory);
}

$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$totalProducts = 0;

if ($resultCount->num_rows > 0) {
    $row = $resultCount->fetch_assoc();
    $totalProducts = $row['total_products'];
}

// Lấy dữ liệu sản phẩm cho trang hiện tại
$startIndex = ($page - 1) * $productsPerPage;
$sqlProducts .= " GROUP BY p.id_product LIMIT ?, ?";
$stmt = $conn->prepare($sqlProducts);

if (!empty($selectedCategory) && !empty($selectedSubcategory)) {
    $stmt->bind_param('iiii', $selectedCategory, $selectedSubcategory, $startIndex, $productsPerPage);
} elseif (!empty($selectedCategory)) {
    $stmt->bind_param('iii', $selectedCategory, $startIndex, $productsPerPage);
} elseif (!empty($selectedSubcategory)) {
    $stmt->bind_param('iii', $selectedSubcategory, $startIndex, $productsPerPage);
} else {
    $stmt->bind_param('ii', $startIndex, $productsPerPage);
}

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
    <script src="js/top.js"></script>


</head>

<body>
<div class="navbar">
    <a href="home.php"><img src="images/logo.png" alt=""></a>
    <div class="navbar_list">
        
    </div>
            <?php
echo "<a href='products.php' class='category-button'>Tất cả</a>";

            foreach ($categoryList as $category) {
                $categoryID = $category['ID_DM'];
                $categoryName = $category['TenDanhMuc'];
                $isActive = $categoryID == $selectedCategory ? 'active' : '';
                $subcategoryList = $category['LoaiSanPham'];

                $subcategoryLinks = [];
                foreach ($subcategoryList as $subcategory) {
                    $subcategoryLink = "products.php?ID_DM=$categoryID&id_product=$id_product&color_id=$color_id&loaisanpham=" . urlencode($subcategory);
                    $isActiveSubcategory = $subcategory == $selectedSubcategory ? 'active' : '';
                    $subcategoryLinks[] = "<a class='subcategory-button $isActiveSubcategory' href='$subcategoryLink'>$subcategory</a>";
                }

                echo "<div class='dropdown'>";
                echo "<a class='category-button $isActive' href='products.php?ID_DM=$categoryID&id_product=$id_product&color_id=$color_id'>$categoryName</a>";
                if (!empty($subcategoryLinks)) {
                    echo "<div class='dropdown-menu'>";
                    echo implode($subcategoryLinks);
                    echo "</div>";
                }
                echo "</div>";
            }
            ?>
                    <div class="navbar_logo">
            <a href=""><i class="fa-solid fa-magnifying-glass"></i></a>
            <a href=""><i class="fa-regular fa-user"></i></a>
            <a href=""><i class="fa-solid fa-cart-shopping"></i></a>
        </div>
        </div>
        <?php
if ($selectedCategory) {
    echo "<h2 class='centered'>$selectedCategory</h2>";
}

if (!empty($selectedSubcategory)) {
    echo "<h2 class='centered'>$selectedSubcategory</h2>";
}
?>

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
                    <a href="products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=1">&laquo; Trang đầu</a>
                </li>
            <?php } else { ?>
                <li class="pagination-item prev-page disabled">
                    <a href="#">&laquo; Trang đầu</a>
                </li>
            <?php }

            for ($i = $startPage; $i <= $endPage; $i++) {
            ?>
                <li class="pagination-item <?= $i == $page ? 'active' : '' ?>">
                    <a href="products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php }

            if ($page < $totalPages) {
            ?>
                <li class="pagination-item next-page">
                    <a href="products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=<?= $totalPages ?>">Trang cuối &raquo;</a>
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
