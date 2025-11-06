<?php
// Sử dụng __DIR__ để tính toán đường dẫn chính xác từ vị trí file hiện tại
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();

// Lấy tên tệp hiện tại để highlight menu item active
$currentPage = basename($_SERVER['PHP_SELF']);
$currentPath = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống quản lý hồ sơ sinh viên đại học - DNU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/baitaplon/assets/css/menu.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid px-4">
            <!-- Toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Collapsible wrapper -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Navbar brand -->
                <a class="navbar-brand" href="/baitaplon/views/student_management.php">
                    <img src="/baitaplon/images/fitdnu_logo.png" height="45" alt="FIT-DNU Logo" loading="lazy" />
                </a>

                <!-- Left links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($currentPath, 'student_management.php') !== false) ? 'active' : '' ?>" 
                           href="/baitaplon/views/student_management.php">
                            <i class="fas fa-users"></i>
                            <span>Quản lý hồ sơ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($currentPath, 'student.php') !== false && strpos($currentPath, 'student_management.php') === false) ? 'active' : '' ?>" 
                           href="/baitaplon/views/student.php">
                            <i class="fas fa-list"></i>
                            <span>Danh sách sinh viên</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($currentPath, 'subject.php') !== false) ? 'active' : '' ?>" 
                           href="/baitaplon/views/subject.php">
                            <i class="fas fa-book"></i>
                            <span>Quản lý môn học</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($currentPath, 'major.php') !== false) ? 'active' : '' ?>" 
                           href="/baitaplon/views/major.php">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Quản lý ngành học</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($currentPath, 'grade.php') !== false) ? 'active' : '' ?>" 
                           href="/baitaplon/views/grade.php">
                            <i class="fas fa-chart-line"></i>
                            <span>Quản lý điểm số</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= (strpos($currentPath, 'reports') !== false || strpos($currentPath, 'report') !== false || strpos($currentPath, 'statistics') !== false) ? 'active' : '' ?>" 
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="reportDropdown">
                            <i class="fas fa-chart-bar"></i>
                            <span>Báo cáo</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="reportDropdown" id="reportDropdownMenu">
                            <li>
                                <a class="dropdown-item" href="/baitaplon/views/reports/student_statistics.php">
                                    <i class="fas fa-user-chart"></i>
                                    <span>Thống kê sinh viên</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/baitaplon/views/reports/grade_report.php">
                                    <i class="fas fa-file-chart-line"></i>
                                    <span>Báo cáo điểm</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="/baitaplon/views/statistics.php">
                                    <i class="fas fa-chart-pie"></i>
                                    <span>Thống kê tổng quan</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- Right elements -->
                <div class="d-flex align-items-center">
                    <div class="dropdown user-menu">
                        <a class="user-info dropdown-toggle" href="#" role="button" id="userDropdownToggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/baitaplon/images/aiotlab_logo.png" class="user-avatar" alt="User Avatar" loading="lazy" />
                            <div class="d-none d-md-block">
                                <div class="username"><?= htmlspecialchars($currentUser['username'] ?? 'Admin') ?></div>
                                <div class="user-role">Quản trị viên</div>
                            </div>
                            <i class="fas fa-chevron-down ms-2" style="font-size: 0.75rem; color: #6c757d;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" id="userDropdownMenu" aria-labelledby="userDropdownToggle">
                            <li>
                                <a class="dropdown-item logout-btn" href="/baitaplon/handle/logout_process.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    <span>Đăng xuất</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/baitaplon/assets/js/menu.js"></script>
</body>

</html>