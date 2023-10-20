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
$sql = "SELECT p.id_product, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
        FROM products p
        LEFT JOIN color c ON p.id_color = c.id_color
        ORDER BY p.so_luong_da_ban DESC
        LIMIT 8";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$productList = [];

while ($row = $result->fetch_assoc()) {
    $productList[] = [
        'id_product' => $row['id_product'],
        'ten_san_pham' => $row['ten_san_pham'],
        'link_hinh_anh' => $row['link_hinh_anh'],
        'gia' => $row['gia'],
        'tenmau' => $row['tenmau'],
        'hex_color' => $row['hex_color'],
    ];
}

// Truy vấn để lấy top 8 sản phẩm giá cao
$sql = "SELECT p.id_product, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
        FROM products p
        LEFT JOIN color c ON p.id_color = c.id_color
        ORDER BY p.gia DESC
        LIMIT 8";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$expensiveProductList = [];

while ($row = $result->fetch_assoc()) {
    $expensiveProductList[] = [
        'id_product' => $row['id_product'],
        'ten_san_pham' => $row['ten_san_pham'],
        'link_hinh_anh' => $row['link_hinh_anh'],
        'gia' => $row['gia'],
        'tenmau' => $row['tenmau'],
        'hex_color' => $row['hex_color'],
    ];
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
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
    <link rel="stylesheet" href="css/header.css">
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
            $colors = $colorsForProducts[$productId];
            echo "<div class='pro--new'>";
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
                    <?php foreach ($expensiveProductList as $product) { ?>
                        <div class="product_box--sold">
                            <div class="pro--sold">
                                <a class="item" href="">
                                    <img src="<?php echo $product['link_hinh_anh']; ?>" alt="<?php echo $product['ten_san_pham']; ?>">
                                </a>
                                <a class="item" href="">
                                    <h4><?php echo $product['ten_san_pham']; ?></h4>
                                </a>
                                <span><?php echo number_format($product['gia']); ?>₫</span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>        
                <div class="product_button--sold">
                    <button id="pro_prev--sold"><i class="fa-solid fa-chevron-left"></i></button>
                    <button id="pro_next--sold"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="product_hot">
                <h2 id="product_title--hot">SẢN PHẨM HOT</h2>
                <div class="list_product--hot">                    <?php foreach ($productList as $product) { ?>
                        <div class="product_box--hot">
                            <div class="pro--hot">
                                <a class="item" href="">
                                    <img src="<?php echo $product['link_hinh_anh']; ?>" alt="<?php echo $product['ten_san_pham']; ?>">
                                </a>
                                <a class="item" href="">
                                    <h4><?php echo $product['ten_san_pham']; ?></h4>
                                </a>
                                <span><?php echo number_format($product['gia']); ?>₫</span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>    
                <div class="product_button--hot">
                    <button id="pro_prev--hot"><i class="fa-solid fa-chevron-left"></i></button>
                    <button id="pro_next--hot"><i class="fa-solid fa-chevron-right"></i></button>
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
<footer class="footer">
<div class="footer_banner">
    <a href="">
        <img src="images/slide-map-footer-slide-19.jpg" alt="">
    </a>
    <div class="footer_main">
        <div class="footer_main--link">
            <div class="link_connect">
                <h4>kết nối với 4men</h4>
                    <div class="icon">
                        <a href=""><i class="fa-brands fa-facebook" id="link_connect facebook" style="color: rgb(70, 70, 203)"></i></a>
                        <a href=""><i class="fa-brands fa-instagram" id="link_connect instagram" style="color: purple ;"></i></a>
                        <a href=""><i class="fa-brands fa-youtube" id="link_connect youtube" style="color: rgb(255, 77, 77);"></i></a>
                    </div>
                <span for="">Nhận Email từ chúng tôi</span>
                    <div>
                        <input type="email" placeholder="Email của bạn">
                        <button>Đăng Ký</button>
                    </div>
            </div>
            <div class="link_brand">
                <h4>Thương hiệu thời trang nam 4men</h4>
                <span>Email mua hàng: khangtranmm@gmail.com</span>
                <span>Hotline: 0899.037390</span>
            </div>
            <div class="link_contact">
                <h4>về chúng tôi</h4>
                <a href="">Giới thiệu 4MEN</a>
                <a href="">Liên hệ</a>
                <a href="">Tuyển dụng</a>
                <a href="">Tin tức 4MEN</a>
            </div>
            <div class="link_helped">
                <h4>trợ giúp</h4>
                <a href="">Hưỡng dẫn mua hàng</a>
                <a href="">Hướng dẫn chọn size</a>
                <a href="">Câu hỏi thường gặp</a>
            </div>
            <div class="link_policy">
                <h4>chính sách</h4>
                <a href="">Chính sách khách VIP</a>
                <a href="">Thanh toán - Giao hàng</a>
                <a href="">Chính sách đổi hàng</a>
            </div>
        </div>
        <div class="footer_main-contact">
            <div class="contact_link">
                <a href="">Tư vấn thời trang</a>
                |
                <a href="">Cách phối đồ nam</a>
                |
                <a href="">Xu hướng thời trang</a>
                |
                <a href="">Chính sách bảo mật thông tin</a>
                |
                <a href="">Chính sách Cookie</a>
            </div>
            <img src="images/gov.png" alt="">
            <div class="contact_content">
                <h4>cty tnhh 4men group</h4>
                <span>Giấy CNĐKDN: 0899037390 - Ngày cấp 07/09/2023 - Nơi cấp: Sở kế hoạch và Đầu Tư Tp.HCM</span>
            </br>
                <span>Copyright 2023 · by 4MEN.COM.VN All rights reserved</span>
            </div>
        </div>
    </div>
</div>
</footer>
</div>

</body>
</html>