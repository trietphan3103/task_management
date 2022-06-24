-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Jan 14, 2022 at 04:16 PM
-- Server version: 8.0.27
-- PHP Version: 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `QUANLYPHONGBAN`
--
CREATE DATABASE IF NOT EXISTS `QUANLYPHONGBAN` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `QUANLYPHONGBAN`;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `_get_current_day_off_used` (`p_user_id` INT)  BEGIN
	SELECT IFNULL(sum(DATEDIFF(`ABSENCE`.`ngay_ket_thuc`, `ABSENCE`.`ngay_bat_dau`)) + 1, 0) as day_used
    FROM `ABSENCE`
    WHERE `ABSENCE`.`nguoi_tao_id` = p_user_id and EXTRACT(YEAR FROM `ABSENCE`.`ngay_bat_dau`) = EXTRACT(YEAR FROM NOW()) and `ABSENCE`.`status` = 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ABSENCE`
--

CREATE TABLE `ABSENCE` (
  `absence_id` int NOT NULL,
  `nguoi_tao_id` int DEFAULT NULL,
  `nguoi_duyet_id` int DEFAULT NULL,
  `status` int DEFAULT '0',
  `file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ngay_bat_dau` date NOT NULL,
  `ngay_ket_thuc` date NOT NULL,
  `ly_do` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ngay_duyet` date DEFAULT NULL,
  `created_on` date NOT NULL DEFAULT (cast(now() as date))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ABSENCE`
--

INSERT INTO `ABSENCE` (`absence_id`, `nguoi_tao_id`, `nguoi_duyet_id`, `status`, `file`, `ngay_bat_dau`, `ngay_ket_thuc`, `ly_do`, `ngay_duyet`, `created_on`) VALUES
(1, 2, 1, 1, NULL, '2022-01-14', '2022-01-15', 'Du lịch', '2022-01-14', '2022-01-14'),
(2, 3, 1, 0, NULL, '2022-01-20', '2022-01-20', 'Du lịch', NULL, '2022-01-15'),
(3, 4, 1, 0, '/files/absence/2/giay-kham-benh.jpg', '2022-01-22', '2022-01-22', 'Khám bệnh', NULL, '2022-01-19'),
(4, 5, 2, 1, NULL, '2022-01-14', '2022-01-15', 'Du lịch', '2022-01-14', '2022-01-14'),
(5, 6, 2, 0, NULL, '2022-01-29', '2022-01-29', 'Du lịch', NULL, '2022-01-24'),
(6, 7, 2, 0, NULL, '2022-01-25', '2022-01-26', 'Đám giỗ', NULL, '2022-01-23'),
(7, 8, 3, 1, NULL, '2022-01-14', '2022-01-15', 'Du lịch', '2022-01-14', '2022-01-14'),
(8, 9, 3, 0, '/files/absence/5/giay-kham-benh.jpg', '2022-01-19', '2022-01-20', 'Khám bệnh', NULL, '2022-01-16'),
(9, 10, 3, 0, NULL, '2022-01-21', '2022-01-23', 'Du lịch', NULL, '2022-01-17'),
(10, 11, 4, 0, NULL, '2022-01-30', '2022-02-10', 'Du lịch', NULL, '2022-01-31'),
(11, 12, 4, 0, NULL, '2022-02-16', '2022-02-17', 'Du lịch', NULL, '2022-01-12');

--
-- Triggers `ABSENCE`
--
DELIMITER $$
CREATE TRIGGER `insert_ABSENCE_max_absence` BEFORE INSERT ON `ABSENCE` FOR EACH ROW begin
	if(
		SELECT IFNULL(sum(DATEDIFF(`ABSENCE`.`ngay_ket_thuc`, `ABSENCE`.`ngay_bat_dau`)) + 1, 0) as day_used
		FROM `ABSENCE`
		WHERE `ABSENCE`.`nguoi_tao_id` = new.nguoi_tao_id and EXTRACT(YEAR FROM `ABSENCE`.`ngay_bat_dau`) = EXTRACT(YEAR FROM new.ngay_bat_dau) and `ABSENCE`.`status` = 1
	) + DATEDIFF(new.ngay_ket_thuc, new.ngay_bat_dau) > (SELECT `USERS`.`so_absence_max` FROM `USERS` WHERE `USERS`.`user_id` = new.nguoi_tao_id) then
		SIGNAL SQLSTATE '45016'
            SET MESSAGE_TEXT = 'absence_exceed_max_conditon';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_ABSENCE_ngay_ket_thuc` BEFORE INSERT ON `ABSENCE` FOR EACH ROW begin
	if not (new.ngay_ket_thuc >= new.ngay_bat_dau and new.ngay_bat_dau >= curdate()) then
		SIGNAL SQLSTATE '45014'
            SET MESSAGE_TEXT = 'absence_date_condiotion';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_ABSENCE_status` BEFORE INSERT ON `ABSENCE` FOR EACH ROW begin
	if new.status not between -1 and 1 then
		SIGNAL SQLSTATE '45013'
            SET MESSAGE_TEXT = N'Nhập lại trạng thái của đơn nghỉ phép';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_ABSENCE_tinh_trang` BEFORE INSERT ON `ABSENCE` FOR EACH ROW begin
	if (select count(*) from ABSENCE where new.nguoi_tao_id = ABSENCE.nguoi_tao_id and status = 0) >= 1 
	or (select count(*) from ABSENCE where new.nguoi_tao_id = ABSENCE.nguoi_tao_id and (curdate() - ABSENCE.ngay_duyet) < 7) >= 1 then
		SIGNAL SQLSTATE '45016'
            SET MESSAGE_TEXT = 'absence_create_conditon';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_ABSENCE_ngay_ket_thuc` BEFORE UPDATE ON `ABSENCE` FOR EACH ROW begin
	if not (new.ngay_ket_thuc >= new.ngay_bat_dau and new.ngay_bat_dau >= curdate()) then
		SIGNAL SQLSTATE '45014'
            SET MESSAGE_TEXT = 'absence_date_condiotion';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_ABSENCE_status` BEFORE UPDATE ON `ABSENCE` FOR EACH ROW begin
	if new.status not between -1 and 1 then
		SIGNAL SQLSTATE '40013'
            SET MESSAGE_TEXT = N'Nhập lại trạng thái của đơn nghỉ phép';
	end if;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `HISTORY`
--

CREATE TABLE `HISTORY` (
  `history_id` int NOT NULL,
  `task_id` int NOT NULL,
  `task_status` int DEFAULT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mo_ta_nop` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_on` datetime DEFAULT CURRENT_TIMESTAMP,
  `file_history` varchar(2550) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_task_nop` varchar(2550) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `HISTORY`
--

INSERT INTO `HISTORY` (`history_id`, `task_id`, `task_status`, `note`, `mo_ta_nop`, `created_on`, `file_history`, `file_task_nop`) VALUES
(1, 7, -1, 'Cần thêm title', 'Đã cập nhật', '2022-01-14 23:06:58', '/files/history/1/Huong dan.txt', '/files/task/task_nop/7/Xem-san-pham-updated.txt'),
(2, 12, -1, 'Tiếp tục đánh giá', 'Đánh giá lần 1', '2022-01-14 23:06:58', '/files/history/2/huong-dan.txt', '/files/task/task_nop/12/danh-gia-lan-1.txt');

--
-- Triggers `HISTORY`
--
DELIMITER $$
CREATE TRIGGER `history_insert` BEFORE INSERT ON `HISTORY` FOR EACH ROW BEGIN

SET NEW.created_on = CURRENT_TIMESTAMP + INTERVAL 7 HOUR;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `PHONG_BAN`
--

CREATE TABLE `PHONG_BAN` (
  `phong_ban_id` int NOT NULL,
  `ten_phong` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `mo_ta` varchar(2550) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `PHONG_BAN`
--

INSERT INTO `PHONG_BAN` (`phong_ban_id`, `ten_phong`, `mo_ta`) VALUES
(1, 'Phòng giám đốc', 'Phòng mặc định cho người dùng có quyền `Giám đốc`'),
(2, 'Phòng CNTT', 'Tham mưu và tổ chức, triển khai thực hiện quản lý toàn bộ hệ thống CNTT thuộc Công ty; bao gồm: Quản lý hệ thống công ty, hệ thống ứng dụng CNTT phục vụ hoạt động phát triển Công ty.'),
(3, 'Phòng nhân sự', 'Tìm kiếm và lựa chọn nhân sự phù hợp để thực hiện các mục tiêu kinh doanh của Công ty, tổ chức các chương trình đào tạo ngắn hạn cho nhân viên. Định kỳ tiến hành đánh giá hiệu quả làm việc của nhân viên công ty, đưa ra các quyết định khen thưởng để thúc đẩy tinh thần làm việc của nhân viên.'),
(4, 'Phòng kế toán', 'Hỗ trợ nhà lãnh đạo, ban giám đốc công ty theo dõi và quản lý dòng tiền trong tổ chức. Để đảm bảo tuyển dụng kế toán và xây dựng đội ngũ cán bộ công nhân viên bộ phận kế toán theo yêu cầu, chiến lược của Công ty cần xây dựng bản mô tả công việc.'),
(5, 'Phòng bảo vệ', 'Phòng Bảo vệ có chức năng nhiệm vụ, giúp việc cho Công ty về công tác an ninh trật tự, công tác phòng cháy chữa cháy');

-- --------------------------------------------------------

--
-- Table structure for table `TASK`
--

CREATE TABLE `TASK` (
  `task_id` int NOT NULL,
  `nguoi_tao_id` int DEFAULT NULL,
  `nguoi_thuc_hien_id` int DEFAULT NULL,
  `ten_task` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `mo_ta` varchar(2550) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mo_ta_nop` varchar(2550) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int DEFAULT NULL,
  `muc_do_hoan_thanh` int DEFAULT '1',
  `file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_nop` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thoi_gian_deadline` datetime DEFAULT NULL,
  `thoi_gian_hoan_thanh` datetime DEFAULT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `TASK`
--

INSERT INTO `TASK` (`task_id`, `nguoi_tao_id`, `nguoi_thuc_hien_id`, `ten_task`, `mo_ta`, `mo_ta_nop`, `status`, `muc_do_hoan_thanh`, `file`, `file_nop`, `thoi_gian_deadline`, `thoi_gian_hoan_thanh`, `note`) VALUES
(1, 2, 5, 'Chức năng thanh toán', 'Thực hiện liên kết các ví điện tử để khách hàng có thể thanh toán', NULL, 1, 1, '/files/task/task_giao/1/Chuc-nang-thanh-toan.txt', NULL, '2022-01-19 10:00:00', NULL, NULL),
(2, 2, 6, 'Trải nghiệm người dùng', 'Test và fix bug', NULL, 1, 1, '/files/task/task_giao/4/UI.txt', NULL, '2022-01-25 11:00:00', NULL, NULL),
(3, 2, 7, 'Fix bug', 'Kiểm tra lỗi khi khách hàng đăng nhập vào hệ thống', NULL, 1, 1, '/files/task/task_giao/3/Loi-can-fix.txt', NULL, '2022-01-29 15:00:00', NULL, NULL),
(4, 2, 6, 'Chức năng thông báo', 'Thông báo cho khách hàng khi đơn hàng bị hủy', NULL, 2, 1, '/files/task/task_giao/2/thong-bao.txt', NULL, '2022-01-22 14:00:00', NULL, NULL),
(5, 2, 5, 'Đặt hàng view', 'Chỉnh sửa giao diện đặt hàng mới', 'Đã chỉnh sửa', 4, 1, '/files/task/task_giao/5/mo-ta.txt', '/files/task/task_nop/5/bao-cao-dat-hang.txt', '2022-01-24 16:30:00', '2022-01-17 10:30:00', NULL),
(6, 2, 6, 'Chỉnh sửa thông báo', 'View Thông báo cần cập nhật lại', 'Thêm tính năng', 5, 1, '/files/task/task_giao/6/update-thong-bao.txt', '/files/task/task_nop/6/update.c', '2022-01-22 14:30:00', '2022-01-19 10:00:00', NULL),
(7, 2, 7, 'Xem sản phẩm view', 'Cập nhật lại giao diện khi khách hàng xem sản phẩm', 'Đã cập nhật', 5, 1, '/files/task/task_giao/7/update-san-pham.txt', '/files/task/task_nop/7/Xem-san-pham-updated.txt', '2022-01-25 14:30:00', '2022-01-24 11:10:00', NULL),
(8, 2, 5, 'Hiển thị sản phẩm view', 'Cập nhật FE cho view', 'Đã có đầy đủ tính năng như mô tả', 6, 3, '/files/task/task_giao/8/mo-ta.txt', '/files/task/task_nop/8/bao-cao.txt', '2022-01-18 11:00:00', '2022-01-17 10:00:00', NULL),
(9, 3, 8, 'Tuyển dụng', 'Cần thêm đội ngũ vào phòng CNTT', NULL, 1, 1, '/files/task/task_giao/9/tuyen-dung.txt', NULL, '2022-01-28 11:00:00', NULL, NULL),
(10, 3, 9, 'Đào tạo nhân viên', 'Hỗ trợ nhân viên hòa nhập với môi trường làm việc của công ty', NULL, 2, 1, '/files/task/task_giao/10/dao-tao.txt', NULL, '2022-02-15 15:00:00', NULL, NULL),
(11, 3, 9, 'Đánh giá', 'Lập bảng thống kê hoạt động đánh giá các phòng ban', NULL, 3, 1, '/files/task/task_giao/19/ke-hoach.txt', NULL, '2022-02-18 14:00:00', NULL, NULL),
(12, 3, 10, 'Tổ chức chương trình đào tạo mới', 'Xác định nhu cầu, xây dựng và quyết định chương trình đào tạo cụ thể', 'Nộp báo cáo kế hoạch', 4, 1, '/files/task/task_giao/11/huong-dan.txt', '/files/task/task_nop/11/bao-cao.txt', '2022-01-30 17:00:00', '2022-02-24 14:10:00', NULL),
(13, 3, 8, 'Đánh giá phòng CNTT', 'Tiến hành đánh giá hiệu quả làm việc của phòng CNTT', 'Đánh giá lần 2', 5, 1, '/files/task/task_giao/12/huong-dan.txt', '/files/task/task_nop/12/danh-gia-lan-2.txt', '2022-01-29 13:00:00', '2022-01-24 09:45:00', NULL),
(14, 3, 10, 'Thông báo quy định', 'Thông báo các thông tin đến toàn thể nhân viên của công ty', 'Báo cáo hoàn thành', 6, 3, '/files/task/task_giao/13/quy-dinh-moi.txt', '/files/task/task_nop/13/noi-dung-thong-bao.txt', '2022-01-25 16:30:00', '2022-01-23 10:45:00', NULL),
(15, 4, 11, 'Thanh toán các khoản của công ty', 'Thống kê và tính toán kỳ nợ lần trước', NULL, 1, 1, '/files/task/task_giao/14/thong-tin.txt', NULL, '2022-02-05 10:40:00', NULL, NULL),
(16, 4, 12, 'Báo cáo kết quả kinh doanh', 'Báo cáo kết quả kinh doanh cho ban quản lý', NULL, 2, 1, '/files/task/task_giao/15/mau-bao-cao.txt', NULL, '2022-01-30 15:45:00', NULL, NULL),
(17, 4, 12, 'Xử lý thông tin', 'Hạch toán và quản lý công nợ cũng như các khoản chi tiêu', NULL, 3, 1, '/files/task/task_giao/16/mau-bao-cao.txt', NULL, '2022-01-24 09:30:00', NULL, NULL),
(18, 4, 13, 'Sắp xếp thời gian chi trả', 'Sắp xếp thời gian chi trả theo quy định của nhà nước', NULL, 1, 1, '/files/task/task_giao/17/quy-dinh.txt', NULL, '2022-02-04 09:30:00', NULL, NULL),
(19, 4, 11, 'Quản lý nguồn vốn', 'Quản lý nguồn vốn trong các phong ban', 'Báo cáo phòng CNTT', 4, 1, '/files/task/task_giao/18/mau-thong-tin.txt', '/files/task/task_nop/18/phong-cntt.txt', '2022-01-28 09:00:00', '2022-01-21 15:00:00', NULL);

--
-- Triggers `TASK`
--
DELIMITER $$
CREATE TRIGGER `insert_TASKS_chung_phong_ban` BEFORE INSERT ON `TASK` FOR EACH ROW begin
	if new.nguoi_tao_id not in (select n.user_id from USERS n where n.phong_ban_id in (select phong_ban_id from USERS n2 where new.nguoi_thuc_hien_id = n2.user_id)) then
		SIGNAL SQLSTATE '45008'
            SET MESSAGE_TEXT = N'Người tạo và người thực hiện phải cùng chung phòng ban';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_TASKS_muc_do_hoan_thanh` BEFORE INSERT ON `TASK` FOR EACH ROW begin
	if new.muc_do_hoan_thanh not between 1 and 3 then
		SIGNAL SQLSTATE '45007'
            SET MESSAGE_TEXT = N'Nhập lại mức độ hoàn thành của task';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_TASKS_status` BEFORE INSERT ON `TASK` FOR EACH ROW begin
	if new.status not between 1 and 6 then
		SIGNAL SQLSTATE '45006'
            SET MESSAGE_TEXT = N'Nhập lại status của task';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_TASKS_thoi_gian` BEFORE INSERT ON `TASK` FOR EACH ROW begin
	if new.thoi_gian_deadline <= now() then
		SIGNAL SQLSTATE '45009'
            SET MESSAGE_TEXT = N'Thời hạn hoàn thành task phải lớn hơn hiện tại';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_TASKS_chung_phong_ban` BEFORE UPDATE ON `TASK` FOR EACH ROW begin
	if new.nguoi_tao_id not in (select n.user_id from USERS n where n.phong_ban_id in (select phong_ban_id from USERS n2 where new.nguoi_thuc_hien_id = n2.user_id)) then
		SIGNAL SQLSTATE '45008'
            SET MESSAGE_TEXT = N'Người tạo và người thực hiện phải cùng chung phòng ban';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_TASKS_muc_do_hoan_thanh` BEFORE UPDATE ON `TASK` FOR EACH ROW begin
	if new.muc_do_hoan_thanh not between 1 and 3 then
		SIGNAL SQLSTATE '45007'
            SET MESSAGE_TEXT = N'Nhập lại mức độ hoàn thành của task';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_TASKS_status` BEFORE UPDATE ON `TASK` FOR EACH ROW begin
	if new.status not between 1 and 6 then
		SIGNAL SQLSTATE '45006'
            SET MESSAGE_TEXT = N'Nhập lại status của task';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_TASKS_thoi_gian` BEFORE UPDATE ON `TASK` FOR EACH ROW begin
	if new.thoi_gian_deadline <= now() then
		SIGNAL SQLSTATE '45009'
            SET MESSAGE_TEXT = N'Thời hạn hoàn thành task phải lớn hơn hiện tại';
	end if;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `USERS`
--

CREATE TABLE `USERS` (
  `user_id` int NOT NULL,
  `user_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_role` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `ho_ten` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gioi_tinh` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `sdt` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `anh_dai_dien` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `so_absence_max` int DEFAULT NULL,
  `phong_ban_id` int DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `USERS`
--

INSERT INTO `USERS` (`user_id`, `user_name`, `password`, `user_role`, `ho_ten`, `gioi_tinh`, `ngay_sinh`, `sdt`, `anh_dai_dien`, `active`, `so_absence_max`, `phong_ban_id`, `status`) VALUES
(1, 'admin', '$2y$12$zDSmZKNrizcmC/A5LGu3hOJIpWMg6vwpTbdZQt.68VTmFL64OG6aq', 'Giám đốc', 'admin', 'Nam', '2000-12-19', '0915634258', NULL, 1, NULL, 1, 0),
(2, 'minhtriet', '$2y$12$2UNsJK/Ybz9qYldJBrKUsu78Qjc4rxpjvJfRV18gi4OWO0Z1w.4CO', 'Nhân viên', 'Phan Minh Triết', 'Nam', '2001-03-31', '0969782633', NULL, 1, 15, 2, 1),
(3, 'dangtri', '$2y$12$r1BHfIbvQcGaWBhsJ.60VuObSG9GKCwbfm7avcJe0TLOl4q6IZdIi', 'Nhân viên', 'Đặng Đăng Trí', 'Nam', '2001-11-29', '0915478422', NULL, 1, 15, 3, 1),
(4, 'thanghy', '$2y$12$KDXocSIrERm8Nkke0KPt5eDYDaCfV3F9Au14pM/J9maANb4UYvFmS', 'Nhân viên', 'Xin Thăng Hỷ', 'Nữ', '2001-02-28', '0938126455', NULL, 1, 15, 4, 1),
(5, 'giangdien', '$2y$12$HPmjFoMKWLroP77vQuWki.11vtnsvLX4zu8/aW0hwf0ckCM5m99Q.', 'Nhân viên', 'Trương Vinh Diễn', 'Nam', '2001-11-13', '0981033100', '/images/user_avt/5/avatar.jpg', 1, 12, 2, 0),
(6, 'ngocanh', '$2y$12$5e6dSX3JEFhgR9n5o2.lauCJ76uZ4NTG31ILLMjDOKHLabxtihkl2', 'Nhân viên', 'Hoàng Thị Ngọc Anh', 'Nữ', '2001-10-08', '0191435077', '/images/user_avt/6/avatar.jpg', 1, 12, 2, 0),
(7, 'vantung', '$2y$12$6Ce0n.h4/f.quCnzR14bEuZqq6fZaEyYdVrKQ66UqmAPieRyw8Czi', 'Nhân viên', 'Lê Văn Tùng', 'Nam', '2001-06-06', '0909714532', '/images/user_avt/7/avatar.jpg', 1, 12, 2, 0),
(8, 'hoaibao', '$2y$12$czDvDZhM5yBdHc.iMtQYDOYY/XQ6XjyxCXN.OM8gGrKn9Dv3VyBFu', 'Nhân viên', 'Trần Hoài Bảo', 'Nam', '2001-10-03', '0985840366', '/images/user_avt/8/avatar.jpg', 1, 12, 3, 0),
(9, 'baothai', '$2y$12$1PEM2qVDRpgIb/AelWH7aOq30wYu4WFPTQlCmwpWYtM7ja0GGHf.6', 'Nhân viên', 'Nguyễn Bảo Thái', 'Nam', '2001-08-27', '0962004072', '/images/user_avt/9/avatar.jpg', 1, 12, 3, 0),
(10, 'khanhhuyen', '$2y$12$coy.T4Yadv/X2umXh0KAnOyi6.COGAV3TtZ9zenJDCh1g1DesDSg.', 'Nhân viên', 'Lê Thị Khánh Huyền', 'Nữ', '2001-09-12', '0937626140', '/images/user_avt/10/avatar.jpg', 1, 12, 3, 0),
(11, 'minhcuong', '$2y$12$tZU9LVGJSZapAUwNR2pCdO71dr0I/TmYweGOqWaUhgJcmITbCtadW', 'Nhân viên', 'Đoàn Minh Cường', 'Nam', '2001-07-19', '0788159438', '/images/user_avt/11/avatar.jpg', 1, 12, 4, 0),
(12, 'thuthao', '$2y$12$n8FXxjFpH.q/d6Ai2OCdkOU2LkoLbvGkAYdYnCXzEZOiQcw.rAnky', 'Nhân viên', 'Phạm Thị Thu Thảo', 'Nữ', '2001-08-09', '0932141589', '/images/user_avt/12/avatar.jpg', 1, 12, 4, 0),
(13, 'viethung', '$2y$12$GzuKHsGUUjZOWT.olSF5zu/VOnfldxDS7JKRxtUdM/glJpQAkbo3O', 'Nhân viên', 'Nguyễn Viết Hùng', 'Nam', '2001-12-02', '0934561282', '/images/user_avt/13/avatar.jpg', 1, 12, 4, 0),
(14, 'tuandat', '$2y$12$MX9k/Vy/Bg/ATJ3D18Gkv.6VnDHZYunkTX0nWLAMHXvQaBlYcvQfS', 'Nhân viên', 'Lâm Tuấn Đạt', 'Nam', '2001-10-10', '0912464808', '/images/user_avt/14/avatar.jpg', 1, 15, 5, 1),
(15, 'dangtuan', '$2y$12$txGFkeusI0gUiW13VypYqOrgC2qeWVK8y1JzRSt4fq2TP6n8d8h7y', 'Nhân viên', 'Trần Đăng Tuấn', 'Nam', '2001-01-29', '0903451188', '/images/user_avt/15/avatar.jpg', 1, 12, 5, 0);

--
-- Triggers `USERS`
--
DELIMITER $$
CREATE TRIGGER `auto_update_absence_max` BEFORE UPDATE ON `USERS` FOR EACH ROW begin
	if new.user_role != N'Giám đốc' then
		if new.status = 0 then
			SET new.so_absence_max = 12;
		else
			SET new.so_absence_max = 15;
		end if;
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_USERS_absence_max_default` BEFORE INSERT ON `USERS` FOR EACH ROW begin
	if new.user_role != N'Giám đốc' then
		if new.status = 0 then
			SET new.so_absence_max = 12;
		else
			SET new.so_absence_max = 15;
		end if;
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_USERS_gioi_tinh` BEFORE INSERT ON `USERS` FOR EACH ROW begin
	IF new.gioi_tinh not in (N'Nam', N'Nữ') THEN
        SIGNAL SQLSTATE '45001'
            SET MESSAGE_TEXT = N'Giới tính phải là Nam hoặc Nữ';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_USERS_nhan_vien` BEFORE INSERT ON `USERS` FOR EACH ROW begin
	if new.status = 1 and new.user_role = N'Giám đốc' then
		SIGNAL SQLSTATE '45004'
            SET MESSAGE_TEXT = N'user_role_status_giamdoc';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_USERS_so_absence_max` BEFORE INSERT ON `USERS` FOR EACH ROW begin
	if new.user_role = N'Nhân viên' and new.so_absence_max not in (12, 15) then
		SIGNAL SQLSTATE '45002'
            SET MESSAGE_TEXT = N'absence_max_condition';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_USERS_so_absence_max_giam_doc` BEFORE INSERT ON `USERS` FOR EACH ROW begin
	if new.user_role = N'Giám đốc' and new.so_absence_max is not null then
		SIGNAL SQLSTATE '45002'
            SET MESSAGE_TEXT = N'Không cần nhập số ngày nghỉ của giám đốc';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_USERS_status` BEFORE INSERT ON `USERS` FOR EACH ROW begin
	if new.status not in (0, 1) then
		SIGNAL SQLSTATE '45003'
            SET MESSAGE_TEXT = N'Nhập lại chức vụ';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_USERS_truong_phong` AFTER INSERT ON `USERS` FOR EACH ROW begin
	if (select count(*) from USERS where status = 1 and new.phong_ban_id = USERS.phong_ban_id) > 1 then
		SIGNAL SQLSTATE '45005'
            SET MESSAGE_TEXT = N'max_leader_condition';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_USERS_user_role` BEFORE INSERT ON `USERS` FOR EACH ROW begin
	if new.user_role not in (N'Giám đốc', N'Nhân viên') then
		SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = N'Lỗi user role';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_USERS_gioi_tinh` BEFORE UPDATE ON `USERS` FOR EACH ROW begin
	IF new.gioi_tinh not in (N'Nam', N'Nữ') THEN
        SIGNAL SQLSTATE '45001'
            SET MESSAGE_TEXT = N'Giới tính phải là Nam hoặc Nữ';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_USERS_nhan_vien` BEFORE UPDATE ON `USERS` FOR EACH ROW begin
	if new.status = 1 and new.user_role = N'Giám đốc' then
		SIGNAL SQLSTATE '45004'
            SET MESSAGE_TEXT = N'Chức vụ phòng ban không hợp lệ';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_USERS_so_absence_max` BEFORE UPDATE ON `USERS` FOR EACH ROW begin
	if new.user_role = N'Nhân viên' and new.so_absence_max not in (12, 15) then
		SIGNAL SQLSTATE '45002'
            SET MESSAGE_TEXT = N'absence_max_condition';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_USERS_so_absence_max_giam_doc` BEFORE UPDATE ON `USERS` FOR EACH ROW begin
	if new.user_role = N'Giám đốc' and new.so_absence_max is not null then
		SIGNAL SQLSTATE '45002'
            SET MESSAGE_TEXT = N'Không cần nhập số ngày nghỉ của giám đốc';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_USERS_status` BEFORE UPDATE ON `USERS` FOR EACH ROW begin
	if new.status not in (0, 1) then
		SIGNAL SQLSTATE '45003'
            SET MESSAGE_TEXT = N'Nhập lại chức vụ phòng ban';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_USERS_truong_phong` AFTER UPDATE ON `USERS` FOR EACH ROW begin
	if (select count(*) from USERS where status = 1 and new.phong_ban_id = USERS.phong_ban_id) > 1 then
		SIGNAL SQLSTATE '45005'
            SET MESSAGE_TEXT = N'max_leader_condition';
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_USERS_user_role` BEFORE UPDATE ON `USERS` FOR EACH ROW begin
	if new.user_role not in (N'Giám đốc', N'Nhân viên') then
		SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = N'Lỗi user role';
	end if;
end
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ABSENCE`
--
ALTER TABLE `ABSENCE`
  ADD PRIMARY KEY (`absence_id`),
  ADD KEY `fk_ABSENCE_nguoi_tao_id` (`nguoi_tao_id`),
  ADD KEY `fk_ABSENCE_nguoi_duyet_id` (`nguoi_duyet_id`);

--
-- Indexes for table `HISTORY`
--
ALTER TABLE `HISTORY`
  ADD PRIMARY KEY (`history_id`,`task_id`),
  ADD KEY `fk_HISTORY_task_id` (`task_id`);

--
-- Indexes for table `PHONG_BAN`
--
ALTER TABLE `PHONG_BAN`
  ADD PRIMARY KEY (`phong_ban_id`);

--
-- Indexes for table `TASK`
--
ALTER TABLE `TASK`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `fk_TASK_nguoi_tao` (`nguoi_tao_id`),
  ADD KEY `fk_TASK_nguoi_thuc_hien` (`nguoi_thuc_hien_id`);

--
-- Indexes for table `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `sdt` (`sdt`),
  ADD KEY `fk_NHANVIEN_PHONGBAN` (`phong_ban_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ABSENCE`
--
ALTER TABLE `ABSENCE`
  MODIFY `absence_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `HISTORY`
--
ALTER TABLE `HISTORY`
  MODIFY `history_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `PHONG_BAN`
--
ALTER TABLE `PHONG_BAN`
  MODIFY `phong_ban_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `TASK`
--
ALTER TABLE `TASK`
  MODIFY `task_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `USERS`
--
ALTER TABLE `USERS`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ABSENCE`
--
ALTER TABLE `ABSENCE`
  ADD CONSTRAINT `fk_ABSENCE_nguoi_duyet_id` FOREIGN KEY (`nguoi_duyet_id`) REFERENCES `USERS` (`user_id`),
  ADD CONSTRAINT `fk_ABSENCE_nguoi_tao_id` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `USERS` (`user_id`);

--
-- Constraints for table `HISTORY`
--
ALTER TABLE `HISTORY`
  ADD CONSTRAINT `fk_HISTORY_task_id` FOREIGN KEY (`task_id`) REFERENCES `TASK` (`task_id`);

--
-- Constraints for table `TASK`
--
ALTER TABLE `TASK`
  ADD CONSTRAINT `fk_TASK_nguoi_tao` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `USERS` (`user_id`),
  ADD CONSTRAINT `fk_TASK_nguoi_thuc_hien` FOREIGN KEY (`nguoi_thuc_hien_id`) REFERENCES `USERS` (`user_id`);

--
-- Constraints for table `USERS`
--
ALTER TABLE `USERS`
  ADD CONSTRAINT `fk_NHANVIEN_PHONGBAN` FOREIGN KEY (`phong_ban_id`) REFERENCES `PHONG_BAN` (`phong_ban_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
