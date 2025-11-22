<?php
require_once __DIR__ . '/../functions/auth.php';
requireAdmin(__DIR__ . '/../index.php'); // Chỉ Admin mới truy cập được
$currentUser = getCurrentUser();

// Get users from database
require_once __DIR__ . '/../functions/db_connection.php';
$conn = getDbConnection();
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - Admin Panel</title>
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
                    <div class="breadcrumb-nav">
                        <a href="admin_dashboard.php">Trang chủ</a>
                        <span>/</span>
                        <span class="current">Quản lý Người dùng</span>
                    </div>
                </div>
                
                <div class="header-right">
                    <button class="btn-icon" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i>
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
                <div class="welcome-section mb-3">
                    <h1 class="page-title">Quản lý Tài khoản Người dùng</h1>
                    <p class="page-subtitle">Thêm, chỉnh sửa, hoặc xóa tài khoản sinh viên và giảng viên.</p>
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
                <div class="filter-section">
                    <div class="filter-row">
                        <div class="search-input-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Tìm theo tên, email, mã số...">
                        </div>
                        <select class="filter-select" id="roleFilter">
                            <option value="">Vai trò: Tất cả</option>
                            <option value="admin">Admin</option>
                            <option value="teacher">Giảng viên</option>
                            <option value="student">Sinh viên</option>
                        </select>
                        <select class="filter-select" id="statusFilter">
                            <option value="">Trạng thái: Hoạt động</option>
                            <option value="1">Hoạt động</option>
                            <option value="0">Đã khóa</option>
                        </select>
                        <button class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
                            <i class="fas fa-trash me-2"></i>Xóa đã chọn (<span id="selectedCount">0</span>)
                        </button>
                        <a href="users/create_users.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm người dùng
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
                                    <th>STT</th>
                                    <th>Họ và tên</th>
                                    <th>Mã số</th>
                                    <th>Email</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="8" class="no-data">
                                            <i class="fas fa-users"></i>
                                            <h5>Chưa có người dùng nào</h5>
                                            <p class="mb-0">Thêm người dùng mới để bắt đầu quản lý</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $index => $user): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input user-checkbox" value="<?= $user['id'] ?>">
                                            </td>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($user['full_name'] ?? $user['username']) ?></td>
                                            <td>
                                                <span class="class-code-badge">
                                                    <?= htmlspecialchars($user['username']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $roleText = '';
                                                $roleClass = '';
                                                switch($user['role'] ?? '') {
                                                    case 'admin': 
                                                        $roleText = 'Admin'; 
                                                        $roleClass = 'badge bg-danger';
                                                        break;
                                                    case 'teacher': 
                                                        $roleText = 'Giảng viên'; 
                                                        $roleClass = 'badge bg-primary';
                                                        break;
                                                    case 'student': 
                                                        $roleText = 'Sinh viên'; 
                                                        $roleClass = 'badge bg-info';
                                                        break;
                                                    default: 
                                                        $roleText = 'Chưa phân quyền'; 
                                                        $roleClass = 'badge bg-secondary';
                                                }
                                                ?>
                                                <span class="<?= $roleClass ?>"><?= $roleText ?></span>
                                            </td>
                                            <td>
                                                <?php if ($user['is_active']): ?>
                                                    <span class="status-badge active">
                                                        <i class="fas fa-check-circle"></i> Hoạt động
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-badge inactive">
                                                        <i class="fas fa-times-circle"></i> Đã khóa
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="users/edit_users.php?id=<?= $user['id'] ?>" 
                                                   class="action-btn edit" 
                                                   title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" 
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

                    <?php if (!empty($users)): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị 1-<?= count($users) ?> trên 100
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
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }
        }, 3000);

        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('userTableBody');
        
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

        function deleteUser(id, username) {
            if (confirm(`Bạn có chắc muốn xóa người dùng "${username}"?`)) {
                window.location.href = `../handle/user_process.php?action=delete&id=${id}`;
            }
        }

        // Delete user function
        function deleteUser(id, username) {
            if (confirm(`Bạn có chắc muốn xóa người dùng "${username}"?\n\nHành động này không thể hoàn tác!`)) {
                window.location.href = `../handle/users_process.php?action=delete&id=${id}`;
            }
        }

        // Bulk delete functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCountSpan = document.getElementById('selectedCount');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
        }

        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkDeleteButton);
        });

        function updateBulkDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
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
                const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);
                
                if (ids.length === 0) return;
                
                if (confirm(`Bạn có chắc muốn xóa ${ids.length} người dùng đã chọn?\n\nHành động này không thể hoàn tác!`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../handle/users_process.php';
                    
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
