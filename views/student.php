<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
require_once __DIR__ . '/../functions/student_functions.php';
$currentUser = getCurrentUser();

// Get students data
if (isset($_GET['search']) && !empty($_GET['search'])) {
    require_once __DIR__ . '/../handle/student_process.php';
    $students = searchStudents($_GET['search']);
    $searchTerm = $_GET['search'];
} else {
    $students = getAllStudents();
    $searchTerm = '';
}

// Get majors for filter
$majors = getAllMajors();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sinh viên - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../css/admin_dashboard.css?v=2.0" rel="stylesheet">
    <link href="../css/student_management.css?v=2.0" rel="stylesheet">
    <style>
        /* Button Override - Tối giản */
        .btn, .btn-apply-filter, .btn-clear-filter {
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
        
        .btn-apply-filter {
            background: var(--primary) !important;
            color: var(--white) !important;
            border: 1px solid var(--primary) !important;
        }
        
        .btn-apply-filter:hover {
            background: var(--primary-hover) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.25) !important;
        }
        
        .btn-clear-filter {
            background: var(--white) !important;
            color: var(--text-secondary) !important;
            border: 1px solid var(--border) !important;
        }
        
        .btn-clear-filter:hover {
            background: var(--bg-light) !important;
            color: var(--text-primary) !important;
            border-color: var(--text-secondary) !important;
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
        
        .btn-success {
            background: var(--success) !important;
            color: var(--white) !important;
            border: 1px solid var(--success) !important;
        }
        
        .btn-success:hover {
            background: #2d9348 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(52, 168, 83, 0.25) !important;
            color: var(--white) !important;
        }
        
        .btn-danger {
            background: var(--danger) !important;
            color: var(--white) !important;
            border: 1px solid var(--danger) !important;
        }
        
        .btn-danger:hover {
            background: #d93025 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(234, 67, 53, 0.25) !important;
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
                    <h2 class="page-title mb-0">Quản lý Sinh viên</h2>
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
                    <p class="page-subtitle">Xem, tìm kiếm, và quản lý hồ sơ sinh viên trong hệ thống.</p>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($_GET['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-row">
                        <div class="search-input-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Tìm kiếm theo Tên hoặc Mã" value="<?= htmlspecialchars($searchTerm) ?>">
                        </div>
                        <select class="filter-select" id="majorFilter">
                            <option value="">Ngành học</option>
                            <?php foreach ($majors as $major): ?>
                                <option value="<?= htmlspecialchars($major['major_name']) ?>">
                                    <?= htmlspecialchars($major['major_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select class="filter-select" id="statusFilter">
                            <option value="">Trạng thái</option>
                            <option value="Đang học">Đang học</option>
                            <option value="Tốt nghiệp">Tốt nghiệp</option>
                            <option value="Bảo lưu">Bảo lưu</option>
                        </select>
                        <div class="filter-actions">
                            <button class="btn-apply-filter" id="applyFilter">Áp dụng</button>
                            <button class="btn-clear-filter" id="clearFilter">Xóa bộ lọc</button>
                        </div>
                        <button class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
                            <i class="fas fa-trash me-2"></i>Xóa đã chọn (<span id="selectedCount">0</span>)
                        </button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-import me-2"></i>Import từ CSV
                        </button>
                        <a href="student/create_student.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm mới sinh viên
                        </a>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="table-card">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Mã SV</th>
                                    <th>Họ và tên</th>
                                    <th>Ngày sinh</th>
                                    <th>Ngành học</th>
                                    <th>Niên khóa</th>
                                    <th>Trạng thái</th>
                                    <th>Số điện thoại</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($students)): ?>
                                    <tr>
                                        <td colspan="9" class="no-data">
                                            <i class="fas fa-user-slash"></i>
                                            <h5>Không có sinh viên nào</h5>
                                            <p class="mb-0">Thêm sinh viên mới để bắt đầu quản lý</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input student-checkbox" value="<?= $student['id'] ?>">
                                            </td>
                                            <td>
                                                <span class="student-code-badge">
                                                    <?= htmlspecialchars($student['student_code']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="student-name-cell">
                                                    <?= htmlspecialchars($student['full_name'] ?? $student['student_name']) ?>
                                                </div>
                                                <div class="student-id-text">ID: <?= $student['id'] ?></div>
                                            </td>
                                            <td>
                                                <?= $student['date_of_birth'] ? date('d/m/Y', strtotime($student['date_of_birth'])) : '-' ?>
                                            </td>
                                            <td><?= htmlspecialchars($student['major_name'] ?? '-') ?></td>
                                            <td>
                                                <span class="academic-year-badge">
                                                    <?= htmlspecialchars($student['academic_year'] ?? '-') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $status = $student['status'] ?? 'Đang học';
                                                $statusClass = '';
                                                $statusIcon = '';
                                                switch ($status) {
                                                    case 'Đang học':
                                                        $statusClass = 'active';
                                                        $statusIcon = 'fa-graduation-cap';
                                                        break;
                                                    case 'Tốt nghiệp':
                                                        $statusClass = 'graduated';
                                                        $statusIcon = 'fa-user-graduate';
                                                        break;
                                                    case 'Bảo lưu':
                                                        $statusClass = 'suspended';
                                                        $statusIcon = 'fa-pause-circle';
                                                        break;
                                                    case 'Tạm nghỉ':
                                                        $statusClass = 'suspended';
                                                        $statusIcon = 'fa-clock';
                                                        break;
                                                    case 'Bị đuổi học':
                                                        $statusClass = 'expelled';
                                                        $statusIcon = 'fa-ban';
                                                        break;
                                                    default:
                                                        $statusClass = 'active';
                                                        $statusIcon = 'fa-graduation-cap';
                                                }
                                                ?>
                                                <span class="status-badge <?= $statusClass ?>">
                                                    <i class="fas <?= $statusIcon ?>"></i>
                                                    <?= htmlspecialchars($status) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($student['phone'] ?? '-') ?></td>
                                            <td>
                                                <a href="student_profile.php?id=<?= $student['id'] ?>" 
                                                   class="action-btn view" 
                                                   title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="student/edit_student.php?id=<?= $student['id'] ?>" 
                                                   class="action-btn edit" 
                                                   title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $student['id'] ?>, '<?= htmlspecialchars($student['full_name'] ?? $student['student_name']) ?>')" 
                                                        class="action-btn delete" 
                                                        title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($students)): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị 1-10 trên 150 hồ sơ
                        </div>
                        <div class="pagination-buttons">
                            <button class="pagination-btn" disabled>Trang trước</button>
                            <button class="pagination-btn active">1</button>
                            <button class="pagination-btn">2</button>
                            <button class="pagination-btn">3</button>
                            <button class="pagination-btn">...</button>
                            <button class="pagination-btn">15</button>
                            <button class="pagination-btn">Trang sau</button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="fas fa-file-import me-2"></i>Import Sinh viên từ File CSV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../handle/student_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="import_excel">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Hướng dẫn:</strong>
                            <ul class="mb-0 mt-2">
                                <li>File CSV phải có dòng đầu tiên là tên các cột</li>
                                <li>Các cột có thể có: <code>student_code</code>, <code>full_name</code>, <code>date_of_birth</code>, <code>gender</code>, <code>phone</code>, <code>email</code>, <code>address</code>, <code>hometown</code>, <code>id_card</code>, <code>class_code</code>, <code>major_code</code>, <code>academic_year</code>, <code>status</code>, <code>enrollment_date</code></li>
                                <li>Cột bắt buộc: <code>student_code</code></li>
                                <li>Các cột khác nếu để trống sẽ được bỏ qua</li>
                                <li>Định dạng ngày: DD/MM/YYYY hoặc YYYY-MM-DD</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="excel_file" class="form-label">
                                <i class="fas fa-file-csv me-2"></i>Chọn file CSV
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="excel_file" 
                                   name="excel_file" 
                                   accept=".csv,.xlsx,.xls"
                                   required>
                            <div class="form-text">
                                Hỗ trợ file: .csv, .xlsx, .xls (tối đa 5MB)
                            </div>
                        </div>

                        <div class="mb-3">
                            <a href="../docs/student_import_example.csv" 
                               class="btn btn-outline-primary btn-sm me-2" 
                               download>
                                <i class="fas fa-download me-2"></i>Tải file mẫu CSV
                            </a>
                            <a href="../docs/HUONG_DAN_IMPORT_SINH_VIEN.md" 
                               class="btn btn-outline-info btn-sm" 
                               target="_blank">
                                <i class="fas fa-book me-2"></i>Xem hướng dẫn chi tiết
                            </a>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Lưu ý:</strong> Quá trình import có thể mất vài phút tùy thuộc vào số lượng sinh viên.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Hủy
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload me-2"></i>Bắt đầu Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/student_management.js"></script>
    <script>
        // Bulk delete functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCountSpan = document.getElementById('selectedCount');

        // Select/Deselect all
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                studentCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
        }

        // Update button when individual checkbox changes
        studentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkDeleteButton);
        });

        function updateBulkDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
            const count = checkedBoxes.length;
            
            if (count > 0) {
                bulkDeleteBtn.style.display = 'inline-block';
                selectedCountSpan.textContent = count;
            } else {
                bulkDeleteBtn.style.display = 'none';
                selectAllCheckbox.checked = false;
            }
        }

        // Bulk delete action
        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);
                
                if (ids.length === 0) return;
                
                if (confirm(`Bạn có chắc muốn xóa ${ids.length} sinh viên đã chọn?\n\nHành động này không thể hoàn tác!`)) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../handle/student_process.php';
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'bulk_delete';
                    form.appendChild(actionInput);
                    
                    ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
