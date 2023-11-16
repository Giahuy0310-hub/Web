function updateQuantity(selectedQuantity, itemId, idProduct, idColor, size) {
    // Gửi yêu cầu Ajax đến máy chủ để cập nhật dữ liệu trong cơ sở dữ liệu
    $.ajax({
        type: 'POST',
        url: 'update_quantity.php',
        data: { quantity: selectedQuantity, itemId: itemId, idProduct: idProduct, idColor: idColor, size: size },
        success: function (response) {
            console.log('Dữ liệu đã được cập nhật thành công!');
            console.log('Response:', response);
        },
        error: function (error) {
            console.error('Lỗi khi cập nhật dữ liệu: ', error);
        }
    });
}
