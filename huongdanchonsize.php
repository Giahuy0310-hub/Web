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
            html ,body,a{
                margin: 0;
                font-family: 'Montserrat', sans-serif;
                width: 100%;
                position: relative;
            }
            
        </style>
    </head>
    <body>
    <div class="navbar">
    <a href="home.php"><img src="images/logo.png" alt=""></a>
    <div class="navbar_list"></div>
    <?php include('php/dropdown.php'); ?>
 

        
    <h3 style="font-size:40px;margin-top:100px;color:#0077cc;text-align:center">HƯỚNG DẪN CHỌN SIZE </h3>
        <div style="text-align:center;font-size:18px">
        <div style="margin-left:100px;margin-right:100px">
        <span style="font-size:18px;text-align:center">Nếu bạn băn khoăn không biết chọn size nào cho phù hợp với cân nặng và chiều cao của mình, đừng lo lắng! Hãy xem bảng hướng dẫn chọn size bên dưới mà&nbsp;
        <span style="font-size:20px;color:blue">4MEN 
        </span>&nbsp;
        </br>
            <h1
            style="font-size:25px;color:red">Tư vấn riêng dành cho bạn!
            </h1>   
        </span>
        <div style="text-align:center">
            <img src="https://4menshop.com/images/2023/07/20230701_a05edeb9e4ba5d26e5459cdfcc5c593c_1688188761.png" alt="Hướng dẫn chọn size - 1">
            <img src="https://4menshop.com/images/2023/07/20230701_25291a52d340dd01a81dc4e6a89628db_1688188761.png" alt="Hướng dẫn chọn size - 2">
        </div>
        <div style="text-align:center">
            <img src="https://4menshop.com/images/2016/12/20161226_ac1f530b18a20a327758473fa4930fc7_1482759836.jpg" alt="Hướng dẫn chọn size - 3">
        </div>
        
        <span style="font-size:17px">Bảng hướng dẫn chọn size trên là bảng hướng dẫn dựa trên kinh nghiệm nhiều năm của 4MEN theo khảo sát nhu cầu sở thích của khách hàng, tất nhiên sẽ không tuyệt đối, sẽ có những trường hợp ngoại lệ phụ thuộc theo vóc dáng, sở thích của từng người. Ví dụ có người thích mặc ôm, có người thích mặc rộng...
        <br> 
        <br>
        <div style="text-align:center">
        <strong
            style="font-size:17px"> Nếu bạn vẫn còn có những mắc thắc và băn khoăn cần được giải đáp? Hãy liên hệ ngay với Bộ phận Chăm sóc khách hàng của 4MEN qua Hotline (08)68 444 644 để được hỗ trợ thêm.&nbsp;Hotline 
            </strong>
                <strong style="font-size:17px;color:blue">(08)98.877.325</strong>
                <strong style="font-size:17px;">để được hỗ trợ thêm.</strong>
        </div>
        </div>
        </span>
        </br>
        </br>
        </br>

        </div>

            
    
</body>
<?php require_once "footer.php"?>;

</html>