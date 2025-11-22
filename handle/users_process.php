<?php
session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/users_functions.php';

// Kiểm tra quyền Admin
requireAdmin(__DIR__ . '/../index.php');

// Xác định action
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateUser();
        break;
    case 'edit':
        handleEditUser();
        break;
    case 'delete':
        handleDeleteUser();
        break;
    case 'bulk_delete':
        handleBulkDeleteUser();
        break;
    case 'toggle_status':
        handleToggleStatus();
        break;
    default:
        header("Location: ../views/users.php?error=Hành động không hợp lệ");
        exit();
}

/**
 * Xử lý tạo người dùng mới
 */
function handleCreateUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/users.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Validate dữ liệu
    $required = ['username', 'password', 'confirm_password', 'role'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            header("Location: ../views/users/create_users.php?error=Vui lòng điền đầy đủ thông tin bắt buộc");
            exit();
        }
    }
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $email = trim($_POST['email'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $role = trim($_POST['role']);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate username
    if (strlen($username) < 3) {
        header("Location: ../views/users/create_users.php?error=Username phải có ít nhất 3 ký tự");
        exit();
    }
    
    // Kiểm tra username đã tồn tại
    if (usernameExists($username)) {
        header("Location: ../views/users/create_users.php?error=Username đã tồn tại");
        exit();
    }
    
    // Validate password
    if (strlen($password) < 6) {
        header("Location: ../views/users/create_users.php?error=Mật khẩu phải có ít nhất 6 ký tự");
        exit();
    }
    
    if ($password !== $confirmPassword) {
        header("Location: ../views/users/create_users.php?error=Mật khẩu xác nhận không khớp");
        exit();
    }
    
    // Validate role
    if (!in_array($role, ['admin', 'teacher'])) {
        header("Location: ../views/users/create_users.php?error=Vai trò không hợp lệ");
        exit();
    }
    
    // Tạo user
    $data = [
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'full_name' => $fullName,
        'role' => $role,
        'is_active' => $isActive
    ];
    
    $result = createUser($data);
    
    if ($result) {
        header("Location: ../views/users.php?success=Thêm người dùng thành công");
    } else {
        header("Location: ../views/users/create_users.php?error=Có lỗi xảy ra khi thêm người dùng");
    }
    exit();
}

/**
 * Xử lý chỉnh sửa người dùng
 */
function handleEditUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/users.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Validate dữ liệu
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        header("Location: ../views/users.php?error=Không tìm thấy ID người dùng");
        exit();
    }
    
    $id = intval($_POST['id']);
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate
    if (empty($username) || empty($role)) {
        header("Location: ../views/users/edit_users.php?id=$id&error=Vui lòng điền đầy đủ thông tin bắt buộc");
        exit();
    }
    
    // Kiểm tra username đã tồn tại (trừ user hiện tại)
    if (usernameExists($username, $id)) {
        header("Location: ../views/users/edit_users.php?id=$id&error=Username đã tồn tại");
        exit();
    }
    
    // Validate password nếu có thay đổi
    if (!empty($password)) {
        if (strlen($password) < 6) {
            header("Location: ../views/users/edit_users.php?id=$id&error=Mật khẩu phải có ít nhất 6 ký tự");
            exit();
        }
        
        if ($password !== $confirmPassword) {
            header("Location: ../views/users/edit_users.php?id=$id&error=Mật khẩu xác nhận không khớp");
            exit();
        }
    }
    
    // Validate role
    if (!in_array($role, ['admin', 'teacher'])) {
        header("Location: ../views/users/edit_users.php?id=$id&error=Vai trò không hợp lệ");
        exit();
    }
    
    // Cập nhật user
    $data = [
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'full_name' => $fullName,
        'role' => $role,
        'is_active' => $isActive
    ];
    
    $result = updateUser($id, $data);
    
    if ($result) {
        header("Location: ../views/users.php?success=Cập nhật người dùng thành công");
    } else {
        header("Location: ../views/users/edit_users.php?id=$id&error=Có lỗi xảy ra khi cập nhật");
    }
    exit();
}

/**
 * Xử lý xóa người dùng
 */
function handleDeleteUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/users.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/users.php?error=Không tìm thấy ID người dùng");
        exit();
    }
    
    $id = intval($_GET['id']);
    
    // Không cho phép xóa chính mình
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if ($id == $_SESSION['user_id']) {
        header("Location: ../views/users.php?error=Không thể xóa tài khoản của chính bạn");
        exit();
    }
    
    $result = deleteUser($id);
    
    if ($result) {
        header("Location: ../views/users.php?success=Xóa người dùng thành công");
    } else {
        header("Location: ../views/users.php?error=Có lỗi xảy ra khi xóa người dùng");
    }
    exit();
}

/**
 * Xử lý xóa nhiều người dùng
 */
function handleBulkDeleteUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/users.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
        header("Location: ../views/users.php?error=Không có người dùng nào được chọn");
        exit();
    }
    
    // Không cho phép xóa chính mình
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $ids = array_map('intval', $_POST['ids']);
    $currentUserId = $_SESSION['user_id'];
    
    // Loại bỏ ID của user hiện tại
    $ids = array_filter($ids, function($id) use ($currentUserId) {
        return $id != $currentUserId;
    });
    
    if (empty($ids)) {
        header("Location: ../views/users.php?error=Không thể xóa tài khoản của chính bạn");
        exit();
    }
    
    $successCount = 0;
    $failCount = 0;
    
    foreach ($ids as $id) {
        if (deleteUser($id)) {
            $successCount++;
        } else {
            $failCount++;
        }
    }
    
    if ($successCount > 0 && $failCount === 0) {
        header("Location: ../views/users.php?success=Đã xóa thành công $successCount người dùng");
    } elseif ($successCount > 0 && $failCount > 0) {
        header("Location: ../views/users.php?success=Đã xóa $successCount người dùng, $failCount thất bại");
    } else {
        header("Location: ../views/users.php?error=Xóa người dùng thất bại");
    }
    exit();
}

/**
 * Xử lý thay đổi trạng thái người dùng
 */
function handleToggleStatus() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/users.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_GET['id']) || !isset($_GET['status'])) {
        header("Location: ../views/users.php?error=Thiếu thông tin");
        exit();
    }
    
    $id = intval($_GET['id']);
    $status = intval($_GET['status']);
    
    $result = toggleUserStatus($id, $status);
    
    if ($result) {
        $message = $status ? "Kích hoạt" : "Khóa";
        header("Location: ../views/users.php?success=$message người dùng thành công");
    } else {
        header("Location: ../views/users.php?error=Có lỗi xảy ra");
    }
    exit();
}
?>
