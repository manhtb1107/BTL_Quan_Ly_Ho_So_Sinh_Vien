<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/grade_functions.php';

// Lấy danh sách sinh viên và môn học cho dropdown
$students = getAllStudentsForDropdown();
$subjects = getAllSubjectsForDropdown();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm điểm mới - Đại Học Đại Nam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .main-container {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            margin: 20px auto;
            overflow: hidden;
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-top: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .content-area {
            padding: 2rem;
        }

        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            border: none;
            overflow: hidden;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-submit {
            background: var(--gradient-primary);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-cancel {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .alert {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(45deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .page-header {
                padding: 1.5rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .content-area {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../menu.php'; ?>
    <div class="container-fluid">
        <div class="main-container">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-plus-circle me-3"></i>
                    THÊM ĐIỂM MỚI
                </h1>
            </div>
            
            <div class="content-area">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="form-card">
                            <div class="card-body p-4">
                
                                <?php
                                // Hiển thị thông báo lỗi
                                if (isset($_GET['error'])) {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        ' . htmlspecialchars($_GET['error']) . '
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>';
                                }
                                ?>
                                <script>
                                // Sau 3 giây sẽ tự động ẩn alert
                                setTimeout(() => {
                                    let alertNode = document.querySelector('.alert');
                                    if (alertNode) {
                                        let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                                        bsAlert.close();
                                    }
                                }, 3000);
                                </script>
                                
                                <form action="../../handle/grade_process.php" method="POST">
                                    <input type="hidden" name="action" value="create">
                                    
                                    <div class="mb-4">
                                        <label for="student_id" class="form-label fw-semibold">
                                            <i class="fas fa-user me-2"></i>Sinh viên
                                        </label>
                                        <select class="form-select" id="student_id" name="student_id" required>
                                            <option value="">-- Chọn sinh viên --</option>
                                            <?php foreach ($students as $student): ?>
                                                <option value="<?= $student['id'] ?>">
                                                    <?= htmlspecialchars($student['student_code']) ?> - <?= htmlspecialchars($student['student_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="subject_id" class="form-label fw-semibold">
                                            <i class="fas fa-book me-2"></i>Môn học
                                        </label>
                                        <select class="form-select" id="subject_id" name="subject_id" required>
                                            <option value="">-- Chọn môn học --</option>
                                            <?php foreach ($subjects as $subject): ?>
                                                <option value="<?= $subject['id'] ?>">
                                                    <?= htmlspecialchars($subject['subject_code']) ?> - <?= htmlspecialchars($subject['subject_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="grade" class="form-label fw-semibold">
                                            <i class="fas fa-star me-2"></i>Điểm số (0-10)
                                        </label>
                                        <input type="number" class="form-control" id="grade" name="grade" 
                                               min="0" max="10" step="0.1" 
                                               placeholder="Nhập điểm từ 0.0 đến 10.0" required>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Điểm số phải từ 0.0 đến 10.0, có thể nhập 1 chữ số thập phân
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="../grade.php" class="btn btn-cancel me-md-2">
                                            <i class="fas fa-times me-2"></i>Hủy
                                        </a>
                                        <button type="submit" class="btn btn-submit">
                                            <i class="fas fa-plus me-2"></i>Thêm điểm
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Thêm hiệu ứng loading cho nút submit
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang thêm...';
            submitBtn.disabled = true;
        });

        // Thêm hiệu ứng cho các input
        document.querySelectorAll('.form-control, .form-select').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>
