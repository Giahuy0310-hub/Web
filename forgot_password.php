<?php
require_once('db_connection.php'); // Đảm bảo đường dẫn tới tệp là chính xác

// Đảm bảo bạn đã cài đặt PHPMailer thông qua Composer
require 'vendor/autoload.php';

// Khai báo namespace và sử dụng các lớp của PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kiểm tra xem có dữ liệu POST được gửi lên không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy địa chỉ email từ form và kiểm tra tính hợp lệ
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo "Địa chỉ email không hợp lệ.";
        exit;
    }

    // Kết nối đến cơ sở dữ liệu của bạn (thay đổi thông tin kết nối cơ sở dữ liệu)
    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Kết nối không thành công: " . $conn->connect_error);
    }

    // Truy vấn cơ sở dữ liệu để kiểm tra xem địa chỉ email có tồn tại trong cơ sở dữ liệu không
    $sql = "SELECT email FROM login WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Tạo mã xác nhận ngẫu nhiên gồm 6 chữ số
        $verificationCode = sprintf('%06d', rand(0, 999999));

        // Khởi tạo đối tượng PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Thiết lập thông tin email
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP host của Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'giahuyletan@gmail.com'; // Địa chỉ email Gmail của bạn
            $mail->Password = 'giahuy0101A@'; // Mật khẩu email Gmail của bạn
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Sử dụng TLS
            $mail->Port = 587; // Cổng SMTP của Gmail

            // Đặt địa chỉ email người nhận
            $mail->addAddress($email);

            // Thiết lập nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Mã xác nhận';
            $mail->Body = "Mã xác nhận của bạn là: $verificationCode";

            // Gửi email
            $mail->send();

            // Lưu mã xác nhận vào cơ sở dữ liệu (thay đổi thông tin tùy theo cơ sở dữ liệu của bạn)
            $sqlUpdate = "UPDATE login SET ma_xac_nhan = ? WHERE email = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param('ss', $verificationCode, $email);
            $stmtUpdate->execute();

            // Đóng kết nối cơ sở dữ liệu
            $stmtUpdate->close();
            $stmt->close();
            $conn->close();

            // Chuyển hướng hoặc thông báo thành công tùy theo nhu cầu
            session_start();
            $_SESSION['thong_bao_thanh_cong'] = "Mã xác nhận đã được gửi đến email của bạn.";
            header('Location: login.html');
            exit;
        } catch (Exception $e) {
            echo "Không thể gửi email xác nhận: {$mail->ErrorInfo}";
        }
    } else {
        // Email không tồn tại trong cơ sở dữ liệu
        echo "Email không tồn tại trong cơ sở dữ liệu.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Các thẻ head của trang -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webleb - Forgot Password</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
    <link rel="stylesheet" href="css/forgot.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="section">
    <div class="container">
        <div class="row full-height justify-content-center">
            <div class="col-12 text-center align-self-center py-5">
                <div class="section pb-5 pt-5 pt-sm-2 text-center">
                    <h6 class="mb-0 pb-3"><span>Forgot Password</span></h6>
                    <div class="center-wrap">
                        <div class="section text-center">
                            <!-- Form quên mật khẩu -->
                            <form action="forgot_password.php" method="post">
                                <div class="form-group">
                                    <input type="email" class="form-style" placeholder="Email" name="email">
                                    <i class="input-icon uil uil-at"></i>
                                </div>
                                <button type="submit" class="btn mt-4">Submit</button>
                            </form>
                            <!-- Hiển thị thông báo thành công -->
                            <?php
                            session_start();
                            if (isset($_SESSION['thong_bao_thanh_cong'])) {
                                echo "<p>{$_SESSION['thong_bao_thanh_cong']}</p>";
                                unset($_SESSION['thong_bao_thanh_cong']); // Xóa thông báo thành công sau khi hiển thị.
                            }
                            ?>
                            <p class="mb-0 mt-4 text-center"><a href="login.html" class="link">Back to Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
