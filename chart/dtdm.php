<?php
require_once('db_connection.php');

$sql = "SELECT tendanhmuc, SUM(quantity * p.gia) as total 
        FROM chitietdonhang ct
        JOIN product_id pi ON ct.id_product = pi.id_product
        JOIN products p ON p.id_product = pi.id_product 
        JOIN categories c on c.id_dm = p.id_dm
        GROUP BY tendanhmuc";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'tendanhmuc' => $row['tendanhmuc'],
        'total' => $row['total'],
    ];
}

$conn->close();

$json_data = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="chart.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<nav>
    <ul>
    <li><a href="../home.php">Trang chủ</a></li>

        <li><a href="dtn.php">Doanh thu theo ngày </a></li>

        <li><a href="dtdm.php">Doanh thu theo danh mục</a></li>
    </ul>
</nav>
<canvas id="myChart" width="1000" height="800"></canvas>
<script>
    var data = <?php echo $json_data; ?>;

    var labels = [];
    var values = [];

    data.forEach(function(item) {
        labels.push(item.tendanhmuc);
        values.push(item.total);
    });

    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Price Dataset',
                data: values,
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(204, 255, 255)',
                    'rgb(102, 0, 255)',
                    'rgb(255, 153, 204)',
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Biểu Đồ Thống Kê Doanh Thu Theo Danh Mục',
                    position: 'bottom',
                    textAlign: 'center',
                    font: {
                        size: 16
                    },
                    padding: {
                        top: 30, 
                        bottom: 30, 
                    }
                },
                legend: {
                    display: true,
                    position: 'top', 
                    labels: {
                        fontColor: 'black', 
                    },
                    padding: {
                        top: 30, 
                        bottom: 30, 
                    }
                },
            },
        }
    });
</script>
</body>
</html>
