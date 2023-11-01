<?php
require_once('php/db_connection.php');

$id_product = isset($_GET['id_product']) ? $_GET['id_product'] : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;

if (!is_numeric($id_product) || !is_numeric($color_id) || $id_product <= 0 || $color_id <= 0) {
    echo "Không có sản phẩm được chọn hoặc giá trị không hợp lệ.";
    exit;
}

// Thêm phần truy vấn size và quantity vào mã của bạn
$sqlProductDetail = "SELECT p.*, c.tenmau, c.hex_color, AVG(pr.rating) as sao, COUNT(pr.rating) as so_danh_gia, p.size_S, p.size_M, p.size_L
FROM products p
LEFT JOIN color c ON p.id_color = c.id_color
LEFT JOIN product_reviews pr ON p.id_product = pr.product_id
WHERE p.id_product = ? AND c.id_color = ?
GROUP BY p.id_product";

$stmt = $conn->prepare($sqlProductDetail);
$stmt->bind_param('ii', $id_product, $color_id);

$stmt->execute();
$resultProductDetail = $stmt->get_result();

if ($resultProductDetail->num_rows > 0) {
    $productDetail = $resultProductDetail->fetch_assoc();
} else {
    echo "Sản phẩm không tồn tại hoặc không có sẵn trong màu sắc này.";
    exit;
}


$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : null;

$sqlLoaisanpham = "SELECT DISTINCT loaisanpham FROM products WHERE id_product = ?";
$stmtLoaisanpham = $conn->prepare($sqlLoaisanpham);
$stmtLoaisanpham->bind_param('i', $id_product);
$stmtLoaisanpham->execute();
$resultLoaisanpham = $stmtLoaisanpham->get_result();

$loaisanphamList = [];
while ($rowLoaisanpham = $resultLoaisanpham->fetch_assoc()) {
    $loaisanphamList[] = $rowLoaisanpham['loaisanpham'];
}
$stmtLoaisanpham->close();

$tendanhmuc = ''; // Khởi tạo tên danh mục mặc định
if (empty($loaisanphamList)) {
    $sqlTendanhmuc = "SELECT tendanhmuc FROM products WHERE id_product = ?";
    $stmtTendanhmuc = $conn->prepare($sqlTendanhmuc);
    $stmtTendanhmuc->bind_param('i', $id_product);
    $stmtTendanhmuc->execute();
    $resultTendanhmuc = $stmtTendanhmuc->get_result();

    if ($resultTendanhmuc->num_rows > 0) {
        $rowTendanhmuc = $resultTendanhmuc->fetch_assoc();
        $tendanhmuc = $rowTendanhmuc['tendanhmuc'];
    }
    $stmtTendanhmuc->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/products_detail.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="js/product_detail.js"></script>
    <script src="js/products.js"></script>
</head>
<body>
    <div class="navbar">
        <a href="home.php"><img src="images/logo.png" alt=""></a>
        <div class="navbar_list">
        </div>

        <?php include('php/dropdown.php');
                echo "<div class='dropdown'>";
                ?>
        <div class="navbar_logo">
            <a href=""><i class="fa-solid fa-magnifying-glass"></i></a>
            <a href=""><i class="fa-regular fa-user"></i></a>
            <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
        </div>
    </div>
</br>
<ul class="centered-list">
    <?php
    // Hiển thị danh mục sản phẩm (loaisanpham hoặc tendanhmuc)
    if (!empty($loaisanphamList)) {
        foreach ($loaisanphamList as $loaisanpham) {
            echo "<li>" . htmlspecialchars($loaisanpham) . "</li>";
        }
    }
    ?>
</ul>


    <div class="product-detail-container">
        <?php
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
        
            // Combobox cho Size
            echo '<label for="size">Size:</label>';
            echo '<select id="size" name="size">';
            if (!empty($productDetail['size_S'])) {
                echo '<option value="S">S</option>';
            }
            if (!empty($productDetail['size_M'])) {
                echo '<option value="M">M</option>';
            }
            if (!empty($productDetail['size_L'])) {
                echo '<option value="L">L</option>';
            }
            if (!empty($productDetail['size_XL'])) {
                echo '<option value="XL">XL</option>';
            }
            // Thêm các tùy chọn size khác tại đây
            echo '</select>';
        
            // Combobox cho Số lượng
            echo '<label for="quantity">Số lượng:</label>';
echo '<select id="quantity" name="quantity">';
for ($i = 1; $i <= 5; $i++) {  // Thay đổi số lượng tùy theo nhu cầu
    if (!empty($productDetail['size_S']) && $i <= $productDetail['size_S']) {
        echo '<option value="' . $i . '">' . $i . '</option>';
    } elseif (!empty($productDetail['size_M']) && $i <= $productDetail['size_M']) {
        echo '<option value="' . $i . '">' . $i . '</option>';
    } elseif (!empty($productDetail['size_L']) && $i <= $productDetail['size_L']) {
        echo '<option value="' . $i . 
        '">' . $i . '</option>';
} elseif (!empty($productDetail['size_XL']) && $i <= $productDetail['size_XL']) {
    echo '<option value="' . $i . '">' . $i . '</option>';
}
}
// Thêm các tùy chọn số lượng khác tại đây
echo '</select>';

        
            // Thêm nút "Thêm vào giỏ hàng"
            echo '<button id="addToCartButton" onclick="addToCart(\'' . $productDetail['ten_san_pham'] . '\', ' . $productDetail['gia'] . ', ' . $productDetail['id_color'] . ', \'' . $productDetail['link_hinh_anh'] . '\', \'' . $id_product . '\')">Thêm vào giỏ hàng</button>';
        }
        ?>
    </div>
    <footer>
    </footer>
    <div id="imageModal" class="modal">
        <span class="closeModal" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" alt="Ảnh lớn" class="modal-content">
    </div>

    <script>
    function addToCart(ten_san_pham, gia, id_color, link_hinh_anh, id_product) {
        var size = document.getElementById('size').value; // Lấy giá trị size đã chọn
        var quantity = document.getElementById('quantity').value; // Lấy giá trị số lượng đã chọn

        var message = "Đã thêm sản phẩm vào giỏ hàng. Size: " + size + ", Số lượng: " + quantity;
        alert(message);

        // Tạo một đối tượng XMLHttpRequest
        var xhttp = new XMLHttpRequest();

        // Xác định phương thức POST và đường dẫn đến trang 'add.php'
        xhttp.open("POST", "add.php", true);

        // Thiết lập tiêu đề yêu cầu
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        // Định dạng dữ liệu sản phẩm
        var data = "ten_san_pham=" + encodeURIComponent(ten_san_pham) + "&gia=" + gia + "&id_color=" + id_color + "&link_hinh_anh=" + encodeURIComponent(link_hinh_anh) + "&id_product=" + id_product + "&size=" + size + "&quantity=" + quantity;

        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                // Xử lý phản hồi từ trang 'add.php' ở đây (nếu cần)
            }
        };

        // Gửi dữ liệu sản phẩm đến trang 'add.php'
        xhttp.send(data);
    }
</script>
</body>
</html>
