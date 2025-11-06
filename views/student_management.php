<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý hồ sơ sinh viên - DNU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066cc;
            --primary-hover: #0052a3;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border: #dee2e6;
            --white: #ffffff;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--bg-light);
            min-height: 100vh;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Header Section */
        .page-header-section {
            background: var(--white);
            border-radius: 12px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .page-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-title i {
            color: var(--primary);
            font-size: 2.2rem;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            margin: 0;
        }

        /* Quick Stats Row */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background: var(--white);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .stat-box.success {
            border-left-color: var(--success);
        }

        .stat-box.warning {
            border-left-color: var(--warning);
        }

        .stat-box.info {
            border-left-color: var(--info);
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0.5rem 0;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .stat-box.success .stat-icon {
            color: var(--success);
        }

        .stat-box.warning .stat-icon {
            color: var(--warning);
        }

        .stat-box.info .stat-icon {
            color: var(--info);
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .feature-module {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid var(--border);
        }

        .feature-module:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
            transform: translateY(-4px);
        }

        .module-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
            color: var(--white);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .module-header.success {
            background: linear-gradient(135deg, var(--success) 0%, #218838 100%);
        }

        .module-header.info {
            background: linear-gradient(135deg, var(--info) 0%, #138496 100%);
        }

        .module-header i {
            font-size: 1.5rem;
        }

        .module-header h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .module-body {
            padding: 1.25rem;
        }

        .module-item {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .module-item:last-child {
            margin-bottom: 0;
        }

        .module-item:hover {
            background: var(--bg-light);
            border-color: var(--border);
            text-decoration: none;
            color: var(--primary);
            transform: translateX(5px);
        }

        .module-item i {
            width: 28px;
            text-align: center;
            font-size: 1.1rem;
            margin-right: 0.75rem;
            color: var(--text-muted);
        }

        .module-item:hover i {
            color: var(--primary);
        }

        .module-item span {
            font-weight: 500;
            font-size: 0.95rem;
        }

        /* Reports Section */
        .reports-section {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--primary);
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .report-card {
            background: var(--bg-light);
            border-radius: 10px;
            padding: 1.25rem;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-dark);
        }

        .report-card:hover {
            background: var(--white);
            border-color: var(--primary);
            text-decoration: none;
            color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .report-card i {
            font-size: 1.75rem;
            color: var(--primary);
            margin-bottom: 0.75rem;
            display: block;
        }

        .report-card h4 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 0.5rem 0;
            color: var(--text-dark);
        }

        .report-card p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* Majors List */
        .majors-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .major-item {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary);
        }

        .major-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
            border-left-color: var(--primary-hover);
        }

        .major-item i {
            font-size: 1.5rem;
            color: var(--primary);
            width: 30px;
            text-align: center;
        }

        .major-item .major-name {
            flex: 1;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 1rem;
        }

        .major-item .major-code {
            background: var(--bg-light);
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Dark Mode cho report cards */
        [data-theme="dark"] .report-card {
            background: #2d2d2d !important;
            color: #ffffff !important;
            border-color: #444 !important;
        }

        [data-theme="dark"] .report-card:hover {
            background: #3d3d3d !important;
            border-color: var(--primary) !important;
            color: var(--primary) !important;
        }

        [data-theme="dark"] .report-card h4,
        [data-theme="dark"] .report-card p {
            color: #ffffff !important;
        }

        [data-theme="dark"] .report-card:hover h4,
        [data-theme="dark"] .report-card:hover p {
            color: var(--primary) !important;
        }

        [data-theme="dark"] .report-card i {
            color: #ffffff !important;
        }

        [data-theme="dark"] .report-card:hover i {
            color: var(--primary) !important;
        }

        [data-theme="dark"] .section-title {
            color: #ffffff !important;
        }

        [data-theme="dark"] .section-title i {
            color: var(--primary) !important;
        }

        /* Dark Mode - Tất cả containers */
        [data-theme="dark"] .reports-section {
            background: #2d2d2d !important;
        }

        [data-theme="dark"] .page-header-section,
        [data-theme="dark"] .stat-box,
        [data-theme="dark"] .feature-module {
            background: #2d2d2d !important;
            color: #ffffff !important;
        }

        [data-theme="dark"] .page-title,
        [data-theme="dark"] .page-subtitle,
        [data-theme="dark"] .stat-value,
        [data-theme="dark"] .stat-label,
        [data-theme="dark"] .module-item span {
            color: #ffffff !important;
        }

        [data-theme="dark"] .module-item {
            color: #ffffff !important;
        }

        [data-theme="dark"] .module-item:hover {
            background: #333 !important;
            color: #ffffff !important;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem 0.5rem;
            }

            .page-header-section {
                padding: 1.5rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .page-title-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .quick-stats {
                grid-template-columns: 1fr;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .reports-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include './menu.php'; ?>
    
    <div class="main-container">
        <!-- Header Section -->
        <div class="page-header-section">
            <div class="page-title-row">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-user-graduate"></i>
                        Quản lý hồ sơ sinh viên
                    </h1>
                    <p class="page-subtitle">Hệ thống quản lý thông tin sinh viên - Đại học Đại Nam</p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="stat-box">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-value"><?= getTotalStudents() ?></div>
                <div class="stat-label">Tổng sinh viên</div>
            </div>
            <div class="stat-box success">
                <i class="fas fa-chalkboard-teacher stat-icon"></i>
                <div class="stat-value"><?= getTotalClasses() ?></div>
                <div class="stat-label">Tổng lớp học</div>
            </div>
            <div class="stat-box warning">
                <i class="fas fa-graduation-cap stat-icon"></i>
                <div class="stat-value"><?= getTotalMajors() ?></div>
                <div class="stat-label">Tổng ngành học</div>
            </div>
            <div class="stat-box info">
                <i class="fas fa-certificate stat-icon"></i>
                <div class="stat-value"><?= getGraduatedStudents() ?></div>
                <div class="stat-label">Đã tốt nghiệp</div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="features-grid">
            <!-- Cập nhật hồ sơ -->
            <div class="feature-module">
                <div class="module-header">
                    <i class="fas fa-edit"></i>
                    <h3>Cập nhật hồ sơ</h3>
                </div>
                <div class="module-body">
                    <a href="student/create_student.php" class="module-item">
                        <i class="fas fa-plus-circle"></i>
                        <span>Nhập hồ sơ mới</span>
                    </a>
                    <a href="student.php" class="module-item">
                        <i class="fas fa-edit"></i>
                        <span>Bổ sung hồ sơ</span>
                    </a>
                    <a href="student_classification.php" class="module-item">
                        <i class="fas fa-tags"></i>
                        <span>Phân loại hồ sơ</span>
                    </a>
                </div>
            </div>

            <!-- Tìm kiếm & Xử lý -->
            <div class="feature-module">
                <div class="module-header success">
                    <i class="fas fa-search"></i>
                    <h3>Tìm kiếm & Xử lý</h3>
                </div>
                <div class="module-body">
                    <a href="search_by_class.php" class="module-item">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Tìm kiếm theo lớp</span>
                    </a>
                    <a href="search_by_major.php" class="module-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Tìm kiếm theo ngành</span>
                    </a>
                </div>
            </div>

            <!-- Thống kê & Báo cáo -->
            <div class="feature-module">
                <div class="module-header info">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Thống kê & Báo cáo</h3>
                </div>
                <div class="module-body">
                    <a href="report_by_class.php" class="module-item">
                        <i class="fas fa-list"></i>
                        <span>Danh sách theo lớp</span>
                    </a>
                    <a href="report_graduation.php" class="module-item">
                        <i class="fas fa-certificate"></i>
                        <span>Hồ sơ tốt nghiệp</span>
                    </a>
                    <a href="statistics.php" class="module-item">
                        <i class="fas fa-chart-pie"></i>
                        <span>Thống kê tổng quan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Majors Section -->
        <div class="reports-section">
            <h2 class="section-title">
                <i class="fas fa-graduation-cap"></i>
                Danh sách ngành học
            </h2>
            <div class="majors-list">
                <?php
                require_once __DIR__ . '/../functions/major_functions.php';
                $majors = getAllMajorsList();
                
                if (empty($majors)) {
                    echo '<div class="text-center py-4 text-muted">
                        <i class="fas fa-graduation-cap fa-2x mb-3"></i>
                        <p>Chưa có ngành học nào</p>
                    </div>';
                } else {
                    foreach ($majors as $major) {
                        echo '<div class="major-item">
                            <i class="fas fa-graduation-cap"></i>
                            <span class="major-name">' . htmlspecialchars($major['major_name']) . '</span>
                            <span class="major-code">' . htmlspecialchars($major['major_code']) . '</span>
                        </div>';
                    }
                }
                ?>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="reports-section">
            <h2 class="section-title">
                <i class="fas fa-file-alt"></i>
                Báo cáo chi tiết
            </h2>
            <div class="reports-grid">
                <a href="reports/student_statistics.php" class="report-card">
                    <i class="fas fa-user-chart"></i>
                    <h4>Thống kê sinh viên</h4>
                    <p>Xem các báo cáo và thống kê chi tiết về sinh viên</p>
                </a>
                <a href="reports/grade_report.php" class="report-card">
                    <i class="fas fa-file-chart-line"></i>
                    <h4>Báo cáo điểm</h4>
                    <p>Xem báo cáo điểm số và kết quả học tập</p>
                </a>
                <a href="statistics.php" class="report-card">
                    <i class="fas fa-chart-pie"></i>
                    <h4>Thống kê tổng quan</h4>
                    <p>Xem tổng quan thống kê toàn hệ thống</p>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Counter animation cho số liệu thống kê
        document.addEventListener('DOMContentLoaded', function() {
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach(stat => {
                const target = parseInt(stat.textContent);
                if (isNaN(target)) return;
                
                let current = 0;
                const increment = target / 40;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current);
                    }
                }, 50);
            });
        });
    </script>
</body>

</html>

<?php
// Các hàm thống kê đơn giản
function getTotalStudents() {
    require_once __DIR__ . '/../functions/student_functions.php';
    $students = getAllStudents();
    return count($students);
}

function getTotalClasses() {
    require_once __DIR__ . '/../functions/student_functions.php';
    $classes = getAllClasses();
    return count($classes);
}

function getTotalMajors() {
    require_once __DIR__ . '/../functions/student_functions.php';
    $majors = getAllMajors();
    return count($majors);
}

function getGraduatedStudents() {
    require_once __DIR__ . '/../functions/db_connection.php';
    $conn = getDbConnection();
    $sql = "SELECT COUNT(*) as count FROM students WHERE status = 'Tốt nghiệp'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $row['count'] ?? 0;
}
?>
