<?php
// session_start();
require_once __DIR__ . '/../functions/student_functions.php';

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateStudent();
        break;
    case 'edit':
        handleEditStudent();
        break;
    case 'delete':
        handleDeleteStudent();
        break;
    // default:
    //     header("Location: ../views/student.php?error=Hành động không hợp lệ");
    //     exit();
}
/**
 * Lấy tất cả danh sách sinh viên
 */
function handleGetAllStudents() {
    return getAllStudents();
}

function handleGetStudentById($id) {
    return getStudentById($id);
}

/**
 * Xử lý upload ảnh sinh viên
 * @return string|null Đường dẫn ảnh hoặc null nếu không upload
 */
function handleImageUpload($student_code) {
    if (!isset($_FILES['student_image']) || $_FILES['student_image']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $file = $_FILES['student_image'];
    
    // Kiểm tra lỗi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Kiểm tra kích thước file (tối đa 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    // Kiểm tra loại file
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return null;
    }
    
    // Tạo thư mục uploads/students nếu chưa có
    $uploadDir = __DIR__ . '/../uploads/students/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Tạo tên file mới (student_code_timestamp.extension)
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = $student_code . '_' . time() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return 'uploads/students/' . $fileName;
    }
    
    return null;
}

/**
 * Xóa ảnh cũ khi cập nhật
 */
function deleteOldImage($imagePath) {
    if (!empty($imagePath)) {
        $fullPath = __DIR__ . '/../' . $imagePath;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}

/**
 * Xử lý tạo sinh viên mới
 */
function handleCreateStudent() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/student.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['student_code']) || !isset($_POST['student_name'])) {
        header("Location: ../views/student/create_student.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $student_code = trim($_POST['student_code']);
    $student_name = trim($_POST['student_name']);
    
    // Validate dữ liệu
    if (empty($student_code) || empty($student_name)) {
        header("Location: ../views/student/create_student.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    
    // Xử lý upload ảnh
    $imagePath = handleImageUpload($student_code);
    
    // Chuẩn bị dữ liệu sinh viên
    $studentData = [
        'student_code' => $student_code,
        'student_name' => $student_name,
        'full_name' => trim($_POST['full_name'] ?? ''),
        'date_of_birth' => !empty($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : null,
        'gender' => $_POST['gender'] ?? '',
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'hometown' => trim($_POST['hometown'] ?? ''),
        'id_card' => trim($_POST['id_card'] ?? ''),
        'class_id' => !empty($_POST['class_id']) ? $_POST['class_id'] : null,
        'major' => $_POST['major'] ?? '',
        'academic_year' => trim($_POST['academic_year'] ?? ''),
        'status' => $_POST['status'] ?? 'Đang học',
        'enrollment_date' => !empty($_POST['enrollment_date']) ? trim($_POST['enrollment_date']) : null,
        'image' => $imagePath
    ];
    
    // Gọi hàm thêm sinh viên
    $result = addStudent($studentData);
    
    if ($result) {
        header("Location: ../views/student.php?success=Thêm sinh viên thành công");
    } else {
        header("Location: ../views/student/create_student.php?error=Có lỗi xảy ra khi thêm sinh viên");
    }
    exit();
}

/**
 * Xử lý chỉnh sửa sinh viên
 */
function handleEditStudent() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/student.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['id']) || !isset($_POST['student_code']) || !isset($_POST['student_name'])) {
        header("Location: ../views/student.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = $_POST['id'];
    $student_code = trim($_POST['student_code']);
    
    // Lấy thông tin sinh viên hiện tại để lấy ảnh cũ
    $currentStudent = getStudentById($id);
    $currentImage = $currentStudent['image'] ?? '';
    
    // Xử lý upload ảnh mới (nếu có)
    $newImagePath = handleImageUpload($student_code);
    
    // Nếu có ảnh mới, xóa ảnh cũ và sử dụng ảnh mới
    // Nếu không có ảnh mới, giữ nguyên ảnh cũ
    $imagePath = $newImagePath !== null ? $newImagePath : ($_POST['current_image'] ?? $currentImage);
    
    // Nếu có ảnh mới và có ảnh cũ, xóa ảnh cũ
    if ($newImagePath !== null && !empty($currentImage) && $currentImage !== $newImagePath) {
        deleteOldImage($currentImage);
    }
    
    // Chuẩn bị dữ liệu từ form
    $studentData = [
        'student_code' => $student_code,
        'student_name' => trim($_POST['student_name']),
        'full_name' => trim($_POST['full_name'] ?? ''),
        'date_of_birth' => !empty($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : null,
        'gender' => $_POST['gender'] ?? '',
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'hometown' => trim($_POST['hometown'] ?? ''),
        'id_card' => trim($_POST['id_card'] ?? ''),
        'class_id' => !empty($_POST['class_id']) ? $_POST['class_id'] : null,
        'major' => $_POST['major'] ?? '',
        'academic_year' => trim($_POST['academic_year'] ?? ''),
        'status' => $_POST['status'] ?? 'Đang học',
        'enrollment_date' => !empty($_POST['enrollment_date']) ? trim($_POST['enrollment_date']) : null,
        'image' => $imagePath
    ];
    
    // Xử lý graduation_date nếu có
    if (isset($_POST['graduation_date']) && !empty($_POST['graduation_date'])) {
        $studentData['graduation_date'] = trim($_POST['graduation_date']);
    }
    
    // Validate dữ liệu cơ bản
    if (empty($studentData['student_code']) || empty($studentData['student_name'])) {
        header("Location: ../views/student/edit_student.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin bắt buộc");
        exit();
    }
    
    // Xử lý ngày tốt nghiệp nếu trạng thái là "Tốt nghiệp"
    if ($studentData['status'] === 'Tốt nghiệp') {
        if (isset($_POST['graduation_date']) && !empty(trim($_POST['graduation_date']))) {
            $studentData['graduation_date'] = trim($_POST['graduation_date']);
        } else {
            // Nếu không có ngày tốt nghiệp được chọn, sử dụng ngày hiện tại
            $studentData['graduation_date'] = date('Y-m-d');
        }
    } else {
        // Nếu không phải trạng thái tốt nghiệp, xóa ngày tốt nghiệp
        $studentData['graduation_date'] = null;
    }
    
    // Gọi function để cập nhật sinh viên
    $result = updateStudent($id, $studentData);
    
    if ($result) {
        header("Location: ../views/student.php?success=Cập nhật sinh viên thành công");
    } else {
        header("Location: ../views/edit_student.php?id=" . $id . "&error=Cập nhật sinh viên thất bại");
    }
    exit();
}

/**
 * Xử lý xóa sinh viên
 */
function handleDeleteStudent() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/student.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/student.php?error=Không tìm thấy ID sinh viên");
        exit();
    }
    
    $id = $_GET['id'];
    
    // Validate ID là số
    if (!is_numeric($id)) {
        header("Location: ../views/student.php?error=ID sinh viên không hợp lệ");
        exit();
    }
    
    // Gọi function để xóa sinh viên
    $result = deleteStudent($id);
    
    if ($result) {
        header("Location: ../views/student.php?success=Xóa sinh viên thành công");
    } else {
        header("Location: ../views/student.php?error=Xóa sinh viên thất bại");
    }
    exit();
}
?>
