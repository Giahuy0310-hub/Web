<!DOCTYPE html>
<html>
<head>
    <style>
        /* CSS cho khung chứa ngôi sao */
        .rating {
            unicode-bidi: bidi-override;
            direction: rtl;
            font-size: 24px; /* Điều chỉnh kích thước ngôi sao */
        }
        .star {
            color: gold; /* Màu của ngôi sao được đánh giá */
        }
        .empty-star {
            color: gray; /* Màu của ngôi sao chưa được đánh giá */
        }
    </style>
</head>
<body>

<?php
    $rating = 4.1; // Điểm số sao bạn muốn hiển thị
    if ($rating < 0) {
        $rating = 0;
    } elseif ($rating > 5) {
        $rating = 5;
    }
?>

<div class="rating">
    <?php
    for ($i = 1; $i <= floor($rating); $i++) {
        echo '<span class="star">★</span>';
    }

    if ($rating - floor($rating) > 0) {
        echo '<span class="star">★</span>';
    }

    for ($i = ceil($rating); $i < 5; $i++) {
        echo '<span class="empty-star">★</span>';
    }
    ?>
</div>

</body>
</html>
