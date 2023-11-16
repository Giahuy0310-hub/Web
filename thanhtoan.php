function createOrder($conn, $fullname, $phone, $email, $address, $province, $district, $wards, $note, $totalPrice, $date, $cartItems) {
    $sqlInsertIntoDonHang = "INSERT INTO DonHang (hoten, sodienthoai, email, sonha_duong, tinh_thanh, quan_huyen, phuong_xa, ghichu, totalPrice, date)
        VALUES (?, ?, ?, ?, (SELECT name FROM province WHERE province_id = ? LIMIT 1), (SELECT name FROM district WHERE district_id = ? LIMIT 1), (SELECT name FROM wards WHERE wards_id = ? LIMIT 1), ?, ?, ?)";

    $stmtInsertIntoDonHang = $conn->prepare($sqlInsertIntoDonHang);
    $stmtInsertIntoDonHang->bind_param("ssssssssds", $fullname, $phone, $email, $address, $province, $district, $wards, $note, $totalPrice, $date);

    if ($stmtInsertIntoDonHang->execute()) {
        $donHangId = $stmtInsertIntoDonHang->insert_id;

        // Initialize an array to store total quantities for each size
        $totalQuantities = ['S' => 0, 'M' => 0, 'L' => 0, 'XL' => 0];

        // Calculate total quantities for each size
        foreach ($cartItems as $item) {
            $totalQuantities[$item['size']] += (int) $item['quantity'];
        }

        // Update quantity for each size
        $sqlUpdateQuantity = "UPDATE products 
            SET size_S = GREATEST(0, size_S - ?)
            WHERE id_product = ? AND id_color = ?";

        $stmtUpdateQuantityS = $conn->prepare($sqlUpdateQuantity);

        $sqlUpdateQuantity = "UPDATE products 
            SET size_M = GREATEST(0, size_M - ?)
            WHERE id_product = ? AND id_color = ?";

        $stmtUpdateQuantityM = $conn->prepare($sqlUpdateQuantity);

        $sqlUpdateQuantity = "UPDATE products 
            SET size_L = GREATEST(0, size_L - ?)
            WHERE id_product = ? AND id_color = ?";

        $stmtUpdateQuantityL = $conn->prepare($sqlUpdateQuantity);

        $sqlUpdateQuantity = "UPDATE products 
            SET size_XL = GREATEST(0, size_XL - ?)
            WHERE id_product = ? AND id_color = ?";

        $stmtUpdateQuantityXL = $conn->prepare($sqlUpdateQuantity);

        // Loop through cart items and execute the corresponding update statement for each size
        foreach ($cartItems as $item) {
            switch ($item['size']) {
                case 'S':
                    $stmtUpdateQuantityS->bind_param("iii", $item['quantity'], $item['id_product'], $item['id_color']);
                    $stmtUpdateQuantityS->execute();
                    break;
                case 'M':
                    $stmtUpdateQuantityM->bind_param("iii", $item['quantity'], $item['id_product'], $item['id_color']);
                    $stmtUpdateQuantityM->execute();
                    break;
                case 'L':
                    $stmtUpdateQuantityL->bind_param("iii", $item['quantity'], $item['id_product'], $item['id_color']);
                    $stmtUpdateQuantityL->execute();
                    break;
                case 'XL':
                    $stmtUpdateQuantityXL->bind_param("iii", $item['quantity'], $item['id_product'], $item['id_color']);
                    $stmtUpdateQuantityXL->execute();
                    break;
            }
            $sqlIncrementSoldQuantity = "UPDATE products 
        SET so_luong_da_ban = so_luong_da_ban + ? 
        WHERE id_product = ? AND id_color = ?";
    
    $stmtIncrementSoldQuantity = $conn->prepare($sqlIncrementSoldQuantity);
    $stmtIncrementSoldQuantity->bind_param("iii", $item['quantity'], $item['id_product'], $item['id_color']);
    $stmtIncrementSoldQuantity->execute();
        }

        // Insert order details
        $sqlCopyToDonHang = "INSERT INTO chitietdonhang (id_donhang, id_product, id_color, size, quantity, gia, link_hinh_anh, ten_san_pham)
            SELECT ?, id_product, id_color, size, quantity, gia, link_hinh_anh, ten_san_pham FROM giohang";
        $stmtCopyToDonHang = $conn->prepare($sqlCopyToDonHang);
        $stmtCopyToDonHang->bind_param("i", $donHangId);
        $stmtCopyToDonHang->execute();

        
        // Clear the cart
        $sqlDeleteFromCart = "DELETE FROM giohang";
        $conn->query($sqlDeleteFromCart);

        $conn->commit();

        return true;
    } else {
        // Handle errors
        return false;
    }
}