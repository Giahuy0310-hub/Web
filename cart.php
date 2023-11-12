<?php
require_once('php/db_connection.php');

// Fetch provinces for dropdown
$sqlProvince = "SELECT * FROM province";
$resultProvince = $conn->query($sqlProvince);

// Fetch cart items
$sqlCart = "SELECT * FROM giohang";
$resultCart = $conn->query($sqlCart);

$cartItems = array();

if ($resultCart->num_rows > 0) {
    while ($row = $resultCart->fetch_assoc()) {
        $cartItems[] = $row;
    }
}

// Check if the form is submitted
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

        if (createOrder($conn, $fullname, $phone, $email, $address, $province, $district, $wards, $note, $totalPrice, $date, $cartItems)) {
            $successMessage = "Đơn hàng đã được đặt thành công! Số đơn hàng của bạn là: " . $donHangId . ". Tổng tiền: " . $totalPrice . ". Thời gian đặt hàng: " . $date;
        } else {
            // Handle errors
            echo "Lỗi: Không thể tạo đơn hàng.";
        }
    } else {
        // Handle errors
        echo "Lỗi: Dữ liệu không hợp lệ.";
    }
}

function createOrder($conn, $fullname, $phone, $email, $address, $province, $district, $wards, $note, $totalPrice, $date, $cartItems) {
    $sqlInsertIntoDonHang = "INSERT INTO DonHang (hoten, sodienthoai, email, sonha_duong, tinh_thanh, quan_huyen, phuong_xa, ghichu, totalPrice, date)
        VALUES (?, ?, ?, ?, (SELECT name FROM province WHERE province_id = ? LIMIT 1), (SELECT name FROM district WHERE district_id = ? LIMIT 1), (SELECT name FROM wards WHERE wards_id = ? LIMIT 1), ?, ?, ?)";

    $stmtInsertIntoDonHang = $conn->prepare($sqlInsertIntoDonHang);
    $stmtInsertIntoDonHang->bind_param("ssssssssds", $fullname, $phone, $email, $address, $province, $district, $wards, $note, $totalPrice, $date);

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
                    <!-- Hidden input for totalPrice -->
                    <input type="hidden" name="totalPrice" value="<?= $totalPrice ?>">
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
            <div class="body_products product" data-id_product="<?= $item['id_product'] ?>">
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
    <select name="size" class="size-dropdown">
        <?php
        $size = $item['size']; 
        echo '<option value="' . $size . '">Size ' . strtoupper($size) . '</option>';
        ?>
    </select>

    x

    <select name="quantity" class="quantity-dropdown" data-id-product="<?= $item['id_product'] ?>" data-id-color="<?= $item['id_color'] ?>" data-size="<?= $item['size'] ?>" data-selected-quantity="<?= $selectedQuantity ?>">
    <?php
    $sql = "SELECT quantity FROM giohang WHERE id_product = ? AND id_color = ? and size = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iis', $item['id_product'], $item['id_color'], $item['size']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $quantity = $row['quantity'];

            echo '<option value="' . $quantity . '" ' . ($quantity == $selectedQuantity ? 'selected' : '') . '>';
            echo  $quantity ;
            echo '</option>';
        }
    } else {
        echo '<option value="" disabled>No quantities available</option>';
    }
    ?>
</select>
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
                    <h4><?= number_format($totalPrice, 0, ',', '.') ?> VNĐ</h4>
                </div>
                <legend>Vận chuyển</legend>
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
    document.addEventListener("DOMContentLoaded", function() {
        // Get the form element
        const form = document.querySelector("form");

        // Get the total price element
        const totalPriceElement = document.getElementById("totalPrice");

        // Add a submit event listener to the form
        form.addEventListener("submit", function(event) {
            // Get the current total price from the displayed element
            const currentTotalPrice = parseFloat(totalPriceElement.getAttribute("data-value"));

            // Update the hidden input field with the current total price
            const hiddenTotalPriceInput = form.querySelector("input[name='totalPrice']");
            hiddenTotalPriceInput.value = currentTotalPrice;
        });
    });
</script>

</body>
</html>
