<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả người dùng
 * @return array Danh sách người dùng
 */
function getAllUsers() {
    $conn = getDbConnection();
    
    $sql = "SELECT id, username, email, full_name, 
            COALESCE(role, 'teacher') as role, 
            COALESCE(is_active, 1) as is_active, 
            created_at 
            FROM users 
            ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    $users = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $users;
}

/**
 * Lấy thông tin người dùng theo ID
 * @param int $id ID người dùng
 * @return array|null Thông tin người dùng hoặc null
 */
function getUserById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT id, username, email, full_name, role, is_active, created_at FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $user;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Kiểm tra username đã tồn tại chưa
 * @param string $username Username cần kiểm tra
 * @param int|null $excludeId ID người dùng cần loại trừ (dùng khi edit)
 * @return bool True nếu đã tồn tại, False nếu chưa
 */
function usernameExists($username, $excludeId = null) {
    $conn = getDbConnection();
    
    if ($excludeId) {
        $sql = "SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $username, $excludeId);
    } else {
        $sql = "SELECT id FROM users WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
    }
    
    $exists = false;
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return $exists;
}

/**
 * Thêm người dùng mới
 * @param array $data Dữ liệu người dùng
 * @return bool True nếu thành công, False nếu thất bại
 */
function createUser($data) {
    $conn = getDbConnection();
    
    // Hash password với MD5
    $hashedPassword = md5($data['password']);
    
    $sql = "INSERT INTO users (username, password, email, full_name, role, is_active) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        $isActive = isset($data['is_active']) ? intval($data['is_active']) : 1;
        
        mysqli_stmt_bind_param($stmt, "sssssi", 
            $data['username'],
            $hashedPassword,
            $data['email'],
            $data['full_name'],
            $data['role'],
            $isActive
        );
        
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Cập nhật thông tin người dùng
 * @param int $id ID người dùng
 * @param array $data Dữ liệu cập nhật
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateUser($id, $data) {
    $conn = getDbConnection();
    
    // Nếu có password mới thì hash, không thì không update password
    if (!empty($data['password'])) {
        $sql = "UPDATE users SET username = ?, password = ?, email = ?, full_name = ?, role = ?, is_active = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            $hashedPassword = md5($data['password']);
            $isActive = isset($data['is_active']) ? intval($data['is_active']) : 1;
            
            mysqli_stmt_bind_param($stmt, "sssssii", 
                $data['username'],
                $hashedPassword,
                $data['email'],
                $data['full_name'],
                $data['role'],
                $isActive,
                $id
            );
            
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $success;
        }
    } else {
        // Không update password
        $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, role = ?, is_active = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            $isActive = isset($data['is_active']) ? intval($data['is_active']) : 1;
            
            mysqli_stmt_bind_param($stmt, "ssssii", 
                $data['username'],
                $data['email'],
                $data['full_name'],
                $data['role'],
                $isActive,
                $id
            );
            
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $success;
        }
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa người dùng
 * @param int $id ID người dùng
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteUser($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Thay đổi trạng thái người dùng (active/inactive)
 * @param int $id ID người dùng
 * @param int $status Trạng thái (1: active, 0: inactive)
 * @return bool True nếu thành công, False nếu thất bại
 */
function toggleUserStatus($id, $status) {
    $conn = getDbConnection();
    
    $sql = "UPDATE users SET is_active = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $status, $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Lấy số lượng người dùng theo role
 * @param string $role Role cần đếm
 * @return int Số lượng người dùng
 */
function countUsersByRole($role) {
    $conn = getDbConnection();
    
    $sql = "SELECT COUNT(*) as count FROM users WHERE role = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    $count = 0;
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $count = $row['count'];
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return $count;
}
?>
