<?php
// session_start();
require_once __DIR__ . '/../functions/class_functions.php';

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateClass();
        break;
    case 'edit':
        handleEditClass();
        break;
    case 'delete':
        handleDeleteClass();
        break;
    case 'bulk_delete':
        handleBulkDeleteClass();
        break;
    // default:
    //     header("Location: ../views/class.php?error=Hành động không hợp lệ");
    //     exit();
}
/**
 * Lấy tất cả danh sách điểm
 */
function handleGetAllClass() {
    return getAllClass();
}

function handleGetClassById($id) {
    return getClassById($id);
}

/**
 * Xử lý tạo điểm mới
 */
function handleCreateClass() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/class.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['class_code']) || !isset($_POST['class_name']) || !isset($_POST['major']) || !isset($_POST['academic_year'])) {
        header("Location: ../views/class/create_class.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $class_code = trim($_POST['class_code']);
    $class_name = trim($_POST['class_name']);
    $major = trim($_POST['major']);
    $academic_year = trim($_POST['academic_year']);
    
    // Validate dữ liệu
    if (empty($class_code) || empty($class_name) || empty($major) || empty($academic_year)) {
        header("Location: ../views/class/create_class.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    $success = addClass($class_code, $class_name, $major, $academic_year);
    
    if ($success) {
        header("Location: ../views/class.php?success=Thêm lớp thành công");
    } else {
        header("Location: ../views/class/create_class.php?error=Có lỗi xảy ra khi thêm lớp");
    }
    exit();
}

/**
 * Xử lý cập nhật lớp
 */
function handleEditClass() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/class.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['id']) || !isset($_POST['class_code']) || !isset($_POST['class_name']) || !isset($_POST['major']) || !isset($_POST['academic_year'])) {
        header("Location: ../views/class.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = (int)$_POST['id'];
    $class_code = trim($_POST['class_code']);
    $class_name = trim($_POST['class_name']);
    $major = trim($_POST['major']);
    $academic_year = trim($_POST['academic_year']);
    
    // Validate dữ liệu
    if (empty($class_code) || empty($class_name) || empty($major) || empty($academic_year)) {
        header("Location: ../views/class/edit_class.php?id=$id&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    
    $success = updateClass($id, $class_code, $class_name, $major, $academic_year);
    
    if ($success) {
        header("Location: ../views/class.php?success=Cập nhật lớp thành công");
    } else {
        header("Location: ../views/class/edit_class.php?id=$id&error=Có lỗi xảy ra khi cập nhật lớp");
    }
    exit();
}

/**
 * Xử lý xóa điểm
 */
function handleDeleteClass() {
    if (!isset($_GET['id'])) {
        header("Location: ../views/class.php?error=Thiếu ID lớp");
        exit();
    }
    
    $id = (int)$_GET['id'];
    
    // Kiểm tra xem class có tồn tại không
    $class = getClassById($id);
    if (!$class) {
        header("Location: ../views/class.php?error=Lớp không tồn tại");
        exit();
    }
    
    $success = deleteClass($id);
    
    if ($success) {
        header("Location: ../views/class.php?success=Xóa lớp thành công");
    } else {
        header("Location: ../views/class.php?error=Có lỗi xảy ra khi xóa lớp");
    }
    exit();
}

/**
 * Xử lý xóa nhiều lớp học
 */
function handleBulkDeleteClass() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/class.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
        header("Location: ../views/class.php?error=Không có lớp học nào được chọn");
        exit();
    }
    
    $ids = array_map('intval', $_POST['ids']);
    $successCount = 0;
    $failCount = 0;
    
    foreach ($ids as $id) {
        if (deleteClass($id)) {
            $successCount++;
        } else {
            $failCount++;
        }
    }
    
    if ($successCount > 0 && $failCount === 0) {
        header("Location: ../views/class.php?success=Đã xóa thành công $successCount lớp học");
    } elseif ($successCount > 0 && $failCount > 0) {
        header("Location: ../views/class.php?success=Đã xóa $successCount lớp học, $failCount thất bại");
    } else {
        header("Location: ../views/class.php?error=Xóa lớp học thất bại");
    }
    exit();
}
?>
