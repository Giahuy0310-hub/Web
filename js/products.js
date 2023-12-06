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
    console.log('Change image for product: ', productId);

    const productImage = document.getElementById('product-image-' + productId);
    if (imageUrl) {
        productImage.src = imageUrl;
    }

    // Đặt tên và giá sản phẩm
    changeProductDetails(productId, imageUrl);
}

function resetProductImage(productId, imageUrl) {
    console.log('Reset image for product: ', productId);

    const productImage = document.getElementById('product-image-' + productId);
    if (imageUrl) {
        productImage.src = imageUrl;
    }

    // Đặt lại tên và giá sản phẩm
    resetProductDetails(productId, imageUrl);
}


// Function to attach event listeners for a specific category
function attachEventListeners(category) {
    const productList = document.querySelector('.list_product[data-category="' + category + '"]');
    console.log(productList);

    productList.addEventListener('mouseover', function (event) {
        const productElement = event.target.closest('.pro--' + category);
        if (productElement) {
            const productId = productElement.getAttribute('data-product-id');
            const imageUrl = productElement.getAttribute('data-image-url');
            console.log('Mouseover on product in category ' + category);

            changeProductDetails(category, productId, imageUrl);
        }
    });

    productList.addEventListener('mouseout', function (event) {
        const productElement = event.target.closest('.pro--' + category);
        if (productElement) {
            const productId = productElement.getAttribute('data-product-id');
            const imageUrl = productElement.getAttribute('data-image-url');
            console.log('Mouseout on product in category ' + category);

            resetProductDetails(category, productId, imageUrl);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Attach event listeners for different categories
    attachEventListeners('new');
    attachEventListeners('hot');
    attachEventListeners('sold');
});


var sortDropdown1 = document.getElementById('sort1');
var sortInput = document.getElementById('sort2');
var sortForm = document.getElementById('sort-form');

sortForm.addEventListener('submit', function(event) {
    event.preventDefault();

    var selectedSort1 = sortDropdown1.value;
    var selectedSort2 = sortInput.value;

    var currentURL = window.location.href;
    var newURL = currentURL;

    var urlParams = new URLSearchParams(window.location.search);

    urlParams.set('sort1', selectedSort1);
    urlParams.set('sort2', selectedSort2);

    newURL = newURL.split('?')[0] + '?' + urlParams.toString();

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

sortInput.addEventListener('mouseup', function() {
    var selectedSort1 = sortDropdown1.value; // Lấy giá trị sort1
    var selectedSort2 = sortInput.value; // Lấy giá trị sort2

    // Lấy URL hiện tại
    var currentURL = window.location.href;
    var newURL = currentURL;

    var urlParams = new URLSearchParams(window.location.search);

    urlParams.set('sort1', selectedSort1);
    urlParams.set('sort2', selectedSort2);

    newURL = newURL.split('?')[0] + '?' + urlParams.toString();

    window.location.href = newURL;
});