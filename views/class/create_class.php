<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/grade_functions.php';

// Lấy danh sách sinh viên và môn học cho dropdown
$students = getAllStudentsForDropdown();
$subjects = getAllSubjectsForDropdown();
?>
<!DOCTYPE html>
<html>

<head>
    <title>DNU - Thêm điểm mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h3 class="mt-3 mb-4">THÊM ĐIỂM MỚI</h3>
                
                <?php
                // Hiển thị thông báo lỗi
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                    
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Sinh viên</label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">-- Chọn sinh viên --</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>">
                                    <span class="badge bg-primary text-white me-2" style="font-size: 0.8rem;">
                                        <i class="fas fa-id-card me-1"></i>
                                        <?= htmlspecialchars($student['student_code']) ?>
                                    </span>
                                    <?= htmlspecialchars($student['student_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="class" class="form-label">Lớp</label>
                        <input type="type" class="form-control" id="class" name="class" required>
                    </div>
                                
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Thêm Lớp</button>
                        <a href="../class.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
