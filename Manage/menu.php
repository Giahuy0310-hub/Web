<?php
require_once('db_connection.php');

function getUserInfoAndType($userId, $conn) {
    $userInfo = [];
    $userType = null;

    if ($userId) {
        // Kiểm tra người dùng trong bảng login
        $sqlCheckUser = "SELECT * FROM login WHERE id = ?";
        $stmtCheckUser = $conn->prepare($sqlCheckUser);
        $stmtCheckUser->bind_param('i', $userId);
        $stmtCheckUser->execute();
        $resultCheckUser = $stmtCheckUser->get_result();

        if ($resultCheckUser->num_rows > 0) {
            // Người dùng tồn tại, tiếp tục xử lý mã của bạn
            $userInfo = $resultCheckUser->fetch_assoc(); // Lấy thông tin người dùng
            $userType = getUserType($userId, $conn); // Lấy loại người dùng
        }

        $stmtCheckUser->close();
    }

    return ['userInfo' => $userInfo, 'userType' => $userType];
}

function getUserType($userId, $conn) {
    $sqlGetType = "SELECT type FROM login WHERE id = ?";
    $stmtGetType = $conn->prepare($sqlGetType);
    $stmtGetType->bind_param('i', $userId);
    $stmtGetType->execute();
    $resultGetType = $stmtGetType->get_result();

    if ($resultGetType->num_rows > 0) {
        $row = $resultGetType->fetch_assoc();
        return $row['type'];
    }

    return null;
}

$userData = getUserInfoAndType($user_id, $conn);

$userInfo = $userData['userInfo'];
$userType = $userData['userType'];
?>

<?php if (!empty($userInfo)): ?>
    <div class='left-column'>
        <ul>
            <h1><?php echo $userInfo['fullname']; ?></h1>

            <?php if ($userType !== null): ?>
                <?php if ($userType == 2): ?>
                    <li><a href='profile.php'>Thông Tin Cá Nhân</a></li>
                    <li><a href='pass.php'>Đổi Mật Khẩu</a></li>
                    <?php if (isset($user_id)): ?>
                        <li><a href='psorder.php?userId=<?php echo $user_id; ?>'>Đơn Hàng</a></li>
                    <?php endif; ?>
                <?php elseif ($userType == 1): ?>
                    <li><a href='profile.php'>Thông Tin Cá Nhân</a></li>
                    <li><a href='pass.php'>Đổi Mật Khẩu</a></li>
                    <li><a href='them.php'>Thêm sản phẩm</a></li>
                    <li><a href='order.php'>Danh Sách Đơn Hàng</a></li>
                <?php elseif ($userType == 0): ?>
                    <li><a href='qlnv.php'>Quản Lý Nhân Viên</a></li>
                    <li><a href='them.php'>Thêm sản phẩm</a></li>
                    <li><a href='order.php'>Danh Sách Đơn Hàng</a></li>
                    <li><a href='../chart/dtdm.php'>Thống Kê</a></li>
                <?php endif; ?>
            <?php else: ?>
                <p>Không có dữ liệu loại người dùng.</p>
            <?php endif; ?>

        </ul>
    </div>
<?php else: ?>
    <p>Người dùng không tồn tại.</p>
<?php endif; ?>
