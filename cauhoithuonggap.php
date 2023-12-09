<?php
require_once('php/db_connection.php');
$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? $_GET['loaisanpham'] : null;
$id_product = isset($_GET['id_product']) ? $_GET['id_product'] : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'asc';

// Lấy danh sách các danh mục
$sqlCategories = "SELECT ID_DM, TenDanhMuc FROM categories";
$stmt = $conn->prepare($sqlCategories);
$stmt->execute();
$resultCategories = $stmt->get_result();
$categoryList = [];

while ($row = $resultCategories->fetch_assoc()) {
    $categoryID = $row['ID_DM'];
    $categoryName = $row['TenDanhMuc'];
    $isActive = $categoryID == $selectedCategory ? 'active' : ''; 

    // Lấy danh sách các loại sản phẩm trong danh mục
    $sqlSubcategories = "SELECT DISTINCT loaisanpham FROM products WHERE id_dm = ?";
    $stmtSubcategories = $conn->prepare($sqlSubcategories);
    $stmtSubcategories->bind_param('i', $categoryID);
    $stmtSubcategories->execute();
    $resultSubcategories = $stmtSubcategories->get_result();

    $subcategories = [];

    while ($rowSubcategory = $resultSubcategories->fetch_assoc()) {
        $subcategories[] = $rowSubcategory['loaisanpham'];
    }

    $categoryList[] = [
        'ID_DM' => $categoryID,
        'TenDanhMuc' => $categoryName,
        'LoaiSanPham' => $subcategories,
    ];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>index</title>
        <link rel="stylesheet" href="css/header.css">
        <link rel="stylesheet" href="css/menu.css">
        <script src="js/products.js"></script>
    
        <link rel="stylesheet" href="css/footer.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <style>
              @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,200&display=swap');
            html ,body{
                margin: 0;
                background-color: none;
                font-family: 'Montserrat', sans-serif;
                width: 100%;
                position: relative;
            }
            
        </style> 
    </head>
    <body>
    <div class="navbar">
<a href="home.php"><img src="images/logoo.png" alt="" style="width:130px; height:80px"></a>

    <div class="navbar_list"></div>
    <?php include('php/dropdown.php'); ?>
<!-- CÂU HỎI THƯỜNG GẶP -->
<h3 style="font-size:30px;margin-top:100px;;color:#0077cc; text-align:center;position: relative;
    z-index: 1;"> </h3>
<div class=" item__accordion__details active"><h1 style="font-size:40px;margin-top:100px;;color:#0077cc; text-align:center">CÂU HỎI THƯỜNG GẶP</h1>


<!-- /ko -->
<div style="margin-left:50px;font-size:25px">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            
        }

        .faq-container {
            max-width: 1500px;
            margin: 20px;
        }

        .faq-item {
            margin-bottom: 20px;
        }

        .question {
            font-weight: bold;
            cursor: pointer;
            color: #333;
        }

        .answer {
            display: none;
            margin-top: 10px;
            color: #555;
        }
    </style>
    <script>
        function toggleAnswer(index) {
            var answer = document.getElementById('answer-' + index);
            answer.style.display = (answer.style.display === 'none' || answer.style.display === '') ? 'block' : 'none';
        }
    </script>
</head>
<body>

<div class="faq-container">
    <div class="faq-item" onclick="toggleAnswer(1)">
        <div class="question">1. Làm thế nào để mua hàng tại 4MEN?</div>
        <div class="answer" id="answer-1">Khi bạn muốn mua một mặt hàng tại 4MEN, có thể thực hiện bằng cách đến trực tiếp cửa hàng, gọi điện thoại cho 4MEN hoặc đặt hàng trên trang web. Hướng dẫn chi tiết có thể được tham khảo tại <a href="https://4menshop.com/dat-hang-truc-tuyen.html">đây</a>.</p>
</div>
    </div>

    <div class="faq-item" onclick="toggleAnswer(2)">
        <div class="question">2. Tôi ở xa có mua hàng được không? Shop có giao hàng không?</div>
        <div class="answer" id="answer-2">4MEN cung cấp dịch vụ giao hàng và thu tiền tận nơi trên toàn quốc. Miễn phí giao hàng cho hóa đơn từ 1 triệu đồng trở lên, đảm bảo sự thuận tiện cho quý khách.
</div>
    </div>
    <div class="faq-item" onclick="toggleAnswer(3)">
        <div class="question">3. Tôi không biết chọn size như thế nào?</div>
        <div class="answer" id="answer-3">Nếu bạn đang phân vân về việc chọn size, hãy tham khảo hướng dẫn chi tiết tại <a href="huongdanchonsize.php">đây</a>, giúp bạn chọn size phù hợp với cân nặng và chiều cao của mình.</div>
    </div>
    <div class="faq-item" onclick="toggleAnswer(4)">
        <div class="question">4. Sao tôi có thể tin tưởng khi chuyển tiền rồi, tôi sẽ nhận được hàng?</div>
        <div class="answer" id="answer-4">Khác với một số cửa hàng chỉ hoạt động trực tuyến không rõ địa chỉ, 4MEN là một hệ thống cửa hàng có địa chỉ rõ ràng được đăng tải trên trang web của cửa hàng. Đặc biệt, 4MEN sử dụng dịch vụ giao hàng và thu tiền tận nhà. Thanh toán chỉ được thực hiện khi khách hàng nhận được hàng, giảm rủi ro đến mức tối thiểu. Uy tín và đảm bảo chất lượng hàng hóa là tiêu chí hàng đầu của 4MEN.
</div>
    </div>
    <div class="faq-item" onclick="toggleAnswer(5)">
        <div class="question">5. Hàng hóa trên 4MEN có đảm bảo chất lượng và mẫu mã không?</div>
        <div class="answer" id="answer-5">Hàng hóa tại 4MEN luôn được cam kết và đảm bảo về chất lượng. Khách hàng có thể kiểm tra sản phẩm ngay khi nhận hàng và từ chối nhận hàng nếu không đúng.
</div>
    </div>
    <div class="faq-item" onclick="toggleAnswer(6)">
        <div class="question">6. Tôi muốn đến cửa hàng để mua trực tiếp</div>
        <div class="answer" id="answer-6">Quý khách có thể đến trực tiếp cửa hàng theo địa chỉ được liệt kê trên trang web. Cửa hàng mở cửa từ 8h30 đến 22h00 hàng ngày, bao gồm cả chủ nhật.
</div>
    </div>
    <div class="faq-item" onclick="toggleAnswer(7)">
        <div class="question">7. Tôi đã mua hàng, nhưng không vừa ý, có thể đổi lại không?</div>
        <div class="answer" id="answer-7">4MEN chấp nhận đổi hàng trong vòng 5 ngày, với điều kiện sản phẩm còn mới 100% và chưa qua sử dụng. Tuy nhiên, không giải quyết trường hợp đổi hàng đã sử dụng hoặc trả lại hàng.
</div>
    </div>
    <div class="faq-item" onclick="toggleAnswer(8)">
        <div class="question">8. Bao lâu tôi sẽ nhận được hàng?</div>
        <div class="answer" id="answer-8">Thời gian nhận hàng tùy thuộc vào địa chỉ của quý khách, thường trong khoảng từ 24h đến 72h trong giờ làm việc.
</div>
    </div>
    <div class="faq-item" onclick="toggleAnswer(9)">
        <div class="question">9. Tại sao sau một thời gian mà hàng của tôi vẫn chưa nhận được?</div>
        <div class="answer" id="answer-9">Thời gian nhận hàng tùy thuộc vào địa chỉ của quý khách, thường trong khoảng từ 24h đến 72h trong giờ làm việc. Trong một số trường hợp ngoài ý muốn như thiên tai, địa chỉ giao hàng không chính xác, không có người nhận, 4MEN sẽ kiểm tra với công ty vận chuyển để xác nhận thông tin và giao hàng lại trong thời gian sớm nhất.</p>
</div>
    </div>
    <div class="faq-item" onclick="toggleAnswer(10)">
        <div class="question">10. Có cửa hàng ở các tỉnh không?</div>
        <div class="answer" id="answer-10">Hiện nay, 4MEN có 3 cửa hàng tại các tỉnh là Long An, Đồng Nai và TP.Pleiku. 4MEN dự kiến mở rộng hệ thống cửa hàng</div>
    </div>

</div>
</div>

</body>
</html>


<!-- phan cuoi -->    
    </br>
        </br>
        </br>

            
    
</body>
<?php require_once "footer.php"?>;
</html>