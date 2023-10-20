<?php
require_once('db_connection.php');

$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : null;
$id_product = isset($_GET['id_product']) ? $_GET['id_product'] : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;

$productsPerPage = 8; // Số sản phẩm trên mỗi trang
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// Tính chỉ số bắt đầu cho sản phẩm trên trang hiện tại
$startIndex = ($page - 1) * $productsPerPage;

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

// Tính tổng số trang và câu lệnh SQL để lấy dữ liệu sản phẩm
$sqlCountProducts = "SELECT COUNT(DISTINCT p.id_product) AS total_products FROM products p WHERE 1=1";
$sqlProducts = "SELECT p.id_product, p.id_dm, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
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

// Kiểm tra và thêm id_product vào URL nếu có giá trị
if (!empty($id_product)) {
    $url .= '&id_product=' . $id_product;
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

$totalPages = ceil($totalProducts / $productsPerPage);

// Lấy dữ liệu sản phẩm cho trang hiện tại
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
    $product = [
        'id_product' => $row['id_product'],
        'id_dm' => $row['id_dm'],
        'ten_san_pham' => $row['ten_san_pham'],
        'link_hinh_anh' => $row['link_hinh_anh'],
        'gia' => $row['gia'],
        'tenmau' => $row['tenmau'],
        'hex_color' => $row['hex_color'],
    ];

    $productList[] = $product;
}

$colorsForProducts = [];

foreach ($productList as $product) {
    $productId = $product['id_product'];
    $sqlColorsForProduct = "SELECT DISTINCT id_color, tenmau, hex_color FROM color WHERE id_color IN (SELECT DISTINCT id_color FROM products WHERE id_product = ?)";
    $stmt = $conn->prepare($sqlColorsForProduct);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $resultColorsForProduct = $stmt->get_result();

    $colorsForProduct = [];

    while ($row = $resultColorsForProduct->fetch_assoc()) {
        $colorsForProduct[] = [
            'id_color' => $row['id_color'],
            'tenmau' => $row['tenmau'],
            'hex_color' => $row['hex_color'],
        ];
    }

    $colorsForProducts[$productId] = $colorsForProduct;
}

$stmtCount->close();
$stmt->close();
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
        <a href="products.php">ALL</a>
    </div>
            <?php
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
        <div id="product-info">
            <div class="product-container">
                <?php
                foreach ($productList as $product) {
                    $productId = $product['id_product'];
                    $colors = $colorsForProducts[$productId];
                    echo "<div class='product'>";
                    echo "<a href='product_detail.php?id_product=" . $product['id_product'] . "&color_id=" . $colors[0]['id_color'] . "'>";
                    echo "<img src='" . $product['link_hinh_anh'] . "' alt='" . $product['ten_san_pham'] . "'>";
                    echo "<p>" . $product['ten_san_pham'] . "</p>";
                    echo "<p class='product-price'>Giá: " . $product['gia'] . "</p>";

                    echo "<div class='color-options'>";
                    foreach ($colors as $color) {
                        $colorHex = $color['hex_color'];
                        echo "<a href='product_detail.php?id_product=" . $product['id_product'] . "&color_id=" . $color['id_color'] . "'>";
                        echo "<div class='color-option' style='background-color: $colorHex;'></div>";
                        echo "</a>";
                    }
                    echo "</div>";
                    echo "</a>";
                    echo "</div>";
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
