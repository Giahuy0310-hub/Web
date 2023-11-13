<?php
require_once('php/db_connection.php');

// SQL query to get distinct id_product and their total gia
$sql = "SELECT id_product, (quantity*SUM(gia)) as total FROM chitietdonhang GROUP BY id_product";
$result = $conn->query($sql);

// Initialize an empty array to store data
$data = [];

// Fetch the result and store it in the $data array
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id_product' => $row['id_product'],
        'total' => $row['total'],
    ];
}

// Close the database connection
$conn->close();

// Convert the $data array to JSON for JavaScript
$json_data = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Sử dụng CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- Adjust the size of the canvas -->
    <canvas id="myChart" width="400" height="200"></canvas>
    <script>
        // Lấy dữ liệu từ PHP và chuyển thành mảng JavaScript
        var data = <?php echo $json_data; ?>;
        
        // Tạo mảng chứa các giá trị của biểu đồ
        var labels = [];
        var values = [];
        
        // Xử lý dữ liệu từ PHP
        data.forEach(function(item) {
            labels.push(item.id_product);
            values.push(item.total);
        });

        // Tạo biểu đồ bằng Chart.js
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
                        // Add more colors as needed
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    </script>
</body>
</html>
