<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Thêm ngành học mới - Quản lý hồ sơ sinh viên đại học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php include '../menu.php'; ?>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h3 class="mt-3 mb-4">
                    <i class="fas fa-graduation-cap"></i> THÊM NGÀNH HỌC MỚI
                </h3>
                
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                
                <form action="../../handle/major_process.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin ngành học</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="major_code" class="form-label">Mã ngành <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="major_code" name="major_code" required 
                                       placeholder="VD: CNTT, KT, QTKD">
                                <small class="form-text text-muted">Mã ngành phải là duy nhất</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="major_name" class="form-label">Tên ngành <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="major_name" name="major_name" required 
                                       placeholder="VD: Công nghệ thông tin">
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Mô tả về ngành học..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Thêm ngành học
                        </button>
                        <a href="../major.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

