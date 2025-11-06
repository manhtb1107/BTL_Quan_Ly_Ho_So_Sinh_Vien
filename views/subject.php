<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý môn học - Đại Học Đại Nam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/baitaplon/css/app.css" rel="stylesheet">
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

        .data-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: none;
        }

        .table-header {
            background: var(--dark-bg);
            color: white;
        }

        .table-header th {
            border: none;
            padding: 1.2rem 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            border: none;
        }

        .table tbody tr:hover {
            background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .table tbody td {
            padding: 1.2rem 1rem;
            border: none;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
        }

        .btn-group .btn {
            margin: 0 2px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-warning {
            background: linear-gradient(45deg, #f39c12, #e67e22);
            border: none;
        }

        .btn-danger {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            border: none;
        }

        .alert {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(45deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(45deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .no-data {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .no-data i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .btn-add {
            background: var(--gradient-secondary);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .table tbody tr:hover {
            background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%) !important;
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
            
            .table-responsive {
                border-radius: 10px;
            }
            
            .btn-group .btn {
                margin: 2px;
                font-size: 0.8rem;
                padding: 6px 10px;
            }
        }
    </style>
</head>

<body>
    <?php include './menu.php'; ?>
    <div class="container-fluid">
        <div class="main-container">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-book me-3"></i>
                    QUẢN LÝ MÔN HỌC
                </h1>
            </div>
            
            <div class="content-area">

                <?php
                // Hiển thị thông báo thành công
                if (isset($_GET['success'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        ' . htmlspecialchars($_GET['success']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
                }
                
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

                <!-- Thanh tìm kiếm và thêm mới -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 me-3">
                            <i class="fas fa-list me-2"></i>Danh sách môn học
                        </h5>
                    </div>
                    <div>
                        <a href="subject/create_subject.php" class="btn btn-add">
                            <i class="fas fa-plus me-2"></i>Thêm môn học mới
                        </a>
                    </div>
                </div>

                <!-- Bảng dữ liệu -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">
                                    <i class="fas fa-id-card me-2"></i>Mã môn học
                                </th>
                                <th scope="col">
                                    <i class="fas fa-book-open me-2"></i>Tên môn học
                                </th>
                                <th scope="col" class="text-end">
                                    <i class="fas fa-cogs me-2"></i>Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require_once '../functions/subject_functions.php';
                            $subjects = getAllSubjects();

                            if (!$subjects || count($subjects) === 0) {
                                echo '<tr>
                                    <td colspan="4" class="no-data">
                                        <i class="fas fa-book-open"></i>
                                        <h5>Không có dữ liệu môn học</h5>
                                        <p class="mb-0">Hãy thêm môn học mới hoặc kiểm tra lại dữ liệu</p>
                                    </td>
                                </tr>';
                            } else {
                                foreach($subjects as $index => $subject){
                                    $stt = $index + 1;
                            ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= $stt ?></td>
                                    <td>
                                        <span class="badge bg-primary text-white px-3 py-2 fw-bold" style="font-size: 0.9rem; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                            <i class="fas fa-book me-1"></i>
                                            <?= htmlspecialchars($subject["subject_code"]) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-book-open text-muted"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($subject["subject_name"]) ?></div>
                                                <small class="text-muted">ID: <?= $subject["id"] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="subject/edit_subject.php?id=<?= htmlspecialchars($subject["id"]) ?>" 
                                               class="btn btn-warning btn-sm" 
                                               title="Chỉnh sửa thông tin môn học">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="../handle/subject_process.php?action=delete&id=<?= htmlspecialchars($subject["id"]) ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa môn học <?= htmlspecialchars($subject['subject_name']) ?>?')" 
                                               title="Xóa môn học">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Thêm hiệu ứng loading cho các nút
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.classList.contains('btn-add') || this.classList.contains('btn-primary')) {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
                    this.disabled = true;
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }, 1000);
                }
            });
        });

        // Thêm hiệu ứng cho bảng
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'none';
            });
        });
    </script>
</body>
</html>