// JavaScript để hiển thị menu dropdown khi kéo xuống
// var dropdowns = document.querySelectorAll('.dropdown');

// dropdowns.forEach(function (dropdown) {
//     dropdown.addEventListener('mouseenter', function () {
//         this.classList.add('active');
//     });

//     dropdown.addEventListener('mouseleave', function () {
//         this.classList.remove('active');
//     });
// });


// function addColorIdToUrl(colorId) {
//     // Lấy URL hiện tại
//     var currentUrl = window.location.href;

//     // Kiểm tra nếu đã có tham số "color_id" trong URL
//     if (currentUrl.includes('color_id')) {
//         // Thay thế giá trị hiện tại bằng giá trị mới
//         var newUrl = currentUrl.replace(/color_id=\d+/, 'color_id=' + colorId);
//         window.location.href = newUrl;
//     } else {
//         // Nếu chưa có tham số "color_id", thêm nó vào URL
//         var separator = currentUrl.includes('?') ? '&' : '?';
//         var newUrl = currentUrl + separator + 'color_id=' + colorId;
//         window.location.href = newUrl;
//     }
// }

// function changeProductColor(colorId) {
//     // Lấy URL hiện tại
//     var currentUrl = window.location.href;
    
//     // Kiểm tra nếu đã có tham số "color_id" trong URL
//     if (currentUrl.includes('color_id')) {
//         // Thay thế giá trị hiện tại bằng giá trị mới
//         var newUrl = currentUrl.replace(/color_id=\d+/, 'color_id=' + colorId);
//         window.location.href = newUrl;
//     } else {
//         // Nếu chưa có tham số "color_id", thêm nó vào URL
//         var separator = currentUrl.includes('?') ? '&' : '?';
//         var newUrl = currentUrl + separator + 'color_id=' + colorId;
//         window.location.href = newUrl;
//     }
// }




if (window.location.search) {
    const params = new URLSearchParams(window.location.search);
    if (!params.get('id_product')) {
        params.delete('id_product');
    }
    if (!params.get('color_id')) {
        params.delete('color_id');
    }
    if (!params.get('ID_DM')) {
        params.delete('ID_DM');
    }
    if (!params.get('loaisanpham')) {
        params.delete('loaisanpham');
    }
    const newUrl = `${window.location.pathname}${params.toString() ? `?${params.toString()}` : ''}`;
    window.history.replaceState({}, '', newUrl);
}


function changeProductImage(productId, imageUrl) {
    const productImage = document.getElementById('product-image-' + productId);
    productImage.src = imageUrl;
}


function resetProductImage(productId, imageUrl) {
    const productImage = document.getElementById('product-image-' + productId);
    productImage.src = imageUrl;
}

function navigateToPage(page) {
    // Tạo URL mới dựa trên trang được chọn
    var newURL = "products.php?ID_DM=<?= $selectedCategory ?>&loaisanpham=<?= $selectedSubcategory ?>&page=" + page + "<?= $sortParam ?>";

    // Thay đổi số trang trong URL
    newURL = newURL.replace(/page=\d+/, "page=" + page);

    // Chuyển hướng đến URL mới
    window.location.href = newURL;
}
