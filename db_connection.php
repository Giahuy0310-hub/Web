<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "testt";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}
?>