<?php
require_once('db_connection.php'); // Đảm bảo đường dẫn tới tệp là chính xác

// Trích xuất tham số id_product và color_id từ URL
$id_product = isset($_GET['id_product']) ? $_GET['id_product'] : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;

// Kiểm tra xem id_product và color_id có giá trị hợp lệ không
if (!is_numeric($id_product) || !is_numeric($color_id) || $id_product <= 0 || $color_id <= 0) {
    // Xử lý khi không có hoặc có giá trị id_product hoặc color_id không hợp lệ
    echo "Không có sản phẩm được chọn hoặc giá trị không hợp lệ.";
    exit; // Kết thúc kịch bản để không tiếp tục xử lý
}

// Truy vấn cơ sở dữ liệu để lấy thông tin sản phẩm từ bảng "products" và "color"
$sqlProductDetail = "SELECT p.*, c.tenmau, c.hex_color
                    FROM products p
                    LEFT JOIN color c ON p.id_color = c.id_color
                    WHERE p.id_product = ? AND c.id_color = ?";
$stmt = $conn->prepare($sqlProductDetail);
$stmt->bind_param('ii', $id_product, $color_id);
$stmt->execute();
$resultProductDetail = $stmt->get_result();

// Kiểm tra xem sản phẩm có tồn tại không
if ($resultProductDetail->num_rows > 0) {
    $productDetail = $resultProductDetail->fetch_assoc();
} else {
    // Xử lý khi không tìm thấy thông tin sản phẩm
    echo "Sản phẩm không tồn tại hoặc không có sẵn trong màu sắc này.";
    exit;
}

$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : null;

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

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/products_detail.css">
    <link rel="stylesheet" href="css/menu.css">

    <link rel="icon" href="3.jpg" type="image/x-icon">
    <script src="js/product_detail.js"></script>
</head>
<body>
    <div class="container">
        <header>

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
        <ul>
    <li><?php echo htmlspecialchars($selectedSubcategory); ?></li>
</ul>

        <div class="product-detail-container">
            <?php
            // Kiểm tra nếu trang được tải lại
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reload'])) {
                echo '<script>modalVisible = false;</script>';
            }

            if (isset($productDetail)) {
                echo "<div class='additional-data'>";
                $imgFields = ['img1', 'img2', 'img3', 'img4'];

                foreach ($imgFields as $index => $imgField) {
                    $imageURL = $productDetail[$imgField];
                    $altText = "Hình ảnh " . ($index + 1);
                    echo "<img class='small-image' src='$imageURL' alt='$altText' onclick='showLargeImage(\"$imageURL\")'>";
                }

                echo "</div>";
                echo "<div class='product-detail-image'>";
                echo "<img id='largeImage' src='" . $productDetail['link_hinh_anh'] . "' alt='" . $productDetail['ten_san_pham'] . "' onclick='openModal()'>";
                echo "</div>";
                echo "<div class='product-detail-info'>";
                echo "<h2>" . $productDetail['ten_san_pham'] . "</h2>";
                echo "<div class='product-detail-price'>Giá: " . $productDetail['gia'] . "</div>";
                echo "</div>";
            }
            ?>
        </div>
        <footer>
            <p>&copy; 2023 Website Bán Hàng</p>
        </footer>
    </div>

    <!-- Modal cho ảnh lớn -->
    <div id="imageModal" class="modal">
        <span class="closeModal" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" alt="Ảnh lớn" class="modal-content">
    </div>

    <!-- Thêm một form để xử lý load lại trang -->
    <form method="post">
        <input type="hidden" name="reload" value="1">
        <input type="submit" value="Reload">
    </form>
</body>
</html>
