$(document).ready(function () {
    $('.quantity-dropdown').change(function () {
        var selectedQuantity = $(this).val();
        var itemId = $(this).data('item-id');

        // Gửi yêu cầu Ajax đến máy chủ để cập nhật dữ liệu trong cơ sở dữ liệu
        $.ajax({
            type: 'POST',
            url: 'ft/update_quantity.php',
            data: { quantity: selectedQuantity, itemId: itemId },
            success: function (response) {
                console.log('Dữ liệu đã được cập nhật thành công!');
                console.log('Response:', response);
                // location.reload();

            },
            error: function (error) {
                console.error('Lỗi khi cập nhật dữ liệu: ', error);
            }
        });
    });
});