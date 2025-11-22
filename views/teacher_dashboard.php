<?php
require_once __DIR__ . '/../functions/auth.php';
requireTeacherOrAdmin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang giảng viên - Quản lý</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../css/admin_dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php 
        // Include sidebar - đường dẫn đúng cho file trong thư mục views
        $sidebarPath = __DIR__ . '/includes/sidebar.php';
        if (file_exists($sidebarPath)) {
            include $sidebarPath;
        } else {
            // Fallback nếu không tìm thấy
            echo '<!-- Sidebar file not found -->';
        }
        ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="btn-menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Tìm kiếm sinh viên hoặc môn học...">
                    </div>
                </div>
                
                <div class="header-right">
                    <button class="btn-icon" title="Thông báo">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </button>
                    
                    <div class="user-profile">
                        <img src="../images/aiotlab_logo.png" alt="User">
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($currentUser['username']) ?></span>
                            <span class="user-email">Giảng viên</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <div>
                        <h1 class="page-title">Chào mừng, <?= htmlspecialchars($currentUser['username']) ?>!</h1>
                        <p class="page-subtitle">Hệ thống quản lý giảng dạy</p>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-content">
                            <p class="stat-label">Tổng số sinh viên</p>
                            <h3 class="stat-value"><?php 
                                require_once __DIR__ . '/../functions/student_functions.php';
                                echo count(getAllStudents());
                            ?></h3>
                            <span class="stat-change positive">Đang quản lý</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <p class="stat-label">Môn học</p>
                            <h3 class="stat-value"><?php 
                                require_once __DIR__ . '/../functions/subject_functions.php';
                                echo count(getAllSubjects());
                            ?></h3>
                            <span class="stat-change positive">Đang giảng dạy</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <p class="stat-label">Lớp học</p>
                            <h3 class="stat-value"><?php 
                                require_once __DIR__ . '/../functions/class_functions.php';
                                $classes = getAllClass();
                                echo count($classes);
                            ?></h3>
                            <span class="stat-change positive">Đang quản lý</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Thao tác nhanh</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="student.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-user-graduate d-block mb-2" style="font-size: 2rem;"></i>
                                    Quản lý Sinh viên
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="subject.php" class="btn btn-outline-success w-100">
                                    <i class="fas fa-book d-block mb-2" style="font-size: 2rem;"></i>
                                    Quản lý Môn học
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="grade.php" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-star d-block mb-2" style="font-size: 2rem;"></i>
                                    Quản lý Điểm
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="class.php" class="btn btn-outline-info w-100">
                                    <i class="fas fa-chalkboard d-block mb-2" style="font-size: 2rem;"></i>
                                    Quản lý Lớp học
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Menu Toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }
    </script>
</body>
</html>
