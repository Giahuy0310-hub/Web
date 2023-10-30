<?php
require_once('php/db_connection.php');

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

$sql = "SELECT * FROM giohang1";
$result = $conn->query($sql);

$cartItems = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script>
        setTimeout(function () {
            document.getElementById('successMessage').style.display = 'none';
        }, 5000); // 5000 milliseconds (5 seconds)
    </script>
</head>
<body>
<div class="navbar">
    <a href="home.php"><img src="images/logo.png" alt=""></a>
    <div class="navbar_list">
        
    </div>
    <?php include('php/dropdown.php'); ?>

        </div>
        </div>

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

    <?php
$totalPrice = 0;

if (isset($cartItems) && is_array($cartItems)) :
    foreach ($cartItems as $item) :
        // Calculate the subtotal for each item
        $subtotal = isset($item['quantity']) ? $item['gia'] * $item['quantity'] : 0;

        // Add the subtotal to the total price
        $totalPrice += $subtotal;
?>
        <div class="body_products product">
            <img src="<?= $item['link_hinh_anh'] ?>" alt="<?= $item['ten_san_pham'] ?>">
            <div class="product_group">
                <div class="product_content">
                    <h4><?= $item['ten_san_pham'] ?></h4>
                    <span><?= number_format($item['gia'], 0, ',', '.') ?> VNĐ</span>
                    <strong> x </strong>
                    <span><?= isset($item['quantity']) ? $item['quantity'] : '0' ?></span>
                    =
                    <span style="color: brown;">
                        <?= number_format($subtotal, 0, ',', '.') ?> VNĐ
                    </span>
                </div>
                <div class="product_selection">
                    <select>
                        <?php if (isset($item['color_name'])) : ?>
                            <option value="<?= $item['id_color'] ?>"><?= $item['color_name'] ?></option>
                        <?php else : ?>
                            <option>Color Not Available</option>
                        <?php endif; ?>
                    </select>
                    x
                    <input type="number" value="<?= isset($item['quantity']) ? $item['quantity'] : '0' ?>" min="1" max="10">
                    <button>Xóa</button>
                </div>
            </div>
        </div>
<?php
    endforeach;
endif;
?>

<div class="product_total">
    <legend>Tổng:</legend>
    <div>
        <span>Số tiền mua sản phẩm</span>
        <h4><?= number_format($totalPrice, 0, ',', '.') ?> VNĐ</h4>
    </div>
    <legend>Vận chuyển</legend>
    <div>
        <legend id="end">Tổng tiền thanh toán</legend>
        <h4><?= number_format($totalPrice, 0, ',', '.') ?> VNĐ</h4>
    </div>
</div>

</div>

    </div>
        <script  src="js/cart.js"></script>

</body>
</html>
