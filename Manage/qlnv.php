<?php
require_once('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : null;

    if ($action === 'add' || $action === 'update') {
        $fullname = $_POST['fullname'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $type = $_POST['type'];


        // Thực hiện tác vụ thêm người dùng
        if ($action === 'add') {
            $sqlAddUser = "INSERT INTO login (fullname, phone_number, email, type) VALUES (?, ?, ?, 1)";
            $stmtAddUser = $conn->prepare($sqlAddUser);
            $stmtAddUser->bind_param('sssi', $fullname, $phone_number, $email, $type);
            $stmtAddUser->execute();
        }

        // Thực hiện tác vụ cập nhật người dùng
        if ($action === 'update') {
            $id = $_POST['id'];
            $sqlUpdateUser = "UPDATE login SET fullname = ?, phone_number = ?, email = ?, type = ? WHERE id = ?";
            $stmtUpdateUser = $conn->prepare($sqlUpdateUser);
            $stmtUpdateUser->bind_param('sssii', $fullname, $phone_number, $email, $type, $id);
            $stmtUpdateUser->execute();
        }

        // Chuyển hướng về trang quản lý người dùng
        header("Location: qlnv.php");
        exit;
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'edit' || $action === 'delete') {
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        if ($id) {
            // Nếu là tác vụ sửa, hiển thị form chỉnh sửa
            if ($action === 'edit') {
                $sqlGetUser = "SELECT id, fullname, phone_number, email,type  FROM login WHERE id = ?";
                $stmtGetUser = $conn->prepare($sqlGetUser);
                $stmtGetUser->bind_param('i', $id);
                $stmtGetUser->execute();
                $resultGetUser = $stmtGetUser->get_result();
                $userData = $resultGetUser->fetch_assoc();
            }

            // Nếu là tác vụ xóa, thực hiện xóa người dùng
            if ($action === 'delete') {
                $sqlDeleteUser = "DELETE FROM login WHERE id = ?";
                $stmtDeleteUser = $conn->prepare($sqlDeleteUser);
                $stmtDeleteUser->bind_param('i', $id);
                $stmtDeleteUser->execute();

                // Chuyển hướng về trang quản lý người dùng
                header("Location: qlnv.php");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng</title>
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,200&display=swap');
        html ,body{
            margin: 0;
            font-family: 'Montserrat', sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            margin-bottom: 20px;
        }
        h2{
            margin: 20px;
        }
        h3{
            font-size: 30px;
            display: flex;
            justify-content: center;
        }
        .font-edit{
            display: flex;
            justify-content: center;
        }
        .font-label{
            width: 150px;
            display: flex;
            flex-direction: column;
        }
        .font-label label{
            height: 30px;
            margin: 2.5px;
        }
        .font-input input{
            padding-left: 10px;
            width: 200px;
            height: 25px;
            margin: 2.5px;
            border-radius: 6px;
            border: 1px solid gray;
        }
        .font-submit{
            margin: 20px;
            display: flex;
            justify-content: center;
        }
        .font-submit input{
            height: 50px;
            width: 200px;
            border: none;
            border-radius: 10px;
            background-color: #444;
            color: white;
            font-size: 15px;
            transition: ease 0.3s;
        }
        .font-submit input:hover{
            background-color: rgb(200,200,200);
            color: black;
            cursor: pointer;
        }

        a:nth-child(1){
            text-decoration: none;
            color: blue;
        }
        a:nth-child(1):hover{
            color: black;
            text-decoration: underline;
        }
        a:nth-child(2){
            text-decoration: none;
            color: red;
        }
        a:nth-child(2):hover{
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <form method="post" action="qlnv.php">
        <?php
        if (isset($userData)) {
            // Nếu là tác vụ sửa, hiển thị dữ liệu người dùng cần chỉnh sửa
            echo '<h3>Chỉnh Sửa Người Dùng</h3>';
            echo "<input type='hidden' name='action' value='update'>";
            echo "<input type='hidden' name='id' value='" . $userData['id'] . "'>";
        } else {
            // Nếu là tác vụ thêm, hiển thị form trống
            echo "<h3>Thêm Người Dùng</h3>";
            echo "<input type='hidden' name='action' value='add'>";
        }
        ?>
        <div class="font-edit">
            <div class="font-label">
            <label for="fullname">Full Name:</label>

            <label for="phone_number">Phone Number:</label>

            <label for="email">Email:</label>

            <label for="type">Type:</label>

            </div>
            <div class="font-input">
                <input type="text" id="fullname" name="fullname" value="<?php echo isset($userData) ? $userData['fullname'] : ''; ?>" required><br>
                
            <input type="text" id="phone_number" name="phone_number" value="<?php echo isset($userData) ? $userData['phone_number'] : ''; ?>" required><br>
            
            <input type="email" id="email" name="email" value="<?php echo isset($userData) ? $userData['email'] : ''; ?>" required><br>
            
            <input type="text" id="type" name="type" value="<?php echo isset($userData) ? $userData['type'] : ''; ?>" required><br>
            </div>
        </div>
        <div class="font-submit">
            <input type="submit" value="<?php echo isset($userData) ? 'Cập Nhật' : 'Thêm Người Dùng'; ?>">
        </div>
    </form>

    <h2 >Quản Lý Nhân Viên</h2>

    <?php
    // Truy vấn SQL để lấy dữ liệu từ bảng login với điều kiện type = 1
    $sql = "SELECT id, fullname, phone_number, email, type FROM login WHERE type = 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Full Name</th><th>Phone Number</th><th>Email</th>
        <th>Type</th><th>Actions</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['fullname'] . "</td>";
            echo "<td>" . $row['phone_number'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>". $row["type"] . "</td>";
            echo "<td>";
            echo "<a href='qlnv.php?action=edit&id=" . $row['id'] . "'>Sửa</a> | ";
            echo "<a href='qlnv.php?action=delete&id=" . $row['id'] . "' onclick='return confirm(\"Bạn có chắc chắn muốn xóa?\")'>Xóa</a>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "Không có dữ liệu.";
    }
    ?>

</body>
</html>
