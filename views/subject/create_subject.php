<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/student_functions.php';
checkLogin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();

// Lấy danh sách ngành học
$majors = getAllMajors();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Môn học mới - Admin Panel</title>
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
                    <h2 class="page-title mb-0">Thêm Môn học mới</h2>
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
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="form-header-content">
                            <h2>Thêm môn học mới</h2>
                            <p>Nhập thông tin chi tiết của môn học</p>
                        </div>
                    </div>

                    <form action="../../handle/subject_process.php" method="POST">
                        <input type="hidden" name="action" value="create">

                        <div class="form-group">
                            <label for="subject_code" class="form-label">
                                Mã môn học<span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="subject_code" 
                                   name="subject_code" 
                                   placeholder="Ví dụ: IT101"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="subject_name" class="form-label">
                                Tên môn học<span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="subject_name" 
                                   name="subject_name" 
                                   placeholder="Ví dụ: Lập trình cơ bản"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="credits" class="form-label">
                                Số tín chỉ<span class="required">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="credits" 
                                   name="credits" 
                                   min="1"
                                   max="10"
                                   placeholder="Ví dụ: 3"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="major_id" class="form-label">
                                Ngành học<span class="required">*</span>
                            </label>
                            <select id="major_id" name="major_id" class="form-select" required>
                                <option value="">-- Chọn ngành học --</option>
                                <?php foreach ($majors as $major): ?>
                                    <option value="<?= $major['id'] ?>">
                                        <?= htmlspecialchars($major['major_code']) ?> - <?= htmlspecialchars($major['major_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Môn học này chỉ dành cho ngành được chọn</small>
                        </div>

                        <div class="form-group">
                            <label for="subject_type" class="form-label">
                                Loại môn học<span class="required">*</span>
                            </label>
                            <select id="subject_type" name="subject_type" class="form-select" required>
                                <option value="">-- Chọn loại môn học --</option>
                                <option value="Bắt buộc">Bắt buộc</option>
                                <option value="Tự chọn">Tự chọn</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                Mô tả
                            </label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Nhập mô tả về môn học..."></textarea>
                        </div>

                        <div class="form-actions">
                            <a href="../subject.php" class="btn-cancel">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-plus me-2"></i>Thêm môn học
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
