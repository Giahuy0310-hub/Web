-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 20, 2023 lúc 05:19 PM
-- Phiên bản máy phục vụ: 10.4.27-MariaDB
-- Phiên bản PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `project9`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id_dm` int(11) NOT NULL,
  `TenDanhMuc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id_dm`, `TenDanhMuc`) VALUES
(1, 'Quần'),
(2, 'Áo'),
(3, 'Giày');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `color`
--

CREATE TABLE `color` (
  `id_color` int(11) NOT NULL,
  `tenmau` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `hex_color` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `color`
--

INSERT INTO `color` (`id_color`, `tenmau`, `hex_color`) VALUES
(1, 'Màu 1', '#FF0000'),
(2, 'Màu 2', '#00FF00'),
(3, 'Màu 3', '#0000FF');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `login`
--

INSERT INTO `login` (`id`, `fullname`, `phone_number`, `email`, `password`) VALUES
(1, 'Gia Huy', '0898877325', 'giahuye@gmail.com', '$2y$10$WeMYqCFeBXB/OlCLFresZ.S0LjL4uUVYWRmok2k833sOy8empD5Eq');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `id_product` int(11) DEFAULT NULL,
  `id_dm` int(11) DEFAULT NULL,
  `id_color` int(11) DEFAULT NULL,
  `ten_san_pham` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `link_hinh_anh` text DEFAULT NULL,
  `loaisanpham` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gia` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `img1` text DEFAULT NULL,
  `img2` text DEFAULT NULL,
  `img3` text DEFAULT NULL,
  `img4` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `so_danh_gia` int(11) DEFAULT NULL,
  `so_luong_da_ban` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `id_product`, `id_dm`, `id_color`, `ten_san_pham`, `link_hinh_anh`, `loaisanpham`, `gia`, `img1`, `img2`, `img3`, `img4`, `rating`, `so_danh_gia`, `so_luong_da_ban`) VALUES
(1, 1, 1, 1, 'Sản phẩm 1', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c6103b2e.jpg', 'Loại 1', '100.00', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c6123090.jpg', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c60a165f.jpg', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c60baf1c.jpg', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c60d5a0c.jpg', '4.50', 100, 2),
(2, 2, 2, 2, 'Sản phẩm 2', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c6123090.jpg', 'Loại 2', '75.50', 'img2_1.jpg', 'img2_2.jpg', 'img2_3.jpg', 'img2_4.jpg', '4.00', 50, 3),
(3, 3, 1, 3, 'Sản phẩm 3', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c60a165f.jpg', 'Loại 1', '120.00', 'img3_1.jpg', 'img3_2.jpg', 'img3_3.jpg', 'img3_4.jpg', '4.80', 200, 0),
(4, 1, 2, 2, 'Sản phẩm 4', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c60a165f.jpg', 'Loại 1', '100.00', 'https://4men.com.vn/images/thumbs/2023/09/ao-so-mi-tay-ngan-nep-giau-nut-regular-sm138-18254-slide-products-651393776d2ac.jpg', 'https://4men.com.vn/images/thumbs/2023/09/ao-so-mi-tay-ngan-nep-giau-nut-regular-sm138-18254-slide-products-6513937793505.jpg', 'https://4men.com.vn/images/thumbs/2023/09/ao-so-mi-tay-ngan-nep-giau-nut-regular-sm138-18254-slide-products-65139377b7f31.jpg', 'https://4men.com.vn/images/thumbs/2023/09/ao-so-mi-tay-ngan-nep-giau-nut-regular-sm138-18254-slide-products-65139377cfbbb.jpg', '4.50', 100, 0),
(5, 1, 1, 3, 'Sản phẩm 5', 'https://4men.com.vn/images/thumbs/2023/08/quan-tay-nazafu-qt004-mau-xanh-den-18196-slide-products-64d063b252187.jpg', NULL, '99', NULL, NULL, NULL, NULL, NULL, NULL, 0),
(8, 4, 1, 3, 'Sản phẩm 6', 'https://4men.com.vn/images/thumbs/2023/08/quan-tay-nazafu-qt004-mau-xanh-den-18196-slide-products-64d063b252187.jpg', NULL, '778', NULL, NULL, NULL, NULL, NULL, NULL, 0),
(9, 5, 1, 3, 'Sản phẩm 7', 'https://4men.com.vn/thumbs/2023/02/ao-so-mi-soc-slimfit-vien-mau-sm128-33769-p.jpg', NULL, '889', NULL, NULL, NULL, NULL, NULL, NULL, 0),
(10, 6, 1, 3, 'Sản phẩm 8', 'https://4men.com.vn/thumbs/2023/08/quan-tay-nazafu-linen-den-qt1137-34481-p.jpg', NULL, '89', NULL, NULL, NULL, NULL, NULL, NULL, 0),
(11, 7, 1, 3, 'Sản phẩm 9', 'https://4men.com.vn/thumbs/2023/08/quan-tay-nazafu-slimfit-qt1140-34491-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(12, 8, 1, 3, 'Sản phẩm 10', 'https://4men.com.vn/thumbs/2023/08/quan-tay-nazafu-linen-den-qt1137-34481-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(14, 5, 1, 2, 'Sản phẩm 11', 'https://4men.com.vn/thumbs/2023/02/ao-so-mi-soc-slimfit-vien-mau-sm128-33769-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(15, 6, 1, 2, 'Sản phẩm 12', 'https://4men.com.vn/thumbs/2023/08/quan-tay-nazafu-linen-den-qt1137-34481-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(16, 7, 1, 2, 'Sản phẩm 13', 'https://4men.com.vn/thumbs/2023/08/quan-tay-nazafu-slimfit-qt1140-34491-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(17, 8, 1, 2, 'Sản phẩm 14', 'https://4men.com.vn/thumbs/2023/08/quan-tay-nazafu-linen-den-qt1137-34481-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(19, 5, 1, 1, 'Sản phẩm 15', 'https://4men.com.vn/thumbs/2023/02/ao-so-mi-soc-slimfit-vien-mau-sm128-33769-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(20, 6, 1, 1, 'Sản phẩm 16', 'https://4men.com.vn/thumbs/2023/08/quan-tay-nazafu-linen-den-qt1137-34481-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(21, 7, 1, 1, 'Sản phẩm 17', 'https://4men.com.vn/thumbs/2023/08/quan-tay-nazafu-slimfit-qt1140-34491-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(22, 8, 1, 1, 'Sản phẩm 18', 'https://4men.com.vn/thumbs/2023/08/quan-tay-nazafu-linen-den-qt1137-34481-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(23, 11, 1, 3, 'Sản phẩm 19', 'https://4men.com.vn/thumbs/2023/07/quan-tay-slimfit-tron-basic-qt042-mau-den-34407-p.JPG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10),
(24, 12, 1, 3, 'Sản phẩm 20', 'https://4men.com.vn/thumbs/2023/07/quan-jeans-regular-rach-light-blue-qj061-mau-xanh-34397-p.JPG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8),
(25, 13, 1, 3, 'Sản phẩm 21', 'https://4men.com.vn/thumbs/2023/07/quan-short-caro-lung-thun-form-slimfit-qs046-34335-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9),
(28, 14, 2, 3, 'Sản phẩm 22', 'https://4men.com.vn/thumbs/2023/02/ao-so-mi-soc-slimfit-vien-mau-sm128-33769-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(29, 15, 2, 3, 'Sản phẩm 23', 'https://4men.com.vn/thumbs/2023/09/ao-so-mi-tay-ngan-nep-giau-nut-regular-sm138-34571-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8),
(30, 16, 2, 3, 'Sản phẩm 24', 'https://4men.com.vn/thumbs/2023/08/ao-so-mi-modal-tron-chong-nhan-form-regular-sm137-mau-trang-34480-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(31, 17, 2, 3, 'Sản phẩm 25', 'https://4men.com.vn/thumbs/2023/08/ao-so-mi-tay-ngan-modal-tron-chong-nhan-form-slimfit-sm136-mau-trang-34477-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(32, 18, 2, 3, 'Sản phẩm 26', 'https://4men.com.vn/thumbs/2023/07/ao-so-mi-nazafu-tay-ngan-sm052-mau-trang-34464-p.JPG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(33, 19, 2, 3, 'Sản phẩm 27', 'https://4men.com.vn/thumbs/2023/07/ao-len-phoi-mau-al011-34429-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(34, 20, 2, 3, 'Sản phẩm 28', 'https://4men.com.vn/thumbs/2023/05/ao-so-mi-denim-ra-tam-giac-form-regular-sm134-34213-p.JPG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(35, 21, 2, 3, 'Sản phẩm 29', 'https://4men.com.vn/thumbs/2023/05/ao-so-mi-linen-co-tru-form-regular-sm132-34154-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(36, 22, 2, 3, 'Sản phẩm 30', 'https://4men.com.vn/thumbs/2023/01/ao-so-mi-slimfit-khuy-noi-sm130-33691-p.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_color`
--

CREATE TABLE `product_color` (
  `id` int(11) NOT NULL,
  `id_product` int(11) DEFAULT NULL,
  `id_color` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_color`
--

INSERT INTO `product_color` (`id`, `id_product`, `id_color`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_id`
--

CREATE TABLE `product_id` (
  `id` int(11) NOT NULL,
  `id_product` char(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_id`
--

INSERT INTO `product_id` (`id`, `id_product`) VALUES
(1, 'SP001'),
(2, 'SP002'),
(3, 'SP003'),
(4, 'SP04'),
(5, 'SP05'),
(6, 'SP06'),
(7, 'SP07'),
(8, 'SP09'),
(11, 'SP10'),
(12, 'SP11'),
(13, 'SP12'),
(14, 'SP13'),
(15, 'SP14'),
(16, 'SP15'),
(17, 'SP16'),
(18, 'SP17'),
(19, 'SP18'),
(20, 'SP19'),
(21, 'SP20'),
(22, 'SP21'),
(23, 'SP22'),
(24, 'SP23'),
(25, 'SP24'),
(26, 'SP25'),
(27, 'SP26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

CREATE TABLE `product_reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_reviews`
--

INSERT INTO `product_reviews` (`review_id`, `product_id`, `review_text`, `rating`, `review_date`, `customer_name`) VALUES
(1, 1, 'Sản phẩm tốt, tôi rất hài lòng!', '4.50', '2023-10-12', 'Người dùng 1'),
(2, 2, 'Rất đẹp và chất lượng tốt', '4.80', '2023-10-13', 'Người dùng 2'),
(3, 1, 'Không thích sản phẩm này', '2.00', '2023-10-14', 'Người dùng 3');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_dm`);

--
-- Chỉ mục cho bảng `color`
--
ALTER TABLE `color`
  ADD PRIMARY KEY (`id_color`);

--
-- Chỉ mục cho bảng `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_color` (`id_color`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_dm` (`id_dm`);

--
-- Chỉ mục cho bảng `product_color`
--
ALTER TABLE `product_color`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_color` (`id_color`);

--
-- Chỉ mục cho bảng `product_id`
--
ALTER TABLE `product_id`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_product` (`id_product`);

--
-- Chỉ mục cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `fk_product_reviews_product_id` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id_dm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `color`
--
ALTER TABLE `color`
  MODIFY `id_color` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT cho bảng `product_color`
--
ALTER TABLE `product_color`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `product_id`
--
ALTER TABLE `product_id`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`id_color`) REFERENCES `color` (`id_color`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `product_id` (`id`),
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`id_dm`) REFERENCES `categories` (`id_dm`);

--
-- Các ràng buộc cho bảng `product_color`
--
ALTER TABLE `product_color`
  ADD CONSTRAINT `product_color_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `product_id` (`id`),
  ADD CONSTRAINT `product_color_ibfk_2` FOREIGN KEY (`id_color`) REFERENCES `color` (`id_color`);

--
-- Các ràng buộc cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `fk_product_reviews_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_product`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
