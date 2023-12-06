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
    <link rel="stylesheet" href="css/footer.css">
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,200&display=swap');
        html ,body,a{
            margin: 0;
            font-family: 'Montserrat', sans-serif;
        }
        h1{
            display: flex;
            justify-content: center;
        }
        h2{
            display: flex;
            justify-content: right;
            width: 420px;
            margin: 10px;
            text-transform: uppercase;
        }
        .link-font{
        border: 1px solid black;
        background-color: white;
        width: 400px;
        height: 50px;
        list-style: none;
        margin: 10px;
        display: flex;
        align-items: center;
        color: #444;
        text-decoration: none;
        padding-left: 20px;
        font-size: 20px;
        transition: all 0.5s;
    }
    .link-font:hover{
        background-color: #444;
        color: white;
    }
</style>
    <div class='left-column'>
        
            <h2><?php echo $userInfo['fullname']; ?></h2>

            <?php if ($userType !== null): ?>
                <?php if ($userType == 2): ?>
                    <a class="link-font" href='profile.php'>Thông Tin Cá Nhân</a>
                    <a class="link-font" href='pass.php'>Đổi Mật Khẩu</a>
                    <?php if (isset($user_id)): ?>
                        <a class="link-font" href='psorder.php?userId=<?php echo $user_id; ?>'>Đơn Hàng</a>
                    <?php endif; ?>
                <?php elseif ($userType == 1): ?>
                    <a class="link-font" href='profile.php'>Thông Tin Cá Nhân</a>
                    <a class="link-font" href='pass.php'>Đổi Mật Khẩu</a>
                    <a class="link-font" href='them.php'>Thêm sản phẩm</a>
                    <a class="link-font" href='order.php'>Danh Sách Đơn Hàng</a>
                <?php elseif ($userType == 0): ?>
                    <a class="link-font" href='qlnv.php'>Quản Lý Nhân Viên</a>
                    <a class="link-font" href='them.php'>Thêm sản phẩm</a>
                    <a class="link-font" href='order.php'>Danh Sách Đơn Hàng</a>
                    <a class="link-font" href='../chart/dtdm.php'>Thống Kê</a>
                <?php endif; ?>
            <?php else: ?>
                <p>Không có dữ liệu loại người dùng.</p>
            <?php endif; ?>

        
    </div>
<?php else: ?>
    <p>Người dùng không tồn tại.</p>
<?php endif; ?>
