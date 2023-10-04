<?php
// Bắt đầu phiên làm việc với session
session_start();

// Thêm một biến để lưu thông báo
$success_message = "";

// Thông tin kết nối cơ sở dữ liệu
$servername = "localhost"; // Địa chỉ máy chủ MySQL
$username = "root"; // Tên đăng nhập MySQL
$password = ""; // Mật khẩu MySQL (để trống nếu không có mật khẩu)
$database = "testt"; // Tên cơ sở dữ liệu MySQL

// Tạo kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

$fullname = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : "";
$phone_number = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : "";
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['pass']);

$action = $_POST['action']; // Thêm một trường ẩn trong form để xác định hành động (login hoặc register)

if ($action === 'login') {
    // Kiểm tra thông tin đăng nhập
    $query = "SELECT * FROM login WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Kiểm tra mật khẩu bằng hàm password_verify
        if (password_verify($password, $row['password'])) {
            // Đăng nhập thành công, gán thông báo vào biến
            $success_message = "Đăng nhập thành công!";
        } else {
            // Đăng nhập thất bại, gán thông báo vào biến
            $success_message = "Đăng nhập thất bại!";
        }
    } else {
        // Đăng nhập thất bại, gán thông báo vào biến
        $success_message = "Đăng nhập thất bại!";
    }
} elseif ($action === 'register') {
    // Băm mật khẩu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert dữ liệu vào bảng
    $sql = "INSERT INTO login (fullname, phone_number, email, password) VALUES ('$fullname', '$phone_number', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        // Đăng ký thành công, gán thông báo vào biến
        $success_message = "Đăng ký thành công!";
    } else {
        // Xử lý lỗi khi thêm dữ liệu vào cơ sở dữ liệu và gán thông báo vào biến
        $success_message = "Lỗi: " . $sql . "<br>" . $conn->error;
    }
}

// Đóng kết nối đến cơ sở dữ liệu
$conn->close();
?>

<!-- Hiển thị thông báo trên trang -->
<p><?php echo $success_message; ?></p>
