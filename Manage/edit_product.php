<?php

require_once('db_connection.php');

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    $getProductQuery = "SELECT * FROM products WHERE id_product = '$productId'";
    $result = $conn->query($getProductQuery);

    if ($result && $result->num_rows > 0) {
        $productDetails = $result->fetch_assoc();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Handle edit operation here
            $ten_san_pham = $_POST["ten_san_pham"];

            $updateProductQuery = "UPDATE products SET ten_san_pham = '$ten_san_pham' WHERE id_product = '$productId'";

            if ($conn->query($updateProductQuery) === TRUE) {
                echo "Product updated successfully.";
            } else {
                echo "Error updating product: " . $conn->error;
            }
        } else {
            echo "<form method='post' action=''>";
            echo "ID Product: <input type='text' name='id_product' value='".$productDetails['id_product']."' readonly><br>";
            echo "ID Color: <input type='text' name='id_color' value='".$productDetails['id_color']."' readonly><br>";
            echo "Tên Sản Phẩm: <input type='text' name='ten_san_pham' value='".$productDetails['ten_san_pham']."'><br>";
            echo "<input type='submit' value='Update'>";
            echo "</form>";
        }
        
        echo "<a href='edit_product.php?id=".$productId."&action=delete'>Delete Product</a>";
    } else {
        echo "Product not found.";
    }
} else {
    echo "Product ID not provided.";
}

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $deleteProductQuery = "DELETE FROM products WHERE id_product = '$productId'";
    
    if ($conn->query($deleteProductQuery) === TRUE) {
        echo "Product deleted successfully.";
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}

$conn->close();
?>
