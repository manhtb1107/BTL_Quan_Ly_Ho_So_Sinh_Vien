<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
require_once __DIR__ . '/../functions/grade_functions.php';
require_once __DIR__ . '/../functions/db_connection.php';
$currentUser = getCurrentUser();

$conn = getDbConnection();

// Lấy danh sách học kỳ
$semesters = mysqli_query($conn, "SELECT DISTINCT semester FROM grade WHERE semester IS NOT NULL ORDER BY semester");

// Lấy danh sách năm học
$academicYears = mysqli_query($conn, "SELECT DISTINCT academic_year FROM grade WHERE academic_year IS NOT NULL ORDER BY academic_year DESC");

// Lấy danh sách ngành
$majors = mysqli_query($conn, "SELECT id, major_name FROM majors ORDER BY major_name");

// Lấy danh sách môn học
$subjects = mysqli_query($conn, "SELECT id, subject_code, subject_name, credits FROM subject WHERE is_active = 1 ORDER BY subject_name");

// Xử lý khi có filter
$selectedSubject = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$selectedSemester = isset($_GET['semester']) ? $_GET['semester'] : '';
$selectedYear = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';
$selectedMajor = isset($_GET['major_id']) ? (int)$_GET['major_id'] : 0;

$students = [];
$subjectInfo = null;

if ($selectedSubject > 0) {
    // Lấy thông tin môn học
    $subjectQuery = mysqli_query($conn, "SELECT * FROM subject WHERE id = $selectedSubject");
    $subjectInfo = mysqli_fetch_assoc($subjectQuery);
    
    // Lấy danh sách sinh viên theo filter
    $sql = "SELECT s.id, s.student_code, s.student_name, s.class_id,
                   g.id as grade_id, g.test1_score, g.test2_score, g.attendance_score, 
                   g.final_score, g.total_score
            FROM students s
            LEFT JOIN grade g ON s.id = g.student_id AND g.subject_id = $selectedSubject";
    
    $conditions = ["s.status = 'Đang học'"];
    
    if ($selectedMajor > 0) {
        $conditions[] = "s.major = $selectedMajor";
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $sql .= " ORDER BY s.student_code";
    
    $studentsResult = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($studentsResult)) {
        $students[] = $row;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Điểm - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../css/admin_dashboard.css?v=2.0" rel="stylesheet">
    <link href="../css/grade_management.css?v=2.0" rel="stylesheet">
    <style>
        /* Button Override - Tối giản */
        .btn-filter, .btn-reset, .btn-export, .btn-save-changes {
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
        
        .btn-filter {
            background: var(--primary) !important;
            color: var(--white) !important;
            border: 1px solid var(--primary) !important;
        }
        
        .btn-filter:hover {
            background: var(--primary-hover) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.25) !important;
        }
        
        .btn-reset {
            background: var(--white) !important;
            color: var(--text-secondary) !important;
            border: 1px solid var(--border) !important;
        }
        
        .btn-reset:hover {
            background: var(--bg-light) !important;
            color: var(--text-primary) !important;
            border-color: var(--text-secondary) !important;
        }
        
        .btn-export {
            background: var(--white) !important;
            color: var(--success) !important;
            border: 1px solid var(--success) !important;
        }
        
        .btn-export:hover {
            background: var(--success) !important;
            color: var(--white) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(52, 168, 83, 0.25) !important;
        }
        
        .btn-save-changes {
            background: var(--primary) !important;
            color: var(--white) !important;
            border: 1px solid var(--primary) !important;
        }
        
        .btn-save-changes:hover {
            background: var(--primary-hover) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.25) !important;
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
                    <div class="breadcrumb-nav">
                        <a href="admin_dashboard.php">Trang tổng quan</a>
                        <span>/</span>
                        <span class="current">Quản lý Điểm</span>
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
                <div class="welcome-section mb-3">
                    <h1 class="page-title">Quản lý Điểm</h1>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($_GET['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter Section -->
                <div class="grade-filter-section">
                    <h3><i class="fas fa-filter me-2"></i>Chọn Lớp học phần</h3>
                    <form method="GET" action="" id="filterForm">
                        <div class="filter-grid">
                            <div class="filter-group">
                                <label>Học kỳ</label>
                                <select name="semester" id="semesterFilter">
                                    <option value="">-- Chọn học kỳ --</option>
                                    <option value="1" <?= $selectedSemester == '1' ? 'selected' : '' ?>>Học kỳ 1</option>
                                    <option value="2" <?= $selectedSemester == '2' ? 'selected' : '' ?>>Học kỳ 2</option>
                                    <option value="3" <?= $selectedSemester == '3' ? 'selected' : '' ?>>Học kỳ 3</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Niên học</label>
                                <select name="academic_year" id="yearFilter">
                                    <option value="">-- Chọn niên học --</option>
                                    <option value="2023-2024" <?= $selectedYear == '2023-2024' ? 'selected' : '' ?>>2023 - 2024</option>
                                    <option value="2024-2025" <?= $selectedYear == '2024-2025' ? 'selected' : '' ?>>2024 - 2025</option>
                                    <option value="2025-2026" <?= $selectedYear == '2025-2026' ? 'selected' : '' ?>>2025 - 2026</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Ngành</label>
                                <select name="major_id" id="majorFilter">
                                    <option value="">-- Tất cả ngành --</option>
                                    <?php 
                                    mysqli_data_seek($majors, 0);
                                    while ($major = mysqli_fetch_assoc($majors)): 
                                    ?>
                                        <option value="<?= $major['id'] ?>" <?= $selectedMajor == $major['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($major['major_name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Môn học</label>
                                <select name="subject_id" id="subjectFilter" required>
                                    <option value="">-- Chọn môn học --</option>
                                    <?php 
                                    mysqli_data_seek($subjects, 0);
                                    while ($subject = mysqli_fetch_assoc($subjects)): 
                                    ?>
                                        <option value="<?= $subject['id'] ?>" <?= $selectedSubject == $subject['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($subject['subject_code']) ?> - <?= htmlspecialchars($subject['subject_name']) ?> (<?= $subject['credits'] ?> TC)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn-filter">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                            <a href="grade.php" class="btn-reset">
                                <i class="fas fa-redo me-2"></i>Đặt lại
                            </a>
                        </div>
                    </form>
                </div>

                <?php if ($subjectInfo): ?>
                <!-- Table Header -->
                <div class="grade-table-header">
                    <div>
                        <h3 class="grade-table-title">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Bảng điểm: <?= htmlspecialchars($subjectInfo['subject_code']) ?> - <?= htmlspecialchars($subjectInfo['subject_name']) ?>
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0.5rem 0 0 0;">
                            <i class="fas fa-calculator me-1"></i>
                            Công thức: ((Kiểm tra 1 + Kiểm tra 2) / 2) × 30% + Chuyên cần × 10% + Cuối kỳ × 60%
                        </p>
                    </div>
                    <div class="search-grade-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchStudent" placeholder="Tìm kiếm sinh viên...">
                    </div>
                </div>
                <?php endif; ?>

                <!-- Grade Table -->
                <div class="grade-table-card">
                    <?php if (empty($students) && $selectedSubject == 0): ?>
                        <div class="no-data-message">
                            <i class="fas fa-filter fa-3x mb-3"></i>
                            <h4>Vui lòng chọn môn học</h4>
                            <p>Chọn môn học từ bộ lọc bên trên để hiển thị danh sách sinh viên và nhập điểm</p>
                        </div>
                    <?php elseif (empty($students) && $selectedSubject > 0): ?>
                        <div class="no-data-message">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <h4>Không tìm thấy sinh viên</h4>
                            <p>Không có sinh viên nào phù hợp với bộ lọc đã chọn</p>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="../handle/grade_process.php?action=batch_update" id="gradeForm">
                            <input type="hidden" name="subject_id" value="<?= $selectedSubject ?>">
                            <input type="hidden" name="semester" value="<?= htmlspecialchars($selectedSemester) ?>">
                            <input type="hidden" name="academic_year" value="<?= htmlspecialchars($selectedYear) ?>">
                            
                            <div class="table-responsive">
                                <table class="grade-data-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">STT</th>
                                            <th style="width: 100px;">Mã SV</th>
                                            <th>Họ và tên</th>
                                            <th style="width: 100px;">Kiểm tra 1</th>
                                            <th style="width: 100px;">Kiểm tra 2</th>
                                            <th style="width: 100px;">Chuyên cần</th>
                                            <th style="width: 100px;">Cuối kỳ</th>
                                            <th style="width: 100px;">Tổng kết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $index => $student): ?>
                                            <tr data-student-id="<?= $student['id'] ?>">
                                                <td><?= $index + 1 ?></td>
                                                <td><span class="student-id-badge"><?= htmlspecialchars($student['student_code']) ?></span></td>
                                                <td><?= htmlspecialchars($student['student_name']) ?></td>
                                                <td>
                                                    <input type="hidden" name="students[<?= $student['id'] ?>][student_id]" value="<?= $student['id'] ?>">
                                                    <input type="hidden" name="students[<?= $student['id'] ?>][grade_id]" value="<?= $student['grade_id'] ?? '' ?>">
                                                    <input type="number" 
                                                           class="grade-input" 
                                                           name="students[<?= $student['id'] ?>][test1_score]"
                                                           data-type="test1" 
                                                           value="<?= $student['test1_score'] ?? '' ?>" 
                                                           min="0" 
                                                           max="10" 
                                                           step="0.1"
                                                           placeholder="-">
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           class="grade-input" 
                                                           name="students[<?= $student['id'] ?>][test2_score]"
                                                           data-type="test2" 
                                                           value="<?= $student['test2_score'] ?? '' ?>" 
                                                           min="0" 
                                                           max="10" 
                                                           step="0.1"
                                                           placeholder="-">
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           class="grade-input" 
                                                           name="students[<?= $student['id'] ?>][attendance_score]"
                                                           data-type="attendance" 
                                                           value="<?= $student['attendance_score'] ?? '' ?>" 
                                                           min="0" 
                                                           max="10" 
                                                           step="0.1"
                                                           placeholder="-">
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           class="grade-input" 
                                                           name="students[<?= $student['id'] ?>][final_score]"
                                                           data-type="final" 
                                                           value="<?= $student['final_score'] ?? '' ?>" 
                                                           min="0" 
                                                           max="10" 
                                                           step="0.1"
                                                           placeholder="-">
                                                </td>
                                                <td>
                                                    <span class="grade-total"><?= $student['total_score'] ? number_format($student['total_score'], 1) : '-' ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="grade-actions">
                                <div class="grade-stats">
                                    <span><i class="fas fa-users me-1"></i> Tổng: <strong><?= count($students) ?></strong> sinh viên</span>
                                </div>
                                <div>
                                    <button type="button" class="btn-export">
                                        <i class="fas fa-file-excel"></i>
                                        Xuất Excel
                                    </button>
                                    <button type="submit" class="btn-save-changes">
                                        <i class="fas fa-save"></i>
                                        Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
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

        // Auto-hide alerts
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }
        }, 3000);

        // Search student
        const searchStudent = document.getElementById('searchStudent');
        if (searchStudent) {
            searchStudent.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.grade-data-table tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Calculate total grade with formula: ((Test1 + Test2) / 2) × 30% + Attendance × 10% + Final × 60%
        function calculateGrade(row) {
            const test1Input = row.querySelector('[data-type="test1"]');
            const test2Input = row.querySelector('[data-type="test2"]');
            const attendanceInput = row.querySelector('[data-type="attendance"]');
            const finalInput = row.querySelector('[data-type="final"]');
            const totalCell = row.querySelector('.grade-total');
            
            const test1 = parseFloat(test1Input?.value) || 0;
            const test2 = parseFloat(test2Input?.value) || 0;
            const attendance = parseFloat(attendanceInput?.value) || 0;
            const final = parseFloat(finalInput?.value) || 0;
            
            // Check if all grades are entered
            const hasTest1 = test1Input?.value && !isNaN(parseFloat(test1Input.value));
            const hasTest2 = test2Input?.value && !isNaN(parseFloat(test2Input.value));
            const hasAttendance = attendanceInput?.value && !isNaN(parseFloat(attendanceInput.value));
            const hasFinal = finalInput?.value && !isNaN(parseFloat(finalInput.value));
            
            if (hasTest1 && hasTest2 && hasAttendance && hasFinal) {
                // Formula: ((Test1 + Test2) / 2) × 30% + Attendance × 10% + Final × 60%
                const total = (((test1 + test2) / 2) * 0.3) + (attendance * 0.1) + (final * 0.6);
                totalCell.textContent = total.toFixed(1);
                totalCell.style.color = total >= 5 ? 'var(--success)' : 'var(--danger)';
                totalCell.style.fontWeight = '700';
            } else {
                totalCell.textContent = '-';
                totalCell.style.color = '';
                totalCell.style.fontWeight = '';
            }
        }
        
        // Validate grade input (0-10)
        function validateGradeInput(input) {
            const value = parseFloat(input.value);
            
            if (input.value === '') {
                input.style.borderColor = '';
                return true;
            }
            
            if (isNaN(value) || value < 0 || value > 10) {
                input.style.borderColor = 'var(--danger)';
                input.title = 'Điểm phải từ 0 đến 10';
                return false;
            }
            
            input.style.borderColor = 'var(--success)';
            input.title = '';
            return true;
        }
        
        // Add event listeners to all grade inputs
        document.querySelectorAll('.grade-input').forEach(input => {
            input.addEventListener('input', function() {
                validateGradeInput(this);
                const row = this.closest('tr');
                calculateGrade(row);
            });
            
            input.addEventListener('blur', function() {
                if (this.value !== '') {
                    const value = parseFloat(this.value);
                    if (!isNaN(value)) {
                        // Round to 1 decimal place
                        this.value = Math.max(0, Math.min(10, value)).toFixed(1);
                        validateGradeInput(this);
                        const row = this.closest('tr');
                        calculateGrade(row);
                    }
                }
            });
            
            // Calculate on page load
            const row = input.closest('tr');
            if (row) calculateGrade(row);
        });
        
        // Validate form before submit
        document.getElementById('gradeForm')?.addEventListener('submit', function(e) {
            let hasError = false;
            
            document.querySelectorAll('.grade-input').forEach(input => {
                if (input.value !== '' && !validateGradeInput(input)) {
                    hasError = true;
                }
            });
            
            if (hasError) {
                e.preventDefault();
                alert('Vui lòng kiểm tra lại điểm! Tất cả điểm phải từ 0 đến 10.');
                return false;
            }
        });
    </script>
</body>
</html>
