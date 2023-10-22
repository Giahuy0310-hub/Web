<?php 
    require_once('db_connection.php'); 

$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : null;
$id_product = isset($_GET['id_product']) ? $_GET['id_product'] : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;

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

// Truy vấn để lấy top 8 sản phẩm bán chạy
$sql = "SELECT p.id_product, p.ten_san_pham, p.gia, c.tenmau, c.hex_color
        FROM products p
        LEFT JOIN color c ON p.id_color = c.id_color
        ORDER BY p.so_luong_da_ban DESC
        LIMIT 8";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$productList = [];

while ($row = $result->fetch_assoc()) {
    $productId = $row['id_product'];
    $colors = getColorsForProduct($conn, $productId);
    $productList[] = [
        'id_product' => $productId,
        'ten_san_pham' => $row['ten_san_pham'],
        'gia' => $row['gia'],
        'tenmau' => $row['tenmau'],
        'hex_color' => $row['hex_color'],
        'colors' => $colors,
    ];
}

// Truy vấn để lấy top 8 sản phẩm giá cao
$sql = "SELECT p.id_product, p.ten_san_pham, p.gia, c.tenmau, c.hex_color
        FROM products p
        LEFT JOIN color c ON p.id_color = c.id_color
        ORDER BY p.gia DESC
        LIMIT 8";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$expensiveProductList = [];

while ($row = $result->fetch_assoc()) {
    $productId = $row['id_product'];
    $colors = getColorsForProduct($conn, $productId);
    $expensiveProductList[] = [
        'id_product' => $productId,
        'ten_san_pham' => $row['ten_san_pham'],
        'gia' => $row['gia'],
        'tenmau' => $row['tenmau'],
        'hex_color' => $row['hex_color'],
        'colors' => $colors,
    ];
}

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
    <!-- <link rel="stylesheet" href="css/header.css"> -->
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/menu.css">
    <script src="js/products.js"></script>

    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
          @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,200&display=swap');
        html ,body{
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: #fff;
            width: 100%;
            position: relative;
        }
        
    </style>
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
        <div class="body">
            <div class="body_banner">
                <img src="images/banner1.jpg" alt="" id="slider">
                <div class="banner_button">
                    <button id="prev" onclick="prev()"><i class="fa-solid fa-chevron-left"></i></button>
                    <!-- <button id="next" onclick="next()"><i class="fa-solid fa-chevron-right"></i></button> -->
                </div>
            </div>
            <div class="product_new">
    <h2 id="product_title--new">SẢN PHẨM MỚI NHẤT</h2>
    <div class="list_product--new">
        <?php
        foreach ($productList as $product) {
            $productId = $product['id_product'];
            $colors = $product['colors'];
            echo '<div class="pro--new">';
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
<div class="product_button--new">
    <button id="pro_prev"><i class="fa-solid fa-chevron-left"></i></button>
    <button id="pro_next"><i class="fa-solid fa-chevron-right"></i></button>
</div>
</div>

<div class="product_sold">
                <h2 id="product_title--sold">SẢN PHẨM BÁN CHẠY NHẤT</h2>
                <div class="list_product--sold">
        <?php
        foreach ($productList as $product) {
            $productId = $product['id_product'];
            $colors = $product['colors'];
            echo '<div class="pro--sold">';
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
</div>    
                <div class="product_button--sold">
                    <button id="pro_prev--sold"><i class="fa-solid fa-chevron-left"></i></button>
                    <button id="pro_next--sold"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="product_hot">
                <h2 id="product_title--hot">SẢN PHẨM BÁN CHẠY NHẤT</h2>
                <div class="list_product--hot">
        <?php
        foreach ($expensiveProductList as $product) {
            $productId = $product['id_product'];
            $colors = $product['colors'];
            echo '<div class="pro--hot">';
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
    </div>    
                <div class="product_button--hot">
                    <button id="pro_prev--hot"><i class="fa-solid fa-chevron-left"></i></button>
                    <button id="pro_next--hot"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
<script src="js/index.js"></script>
<script>
// Hàm này sẽ thay đổi hình ảnh sản phẩm khi người dùng di chuột vào ô màu
function changeProductImage(productId, imageUrl) {
    const productImage = document.getElementById('product-image-' + productId);
    productImage.src = imageUrl;
}

// Hàm này sẽ đặt lại hình ảnh sản phẩm khi người dùng di chuột ra khỏi ô màu
function resetProductImage(productId, imageUrl) {
    const productImage = document.getElementById('product-image-' + productId);
    productImage.src = imageUrl;
}

</script>
</body>
</html>
