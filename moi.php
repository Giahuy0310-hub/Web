<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .add-to-cart span {
            display: block;
            text-align: center;
            position: relative;
            z-index: 1;
            font-size: 14px;
            font-weight: 600;
            line-height: 24px;
            color: var(--text-color);
            opacity: var(--text-opacity);
            transform: translateX(var(--text-x)) translateZ(0);
        }

        .add-to-cart .shirt,
        .add-to-cart .cart {
            pointer-events: none;
            position: absolute;
            left: 50%;
        }

        .add-to-cart::before {
            content: "";
            display: block;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            border-radius: 5px;
            transition: background 0.25s;
            background: var(--background, var(--background-default));
            transform: scaleX(var(--background-scale));
            /* translateZ(0); */
        }

        /* Style for the button */
        .add-to-cart-button {
            background-color: #007bff; /* Change to your desired background color */
            color: #fff; /* Text color */
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php
    echo '<button id="addToCartButton" class="add-to-cart-button" onclick="addToCart(\'' . $productDetail['ten_san_pham'] . '\', ' . $productDetail['gia'] . ', ' . $productDetail['id_color'] . ', \'' . $productDetail['link_hinh_anh'] . '\', \'' . $id_product . '\')">Thêm vào giỏ hàng</button>';
    ?>
</body>
</html>
