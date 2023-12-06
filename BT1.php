<?php
$thu=["Chủ nhật","Thứ hai","Thứ ba","Thứ tư","Thứ năm","Thứ sáu","Thứ bảy"]; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            width: 300px; 
            height: 20px; 
             }
        
             div {
                border-radius: 20px;
            border: 1px solid black; 
        }

        h3 {
            margin:0;
            border-radius: 20px 20px 0px 0px;
            background-color: green;
            padding: 10px;
            text-align: center;
            font-size: 14px;

        }

        p {
            color: black;
            font-size: 14px;
            margin: 0;
        }
        </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div>
        <h3>Các thứ trong tuần</h3>

<?php for ($i= 0;$i<count($thu);$i++){ ?>
 <p><?php echo $i+1, ".", $thu[$i]; ?> </p>
 <?php } ?>
</div>
</body>
</html>