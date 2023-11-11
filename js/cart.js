// abc.js
// Lắng nghe sự kiện khi lựa chọn tỉnh/thành phố thay đổi
document.getElementById('province').addEventListener('change', function () {
    var selectedProvince = this.value;
    var districtSelect = document.getElementById('district');
    var wardsSelect = document.getElementById('wards');

    // Gửi một yêu cầu AJAX để lấy danh sách quận/huyện dựa trên tỉnh/thành phố được chọn
    var xhrDistrict = new XMLHttpRequest();
    xhrDistrict.open('GET', 'get.php?province=' + selectedProvince, true);

    xhrDistrict.onload = function () {
        if (xhrDistrict.status === 200) {
            var districts = JSON.parse(xhrDistrict.responseText);
            districtSelect.innerHTML = '<option value="">----Chọn quận/huyện----</option>';
            wardsSelect.innerHTML = '<option value="">----Chọn phường/xã----</option>';

            for (var i = 0; i < districts.length; i++) {
                var option = document.createElement('option');
                option.value = districts[i].district_id;
                option.text = districts[i].name;
                districtSelect.appendChild(option);
            }
        }
    };

    xhrDistrict.send();
});

// Lắng nghe sự kiện khi lựa chọn quận/huyện thay đổi
document.getElementById('district').addEventListener('change', function () {
    var selectedDistrict = this.value;
    var wardsSelect = document.getElementById('wards');

    // Gửi một yêu cầu AJAX để lấy danh sách phường/xã dựa trên quận/huyện được chọn
    var xhrWards = new XMLHttpRequest();
    xhrWards.open('GET', 'get.php?district=' + selectedDistrict, true);

    xhrWards.onload = function () {
        if (xhrWards.status === 200) {
            var wards = JSON.parse(xhrWards.responseText);
            wardsSelect.innerHTML = '<option value="">----Chọn phường/xã----</option>';

            for (var i = 0; i < wards.length; i++) {
                var option = document.createElement('option');
                option.value = wards[i].wards_id;
                option.text = wards[i].name;
                wardsSelect.appendChild(option);
            }
        }
    };

    xhrWards.send();
});

$(document).ready(function () {
    $(document).on("click", ".delete-button", function () {
        const productId = $(this).data("id_product");
        const colorId = $(this).data("id_color");
        const size = $(this).data("size");

        // Gửi yêu cầu AJAX với cả id_product, id_color, và size
        $.ajax({
            type: "POST",
            url: "xoa.php",
            data: { 
                delete_product: 1, 
                id_product_to_delete: productId, 
                id_color_to_delete: colorId, 
                size_to_delete: size 
            },
            success: function (response) {
                try {
                    const responseData = JSON.parse(response);

                    if (responseData.status === "success") {
                        // Loại bỏ sản phẩm khỏi DOM
                        $(".product[data-id_product=" + productId + "][data-id_color=" + colorId + "][data-size=" + size + "]").remove();
                        // Cập nhật tổng giá
                        updateTotalPrice();
                    } else {
                        console.error("Xóa sản phẩm không thành công. Response:", responseData);
                    }
                } catch (error) {
                    console.error("Lỗi trong quá trình xử lý phản hồi JSON:", error);
                }
            },
            error: function () {
                console.error("Lỗi trong quá trình xử lý yêu cầu xóa.");
            }
        });
    });

    function updateTotalPrice() {
        // Tính tổng giá dựa trên số sản phẩm còn lại trong giỏ hàng
        let total = 0;
        $(".product").each(function () {
            const price = parseFloat($(this).find(".product-content span").text().replace(" VNĐ", "").replace(",", ""));
            const quantity = parseInt($(this).find(".product-selection input").val());
            total += price * quantity;
        });

        // Cập nhật tổng giá trên giao diện
        $(".total-price").text(formatCurrency(total) + " VNĐ");
    }

    function formatCurrency(value) {
        return value.toLocaleString("vi-VN");
    }
});




