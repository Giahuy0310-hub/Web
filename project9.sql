-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 10, 2023 lúc 08:31 AM
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
(1, 'Danh Mục 1'),
(2, 'Danh Mục 2'),
(3, 'Danh Mục 3');

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
  `so_danh_gia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `id_product`, `id_dm`, `id_color`, `ten_san_pham`, `link_hinh_anh`, `loaisanpham`, `gia`, `img1`, `img2`, `img3`, `img4`, `rating`, `so_danh_gia`) VALUES
(1, 1, 1, 1, 'Sản phẩm 1', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c6103b2e.jpg', 'Loại 1', '100.00', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c6123090.jpg', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c60a165f.jpg', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c60baf1c.jpg', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c60d5a0c.jpg', '4.50', 100),
(2, 2, 2, 2, 'Sản phẩm 2', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c6123090.jpg', 'Loại 2', '75.50', 'img2_1.jpg', 'img2_2.jpg', 'img2_3.jpg', 'img2_4.jpg', '4.00', 50),
(3, 3, 1, 3, 'Sản phẩm 3', 'https://4men.com.vn/images/thumbs/2023/09/-18261-slide-products-65169c60a165f.jpg', 'Loại 1', '120.00', 'img3_1.jpg', 'img3_2.jpg', 'img3_3.jpg', 'img3_4.jpg', '4.80', 200),
(4, 1, 1, 2, 'Sản phẩm 1', 'https://4men.com.vn/images/thumbs/2023/09/ao-so-mi-tay-ngan-nep-giau-nut-regular-sm138-18254-slide-products-6513937758ff7.jpg', 'Loại 1', '100.00', 'https://4men.com.vn/images/thumbs/2023/09/ao-so-mi-tay-ngan-nep-giau-nut-regular-sm138-18254-slide-products-651393776d2ac.jpg', 'https://4men.com.vn/images/thumbs/2023/09/ao-so-mi-tay-ngan-nep-giau-nut-regular-sm138-18254-slide-products-6513937793505.jpg', 'https://4men.com.vn/images/thumbs/2023/09/ao-so-mi-tay-ngan-nep-giau-nut-regular-sm138-18254-slide-products-65139377b7f31.jpg', 'https://4men.com.vn/images/thumbs/2023/09/ao-so-mi-tay-ngan-nep-giau-nut-regular-sm138-18254-slide-products-65139377cfbbb.jpg', '4.50', 100);

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
(3, 'SP003');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `product_color`
--
ALTER TABLE `product_color`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `product_id`
--
ALTER TABLE `product_id`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
