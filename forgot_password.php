<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xử lý quên mật khẩu ở đây, gửi email xác nhận hoặc mã khôi phục
    // Sau khi gửi email thành công, bạn có thể gán thông báo thành công vào session.
    // Ví dụ:
    session_start();
    $_SESSION['success_message'] = "Vui lòng kiểm tra email để đặt lại mật khẩu.";
    header('Location: login.html'); // Chuyển hướng về trang đăng nhập sau khi gửi email thành công.
    exit;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        /* Đặt mã CSS ở đây */

        body {
            font-family: 'Poppins', sans-serif;
            font-weight: 300;
            line-height: 1.7;
            color: #ffeba7;
            background-color: #1f2029;
        }

        /* Rest of your CSS styles go here */

        .form-group {
            position: relative;
            display: block;
            margin: 0;
            padding: 0;
        }

        .form-style {
            padding: 13px 20px;
            height: 48px;
            width: 100%;
            font-weight: 500;
            border-radius: 4px;
            font-size: 14px;
            line-height: 22px;
            letter-spacing: 0.5px;
            outline: none;
            color: #c4c3ca;
            background-color: #1f2029;
            border: none;
            -webkit-transition: all 200ms linear;
            transition: all 200ms linear;
            box-shadow: 0 4px 8px 0 rgba(21, 21, 21, .2);
        }

        .form-style:focus,
        .form-style:active {
            border: none;
            outline: none;
            box-shadow: 0 4px 8px 0 rgba(21, 21, 21, .2);
        }

        .input-icon {
            position: absolute;
            top: 0;
            left: 18px;
            height: 48px;
            font-size: 24px;
            line-height: 48px;
            text-align: left;
            -webkit-transition: all 200ms linear;
            transition: all 200ms linear;
        }

        .btn {
            border-radius: 4px;
            height: 44px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            -webkit-transition: all 200ms linear;
            transition: all 200ms linear;
            padding: 0 30px;
            letter-spacing: 1px;
            display: -webkit-inline-flex;
            display: -ms-inline-flexbox;
            display: inline-flex;
            align-items: center;
            background-color: #ffeba7;
            color: #000000;
        }

        .btn:hover {
            background-color: #000000;
            color: #ffeba7;
            box-shadow: 0 8px 24px 0 rgba(16, 39, 112, .2);
        }
    </style>
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
                                <div class="form-group">
                                    <!-- Thêm trường nhập mật khẩu -->
                                    <input type="password" class="form-style" placeholder="Password" name="password">
                                    <i class="input-icon uil uil-lock-alt"></i>
                                </div>
                                <button type="submit" class="btn mt-4">Submit</button>
                            </form>
                            <!-- Hiển thị thông báo thành công -->
                            <?php
                            session_start();
                            if (isset($_SESSION['success_message'])) {
                                echo "<p>{$_SESSION['success_message']}</p>";
                                unset($_SESSION['success_message']); // Xóa thông báo thành công sau khi hiển thị.
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
