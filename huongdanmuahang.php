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
    <h1>Chào mừng đến với trang web của chúng tôi</h1>
    <div class="navbar">
    <a href="home.php"><img src="images/logo.png" alt=""></a>
    <div class="navbar_list"></div>
    <?php include('php/dropdown.php'); ?>
    </div>
            <div class="blog-content" ; style="margin-left:100px;margin-right:100px"> 
            <div class="container">
            <div class="row"> 
            <div class="col-md-9 col-sm-8 blog-content"> 
            <div class="blog-single"> <article class="blogpost"> 
                <h1 style="color:#0077cc;text-align:center;font-size:40px;">HƯỚNG DẪN ĐẶT HÀNG</h1>
            <div class="space30"></div> 
            
            <div class="article-content" style="text-align: center; margin: 0 auto; width: 100%;">
    <span style="color:#000000">
        
            <h1 style="font-size:25px;">HƯỚNG DẪN MUA HÀNG TẠI HỆ THỐNG CỬA HÀNG THỜI TRANG 4MEN</h1>
        
    </span>
    <span style="font-size:18px;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        <br> 4MEN - hệ thống&nbsp;thời trang nam uy tín hiện đang sở hữu đến 15 chi nhánh, phân bố&nbsp;rộng khắp khu vực Đông Nam Bộ và Tây Nam Bộ. Quý khách hàng khi đến với hệ thống cửa hàng của 4MEN có thể hoàn toàn tin tưởng và hài lòng, từ&nbsp;phong cách và chất lượng&nbsp;sản cho đến thái độ, quy cách của nhân viên luôn được kiểm&nbsp;soát một cách chặt chẽ, đảm bảo quý&nbsp;khách hàng phải được phục vụ một cách chu đáo, chất lượng nhất.
        <br> Ngoài việc&nbsp;tham khảo hoặc liên hệ với 4MEN&nbsp;để được giải đáp&nbsp;mọi vấn đề liên quan đến&nbsp;cửa hàng, sản phẩm,... Quý khách hàng có thể&nbsp;trực tiếp đến Store&nbsp;4MEN gần nhất để tham gia mua sắm và nhận thêm&nbsp;nhiều ưu đãi hấp dẫn khác.
    </span>
</div>
 
            </li> </ul>
            </div> </div> </div> 
            
            
            <div style="text-align: center">
                <span style="color:rgb(178, 34, 34)">
                <span style="font-size:16px">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                </span>
                </span>
                    <br> 
                    <span style="color:#000000">
                        <strong>   
                            <h1 style="font-size:25px">HƯỚNG DẪN MUA HÀNG QUA ĐIỆN THOẠI</h1>
                        </strong>
                    </span>
                     
                    <span style="font-size:18px;">
                        <span style="color:rgb(0, 0, 0)"><br> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        </span>
                    </span>
                    <span style="text-align:center;font-size:18px">Quý khách vui lòng gọi vào số:&nbsp;
                        <span style="color:#0000FF">0898.877.325
                        </span>
                        &nbsp;để cung cấp các thông tin: Mã hàng, size, số lượng, tên, số điện thoại
                        &nbsp;và địa chỉ người nhận hàng. Nhân viên tổng đài 4MEN sẽ tư vấn cách thức đặt hàng dễ dàng và nhanh nhất cho quý khách.
                    </span>
                </div> 


                
                <div style="text-align:center">&nbsp;
                </div> 
                <div style="text-align:center">
                    <span style="color:#000000">
                        <span style="font-size:14px"><strong>
                            <span style="font-size:25px">HƯỚNG DẪN MUA HÀNG QUA WEBSITE 4MEN
                            </span>
                            </strong>
                        </span>
                    </span>
                    <br> <br> 
                        <strong style="font-size:18px">
                            Để mua hàng online qua website 4MEN, quý khách vui lòng làm theo các bước hướng dẫn sau:
                        </strong>
                    <br> <strong>Bước đầu tiên</strong>:&nbsp;Tại sản phẩm cần mua,&nbsp;
                        <strong>chọn size</strong>, 
                        <strong>chọn số lượng</strong>, sau đó:
                    <br> - Nhấp&nbsp;vào&nbsp;ô&nbsp;<span style="color:#0000FF">MUA NGAY</span>&nbsp;,&nbsp;tiếp tục chuyển qua bước 1
                </div> 
                <div style="text-align:center">
                    <br> 
                </div> 
                <div style="text-align:center;font-size:25px">
                    <br> <strong>BƯỚC&nbsp;1: Nhập&nbsp;thông tin cần thiết</strong>
                </div>
                <div style="font-size:18px">
                    <br> - Kiểm tra lại thông tin sản phẩm đặt hàng &nbsp;(tên sản phẩm, số lượng, size,&nbsp;đơn giá)&nbsp;tại mục&nbsp;
                    <span style="color:#FF0000">1</span>
                    &nbsp;ở cột&nbsp;
                        <strong>"Giỏ hàng của bạn" 
                        </strong>&nbsp;bên phải
                    <br> -&nbsp;Nhập thông tin liên hệ đầy đủ của người mua tại mục&nbsp;&nbsp;
                    <span style="color:#FF0000">2
                    </span>
                    <br> - Nhập địa chỉ giao hàng tại mục&nbsp;&nbsp;   
                    <span style="color:#FF0000">3
                    </span>
                    <br> - Quý khách có thể theo dõi phí vận chuyển (PVC)&nbsp;&nbsp;phát sinh và tổng tiền thanh toán&nbsp;tại mục&nbsp;
                    <span style="color:#FF0000">*
                    </span>&nbsp;ở cột&nbsp;<strong>"Thông tin đơn hàng"</strong>&nbsp;bên phải.
                    <br> - Nhấn chọn&nbsp;
                    <span style="color:#0000FF">GỬI ĐƠN HÀNG
                    </span>&nbsp;tại mục&nbsp;
                    <span style="color:#FF0000">4
                    </span>&nbsp;, hoặc mục&nbsp;
                    <span style="color:#FF0000">*
                    </span></div> <div style="text-align:center;width: px;height: auto;">
                    <br> <img src="images/Artboard 1.png" alt="Hướng dẫn đặt hàng - 2"
                                style="text-align:center;width: 1000px;height: 100%;">
                </div> 
                <div style="text-align:center;font-size:25px">
                    <br> <strong>BƯỚC&nbsp;2: Nhận thông báo&nbsp;gửi đơn hàng</strong>
                </div>
                <div style="text-align: center;font-size:18px;">
                    <br> - Quý khách sau khi nhấn nút <strong>GỬI ĐƠN HÀNG</strong> sẽ nhận được thông báo&nbsp;đặt hàng thành công, để mua thêm sản phẩm vui lòng nhấn chọn&nbsp;<strong>TIẾP TỤC THAM GIA MUA HÀNG</strong>
                    <br> 
                    <br> <img src="http://4menshop.com/images/2015/07/20150717_f10dec5cab127665f8be86bc0524f146_1437129867.jpg" alt="Hướng dẫn đặt hàng - 3">
                    <br> 
                    <br> Sau khi nhận được đơn hàng của quý khách, 4MEN sẽ phản hồi lại trong vòng 24h để xác nhận đơn hàng, hình thức thanh toán, giao hàng, chuyển hàng hoặc thông báo các trường hợp đơn hàng gặp sự cố.
                    <br> &nbsp;
                </div> 
        </br>
        </br>
        </br>
                <div 
            
                style="color:#FF0000;text-align:center;font-size:18px">Cảm ơn quý khách đã tin tưởng và lựa chọn&nbsp;THƯƠNG HIỆU THỜI TRANG NAM&nbsp;4MEN
                <br> Chúc quý khách có những giây phút mua sắm vui vẻ.
            </div> 
            </div> 
            </div> </article> 
            </div> 
        <!-- phan cuoi -->
        </br>
        </br>
        </br>
            
        

</body>
<?php require_once "footer.php"?>;

</html>
