<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/major_functions.php';
checkLogin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../major.php?error=Không tìm thấy ngành học");
    exit;
}

$id = (int)$_GET['id'];
$major = getMajorById($id);

if (!$major) {
    header("Location: ../major.php?error=Không tìm thấy ngành học");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Ngành học - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../css/admin_dashboard.css" rel="stylesheet">
    <link href="../../css/form_style.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="btn-menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="page-title mb-0">Chỉnh sửa Ngành học</h2>
                </div>
                
                <div class="header-right">
                    <button class="btn-icon" title="Thông báo">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </button>
                    
                    <div class="user-profile">
                        <img src="../../images/aiotlab_logo.png" alt="Admin">
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($currentUser['username'] ?? 'Admin') ?></span>
                            <span class="user-email">Đại học Công nghệ</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="form-card">
                    <div class="form-header">
                        <div class="form-header-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="form-header-content">
                            <h2>Chỉnh sửa Ngành học</h2>
                        </div>
                    </div>

                    <form action="../../handle/major_process.php" method="POST">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($major['id']) ?>">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="major_code" class="form-label">
                                    Mã Ngành<span class="required">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="major_code" 
                                       name="major_code" 
                                       value="<?= htmlspecialchars($major['major_code'] ?? '') ?>"
                                       placeholder="Ví dụ: CNTT"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="major_name" class="form-label">
                                    Tên Ngành<span class="required">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="major_name" 
                                       name="major_name" 
                                       value="<?= htmlspecialchars($major['major_name'] ?? '') ?>"
                                       placeholder="Ví dụ: Công nghệ Thông tin"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="department" class="form-label">
                                    Thuộc Khoa
                                </label>
                                <select id="department" name="department" class="form-select">
                                    <option value="">Chọn khoa chủ quản</option>
                                    <option value="cntt">Công nghệ Thông tin</option>
                                    <option value="kt">Kinh tế</option>
                                    <option value="nn">Ngoại ngữ</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                Mô tả (tùy chọn)
                            </label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      placeholder="Nhập mô tả ngắn về ngành học..."><?= htmlspecialchars($major['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-actions">
                            <a href="../major.php" class="btn-cancel">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save"></i>
                                Cập nhật Ngành học
                            </button>
                        </div>
                    </form>
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
    </script>
</body>
</html>
