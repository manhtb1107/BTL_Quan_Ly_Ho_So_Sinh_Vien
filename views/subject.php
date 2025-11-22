<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
require_once __DIR__ . '/../functions/subject_functions.php';
require_once __DIR__ . '/../functions/student_functions.php';
$currentUser = getCurrentUser();

// Lấy danh sách ngành học để filter
$majors = getAllMajors();

// Lọc theo major nếu có
if (isset($_GET['major_id']) && !empty($_GET['major_id'])) {
    $subjects = getSubjectsByMajor($_GET['major_id']);
} else {
    $subjects = getAllSubjects();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Môn học - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../css/admin_dashboard.css" rel="stylesheet">
    <link href="../css/subject_management.css" rel="stylesheet">
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
                        <a href="#">Quản lý dữ liệu</a>
                        <span>/</span>
                        <span class="current">Quản lý Môn học</span>
                    </div>
                </div>
                
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../images/aiotlab_logo.png" alt="Admin">
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($currentUser['username'] ?? 'Admin') ?></span>
                            <span class="user-email">admin@university.edu.vn</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content">
                <!-- Page Header -->
                <div class="welcome-section mb-3">
                    <h1 class="page-title">Quản lý Môn học</h1>
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
                            <input type="text" id="searchInput" placeholder="Tìm theo tên hoặc mã môn học...">
                        </div>
                        <select class="filter-select" id="majorFilter" onchange="filterByMajor()">
                            <option value="">Tất cả ngành</option>
                            <?php foreach ($majors as $major): ?>
                                <option value="<?= $major['id'] ?>" <?= (isset($_GET['major_id']) && $_GET['major_id'] == $major['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($major['major_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select class="filter-select" id="semesterFilter">
                            <option value="">Học kỳ</option>
                            <option value="1">Học kỳ 1</option>
                            <option value="2">Học kỳ 2</option>
                        </select>
                        <button class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
                            <i class="fas fa-trash me-2"></i>Xóa đã chọn (<span id="selectedCount">0</span>)
                        </button>
                        <a href="subject/create_subject.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm môn học mới
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
                                    <th>Mã môn học</th>
                                    <th>Tên môn học</th>
                                    <th>Số tín chỉ</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($subjects)): ?>
                                    <tr>
                                        <td colspan="5" class="no-data">
                                            <i class="fas fa-book-open"></i>
                                            <h5>Không có môn học nào</h5>
                                            <p class="mb-0">Thêm môn học mới để bắt đầu quản lý</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($subjects as $subject): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input subject-checkbox" value="<?= $subject['id'] ?>">
                                            </td>
                                            <td>
                                                <span class="subject-code-badge">
                                                    <?= htmlspecialchars($subject['subject_code']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                                            <td><?= htmlspecialchars($subject['credits'] ?? '3') ?></td>
                                            <td>
                                                <button class="action-btn view" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="subject/edit_subject.php?id=<?= $subject['id'] ?>" 
                                                   class="action-btn edit" 
                                                   title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $subject['id'] ?>, '<?= htmlspecialchars($subject['subject_name']) ?>')" 
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

                    <?php if (!empty($subjects)): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị 1-10 trên 1000
                        </div>
                        <div class="pagination-buttons">
                            <button class="pagination-btn" disabled>Trước</button>
                            <button class="pagination-btn active">1</button>
                            <button class="pagination-btn">2</button>
                            <button class="pagination-btn">...</button>
                            <button class="pagination-btn">Sau</button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/subject_management.js"></script>
    <script>
        // Filter by major
        function filterByMajor() {
            const majorId = document.getElementById('majorFilter').value;
            if (majorId) {
                window.location.href = 'subject.php?major_id=' + majorId;
            } else {
                window.location.href = 'subject.php';
            }
        }
        
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.data-table tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Bulk delete functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const subjectCheckboxes = document.querySelectorAll('.subject-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCountSpan = document.getElementById('selectedCount');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                subjectCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
        }

        subjectCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkDeleteButton);
        });

        function updateBulkDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.subject-checkbox:checked');
            const count = checkedBoxes.length;
            
            if (count > 0) {
                bulkDeleteBtn.style.display = 'inline-block';
                selectedCountSpan.textContent = count;
            } else {
                bulkDeleteBtn.style.display = 'none';
                selectAllCheckbox.checked = false;
            }
        }

        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.subject-checkbox:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);
                
                if (ids.length === 0) return;
                
                if (confirm(`Bạn có chắc muốn xóa ${ids.length} môn học đã chọn?\n\nHành động này không thể hoàn tác!`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../handle/subject_process.php';
                    
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
