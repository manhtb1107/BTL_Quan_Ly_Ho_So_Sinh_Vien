<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/student_functions.php';
checkLogin(__DIR__ . '/../../index.php');

// Lấy danh sách lớp và ngành
$classes = getAllClasses();
$majors = getAllMajors();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Thêm sinh viên mới - Quản lý hồ sơ sinh viên đại học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php include '../menu.php'; ?>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h3 class="mt-3 mb-4">
                    <i class="fas fa-user-plus"></i> THÊM SINH VIÊN MỚI
                </h3>
                
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
                
                <form action="../../handle/student_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
                    
                    <!-- Thông tin cơ bản -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user"></i> Thông tin cơ bản</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_code" class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="student_code" name="student_code" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_name" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="student_name" name="student_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Họ và tên đầy đủ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_image" class="form-label">Ảnh đại diện</label>
                                        <input type="file" class="form-control" id="student_image" name="student_image" accept="image/*" onchange="previewImage(this)">
                                        <small class="text-muted">Chấp nhận: JPG, PNG, GIF (tối đa 5MB)</small>
                                        <div class="mt-2 text-center" id="image_preview" style="display: none;">
                                            <img id="preview_img" src="" alt="Preview" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 2px solid #dee2e6;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="date_of_birth" class="form-label">Ngày sinh</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Giới tính</label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="">Chọn giới tính</option>
                                            <option value="Nam">Nam</option>
                                            <option value="Nữ">Nữ</option>
                                            <option value="Khác">Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_card" class="form-label">CMND/CCCD</label>
                                        <input type="text" class="form-control" id="id_card" name="id_card">
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
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Địa chỉ hiện tại</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hometown" class="form-label">Quê quán</label>
                                        <input type="text" class="form-control" id="hometown" name="hometown">
                                    </div>
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
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="class_id" class="form-label">Lớp học</label>
                                        <select class="form-select" id="class_id" name="class_id">
                                            <option value="">Chọn lớp học</option>
                                            <?php foreach($classes as $class): ?>
                                                <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="major" class="form-label">Ngành học</label>
                                        <select class="form-select" id="major" name="major">
                                            <option value="">Chọn ngành học</option>
                                            <?php foreach($majors as $major): ?>
                                                <option value="<?= htmlspecialchars($major['major_name']) ?>">
                                                    <?= htmlspecialchars($major['major_code']) ?> - <?= htmlspecialchars($major['major_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="academic_year" class="form-label">Niên khóa</label>
                                        <input type="text" class="form-control" id="academic_year" name="academic_year" placeholder="VD: 2024-2025">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="enrollment_date" class="form-label">Ngày nhập học</label>
                                        <input type="date" class="form-control" id="enrollment_date" name="enrollment_date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Đang học">Đang học</option>
                                            <option value="Tạm nghỉ">Tạm nghỉ</option>
                                            <option value="Tốt nghiệp">Tốt nghiệp</option>
                                            <option value="Bị đuổi học">Bị đuổi học</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Thêm sinh viên
                        </button>
                        <a href="../student.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('image_preview');
            const previewImg = document.getElementById('preview_img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>

</html>
