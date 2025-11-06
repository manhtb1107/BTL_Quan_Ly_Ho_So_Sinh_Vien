<?php
require_once __DIR__ . '/../functions/major_functions.php';

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateMajor();
        break;
    case 'edit':
        handleEditMajor();
        break;
    case 'delete':
        handleDeleteMajor();
        break;
}

function handleGetAllMajors() {
    return getAllMajorsList();
}

function handleGetMajorById($id) {
    return getMajorById($id);
}

/**
 * Xử lý tạo ngành học mới
 */
function handleCreateMajor() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/major.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['major_code']) || !isset($_POST['major_name'])) {
        header("Location: ../views/major/create_major.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $major_code = trim($_POST['major_code']);
    $major_name = trim($_POST['major_name']);
    $description = trim($_POST['description'] ?? '');
    
    // Validate dữ liệu
    if (empty($major_code) || empty($major_name)) {
        header("Location: ../views/major/create_major.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    
    // Kiểm tra mã ngành đã tồn tại chưa
    if (majorCodeExists($major_code)) {
        header("Location: ../views/major/create_major.php?error=Mã ngành đã tồn tại");
        exit();
    }
    
    // Gọi hàm thêm ngành học
    $result = addMajor($major_code, $major_name, $description);
    
    if ($result) {
        header("Location: ../views/major.php?success=Thêm ngành học thành công");
    } else {
        header("Location: ../views/major/create_major.php?error=Có lỗi xảy ra khi thêm ngành học");
    }
    exit();
}

/**
 * Xử lý chỉnh sửa ngành học
 */
function handleEditMajor() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/major.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['id']) || !isset($_POST['major_code']) || !isset($_POST['major_name'])) {
        header("Location: ../views/major.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = $_POST['id'];
    $major_code = trim($_POST['major_code']);
    $major_name = trim($_POST['major_name']);
    $description = trim($_POST['description'] ?? '');
    
    // Validate dữ liệu
    if (empty($major_code) || empty($major_name)) {
        header("Location: ../views/major/edit_major.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    
    // Kiểm tra mã ngành đã tồn tại chưa (trừ chính nó)
    if (majorCodeExists($major_code, $id)) {
        header("Location: ../views/major/edit_major.php?id=" . $id . "&error=Mã ngành đã tồn tại");
        exit();
    }
    
    // Gọi function để cập nhật ngành học
    $result = updateMajor($id, $major_code, $major_name, $description);
    
    if ($result) {
        header("Location: ../views/major.php?success=Cập nhật ngành học thành công");
    } else {
        header("Location: ../views/major/edit_major.php?id=" . $id . "&error=Cập nhật ngành học thất bại");
    }
    exit();
}

/**
 * Xử lý xóa ngành học
 */
function handleDeleteMajor() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/major.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/major.php?error=Không tìm thấy ID ngành học");
        exit();
    }
    
    $id = $_GET['id'];
    
    // Validate ID là số
    if (!is_numeric($id)) {
        header("Location: ../views/major.php?error=ID ngành học không hợp lệ");
        exit();
    }
    
    // Gọi function để xóa ngành học
    $result = deleteMajor($id);
    
    if ($result) {
        header("Location: ../views/major.php?success=Xóa ngành học thành công");
    } else {
        header("Location: ../views/major.php?error=Không thể xóa ngành học. Có thể còn sinh viên đang học ngành này");
    }
    exit();
}

?>

