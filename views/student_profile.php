<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
require_once __DIR__ . '/../functions/student_functions.php';
$currentUser = getCurrentUser();

// Get student ID from URL
$studentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$student = null;

if ($studentId > 0) {
    $student = getStudentById($studentId);
}

if (!$student) {
    header('Location: student.php?error=Không tìm thấy sinh viên');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ Sinh viên - <?= htmlspecialchars($student['full_name'] ?? $student['student_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../css/admin_dashboard.css" rel="stylesheet">
    <link href="../css/student_profile.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="btn-menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="breadcrumb-nav">
                        <a href="admin_dashboard.php">Trang chủ</a>
                        <span>/</span>
                        <a href="student.php">Quản lý Sinh viên</a>
                        <span>/</span>
                        <span class="current">Chi tiết Hồ sơ</span>
                    </div>
                </div>
                
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../images/aiotlab_logo.png" alt="Admin">
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($currentUser['username'] ?? 'Admin') ?></span>
                            <span class="user-email">admin@university.edu</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content">
                <!-- Page Header -->
                <div class="profile-header">
                    <h1 class="page-title">Hồ sơ Sinh viên</h1>
                    <p class="page-subtitle">Xem và quản lý thông tin sinh viên.</p>
                </div>

                <!-- Student Info Header -->
                <div class="student-info-header">
                    <div class="student-name-section">
                        <h2>Hiển thị thông tin của sinh viên <strong><?= htmlspecialchars($student['full_name'] ?? $student['student_name']) ?></strong></h2>
                    </div>
                    <div class="action-buttons">
                        <button class="btn-action btn-secondary" onclick="window.print()">
                            <i class="fas fa-file-pdf me-2"></i>Xuất PDF
                        </button>
                        <a href="student/edit_student.php?id=<?= $studentId ?>" class="btn-action btn-primary">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa Hồ sơ
                        </a>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="profile-content">
                    <!-- Left Column - Avatar & Basic Info -->
                    <div class="profile-sidebar">
                        <div class="avatar-card">
                            <div class="avatar-wrapper">
                                <?php if (!empty($student['image']) && file_exists(__DIR__ . '/../' . $student['image'])): ?>
                                    <img src="../<?= htmlspecialchars($student['image']) ?>" alt="Avatar" class="student-avatar">
                                <?php else: ?>
                                    <div class="avatar-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h3 class="student-name"><?= htmlspecialchars($student['full_name'] ?? $student['student_name']) ?></h3>
                            <p class="student-code">MSV: <?= htmlspecialchars($student['student_code']) ?></p>
                            <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $student['status'] ?? 'active')) ?>">
                                <?= htmlspecialchars($student['status'] ?? 'Đang học') ?>
                            </span>
                        </div>

                        <div class="quick-info-card">
                            <div class="info-item">
                                <i class="fas fa-graduation-cap"></i>
                                <div>
                                    <span class="info-label">Khoa:</span>
                                    <span class="info-value"><?= htmlspecialchars($student['major'] ?? 'N/A') ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-chalkboard"></i>
                                <div>
                                    <span class="info-label">Lớp:</span>
                                    <span class="info-value"><?= htmlspecialchars($student['class_name'] ?? 'N/A') ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <div>
                                    <span class="info-label">Khóa học:</span>
                                    <span class="info-value"><?= htmlspecialchars($student['academic_year'] ?? 'N/A') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Detailed Info -->
                    <div class="profile-main">
                        <!-- Tabs -->
                        <div class="profile-tabs">
                            <button class="tab-btn active" data-tab="personal">Thông tin cá nhân</button>
                            <button class="tab-btn" data-tab="academic">Quá trình học tập</button>
                            <button class="tab-btn" data-tab="family">Khen thưởng & Kỷ luật</button>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content active" id="personal">
                            <h3 class="section-title">Thông tin cá bản</h3>
                            
                            <div class="info-grid">
                                <div class="info-field">
                                    <label>Ngày sinh</label>
                                    <p><?= $student['date_of_birth'] ? date('d/m/Y', strtotime($student['date_of_birth'])) : 'N/A' ?></p>
                                </div>
                                <div class="info-field">
                                    <label>Giới tính</label>
                                    <p><?= htmlspecialchars($student['gender'] ?? 'N/A') ?></p>
                                </div>
                                <div class="info-field">
                                    <label>Nơi sinh</label>
                                    <p><?= htmlspecialchars($student['hometown'] ?? 'N/A') ?></p>
                                </div>
                                <div class="info-field">
                                    <label>Quốc tịch</label>
                                    <p>Việt Nam</p>
                                </div>
                                <div class="info-field">
                                    <label>Số CMND/CCCD</label>
                                    <p><?= htmlspecialchars($student['id_card'] ?? 'N/A') ?></p>
                                </div>
                            </div>

                            <h3 class="section-title mt-4">Thông tin liên hệ</h3>
                            
                            <div class="info-grid">
                                <div class="info-field">
                                    <label>Số điện thoại</label>
                                    <p><?= htmlspecialchars($student['phone'] ?? 'N/A') ?></p>
                                </div>
                                <div class="info-field">
                                    <label>Email</label>
                                    <p><?= htmlspecialchars($student['email'] ?? 'N/A') ?></p>
                                </div>
                                <div class="info-field full-width">
                                    <label>Địa chỉ thường trú</label>
                                    <p><?= htmlspecialchars($student['address'] ?? 'N/A') ?></p>
                                </div>
                                <div class="info-field full-width">
                                    <label>Địa chỉ tạm trú</label>
                                    <p><?= htmlspecialchars($student['address'] ?? 'N/A') ?></p>
                                </div>
                            </div>

                            <h3 class="section-title mt-4">Thông tin gia đình</h3>
                            
                            <div class="info-grid">
                                <div class="info-field">
                                    <label>Họ tên phụ huynh</label>
                                    <p>Nguyễn Văn B</p>
                                </div>
                                <div class="info-field">
                                    <label>Số điện thoại phụ huynh</label>
                                    <p>0912 345 678</p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-content" id="academic">
                            <h3 class="section-title">Quá trình học tập</h3>
                            <div class="info-grid">
                                <div class="info-field">
                                    <label>Ngày nhập học</label>
                                    <p><?= $student['enrollment_date'] ? date('d/m/Y', strtotime($student['enrollment_date'])) : 'N/A' ?></p>
                                </div>
                                <div class="info-field">
                                    <label>Trạng thái</label>
                                    <p><?= htmlspecialchars($student['status'] ?? 'N/A') ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-content" id="family">
                            <h3 class="section-title">Khen thưởng & Kỷ luật</h3>
                            <p class="text-muted">Chưa có thông tin khen thưởng hoặc kỷ luật</p>
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

        // Tab switching
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabName = btn.getAttribute('data-tab');
                
                // Remove active class from all tabs
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab
                btn.classList.add('active');
                document.getElementById(tabName).classList.add('active');
            });
        });
    </script>
</body>
</html>
