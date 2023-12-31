<?php
session_start();

require_once('php/db_connection.php');

$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? urldecode($_GET['loaisanpham']) : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;

$sortParam = isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : '';  // phân trang

$productsPerPage = 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

$bindParams = [];
$bindTypes = "";

// Xây dựng câu truy vấn
$sqlCountProducts = "SELECT COUNT(DISTINCT p.id_product) AS total_products FROM products p WHERE 1=1";
$sqlProducts = "SELECT p.id_product, p.id_dm, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
              FROM products p
              LEFT JOIN color c ON p.id_color = c.id_color
              WHERE 1=1";

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

$sort1 = isset($_GET['sort1']) ? $_GET['sort1'] : 'asc';
$sort2 = isset($_GET['sort2']) ? $_GET['sort2'] : '';
// Sắp xếp
$sqlProductsSorting = " ORDER BY p.gia " . ($sort1 === 'desc' ? 'DESC' : 'ASC');

if ($sort2 !== '') {
    if (is_numeric($sort2)) {
        $minPrice = max(0, $sort2 - 15); // Tính giá tối thiểu
        $sqlProducts .= " AND p.gia >= $minPrice";
    } elseif (preg_match('/(\d+)\s*\+\s*(\d+)\s*-\s*max/', $sort2, $matches)) {
        $minPrice = $matches[1];
        $diff = $matches[2];
        $maxPrice = $minPrice + $diff;
        $sqlProducts .= " AND p.gia >= $minPrice AND p.gia <= $maxPrice";
    }
}

// Lấy số lượng sản phẩm
$stmtCount = $conn->prepare($sqlCountProducts);
if (!empty($bindTypes)) {
    $stmtCount->bind_param($bindTypes, ...$bindParams);
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

// Câu truy vấn lấy sản phẩm đã sửa đổi để sử dụng sắp xếp và giới hạn kết quả
$sqlProducts .= " GROUP BY p.id_product" . $sqlProductsSorting . " LIMIT ?, ?";
$stmt = $conn->prepare($sqlProducts);

// Thêm kiểu dữ liệu 'ii' cho integer và bind tham số startIndex và productsPerPage
$bindTypes .= 'ii';
$bindParams[] = $startIndex;
$bindParams[] = $productsPerPage;

// Bây giờ bạn có thể bind tất cả các tham số cần thiết
if (!empty($bindTypes)) {
    $stmt->bind_param($bindTypes, ...$bindParams);
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
    $stmt->bind_param('s', $productId);
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
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="js/products.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,200&display=swap');
        html ,body,p{
            margin: 0;
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>

<body>
<div class="navbar">
<a href="home.php"><img src="images/logoo.png" alt="" style="width:130px; height:80px"></a>

    <div class="navbar_list"></div>
    <?php include('php/dropdown.php'); ?>
</br>
<div class="filter">
    <div method="get" id="sort-form">
    <label for="sort1">Sắp xếp:</label>
    <select name="sort1" id="sort1">
        <option value="asc" <?php echo ($sort1 === 'asc') ? 'selected' : ''; ?>>Tăng dần (ASC)</option>
        <option value="desc" <?php echo ($sort1 === 'desc') ? 'selected' : ''; ?>>Giảm dần (DESC)</option>
    </select>
</div>
<div method="get" id="sort-form">
    <label for="sort2">Sắp xếp theo giá:</label>
    <input type="range" name="sort2" id="sort2" min="0" max="10000" value="<?php echo $sort2; ?>">
    <span id="sort2-value"> Giá: <?php echo $sort2 . ' ₫'; ?></span>
</div>
</div>

<script> src ='js/products.js'</script>
<div id="product-info">
    <div class="product-container">
        <?php
        foreach ($productList as $product) {
            $productId = $product['id_product'];
            $colors = $product['colors'];

            if (!empty($colors) && isset($colors[0]['id_color'])) {
                echo '<div class="product">';
                echo '<a href="product_detail.php?id_product=' . $productId . '&color_id=' . $colors[0]['id_color'] . '">';
                echo '<img id="product-image-' . $productId . '" src="' . $colors[0]['link_hinh_anh'] . '" alt="' . $product['ten_san_pham'] . '">';
                echo '<p>' . $product['ten_san_pham'] . '</p>';
                echo '<p class="product-price">Giá: ' . $product['gia'] . ' ₫' . '</p>';

                echo '<div class="color-options">';
                foreach ($colors as $color) {
                    $colorHex = $color['hex_color'];
                    $colorId = $color['id_color'];
                    echo '<a href="product_detail.php?id_product=' . $productId . '&color_id=' . $colorId . '">';
                    echo '<div class="color-option" style="background-color: ' . $colorHex . ';" onmouseover="changeProductImage(\'' . $productId . '\', \'' . $color['link_hinh_anh'] . '\')" onmouseout="resetProductImage(\'' . $productId . '\', \'' . $colors[0]['link_hinh_anh'] . '\')"></div>';
                    echo '</a>';
                }
                echo '</div>';
                echo '</a>';
                echo '</div>';
            }
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
                <a href="products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=1<?= $sortParam ?>&sort1=<?= $sort1 ?>&sort2=<?= $sort2 ?>">&laquo; Trang đầu</a>
                </li>
                <?php
            } else {
                ?>
                <li class="pagination-item prev-page disabled">
                    <a href="#">&laquo; Trang đầu</a>
                </li>
                <?php
            }

            for ($i = $startPage; $i <= $endPage; $i++) {
                ?>
                <li class="pagination-item <?= $i == $page ? 'active' : '' ?>">
                <a href="products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=<?= $i . $sortParam ?>&sort1=<?= $sort1 ?>&sort2=<?= $sort2 ?>"><?= $i ?></a>
                </li>
                <?php
            }

            if ($page < $totalPages) {
                ?>
                <li class="pagination-item next-page">
                <a href="products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=<?= $totalPages . $sortParam ?>&sort1=<?= $sort1 ?>&sort2=<?= $sort2 ?>">Trang cuối &raquo;</a>
                </li>
                <?php
            } else {
                ?>
                <li class="pagination-item next-page disabled">
                    <a href="#">Trang cuối &raquo;</a>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
    <script src="js/products.js"></script>

</div>
<script>
function navigateToPage(page) {
    // Tạo URL mới dựa trên trang được chọn
    var newURL = "products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=" + page + "<?= $sortParam ?>";

    // Thay đổi số trang trong URL
    newURL = newURL.replace(/page=\d+/, "page=" + page);

    // Chuyển hướng đến URL mới
    window.location.href = newURL;
}
</script>

</body>
<?php require_once "footer.php"?>;
</html>
