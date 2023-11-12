<?php
require_once('php/db_connection.php');

$id_product = isset($_GET['id_product']) ? $_GET['id_product'] : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;

// Validate input
if (empty($id_product) || !is_numeric($color_id) || $color_id <= 0) {
    echo "Không có sản phẩm được chọn hoặc giá trị không hợp lệ.";
    exit;
}

// Thêm phần truy vấn size và quantity vào mã của bạn
$sqlProductDetail = "SELECT p.*, c.tenmau, c.hex_color,p.size_S, p.size_M, p.size_L
FROM products p
LEFT JOIN color c ON p.id_color = c.id_color
WHERE p.id_product = ? AND c.id_color = ?
GROUP BY p.id_product";

$stmt = $conn->prepare($sqlProductDetail);

// Bind parameters
$stmt->bind_param('si', $id_product, $color_id);

$stmt->execute();
$resultProductDetail = $stmt->get_result();

if ($resultProductDetail->num_rows > 0) {
    $productDetail = $resultProductDetail->fetch_assoc();
    print_r($productDetail);  // Add this line for debugging
} else {
    echo "Không tìm thấy thông tin cho sản phẩm hoặc màu sắc đã chọn.";
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

// Echo the "Thêm vào giỏ hàng" button
echo '<button id="addToCartButton" class="add-to-cart-button" onclick="addToCart(\'' . $productDetail['ten_san_pham'] . '\', ' . $productDetail['gia'] . ', ' . $productDetail['id_color'] . ', \'' . $productDetail['link_hinh_anh'] . '\', \'' . $id_product . '\')">
<span class="text">Thêm vào giỏ hàng</span>
<i class="cart-icon fa-solid fa-cart-shopping"></i>
<div class="shirt-icon">
  <img src="images/icons8-clothes-50.png" alt="Áo" />
</div>
</button>';

      }
        ?>
    </div>
    <footer>
    </footer>
    <!-- <div id="imageModal" class="modal">
        <span class="closeModal" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" alt="Ảnh lớn" class="modal-content">
    </div> -->

    <script>
function addToCart(ten_san_pham, gia, id_color, link_hinh_anh, id_product) {
  var size = document.getElementById('size').value;
  var quantity = document.getElementById('quantity').value;

  var xhttp = new XMLHttpRequest();

  xhttp.open('POST', 'add.php', true);

  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

  var data =
    'ten_san_pham=' +
    encodeURIComponent(ten_san_pham) +
    '&gia=' +
    gia +
    '&id_color=' +
    id_color +
    '&link_hinh_anh=' +
    encodeURIComponent(link_hinh_anh) +
    '&id_product=' +
    id_product +
    '&size=' +
    size +
    '&quantity=' +
    quantity;

  const button = document.getElementById('addToCartButton');
  const cartIcon = button.querySelector('.cart-icon');
  const text = button.querySelector('.text');
  const shirtIcon = button.querySelector('.shirt-icon');

  // Ẩn văn bản "Thêm vào giỏ hàng" bằng opacity
  text.style.opacity = 0;

  cartIcon.style.transition = 'left 4s';
  cartIcon.style.left = '100%';

  // Bắt đầu sự kiện rơi của chiếc áo
  shirtIcon.style.animation = 'fallFromTop 2s ease-in-out';
  shirtIcon.style.animationFillMode = 'forwards';

  setTimeout(function () {
    button.classList.remove('clicked');
    cartIcon.style.transition = 'left 0s';
    cartIcon.style.left = '0';

    // Hiển thị văn bản lại bằng opacity
    text.style.opacity = 1;
  }, 4000);

  xhttp.onreadystatechange = function () {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
    }
  };

  xhttp.send(data);
}
</script>
</body>
</html>
