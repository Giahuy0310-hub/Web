<?php
// Kết nối đến cơ sở dữ liệu (giống như bạn đã làm trong trang products.php)
$servername = "localhost";
$username = "root";
$password = "";
$database = "testt";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

// Trích xuất tham số product_id từ URL
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

// Truy vấn sản phẩm dựa trên product_id
if ($product_id) {
    $sqlProductDetail = "SELECT * FROM products WHERE ID = $product_id";
    $resultProductDetail = $conn->query($sqlProductDetail);

    // Kiểm tra xem sản phẩm có tồn tại không
    if ($resultProductDetail->num_rows > 0) {
        $productDetail = $resultProductDetail->fetch_assoc();
    } else {
        // Xử lý khi không tìm thấy sản phẩm
        echo "Sản phẩm không tồn tại.";
    }
} else {
    // Xử lý khi không có tham số product_id
    echo "Không có sản phẩm được chọn.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chi Tiết Sản Phẩm</title>
    <link rel="stylesheet" href="css/products_detail.css">

</head>
<body>
    <div class="container">
        <header>
            <h1>Chi Tiết Sản Phẩm</h1>
        </header>
        <ul>
            <li><a href="products.php">Trang chủ</a></li>
            <li>Liên hệ</li>
            <li>Giới thiệu</li>
        </ul>
        <div class="product-detail-container">
            <?php
            if (isset($productDetail)) {
                echo "<div class='additional-data'>";
                // Chèn 4 hình ảnh vào đây
                echo "<img class='small-image' src='https://4men.com.vn/images/thumbs/2023/08/ao-so-mi-modal-tron-chong-nhan-form-regular-sm137-mau-trang-18201-slide-products-64d36830e4e6e.jpg' alt='Hình ảnh 1' onclick='showLargeImage(\"https://4men.com.vn/images/thumbs/2023/08/ao-so-mi-modal-tron-chong-nhan-form-regular-sm137-mau-trang-18201-slide-products-64d36830e4e6e.jpg\")'>";
                echo "<img class='small-image' src='https://4men.com.vn/images/thumbs/2023/08/ao-so-mi-modal-tron-chong-nhan-form-regular-sm137-mau-trang-18201-slide-products-64d36830d4887.jpg' alt='Hình ảnh 2' onclick='showLargeImage(\"https://4men.com.vn/images/thumbs/2023/08/ao-so-mi-modal-tron-chong-nhan-form-regular-sm137-mau-trang-18201-slide-products-64d36830e4e6e.jpg\")'>";
                echo "<img class='small-image' src='https://4men.com.vn/images/thumbs/2023/08/ao-so-mi-modal-tron-chong-nhan-form-regular-sm137-mau-trang-18201-slide-products-64d36830c5d60.jpg' alt='Hình ảnh 3' onclick='showLargeImage(\"https://4men.com.vn/images/thumbs/2023/08/ao-so-mi-modal-tron-chong-nhan-form-regular-sm137-mau-trang-18201-slide-products-64d36830c5d60.jpg\")'>";
                echo "<img class='small-image' src='https://4men.com.vn/images/thumbs/2023/08/ao-so-mi-modal-tron-chong-nhan-form-regular-sm137-mau-trang-18201-slide-products-64d3683134cf4.jpg' alt='Hình ảnh 4' onclick='showLargeImage(\"https://4men.com.vn/images/thumbs/2023/08/ao-so-mi-modal-tron-chong-nhan-form-regular-sm137-mau-trang-18201-slide-products-64d36830e4e6e.jpg\")'>";
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

<script>
    var originalLargeImageSrc = ""; // Biến trung gian lưu trữ nguồn ảnh lớn ban đầu
    var modalImageSrc = ""; // Biến để lưu trữ nguồn ảnh của ảnh modal

    function showLargeImage(imageSrc) {
        // Lấy đối tượng ảnh lớn
        var largeImage = document.getElementById('largeImage');

        if (originalLargeImageSrc === "") {
            originalLargeImageSrc = largeImage.src;
        }

        // Lưu nguồn ảnh của ảnh modal
        modalImageSrc = imageSrc;

        // Thay đổi nguồn ảnh của ảnh lớn thành nguồn ảnh được click
        largeImage.src = imageSrc;
    }

    function resetLargeImage() {
        // Đặt lại ảnh lớn về nguồn ảnh ban đầu
        var largeImage = document.getElementById('largeImage');
        largeImage.src = originalLargeImageSrc;
    }

    function openModal() {
    var modal = document.getElementById('imageModal');
    modal.style.display = 'block';

    // Lấy đối tượng ảnh modal
    var modalImage = document.getElementById('modalImage');
    modalImage.src = modalImageSrc;

    // Căn giữa ảnh lớn trong modal
    modalImage.style.marginTop = (modal.clientHeight - modalImage.clientHeight) / 2 + 'px';
}

function closeModal() {
    var modal = document.getElementById('imageModal');
    modal.style.display = 'none';

    // Đặt lại nguồn ảnh lớn về nguồn ảnh lớn hiện tại
    originalLargeImageSrc = document.getElementById('largeImage').src;

    // Đặt lại nguồn ảnh của ảnh modal và margin-top
    var modalImage = document.getElementById('modalImage');
    modalImage.src = "";
    modalImage.style.marginTop = '0';
}
</script>


</body>
</html>
