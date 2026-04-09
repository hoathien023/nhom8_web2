-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 06, 2025 at 04:51 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `duan1_traicay`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`id`, `user_id`, `address`) VALUES
(17, 15, 'Vĩnh Long 123'),
(18, 6, 'Hà Nội'),
(19, 16, 'Cầu giấy, Hà Nội');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `product_price` int NOT NULL,
  `product_quantity` int NOT NULL,
  `product_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `product_id`, `user_id`, `product_name`, `product_price`, `product_quantity`, `product_image`) VALUES
(80, 25, 9, 'Cây cam ngọt', 160000, 1, 'cay-cam-ngot.jpg'),
(81, 23, 9, 'Sách đất rừng', 120000, 1, 'dat-rung.jpg'),
(82, 27, 9, 'Người bà tài giỏi(tái bản 2022)', 180000, 1, 'nguoi-ba.jpg'),
(83, 2, 9, 'Sách mới', 110000, 3, 'book-2.png'),
(138, 32, 11, 'Kiếm Tiền Từ Tiktok', 100000, 1, 'kiem-tien-titok.jpg'),
(203, 32, 8, 'Iphone', 100000, 1, 'iphone2.jpg'),
(212, 30, 6, 'Áo Thu Đông Nữ Giữ Nhiệt Cổ 3cm', 102000, 1, 'quanao6.png'),
(213, 32, 6, 'Áo trẻ em', 100000, 1, 'quanao4.png'),
(214, 25, 6, 'Áo polo Nam', 160000, 1, 'quanao8.png'),
(221, 31, 16, 'Dưa hấu không hạt', 126000, 1, 'sp-trai-cay-2.png'),
(222, 30, 16, 'Nho đen Mỹ', 279000, 1, 'sp-trai-cay-3.png'),
(223, 32, 16, 'Cam vàng Úc', 100000, 1, 'sp-trai-cay-1.png');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `image`, `status`) VALUES
(1, 'Chưa có danh mục', 'tieng-anh-nguoi-moi.jpg', 1),
(2, 'Trái cây Việt Nam', 'sp-trai-cay-2.png', 1),
(4, 'Trái cây nhập khẩu', 'sp-trai-cay-3.png', 1),
(6, 'Trái cây cắt sẵn', 'sp-trai-cay-4.png', 1),
(16, 'Quà tặng trái cây', 'sp-traicay-5.jpg', 1),
(17, 'Mâm Ngũ Quả', 'sp-trai-cay-15.png', 1),
(19, 'Hộp quà Nguyệt Cát', 'sp-trai-cay-16.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 ẩn 1 hiện',
  `user_id` int NOT NULL,
  `product_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `content`, `date`, `status`, `user_id`, `product_id`) VALUES
(1, 'Helllo', '2023-11-25 19:46:51', 1, 6, 26),
(2, 'Tôi là khoa nè', '2023-11-25 20:06:15', 1, 6, 21),
(3, 'Admin nè xin chào mn', '2023-11-25 20:48:50', 1, 8, 26),
(4, 'Hello', '2023-11-26 12:00:44', 1, 6, 27),
(5, 'Sản phẩm tốt đọc hay nên mua nha mn', '2023-11-29 21:11:44', 1, 6, 27),
(8, 'Ngày 12/5/2023 Hello every one', '2023-12-05 09:46:35', 1, 6, 28),
(11, 'Xin chào mọi người trên sharecode', '2024-09-05 21:01:47', 1, 6, 31),
(12, 'mUA CODE ỦNG HỘ NHA HIHI', '2024-09-05 21:06:58', 1, 6, 32),
(13, 'Sản phẩm ăn ngon ngọt, nên mua', '2025-11-06 23:04:48', 1, 16, 31);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `orderdetails_id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`orderdetails_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(21, 10, 24, 1, 120000),
(22, 10, 27, 1, 180000),
(23, 11, 1, 2, 110000),
(24, 11, 24, 1, 120000),
(25, 12, 23, 1, 120000),
(26, 12, 20, 1, 160000),
(27, 13, 25, 2, 160000),
(28, 13, 26, 2, 200000),
(29, 14, 25, 4, 160000),
(30, 14, 27, 1, 180000),
(31, 14, 26, 2, 200000),
(32, 15, 23, 1, 120000),
(33, 15, 26, 1, 200000),
(34, 16, 27, 1, 180000),
(35, 16, 26, 1, 200000),
(36, 16, 20, 1, 160000),
(37, 17, 29, 1, 50000),
(38, 17, 31, 1, 126000),
(39, 17, 32, 2, 100000),
(40, 18, 31, 1, 126000),
(41, 18, 24, 1, 120000),
(42, 19, 30, 1, 102000),
(48, 22, 29, 1, 50000),
(49, 22, 28, 1, 180000),
(50, 23, 1, 2, 159000),
(51, 23, 24, 1, 120000),
(52, 24, 15, 1, 95000),
(53, 24, 14, 1, 102000),
(54, 25, 21, 1, 88000),
(55, 26, 21, 4, 88000),
(56, 27, 29, 1, 50000),
(57, 27, 1, 1, 159000),
(58, 28, 28, 1, 180000),
(59, 29, 2, 1, 97000),
(60, 29, 29, 1, 50000),
(61, 29, 1, 1, 159000),
(62, 30, 31, 3, 126000),
(63, 31, 2, 2, 97000),
(64, 31, 1, 1, 159000),
(65, 32, 32, 1, 100000),
(66, 33, 21, 1, 88000),
(67, 33, 25, 1, 160000),
(68, 33, 28, 3, 180000),
(69, 34, 29, 1, 50000),
(70, 34, 31, 3, 126000),
(71, 35, 1, 1, 159000),
(72, 35, 28, 2, 180000),
(73, 36, 25, 1, 160000),
(74, 36, 27, 1, 180000),
(75, 36, 31, 2, 126000),
(76, 37, 32, 1, 100000),
(77, 38, 31, 1, 126000),
(78, 38, 30, 1, 102000),
(79, 39, 25, 1, 160000),
(80, 39, 29, 2, 50000),
(81, 39, 30, 1, 102000),
(82, 40, 17, 1, 187000),
(83, 40, 16, 1, 90000),
(84, 40, 18, 2, 120000),
(85, 41, 25, 5, 160000),
(86, 42, 29, 2, 50000),
(87, 42, 17, 1, 187000),
(88, 43, 27, 1, 180000),
(89, 43, 31, 1, 126000),
(90, 44, 1, 1, 159000),
(91, 44, 28, 1, 180000),
(92, 45, 28, 1, 180000),
(93, 45, 31, 2, 126000),
(94, 46, 6, 1, 100000),
(95, 47, 6, 5, 100000),
(96, 48, 2, 4, 97000),
(97, 48, 1, 1, 159000),
(98, 49, 31, 1, 126000),
(99, 50, 27, 3, 180000),
(100, 50, 32, 1, 100000),
(101, 51, 32, 1, 100000),
(102, 51, 26, 1, 200000),
(103, 52, 31, 1, 126000),
(104, 52, 32, 2, 100000),
(105, 53, 28, 1, 180000),
(106, 53, 30, 2, 102000),
(107, 54, 27, 1, 180000),
(108, 54, 30, 1, 102000),
(109, 54, 31, 1, 126000),
(110, 55, 24, 2, 120000),
(111, 55, 31, 1, 126000),
(112, 56, 31, 1, 126000),
(113, 56, 26, 1, 200000),
(114, 57, 31, 1, 126000),
(115, 58, 28, 1, 105000),
(116, 58, 18, 1, 120000),
(117, 58, 31, 1, 126000),
(118, 59, 28, 2, 105000),
(119, 59, 32, 1, 100000),
(120, 59, 27, 2, 95000);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` int NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `date`, `total`, `address`, `phone`, `note`, `status`) VALUES
(10, 6, '2023-11-27 22:13:51', 300000, 'Can tho', '0909135986', '', 2),
(11, 6, '2023-11-28 09:00:28', 340000, 'Can tho', '0909135986', 'Gói sách kĩ giúp em lần trước mua bị rách', 3),
(12, 7, '2023-11-28 09:24:42', 280000, 'Kiên Giang', '0336216654', 'Hello my friend', 3),
(13, 9, '2023-11-28 12:01:11', 720000, 'Cái Răng, Cần Thơ', '0909135969', 'Đóng gói hàng kĩ', 2),
(14, 6, '2023-11-28 14:00:19', 1220000, 'Can tho', '0909135986', 'hi', 4),
(15, 6, '2023-11-28 18:22:55', 320000, 'Can tho', '0909135986', 'Chúc 1 ngày vui', 4),
(16, 6, '2023-11-29 22:07:55', 540000, 'Can tho', '0909135986', 'Mua hang 29/11/2023', 3),
(17, 10, '2023-12-03 10:12:41', 376000, 'Ninh Kiều, Cần Thơ', '0909135985', 'Gói hàng cẩn thận giao nhanh giúp tôi ', 1),
(18, 6, '2023-12-04 18:23:31', 246000, 'Cái Răng, Cần Thơ', '0909135329', 'Giao hàng nhanh nha, đang cần gấp', 1),
(19, 6, '2023-12-04 19:43:58', 102000, 'Quận Đống Đa, Hà Nội', '0909246546', 'Mong là sách đọc hay', 4),
(22, 11, '2023-12-04 20:28:09', 230000, 'Sóc Trăng', '0336246546', 'Đóng hàng kĩ', 3),
(23, 11, '2023-12-04 20:29:51', 279000, 'Cần Thơ', '0909006764', 'Hello', 1),
(24, 11, '2023-12-04 21:49:06', 197000, 'Cần Thơ', '0909006764', '', 1),
(25, 11, '2023-12-04 21:56:40', 88000, 'Cần Thơ', '0909006764', '', 1),
(26, 11, '2023-12-04 22:00:39', 352000, 'Cần Thơ', '0909006764', '', 1),
(27, 10, '2023-12-06 22:10:19', 209000, 'Ninh Kiều, Cần Thơ', '0909135985', '', 2),
(28, 10, '2023-12-06 22:12:15', 180000, 'Long Hồ, Vĩnh Long', '0909135399', 'Mua hàng cho bạn ở quê', 1),
(29, 6, '2023-12-07 08:48:32', 306000, 'Ninh Kiều, Cần Thơ', '0909135329', '', 3),
(30, 6, '2023-12-07 08:50:28', 378000, 'Quận Mỹ Đình, Hà Nội', '0336216546', 'Hello 2023', 2),
(31, 6, '2023-12-11 17:33:40', 353000, 'Long Biên, Hà Nội', '0336216546', 'Giao hàng nhanh giúp tôi', 1),
(32, 6, '2023-12-11 19:07:50', 100000, 'Cần Thơ', '0336216546', 'Giao nhanh', 1),
(33, 11, '2023-12-11 19:13:10', 788000, 'Quận Cầu Giấy Hà Nội', '0336216546', 'Sách hay quóaa', 1),
(34, 10, '2023-12-11 19:55:09', 428000, 'Quận Cầu Giấy Hà Nội', '0909135329', 'Giao hàng nhanh giúp tôi', 4),
(35, 10, '2023-12-11 20:01:28', 519000, 'Quận Cầu Giấy Hà Nội', '0336216546', 'Giao hàng nhanh giúp tôi', 2),
(36, 6, '2023-12-12 10:11:51', 592000, 'Anh Khánh, Ninh Kiều, Cần Thơ', '0336246546', 'Đóng hàng kĩ', 1),
(37, 6, '2023-12-12 10:25:55', 100000, 'Anh Khánh, Ninh Kiều, Cần Thơ', '0909135985', 'Hello', 4),
(38, 6, '2023-12-12 10:31:24', 228000, 'Anh Khánh, Ninh Kiều, Cần Thơ', '0909135985', 'Đóng hàng kĩ', 1),
(39, 7, '2023-12-12 17:38:21', 362000, 'Số 14 Nguyễn Công Trứ, phường Vĩnh Thanh, thành phố Rạch Giá, tỉnh Kiên Giang', '0336216123', 'Sách hay', 2),
(40, 6, '2023-12-12 17:47:50', 517000, 'Số 14 Nguyễn Công Trứ, phường Vĩnh Thanh, thành phố Rạch Giá, tỉnh Kiên Giang', '0909135329', 'Đóng gói hàng kĩ', 1),
(41, 8, '2023-12-12 17:53:39', 800000, 'Anh Khánh, Ninh Kiều, Cần Thơ', '0336246546', 'Gói hàng kĩ', 1),
(42, 6, '2023-12-12 18:19:03', 287000, 'Quận Cầu Giấy Hà Nội', '0909135329', 'Mua hàng nè hihi', 2),
(43, 10, '2023-12-12 18:22:50', 306000, 'Cái Răng, Cần Thơ', '0336246546', 'Gói hàng kĩ', 1),
(44, 10, '2023-12-12 18:32:55', 339000, 'Số 14 Nguyễn Công Trứ, phường Vĩnh Thanh, thành phố Rạch Giá, tỉnh Kiên Giang', '0909135329', 'Giao hàng nhanh nha', 1),
(45, 6, '2023-12-13 15:12:18', 432000, 'Số 14 thành phố Rạch Giá, tỉnh Kiên Giang', '0336216546', 'Test mua hàng 13/12/2023', 1),
(46, 10, '2023-12-13 16:54:29', 100000, 'Ninh Kiều, Cần Thơ', '0909135985', 'Hảo mua hàng nè', 1),
(47, 10, '2023-12-13 17:01:33', 500000, 'Ninh Kiều, Cần Thơ', '0909135985', '', 1),
(48, 7, '2023-12-13 17:07:00', 547000, 'Kiên Giang', '0336216654', '', 1),
(49, 6, '2025-03-26 20:18:15', 126000, 'Ninh Kiều, Cần Thơ', '0909135329', '', 1),
(50, 6, '2025-03-26 20:20:18', 640000, 'Cần Thơ', '0336216546', '', 1),
(51, 6, '2025-04-19 20:51:54', 300000, 'Anh Khánh, Ninh Kiều, Cần Thơ', '0336216546', 'Đóng hàng kĩ', 1),
(52, 6, '2025-04-21 18:28:19', 326000, 'Ninh Kiều, Cần Thơ', '0909135329', 'Giao nhanh', 1),
(53, 6, '2025-04-21 18:45:37', 384000, 'Anh Khánh, Ninh Kiều, Cần Thơ', '0909135985', 'Đóng hàng kĩ', 1),
(54, 6, '2025-09-05 20:56:24', 408000, 'Ninh Kiều, Cần Thơ', '0909135329', 'oki', 1),
(55, 6, '2025-09-05 21:02:30', 366000, 'Ninh Kiều, Cần Thơ', '0909135329', 'Đóng hàng kĩ', 4),
(56, 6, '2025-09-05 21:09:21', 326000, 'Hà Nội', '0909135555', 'GIAO NHANH NHA', 1),
(57, 6, '2025-10-10 20:33:44', 126000, 'xin chào  0909199999', '0909199999', 'Gói hàng kĩ', 1),
(58, 16, '2025-11-06 23:07:01', 351000, 'Quận 1 HCM', '0909789456', 'giao hàng nhanh', 4),
(59, 16, '2025-11-06 23:14:19', 500000, 'Quận 1, HCM', '0909123000', 'giao hàng nhanh', 3);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int NOT NULL,
  `category_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `views` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `category_id`, `title`, `image`, `author`, `content`, `views`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, '10+ Ý Tưởng Content Về Trái Cây Thu Hút Khách Hàng', 'bai-viet-1.png', 'Admin', '<p>Nếu bạn đang kinh doanh/viết <strong>content về trái cây</strong> cho doanh nghiệp thì hãy tham khảo những mẫu content mà ABC Digi gợi ý dưới đây. Ý tưởng và các mẫu content quảng cáo có sẵn sẽ giúp content của bạn thu hút khán giả, từ đó gia tăng chuyển đổi cho doanh&nbsp;nghiệp.</p><figure class=\"image\"><img style=\"aspect-ratio:641/2560;\" src=\"https://emvcy2pfzxa.exactdn.com/wp-content/uploads/2024/04/infographic-content-trai-cay-scaled.jpg?strip=all&amp;lossy=1&amp;quality=85&amp;webp=80&amp;avif=70&amp;sharp=1&amp;resize=641%2C2560&amp;ssl=1\" alt=\"infographic content trai cay scaled\" width=\"641\" height=\"2560\"></figure><h2><strong>I. 10+ ý tưởng content về trái cây hấp dẫn</strong></h2><ul><li><strong>Bài viết hướng dẫn:</strong> Viết một bài viết chi tiết về cách lựa chọn, bảo quản các loại trái cây khác nhau, đảm bảo giúp trái cây tươi ngon trong thời gian dài.</li></ul>', 0, 1, '2023-11-29 17:13:09', '2025-11-06 14:47:41'),
(5, 2, 'Top 5 Loại Hoa Quả Nhập Khẩu Đáng Mua Làm Quà Tặng Mùa Trung Thu Năm 2025', 'bai-viet-4.png', 'Admin', '<h2><strong>Ý Nghĩa Trung Thu &amp; Xu Hướng Quà Tặng Trung Thu 2025</strong></h2><p>Trung Thu không chỉ là Tết đoàn viên, mà còn là dịp để gắn kết gia đình, tri ân đối tác và gửi trao tình cảm. Năm 2025, xu hướng <strong>quà tặng Trung Thu</strong> chuyển dịch mạnh mẽ sang các sản phẩm <strong>hoa quả nhập khẩu cao cấp</strong> – vừa sang trọng, vừa mang ý nghĩa sức khỏe.</p><p><img src=\"https://cdn.hstatic.net/200000528965/file/y-nghia-pha-co-trung-thu_749759b2309a45f2b25c3ae97c271c73_grande.jpg\" alt=\"Top 5 Loại Hoa Quả Nhập Khẩu Đáng Mua Làm Quà Tặng Mùa Trung Thu Năm 2025\" width=\"600\" height=\"400\"></p><p><i>Xu hướng tặng quà Trung thu 2025</i></p><p>Theo khảo sát khách hàng của Klever Fruit, quà biếu không còn dừng lại ở bánh kẹo truyền thống, mà được ưa chuộng hơn khi mang tính cá nhân hóa, giàu dinh dưỡng và thể hiện đẳng cấp người tặng.</p><p><img src=\"https://cdn.hstatic.net/200000528965/file/u-dang-mua-lam-qua-tet-trung-thu-2025_77965294a37749ab888f4df5a985e152_grande.jpg\" alt=\"Top 5 Loại Hoa Quả Nhập Khẩu Đáng Mua Làm Quà Tặng Mùa Trung Thu Năm 2025\" width=\"600\" height=\"450\"></p><p><i>Top 5 Loại Hoa Quả Nhập Khẩu Đáng Mua Làm Quà Tặng Mùa Trung Thu Năm 2025</i></p><p>&nbsp;</p><h2><strong>Top 5 Loại Hoa Quả Nhập Khẩu Đáng Mua Làm Quà Tặng Trung Thu</strong></h2><h3><strong>1. Nho Mẫu Đơn Hàn Quốc – Biểu tượng sung túc</strong></h3><p>Nho mẫu đơn Hàn Quốc được mệnh danh là “nữ hoàng của các loại nho” với hương vị ngọt thanh, giòn mọng. Hình tròn viên mãn của từng chùm nho còn tượng trưng cho sự <strong>sung túc và trọn vẹn</strong> trong đời sống. Đây là loại quà được nhiều gia đình và doanh nghiệp lựa chọn để thể hiện lời chúc “vạn sự như ý” trong mùa đoàn viên.</p><p><img src=\"https://cdn.hstatic.net/files/200000528965/file/hop-qua-trung-thu-nho-mau-don-han-quoc-nguyet-kim_grande.jpg\" alt=\"Nho mẫu đơn hàn quốc\" width=\"600\" height=\"600\"></p><p><i>Hộp Quà Tặng Trung Thu Nho Mẫu Đơn Hàn Quốc tại Klever Fruit</i></p><p>Xem thêm <a href=\"https://kleverfruits.com.vn/products/hop-qua-trung-thu-03\">Nho Mẫu Đơn Hàn Quốc</a> tại Klever Fruit.</p><p>&nbsp;</p><h3><strong>2. Nho Royal Vine Hàn Quốc – Biểu tượng thịnh vượng</strong></h3><p>Royal Vine là giống nho quý hiếm, được nuôi trồng theo quy trình kiểm định nghiêm ngặt. Với lớp vỏ mỏng, thịt nho chắc, vị ngọt thơm với độ brix lên đến 20+, loại nho này không chỉ ngon miệng mà còn chứa hàm lượng <strong>resveratrol</strong> – hoạt chất tốt cho tim mạch.<br>Royal Vine còn mang ý nghĩa <strong>thịnh vượng và phát triển bền vững</strong>, là món quà lý tưởng cho doanh nghiệp muốn tri ân đối tác trong dịp Tết Trung Thu.</p><p><img src=\"https://cdn.hstatic.net/200000528965/file/hu-nho-royal-vine-han-quoc-nguyet-tim_16339817f944471db7868f1a57b400e7_grande.jpg\" alt=\"Nho Royal Vine Hàn Quốc – Biểu tượng thịnh vượng\" width=\"600\" height=\"600\"></p><p><a href=\"https://kleverfruits.com.vn/products/nho-royal-vine-han-quoc\"><i>Nho Royal Vine Hàn Quốc</i></a><i> – Biểu tượng thịnh vượng</i></p><p>&nbsp;</p><h3><strong>3. Dưa Musk Melon Nhật Bản – Đẳng cấp &amp; Quý hiếm</strong></h3><p>Nhắc đến <strong>dưa lưới Nhật (Muskmelon)</strong>, giới sành quà không thể bỏ qua biểu tượng của sự đẳng cấp. Với dáng tròn hoàn hảo, cuống dưa cong thanh thoát, Musk Melon từ lâu đã được xem là “ông hoàng của các loại dưa”.<br>Giá trị của một quả dưa có thể lên tới <strong>800.000đ – 1.000.000đ/quả</strong>, bởi giống dưa này được chăm sóc tỉ mỉ, mỗi cây chỉ nuôi duy nhất một quả để đạt hương vị ngọt đậm, thơm đặc trưng.<br>Không chỉ là món quà cao cấp, Musk Melon còn mang ý nghĩa <strong>may mắn, sung túc và thịnh vượng</strong>, đặc biệt thích hợp làm <strong>quà tặng Trung Thu</strong> cho người thân, sếp hoặc đối tác.</p>', 0, 1, '2023-11-29 17:25:47', '2025-11-06 14:50:31'),
(8, 9, 'Tại Sao Hoa Cẩm Chướng Trở Thành Lựa Chọn Hoàn Hảo Cho \"Dáng Hoa\" Klever Fruit', 'bai-viet-3.png', 'Admin', '<h2><strong>Hoa Cẩm Chướng – Biểu Tượng Vẻ Đẹp Truyền Đời Của Người Phụ Nữ Việt</strong></h2><p>Giữa muôn sắc hương, <strong>hoa cẩm chướng</strong> vẫn luôn giữ cho mình một nét riêng – giản dị mà bền bỉ, mềm mại mà kiên cường. Giống như những người phụ nữ Việt – dịu dàng trong cách sống, mạnh mẽ trong từng hành trình.</p><p>Khi Klever Fruit lựa chọn <strong>hoa cẩm chướng</strong> làm linh hồn cho <strong>BST “Dáng Hoa” 20/10</strong>, đó không chỉ là một loài hoa trang trí. Đó là <strong>lời tri ân gửi đến mọi dáng hoa của đời</strong> – mẹ, bà, vợ, cô giáo, con gái… mỗi người một sắc thái, nhưng đều tỏa hương từ sự yêu thương và hy sinh thầm lặng.</p><p><img src=\"https://cdn.hstatic.net/200000528965/file/-dep-truyen-doi-cua-nguoi-phu-nu-viet_2dbabecb9f2a4b0db8113d8142cd955b_grande.jpg\" alt=\"Tại Sao Hoa Cẩm Chướng Trở Thành Lựa Chọn Hoàn Hảo Cho &quot;Dáng Hoa&quot; Klever Fruit\" width=\"600\" height=\"450\"></p><p><i>Hoa Cẩm Chướng – Biểu Tượng Vẻ Đẹp Truyền Đời Của Người Phụ Nữ Việt</i></p><p>&nbsp;</p><h2><strong>Hai Sắc Hoa – Hai Thông Điệp Yêu Thương</strong></h2><p><strong>Cẩm Chướng Hồng</strong> – tượng trưng cho tình yêu, sự ngọt ngào và lòng biết ơn.<br>Là lựa chọn hoàn hảo cho <strong>quà tặng 20/10</strong> gửi <strong>cô giáo, sếp nữ, đối tác, vợ hay người yêu</strong> – những người phụ nữ truyền cảm hứng với phong thái tinh tế, hiện đại.</p><p><img src=\"https://cdn.hstatic.net/200000528965/file/en-doi-cua-nguoi-phu-nu-viet-ngay-nay_bc46d4958eae47eea5805318e91205a1_grande.jpg\" alt=\"Tại Sao Hoa Cẩm Chướng Trở Thành Lựa Chọn Hoàn Hảo Cho &quot;Dáng Hoa&quot; Klever Fruit\" width=\"600\" height=\"450\"></p><p><i><strong>Cẩm Chướng Hồng</strong> – tượng trưng cho tình yêu, sự ngọt ngào và lòng biết ơn</i></p><p>&nbsp;</p><p><strong>Cẩm Chướng Tím Đậm</strong> – sắc hoa của sự tôn kính và bền vững.<br>Klever Fruit chọn sắc tím đậm để tôn vinh <strong>mẹ và bà</strong> – những “dáng hoa” xưa nay vẫn kiêu hãnh tỏa hương, luôn vẹn nguyên tình yêu thương dù thời gian có đổi thay.</p><p><img src=\"https://cdn.hstatic.net/200000528965/file/oi-cua-nguoi-phu-nu-viet-ngay-xua__1__8b13e74ee6d04f689aa027b9076504cd_grande.jpg\" alt=\"Tại Sao Hoa Cẩm Chướng Trở Thành Lựa Chọn Hoàn Hảo Cho &quot;Dáng Hoa&quot; Klever Fruit\" width=\"600\" height=\"450\"></p><p><i>Cẩm Chướng Tím Đậm – sắc hoa của sự tôn kính và bền vững</i></p><p>&nbsp;</p><h2><strong>Sự Giao Thoa Giữa Hoa Và Quả – Nghệ Thuật Tặng Quà Mùa 20/10</strong></h2><p>Không dừng ở một bó hoa đẹp, Klever Fruit kết hợp <strong>hoa cẩm chướng</strong> cùng <strong>trái cây nhập khẩu cao cấp</strong> – tạo nên những <strong>set quà 20/10</strong> vừa thanh lịch, vừa chứa đựng giá trị dinh dưỡng và thẩm mỹ.</p><p>Mỗi hộp quà như một <strong>bản hòa ca giữa sắc – hương – vị</strong>, nơi những đóa cẩm chướng mềm mại tôn lên vẻ rực rỡ của <strong>nho Shine Muscat, táo Diva, kiwi vàng, cherry New Zealand...</strong> – tất cả hòa quyện trong một ngôn ngữ duy nhất: <strong>Yêu thương tinh tế</strong>.</p>', 0, 1, '2023-12-03 13:45:32', '2025-11-06 14:48:55'),
(9, 9, 'Quà 20/10 Cho Vợ, Người Yêu: Khi Trái Cây Cũng Biết Nói Lời Yêu', 'bai-viet-2.png', 'Admin', '<h2><strong>Quà 20/10 Cho Vợ,&nbsp;Người Yêu: Khi Trái Cây Cũng Biết Nói Lời Yêu</strong></h2><p>Tình yêu đôi khi không cần đến những điều quá lớn lao — chỉ cần người ấy nhớ ngày bạn mệt, hỏi han một câu “Em ăn chưa?”, hay khẽ gửi tặng một món quà nhỏ trong dịp 20/10, là đủ khiến tim ai đó ấm lên cả ngày.</p><p>Và nếu bạn đang băn khoăn không biết <strong>nên tặng gì cho người yêu 20/10</strong>, thì có lẽ <strong>một giỏ quà trái cây tươi &amp; hoa</strong> từ Klever Fruit chính là điều ngọt ngào, vừa tinh tế vừa khác biệt.</p><p><img src=\"https://cdn.hstatic.net/200000528965/file/yeu-qua-gio-qua-trai-cay-klever-fruit_ef0c3536670a49a8a38037ae66dd5773_grande.jpg\" alt=\"Quà 20/10 Cho Vợ, Người Yêu – Gửi Lời Yêu Qua Giỏ Quà Trái Cây Klever Fruit\" width=\"600\" height=\"450\"></p><p><i>Quà 20/10 Cho Vợ, Người Yêu: Khi Trái Cây Cũng Biết Nói Lời Yêu</i></p><p>&nbsp;</p><h2><strong>Khi tình yêu được gói ghém bằng những sắc hoa và hương trái</strong></h2><p>Khác với bó hoa nhanh tàn, <strong>giỏ quà Klever Fruit</strong> mang đến cảm giác <strong>vừa lãng mạn, vừa thực tế</strong>, vừa có thể dùng để thưởng thức, vừa là lời chúc “Luôn xinh đẹp và rạng rỡ như những mùa quả chín”.</p><p>Trong bộ sưu tập <strong>Quà tặng 20/10 của Klever Fruit</strong>, những mẫu giỏ dành riêng cho người yêu được thiết kế với <strong>hoa cẩm chướng hồng</strong> – gam màu ngọt ngào, dịu dàng tượng trưng cho sự trìu mến, cùng các loại <strong>trái cây nhập khẩu</strong> tượng trưng cho tình yêu bền lâu.</p><p><img src=\"https://cdn.hstatic.net/files/200000528965/file/hop-qua-20-10-trai-cay-nhap-khau-mix-hoa-tuoi-klever-fruit__4__grande.jpg\" alt=\"Quà 20/10 Cho Vợ, Người Yêu – Gửi Lời Yêu Qua Giỏ Quà Trái Cây Klever Fruit\" width=\"600\" height=\"600\"></p><p><a href=\"https://kleverfruits.com.vn/collections/phu-nu-viet-nam-20-10\"><i>Quà tặng 20/10 của Klever Fruit</i></a></p><p>&nbsp;</p><h2><strong>Gợi ý những mẫu giỏ quà 20/10 dành cho người yêu</strong></h2><ul><li><strong>Lẵng Quà 20/10 Trái Cây Mix Hoa Lụa Tone Hồng Thanh Nhã</strong> – hoa cẩm chướng và trái cây nhập khẩu tươi ngon, được yêu thích nhất bởi sự tươi trẻ và tinh tế.</li></ul><p><img src=\"https://cdn.hstatic.net/files/200000528965/file/lang-qua-20-10-tone-hong-klever-fruit-599k_grande.jpg\" alt=\"Quà 20/10 Cho Vợ, Người Yêu – Gửi Lời Yêu Qua Giỏ Quà Trái Cây Klever Fruit\" width=\"600\" height=\"600\"></p><p><a href=\"https://kleverfruits.com.vn/products/hop-qua-20-10-tran-quy\"><i>Lẵng Quà 20/10 Trái Cây Mix Hoa Lụa Tone Hồng Thanh Nhã</i></a></p><p>&nbsp;</p><ul><li><strong>Hộp Quà 20/10 Mộc Hoa Tone Nâu Trang Nhã</strong> – kết hợp trái cây nhập khẩu, hoa cẩm chướng hồng và khay gỗ mộc mạc nhưng được trạm khắc tinh xảo. Như những người vợ mộc mạc nhưng tài giỏi, luôn là hậu phương vững chắc cho chồng.</li></ul>', 0, 1, '2023-12-03 17:41:06', '2025-11-06 14:47:26');

-- --------------------------------------------------------

--
-- Table structure for table `post_categories`
--

CREATE TABLE `post_categories` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_categories`
--

INSERT INTO `post_categories` (`id`, `name`) VALUES
(1, 'Chưa có chuyên mục'),
(2, 'Tác giả nổi tiếng'),
(9, 'Giới thiệu sách');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Kg',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL,
  `sell_quantity` int NOT NULL DEFAULT '0',
  `price` int NOT NULL,
  `sale_price` int NOT NULL,
  `cost_price` decimal(12,3) NOT NULL DEFAULT '0.000',
  `profit_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `views` int NOT NULL DEFAULT '0',
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `short_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `image`, `quantity`, `sell_quantity`, `price`, `sale_price`, `create_date`, `views`, `details`, `short_description`, `status`) VALUES
(1, 16, 'Hộp quà Vali trái cây V012', 'sp-trai-cay-13.png', 100, 0, 280000, 255000, '2023-11-18 08:22:03', 1, '<p>Vali Mica</p><p>Táo Envy New Zealand size 100</p><p>Xoài cát chu da vàng</p><p>Nho xanh Mỹ</p><p>Quýt Úc</p><p>Đào Tuyết Lệ Giang</p><p>Kiwi vàng New Zealand</p><p>Dưa lưới mật Amore</p><p><i>*Giá tiền trái cây có thể sẽ chênh lệch theo thời điểm&nbsp;</i></p><p><i>*Giá tiền chưa bao gồm phí VAT và phí vận chuyển&nbsp;</i></p><p><strong>Hướng dẫn bảo quản:</strong>&nbsp;Nên sử dụng ngay để đảm bảo độ tươi ngon của trái cây. Bảo quản hộp ở nơi thoáng mát.</p>', '<p><i>*Giá tiền trái cây có thể sẽ chênh lệch theo thời điểm&nbsp;</i></p><p><i>*Giá tiền chưa bao gồm phí VAT và phí vận chuyển&nbsp;</i></p>', 1),
(2, 16, 'Hộp quà Vali trái cây V013', 'sp-trai-cay-12.png', 100, 0, 140000, 97000, '2023-11-18 10:15:54', 0, '<p>Mận An Phước</p><p>Nho xanh Mỹ</p><p>Táo Envy New Zealand</p><p>Quýt Úc</p><p>Kiwi vàng New Zealand</p><p>Túi nhựa trong</p><p><i>*Giá tiền trái cây có thể sẽ chênh lệch theo thời điểm&nbsp;</i></p><p><i>*Giá tiền chưa bao gồm phí VAT và phí vận chuyển&nbsp;</i></p><p><strong>Hướng dẫn bảo quản:</strong>&nbsp;Nên sử dụng ngay để đảm bảo độ tươi ngon của trái cây. Bảo quản hộp ở nơi thoáng mát.</p>', '<p><i>*Giá tiền trái cây có thể sẽ chênh lệch theo thời điểm&nbsp;</i></p><p><i>*Giá tiền chưa bao gồm phí VAT và phí vận chuyển&nbsp;</i></p>', 1),
(6, 16, 'Giỏ trái cây phối hoa tươi G040', 'sp-trai-cay-14.png', 50, 0, 145000, 100000, '2023-11-20 22:23:49', 0, '<p>Cốt trái cây C24</p><p>Kiwi vàng New Zealand</p><p>Đào Tuyết Lệ Giang</p><p>Quýt Úc</p><p>Lựu Tứ Xuyên</p><p>Táo Envy New Zealand</p><p>Lê Hàn Quốc</p><p>Nho mẫu đơn&nbsp;</p><p><i>*Giá tiền trái cây có thể sẽ chênh lệch theo thời điểm&nbsp;</i></p><p><i>*Giá tiền chưa bao gồm phí VAT và phí vận chuyển&nbsp;</i></p><p><strong>Hướng dẫn bảo quản:</strong>&nbsp;Nên sử dụng ngay để đảm bảo độ tươi ngon của trái cây. Bảo quản hộp ở nơi thoáng mát.</p>', '<p>Cốt trái cây C24</p><p>Kiwi vàng New Zealand</p><p>Đào Tuyết Lệ Giang</p>', 1),
(14, 2, 'Quýt Úc', 'sp-trai-cay-21.png', 5, 0, 120000, 102000, '2023-11-20 22:54:49', 0, '<p>Quýt Úc được ca ngợi là loại quýt ngon và ngọt nhất thế giới. Những trái quýt nhập khẩu được tuyển chọn kỹ càng, chín mọng, vị ngọt mát ấn tượng. Khám phá ngay!&nbsp;</p><h3><strong>1. Đặc điểm của quýt Úc&nbsp;</strong></h3><h4><strong>1.1 Nguồn gốc&nbsp;</strong></h4><p>Úc là đất nước phát triển nền nông nghiệp mạnh mẽ với những quy trình sản xuất tốt nhất trên thế giới. Quýt Úc được phát triển ở những vùng có khí hậu cận nhiệt đới, ấm nóng. Đặc biệt là khu vực Nam Úc, nhiều nhất là ở bang Queensland.&nbsp;</p><p><img src=\"https://file.hstatic.net/200000377165/file/quyt-uc-2_grande.jpg\" alt=\"Quýt Úc nhập khẩu chính ngạch, vị ngọt đậm đà, giá tốt\" width=\"600\" height=\"600\"></p><p>Những vườn quýt ở đây rộng đến hàng nghìn héc ta. Quá trình canh tác đến thu hoạch được người dân nơi đây chăm chút, ứng dụng kỹ thuật và công nghệ hiện đại. Đặc biệt, hầu hết các vườn quýt đều được bao lưới xung quanh. Điều này vừa ngăn cản được côn trùng, vừa tránh được tình trạng thụ phấn chéo. Từ đó, quýt Úc đạt được sự đồng đều về màu sắc và hương vị.&nbsp;</p><p>Mùa vụ quýt bắt đầu từ tháng 4 và chính vụ vào tháng 10.&nbsp;</p>', '<p><strong>Xuất Xứ:</strong> Thị trấn&nbsp;Gayndah, Bang Queensland, Úc</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Nhập Khẩu Chính Ngạch</p>', 1),
(15, 2, 'Dừa xiêm gọt trọc', 'sp-trai-cay-20.png', 50, 0, 25000, 19000, '2023-11-20 23:05:47', 1, '<p>Nếu là một mọt sách thì chắc hản chúng ta ít nhất đã từng nghe qua tên cuốn sách <i>Hành trình về phương Đông, </i>mình cũng vậy, cũng từng tò mò, từng có ham muốn mãnh liệt đọc cuốn sách được mệnh danh all time best selling book này, và quả thực trải nghiệm đọc cuốn sách này thật khó có thể truyền đạt bằng lời nói hay ngôn ngữ ký hiệu như chúng ta vẫn đang thấy ở đây. Nhưng quả thực mình vẫn muốn chia sẻ và để lại một chút gì đó cảm nhận của bản thân về những điều mình học được, mình suy ngẫm hay ít ra là cho rằng là những nội dung mà sách hướng tới.&nbsp;</p><p><strong>Về tác giả Baird Thomas Spalding</strong></p><p>Là một nhà văn tâm linh người Mĩ sống vào cuốn thế kỷ XVIII đầu thế kỷ IX, những thông tin vè quê quán của ông hiện nay vẫn còn nhiều tranh cãi. Ông là nhà văn tâm linh với tác phẩm nổi tiếng Life and Teachng of the Masters of the Far East (tạm dịch: Cuộc đời các chân sư phương Đông).</p>', '<p><strong>Dừa xiêm gọt trọc là một loại trái cây đặc sản của miền tây. Đây là thức uống giải khát tuyệt vời, đồng thời cung cấp nhiều giá trị dinh dưỡng cho sức khoẻ. Cùng tìm hiểu về địa chỉ mua&nbsp;trái cây Việt Nam chuẩn ngon, chất lượng, giá tốt. Khám phá ngay!&nbsp;</strong></p><h3><strong>1. Dừa xiêm gọt trọc là gì?&nbsp;</strong></h3><p><img src=\"https://file.hstatic.net/200000377165/file/dua-xiem-1_grande.jpg\" alt=\"Dừa xiêm gọt trọc Bến Tre mới về, ngọt thơm, giá tốt TP.HCM\" width=\"600\" height=\"600\"></p><p>Dừa xiêm gọt trọc là một sản phẩm của trái dừa xiêm tươi. Sau khi thu hoạch từ trên cây, dừa sẽ được gọt hoàn toàn lớp xơ dừa bên ngoài và làm sạch. Nhờ đó, người tiêu dùng có thể dễ dàng cắm ống hút vào mắt dừa và thưởng thức vị ngọt thơm, dịu thanh của dừa xiêm. &nbsp;</p><p>Dừa xiêm tại Bến Tre có sản lượng lớn nhất cả nước, ước tính trên 600 triệu quả mỗi năm. Bến Tre thuộc vùng đồng bằng sông Cửu Long, được bồi đắp phù sa quanh năm nhờ sự bao phủ của 4 con sông lớn. Do đó, dừa xiêm nơi đây mang sức hút lớn cho du khách với hương vị ngọt, đậm đà.&nbsp;</p>', 1),
(16, 2, 'Dưa lưới Hoàng Kim', 'sp-trai-cay-19.png', 200, 0, 110000, 90000, '2023-11-20 23:09:13', 0, '<p><strong>Xuất Xứ:</strong> Xã Thuận Quý, Huyện Hàm Thuận Nam, Tỉnh Bình Thuận</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong>&nbsp;GlobalGAP</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Dưa lưới thuộc giống cây thảo hằng năm, có thân mọc bò, phủ lông ngắn, tua cuốn đơn. Nguồn gốc xuất phát từ Châu Phi và Ấn Độ.<br>- Dưa Lưới Hoàng Kim là một trong những giống mới, được gieo trồng ở nhiều tỉnh thành khác nhau. Với vẻ ngoài vàng kim tươi mát, dưa lưới hoàng kim không chỉ thích hợp để mua làm quà mà còn được trưng trong các dịp lễ, tết, đình đám.<br>- Như các giống dưa khác, dưa lưới hoàng kim cũng mọng nước, ngọt thịt &nbsp;và tươi mát. Tuy nhiên, so sánh với dưa lưới queen thì độ giòn của dưa lưới hoàng kim sẽ nhỉnh hơn một chút và độ ngọt cũng sẽ thanh hơn.</p><p><strong>Bảo Quản Và Sử Dụng</strong><br>- Dưa chưa héo cuống nên được bảo quản ở nơi khô ráo, thoáng mát.<br>- Đối với dưa đã héo cuống (cuống khô đen) nên thưởng thức ngay hoặc bảo quản trong ngăn mát tủ lạnh.<br>- Lưu ý không nên để dưa quá chín vì khi đó thịt quả thường bị nhũn, chuyển đắng hoặc lên men.<br>- Lựa chọn dưa lưới hoàng kim ăn liền nên lựa trái héo cuống vì trái cuống héo sẽ có ngọt hơn trái cuống xanh.</p><p><strong>Lợi Ích Của Dưa Lưới Hoàng Kim</strong><br>- Tốt cho phụ nữ mang thai<br>- Giúp mắt sáng khoẻ<br>- Cải thiện hệ tiêu hoá<br>- Giảm căng thẳng, lo âu<br>- Bảo vệ tim mạch<br>- Tốt cho bệnh nhân tiểu đường</p>', '<p><strong>Xuất Xứ:</strong> Xã Thuận Quý, Huyện Hàm Thuận Nam, Tỉnh Bình Thuận</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong>&nbsp;GlobalGAP</p>', 1),
(17, 4, 'Cherry vàng Mỹ', 'sp-trai-cay-18.png', 100, 0, 700000, 670000, '2023-11-20 23:12:48', 0, '<p><strong>Xuất Xứ: </strong>Bang California, Mỹ</p><p><strong>Tiêu Chuẩn Chất Lượng: </strong>Nhập Khẩu Chính Ngạch</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Được xem là viên ngọc quý của phương trời tây, từ khi vừa xuất hiện tại Việt Nam đến nay, cơn sốt Cherry Vàng chưa bao giờ giảm nhiệt Với sản lượng thấp và đặc tính kén chăm sóc nên giá thành cherry vàng thường cao hơn hai dòng cherry đỏ và cherry đen. Tuy nhiên, vẫn được nhiều người chọn mua để thưởng thức và làm quà<br>- So với cherry đỏ và đen, cherry vàng mỏng manh hơn với lớp vỏ mỏng, màu vàng hồng tinh tế, bắt mắt. Cũng bởi \"sự yếu ớt\" đặc trưng, nên khi quản bảo cherry vàng cũng cần rất nhẹ tay trong vận chuyển và bảo quản<br>- Độ ngọt của cherry vàng không đậm ngọt mà dịu dàng tinh tế. Khi đưa cherry lên miệng thưởng thức, sẽ cảm nhận rõ ràng độ mọng tươi trong chất thịt, hài hoà cùng vị dịu ngọt thanh, tạo nên âm vị cuốn hút khó quên.</p><p><strong>Bảo Quản Và Sử Dụng</strong><br>- Cherry mua về ăn ngay hoặc bảo quản trong ngăn mát tủ lạnh từ 1-3 ngày.<br>- Không nên rửa trước khi bảo quản, ăn tới đâu rửa tới đó để đảm bảo chất lượng tốt nhất của trái.<br>- Loại bỏ những trái bị hư hỏng, mốc, dập,... để tránh lan sang những trái bình thường khác.</p><p><strong>Lợi Ích Của Cherry Vàng</strong><br>- Giàu chất chống oxy hoá<br>- Tăng cường hệ miễn dịch<br>- Kiểm soát lượng đường trong máu<br>- Hỗ trợ ngủ ngon<br>- Có lợi cho sức khoẻ tim mạch<br>- Giảm nguy cơ bệnh gout</p>', '<p><strong>Xuất Xứ: </strong>Bang California, Mỹ</p><p><strong>Tiêu Chuẩn Chất Lượng: </strong>Nhập Khẩu Chính Ngạch</p>', 1),
(18, 4, 'Lựu Tứ Xuyên', 'sp-trai-cay-17.png', 65, 1, 150000, 120000, '2023-11-20 23:20:33', 1, '<p><strong>Xuất Xứ:</strong> Tỉnh Tứ Xuyên - Tỉnh Hà Nam, Trung Quốc</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong>&nbsp;Natural Farming</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Lựu có nguồn gốc bản địa Tây Nam Á và được đem trồng tại vùng Kavkaz từ thời cổ đại.<br>- Lựu không hạt được mệnh danh là loại lựu ngon nhất thế giới vì hạt tương đối nhỏ và cực mềm. Khi ăn do hạt quá mềm như tan ra cùng với tép, tạo cảm giác như không có hạt.<br>- Hạt của Lựu Tunisia chắc, mọng nước, ngọt đậm điểm chút chua nhẹ rất kích thích vị giác. Thích hợp cho cả ăn tươi và ép nước.</p><p><strong>Bảo Quản Và Sử Dụng</strong><br>- Lựu mua về nên ăn ngay hoặc bảo quản trong ngăn mát tủ lạnh trong khoảng từ 5-7 ngày.<br>- Ăn đến đâu, rửa và gọt đến đó để đảm bảo chất lượng tốt nhất của trái.<br>- Lọc những trái hư hỏng, tránh lan nấm, mốc sang những trái bình thường khác.</p><p><strong>Lợi Ích Của Lựu Tunisia</strong><br>- Tăng cường thị lực<br>- Hỗ trợ giảm máu nhiễm mỡ<br>- Cải thiện hệ tiêu hoá<br>- Phòng chống xơ vữa động mạch<br>- Bảo vệ tim mạch<br>- Đặc tính chống viêm</p>', '<p><strong>Xuất Xứ:</strong> Tỉnh Tứ Xuyên - Tỉnh Hà Nam, Trung Quốc</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong>&nbsp;Natural Farming</p>', 1),
(20, 19, 'Nguyệt Cát 003', 'sp-trai-cay-16.png', 55, 0, 200000, 160000, '2023-11-20 23:31:04', 0, '<p>* Hộp gỗ vân sồi cao cấp với kích thước: 40x40x18.5 (cm)</p><p>* Sang trọng với lớp vải bao ngoài êm ái, tinh tế</p><p>* Bỏ ngỏ thiệp xinh cùng hoa nhập điểm xuyết</p><p>* Chắc chắn với thùng carton bảo vệ 5 lớp</p><p>Dịch vụ đi kèm của Hộp quà Nguyệt Cát</p><p>Thời gian hoàn thành hộp:<br>- Khách đặt hộp quà Nguyệt Cát sẽ được ưu tiên về thứ tự chuẩn bị hộp (chuẩn bị trước so với các hộp quà khác)<br>- Tuy nhiên, hộp quà Nguyệt Cát cần chuẩn bị tỉ mỉ nên thời gian chuẩn bị hộp sẽ từ 90-120 phút</p><p>Giao hàng tận tay:<br>- Do hộp quà có trọng lượng lớn và giá trị cao, tài xế sẽ gửi hộp đến tận nhà/tận phòng cho khách<br>- Trường hợp khách hàng ở các tòa chung cư cao cấp hạn chế người lạ ra vào, cửa hàng sẽ liên hệ với khách, nhờ khách dẫn tài xế lên</p><p>Quy cách bảo quản hộp quà:<br>Hộp quà được bọc trong thùng carton dày 5 lớp khi vận chuyển để:<br>- Giảm sốc<br>- Hạn chế trái cây bị xô lệch trong hộp<br>- Bảo vệ hộp khỏi các yếu tố về điều kiện thời tiết ảnh hưởng đến trái cây (nắng/mưa)</p>', '<p>Bộ sản phẩm Nguyệt Cát bao gồm:</p><p>* Danh sách trái cây thượng hạng</p><p>- Đào tiên Ngộ Không</p><p>- Kiwi vàng New Zealand</p><p>- Xoài cát chu da vàng</p><p>- Cherry đỏ&nbsp;Mỹ size 9</p><p>- Quýt Úc</p>', 1),
(21, 17, 'Mâm Ngũ Quả', 'sp-trai-cay-15.png', 20, 0, 110000, 88000, '2023-11-23 09:54:06', 0, '', '', 1),
(23, 16, 'Túi quà nhỏ TM021', 'sp-trai-cay-12.png', 100, 0, 250000, 205000, '2023-11-23 12:19:16', 0, '<p>Nho đen Mỹ</p><p>Nho xanh Mỹ</p><p>Mận An Phước</p><p>Quýt Úc</p><p>Xoài cát chu da vàng</p><p>Túi nhựa trong</p><p><i>*Giá tiền trái cây có thể sẽ chênh lệch theo thời điểm&nbsp;</i></p><p><i>*Giá tiền chưa bao gồm phí VAT và phí vận chuyển&nbsp;</i></p><p><strong>Hướng dẫn bảo quản:</strong>&nbsp;Nên sử dụng ngay để đảm bảo độ tươi ngon của trái cây. Bảo quản hộp ở nơi thoáng mát.</p>', '<p><i>Giá tiền trái cây có thể sẽ chênh lệch theo thời điểm&nbsp;</i></p><p><i>*Giá tiền chưa bao gồm phí VAT và phí vận chuyển&nbsp;</i></p>', 1),
(24, 4, 'Mận Úc Khủng Long', 'sp-trai-cay-11.png', 121, 2, 400000, 388000, '2023-11-23 12:20:16', 3, '<p><strong>Xuất Xứ:</strong> Vùng ngoại ô Narre Warren North, Bang Victoria, Úc</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Nhập khẩu chính ngạch</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Mùa mận Việt Nam kết thúc thì cũng là lúc Mận Úc lên ngôi. Không chỉ riêng thời điểm cuối năm, Mận Úc được khá nhiều người săn đón nhờ vẻ ngoài bắt mắt cùng hương vị lôi cuốn.<br>- So với mận Việt, mận Úc có kích thước lớn hơn, hình dáng tròn và đầy trái, khi cầm rất chắc tay.<br>- Trái có lớp vỏ đỏ thẫm và bóng nhẹ. Vì thịt mận bên trong rất đẫy nước, nên khi ăn nguyên trái sẽ cảm nhận được chất nước ứa ra toàn khoang miệng, rất sảng khoái.<br>- Hương vị ngọt ngọt chua chua, nhưng độ chua vừa đủ, ăn không thôi cũng cảm thấy thích. Tuy nhiên có thể dùng thêm muối tôm hoặc muối xí muội để tăng phần kích thích vị giác.</p><p><strong>Bảo Quản Và Sử Dụng</strong><br>- Mận mua về nên dùng ngay hoặc bảo quản trong tủ mát từ 3-5 ngày<br>- Không nên rửa trước, ăn đến đâu rửa đến đó để cảm nhận được chất lượng tốt nhất của trái.<br>- Lựa và bỏ những trái có dấu hiệu hư, mềm nhũn hoặc nấm mốc để tránh ảnh hưởng đến chất lượng của những trái còn lại</p><p><strong>Lợi Ích Của Mận Úc</strong><br>- Giàu polyphenol, chất chống oxy hoá tốt cho cơ thể<br>- Giúp hạ đường huyết hiệu quả<br>- Bảo vệ sức khoẻ xương khớp<br>- Ổn định hệ tiêu hoá<br>- Tốt cho thị lực<br>- Phòng chống ung thư</p>', '<p><strong>Xuất Xứ:</strong> Vùng ngoại ô Narre Warren North, Bang Victoria, Úc</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Nhập khẩu chính ngạch</p>', 1),
(25, 4, 'Nho mẫu đơn nội địa Trung', 'sp-trai-cay-10.png', 100, 0, 279000, 249000, '2023-11-23 16:21:03', 4, '<p><strong>Xuất Xứ:</strong>&nbsp;Quảng Tây, Trung Quốc</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong>&nbsp;Nhập khẩu chính ngạch</p><p><strong>Nho mẫu đơn nội địa Trung đang được người Việt ưa chuộng bởi mức giá thành khá mềm so với nho nhập khẩu Hàn, Nhật. Vậy, sản phẩm nho mẫu đơn Trung có tốt không? Mua nho nội địa Trung ở đâu uy tín? Khám phá ngay!&nbsp;</strong></p><h3><strong>1. Tìm hiểu về nho mẫu đơn nội địa Trung&nbsp;</strong></h3><h4><strong>1.1&nbsp;Xuất xứ&nbsp;</strong></h4><p>Nho mẫu đơn hay còn gọi là nho sữa (Shine Muscat) có nguồn gốc từ Địa Trung Hải. Nhật Bản là nơi đầu tiên nhân giống thành công giống nho lưỡng bội cao cấp này và bắt đầu đưa vào thị trường. Nho mẫu đơn được yêu thích bởi mẫu mã đẹp, trứ danh “Vua của các loại nhỏ” với vị ngọt thơm sữa đặc biệt hiếm có ở giống nho nào. Vì vậy mà có giá thành khá cao.&nbsp;</p><p><img src=\"https://file.hstatic.net/200000377165/file/nho-mau-don-ndt-3_grande.jpg\" alt=\"Nho mẫu đơn nội địa trung nhập khẩu chính ngạch, an toàn sức khỏe\" width=\"600\" height=\"600\"></p><p>Đến năm 2021, Trung Quốc trồng thành công giống này và xuất đi các nước với mức giá thành siêu rẻ, thậm chí rẻ hơn cả các giống nho cao cấp trong nước.&nbsp;</p><p>Song, người tiêu dùng cần tránh nhầm lẫn với nho mẫu đơn được bày bán tràn lan ngoài chợ, không bao bì, tem nhãn với giá chỉ 20.000-85.000 đồng/kg. Hầu hết đây là nho nhập tiểu ngạch, quy trình trồng đại trà, không rõ ràng. Nho được vận chuyển bằng ô tô thông thường, không bảo quản lạnh nhưng vẫn giữ được độ tươi nhờ chất bảo quản.&nbsp;</p><p>Nhà Morning hiện tại vẫn kinh doanh mặt hàng nho mẫu đơn nội địa Trung nhập khẩu chính ngạch. Sản phẩm có cam kết đầy đủ giấy tờ rõ ràng về xuất xứ, chứng nhận kiểm dịch thực vật và an toàn thực phẩm.&nbsp;&nbsp;</p><h4><strong>1.2&nbsp;Mùa vụ &amp; giá thành&nbsp;</strong></h4><p>Nho mẫu đơn nội địa Trung vào mùa từ giữa tháng 8 đến tháng 10 hằng năm. Trên thị trường, sản phẩm đang có mức giá dao động từ 200.000đ đến 300.000đ/kg. Các nguyên nhân chính dẫn đến giá thành rẻ của nho mẫu đơn nội địa Trung:&nbsp;</p><ul><li>Chi phí nhân công, đất đai thấp dẫn đến chi phí sản xuất thấp hơn.</li><li>Quy mô sản xuất lớn: diện tích đất trồng trọt rộng lớn và sản xuất nho mẫu đơn với quy mô công nghiệp đã giúp các nhà vườn tối ưu hóa chi phí&nbsp;</li><li>Cước vận chuyển không cao như nho Hàn hay Nhật, đặc biệt là khi vận chuyển đến các nước lân cận Trung Quốc như Việt Nam.&nbsp;</li><li>Ngoài ra giá cả dao động còn phụ thuộc vào mùa vụ. Nho mẫu đơn vào mùa, lượng cung lớn thì giá sẽ rẻ hơn so với lúc hiếm hàng.&nbsp;</li></ul><p>Tại Morning Fruit đang phân phối nho mẫu đơn nội địa Trung nhập khẩu chính ngạch với mức giá siêu tốt.&nbsp;</p>', '<p><strong>Xuất Xứ:</strong>&nbsp;Quảng Tây, Trung Quốc</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong>&nbsp;Nhập khẩu chính ngạch</p>', 1),
(26, 2, 'Dứa mật MD2', 'sp-trai-cay-9.png', 100, 1, 80000, 60000, '2023-11-23 16:23:55', 19, '<p><strong>Xuất Xứ:</strong> Xã Quỳnh Thắng, Huyện Quỳnh Lưu, Tỉnh Nghệ An</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Natural Farming</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Hiện nay có ba giống dứa đang phổ biến tại thị trường Việt Nam là dứa Queen, Cayenne và MD2. Tuy nhiên dứa Queen vẫn được nhiều người biết đến hơn cả bởi hương vị phù hợp với thị hiếu tiêu dùng của đa số người Việt.<br>- Dứa Queen có kích thước vừa, khối lượng và độ lớn trung mình trong khoảng từ 500-900g. Bản lá cứng và hẹp, có gai nhiều bao quanh mép lá, xuất hiện vân trắng chạy song song theo chiều lá nằm ở mặt trong của phiến lá.<br>- Dứa Queen đậm vị, ngọt tươi mọng nước. Tuy nhiên tuỳ thuộc vào địa điểm canh tác mà chất lượng của dứa Queen sẽ khác nhau. Thường thì mùa nắng độ ngọt của dứa Queen sẽ cao hơn so với mùa mưa.</p><p><strong>Bảo quản và sử dụng:</strong><br>- Dứa queen khi mua gọt sẵn nên thưởng thức ngay để đảm bảo hương vị tốt nhất của trái.<br>- Bảo quản trong ngăn mát tủ lạnh từ 12-36 giờ.</p><p><strong>Lợi Ích Của Dứa Queen</strong><br>- Tăng cường sức khoẻ hệ tiêu hoá<br>- Chứa nhiều chất chống oxy hoá<br>- Thúc đẩy khả năng hệ miễn dịch<br>- Làm dịu cơn ho<br>- Hỗ trợ sức khoẻ xương khớp<br>- Bảo vệ sức khoẻ não bộ</p>', '<p><strong>Xuất Xứ:</strong> Xã Quỳnh Thắng, Huyện Quỳnh Lưu, Tỉnh Nghệ An</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Natural Farming</p>', 1),
(27, 2, 'Thanh long ruột tím hồng', 'sp-trai-cay-8.png', 27, 3, 125000, 95000, '2023-11-23 20:31:17', 3, '<p><strong>Xuất Xứ:</strong> Xã Thuận Quý, Huyện Hàm Thuận Nam, Tỉnh Bình Thuận</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> GlobalGAP</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Thanh Long ruột tím hồng được lai tạo từ thanh long ruột trắng và thanh long ruột đỏ, chính vì thế thịt trái có đặc điểm &nbsp;chắc như thanh long ruột trắng nhưng màu sắc và độ ngọt lại thiên nhiều thanh long ruột đỏ.<br>- Nhờ sự lai tạo có chọn lọc, thanh long ruột tím hồng nhiễm rất ít côn trùng và sâu bệnh hại, chính vì thế là giống cây thích hợp để chọn gieo trồng và canh tác theo tiêu chuẩn globalGAP, xuất khẩu ra nhiều nước trên thế giới.<br>- Trái có hương vị ngọt thanh mát, sẽ thiên chua khi còn xanh và ngọt đậm hơn khi đã chín héo vỏ.</p><p><strong>Bảo Quản Và Sử Dụng</strong><br>- Bảo quản thanh long trong ngăn mát tủ lạnh khoảng từ 2-5 ngày.<br>- Thanh long vỏ héo thường có độ ngọt đậm hơn thanh long khi mới thu hoạch (vỏ tươi).&nbsp;</p><p><strong>Lợi Ích Của Thanh Long Ruột Tím Hồng</strong><br>- Giàu chất chống oxy hoá<br>- Hỗ trợ giảm cân<br>- Bảo vệ sức khoẻ đường ruột<br>- Kiểm soát đường huyết<br>- Giảm thiếu máu khi mang thai<br>- Tốt cho sức khoẻ tim mạch</p>', '<p><strong>Xuất Xứ:</strong> Xã Thuận Quý, Huyện Hàm Thuận Nam, Tỉnh Bình Thuận</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> GlobalGAP</p>', 1),
(28, 2, 'Măng cụt Miền Tây', 'sp-trai-cay-7.png', 496, 4, 140000, 105000, '2023-11-29 20:36:43', 15, '<p><strong>Xuất Xứ:</strong> Long Khánh, Lái Thiêu</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Natural Farming</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Măng cụt là dòng trái cây nổi tiếng và được nhiều người yêu thích nhờ chất vị ngọt đậm, kết hợp cùng độ thanh tươi đặc trưng.<br>- Bên cạnh măng cụt miền tây, Thái Lan là quốc gia cung cấp măng cụt nhập khập nhiều nhất cho thị trường trái cây nước ta.<br>- Với thổ nhưỡng và khí hậu khác nhau, măng cụt Thái và măng cụt Miền Tây sẽ có sự khác nhau về hình dáng và hương vị.<br>- Không tròn dài như măng cụt Thái, hình dáng của măng cụt Miền Tây sẽ bầu bĩnh hơn. Bên cạnh đó, chất măng cụt miền tây sẽ láng và tím sậm, măng cụt Thái sẽ có màu nâu nâu và da cám.<br>- Kích thước cuống của măng cụt Thái sẽ dài hơn và héo do mất nhiều thời gian vận chuyển từ Thái Lan. Ngược lại măng cụt miền Tây sẽ có cuống tươi và ngắn hơn.<br>- Xét tổng quan hương vị thì măng cụt miền Tây sẽ có độ kích thích hơn vì ngọt đậm 8 phần và 2 phần xen chua diu. Măng cụt Thái Lan thường ngọt thuần, thanh mát.</p><p><strong>Bảo Quản Và Sử Dụng</strong><br>- Trái &nbsp;măng cụt có vỏ tím hồng là trái còn xanh, ăn có vị dôn dốt chua. Bảo quản nhiệt độ thường 2-3 ngày trái sẽ chín.<br>- Trái đã chuyển tím sẫm (đen) là trái đã chín. Vỏ mềm, dễ khui. Vị ngọt hơn.<br>- Bảo quản măng cụt cần rất nhẹ tay. Nếu xếp ra rổ, khay, cần xếp từng trái, tránh đổ hàng loạt. Va đập mạnh bên ngoài, có thể khiến ruột bên trong bị hư hỏng, xì mủ.</p><p><strong>Lợi Ích Của Măng Cụt</strong><br>- Chống lão hoá làn da<br>- Giảm cholesterol<br>- Cải thiện tâm trạng<br>- Ngăn ngừa ung thư<br>- Ổn định đường huyết<br>- Phòng chống bệnh tim mạch</p>', '<p><strong>Xuất Xứ:</strong> Long Khánh, Lái Thiêu</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Natural Farming</p>', 1),
(29, 2, 'Thêm vào giỏ Sầu riêng Lão Nông Monthong', 'sp-trai-cay-6.png', 200, 0, 200000, 185000, '2023-12-02 20:54:53', 11, '<p><strong>Xuất Xứ:</strong> Thu mua tại các tỉnh: Long An, Tiền Giang, Vĩnh Long, Hậu Giang</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong>&nbsp;Natural Farming</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Dưa hấu là một loại trái đặc biệt thuộc lớp quả mọng, bởi lớp vỏ cứng và không có sự phân chia trong trái.<br>- Dưa hấu không hạt có lớp thịt mọng, nhiều nước, hương vị ngọt mát, tự nhiên.<br>- So sánh với dưa hấu có hạt, độ ngọt của dưa hấu không hạt có vị thanh hơn. Tuy nhiên, nhờ đặc tính không hạt đặc trưng, dễ dàng thưởng thức nên dưa hấu không hạt trở thành thực quả phù hợp cho tất cả mọi người, từ người già cho đến trẻ nhỏ.</p><p><strong>Bảo Quản Và Sử Dụng</strong><br>- Nên thưởng thức dưa hấu khi héo cuống (cuống chuyển khô đen) vì khi đó hương vị của dưa sẽ đậm đà hơn.<br>- Dưa xanh cuống có thể bảo quản ở nơi khô ráo, thoáng mát.<br>- Dưa héo cuống nên được bảo quản trong ngăn mát tủ lạnh. Khi trái chín quá, ruột sẽ chuyển sang nẫu và có vị đắng.</p>', '<p><strong>Xuất Xứ:</strong> Thu mua tại các tỉnh: Long An, Tiền Giang, Vĩnh Long, Hậu Giang</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong>&nbsp;Natural Farming</p>', 1),
(30, 4, 'Nho đen Mỹ', 'sp-trai-cay-3.png', 100, 3, 300000, 279000, '2023-12-02 20:56:21', 15, '<p><strong>Xuất Xứ:</strong> California, Mỹ</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Nhập khẩu chính ngạch</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Nho đen không hạt Mỹ được gieo trồng nhiều ở các bang California, Oregon, Washingtong, nơi có khí hậu khô và ấm áp.<br>- Nho đen có nhiều chủng loại, trong đó có Pandol Dark Star. Khi thưởng thức trái có vị ngọt đậm, xen chát nhẹ đăng trưng.<br>- Hình dáng trái thường không tròn mà hơi bầu và thuôn dài. Vỏ có màu đen thẫm, xung quanh phủ một lớp phấn tự nhiên bảo vệ nho khỏi tự tấn công, gây hại của côn trùng.<br>- Đặc trưng của nho Mỹ sẽ không mềm mọng mà thịt thường chắc và giòn. Khi cắn sẽ có cảm giác tươi mới và sảng khoái.<br>- Vì nho đen Mỹ không có hạt nên trẻ em và người già, ai ai cũng đều dễ dàng thưởng thức.</p><p><strong>Bảo Quản Và Sử Dụng</strong><br>- Nho đen mua về nên dùng ngay hoặc bảo quản trong ngăn mát tủ lạnh từ 1-3 ngày<br>- Không rửa trước, ăn đến đâu rửa nho đến đó, để đảm bảo hương vị tốt nhất của trái.<br>- Loại bỏ những trái có dấu hiệu mềm, nhũng, dập, hư hoặc mốc để tránh ảnh tưởng đến những trái còn lại.</p><p><strong>Lợi Ích Của Nho Đen Mỹ</strong><br>- Tăng cường thị lực<br>- Hỗ trợ giảm máu nhiễm mỡ<br>- Cải thiện hệ tiêu hoá<br>- Phòng chống xơ vữa động mạch<br>- Bảo vệ tim mạch<br>- Đặc tính chống viêm</p>', '<p><strong>Xuất Xứ:</strong> California, Mỹ</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Nhập khẩu chính ngạch</p>', 1),
(31, 2, 'Dưa hấu không hạt', 'sp-trai-cay-2.png', 44, 6, 180000, 126000, '2023-12-02 20:59:06', 26, '<p><strong>Xuất Xứ:</strong> Thu mua tại các tỉnh: Long An, Tiền Giang, Vĩnh Long, Hậu Giang</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong>&nbsp;Natural Farming</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Dưa hấu là một loại trái đặc biệt thuộc lớp quả mọng, bởi lớp vỏ cứng và không có sự phân chia trong trái.<br>- Dưa hấu không hạt có lớp thịt mọng, nhiều nước, hương vị ngọt mát, tự nhiên.<br>- So sánh với dưa hấu có hạt, độ ngọt của dưa hấu không hạt có vị thanh hơn. Tuy nhiên, nhờ đặc tính không hạt đặc trưng, dễ dàng thưởng thức nên dưa hấu không hạt trở thành thực quả phù hợp cho tất cả mọi người, từ người già cho đến trẻ nhỏ.</p><p><strong>Bảo Quản Và Sử Dụng</strong><br>- Nên thưởng thức dưa hấu khi héo cuống (cuống chuyển khô đen) vì khi đó hương vị của dưa sẽ đậm đà hơn.<br>- Dưa xanh cuống có thể bảo quản ở nơi khô ráo, thoáng mát.<br>- Dưa héo cuống nên được bảo quản trong ngăn mát tủ lạnh. Khi trái chín quá, ruột sẽ chuyển sang nẫu và có vị đắng.</p><p><strong>Lợi Ích Của Dưa Hấu Không Hạt</strong><br>- Trong dưa hấu hàm lượng nước (chiếm 91%) và chất xơ cao nên việc thưởng thức dưa hấu không chỉ giúp ta no lâu, hỗ trợ giảm cân mà còn rất tốt cho hoạt động của hệ tiêu hoá. Bên cạnh đó, với lượng đường tự nhiên và các khoáng chất tốt cho sức khoẻ sẽ giúp các mẹ bầu giảm chứng ợ nóng, ốm nghén và mất nước trong suốt thời kì mang thai.<br>- Hai loại vitamin A và C có trong dưa hấu, chiếm vai trò vô cùng quan trọng đối với sức khoẻ của da và tóc. Vitamin A giúp tái tạo và chữa lành các tế bào da tổn thương. Vitamin C thúc đẩy cơ thể tái tạo collagen, giữ cho da luôn mịn màng, săn chắc, đồng thời nuôi dưỡng một mái tóc chắc khoẻ, bóng mượt từ bên trong.<br>- Lycopene và vitamin C có trong dưa sẽ&nbsp;loại bỏ các gốc tự do gây tổn hại tế bào ra khỏi cơ thể, hỗ trợ ngăn ngừa bệnh viêm khớp, hen suyễn, ung thư và nhiều loại bệnh liên quan khác.</p>', '<p><strong>Xuất Xứ:</strong> Thu mua tại các tỉnh: Long An, Tiền Giang, Vĩnh Long, Hậu Giang</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong>&nbsp;Natural Farming</p>', 1),
(32, 16, 'Cam vàng Úc', 'sp-trai-cay-1.png', 49, 3, 120000, 100000, '2023-12-02 21:00:48', 33, '<p><strong>Xuất Xứ:</strong> Thị trấn Moorook, Úc</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Nhập khẩu chính ngạch</p><p><strong>Đặc Điểm Sản Phẩm</strong><br>- Cam vàng là giống lai giữa bưởi (Citrus maxima) và quýt (Citrus reticulata).<br>- Sở hữu hình dáng tròn đều cùng sắc cam tươi tắn, bắt mắt. Cam vàng là sự lựa chọn rất thích hợp để mua làm quà hoặc trưng bày trên mâm quả của gia đình trong các dịp lễ.<br>- Hương vị của cam vàng không thể tươi mát hơn với độ ngọt của thịt và độ mọng tươi của tép. Do vị ngọt phụ thuộc tương đối vào mùa vụ, khí hậu nên sẽ có thời điểm cam có hậu xen chua, tuy nhiên vẫn không ảnh hưởng nhiều đến độ ngon của trái.</p><p><strong>Bảo Quản Và Sử Dụng</strong><br>- Cam bảo quản tủ mát được 5-10 ngày.<br>- Để càng lâu, trái cam càng mọng nước và ngọt đậm.<br>- Nếu xuất hiện trái hư (ấn vào mềm, ngửi mùi chua), cần loại bỏ ngay. Tránh lây lan sang trái khác.</p><p><strong>Lợi Ích Của Cam Vàng</strong><br>- Bảo vệ, nuôi dưỡng làn da<br>- Giảm stress, mệt mỏi<br>- Điều hoà huyết áp<br>- Tăng cường sức đề kháng<br>- Tốt cho mẹ bầu<br>- Bảo vệ sức khoẻ tim mạch</p>', '<p><strong>Xuất Xứ:</strong> Thị trấn Moorook, Úc</p><p><strong>Tiêu Chuẩn Chất Lượng:</strong> Nhập khẩu chính ngạch</p>', 1),
(34, 4, 'Nguyễn Lê Anh Khoa', 'ExampleFile.pdf', 5, 0, 123555, 55, '2024-03-31 22:24:56', 2, '', '', 0),
(35, 2, 'test nè', 'meomeo.png', 10, 0, 1000, 500, '2025-09-19 22:15:50', 0, '<p>Hello</p>', '<p>Hello</p>', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Tên đăng nhập',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Họ tên',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 là khách hàng 1 là nhân viên',
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `image`, `email`, `phone`, `address`, `role`, `active`) VALUES
(6, 'khoanguyen', '$2y$10$Cm.2KiZ85WRGUTBk8vhMaOIQt46A53HKuzPfZh2jS.fdZzAr33dTi', 'Nguyễn Tuấn', 'avatar_it.png', 'abc@gmail.com', '0909155555', 'Ninh Kiều, Cần Thơ', 0, 1),
(7, 'khahi', '$2y$10$sF.yA6lAhvCE1vhwffwijuzA3JMoVhgnxkk.FdqXR4HVHlHdnXHzK', 'Lê Châu Khả Hi', 'user-default.png', 'hilckpc524@fpt.edu.vn', '0336216654', 'Kiên Giang', 0, 1),
(8, 'admin', '$2y$10$Cm.2KiZ85WRGUTBk8vhMaOIQt46A53HKuzPfZh2jS.fdZzAr33dTi', 'Admin', 'avatar_it.png', 'khoacn03@gmail.com', '0336216111', 'Cần Thơ nè', 1, 1),
(9, 'tranvana', '$2y$10$ts748iCUjwA5HpQBMLuROuAXa70addsKmfkMh9rYIw/PjhxLLwH8i', 'Trần Văn A', 'user-default.png', 'tranvana@gmail.com', '0909135969', 'Cái Răng, Cần Thơ', 0, 1),
(10, 'haohao', '$2y$10$gcBHpzElBGDkOv5EEzJFhuoireNk2HsaloJQLy2KHvzGqx6MIyYku', 'Mai Hảo Hảo', 'user-default.png', 'haomhpc07316@fpt.edu.vn', '0909135985', 'Ninh Kiều, Cần Thơ', 0, 1),
(11, 'tuankiet', '$2y$10$ENy4z0Infjac7VjlKYp2A.gqCBwc8N01tKGLT9A3buGdVoyd7sXnK', 'Đặng tuấn Kiệt', 'user-default.png', 'kietdtpc06764@fpt.edu.vn', '0909006764', 'Cần Thơ', 0, 1),
(12, 'khoanguyen111', '$2y$10$ZPAY2O7ntfQ5/Huv3dUAIuY4qHuPD/DpRxiw11TurgYr3hCrWfnv2', 'Nguyễn Lê Anh Khoa', 'user-default.png', 'khoanlapc1234@gmail.com', '0336216546', 'Cần Thơ', 0, 1),
(13, 'khoanguyen001', '$2y$10$ptSvfsaT78h4LdZQNCrKdemlC.AqyZ/q5cudTk9/FKcbe6TtJDJAC', 'Nguyễn Lê Anh Khoa', 'user-default.png', 'khoanla113@gmail.com', '0336216555', 'Can tho', 0, 1),
(15, 'toan', '$2y$10$rwdD7UlOPC2XUc4d3nJ/nO0THzotlhmrKekcbBynHxTqpOmFlN79a', 'Toàn', 'user-default.png', 'toan@gmail.com', '0336256555', 'Đại Học Cửu Long', 0, 1),
(16, 'tranvanc', '$2y$10$JQrXKJCw01gkdaULOzOzyeRhJl4o64qmluPyEi76bHiFhbD.XCPfi', 'Tran Van C', 'avatar_it.png', 'tranvanc@gmail.com', '0909123000', 'Quận 1, HCM', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `warehouse`
--

CREATE TABLE `warehouse` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` int NOT NULL,
  `quantity` int NOT NULL,
  `sell` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouse`
--

INSERT INTO `warehouse` (`id`, `name`, `price`, `quantity`, `sell`, `created_at`) VALUES
(17, 'Cam vàng Úc', 100000, 100, 0, '2025-11-06 22:57:58'),
(18, 'Dưa hấu không hạt', 180000, 120, 0, '2025-11-06 22:58:26'),
(19, 'Nho đen Mỹ', 300000, 120, 0, '2025-11-06 22:58:47');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_receipts`
--

CREATE TABLE `warehouse_receipts` (
  `receipt_id` int NOT NULL,
  `receipt_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `import_date` date NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: nhap, 1: hoan thanh',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_receipt_items`
--

CREATE TABLE `warehouse_receipt_items` (
  `item_id` int NOT NULL,
  `receipt_id` int NOT NULL,
  `product_id` int NOT NULL,
  `import_price` int NOT NULL,
  `import_quantity` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`orderdetails_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_orders_status_date` (`status`,`date`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_category` (`category_id`),
  ADD KEY `idx_products_status_quantity` (`status`,`quantity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `warehouse`
--
ALTER TABLE `warehouse`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `orderdetails_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `post_categories`
--
ALTER TABLE `post_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `warehouse`
--
ALTER TABLE `warehouse`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `warehouse_receipts`
--
ALTER TABLE `warehouse_receipts`
  MODIFY `receipt_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouse_receipt_items`
--
ALTER TABLE `warehouse_receipt_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `post_categories` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `warehouse_receipts`
--
ALTER TABLE `warehouse_receipts`
  ADD PRIMARY KEY (`receipt_id`),
  ADD KEY `idx_wr_status_date` (`status`,`import_date`),
  ADD UNIQUE KEY `receipt_code` (`receipt_code`);

--
-- Constraints for table `warehouse_receipt_items`
--
ALTER TABLE `warehouse_receipt_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_receipt_items_product` (`product_id`),
  ADD KEY `idx_receipt_items_receipt` (`receipt_id`),
  ADD CONSTRAINT `fk_receipt_items_receipt` FOREIGN KEY (`receipt_id`) REFERENCES `warehouse_receipts` (`receipt_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_receipt_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
