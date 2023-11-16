$(document).ready(function () {
    $('.size-dropdown').change(function () {
        var selectedSize = $(this).val();
        var itemId = $(this).data('item-id'); // Lấy giá trị data-item-id

        // Gửi yêu cầu Ajax đến máy chủ để cập nhật dữ liệu trong cơ sở dữ liệu
        $.ajax({
            type: 'POST',
            url: 'update_size.php',
            data: { size: selectedSize, itemId: itemId },
            success: function (response) {
                console.log('Dữ liệu đã được cập nhật thành công!');
                console.log('Response:', response); // Thêm dòng này để xem response
            },
            error: function (error) {
                console.error('Lỗi khi cập nhật dữ liệu: ', error);
            }
        });
    });
});