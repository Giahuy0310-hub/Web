<?php
$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : null;
$id_product = isset($_GET['id_product']) ? $_GET['id_product'] : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;

// Bắt đầu xây dựng câu truy vấn lấy sản phẩm
$sqlCountProducts = "SELECT COUNT(DISTINCT p.id_product) AS total_products FROM products p WHERE 1=1";
$sqlProducts = "SELECT p.id_product, p.id_dm, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
              FROM products p
              JOIN color c ON p.id_color = c.id_color
              WHERE 1=1";

if (!empty($selectedCategory)) {
    $sqlCountProducts .= " AND p.id_dm = ?";
    $sqlProducts .= " AND p.id_dm = ?";
}

if (!empty($selectedSubcategory)) {
    $sqlCountProducts .= " AND p.loaisanpham = ?";
    $sqlProducts .= " AND p.loaisanpham = ?";
}

if (!empty($color_id)) {
    $sqlCountProducts .= " AND p.id_color = ?";
    $sqlProducts .= " AND p.id_color = ?";
}

// Bây giờ bạn có thể thêm vào câu truy vấn sắp xếp nếu cần.
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'asc';

// Tiếp theo là mã xử lý số lượng sản phẩm và phân trang, giữ nguyên phần xử lý phân trang của bạn.

$sqlCategories = "SELECT ID_DM, TenDanhMuc FROM categories";
$stmt = $conn->prepare($sqlCategories);
$stmt->execute();
$resultCategories = $stmt->get_result();
$categoryList = [];

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

    while ($rowSubcategory = $resultSubcategories->fetch_assoc()) {
        $subcategories[] = $rowSubcategory['loaisanpham'];
    }

    $categoryList[] = [
        'ID_DM' => $categoryID,
        'TenDanhMuc' => $categoryName,
        'LoaiSanPham' => $subcategories,
    ];
}

foreach ($categoryList as $category) {
    $categoryID = $category['ID_DM'];
    $categoryName = $category['TenDanhMuc'];
    $isActive = $categoryID == $selectedCategory ? 'active' : '';
    $subcategoryList = $category['LoaiSanPham'];

    $subcategoryLinks = [];
    foreach ($subcategoryList as $subcategory) {
        $subcategoryLink = "products.php?ID_DM=$categoryID&loaisanpham=" . urlencode($subcategory);
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
    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
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
