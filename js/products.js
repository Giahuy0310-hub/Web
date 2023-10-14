// JavaScript để hiển thị menu dropdown khi kéo xuống
var dropdowns = document.querySelectorAll('.dropdown');

dropdowns.forEach(function (dropdown) {
    dropdown.addEventListener('mouseenter', function () {
        this.classList.add('active');
    });

    dropdown.addEventListener('mouseleave', function () {
        this.classList.remove('active');
    });
});


function addColorIdToUrl(colorId) {
    // Lấy URL hiện tại
    var currentUrl = window.location.href;

    // Kiểm tra nếu đã có tham số "color_id" trong URL
    if (currentUrl.includes('color_id')) {
        // Thay thế giá trị hiện tại bằng giá trị mới
        var newUrl = currentUrl.replace(/color_id=\d+/, 'color_id=' + colorId);
        window.location.href = newUrl;
    } else {
        // Nếu chưa có tham số "color_id", thêm nó vào URL
        var separator = currentUrl.includes('?') ? '&' : '?';
        var newUrl = currentUrl + separator + 'color_id=' + colorId;
        window.location.href = newUrl;
    }
}

function changeProductColor(colorId) {
    // Lấy URL hiện tại
    var currentUrl = window.location.href;
    
    // Kiểm tra nếu đã có tham số "color_id" trong URL
    if (currentUrl.includes('color_id')) {
        // Thay thế giá trị hiện tại bằng giá trị mới
        var newUrl = currentUrl.replace(/color_id=\d+/, 'color_id=' + colorId);
        window.location.href = newUrl;
    } else {
        // Nếu chưa có tham số "color_id", thêm nó vào URL
        var separator = currentUrl.includes('?') ? '&' : '?';
        var newUrl = currentUrl + separator + 'color_id=' + colorId;
        window.location.href = newUrl;
    }
}


// Đoạn mã trong js/products.js
$(document).ready(function() {
    var displayedProducts = <?= count($productList) ?>;
    var productsPerPage = 6;
    var selectedCategory = <?= json_encode($selectedCategory) ?>;
    var selectedSubcategory = <?= json_encode($selectedSubcategory) ?>;
    
    $('#load-more').click(function() {
        $.ajax({
            type: 'GET',
            url: 'load_more.php',
            data: {
                displayed: displayedProducts,
                category: selectedCategory,
                subcategory: selectedSubcategory
            },
            success: function(data) {
                $('#product-info .product-container').append(data); // Thêm sản phẩm tải thêm vào danh sách hiện có
                displayedProducts += productsPerPage;
            }
        });
    });
});
