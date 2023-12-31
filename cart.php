<?php
session_start();

require_once('php/db_connection.php');

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header("Location: login.html");
    exit;
}

// Kiểm tra người dùng trong bảng login
$sqlCheckUser = "SELECT * FROM login WHERE id = ?";
$stmtCheckUser = $conn->prepare($sqlCheckUser);
$stmtCheckUser->bind_param('i', $user_id);
$stmtCheckUser->execute();
$resultCheckUser = $stmtCheckUser->get_result();

if ($resultCheckUser->num_rows > 0) {
    // Người dùng tồn tại trong bảng login
    // Tiếp tục xử lý mã của bạn
} else {
    echo "Người dùng không tồn tại.";
    exit;
}

$stmtCheckUser->close();

// Fetch provinces for dropdown
$sqlProvince = "SELECT * FROM province";
$resultProvince = $conn->query($sqlProvince);

// Fetch cart items
$sqlCart = "SELECT * FROM giohang WHERE nguoidung_id = ?";
$stmtCart = $conn->prepare($sqlCart);
$stmtCart->bind_param('i', $user_id);
$stmtCart->execute();
$resultCart = $stmtCart->get_result();

$cartItems = array();

if ($resultCart->num_rows > 0) {
    while ($row = $resultCart->fetch_assoc()) {
        $cartItems[] = $row;
    }
}

if (isset($_POST['submit'])) {
    // Check all required fields in $_POST to ensure they exist and are not empty
    $requiredFields = ['fullname', 'phone', 'email', 'address', 'province', 'district', 'wards', 'note', 'totalPrice'];
    $validData = true;

    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $validData = false;
            break;
        }
    }

    if ($validData) {
        $fullname = $_POST['fullname'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $province = $_POST['province'];
        $district = $_POST['district'];
        $wards = $_POST['wards'];
        $note = $_POST['note'];
        $totalPrice = $_POST['totalPrice'];

        $conn->begin_transaction();

        // Assuming $date is declared and assigned somewhere
        $date = date("Y-m-d H:i:s");

        if (createOrder($conn, $fullname,$user_id, $phone, $email, $address, $province, $district, $wards, $note, $totalPrice, $date, $cartItems)) {
            $successMessage = "Đơn hàng đã được đặt thành công! Số đơn hàng của bạn là: " . $donHangId . ". Tổng tiền: " . $totalPrice . ". Thời gian đặt hàng: " . $date;
        } else {
            // Handle errors
            echo "Lỗi: Không thể tạo đơn hàng.";
            $conn->rollback(); // Rollback the transaction
        }
    } else {
        // Handle errors
        echo "Lỗi: Dữ liệu không hợp lệ.";
    }
}
function createOrder($conn, $fullname,$user_id, $phone, $email, $address, $province, $district, $wards, $note, $totalPrice, $date, $cartItems) {
    $sqlInsertIntoDonHang = "INSERT INTO DonHang (hoten, id_nguoidung, sodienthoai, email, sonha_duong, tinh_thanh, quan_huyen, phuong_xa, ghichu, totalPrice, date)
        VALUES (?, ?, ?, ?,?, (SELECT name FROM province WHERE province_id = ? LIMIT 1), (SELECT name FROM district WHERE district_id = ? LIMIT 1), (SELECT name FROM wards WHERE wards_id = ? LIMIT 1), ?, ?, ?)";

    $stmtInsertIntoDonHang = $conn->prepare($sqlInsertIntoDonHang);
    $stmtInsertIntoDonHang->bind_param("sssssssssds", $fullname,$user_id, $phone, $email, $address, $province, $district, $wards, $note, $totalPrice, $date);

    if ($stmtInsertIntoDonHang->execute()) {
        $donHangId = $stmtInsertIntoDonHang->insert_id;

        // Initialize an array to store total quantities for each size
        $totalQuantities = ['S' => 0, 'M' => 0, 'L' => 0, 'XL' => 0];

        // Calculate total quantities for each size
        foreach ($cartItems as $item) {
            $totalQuantities[$item['size']] += (int) $item['quantity'];
        }

        // Update quantity for each size
        $sqlUpdateQuantity = "UPDATE products 
            SET size_S = GREATEST(0, size_S - ?)
            WHERE id_product = ? AND id_color = ?";

        $stmtUpdateQuantityS = $conn->prepare($sqlUpdateQuantity);

        $sqlUpdateQuantity = "UPDATE products 
            SET size_M = GREATEST(0, size_M - ?)
            WHERE id_product = ? AND id_color = ?";

        $stmtUpdateQuantityM = $conn->prepare($sqlUpdateQuantity);

        $sqlUpdateQuantity = "UPDATE products 
            SET size_L = GREATEST(0, size_L - ?)
            WHERE id_product = ? AND id_color = ?";

        $stmtUpdateQuantityL = $conn->prepare($sqlUpdateQuantity);

        $sqlUpdateQuantity = "UPDATE products 
            SET size_XL = GREATEST(0, size_XL - ?)
            WHERE id_product = ? AND id_color = ?";

        $stmtUpdateQuantityXL = $conn->prepare($sqlUpdateQuantity);

        // Loop through cart items and execute the corresponding update statement for each size
        foreach ($cartItems as $item) {
            switch ($item['size']) {
                case 'S':
                    $stmtUpdateQuantityS->bind_param("iii", $item['quantity'], $item['id_product'], $item['id_color']);
                    $stmtUpdateQuantityS->execute();
                    break;
                case 'M':
                    $stmtUpdateQuantityM->bind_param("iii", $item['quantity'], $item['id_product'], $item['id_color']);
                    $stmtUpdateQuantityM->execute();
                    break;
                case 'L':
                    $stmtUpdateQuantityL->bind_param("iii", $item['quantity'], $item['id_product'], $item['id_color']);
                    $stmtUpdateQuantityL->execute();
                    break;
                case 'XL':
                    $stmtUpdateQuantityXL->bind_param("iii", $item['quantity'], $item['id_product'], $item['id_color']);
                    $stmtUpdateQuantityXL->execute();
                    break;
            }
            $sqlIncrementSoldQuantity = "UPDATE products 
        SET so_luong_da_ban = so_luong_da_ban + ? 
        WHERE id_product = ? AND id_color = ?";
    
    $stmtIncrementSoldQuantity = $conn->prepare($sqlIncrementSoldQuantity);
    $stmtIncrementSoldQuantity->bind_param("iii", $item['quantity'], $item['id_product'], $item['id_color']);
    $stmtIncrementSoldQuantity->execute();
        }

        // Insert order details
        $sqlCopyToDonHang = "INSERT INTO chitietdonhang (id_donhang, id_product, id_color, size, quantity, gia, link_hinh_anh, ten_san_pham)
            SELECT ?, id_product, id_color, size, quantity, gia, link_hinh_anh, ten_san_pham FROM giohang";
        $stmtCopyToDonHang = $conn->prepare($sqlCopyToDonHang);
        $stmtCopyToDonHang->bind_param("i", $donHangId);
        $stmtCopyToDonHang->execute();

        
        // Clear the cart
        $sqlDeleteFromCart = "DELETE FROM giohang";
        $conn->query($sqlDeleteFromCart);

        $conn->commit();

        return true;
    } else {
        // Handle errors
        return false;
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
    <style>
          @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,200&display=swap');
        body{
            font-family: 'Montserrat', sans-serif;
            margin: 0;
        }
    </style>


    <script>
        setTimeout(function () {
            document.getElementById('successMessage').style.display = 'none';
        }, 5000); // 5000 milliseconds (5 seconds)
    </script>
</head>
<body>
<div class="navbar">
<a href="home.php"><img src="images/logoo.png" alt="" style="width:130px; height:80px"></a>
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
                        while ($rowProvince = $resultProvince->fetch_assoc()) {
                            $giaVanChuyen = ($rowProvince['province_id'] == 1 || $rowProvince['province_id'] == 50) ? 15000 : 30000;
                            echo "<option value='" . $rowProvince['province_id'] . "' data-gia='" . $giaVanChuyen . "'>" . $rowProvince['name'] . "</option>";
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
                    <input type="hidden" name="totalPrice" value="<?= $totalPrice ?>">
                </table>
                <div class="pay-div">
                    <input class="body_pay pay" type="submit" name="submit" value="Thanh Toán" >
                </div>
            </form>
        </div>
        <div class="body_products">
    <legend>Giỏ hàng của bạn</legend>
    <?php
    $totalPrice = 0;
    $shipPrice  = 0;
    if (isset($cartItems) && is_array($cartItems)) :
        foreach ($cartItems as $item) :
            // Calculate the subtotal for each item
            $subtotal = isset($item['quantity']) ? $item['gia'] * $item['quantity'] : 0;

            // Add the subtotal to the total price
            $totalPrice += $subtotal;
    ?>
    <div class="body_products product" data-id_product="<?= $item['id_product'] ?>">
        <img src="<?= $item['link_hinh_anh'] ?>" alt="<?= $item['ten_san_pham'] ?>">
        <div class="product_group">
        <div class="product_content" data-gia="<?= $item['gia']; ?>">
        <h4><?= $item['ten_san_pham'] ?></h4>

        <span style="color: brown;" class="subtotal" data-item-id="<?= $item['id']; ?>">

            <?= number_format($item['gia'], 0, ',', '.') ?> VNĐ x <?= isset($item['quantity']) ? $item['quantity'] : '0' ?> =
            <?= number_format($subtotal, 0, ',', '.') ?> VNĐ
        </span>
    </div>

            <div class="product_selection">
                <select name="size" class="size-dropdown" data-item-id="<?= $item['id']; ?>">
                    <?php
                    // $sizes = ['S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL']; // Các kích thước
                    $sizes = ['S', 'M', 'L', 'XL']; // Các kích thước

                    $selectedSize = $item['size'];

                    foreach ($sizes as $size) {
                        $selected = ($size == $selectedSize) ? 'selected' : '';
                        echo '<option value="' . $size . '" ' . $selected . '>Size ' . strtoupper($size) . '</option>';
                    }
                    ?>
                </select>
                x
                <select name="quantity" class="quantity-dropdown" data-item-id="<?= $item['id']; ?>">
                    <?php
                    // Truy vấn số lượng từ cơ sở dữ liệu
                    $sql = "SELECT quantity FROM giohang WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $item['id']);
                    $stmt->execute();
                    $stmt->bind_result($dbQuantity);
                    $stmt->fetch();
                    $stmt->close();

                    // Tạo tùy chọn cho số lượng từ 1 đến 10
                    for ($i = 1; $i <= 10; $i++) {
                        $selected = ($i == $dbQuantity) ? 'selected' : '';
                        echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                    }
                    ?>
                </select>
                <script src="js/cart_quantity.js"></script>



<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="js/cart.js"></script>


<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="js/cart_size.js"></script>
<script src="js/cart_quantity.js"></script>

                    <button class="delete-button" data-id_product="<?= $item['id_product'] ?>" data-id_color="<?= $item['id_color'] ?>" data-size="<?= $item['size'] ?>">Xóa</button>
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
             <h4 id="productTotalPrice"><?= number_format($totalPrice, 0, ',', '.') ?> VNĐ</h4>
                </div>

                <div>
    <span>Số tiền vận chuyển</span>
    <h4 id="shipPrice">0 VNĐ</h4>
</div>

                <div>
                    <legend id="end">Tổng tiền thanh toán</legend>
                    <h4 id="totalPrice" data-value="<?= $totalPrice ?>" name="totalPrice"><?= number_format($totalPrice, 0, ',', '.') ?> VNĐ</h4>
                </div>
            </div>
        </div>
    </div>
    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/cart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    const totalPriceElement = document.getElementById("totalPrice");

    form.addEventListener("submit", function (event) {
        const currentTotalPrice = parseFloat(totalPriceElement.getAttribute("data-value"));

        const hiddenTotalPriceInput = form.querySelector("input[name='totalPrice']");
        hiddenTotalPriceInput.value = currentTotalPrice;
    });
});

$(document).ready(function () {
    $('.size-dropdown, .quantity-dropdown').change(function () {
        // Lấy giá trị mới của size và quantity
        var newSize = $(this).closest('.product_group').find('.size-dropdown').val();
        var newQuantity = $(this).closest('.product_group').find('.quantity-dropdown').val();

        var giaSanPham = parseFloat($(this).closest('.product_group').find('.product_content').data('gia'));

        // Lấy tên sản phẩm
        var tenSanPham = $(this).closest('.product_group').find('.product_content h4').text();

        var subtotalText = calculateSubtotal(newQuantity, giaSanPham, tenSanPham);
        var productContent = $(this).closest('.product_group').find('.product_content');
        productContent.find('.subtotal').html(subtotalText);

        updateSubtotal();
    });

    function calculateSubtotal(quantity, price) {
        var subtotal = quantity * price;
        return price + ' VNĐ' + ' x ' + quantity + ' = ' + subtotal.toLocaleString('en-US') + ' VNĐ';
    }

    function updateSubtotal() {
        var productTotalPrice = 0;

        $('.product_group').each(function () {
            var subtotalText = $(this).find('.subtotal').text();
            var subtotalValue = parseFloat(subtotalText.replace(' VNĐ', '').replace(',', '').split('=')[1]);

            if (!isNaN(subtotalValue)) {
                productTotalPrice += subtotalValue;
            }
        });

        // Get the shipping cost
        var shipPrice = parseFloat($('#shipPrice').text().replace(' VNĐ', '').replace(',', ''));

        // Calculate the total price
        var totalPrice = shipPrice + productTotalPrice;

        // Hiển thị tổng giá vào thẻ h4 có id là 'totalPrice'
        $('#totalPrice').text(totalPrice.toLocaleString('en-US') + ' VNĐ');
        $('#totalPrice').attr('data-value', totalPrice);

        // Cập nhật số tiền mua sản phẩm
        $('#productTotalPrice').text(productTotalPrice.toLocaleString('en-US') + ' VNĐ');
    }

    $('#province').change(function () {
        var selectedOption = $(this).find(':selected');
        var giaVanChuyen = parseFloat(selectedOption.data('gia'));

        $('#shipPrice').text(giaVanChuyen.toLocaleString('en-US') + ' VNĐ');

        updateSubtotal(); // Call the updateSubtotal function when the province changes
    });
});

</script>

</body>
<?php require_once "footer.php"?>;
</html>
