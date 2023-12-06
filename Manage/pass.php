<?php
session_start();
require_once('db_connection.php');

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    $stmtCheckUser = $conn->prepare("SELECT * FROM login WHERE id = ?");
    $stmtCheckUser->bind_param('i', $user_id);
    $stmtCheckUser->execute();
    $resultCheckUser = $stmtCheckUser->get_result();

    if ($resultCheckUser->num_rows > 0) {
        $stmt = $conn->prepare("SELECT * FROM login WHERE email=? AND password=?");
        $stmt->bind_param('ss', $email, $current_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $updateStmt = $conn->prepare("UPDATE login SET password=? WHERE email=?");
            $updateStmt->bind_param('ss', $new_password, $email);

            if ($updateStmt->execute()) {
                echo "Password updated successfully";
            } else {
                echo "Error updating password: " . $conn->error;
            }

            // Close the update statement only if it was successfully prepared
            $updateStmt->close();
        } else {
            echo "Incorrect current password";
        }
    } else {
        echo "User not found";
    }

    // Close all other statements
    $stmt->close();
    $stmtCheckUser->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="css/menu.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
    <link rel="stylesheet" href="css/menu.css">

    <link rel="stylesheet" href="css/pf.css">
    <style>
        .main{
            width: 100%;
        }
        .right-column{
            width: 50%;
            margin-left: 100px;
        }
        form{
            display: flex;
        }
        .font-label{
            display: flex;
            flex-direction: column;
        }
        .font-label label{
            height: 30px;
            width: 160px;
            margin: 2.5px;
        }
        .font-input input{
            padding: 5px 0 5px 10px;
            width: 200px;
            margin: 2.5px;
            height: 20px;
            border: 1px solid gray;
            border-radius: 5px;
        }
        .font-submit{
            margin: 10px 0 10px 100px;
        }
        .font-submit button{
            height: 50px;
            width: 200px;
            background-color: #444;
            color: white;
            border: 1px solid #444;
            border-radius: 10px;
            transition: all 0.3s;
            font-size: 15px;
            font-weight: 300;
        }
        .font-submit button:hover{
            cursor: pointer;
            background-color: white;
            color: #444;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../home.php"><img src="../images/logo.png" alt=""></a>
        <div class="navbar_list"></div>
        <?php include('dropdown.php'); ?>
    </div>


    <main>
        <div class="left-column">
            <?php include('menu.php'); ?>
        </div>
        <div class="right-column">

    <h2>Change Password</h2>
    <form action="" method="post">
        <div class="font-label">
        <label for="email">Email:</label>

        <label for="current_password">Current Password:</label>

        <label for="new_password">New Password:</label>

        </div>
        <div class="font-input">
            <input type="email" name="email" required><br>
            
            <input type="password" name="current_password" required><br>
            
            <input type="password" name="new_password" required><br>
        </div>
        
    </form>
    <div class="font-submit">
        <button type="submit">Change Password</button>
    </div>
</body>
</html>
