<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../functions/auth.php';
requireAdmin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người dùng - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../../css/admin_dashboard.css" rel="stylesheet">
    <link href="../../css/form_style.css" rel="stylesheet">
    <style>
        .form-card {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-header-simple {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .form-header-simple .icon {
            width: 60px;
            height: 60px;
            background: #2196F3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .form-header-simple .content h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: #333;
        }
        
        .form-header-simple .content p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .form-group-simple {
            margin-bottom: 1.5rem;
        }
        
        .form-group-simple label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-group-simple label .required {
            color: #f44336;
        }
        
        .form-group-simple input,
        .form-group-simple select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group-simple input:focus,
        .form-group-simple select:focus {
            outline: none;
            border-color: #2196F3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }
        
        .form-actions-simple {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }
        
        .btn-cancel-simple {
            padding: 0.75rem 2rem;
            border: 1px solid #ddd;
            background: white;
            color: #666;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        
        .btn-cancel-simple:hover {
            background: #f5f5f5;
            color: #333;
        }
        
        .btn-submit-simple {
            padding: 0.75rem 2rem;
            border: none;
            background: #2196F3;
            color: white;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            cursor: pointer;
        }
        
        .btn-submit-simple:hover {
            background: #1976D2;
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <?php 
        $sidebarPath = __DIR__ . '/../includes/sidebar.php';
        if (file_exists($sidebarPath)) {
            include $sidebarPath;
        } else {
            echo "<!-- Sidebar not found at: $sidebarPath -->";
        }
        ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button class="btn-menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="page-title mb-0">Thêm Người dùng Mới</h2>
                </div>
                
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../../images/aiotlab_logo.png" alt="Admin">
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($currentUser['username']) ?></span>
                            <span class="user-email">Admin</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="page-content">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="form-card">
                    <div class="form-header-simple">
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="content">
                            <h2>Thêm người dùng mới</h2>
                            <p>Nhập thông tin chi tiết của người dùng</p>
                        </div>
                    </div>

                    <form action="../../handle/users_process.php" method="POST">
                        <input type="hidden" name="action" value="create">

                        <div class="form-group-simple">
                            <label for="username">Tên đăng nhập <span class="required">*</span></label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   placeholder="Ví dụ: teacher01"
                                   required
                                   minlength="3"
                                   maxlength="50">
                        </div>

                        <div class="form-group-simple">
                            <label for="password">Mật khẩu <span class="required">*</span></label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)"
                                   required
                                   minlength="6">
                        </div>

                        <div class="form-group-simple">
                            <label for="confirm_password">Xác nhận mật khẩu <span class="required">*</span></label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Nhập lại mật khẩu"
                                   required
                                   minlength="6">
                        </div>

                        <div class="form-group-simple">
                            <label for="full_name">Họ và tên</label>
                            <input type="text" 
                                   id="full_name" 
                                   name="full_name" 
                                   placeholder="Nguyễn Văn A"
                                   maxlength="100">
                        </div>

                        <div class="form-group-simple">
                            <label for="email">Email</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   placeholder="example@university.edu"
                                   maxlength="100">
                        </div>

                        <div class="form-group-simple">
                            <label for="role">Vai trò <span class="required">*</span></label>
                            <select id="role" name="role" required>
                                <option value="">-- Chọn vai trò --</option>
                                <option value="admin">Admin (Quản trị viên)</option>
                                <option value="teacher" selected>Teacher (Giảng viên)</option>
                            </select>
                        </div>

                        <div class="form-group-simple">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       checked
                                       style="width: 18px; height: 18px; cursor: pointer;">
                                <label for="is_active" style="margin: 0; cursor: pointer;">
                                    Kích hoạt tài khoản (cho phép đăng nhập)
                                </label>
                            </div>
                        </div>

                        <div class="form-actions-simple">
                            <a href="../users.php" class="btn-cancel-simple">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn-submit-simple">
                                <i class="fas fa-save"></i> Tạo người dùng
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
        }, 5000);

        // Validate password match
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                document.getElementById('confirm_password').focus();
            }
        });
    </script>
</body>
</html>
