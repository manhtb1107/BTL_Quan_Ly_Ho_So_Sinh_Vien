<h2 align="center">
    <a href="https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin">
    🎓 Faculty of Information Technology (DaiNam University)
    </a>
</h2>
<h2 align="center">
    Youth Union Member Management
</h2>
<div align="center">
    <p align="center">
        <img src="docs/logo/aiotlab_logo.png" alt="AIoTLab Logo" width="170"/>
        <img src="docs/logo/fitdnu_logo.png" alt="AIoTLab Logo" width="180"/>
        <img src="docs/logo/dnu_logo.png" alt="DaiNam University Logo" width="200"/>
    </p>

[![AIoTLab](https://img.shields.io/badge/AIoTLab-green?style=for-the-badge)](https://www.facebook.com/DNUAIoTLab)
[![Faculty of Information Technology](https://img.shields.io/badge/Faculty%20of%20Information%20Technology-blue?style=for-the-badge)](https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin)
[![DaiNam University](https://img.shields.io/badge/DaiNam%20University-orange?style=for-the-badge)](https://dainam.edu.vn)

</div>
 
## 📖 1. Giới thiệu
Hệ thống Quản lý Hồ sơ Sinh viên Đại học (DNU) là nền tảng web hỗ trợ quản trị toàn diện thông tin sinh viên, lớp, ngành, môn học và điểm số, giúp số hóa quy trình và nâng cao hiệu quả quản lý.

## 🔧 2. Các công nghệ được sử dụng
<div align="center">

### Hệ điều hành
![macOS](https://img.shields.io/badge/macOS-000000?style=for-the-badge&logo=macos&logoColor=F0F0F0)
[![Windows](https://img.shields.io/badge/Windows-0078D6?style=for-the-badge&logo=windows&logoColor=white)](https://www.microsoft.com/en-us/windows/)
[![Ubuntu](https://img.shields.io/badge/Ubuntu-E95420?style=for-the-badge&logo=ubuntu&logoColor=white)](https://ubuntu.com/)

### Công nghệ chính
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](#)
[![CSS](https://img.shields.io/badge/CSS-1572B6?style=for-the-badge&logo=css3&logoColor=white)](#)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](#)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)

### Web Server & Database
[![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white)](https://httpd.apache.org/)
[![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)](https://www.apachefriends.org/)

### Database Management Tools
[![MySQL Workbench](https://img.shields.io/badge/MySQL_Workbench-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/downloads/workbench/)
</div>

## 🚀 3. Hình ảnh các chức năng
### Trang đăng nhập
<img width="1902" height="931" alt="image" src="https://github.com/user-attachments/assets/c982cf12-24f8-4137-8ad4-d6c8768c5e54" />
### Trang dashboard admin
<img width="1885" height="935" alt="image" src="https://github.com/user-attachments/assets/ef86f98f-d8aa-4bdd-8157-ee1bae6a0a09" />
### Trang quản lý sinh viên 
<img width="1886" height="940" alt="image" src="https://github.com/user-attachments/assets/486fc55b-6c61-46e6-9c92-ca52ad7ad1b2" />
### Trang quản lí môn học     
<img width="1890" height="932" alt="image" src="https://github.com/user-attachments/assets/2b5e861a-8a59-4505-8149-4ceb1ec6b503" />
### Trang quản lý điểm số
<img width="1901" height="939" alt="image" src="https://github.com/user-attachments/assets/f3c88b32-5c7e-4911-b977-303523e5e4e3" />
### Trang quản lý ngành
<img width="1892" height="938" alt="image" src="https://github.com/user-attachments/assets/0ed13de0-2745-4770-b045-980c513a380d" />
### Trang thống kê sinh viên
<img width="1882" height="937" alt="image" src="https://github.com/user-attachments/assets/763dbe71-4984-459f-8348-18ce505479cc" />
### Trang báo cáo điểm số
<img width="1882" height="937" alt="image" src="https://github.com/user-attachments/assets/3b6a90e7-e622-4cc3-ab67-3218b195f8ed" />

### 4.1. Cài đặt công cụ, môi trường và các thư viện cần thiết

- Tải và cài đặt **XAMPP**  
  👉 https://www.apachefriends.org/download.html  
  (Khuyến nghị bản XAMPP với PHP 8.x)

- Cài đặt **Visual Studio Code** và các extension:
  - PHP Intelephense  
  - MySQL  
  - Prettier – Code Formatter  
### 4.2. Tải project
Clone project về thư mục `htdocs` của XAMPP (ví dụ ổ C):

```bash
cd C:\xampp\htdocs
https://github.com/manhtb1107/BTL_Quan_Ly_Ho_So_Sinh_Vien.git
Truy cập project qua đường dẫn:
👉 http://localhost/baitaplon.
```
### 4.3. Setup database
Mở XAMPP Control Panel, Start Apache và MySQL

Truy cập MySQL WorkBench
Tạo database:
```bash
CREATE DATABASE IF NOT EXISTS qlhssv
   CHARACTER SET utf8mb4
   COLLATE utf8mb4_unicode_ci;
```

### 4.4. Setup tham số kết nối
Mở file config.php (hoặc .env) trong project, chỉnh thông tin DB:
```bash

<?php

function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = ""; xài pass nếu như không bật mysql ở xampp
    $dbname = "qlhssv";
    $port = 3306;

    // Tạo kết nối
    $conn = mysqli_connect($servername, $username, $password, $dbname, $port);

    // Kiểm tra kết nối
    if (!$conn) {
        die("Kết nối database thất bại: " . mysqli_connect_error());
    }
    // Thiết lập charset cho kết nối (quan trọng để hiển thị tiếng Việt đúng)
    mysqli_set_charset($conn, "utf8");
    return $conn;
}

?>
```
### 4.5. Chạy hệ thống
Mở XAMPP Control Panel → Start Apache và MySQL ( 

Truy cập hệ thống:
👉 http://localhost/baitaplon

Hệ thống khởi tạo sẵn một tài khoản quản trị (Admin) để truy cập ban đầu.

Sau khi đăng nhập, Admin có thể:

Quản lý danh mục: tạo/sửa/xóa Ngành học, Lớp học, Môn học.

Quản lý Sinh viên: thêm mới, chỉnh sửa hồ sơ, cập nhật ảnh.

Quản lý Điểm số: nhập, cập nhật, theo dõi kết quả học tập.

Tra cứu và báo cáo: tìm kiếm theo lớp/ngành, xem báo cáo thống kê và tổng hợp.
