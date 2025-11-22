<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/major_functions.php';
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();
$majors = getAllMajorsList();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Ngành học - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../css/admin_dashboard.css" rel="stylesheet">
    <link href="../css/class_management.css" rel="stylesheet">
    <link href="../css/form_style.css" rel="stylesheet">
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
                    <h2 class="page-title mb-0">Quản lý Ngành học</h2>
                </div>
                
                <div class="header-right">
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

                <!-- Add Major Form -->
                <div class="form-card mb-4">
                    <div class="form-header">
                        <div class="form-header-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="form-header-content">
                            <h2>Thêm Ngành học mới</h2>
                        </div>
                    </div>

                    <form action="../handle/major_process.php" method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="major_code" class="form-label">
                                        Mã Ngành<span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="major_code" 
                                           name="major_code" 
                                           placeholder="Ví dụ: CNTT"
                                           required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="major_name" class="form-label">
                                        Tên Ngành<span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="major_name" 
                                           name="major_name" 
                                           placeholder="Ví dụ: Công nghệ Thông tin"
                                           required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="department" class="form-label">
                                        Thuộc Khoa
                                    </label>
                                    <select class="form-select" id="department" name="department">
                                        <option value="">Chọn khoa chủ quản</option>
                                        <option value="Khoa Công nghệ Thông tin">Khoa Công nghệ Thông tin</option>
                                        <option value="Khoa Kinh tế">Khoa Kinh tế</option>
                                        <option value="Khoa Ngoại ngữ">Khoa Ngoại ngữ</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                Mô tả (tùy chọn)
                            </label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Nhập mô tả ngắn về ngành học..."></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-plus me-2"></i>Thêm Ngành học
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Major List -->
                <div class="table-card">
                    <div class="table-header-row mb-3">
                        <h3 class="table-title">Danh sách Ngành học</h3>
                        <div class="d-flex gap-2">
                            <button class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
                                <i class="fas fa-trash me-2"></i>Xóa đã chọn (<span id="selectedCount">0</span>)
                            </button>
                            <div class="search-input-wrapper" style="max-width: 300px;">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchInput" placeholder="Tìm kiếm ngành học...">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Mã Ngành</th>
                                    <th>Tên Ngành</th>
                                    <th>Khoa</th>
                                    <th class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="majorTableBody">
                                <?php if (empty($majors)): ?>
                                    <tr>
                                        <td colspan="5" class="no-data">
                                            <i class="fas fa-graduation-cap"></i>
                                            <h5>Chưa có ngành học nào</h5>
                                            <p class="mb-0">Thêm ngành học mới ở form bên trên</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($majors as $major): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input major-checkbox" value="<?= $major['id'] ?>">
                                            </td>
                                            <td>
                                                <span class="class-code-badge">
                                                    <?= htmlspecialchars($major['major_code']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($major['major_name']) ?></td>
                                            <td><?= htmlspecialchars($major['department'] ?? 'Khoa Công nghệ Thông tin') ?></td>
                                            <td class="text-end">
                                                <a href="major/edit_major.php?id=<?= $major['id'] ?>" 
                                                   class="action-btn edit" 
                                                   title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="deleteMajor(<?= $major['id'] ?>, '<?= htmlspecialchars($major['major_name']) ?>')" 
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

                    <?php if (!empty($majors)): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị 1 - 4 của 12 kết quả
                        </div>
                        <div class="pagination-buttons">
                            <button class="pagination-btn" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="pagination-btn active">1</button>
                            <button class="pagination-btn">2</button>
                            <button class="pagination-btn">
                                <i class="fas fa-chevron-right"></i>
                            </button>
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
        const tableBody = document.getElementById('majorTableBody');
        
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
        function deleteMajor(id, name) {
            if (confirm(`Bạn có chắc muốn xóa ngành "${name}"?`)) {
                window.location.href = `../handle/major_process.php?action=delete&id=${id}`;
            }
        }

        // Bulk delete functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const majorCheckboxes = document.querySelectorAll('.major-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCountSpan = document.getElementById('selectedCount');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                majorCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
        }

        majorCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkDeleteButton);
        });

        function updateBulkDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.major-checkbox:checked');
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
                const checkedBoxes = document.querySelectorAll('.major-checkbox:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);
                
                if (ids.length === 0) return;
                
                if (confirm(`Bạn có chắc muốn xóa ${ids.length} ngành học đã chọn?\n\nHành động này không thể hoàn tác!`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../handle/major_process.php';
                    
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
