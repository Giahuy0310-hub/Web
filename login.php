<?php
require_once('db_connection.php'); // Đảm bảo đường dẫn tới tệp là chính xác

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['pass']);

    $query = "SELECT * FROM login WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_email'] = $email;
            echo "success"; // Trả kết quả về cho JavaScript
        } else {
            echo "Đăng nhập thất bại. Vui lòng kiểm tra lại thông tin đăng nhập.";
        }
    } else {
        echo "Đăng nhập thất bại. Tài khoản không tồn tại.";
    }
} elseif (isset($_POST['action']) && $_POST['action'] === 'register') {
    $fullname = mysqli_real_escape_string($conn, $_POST['name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['pass']);

    // Kiểm tra xem email đã tồn tại trong cơ sở dữ liệu chưa
    $check_query = "SELECT * FROM login WHERE email = '$email'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        echo "Đăng ký thất bại. Email đã tồn tại.";
    } else {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Thêm người dùng vào cơ sở dữ liệu
        $insert_query = "INSERT INTO login (fullname, phone_number, email, password) VALUES ('$fullname', '$phone_number', '$email', '$hashed_password')";

        if ($conn->query($insert_query) === TRUE) {
            echo "success";
        } else {
            echo "Đăng ký thất bại. Lỗi: " . $conn->error;
        }
    }
}

$conn->close();
?>
