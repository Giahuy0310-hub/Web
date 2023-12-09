<?php
session_start();

require_once('php/db_connection.php');

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
$sqlTopSelling = "SELECT p.id_product, p.ten_san_pham, p.gia, c.tenmau, c.hex_color
        FROM products p
        LEFT JOIN color c ON p.id_color = c.id_color
        ORDER BY p.so_luong_da_ban DESC";

$stmtTopSelling = $conn->prepare($sqlTopSelling);
$stmtTopSelling->execute();
$resultTopSelling = $stmtTopSelling->get_result();
$productList = [];

$uniqueProductIds = []; 

while ($row = $resultTopSelling->fetch_assoc()) {
    $productId = $row['id_product'];

    if (!in_array($productId, $uniqueProductIds)) {
        $uniqueProductIds[] = $productId; 

        $colors = getColorsForProduct($conn, $productId);

        $productList[] = [
            'id_product' => $productId,
            'ten_san_pham' => $row['ten_san_pham'],
            'gia' => $row['gia'],
            'tenmau' => $row['tenmau'],
            'hex_color' => $row['hex_color'],
            'colors' => $colors,
        ];

        if (count($productList) >= 8) {
            break;
        }
    }
}

// Truy vấn để lấy top 8 sản phẩm bán chạy
$sqlTopExpensive = "SELECT p.id_product, p.ten_san_pham, p.gia, c.tenmau, c.hex_color
        FROM products p
        LEFT JOIN color c ON p.id_color = c.id_color
        ORDER BY p.gia DESC";

$stmtTopExpensive = $conn->prepare($sqlTopExpensive);
$stmtTopExpensive->execute();
$resultTopExpensive = $stmtTopExpensive->get_result();
$expensiveProductList = [];

$uniqueProductIds = [];

while ($row = $resultTopExpensive->fetch_assoc()) {
    $productId = $row['id_product'];

    if (!in_array($productId, $uniqueProductIds)) {
        $uniqueProductIds[] = $productId;

        $colors = getColorsForProduct($conn, $productId);

        $expensiveProductList[] = [
            'id_product' => $productId,
            'ten_san_pham' => $row['ten_san_pham'],
            'gia' => $row['gia'],
            'tenmau' => $row['tenmau'],
            'hex_color' => $row['hex_color'],
            'colors' => $colors,
        ];

        if (count($expensiveProductList) >= 8) {
            break;
        }
    }
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
          @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,200&display=swap');
        html ,body,a{
            margin: 0;
            font-family: 'Montserrat', sans-serif !important;
            width: 100%;
            position: relative;
        }
        
    </style>
</head>
<body>
<div class="navbar">
<a href="home.php"><img src="images/logoo.png" alt="" style="width:130px; height:80px"></a>
    <div class="navbar_list">
        
    </div>
    <?php include('php/dropdown.php'); ?>

        </div>
        </div>
        <div class="body">
            <div class="body_banner">
                <img src="images/banner1.jpg" alt="" id="slider">
                <div class="banner_button">
                    <button id="prev" onclick="prev()"><i class="fa-solid fa-chevron-left"></i></button>
                    <button id="next" onclick="next()"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
            
            <div class="product_new" id="product_new">
    <h2 id="product_title--new">SẢN PHẨM MỚI NHẤT</h2>
    <div class="list_product--new">
        <?php
foreach ($productList as $product) {
    $productId = $product['id_product'];
    $colors = $product['colors'];
    echo '<div class="pro--new">';
    echo '<a href="product_detail.php?id_product=' . $productId . '&color_id=' . $colors[0]['id_color'] . '">';
    echo '<img id="product-image-' . $productId . '-new" src="' . $colors[0]['link_hinh_anh'] . '" alt="' . (isset($product['ten_san_pham']) ? $product['ten_san_pham'] : '') . '">';
    
    // Kiểm tra xem khóa 'ten_san_pham' có tồn tại hay không
    $tenSanPham = isset($product['ten_san_pham']) ? $product['ten_san_pham'] : 'Tên sản phẩm không tồn tại';
    echo '<p>' . $tenSanPham . '</p>';
    
    echo '<p class="product-price">Giá: ' . $product['gia'] .' ₫' . '</p>';
    echo '<div class="color-options">';
    
    foreach ($colors as $color) {
        $colorHex = $color['hex_color'];
        $colorId = $color['id_color'];
        echo '<a href="product_detail.php?id_product=' . $productId . '&color_id=' . $colorId . '">';
        echo '<div class="color-option" style="background-color: ' . $colorHex . ';" onmouseover="changeProductImage(\'' . $productId . '\', \'' . $color['link_hinh_anh'] . '\', \'new\')" onmouseout="resetProductImage(\'' . $productId . '\', \'' . $colors[0]['link_hinh_anh'] . '\', \'new\')"></div>';
        echo '</a>';
    }
    
    echo '</div>';
    echo '</a>';
    echo '</div>';
}

        ?>
    </div>
    <div class="product_button--new">
                        <button id="pro_prev"><i class="fa-solid fa-chevron-left"></i></button>
                        <button id="pro_next"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
</div>

                <div class="policy">
                <div class="policy_container">
                    <div class="ship">
                        <i class="fa-solid fa-plane"></i>
                        <h4>MIỄN PHÍ VẬN CHUYỂN</h4>
                        <span>Cho đơn hàng từ 399K</span>
                    </div>
                    <div class="trade">
                        <i class="fa-solid fa-handshake"></i>
                        <h4>ĐỔI TRẢ DỄ DÀNG</h4>
                        <span>Trong vòng 15 ngày</span>
                    </div>
                    <div class="discount">
                        <i class="fa-solid fa-tag"></i>
                        <h4>ƯU ĐÃI THÀNH VIÊN</h4>
                        <span>Giảm từ 5% - 20%</span>
                    </div>
                    <div class="hotline">
                        <i class="fa-solid fa-headphones"></i>
                        <h4>HOTLINE 0899.037.390</h4>
                        <span>Hỗ trợ từ 8h30 - 22h</span>
                    </div>
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
            echo '<img id="product-image-' . $productId . '-sold" src="' . $colors[0]['link_hinh_anh'] . '" alt="' . $product['ten_san_pham'] . '">';
            echo '<p>' . $product['ten_san_pham'] . '</p>';
            echo '<p class="product-price">Giá: ' . $product['gia'] .' ₫' . '</p>';

            echo '<div class "color-options">';
            foreach ($colors as $color) {
                $colorHex = $color['hex_color'];
                $colorId = $color['id_color'];
                echo '<a href="product_detail.php?id_product=' . $productId . '&color_id=' . $colorId . '">';
                echo '<div class="color-option" style="background-color: ' . $colorHex . ';" onmouseover="changeProductImage(\'' . $productId . '\', \'' . $color['link_hinh_anh'] . '\', \'sold\')" onmouseout="resetProductImage(\'' . $productId . '\', \'' . $colors[0]['link_hinh_anh'] . '\', \'sold\')"></div>';                
                echo '</a>';
            }
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
        ?>
    </div>
    <div class="product_button--sold">
                        <button id="pro_prev--sold"><i class="fa-solid fa-chevron-left"></i></button>
                        <button id="pro_next--sold"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
</div>
            </div>
<div class="product_hot">
    <h2 id="product_title--hot">SẢN PHẨM NỔI BẬT</h2>
    <div class="list_product--hot">
        <?php
        foreach ($expensiveProductList as $product) {
            $productId = $product['id_product'];
            $colors = $product['colors'];
            echo '<div class="pro--hot">';
            echo '<a href="product_detail.php?id_product=' . $productId . '&color_id=' . $colors[0]['id_color'] . '">';
            echo '<img id="product-image-' . $productId . '-hot" src="' . $colors[0]['link_hinh_anh'] . '" alt="' . $product['ten_san_pham'] . '">';
            echo '<p>' . $product['ten_san_pham'] . '</p>';
            echo '<p class="product-price">Giá: ' . $product['gia'] .' ₫' . '</p>';

            echo '<div class="color-options">';
            foreach ($colors as $color) {
                $colorHex = $color['hex_color'];
                $colorId = $color['id_color'];
                echo '<a href="product_detail.php?id_product=' . $productId . '&color_id=' . $colorId . '">';
                echo '<div class="color-option" style="background-color: ' . $colorHex . ';" onmouseover="changeProductImage(\'' . $productId . '\', \'' . $color['link_hinh_anh'] . '\', \'hot\')" onmouseout="resetProductImage(\'' . $productId . '\', \'' . $colors[0]['link_hinh_anh'] . '\', \'hot\')"></div>';                
                echo '</a>';
            }
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
        ?>
    </div>
    <div class="product_button--hot">
                        <button id="pro_prev--hot"><i class="fa-solid fa-chevron-left"></i></button>
                        <button id="pro_next--hot"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
</div>
            </div>

<script src="js/index.js"></script>

<div class="blog">
    <h2 class="blog_title">BLOG 4MEN</h2>
    <div class="blog_container">
        <div class="blog_item">
            <div class="blog_thumb">
                <a href="">
                    <img src="images/blog1.jpg" alt="">
                </a>
            </div>
            <div class="blog_excerpt">
                <h4>[ BST SUMMER COLLECTION 2023 ] - SURF THE WAVES</h4>
                <p>Bạn đã từng có trải nghiệm như thế nào với những con sóng? Những cơn sóng nhẹ vào ngày nắng ấm, hay dữ dội vào thời những lúc thời tiết [...]</p>
            </div>
        </div>
        <div class="blog_item">
            <div class="blog_thumb">
                <a href="">
                    <img src="images/blog2.jpg" alt="">
                </a>
            </div>
            <div class="blog_excerpt">
                <h4>BLACK FRIDAY - SĂN NGAY DEAL HỜI</h4>
                <p>BLACK FRIDAY - SĂN NGAY DEAL HỜI Cơ hội duy nhất trong năm với mức #SALE KHỦNG NHẤT. - SALE [...]</p>
            </div>
        </div>
        <div class="blog_item">
            <div class="blog_thumb">
                <a href="">
                    <img src="images/blog3.jpg" alt="">
                </a>
            </div>
            <div class="blog_excerpt">
                <h4>ĐÔNG SẴN SÀNG - MUÔN VÀN DEAL XỊN</h4>
                <p>READY ANYWHERE là tên BST FW22 nhà 4MEN chúng mình. Các chàng chuẩn bị tinh thần để shopping ngay với [...]</p>
            </div>
        </div>
    </div>
</div>
</div>

</div>
</body>
<?php require_once "footer.php";?>
</html>
