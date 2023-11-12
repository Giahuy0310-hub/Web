<?php
session_start();
require_once('php/db_connection.php');

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['pass']);

    $query = "SELECT id, fullname, email, password FROM login WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Lưu thông tin người dùng vào session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_fullname'] = $row['fullname'];
            $_SESSION['user_email'] = $row['email'];

            echo "success";
        } else {
            echo "Đăng nhập thất bại. Vui lòng kiểm tra lại thông tin đăng nhập.";
        }
    } else {
        echo "Đăng nhập thất bại. Tài khoản không tồn tại.";
    }
    $stmt->close();
} elseif (isset($_POST['action']) && $_POST['action'] === 'register') {
    $fullname = mysqli_real_escape_string($conn, $_POST['name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['pass']);

    // Kiểm tra xem email đã tồn tại trong cơ sở dữ liệu chưa
    $check_query = "SELECT id FROM login WHERE email = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('s', $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "Đăng ký thất bại. Email đã tồn tại.";
    } else {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Thêm người dùng vào cơ sở dữ liệu
        $insert_query = "INSERT INTO login (fullname, phone_number, email, password) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param('ssss', $fullname, $phone_number, $email, $hashed_password);

        if ($insert_stmt->execute()) {
            echo "success";
        } else {
            echo "Đăng ký thất bại. Lỗi: " . $insert_stmt->error;
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
?>
