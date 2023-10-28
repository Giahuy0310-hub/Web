<?php
require_once('db_connection.php');

// Lấy danh sách tỉnh/thành phố
$sqlProvince = "SELECT * FROM province";
$resultProvince = $conn->query($sqlProvince);

$successMessage = '';

if (isset($_POST['submit'])) {
    // Xử lý lưu dữ liệu đơn hàng
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $province = isset($_POST['province']) ? $_POST['province'] : '';
    $district = isset($_POST['district']) ? $_POST['district'] : '';
    $wards = isset($_POST['wards']) ? $_POST['wards'] : '';
    $note = isset($_POST['note']) ? $_POST['note'] : '';

    // Sử dụng prepared statement để thêm dữ liệu vào bảng DonHang
    $sql = "INSERT INTO DonHang (hoten, sodienthoai, email, sonha_duong, tinh_thanh, quan_huyen, phuong_xa, ghichu)
    VALUES (?, ?, ?, ?, (SELECT name FROM province WHERE province_id = ?), (SELECT name FROM district WHERE district_id = ?), ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssisss", $fullname, $phone, $email, $address, $province, $district, $wards, $note);

    if ($stmt->execute()) {
        $successMessage = "Thêm vào bảng DonHang thành công!";
    } else {
        // Xử lý lỗi nếu có lỗi xảy ra
        echo "Lỗi: " . $stmt->error;
    }
}

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/footer.css">
    <script>
    setTimeout(function () {
        document.getElementById('successMessage').style.display = 'none';
    }, 5000); // 5000 milliseconds (5 seconds)
</script>
</head>
<body>
    <div class="body">
        <div class="body_information">
            <form action="" method="post">
                <legend>Thông tin vận chuyển</legend>
                <?php if (!empty($successMessage)) : ?>
                    <p id="successMessage" style="color: green;"><?php echo $successMessage; ?></p>
                <?php endif; ?>
                <table class="body_table">
                    <tr class="form_group">
                        <th class="body_table-label">
                            <label class="control-label">Họ và tên *</label>
                        </th>
                        <th class="body_table-input">
                            <input style="border-color: rgba(128, 128, 128,0.2);" type="text" name="fullname" id="fullname" required>
                        </th>
                    </tr>
                    <tr class="form_group">
                        <th class="body_table-label">
                            <label class="control-label">Số điện thoại *</label>
                        </th>
                        <th class="body_table-input">
                            <input style="border-color: rgba(128, 128, 128,0.2);" type="tel" name="phone" id="phone" required>
                        </th>
                    </tr>
                    <tr class="form_group">
                        <th class="body_table-label">
                            <label class="control-label">Email</label>
                        </th>
                        <th class="body_table-input">
                            <input style="border-color: rgba(128, 128, 128,0.2);" type="email" name="email" id="email">
                        </th>
                    </tr>
                    <tr class="form_group">
                        <th class="body_table-label">
                            <label class="control-label">Số nhà, tên đường *</label>
                        </th>
                        <th class="body_table-input">
                            <input style="border-color: rgba(128, 128, 128,0.2);" type="text" name="address" id="address" required>
                        </th>
                    </tr>
                    <tr class="form_group">
                        <th class="body_table-label">
                            <label class="control-label">Chọn tỉnh/ thành phố *</label>
                        </th>
                        <th class="select_group">
                            <select class="flied_selection" name="province" id="province" required>
                                <option value="">----Chọn tỉnh/ thành phố----</option>
                                <?php
                                // Hiển thị danh sách tỉnh/thành phố từ kết quả truy vấn
                                while ($rowProvince = $resultProvince->fetch_assoc()) {
                                    echo "<option value='" . $rowProvince['province_id'] . "'>" . $rowProvince['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </th>
                    </tr>
                    <tr class="form_group">
                        <th class="body_table-label">
                            <label class="control-label">Chọn Quận huyện *</label>
                        </th>
                        <th class="select_group">
                            <select class="flied_selection" name="district" id="district" required>
                                <option value="">----Chọn quận/huyện----</option>
                            </select>
                        </th>
                    </tr>
                    <tr class="form_group">
                        <th class="body_table-label">
                            <label class="control-label">Chọn Phường xã *</label>
                        </th>
                        <th class="select_group">
                            <select class="flied_selection" name="wards" id="wards" required>
                                <option value="">----Chọn phường/xã----</option>
                            </select>
                        </th>
                    </tr>
                    <tr class="form_group">
                        <th class="body_table-label ghi_chu">
                            <label class="control-label">Ghi chú</label>
                        </th>
                        <th class="body_table-input ghi_chu">
                            <textarea style="border-color: rgba(128, 128, 128,0.2);" name="note" id="note"></textarea>
                        </th>
                    </tr>
                </table>
                <input class="body_pay pay" type="submit" name="submit" value="Thanh Toán" >

            </form>

            <div class="body_pay">
                <legend>Hình thức thanh toán</legend>
                <div class="body_pay option">
                    <label class="option_cod" for="payment_cod">
                        <input type="radio" name="payment" id="payment_cod" value="cod">
                        <i class="fa-solid fa-house"></i>
                        <div>
                            <span>COD</span>
                            <p>Thanh Toán khi nhận hàng.</p>
                        </div>
                    </label>
                    <label class="option_banking" for="payment_banking">
                        <input type="radio" name="payment" id="payment_banking" value="banking">
                        <i class="fa-solid fa-money-check-dollar"></i>
                        <div>
                            <span>Banking</span>
                            <p>Phương thức thanh toán Online</p>
                        </div>
                    </label>

                    
                </div>
                
            </div>
        </div>
        <div class="body_products">
            <legend>Giỏ hàng của bạn</legend>
            <div class="body_products product">
    <img src="images/-34586-p.jpg" alt="">
    <div class="product_group">
        <div class="product_content">
            <h4>Áo thun Raglan in basic form Regular AT134 màu kem</h4>
            <span>295.000</span>
            <strong> x </strong>
            <span>1</span>
            =
            <span style="color: brown;">295.000</span>
        </div>
        <div class="product_selection">
            <select>
                <option value="1">S</option>
                <option value="2">M</option>
                <option value="3">L</option>
                <option value="4">XL</option>
            </select>
            x
            <input type="number" value="1" min="1" max="10">
            <button>Xóa</button>
        </div>
    </div>
</div>

<div class="body_products product">
    <img src="images/-34586-p.jpg" alt="">
    <div class="product_group">
        <div class="product_content">
            <h4>Áo thun Raglan in basic form Regular AT134 màu kem</h4>
            <span>295.000</span>
            <strong> x </strong>
            <span>1</span>
            =
            <span style="color: brown;">295.000</span>
        </div>
        <div class="product_selection">
            <select>
                <option value="1">S</option>
                <option value="2">M</option>
                <option value="3">L</option>
                <option value="4">XL</option>
            </select>
            x
            <input type="number" value="1" min="1" max="10">
            <button>Xóa</button>
        </div>
    </div>
</div>

            <div class="product_total">
                <legend>Tổng:</legend>
                <div>
                    <span>Số tiền mua sản phẩm</span>
                    <h4>295,000</h4>
                </div>
                <legend>Vận chuyển</legend>
                <div>
                    <legend id="end">Tổng tiền thanh toán</legend>
                    <h4>295,000</h4>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
    <script  src="js/donhang.js"></script>
</body>
</html>
