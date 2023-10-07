<?php
require_once('db_connection.php'); // Đảm bảo đường dẫn tới tệp là chính xác


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

// Kiểm tra xem product_id và rating có tồn tại và hợp lệ không
if (isset($_POST['rating'])) {
    $rating = intval($_POST['rating']);
    
    if ($rating >= 1 && $rating <= 5) { // Đánh giá phải nằm trong khoảng từ 1 đến 5
        // Cập nhật đánh giá sản phẩm trong cơ sở dữ liệu
        $sqlUpdateRating = "UPDATE products SET rating = $rating WHERE ID = $product_id";
        
        if ($conn->query($sqlUpdateRating) === true) {
            // Trả về phản hồi thành công (có thể được xử lý bằng mã JavaScript ở trang sản phẩm)
            echo "Cập nhật đánh giá thành công.";
        } else {
            // Xử lý lỗi khi cập nhật đánh giá
            echo "Lỗi: " . $conn->error;
        }
    } else {
        // Xử lý lỗi khi đánh giá không hợp lệ
        echo "Đánh giá không hợp lệ. Vui lòng chọn đánh giá từ 1 đến 5.";
    }
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
                
                // Kiểm tra xem khóa "rating" có tồn tại trong mảng $productDetail không
                if (array_key_exists('rating', $productDetail)) {
                    echo "<div class='product-detail-rating'>";
                    echo "<h3>Đánh giá sản phẩm</h3>";
                    echo "<div class='star-rating'>";

                    $rating = $productDetail['rating'];

                    // Tính toán số sao nguyên và phần thập phân
                    $wholeStars = floor($rating);
                    $decimalPart = $rating - $wholeStars;

                    // Hiển thị các sao đánh giá
                    for ($i = 1; $i <= 5; $i++) {
                        $selectedClass = ($i <= $wholeStars) ? 'selected' : ''; // Đánh dấu sao đã được chọn

                        // Sử dụng biểu tượng Unicode của ngôi sao và ngôi sao trống (nếu cần)
                        echo "<span class='star $selectedClass' onclick='rateProduct($i)'>";
                        
                        if ($i <= $wholeStars) {
                            echo "&#9733;"; // Hiển thị sao đầy đủ
                        } elseif ($i - 1 < $wholeStars && $i - 0.5 > $wholeStars) {
                            echo "&#9734;&#188;"; // Hiển thị nửa phần thứ 5 của sao
                        } else {
                            echo "&#9734;"; // Hiển thị sao trống
                        }
                        
                        echo "</span>";
                    }
                    echo "</div>";
                    
                    // Kiểm tra xem khóa "so_danh_gia" có tồn tại trong mảng $productDetail không
                    if (array_key_exists('so_danh_gia', $productDetail)) {
                        echo "<p>" . number_format($productDetail['rating'], 1) . " / 5.0 (Đánh giá từ " . $productDetail['so_danh_gia'] . " người dùng)</p>";
                    }
                    
                    echo "<button class='write-review-button' onclick='showReviewForm()'>Viết đánh giá</button>";
                    echo "</div>";
                }
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
        var originalLargeImageSrc = ""; 
        var modalImageSrc = ""; 
        var ratingValue = 0.0; 
        var modalVisible = false; 

        function showLargeImage(imageSrc) {
            var largeImage = document.getElementById('largeImage');

            if (originalLargeImageSrc === "") {
                originalLargeImageSrc = largeImage.src;
            }

            modalImageSrc = imageSrc;

            // Đặt độ trong suốt của ảnh lớn thành 0 trước khi thay đổi nguồn ảnh
            largeImage.style.opacity = "0";

            // Sau một khoảng thời gian ngắn, đặt lại nguồn ảnh của ảnh lớn và độ trong suốt
            setTimeout(function () {
                largeImage.src = imageSrc;
                largeImage.style.opacity = "1"; // Đặt lại độ trong suốt
            }, 200); // 200 milliseconds

            modalVisible = true; // Đánh dấu modal đã mở
        }

        function rateProduct(rating) {
            // Gán giá trị đánh giá từ số sao đã chọn (sử dụng giá trị với thập phân)
            ratingValue = rating;

            // Xóa tất cả các lớp 'selected' trên các sao
            var stars = document.querySelectorAll('.star');
            stars.forEach(function (star) {
                star.classList.remove('selected');
            });

            // Làm tròn giá trị đánh giá với thập phân để đặt lớp 'selected' cho các sao
            var roundedRating = Math.round(rating * 2) / 2;
            var starIndex = (roundedRating - 1) * 2; // Tính toán chỉ số của sao

            for (var i = 0; i <= starIndex; i++) {
                stars[i].classList.add('selected');
            }
        }

        function openModal() {
            if (modalVisible) {
                var modal = document.getElementById('imageModal');
                modal.style.display = 'block';

                // Lấy đối tượng ảnh modal
                var modalImage = document.getElementById('modalImage');
                modalImage.src = modalImageSrc;

                // Căn giữa ảnh lớn trong modal
                modalImage.style.marginTop = (modal.clientHeight - modalImage.clientHeight) / 2 + 'px';
            }
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

        window.addEventListener('load', function () {
            closeModal();
        });
    </script>

    <!-- Thêm một form để xử lý load lại trang -->
    <form method="post">
        <input type="hidden" name="reload" value="1">
        <input type="submit" value="Reload">
    </form>
</body>
</html>
