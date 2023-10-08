<?php
require_once('db_connection.php'); // Đảm bảo đường dẫn tới tệp là chính xác

// Truy vấn cơ sở dữ liệu để lấy danh sách các danh mục sản phẩm từ bảng "categories"
$sqlCategories = "SELECT ID_DM, TenDanhMuc FROM categories";
$stmt = $conn->prepare($sqlCategories);
$stmt->execute();
$resultCategories = $stmt->get_result();

$categoryList = [];
if ($resultCategories->num_rows > 0) {
    while ($row = $resultCategories->fetch_assoc()) {
        $categoryList[] = [
            'ID_DM' => $row['ID_DM'],
            'TenDanhMuc' => $row['TenDanhMuc'],
        ];
    }
}

// Xác định danh mục sản phẩm và danh mục con được chọn (nếu có) từ tham số truy vấn
$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : null;

// Truy vấn cơ sở dữ liệu để lấy danh sách sản phẩm dựa trên danh mục sản phẩm và danh mục con được chọn
$sqlProducts = "SELECT * FROM products WHERE 1=1"; // Sử dụng "WHERE 1=1" để dễ dàng thêm điều kiện

if (!empty($selectedCategory)) {
    $sqlProducts .= " AND ID_DM = ?";
}

if (!empty($selectedSubcategory)) {
    $sqlProducts .= " AND loaisanpham = ?";
}

$stmt = $conn->prepare($sqlProducts);

if (!empty($selectedCategory) && !empty($selectedSubcategory)) {
    $stmt->bind_param('is', $selectedCategory, $selectedSubcategory);
} elseif (!empty($selectedCategory)) {
    $stmt->bind_param('i', $selectedCategory);
} elseif (!empty($selectedSubcategory)) {
    $stmt->bind_param('s', $selectedSubcategory);
}

$stmt->execute();
$resultProducts = $stmt->get_result();

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
}

// Truy vấn danh mục con dựa trên cột "loaisanpham" trong bảng "products" và giá trị ID_DM
$subcategories = [];
foreach ($categoryList as $category) {
    $categoryID = $category['ID_DM'];
    $sqlSubcategories = "SELECT DISTINCT loaisanpham FROM products WHERE ID_DM = ?";
    $stmt = $conn->prepare($sqlSubcategories);
    $stmt->bind_param('i', $categoryID);
    $stmt->execute();
    $resultSubcategories = $stmt->get_result();

    if ($resultSubcategories->num_rows > 0) {
        $subcategoryList = [];
        while ($row = $resultSubcategories->fetch_assoc()) {
            $subcategoryList[] = $row['loaisanpham'];
        }
        $subcategories[$categoryID] = $subcategoryList;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Website Bán Hàng</title>
    <link rel="stylesheet" href="css/products.css">
    <link rel="icon" href="1.jpg" type="image/x-icon">
</head>
<body>
    <div class="container">
        <header>
            <h1>Four men</h1>
        </header>
        <ul>
            <li><a href="products.php">Home</a></li>
            <li>Contact</li>
            <li>About</li>
        </ul>
        <div class="menu">
            <div class="dropdown">
            </div>
            <?php
            // Tạo menu danh mục sản phẩm động
            foreach ($categoryList as $category) {
                $categoryID = $category['ID_DM'];
                $categoryName = $category['TenDanhMuc'];
                $isActive = $categoryID == $selectedCategory ? 'active' : '';
                echo "<div class='dropdown'>";
                echo "<a class='category-button $isActive' href='products.php?ID_DM=$categoryID'>$categoryName</a>";
                
                // Hiển thị danh mục con nếu có
                if (isset($subcategories[$categoryID])) {
                    echo "<div class='dropdown-menu'>";
                    foreach ($subcategories[$categoryID] as $subcategory) {
                        $subcategoryLink = "products.php?ID_DM=$categoryID&loaisanpham=" . urlencode($subcategory);
                        echo "<a href='$subcategoryLink'>$subcategory</a>";
                    }
                    echo "</div>";
                }
                
                echo "</div>";
            }
            ?>
        </div>

        <div id="product-info">
            <div class="product-container">
                <?php
                foreach ($productList as $product) {
                    echo "<div class='product'>";
                    echo "<a href='product_detail.php?product_id=" . $product['ID'] . "'>";
                    echo "<img src='" . $product['LinkHinhAnh'] . "' alt='" . $product['TenSanPham'] . "'>";
                    echo "<p>" . $product['TenSanPham'] . "</p>";
                    echo "<p class='product-price'>Giá: " . $product['Gia'] . "</p>";
                    echo "</a>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <footer>
            <p>&copy; 2023 Website Bán Hàng</p>
        </footer>
    </div>
    <script src="js/products.js"></script>
</body>
</html>
