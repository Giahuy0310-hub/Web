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

// // Sử dụng JavaScript để lấy thông tin màu sắc từ các thẻ <a> và hiển thị chúng

// productContainer.addEventListener("mouseenter", function (event) {
//     const targetLink = event.target.closest(".product");

//     if (targetLink) {
//         const colorsData = targetLink.getAttribute("data-colors");

//         // Chuyển dữ liệu màu sắc từ chuỗi JSON thành mảng JavaScript
//         const colors = JSON.parse(colorsData);

//         // Tạo các ô màu sắc cho sản phẩm
//         const colorOptions = document.createElement("div");
//         colorOptions.classList.add("color-options");

//         colors.forEach(function (color) {
//             const colorOption = document.createElement("div");
//             colorOption.classList.add("color-option");
//             colorOption.style.backgroundColor = color.hex_color;
//             colorOptions.appendChild(colorOption);

//             // Thêm sự kiện click vào ô màu sắc để chọn màu
//             colorOption.addEventListener("click", function () {
//                 // Lấy ID sản phẩm từ thuộc tính data-product-id của ô màu sắc
//                 const productId = colorOption.dataset.productId;

//                 // Lấy màu sắc từ thuộc tính data-color của ô màu sắc
//                 const color = colorOption.dataset.color;

//                 // Lấy URL hiện tại
//                 const currentURL = new URL(window.location.href);

//                 // Cập nhật tham số 'tenmau' trong URL với màu sắc đã chọn
//                 currentURL.searchParams.set('tenmau', color);

//                 window.location.href = currentURL.toString();
//             });
//         });

//         targetLink.appendChild(colorOptions);
//     }
// });


// // Lấy tất cả các ô màu sắc có class "color-option-clickable"
// const colorOptions = document.querySelectorAll(".color-option-clickable");

// // Lặp qua danh sách các ô màu sắc và thêm sự kiện click vào chúng
// colorOptions.forEach(function (colorOption) {
//     colorOption.addEventListener("click", function () {
//         // Lấy màu sắc từ thuộc tính data-color của ô màu sắc
//         const color = colorOption.dataset.color;
        
//         // Lấy ID sản phẩm từ thuộc tính data-product-id của ô màu sắc
//         const productId = colorOption.dataset.productId;
        
//         // Tạo URL của trang chi tiết sản phẩm với màu sắc và ID sản phẩm
//         const productDetailURL = `product_detail.php?id=${productId}&tenmau=${encodeURIComponent(color)}`;
        
//         // Chuyển hướng người dùng đến trang chi tiết sản phẩm với màu sắc và ID sản phẩm
//         window.location.href = productDetailURL;
//     });
// });


// const urlParams = new URLSearchParams(window.location.search);
// const color = urlParams.get("tenmau");

// if (color) {
//     const productImages = document.querySelectorAll(".small-image");
    
//     productImages.forEach(function (image) {
//         image.style.backgroundColor = decodeURIComponent(color);
//     });
// }
