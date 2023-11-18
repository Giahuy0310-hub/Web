<?php
require_once('php/db_connection.php');

$sqlDate = "SELECT DISTINCT DATE(date) as orderDate FROM donhang ORDER BY orderDate ASC";
$stmtDate = $conn->prepare($sqlDate);

$stmtDate->execute();
$resultDate = $stmtDate->get_result();

$data = [];

while ($rowDate = $resultDate->fetch_assoc()) {
    $date = $rowDate['orderDate'];

    $sql = "SELECT SUM(totalPrice) as total FROM donhang WHERE DATE(date) = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param('s', $date);

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'date' => $date,
            'total' => $row['total'],
        ];
    }

    $stmt->close();
}

$stmtDate->close();

$conn->close();

$json_data = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/chart.css">

    <title>Line Chart</title>
</head>
<body>
<nav>
    <ul>
        <li><a href="home.php">Trang chủ</a></li>

        <li><a href="dtn.php">Doanh thu theo ngày </a></li>

        <li><a href="dtdm.php">Doanh thu theo danh mục</a></li>
    </ul>
</nav>

<canvas id="lineChart" width="7000" height="2900"></canvas>

<script>
var jsonData = <?php echo $json_data; ?>;

var labels = jsonData.map(function(entry) {
    return moment(entry.date).format('YYYY-MM-DD');
});

var values = jsonData.map(function(entry) {
    return entry.total;
});

var ctx = document.getElementById('lineChart').getContext('2d');
var lineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total Revenue',
            data: values,
            borderColor: 'rgb(75, 192, 192)',
            borderWidth: 2,
            fill: false
        }]
    },
    options: {
        scales: {
            x: {
                type: 'category', 
            },
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Biểu Đồ Doanh Thu Theo Ngày', 
                position: 'bottom',
                font: {
                    size: 16
                },
                padding: 20
            }
        }
    }
});
</script>
</body>
</html>

