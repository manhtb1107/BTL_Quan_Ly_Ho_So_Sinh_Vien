<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
require_once __DIR__ . '/../functions/class_functions.php';
$currentUser = getCurrentUser();
$currentPath = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Lớp học - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../css/admin_dashboard.css" rel="stylesheet">
    <link href="../css/class_management.css" rel="stylesheet">
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
                    <h2 class="page-title mb-0">Quản lý Lớp học</h2>
                </div>
                
                <div class="header-right">
                    <button class="btn-icon" title="Thông báo">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </button>
                    
                    <div class="user-profile">
                        <img src="../images/aiotlab_logo.png" alt="Admin">
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($currentUser['username'] ?? 'Admin') ?></span>
                            <span class="user-email">Đại học Công nghệ</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content">
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
                            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên hoặc mã lớp...">
                        </div>
                        <select class="filter-select" id="majorFilter">
                            <option value="">Ngành học</option>
                            <option value="cntt">Công nghệ thông tin</option>
                            <option value="kt">Kinh tế</option>
                        </select>
                        <select class="filter-select" id="yearFilter">
                            <option value="">Khóa học</option>
                            <option value="2021-2025">2021-2025</option>
                            <option value="2022-2026">2022-2026</option>
                        </select>
                        <button class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
                            <i class="fas fa-trash me-2"></i>Xóa đã chọn (<span id="selectedCount">0</span>)
                        </button>
                        <a href="class/create_class.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm Lớp học mới
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
                                    <th>Mã lớp</th>
                                    <th>Tên lớp</th>
                                    <th>Ngành học</th>
                                    <th>Khóa học</th>
                                    <th>Sĩ số</th>
                                    <th class="text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="classTableBody">
                                <?php
                                $classes = getAllClass();
                                if (!$classes || count($classes) === 0):
                                ?>
                                    <tr>
                                        <td colspan="7" class="no-data">
                                            <i class="fas fa-chalkboard"></i>
                                            <h5>Không có lớp học nào</h5>
                                            <p class="mb-0">Thêm lớp học mới để bắt đầu quản lý</p>
                                        </td>
                                    </tr>
                                <?php else:
                                    foreach ($classes as $class):
                                ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input class-checkbox" value="<?= $class['id'] ?>">
                                        </td>
                                        <td>
                                            <span class="class-code-badge">
                                                <?= htmlspecialchars($class['class_code'] ?? '') ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($class['class_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($class['major'] ?? 'Chưa xác định') ?></td>
                                        <td><?= htmlspecialchars($class['academic_year'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($class['student_count'] ?? '0') ?></td>
                                        <td class="text-end">
                                            <a href="class/edit_class.php?id=<?= htmlspecialchars($class['id'] ?? '') ?>" 
                                               class="action-btn edit" 
                                               title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deleteClass(<?= htmlspecialchars($class['id'] ?? '') ?>, '<?= htmlspecialchars($class['class_name'] ?? '') ?>')" 
                                                    class="action-btn delete" 
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($classes && count($classes) > 0): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị 1 đến <?= count($classes) ?> trên 20 kết quả
                        </div>
                        <div class="pagination-buttons">
                            <button class="pagination-btn" disabled>Trang trước</button>
                            <button class="pagination-btn">Trang sau</button>
                        </div>
                    </div>
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

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('classTableBody');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');
                
                Array.from(rows).forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Delete function
        function deleteClass(id, name) {
            if (confirm(`Bạn có chắc muốn xóa lớp "${name}"?`)) {
                window.location.href = `../handle/class_process.php?action=delete&id=${id}`;
            }
        }

        // Bulk delete functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const classCheckboxes = document.querySelectorAll('.class-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCountSpan = document.getElementById('selectedCount');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                classCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
        }

        classCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkDeleteButton);
        });

        function updateBulkDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.class-checkbox:checked');
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
                const checkedBoxes = document.querySelectorAll('.class-checkbox:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);
                
                if (ids.length === 0) return;
                
                if (confirm(`Bạn có chắc muốn xóa ${ids.length} lớp học đã chọn?\n\nHành động này không thể hoàn tác!`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../handle/class_process.php';
                    
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
