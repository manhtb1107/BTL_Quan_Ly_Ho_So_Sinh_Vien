-- =============================================
-- RECREATE DATABASE SCRIPT
-- Xóa và tạo lại database hoàn toàn mới
-- =============================================

-- Xóa database cũ nếu tồn tại
DROP DATABASE IF EXISTS `qlhssv`;

-- Tạo database mới
CREATE DATABASE `qlhssv` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Sử dụng database
USE `qlhssv`;

-- =============================================
-- CẤU TRÚC BẢNG
-- =============================================

-- Bảng users: Quản lý người dùng hệ thống
CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `email` varchar(100) DEFAULT NULL,
    `full_name` varchar(100) DEFAULT NULL,
    `role` enum('admin','teacher','student') DEFAULT 'admin',
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_username` (`username`),
    KEY `idx_role` (`role`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng majors: Quản lý ngành học
CREATE TABLE `majors` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `major_code` varchar(20) NOT NULL UNIQUE,
    `major_name` varchar(100) NOT NULL,
    `department` varchar(100) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_major_code` (`major_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng class: Quản lý lớp học
CREATE TABLE `class` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `class_code` varchar(20) NOT NULL UNIQUE,
    `class_name` varchar(100) NOT NULL,
    `major` varchar(100) DEFAULT NULL,
    `academic_year` varchar(20) DEFAULT NULL,
    `student_count` int(11) DEFAULT 0,
    `description` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_class_code` (`class_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng students: Quản lý sinh viên
CREATE TABLE `students` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `student_code` varchar(20) NOT NULL UNIQUE,
    `student_name` varchar(100) NOT NULL,
    `full_name` varchar(150) DEFAULT NULL,
    `date_of_birth` date DEFAULT NULL,
    `gender` enum('Nam','Nữ','Khác') DEFAULT NULL,
    `phone` varchar(15) DEFAULT NULL,
    `email` varchar(100) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `hometown` varchar(100) DEFAULT NULL,
    `id_card` varchar(20) DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `class_id` int(11) DEFAULT NULL,
    `major` int(11) DEFAULT NULL,
    `academic_year` varchar(20) DEFAULT NULL,
    `status` enum('Đang học','Tạm nghỉ','Bảo lưu','Tốt nghiệp','Bị đuổi học') DEFAULT 'Đang học',
    `enrollment_date` date DEFAULT NULL,
    `graduation_date` date DEFAULT NULL,
    `gpa` decimal(3,2) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_student_code` (`student_code`),
    KEY `idx_class_id` (`class_id`),
    KEY `idx_major` (`major`),
    CONSTRAINT `fk_students_class` FOREIGN KEY (`class_id`) REFERENCES `class` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_students_major` FOREIGN KEY (`major`) REFERENCES `majors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng subject: Quản lý môn học
CREATE TABLE `subject` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `subject_code` varchar(20) NOT NULL UNIQUE,
    `subject_name` varchar(150) NOT NULL,
    `credits` int(2) DEFAULT 3,
    `major_id` int(11) DEFAULT NULL,
    `subject_type` varchar(50) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_subject_code` (`subject_code`),
    KEY `idx_major_id` (`major_id`),
    CONSTRAINT `fk_subject_major` FOREIGN KEY (`major_id`) REFERENCES `majors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng grade: Quản lý điểm số
CREATE TABLE `grade` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `student_id` int(11) NOT NULL,
    `subject_id` int(11) NOT NULL,
    `test1_score` decimal(5,2) DEFAULT NULL COMMENT 'Điểm kiểm tra 1 (0-10)',
    `test2_score` decimal(5,2) DEFAULT NULL COMMENT 'Điểm kiểm tra 2 (0-10)',
    `attendance_score` decimal(5,2) DEFAULT NULL COMMENT 'Điểm chuyên cần (0-10)',
    `midterm_score` decimal(5,2) DEFAULT NULL COMMENT 'Điểm giữa kỳ (0-10) (legacy)',
    `final_score` decimal(5,2) DEFAULT NULL COMMENT 'Điểm cuối kỳ (0-10)',
    `total_score` decimal(5,2) DEFAULT NULL COMMENT 'Điểm tổng kết (0-10)',
    `letter_grade` varchar(2) DEFAULT NULL COMMENT 'Điểm chữ (A, B, C, D, F)',
    `semester` varchar(20) DEFAULT NULL,
    `academic_year` varchar(20) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_student_id` (`student_id`),
    KEY `idx_subject_id` (`subject_id`),
    CONSTRAINT `fk_grade_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_grade_subject` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`) ON DELETE CASCADE,
    CONSTRAINT `chk_test1_score` CHECK (`test1_score` IS NULL OR (`test1_score` >= 0 AND `test1_score` <= 10)),
    CONSTRAINT `chk_test2_score` CHECK (`test2_score` IS NULL OR (`test2_score` >= 0 AND `test2_score` <= 10)),
    CONSTRAINT `chk_attendance_score` CHECK (`attendance_score` IS NULL OR (`attendance_score` >= 0 AND `attendance_score` <= 10)),
    CONSTRAINT `chk_midterm_score` CHECK (`midterm_score` IS NULL OR (`midterm_score` >= 0 AND `midterm_score` <= 10)),
    CONSTRAINT `chk_final_score` CHECK (`final_score` IS NULL OR (`final_score` >= 0 AND `final_score` <= 10)),
    CONSTRAINT `chk_total_score` CHECK (`total_score` IS NULL OR (`total_score` >= 0 AND `total_score` <= 10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DỮ LIỆU MẪU
-- =============================================

-- Users
INSERT INTO `users` (`username`, `password`, `email`, `full_name`, `role`, `is_active`) VALUES
('admin', MD5('admin123'), 'admin@dnu.edu.vn', 'Quản trị viên', 'admin', 1),
('teacher1', MD5('teacher123'), 'teacher1@dnu.edu.vn', 'Nguyễn Văn Giảng', 'teacher', 1),
('teacher2', MD5('teacher123'), 'teacher2@dnu.edu.vn', 'Trần Thị Hương', 'teacher', 1);

-- Majors
INSERT INTO `majors` (`major_code`, `major_name`, `department`, `description`) VALUES
('CNTT', 'Công nghệ thông tin', 'Khoa Công nghệ Thông tin', 'Đào tạo kỹ sư công nghệ thông tin chất lượng cao'),
('KT', 'Kế toán', 'Khoa Kinh tế', 'Đào tạo cử nhân kế toán và kiểm toán'),
('QTKD', 'Quản trị kinh doanh', 'Khoa Kinh tế', 'Đào tạo cử nhân quản trị kinh doanh'),
('KTPM', 'Kỹ thuật phần mềm', 'Khoa Công nghệ Thông tin', 'Đào tạo kỹ sư phần mềm chuyên nghiệp'),
('ATTT', 'An toàn thông tin', 'Khoa Công nghệ Thông tin', 'Đào tạo chuyên gia an toàn thông tin');

-- Classes
INSERT INTO `class` (`class_code`, `class_name`, `major`, `academic_year`, `student_count`) VALUES
('CNTT01-K20', 'Công nghệ thông tin 01 - K20', 'Công nghệ thông tin', '2020-2024', 35),
('CNTT02-K20', 'Công nghệ thông tin 02 - K20', 'Công nghệ thông tin', '2020-2024', 32),
('CNTT01-K21', 'Công nghệ thông tin 01 - K21', 'Công nghệ thông tin', '2021-2025', 40),
('CNTT02-K21', 'Công nghệ thông tin 02 - K21', 'Công nghệ thông tin', '2021-2025', 38),
('KT01-K21', 'Kế toán 01 - K21', 'Kế toán', '2021-2025', 30),
('QTKD01-K21', 'Quản trị kinh doanh 01 - K21', 'Quản trị kinh doanh', '2021-2025', 28),
('KTPM01-K22', 'Kỹ thuật phần mềm 01 - K22', 'Kỹ thuật phần mềm', '2022-2026', 35),
('ATTT01-K22', 'An toàn thông tin 01 - K22', 'An toàn thông tin', '2022-2026', 25);

-- Subjects
INSERT INTO `subject` (`subject_code`, `subject_name`, `credits`, `subject_type`, `is_active`) VALUES
('CS101', 'Lập trình cơ bản', 3, 'Bắt buộc', 1),
('CS102', 'Cấu trúc dữ liệu và giải thuật', 4, 'Bắt buộc', 1),
('CS103', 'Lập trình hướng đối tượng', 3, 'Bắt buộc', 1),
('CS201', 'Cơ sở dữ liệu', 4, 'Bắt buộc', 1),
('CS202', 'Mạng máy tính', 3, 'Bắt buộc', 1),
('CS301', 'Công nghệ Web', 3, 'Bắt buộc', 1),
('CS302', 'Phát triển ứng dụng di động', 3, 'Tự chọn', 1),
('MATH101', 'Toán cao cấp A1', 4, 'Đại cương', 1),
('MATH102', 'Toán cao cấp A2', 4, 'Đại cương', 1),
('MATH201', 'Xác suất thống kê', 3, 'Đại cương', 1),
('ENG101', 'Tiếng Anh 1', 3, 'Đại cương', 1),
('ENG102', 'Tiếng Anh 2', 3, 'Đại cương', 1),
('PHY101', 'Vật lý đại cương', 3, 'Đại cương', 1);

-- Students
INSERT INTO `students` (`student_code`, `student_name`, `full_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `hometown`, `id_card`, `class_id`, `major`, `academic_year`, `status`, `enrollment_date`, `gpa`) VALUES
-- Khóa 2020 - Sắp tốt nghiệp
('SV2021001', 'Nguyễn Văn An', 'Nguyễn Văn An', '2003-01-15', 'Nam', '0901234567', 'nvana@student.dnu.edu.vn', '123 Lê Duẩn, Đà Nẵng', 'Đà Nẵng', '201234567', 1, 1, '2020-2024', 'Đang học', '2020-09-01', 3.45),
('SV2021002', 'Trần Thị Bình', 'Trần Thị Bình', '2003-03-20', 'Nữ', '0902345678', 'ttbinh@student.dnu.edu.vn', '456 Nguyễn Văn Linh, Đà Nẵng', 'Quảng Nam', '201234568', 1, 1, '2020-2024', 'Đang học', '2020-09-01', 3.78),
('SV2021003', 'Lê Văn Cường', 'Lê Văn Cường', '2003-05-10', 'Nam', '0903456789', 'lvcuong@student.dnu.edu.vn', '789 Hùng Vương, Đà Nẵng', 'Đà Nẵng', '201234569', 2, 1, '2020-2024', 'Đang học', '2020-09-01', 3.12),
('SV2021004', 'Phạm Thị Dung', 'Phạm Thị Dung', '2003-07-25', 'Nữ', '0904567890', 'ptdung@student.dnu.edu.vn', '321 Trần Phú, Đà Nẵng', 'Huế', '201234570', 2, 1, '2020-2024', 'Tốt nghiệp', '2020-09-01', 3.89),
('SV2021005', 'Hoàng Văn Em', 'Hoàng Văn Em', '2003-02-14', 'Nam', '0905678901', 'hvem@student.dnu.edu.vn', '654 Điện Biên Phủ, Đà Nẵng', 'Quảng Ngãi', '201234571', 1, 1, '2020-2024', 'Đang học', '2020-09-01', 3.56),

-- Khóa 2021 - Đang học năm 3
('SV2021006', 'Võ Thị Phương', 'Võ Thị Phương', '2004-04-12', 'Nữ', '0906789012', 'vtphuong@student.dnu.edu.vn', '111 Lê Lợi, Đà Nẵng', 'Đà Nẵng', '202234567', 3, 1, '2021-2025', 'Đang học', '2021-09-01', 3.67),
('SV2021007', 'Đặng Văn Giang', 'Đặng Văn Giang', '2004-06-18', 'Nam', '0907890123', 'dvgiang@student.dnu.edu.vn', '222 Hải Phòng, Đà Nẵng', 'Quảng Nam', '202234568', 3, 1, '2021-2025', 'Đang học', '2021-09-01', 3.23),
('SV2021008', 'Bùi Thị Hoa', 'Bùi Thị Hoa', '2004-08-22', 'Nữ', '0908901234', 'bthoa@student.dnu.edu.vn', '333 Phan Châu Trinh, Đà Nẵng', 'Đà Nẵng', '202234569', 4, 1, '2021-2025', 'Bảo lưu', '2021-09-01', 2.98),
('SV2021009', 'Ngô Văn Khoa', 'Ngô Văn Khoa', '2004-09-30', 'Nam', '0909012345', 'nvkhoa@student.dnu.edu.vn', '444 Ông Ích Khiêm, Đà Nẵng', 'Huế', '202234570', 4, 1, '2021-2025', 'Đang học', '2021-09-01', 3.45),
('SV2021010', 'Trương Thị Lan', 'Trương Thị Lan', '2004-11-05', 'Nữ', '0910123456', 'ttlan@student.dnu.edu.vn', '555 Núi Thành, Đà Nẵng', 'Quảng Ngãi', '202234571', 3, 1, '2021-2025', 'Đang học', '2021-09-01', 3.78),

-- Sinh viên ngành Kế toán
('SV2021011', 'Lý Văn Minh', 'Lý Văn Minh', '2004-01-20', 'Nam', '0911234567', 'lvminh@student.dnu.edu.vn', '666 Hoàng Diệu, Đà Nẵng', 'Đà Nẵng', '202234572', 5, 2, '2021-2025', 'Đang học', '2021-09-01', 3.34),
('SV2021012', 'Phan Thị Nga', 'Phan Thị Nga', '2004-03-15', 'Nữ', '0912345678', 'ptnga@student.dnu.edu.vn', '777 Lý Thái Tổ, Đà Nẵng', 'Quảng Nam', '202234573', 5, 2, '2021-2025', 'Đang học', '2021-09-01', 3.56),

-- Sinh viên ngành QTKD
('SV2021013', 'Đinh Văn Phúc', 'Đinh Văn Phúc', '2004-05-08', 'Nam', '0913456789', 'dvphuc@student.dnu.edu.vn', '888 Trường Chinh, Đà Nẵng', 'Huế', '202234574', 6, 3, '2021-2025', 'Đang học', '2021-09-01', 3.12),
('SV2021014', 'Vũ Thị Quỳnh', 'Vũ Thị Quỳnh', '2004-07-12', 'Nữ', '0914567890', 'vtquynh@student.dnu.edu.vn', '999 Ngô Quyền, Đà Nẵng', 'Đà Nẵng', '202234575', 6, 3, '2021-2025', 'Tạm nghỉ', '2021-09-01', 2.87),

-- Khóa 2022 - Năm 2
('SV2022001', 'Cao Văn Sơn', 'Cao Văn Sơn', '2005-02-10', 'Nam', '0915678901', 'cvson@student.dnu.edu.vn', '101 Lê Thánh Tông, Đà Nẵng', 'Quảng Ngãi', '203234567', 7, 4, '2022-2026', 'Đang học', '2022-09-01', 3.45),
('SV2022002', 'Đỗ Thị Tâm', 'Đỗ Thị Tâm', '2005-04-18', 'Nữ', '0916789012', 'dttam@student.dnu.edu.vn', '202 Lê Văn Hưu, Đà Nẵng', 'Đà Nẵng', '203234568', 7, 4, '2022-2026', 'Đang học', '2022-09-01', 3.67),
('SV2022003', 'Hồ Văn Tuấn', 'Hồ Văn Tuấn', '2005-06-25', 'Nam', '0917890123', 'hvtuan@student.dnu.edu.vn', '303 Phan Bội Châu, Đà Nẵng', 'Quảng Nam', '203234569', 8, 5, '2022-2026', 'Đang học', '2022-09-01', 3.23),
('SV2022004', 'Lâm Thị Uyên', 'Lâm Thị Uyên', '2005-08-30', 'Nữ', '0918901234', 'ltuyen@student.dnu.edu.vn', '404 Tôn Đức Thắng, Đà Nẵng', 'Huế', '203234570', 8, 5, '2022-2026', 'Đang học', '2022-09-01', 3.89);

-- Grades - Với công thức mới: ((Test1 + Test2) / 2) × 30% + Attendance × 10% + Final × 60%
INSERT INTO `grade` (`student_id`, `subject_id`, `test1_score`, `test2_score`, `attendance_score`, `final_score`, `total_score`, `letter_grade`, `semester`, `academic_year`) VALUES
-- Điểm môn CS101 - Lập trình cơ bản
(1, 1, 8.0, 7.5, 9.0, 7.5, 7.8, 'B', '1', '2020-2021'),
(2, 1, 9.0, 8.5, 10.0, 8.5, 8.8, 'A', '1', '2020-2021'),
(3, 1, 6.5, 7.0, 8.0, 7.0, 6.9, 'C', '1', '2020-2021'),
(5, 1, 7.5, 8.0, 9.5, 8.0, 7.9, 'B', '1', '2020-2021'),

-- Điểm môn CS102 - Cấu trúc dữ liệu
(1, 2, 7.5, 8.0, 9.0, 8.0, 7.9, 'B', '2', '2020-2021'),
(2, 2, 8.5, 9.0, 10.0, 9.0, 9.0, 'A', '2', '2020-2021'),
(3, 2, 6.0, 6.5, 7.5, 6.5, 6.4, 'C', '2', '2020-2021'),

-- Điểm môn MATH101 - Toán cao cấp
(1, 8, 8.5, 8.0, 9.5, 8.0, 8.2, 'A', '1', '2020-2021'),
(2, 8, 9.0, 9.5, 10.0, 9.5, 9.5, 'A', '1', '2020-2021'),
(3, 8, 7.0, 7.5, 8.5, 7.5, 7.5, 'B', '1', '2020-2021'),
(6, 8, 8.0, 8.5, 9.0, 8.5, 8.4, 'A', '1', '2021-2022'),
(7, 8, 7.5, 7.0, 8.0, 7.0, 7.2, 'B', '1', '2021-2022');

-- =============================================
-- HOÀN TẤT
-- =============================================

SELECT '✅ Database đã được tạo lại thành công!' as message;
SELECT '👤 Tài khoản: admin / admin123' as login_info;
SELECT CONCAT('👨‍🎓 Tổng sinh viên: ', COUNT(*)) as students_count FROM students;
SELECT CONCAT('📚 Tổng môn học: ', COUNT(*)) as subjects_count FROM subject;
SELECT CONCAT('🏫 Tổng lớp học: ', COUNT(*)) as classes_count FROM class;
SELECT CONCAT('📊 Tổng điểm: ', COUNT(*)) as grades_count FROM grade;
