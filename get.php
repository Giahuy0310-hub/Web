<?php
require_once('php/db_connection.php');

if (isset($_GET['province'])) {
    $selectedProvince = $_GET['province'];

    $sqlDistrict = "SELECT district_id, name FROM district WHERE province_id = ?";
    $stmtDistrict = $conn->prepare($sqlDistrict);
    $stmtDistrict->bind_param("i", $selectedProvince);
    $stmtDistrict->execute();

    $result = $stmtDistrict->get_result();
    $districts = [];

    while ($row = $result->fetch_assoc()) {
        $districts[] = $row;
    }

    echo json_encode($districts);
} elseif (isset($_GET['district'])) {
    $selectedDistrict = $_GET['district'];

    $sqlWards = "SELECT wards_id, name FROM wards WHERE district_id = ?";
    $stmtWards = $conn->prepare($sqlWards);
    $stmtWards->bind_param("i", $selectedDistrict);
    $stmtWards->execute();

    $result = $stmtWards->get_result();
    $wards = [];

    while ($row = $result->fetch_assoc()) {
        $wards[] = $row;
    }

    echo json_encode($wards);
} else {
    echo json_encode([]);
}

$conn->close();
?>
