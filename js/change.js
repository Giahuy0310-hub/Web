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


function changeProductDetails(productId, imageUrl) {
    console.log('Change details for product: ', productId);

    const productElement = document.querySelector('.pro--new[data-product-id="' + productId + '"]');
    if (productElement) {
        const productNameElement = productElement.querySelector('.product-name');
        const productPriceElement = productElement.querySelector('.product-price');
        const productImageElement = productElement.querySelector('.product-image');

        productNameElement.textContent = productElement.getAttribute('data-product-name');
        productPriceElement.textContent = 'Giá: ' + productElement.getAttribute('data-product-price');

        if (imageUrl) {
            productImageElement.src = imageUrl;
        }
    }
}

function resetProductDetails(productId, imageUrl) {
    console.log('Reset details for product: ', productId);

    const productElement = document.querySelector('.pro--new[data-product-id="' + productId + '"]');
    if (productElement) {
        const productNameElement = productElement.querySelector('.product-name');
        const productPriceElement = productElement.querySelector('.product-price');
        const productImageElement = productElement.querySelector('.product-image');

        productNameElement.textContent = productElement.getAttribute('data-product-name');
        productPriceElement.textContent = 'Giá: ' + productElement.getAttribute('data-product-price');

        if (imageUrl) {
            productImageElement.src = imageUrl;
        }
    }
}
