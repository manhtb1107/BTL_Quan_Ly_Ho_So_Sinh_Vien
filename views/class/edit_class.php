<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/class_functions.php';
$currentUser = getCurrentUser();

// Validate id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../class.php?error=Không tìm thấy lớp");
    exit;
}

$id = (int)$_GET['id'];
$classInfo = getClassById($id);
if (!$classInfo) {
    header("Location: ../class.php?error=Không tìm thấy lớp");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Lớp học - Admin Panel</title>
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
                    <h2 class="page-title mb-0">Chỉnh sửa Lớp học</h2>
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
                            <h2>Chỉnh sửa thông tin lớp học</h2>
                            <p>Cập nhật thông tin chi tiết của lớp học</p>
                        </div>
                    </div>

                    <?php
                    require_once __DIR__ . '/../../functions/major_functions.php';
                    $majors = getAllMajorsList();
                    ?>

                    <form action="../../handle/class_process.php" method="POST">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($classInfo['id']) ?>">

                        <div class="form-group">
                            <label for="class_code" class="form-label">
                                Mã lớp<span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="class_code" 
                                   name="class_code" 
                                   value="<?= htmlspecialchars($classInfo['class_code'] ?? '') ?>" 
                                   placeholder="Ví dụ: CNTT01-K15"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="class_name" class="form-label">
                                Tên lớp<span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="class_name" 
                                   name="class_name" 
                                   value="<?= htmlspecialchars($classInfo['class_name'] ?? '') ?>" 
                                   placeholder="Ví dụ: Công nghệ phần mềm 01"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="major" class="form-label">
                                Ngành học<span class="required">*</span>
                            </label>
                            <select id="major" name="major" class="form-select" required>
                                <option value="">-- Chọn ngành học --</option>
                                <?php foreach ($majors as $m): ?>
                                    <option value="<?= htmlspecialchars($m['major_name']) ?>" 
                                            <?= (isset($classInfo['major']) && $classInfo['major'] == $m['major_name']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['major_code'] . ' - ' . $m['major_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="academic_year" class="form-label">
                                Niên khóa<span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="academic_year" 
                                   name="academic_year" 
                                   value="<?= htmlspecialchars($classInfo['academic_year'] ?? '') ?>" 
                                   placeholder="Ví dụ: 2021-2025"
                                   required>
                        </div>

                        <div class="form-actions">
                            <a href="../class.php" class="btn-cancel">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save me-2"></i>Cập nhật
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
