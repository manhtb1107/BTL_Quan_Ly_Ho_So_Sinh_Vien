<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/student_functions.php';
checkLogin(__DIR__ . '/../../index.php');

// Lấy ID sinh viên từ URL
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id <= 0) {
    header('Location: ../student.php?error=ID sinh viên không hợp lệ');
    exit;
}

// Lấy thông tin sinh viên
$student = getStudentById($student_id);

if (!$student) {
    header('Location: ../student.php?error=Không tìm thấy sinh viên');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Chi tiết sinh viên - Quản lý hồ sơ sinh viên đại học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php include '../menu.php'; ?>
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>
                        <i class="fas fa-user"></i> Chi tiết hồ sơ sinh viên
                    </h3>
                    <div>
                        <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        <a href="../student.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>

                <!-- Thông tin cơ bản -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Thông tin cơ bản</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="mb-3 d-flex justify-content-center">
                                        <?php if (!empty($student['image'])): ?>
                                            <img src="/baitaplon/<?= htmlspecialchars($student['image']) ?>" 
                                                 alt="Hình ảnh sinh viên" 
                                                 class="rounded-circle" 
                                                 style="width: 180px; height: 180px; object-fit: cover; border: 4px solid #dee2e6; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 180px; height: 180px; border: 4px solid #dee2e6;">
                                                <i class="fas fa-user-circle fa-5x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="mt-3 mb-2"><?= htmlspecialchars($student['full_name'] ?? $student['student_name']) ?></h5>
                                    <span class="badge bg-primary text-white px-3 py-2 fw-bold" style="font-size: 0.9rem; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                        <i class="fas fa-id-card me-1"></i>
                                        <?= htmlspecialchars($student['student_code']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Mã sinh viên:</strong> 
                                            <span class="badge bg-primary text-white px-2 py-1 fw-bold" style="font-size: 0.85rem; letter-spacing: 0.3px;">
                                                <i class="fas fa-id-card me-1"></i>
                                                <?= htmlspecialchars($student['student_code']) ?>
                                            </span>
                                        </p>
                                        <p><strong>Họ và tên:</strong> <?= htmlspecialchars($student['full_name'] ?? $student['student_name']) ?></p>
                                        <p><strong>Ngày sinh:</strong> <?= !empty($student['date_of_birth']) ? date('d/m/Y', strtotime($student['date_of_birth'])) : 'Chưa cập nhật' ?></p>
                                        <p><strong>Giới tính:</strong> <?= htmlspecialchars($student['gender'] ?? 'Chưa cập nhật') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>CMND/CCCD:</strong> <?= htmlspecialchars($student['id_card'] ?? 'Chưa cập nhật') ?></p>
                                        <p><strong>Lớp học:</strong> <?= htmlspecialchars($student['class_name'] ?? 'Chưa phân lớp') ?></p>
                                        <p><strong>Ngành học:</strong> <?= htmlspecialchars($student['major'] ?? 'Chưa cập nhật') ?></p>
                                        <p><strong>Niên khóa:</strong> <?= htmlspecialchars($student['academic_year'] ?? 'Chưa cập nhật') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin liên hệ -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-address-book"></i> Thông tin liên hệ</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Số điện thoại:</strong> 
                                    <?php if (!empty($student['phone'])): ?>
                                        <a href="tel:<?= htmlspecialchars($student['phone']) ?>"><?= htmlspecialchars($student['phone']) ?></a>
                                    <?php else: ?>
                                        Chưa cập nhật
                                    <?php endif; ?>
                                </p>
                                <p><strong>Email:</strong> 
                                    <?php if (!empty($student['email'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($student['email']) ?>"><?= htmlspecialchars($student['email']) ?></a>
                                    <?php else: ?>
                                        Chưa cập nhật
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Địa chỉ hiện tại:</strong></p>
                                <p><?= !empty($student['address']) ? nl2br(htmlspecialchars($student['address'])) : 'Chưa cập nhật' ?></p>
                                <p><strong>Quê quán:</strong> <?= htmlspecialchars($student['hometown'] ?? 'Chưa cập nhật') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin học tập -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Thông tin học tập</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Ngày nhập học:</strong> <?= !empty($student['enrollment_date']) ? date('d/m/Y', strtotime($student['enrollment_date'])) : 'Chưa cập nhật' ?></p>
                                <p><strong>Ngày tốt nghiệp:</strong> <?= !empty($student['graduation_date']) ? date('d/m/Y', strtotime($student['graduation_date'])) : 'Chưa tốt nghiệp' ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Trạng thái:</strong> 
                                    <?php
                                    $statusClass = '';
                                    switch($student['status']) {
                                        case 'Đang học':
                                            $statusClass = 'bg-success';
                                            break;
                                        case 'Tạm nghỉ':
                                            $statusClass = 'bg-warning';
                                            break;
                                        case 'Tốt nghiệp':
                                            $statusClass = 'bg-info';
                                            break;
                                        case 'Bị đuổi học':
                                            $statusClass = 'bg-danger';
                                            break;
                                        default:
                                            $statusClass = 'bg-secondary';
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= htmlspecialchars($student['status'] ?? 'Chưa xác định') ?>
                                    </span>
                                </p>
                                <p><strong>GPA:</strong> 
                                    <?php if (!empty($student['gpa'])): ?>
                                        <span class="badge bg-primary"><?= number_format($student['gpa'], 2) ?></span>
                                    <?php else: ?>
                                        Chưa có điểm
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin hệ thống -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin hệ thống</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Ngày tạo:</strong> <?= !empty($student['created_at']) ? date('d/m/Y H:i:s', strtotime($student['created_at'])) : 'Không xác định' ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Cập nhật lần cuối:</strong> <?= !empty($student['updated_at']) ? date('d/m/Y H:i:s', strtotime($student['updated_at'])) : 'Không xác định' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
