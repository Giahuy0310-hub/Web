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
        <link rel="stylesheet" href="css/index.css">
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
<a href="home.php"><img src="images/logoo.png" alt="" style="width:130px; height:80px"></a>

    <div class="navbar_list"></div>
    <?php include('php/dropdown.php'); ?>
<!-- CHÍNH SÁCH VẬN CHUYỂN -->
<h3 style="font-size:30px;margin-top:100px;;color: black; text-align:left;position: relative;
    z-index: 1;"> </h3>
    <h1 style="font-size:40px;text-align:center;color:#0077cc":>CHÍNH SÁCH VẬN CHUYỂN</h1>
<div class=" item__accordion__details active" style="margin-left:100px;margin-right:100px;font-size:20px">  

<p><strong>I. Chính sách vận chuyển</strong></p>

<p><strong>1. Phương thức vận chuyển:</strong></p>

<p>- Giao hàng qua GHTK</p>

<p><strong>2. Phạm vi áp dụng:</strong></p>

<p>- Đối với khách hàng trong quận nội thành Hồ Chí Minh 15.000 vnđ<br>
- Đối với khách hàng ngoại tỉnh 30.000đ (&lt;500gr), 35.000đ (500g=&gt; 1000gr)<br>
- Đối với khách hàng thuộc khu vực biển đảo 50.000đ</p>

<p><strong>3. Thời gian giao hàng:</strong></p>

<p>3.1. Đối với khách hàng thuộc các&nbsp;quận nội thành Đà Nẵng (Hải Châu, Thanh Khê ) sẽ được giao trong 1 ngày vào buổi chiều tối mỗi ngày ( trừ ngày chủ nhật )<br>
3.2. Đối với khách hàng thuộc&nbsp;TP Hồ Chí Minh, Hà Nội, Hải Phòng, Cần Thơ, Bình Dương thời gian giao hàng từ 1 - 3 ngày.<br>
3.3. Đối với khách hàng thuộc các tỉnh khác thời gian giao hàng từ 4 - 7 ngày.</p>

<p>*Lưu ý:</p>

<p>- Thời gian xử lý đơn hàng sẽ được tính từ khi nhận được thanh toán hoàn tất của&nbsp;khách hàng.</p>

<p>- Có thể thay đổi thời gian giao hàng nếu khách hàng yêu cầu hoặc&nbsp;<a href="4Men" title="4Men">4MEN</a>&nbsp;chủ động thay đổi trong trường hợp chịu ảnh hưởng của thiên tai hoặc các sự kiện đặc biệt khác.</p>

<p>- Ưu đãi freeship chỉ áp dụng với đơn hàng&nbsp;nguyên giá (không giảm giá).</p>

<p><strong>II. Hình thức&nbsp;thanh toán:</strong></p>

<p>Khi mua hàng tại website&nbsp;4MEN, khách hàng&nbsp;được lựa chọn 1 trong 2 hình thức thanh toán sau:</p>

<p><strong>1. Thanh toán bằng hình thức COD:</strong></p>

<p>- Thanh toán khi nhận hàng.</p>

<p><strong>2. Thanh toán bằng hình thức chuyển khoản:</strong></p>

<p>- Tên chủ tài khoản:&nbsp;Nguyễn Võ Hoàng Anh</p>

<p>- Ngân hàng&nbsp;Agribank</p>

<p>- Số tài khoản:&nbsp;5000205299380</p>
</div>
    <!-- phan cuoi -->    
    </br>
        </br>
        </br>
        
            
    
</body>
<?php require_once "footer.php"?>;

</html>