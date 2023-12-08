<?php
session_start();

require_once('php/db_connection.php');

$selectedCategory = isset($_GET['ID_DM']) ? $_GET['ID_DM'] : null;
$selectedSubcategory = isset($_GET['loaisanpham']) ? urldecode($_GET['loaisanpham']) : null;
$color_id = isset($_GET['color_id']) ? $_GET['color_id'] : null;
$sortParam = isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : '';
$productsPerPage = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// Build the SQL query for counting products
$sqlCountProducts = "SELECT COUNT(DISTINCT p.id_product) AS total_products FROM products p WHERE 1=1";

// Build the SQL query for retrieving products
$sqlProducts = "SELECT p.id_product, p.id_dm, p.ten_san_pham, p.link_hinh_anh, p.gia, c.tenmau, c.hex_color
              FROM products p
              LEFT JOIN color c ON p.id_color = c.id_color
              WHERE 1=1";

$bindParams = [];
$bindTypes = "";

// Handle selected category
if (!empty($selectedCategory)) {
    $sqlCountProducts .= " AND p.id_dm = ?";
    $sqlProducts .= " AND p.id_dm = ?";
    $bindParams[] = $selectedCategory;
    $bindTypes .= 'i';
}

// Handle selected subcategory
if (!empty($selectedSubcategory)) {
    $sqlCountProducts .= " AND p.loaisanpham = ?";
    $sqlProducts .= " AND p.loaisanpham = ?";
    $bindParams[] = $selectedSubcategory;
    $bindTypes .= 's';
}

// Handle selected color
if (!empty($color_id)) {
    $sqlCountProducts .= " AND p.id_color = ?";
    $sqlProducts .= " AND p.id_color = ?";
    $bindParams[] = $color_id;
    $bindTypes .= 'i';
}

// Sorting
$sort1 = isset($_GET['sort1']) ? $_GET['sort1'] : 'asc';
$sort2 = isset($_GET['sort2']) ? $_GET['sort2'] : '';

// Sort products by price
$sqlProductsSorting = " ORDER BY p.gia " . ($sort1 === 'desc' ? 'DESC' : 'ASC');

// Filter products based on price range
if ($sort2 !== '') {
    if (is_numeric($sort2)) {
        $minPrice = max(0, $sort2 - 15);
        $sqlProducts .= " AND p.gia >= $minPrice";
    } elseif (preg_match('/(\d+)\s*\+\s*(\d+)\s*-\s*max/', $sort2, $matches)) {
        $minPrice = $matches[1];
        $diff = $matches[2];
        $maxPrice = $minPrice + $diff;
        $sqlProducts .= " AND p.gia >= $minPrice AND p.gia <= $maxPrice";
    }
}

// Execute the query to count total products
$stmtCount = $conn->prepare($sqlCountProducts);
if (!empty($bindTypes)) {
    $stmtCount->bind_param($bindTypes, ...$bindParams);
}
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$totalProducts = 0;

if ($resultCount->num_rows > 0) {
    $row = $resultCount->fetch_assoc();
    $totalProducts = $row['total_products'];
}

// Calculate the starting index for pagination
$startIndex = ($page - 1) * $productsPerPage;

// Modify the products query to implement pagination
$sqlProducts .= " GROUP BY p.id_product" . $sqlProductsSorting . " LIMIT ?, ?";
$stmt = $conn->prepare($sqlProducts);

// Add 'ii' for integer and bind parameters for pagination
$bindTypes .= 'ii';
$bindParams[] = $startIndex;
$bindParams[] = $productsPerPage;

if (!empty($bindTypes)) {
    $stmt->bind_param($bindTypes, ...$bindParams);
}
$stmt->execute();
$resultProducts = $stmt->get_result();

$productList = [];

while ($row = $resultProducts->fetch_assoc()) {
    $productId = $row['id_product'];
    $colors = getColorsForProduct($conn, $productId);
    $product = [
        'id_product' => $row['id_product'],
        'id_dm' => $row['id_dm'],
        'ten_san_pham' => $row['ten_san_pham'],
        'link_hinh_anh' => $row['link_hinh_anh'],
        'gia' => $row['gia'],
        'tenmau' => $row['tenmau'],
        'hex_color' => $row['hex_color'],
        'colors' => $colors,
    ];

    $productList[] = $product;
}

$totalPages = ceil($totalProducts / $productsPerPage);

function getColorsForProduct($conn, $productId) {
    $sqlColorsForProduct = "SELECT DISTINCT c.id_color, c.tenmau, c.hex_color, p.link_hinh_anh
                            FROM color c
                            JOIN products p ON c.id_color = p.id_color
                            WHERE p.id_product = ?";

    $stmt = $conn->prepare($sqlColorsForProduct);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $resultColorsForProduct = $stmt->get_result();
    $colors = [];

    while ($row = $resultColorsForProduct->fetch_assoc()) {
        $colors[] = [
            'id_color' => $row['id_color'],
            'tenmau' => $row['tenmau'],
            'hex_color' => $row['hex_color'],
            'link_hinh_anh' => $row['link_hinh_anh'],
        ];
    }

    return $colors;
}

if (isset($_GET['searchTerm'])) {
    $searchTerm = '%' . $_GET['searchTerm'] . '%';  // Add % for wildcard search

    $sql = "SELECT p.id_product, p.ten_san_pham, p.gia, c.id_color, c.tenmau, c.hex_color, p.link_hinh_anh
            FROM products p
            LEFT JOIN color c ON p.id_color = c.id_color
            WHERE p.ten_san_pham LIKE ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<h2>Kết quả tìm kiếm cho: ' . $_GET['searchTerm'] . '</h2>';
        echo '<div class="product-container">';
        $uniqueProducts = [];

        while ($row = $result->fetch_assoc()) {
            $productId = $row['id_product'];

            if (!isset($uniqueProducts[$productId])) {
                $uniqueProducts[$productId] = [
                    'id_product' => $row['id_product'],
                    'ten_san_pham' => $row['ten_san_pham'],
                    'gia' => $row['gia'],
                    'link_hinh_anh' => $row['link_hinh_anh'],
                    'colors' => [],
                ];
            }

            $color = [
                'id_color' => $row['id_color'],
                'tenmau' => $row['tenmau'],
                'hex_color' => $row['hex_color'],
                'link_hinh_anh' => $row['link_hinh_anh'],
            ];

            $uniqueProducts[$productId]['colors'][] = $color;
        }
        foreach ($uniqueProducts as $productId => $product) {
            echo '<div class="product">';
            echo '<a href="product_detail.php?id_product=' . $product['id_product'] . '&color_id=' . $product['colors'][0]['id_color'] . '">';
            echo '<img id="product-image-' . $product['id_product'] . '" src="' . $product['link_hinh_anh'] . '" alt="' . $product['ten_san_pham'] . '">';
            echo '<p>' . $product['ten_san_pham'] . '</p>';
            echo '<p class="product-price">Giá: ' . $product['gia'] . '</p>';
            echo '<div class="color-options">';

            $firstColor = reset($product['colors']);
            $defaultImage = $firstColor['link_hinh_anh'];

            foreach ($product['colors'] as $color) {
                $colorHex = $color['hex_color'];
                $colorId = $color['id_color'];
                $colorImage = $color['link_hinh_anh'] ?: $defaultImage;

                echo '<a href="product_detail.php?id_product=' . $product['id_product'] . '&color_id=' . $colorId . '">';
                echo '<div class="color-option" style="background-color: ' . $colorHex . ';" onmouseover="changeProductImage(' . $product['id_product'] . ', \'' . $colorImage . '\')" onmouseout="resetProductImage(' . $product['id_product'] . ', \'' . $defaultImage . '\')"></div>';
                echo '</a>';
            }

            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Website Bán Hàng</title>
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="js/products.js"></script>

</head>

<body>
<div class="navbar">
    <a href="home.php"><img src="images/logo.png" alt=""></a>
    <div class="navbar_list"></div>

<?php include('php/dropdown.php'); ?>
</body>

</html>
