<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/db_connection.php';
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();

// Lấy tên tệp hiện tại để highlight menu item active
$currentPage = basename($_SERVER['PHP_SELF']);
$currentPath = $_SERVER['REQUEST_URI'];

// Lấy dữ liệu thống kê từ database
$conn = getDbConnection();

// Tổng số sinh viên
$totalStudents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM students"))['count'];

// Tổng số môn học
$totalSubjects = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM subject WHERE is_active = 1"))['count'];

// Tổng số lớp học
$totalClasses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM class"))['count'];

// Sinh viên mới tháng này
$currentMonth = date('Y-m');
$newStudentsThisMonth = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM students WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'"))['count'];

// Phân bố sinh viên theo trạng thái
$statusQuery = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM students GROUP BY status");
$statusData = [];
while ($row = mysqli_fetch_assoc($statusQuery)) {
    $statusData[] = $row;
}

// Phân bố sinh viên theo lớp (top 5)
$classQuery = mysqli_query($conn, "SELECT c.class_name, COUNT(s.id) as count FROM class c LEFT JOIN students s ON c.id = s.class_id GROUP BY c.id ORDER BY count DESC LIMIT 5");
$classData = [];
while ($row = mysqli_fetch_assoc($classQuery)) {
    $classData[] = $row;
}

// Hoạt động gần đây
$recentActivities = mysqli_query($conn, "SELECT student_name, created_at FROM students ORDER BY created_at DESC LIMIT 5");

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang tổng quan - Quản trị viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../css/admin_dashboard.css?v=2.0" rel="stylesheet">
    <style>
        /* Button Override - Tối giản */
        .btn, .btn-primary {
            padding: 0.625rem 1.25rem !important;
            border-radius: 6px !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            text-decoration: none !important;
        }
        
        .btn-primary {
            background: var(--primary) !important;
            color: var(--white) !important;
            border: 1px solid var(--primary) !important;
        }
        
        .btn-primary:hover {
            background: var(--primary-hover) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.25) !important;
            color: var(--white) !important;
        }
    </style>
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
                        <img src="../images/aiotlab_logo.png" alt="Admin">
                        <div class="user-info">
                            <span class="user-name">Admin</span>
                            <span class="user-email">admin@university.edu</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <div>
                        <h1 class="page-title">Trang tổng quan</h1>
                        <p class="page-subtitle">Chào mừng trở lại, Quản trị viên!</p>
                    </div>
                    <a href="student/create_student.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm sinh viên mới
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-content">
                            <p class="stat-label">Tổng số sinh viên</p>
                            <h3 class="stat-value"><?= $totalStudents ?></h3>
                            <span class="stat-change positive"><i class="fas fa-arrow-up"></i> <?= $newStudentsThisMonth ?> sinh viên mới tháng này</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-content">
                            <p class="stat-label">Môn học đang hoạt động</p>
                            <h3 class="stat-value"><?= $totalSubjects ?></h3>
                            <span class="stat-change positive"><i class="fas fa-check-circle"></i> Đang hoạt động</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <p class="stat-label">Tổng số lớp học</p>
                            <h3 class="stat-value"><?= $totalClasses ?></h3>
                            <span class="stat-change"><i class="fas fa-info-circle"></i> Đang quản lý</span>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="charts-section">
                    <div class="chart-card large">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-bar me-2"></i>Phân bố sinh viên theo lớp</h3>
                            <div class="chart-legend">
                                <span class="legend-total"><?= $totalStudents ?> sinh viên</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="studentDistributionChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="card-header">
                            <h3><i class="fas fa-clock me-2"></i>Hoạt động gần đây</h3>
                        </div>
                        <div class="card-body">
                            <div class="activity-list">
                                <?php if (mysqli_num_rows($recentActivities) > 0): ?>
                                    <?php while ($activity = mysqli_fetch_assoc($recentActivities)): ?>
                                        <div class="activity-item">
                                            <div class="activity-icon green">
                                                <i class="fas fa-user-plus"></i>
                                            </div>
                                            <div class="activity-content">
                                                <p class="activity-title">Sinh viên mới: <?= htmlspecialchars($activity['student_name']) ?></p>
                                                <p class="activity-time">
                                                    <i class="far fa-clock"></i> 
                                                    <?php
                                                        $time = strtotime($activity['created_at']);
                                                        $diff = time() - $time;
                                                        if ($diff < 60) echo $diff . ' giây trước';
                                                        elseif ($diff < 3600) echo floor($diff/60) . ' phút trước';
                                                        elseif ($diff < 86400) echo floor($diff/3600) . ' giờ trước';
                                                        else echo floor($diff/86400) . ' ngày trước';
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">Chưa có hoạt động nào</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="quick-stats">
                    <div class="stat-item">
                        <div class="stat-header">
                            <h4><i class="fas fa-chart-pie me-2"></i>Trạng thái sinh viên</h4>
                        </div>
                        <div class="stat-list">
                            <?php foreach ($statusData as $status): ?>
                                <div class="stat-row">
                                    <span class="stat-name"><?= htmlspecialchars($status['status']) ?></span>
                                    <span class="stat-count"><?= $status['count'] ?> sinh viên</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Menu toggle
        document.getElementById('menuToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar')?.classList.toggle('active');
        });

        // Chart data from PHP
        const classData = <?= json_encode($classData) ?>;
        const classLabels = classData.map(item => item.class_name);
        const classValues = classData.map(item => parseInt(item.count));

        // Create chart
        const ctx = document.getElementById('studentDistributionChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: classLabels,
                    datasets: [{
                        label: 'Số sinh viên',
                        data: classValues,
                        backgroundColor: [
                            'rgba(66, 133, 244, 0.8)',
                            'rgba(52, 168, 83, 0.8)',
                            'rgba(251, 188, 4, 0.8)',
                            'rgba(234, 67, 53, 0.8)',
                            'rgba(156, 39, 176, 0.8)'
                        ],
                        borderColor: [
                            'rgb(66, 133, 244)',
                            'rgb(52, 168, 83)',
                            'rgb(251, 188, 4)',
                            'rgb(234, 67, 53)',
                            'rgb(156, 39, 176)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            borderRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
