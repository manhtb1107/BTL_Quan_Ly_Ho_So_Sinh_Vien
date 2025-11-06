<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/major_functions.php';
checkLogin(__DIR__ . '/../index.php');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý ngành học - Đại Học Đại Nam</title>
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

        .btn-add {
            background: var(--gradient-secondary);
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            color: white;
        }

        .table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .table tbody tr:hover {
            background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }
    </style>
</head>

<body>
    <?php include './menu.php'; ?>
    <div class="container-fluid">
        <div class="main-container">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-graduation-cap me-3"></i>
                    QUẢN LÝ NGÀNH HỌC
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
                            <i class="fas fa-list me-2"></i>Danh sách ngành học
                        </h5>
                    </div>
                    <div>
                        <a href="major/create_major.php" class="btn btn-add">
                            <i class="fas fa-plus me-2"></i>Thêm ngành học mới
                        </a>
                    </div>
                </div>

                <!-- Bảng dữ liệu -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">
                                    <i class="fas fa-id-card me-2"></i>Mã ngành
                                </th>
                                <th scope="col">
                                    <i class="fas fa-graduation-cap me-2"></i>Tên ngành
                                </th>
                                <th scope="col">
                                    <i class="fas fa-info-circle me-2"></i>Mô tả
                                </th>
                                <th scope="col" class="text-end">
                                    <i class="fas fa-cogs me-2"></i>Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $majors = getAllMajorsList();

                            if (!$majors || count($majors) === 0) {
                                echo '<tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                        <h5>Không có dữ liệu ngành học</h5>
                                        <p class="mb-0 text-muted">Hãy thêm ngành học mới</p>
                                    </td>
                                </tr>';
                            } else {
                                foreach($majors as $index => $major){
                                    $stt = $index + 1;
                            ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= $stt ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?= htmlspecialchars($major['major_code']) ?></span>
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($major['major_name']) ?></td>
                                    <td><?= htmlspecialchars($major['description'] ?? 'Chưa có mô tả') ?></td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="major/edit_major.php?id=<?= $major['id'] ?>" 
                                               class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="../handle/major_process.php?action=delete&id=<?= $major['id'] ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa ngành học này?')" 
                                               title="Xóa">
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
</body>

</html>

