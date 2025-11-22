<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/student_functions.php';
checkLogin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();

// Lấy danh sách lớp và ngành
$classes = getAllClasses();
$majors = getAllMajors();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sinh viên Mới - Admin Panel</title>
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
            <header class="top-header">
                <div class="header-left">
                    <button class="btn-menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="page-title mb-0">Thêm Sinh viên Mới</h2>
                </div>
                
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../../images/aiotlab_logo.png" alt="Admin">
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($currentUser['username'] ?? 'Admin') ?></span>
                            <span class="user-email">admin@university.edu</span>
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
                                        <input type="text" class="form-control" id="student_code" name="student_code" 
                                            placeholder="Ví dụ: 20210001" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_name" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="student_name" name="student_name"
                                            placeholder="Tên đăng nhập" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Họ và tên đầy đủ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="full_name" name="full_name"
                                            placeholder="Nguyễn Văn A" required>
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
                                            <option value="Nam" selected>Nam</option>
                                            <option value="Nữ">Nữ</option>
                                            <option value="Khác">Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_card" class="form-label">CMND/CCCD</label>
                                        <input type="text" class="form-control" id="id_card" name="id_card"
                                            placeholder="Số CMND/CCCD">
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
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            placeholder="0987654321">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="example@email.com">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Địa chỉ hiện tại</label>
                                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Nhập địa chỉ hiện tại"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hometown" class="form-label">Quê quán</label>
                                        <textarea class="form-control" id="hometown" name="hometown" rows="2" placeholder="Nhập quê quán"></textarea>
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
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="class_id" class="form-label">Lớp <span class="text-danger">*</span></label>
                                        <select class="form-select" id="class_id" name="class_id" required>
                                            <option value="">Chọn lớp</option>
                                            <?php foreach ($classes as $class): ?>
                                                <option value="<?php echo $class['id']; ?>">
                                                    <?php echo htmlspecialchars($class['class_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="major" class="form-label">Ngành học</label>
                                        <select class="form-select" id="major" name="major">
                                            <option value="">Chọn ngành</option>
                                            <?php foreach ($majors as $major): ?>
                                                <option value="<?php echo intval($major['id']); ?>">
                                                    <?php echo htmlspecialchars($major['major_code']); ?> - <?php echo htmlspecialchars($major['major_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="academic_year" class="form-label">Khóa học</label>
                                        <input type="text" class="form-control" id="academic_year" name="academic_year"
                                            placeholder="Ví dụ: 2021-2025">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Đang học" selected>Đang học</option>
                                            <option value="Tạm nghỉ">Tạm nghỉ</option>
                                            <option value="Tốt nghiệp">Tốt nghiệp</option>
                                            <option value="Bị đuổi học">Bị đuổi học</option>
                                        </select>
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
                                    <div class="mb-3" id="graduation_date_container" style="display: none;">
                                        <label for="graduation_date" class="form-label">Ngày tốt nghiệp</label>
                                        <input type="date" class="form-control" id="graduation_date" name="graduation_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                        <a href="../student.php" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                    </div>
                </form>
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

        // Preview image
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

        // Xử lý hiển thị/ẩn trường ngày tốt nghiệp
        document.getElementById('status').addEventListener('change', function() {
            const graduationDateContainer = document.getElementById('graduation_date_container');
            const graduationDateInput = document.getElementById('graduation_date');
            
            if (this.value === 'Tốt nghiệp') {
                graduationDateContainer.style.display = 'block';
                graduationDateInput.required = true;
                if (!graduationDateInput.value) {
                    graduationDateInput.value = new Date().toISOString().split('T')[0];
                }
            } else {
                graduationDateContainer.style.display = 'none';
                graduationDateInput.required = false;
            }
        });
    </script>
</body>
</html>
