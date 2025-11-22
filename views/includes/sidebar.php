<?php
// Determine active page
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="<?= strpos($_SERVER['PHP_SELF'], '/views/') !== false && strpos($_SERVER['PHP_SELF'], '/views/student/') === false && strpos($_SERVER['PHP_SELF'], '/views/class/') === false && strpos($_SERVER['PHP_SELF'], '/views/subject/') === false && strpos($_SERVER['PHP_SELF'], '/views/grade/') === false && strpos($_SERVER['PHP_SELF'], '/views/major/') === false ? '../images/fitdnu_logo.png' : '../../images/fitdnu_logo.png' ?>" alt="University Logo">
            <span>University<br><small>Admin Panel</small></span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <?php
        // Xác định link dashboard dựa trên role
        $dashboardLink = 'admin_dashboard.php';
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'teacher') {
            $dashboardLink = 'teacher_dashboard.php';
        }
        
        // Xác định đường dẫn (trong subfolder hay không)
        $isInSubfolder = strpos($_SERVER['PHP_SELF'], '/views/student/') !== false || 
                         strpos($_SERVER['PHP_SELF'], '/views/class/') !== false || 
                         strpos($_SERVER['PHP_SELF'], '/views/subject/') !== false || 
                         strpos($_SERVER['PHP_SELF'], '/views/grade/') !== false || 
                         strpos($_SERVER['PHP_SELF'], '/views/major/') !== false ||
                         strpos($_SERVER['PHP_SELF'], '/views/users/') !== false;
        
        $prefix = $isInSubfolder ? '../' : '';
        ?>
        <a href="<?= $prefix . $dashboardLink ?>" class="nav-item <?= ($currentPage == 'admin_dashboard.php' || $currentPage == 'teacher_dashboard.php') ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i>
            <span>Trang tổng quan</span>
        </a>
        <a href="<?= strpos($_SERVER['PHP_SELF'], '/views/student/') !== false || strpos($_SERVER['PHP_SELF'], '/views/class/') !== false || strpos($_SERVER['PHP_SELF'], '/views/subject/') !== false || strpos($_SERVER['PHP_SELF'], '/views/grade/') !== false || strpos($_SERVER['PHP_SELF'], '/views/major/') !== false ? '../student.php' : 'student.php' ?>" class="nav-item <?= $currentPage == 'student.php' ? 'active' : '' ?>">
            <i class="fas fa-user-graduate"></i>
            <span>Quản lý Hồ sơ Sinh viên</span>
        </a>
        <a href="<?= strpos($_SERVER['PHP_SELF'], '/views/student/') !== false || strpos($_SERVER['PHP_SELF'], '/views/class/') !== false || strpos($_SERVER['PHP_SELF'], '/views/subject/') !== false || strpos($_SERVER['PHP_SELF'], '/views/grade/') !== false || strpos($_SERVER['PHP_SELF'], '/views/major/') !== false ? '../subject.php' : 'subject.php' ?>" class="nav-item <?= $currentPage == 'subject.php' ? 'active' : '' ?>">
            <i class="fas fa-book"></i>
            <span>Quản lý Môn học</span>
        </a>
        <a href="<?= strpos($_SERVER['PHP_SELF'], '/views/student/') !== false || strpos($_SERVER['PHP_SELF'], '/views/class/') !== false || strpos($_SERVER['PHP_SELF'], '/views/subject/') !== false || strpos($_SERVER['PHP_SELF'], '/views/grade/') !== false || strpos($_SERVER['PHP_SELF'], '/views/major/') !== false ? '../grade.php' : 'grade.php' ?>" class="nav-item <?= $currentPage == 'grade.php' ? 'active' : '' ?>">
            <i class="fas fa-star"></i>
            <span>Quản lý Điểm</span>
        </a>
        <a href="<?= strpos($_SERVER['PHP_SELF'], '/views/student/') !== false || strpos($_SERVER['PHP_SELF'], '/views/class/') !== false || strpos($_SERVER['PHP_SELF'], '/views/subject/') !== false || strpos($_SERVER['PHP_SELF'], '/views/grade/') !== false || strpos($_SERVER['PHP_SELF'], '/views/major/') !== false ? '../class.php' : 'class.php' ?>" class="nav-item <?= $currentPage == 'class.php' ? 'active' : '' ?>">
            <i class="fas fa-chalkboard"></i>
            <span>Quản lý Lớp học</span>
        </a>
        <a href="<?= strpos($_SERVER['PHP_SELF'], '/views/student/') !== false || strpos($_SERVER['PHP_SELF'], '/views/class/') !== false || strpos($_SERVER['PHP_SELF'], '/views/subject/') !== false || strpos($_SERVER['PHP_SELF'], '/views/grade/') !== false || strpos($_SERVER['PHP_SELF'], '/views/major/') !== false ? '../major.php' : 'major.php' ?>" class="nav-item <?= $currentPage == 'major.php' ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i>
            <span>Quản lý Ngành học</span>
        </a>
        
        <?php
        // Chỉ hiển thị menu Quản lý Người dùng cho Admin
        require_once __DIR__ . '/../../functions/auth.php';
        if (isAdmin()):
        ?>
        <a href="<?= strpos($_SERVER['PHP_SELF'], '/views/student/') !== false || strpos($_SERVER['PHP_SELF'], '/views/class/') !== false || strpos($_SERVER['PHP_SELF'], '/views/subject/') !== false || strpos($_SERVER['PHP_SELF'], '/views/grade/') !== false || strpos($_SERVER['PHP_SELF'], '/views/major/') !== false ? '../users.php' : 'users.php' ?>" class="nav-item <?= $currentPage == 'users.php' ? 'active' : '' ?>">
            <i class="fas fa-users-cog"></i>
            <span>Quản lý Người dùng</span>
        </a>
        <?php endif; ?>
        
        <div class="nav-divider"></div>
        
        <a href="<?= strpos($_SERVER['PHP_SELF'], '/views/student/') !== false || strpos($_SERVER['PHP_SELF'], '/views/class/') !== false || strpos($_SERVER['PHP_SELF'], '/views/subject/') !== false || strpos($_SERVER['PHP_SELF'], '/views/grade/') !== false || strpos($_SERVER['PHP_SELF'], '/views/major/') !== false ? '../../handle/logout_process.php' : '../handle/logout_process.php' ?>" class="nav-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Đăng xuất</span>
        </a>
    </nav>
</aside>
