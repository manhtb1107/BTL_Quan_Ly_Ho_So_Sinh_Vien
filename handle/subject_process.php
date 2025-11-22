<?php
// session_start();
require_once __DIR__ . '/../functions/subject_functions.php';

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateSubject();
        break;
    case 'edit':
        handleEditSubject();
        break;
    case 'delete':
        handleDeleteSubject();
        break;
    case 'bulk_delete':
        handleBulkDeleteSubject();
        break;
    // default:
    //     header("Location: ../views/subject.php?error=Hành động không hợp lệ");
    //     exit();
}

function handleGetAllSubjects() {
    return getAllSubjects();
    // Xử lý hiển thị danh sách subjects
}
function handleGetSubjectById($id) {
    return getSubjectById($id);
}

/**
 * Xử lý tạo học phần mới
 */
function handleCreateSubject () {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/subject.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['subject_code']) || !isset($_POST['subject_name']) || !isset($_POST['major_id'])) {
        header("Location: ../views/subject/create_subject.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $data = [
        'subject_code' => trim($_POST['subject_code']),
        'subject_name' => trim($_POST['subject_name']),
        'credits' => intval($_POST['credits'] ?? 3),
        'major_id' => intval($_POST['major_id']),
        'subject_type' => trim($_POST['subject_type'] ?? ''),
        'description' => trim($_POST['description'] ?? '')
    ];
    
    // Validate dữ liệu
    if (empty($data['subject_code']) || empty($data['subject_name']) || empty($data['major_id'])) {
        header("Location: ../views/subject/create_subject.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Gọi hàm thêm học phần
    $result = addSubject($data);

    if ($result) {
        header("Location: ../views/subject.php?success=Thêm môn học thành công");
    } else {
        header("Location: ../views/subject/create_subject.php?error=Có lỗi xảy ra khi thêm môn học");
    }
    exit();
}

/**
 * Xử lý chỉnh sửa học phần
 */
function handleEditSubject() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/subject.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['id']) || !isset($_POST['subject_code']) || !isset($_POST['subject_name']) || !isset($_POST['major_id'])) {
        header("Location: ../views/subject.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = intval($_POST['id']);
    $data = [
        'subject_code' => trim($_POST['subject_code']),
        'subject_name' => trim($_POST['subject_name']),
        'credits' => intval($_POST['credits'] ?? 3),
        'major_id' => intval($_POST['major_id']),
        'subject_type' => trim($_POST['subject_type'] ?? ''),
        'description' => trim($_POST['description'] ?? '')
    ];
    
    // Validate dữ liệu
    if (empty($data['subject_code']) || empty($data['subject_name']) || empty($data['major_id'])) {
        header("Location: ../views/subject/edit_subject.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Gọi function để cập nhật học phần
    $result = updateSubject($id, $data);
    
    if ($result) {
        header("Location: ../views/subject.php?success=Cập nhật môn học thành công");
    } else {
        header("Location: ../views/subject/edit_subject.php?id=" . $id . "&error=Cập nhật môn học thất bại");
    }
    exit();
}

/**
 * Xử lý xóa học phần
 */
function handleDeleteSubject() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/subject.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/subject.php?error=Không tìm thấy ID học phần");
        exit();
    }
    
    $id = $_GET['id'];
    
    // Validate ID là số
    if (!is_numeric($id)) {
        header("Location: ../views/subject.php?error=ID học phần không hợp lệ");
        exit();
    }
    
    // Gọi function để xóa học phần
    $result = deleteSubject($id);

    if ($result) {
        header("Location: ../views/subject.php?success=Xóa học phần thành công");
    } else {
        header("Location: ../views/subject.php?error=Xóa học phần thất bại");
    }
    exit();
}

/**
 * Xử lý xóa nhiều môn học
 */
function handleBulkDeleteSubject() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/subject.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
        header("Location: ../views/subject.php?error=Không có môn học nào được chọn");
        exit();
    }
    
    $ids = array_map('intval', $_POST['ids']);
    $successCount = 0;
    $failCount = 0;
    
    foreach ($ids as $id) {
        if (deleteSubject($id)) {
            $successCount++;
        } else {
            $failCount++;
        }
    }
    
    if ($successCount > 0 && $failCount === 0) {
        header("Location: ../views/subject.php?success=Đã xóa thành công $successCount môn học");
    } elseif ($successCount > 0 && $failCount > 0) {
        header("Location: ../views/subject.php?success=Đã xóa $successCount môn học, $failCount thất bại");
    } else {
        header("Location: ../views/subject.php?error=Xóa môn học thất bại");
    }
    exit();
}
?>
