<?php
require_once('db_connection.php');

$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : null;
$id_product = isset($_GET['id_product']) ? $_GET['id_product'] : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;

// Số sản phẩm trên mỗi trang
$productsPerPage = 100;

// Lấy trang hiện tại từ tham số truy vấn
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// Tính chỉ số bắt đầu và kết thúc cho sản phẩm trên trang hiện tại
$startIndex = ($page - 1) * $productsPerPage;
$endIndex = $startIndex + $productsPerPage;

// Truy vấn cơ sở dữ liệu để lấy danh sách các danh mục sản phẩm từ bảng "categories"
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

        // Truy vấn cơ sở dữ liệu để lấy danh sách "loaisanpham" cho danh mục hiện tại
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

// Truy vấn cơ sở dữ liệu để lấy sản phẩm cho trang hiện tại
$sqlProducts = "SELECT p.id_product, p.id_dm, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
                FROM products p
                LEFT JOIN color c ON p.id_color = c.id_color
                WHERE 1=1";

if (!empty($selectedCategory)) {
    $sqlProducts .= " AND p.id_dm = ?";
}

if (!empty($selectedSubcategory)) {
    $sqlProducts .= " AND p.loaisanpham = ?";
}

// Thêm LIMIT vào truy vấn SQL để giới hạn kết quả trả về
$sqlProducts .= " LIMIT ?, ?";

$stmt = $conn->prepare($sqlProducts);

if (!empty($selectedCategory) && !empty($selectedSubcategory)) {
    $stmt->bind_param('iiii', $selectedCategory, $selectedSubcategory, $startIndex, $productsPerPage);
} elseif (!empty($selectedCategory)) {
    $stmt->bind_param('iii', $selectedCategory, $startIndex, $startIndex);
} elseif (!empty($selectedSubcategory)) {
    $stmt->bind_param('iii', $selectedSubcategory, $startIndex, $startIndex);
} else {
    // Nếu không có điều kiện, bạn cần chỉ bind số trang (vị trí bắt đầu) và số lượng sản phẩm trên mỗi trang
    $stmt->bind_param('ii', $startIndex, $productsPerPage);
}

$stmt->execute();

$resultProducts = $stmt->get_result();

$productList = [];
if ($resultProducts->num_rows > 0) {
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
    $stmt->close();
}

$uniqueProducts = [];

foreach ($productList as $product) {
    $productId = $product['id_product'];
    if (!isset($uniqueProducts[$productId])) {
        $uniqueProducts[$productId] = [
            'product' => $product,
            'colors' => $colorsForProducts[$productId],
        ];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Website Bán Hàng</title>
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="icon" href="3.jpg" type="image/x-icon">
</head>

<body>
    <div class="container">
        <header>
            <!-- <h1>Four men</h1> -->
        </header>

        <div class="menu">
            <div class="dropdown">
            </div>
            <?php
            // Tạo menu danh mục sản phẩm động
            foreach ($categoryList as $category) {
                $categoryID = $category['ID_DM'];
                $categoryName = $category['TenDanhMuc'];
                $isActive = $categoryID == $selectedCategory ? 'active' : '';
                $subcategoryList = $category['LoaiSanPham'];

                // Tạo danh sách loaisanpham dưới đây
                $subcategoryLinks = [];
                foreach ($subcategoryList as $subcategory) {
                    $subcategoryLink = "products.php?ID_DM=$categoryID&id_product=$id_product&color_id=$color_id&loaisanpham=" . urlencode($subcategory);
                    $isActiveSubcategory = $subcategory == $selectedSubcategory ? 'active' : '';
                    $subcategoryLinks[] = "<a class='subcategory-button $isActiveSubcategory' href='$subcategoryLink'>$subcategory</a>";
                }

                // Tạo nút danh mục chính và hiển thị danh sách loaisanpham
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
        </div>

        
        <div id="product-info">
            <div class="product-container">
                <?php
                foreach ($uniqueProducts as $uniqueProduct) {
                    $product = $uniqueProduct['product'];
                    $colors = $uniqueProduct['colors'];
                    echo "<div class='product'>";
                    echo "<a href='product_detail.php?id_product=" . $product['id_product'] . "&color_id=" . $colors[0]['id_color'] . "'>";
                    echo "<img src='" . $product['link_hinh_anh'] . "' alt='" . $product['ten_san_pham'] . "'>";
                    echo "<p>" . $product['ten_san_pham'] . "</p>";
                    echo "<p class='product-price'>Giá: " . $product['gia'] . "</p>";

                    // Hiển thị ô màu cho sản phẩm
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
            <?php
            // Tính tổng số trang dựa trên số sản phẩm và số sản phẩm trên mỗi trang
            $totalProducts = count($uniqueProducts);
            $totalPages = ceil($totalProducts / $productsPerPage);

            // Hiển thị các liên kết phân trang
            for ($i = 1; $i <= $totalPages; $i++) {
                $pageLink = "products.php?ID_DM=$selectedCategory&loaisanpham=$selectedSubcategory&page=$i";
                $isActivePage = $i == $page ? 'active-page' : '';
                echo "<a class='page-link $isActivePage' href='$pageLink'>$i</a>";
            }
            ?>
        </div>
    </div>
</body>
</html>
