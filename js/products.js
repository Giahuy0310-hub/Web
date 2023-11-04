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
    if (imageUrl) {
        productImage.src = imageUrl;
    }
}

function resetProductImage(productId, imageUrl) {
    const productImage = document.getElementById('product-image-' + productId);
    if (imageUrl) {
        productImage.src = imageUrl;
    }
}





var sortDropdown1 = document.getElementById('sort1');
var sortInput = document.getElementById('sort2');
var sortForm = document.getElementById('sort-form');

// Lắng nghe sự kiện submit của form
sortForm.addEventListener('submit', function(event) {
    // Ngăn chặn sự kiện mặc định của việc gửi biểu mẫu
    event.preventDefault();

    // Lấy giá trị sort1 và sort2
    var selectedSort1 = sortDropdown1.value;
    var selectedSort2 = sortInput.value;

    // Lấy URL hiện tại
    var currentURL = window.location.href;
    var newURL = currentURL;

    // Tạo một đối tượng URLSearchParams để quản lý tham số URL
    var urlParams = new URLSearchParams(window.location.search);

    // Đặt giá trị tham số sort1 và sort2
    urlParams.set('sort1', selectedSort1);
    urlParams.set('sort2', selectedSort2);

    // Cập nhật URL mới dựa trên URLSearchParams
    newURL = newURL.split('?')[0] + '?' + urlParams.toString();

    // Thay đổi URL của trình duyệt để cập nhật tham số "sort1" và "sort2"
    window.history.pushState({ path: newURL }, '', newURL);
});

// Lắng nghe sự kiện change của dropdown sort1
sortDropdown1.addEventListener('change', function() {
    var selectedSort1 = sortDropdown1.value; // Lấy giá trị mới
    var currentURL = window.location.href; // Lấy URL hiện tại

    // Lấy URLSearchParams hiện tại
    var urlParams = new URLSearchParams(window.location.search);

    // Đặt giá trị tham số sort1
    urlParams.set('sort1', selectedSort1);

    // Tạo URL mới dựa trên URLSearchParams
    var newURL = currentURL.split('?')[0] + '?' + urlParams.toString();

    // Cập nhật URL của trình duyệt
    window.location.href = newURL;
});

// Lắng nghe sự kiện khi buông chuột sau khi kéo thanh trượt sort2
sortInput.addEventListener('mouseup', function() {
    var selectedSort1 = sortDropdown1.value; // Lấy giá trị sort1
    var selectedSort2 = sortInput.value; // Lấy giá trị sort2

    // Lấy URL hiện tại
    var currentURL = window.location.href;
    var newURL = currentURL;

    // Tạo một đối tượng URLSearchParams để quản lý tham số URL
    var urlParams = new URLSearchParams(window.location.search);

    // Đặt giá trị tham số sort1 và sort2
    urlParams.set('sort1', selectedSort1);
    urlParams.set('sort2', selectedSort2);

    // Cập nhật URL mới dựa trên URLSearchParams
    newURL = newURL.split('?')[0] + '?' + urlParams.toString();

    // Thay đổi URL của trình duyệt để cập nhật tham số "sort1" và "sort2"
    window.location.href = newURL;
});