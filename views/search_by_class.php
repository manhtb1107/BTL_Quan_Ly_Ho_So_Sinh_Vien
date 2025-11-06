<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/student_functions.php';
checkLogin(__DIR__ . '/../index.php');

// Lấy danh sách lớp
$classes = getAllClasses();

// Xử lý tìm kiếm
$students = [];
$selectedClass = '';

if (isset($_GET['class_id']) && !empty($_GET['class_id'])) {
    $selectedClass = $_GET['class_id'];
    $students = getStudentsByClass($selectedClass);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Tìm kiếm sinh viên theo lớp - DNU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php include './menu.php'; ?>
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>
                <i class="fas fa-chalkboard-teacher"></i> Tìm kiếm sinh viên theo lớp
            </h3>
            <a href="/baitaplon/views/student_management.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Form tìm kiếm -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-search"></i> Chọn lớp học
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row">
                        <div class="col-md-8">
                            <select name="class_id" class="form-select" required>
                                <option value="">-- Chọn lớp học --</option>
                                <?php foreach($classes as $class): ?>
                                    <option value="<?= $class['id'] ?>" 
                                            <?= $selectedClass == $class['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($class['class_name']) ?> 
                                        (<?= htmlspecialchars($class['class_code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kết quả tìm kiếm -->
        <?php if (!empty($students)): ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Kết quả tìm kiếm 
                        (<?= count($students) ?> sinh viên)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Mã SV</th>
                                    <th>Họ và tên</th>
                                    <th>Ngày sinh</th>
                                    <th>Giới tính</th>
                                    <th>Ngành</th>
                                    <th>Trạng thái</th>
                                    <th>Đối tượng ưu tiên</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($students as $student): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary text-white px-3 py-2 fw-bold" style="font-size: 0.9rem; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                <i class="fas fa-id-card me-1"></i>
                                                <?= htmlspecialchars($student['student_code']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($student['full_name'] ?? $student['student_name']) ?></td>
                                        <td><?= !empty($student['date_of_birth']) ? date('d/m/Y', strtotime($student['date_of_birth'])) : '-' ?></td>
                                        <td><?= htmlspecialchars($student['gender'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($student['major'] ?? '-') ?></td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch($student['status']) {
                                                case 'Đang học': $statusClass = 'bg-success'; break;
                                                case 'Tạm nghỉ': $statusClass = 'bg-warning'; break;
                                                case 'Tốt nghiệp': $statusClass = 'bg-info'; break;
                                                case 'Bị đuổi học': $statusClass = 'bg-danger'; break;
                                                default: $statusClass = 'bg-secondary';
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>">
                                                <?= htmlspecialchars($student['status'] ?? 'Chưa xác định') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($student['priority_group']) && $student['priority_group'] != 'Không'): ?>
                                                <span class="badge bg-warning">
                                                    <?= htmlspecialchars($student['priority_group']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Không</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="/baitaplon/views/student/view_student.php?id=<?= $student['id'] ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php elseif (isset($_GET['class_id'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Không tìm thấy sinh viên nào trong lớp đã chọn.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
