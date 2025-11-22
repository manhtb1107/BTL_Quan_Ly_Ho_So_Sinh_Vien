<?php
session_start();
require_once '../functions/db_connection.php';
require_once '../functions/auth.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    handleLogin();
}

function handleLogin() {
    $conn = getDbConnection();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng nhập đầy đủ username và password!';
        header('Location: ../index.php');
        exit();
    }

    $user = authenticateUser($conn, $username, $password);
    
    // Kiểm tra tài khoản bị khóa
    if ($user === 'inactive') {
        $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên!';
        mysqli_close($conn);
        header('Location: ../index.php');
        exit();
    }
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'] ?? 'teacher';
        $_SESSION['success'] = 'Đăng nhập thành công!';
        mysqli_close($conn);
        
        // Redirect theo role
        if ($user['role'] === 'admin') {
            header('Location: ../views/admin_dashboard.php');
        } else {
            header('Location: ../views/teacher_dashboard.php');
        }
        exit();
    }

    $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
    mysqli_close($conn);
    header('Location: ../index.php');
    exit();
}
?>