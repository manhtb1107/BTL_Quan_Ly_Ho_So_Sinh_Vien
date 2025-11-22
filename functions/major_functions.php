<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách ngành học từ database
 * @return array Danh sách ngành học
 */
function getAllMajorsList() {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM majors ORDER BY major_name";
    $result = mysqli_query($conn, $sql);
    
    $majors = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $majors[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $majors;
}

/**
 * Thêm ngành học mới
 * @param string $major_code Mã ngành
 * @param string $major_name Tên ngành
 * @param string $description Mô tả
 * @return bool True nếu thành công, False nếu thất bại
 */
function addMajor($major_code, $major_name, $description = '') {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO majors (major_code, major_name, description) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $major_code, $major_name, $description);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin một ngành học theo ID
 * @param int $id ID của ngành học
 * @return array|null Thông tin ngành học hoặc null nếu không tìm thấy
 */
function getMajorById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM majors WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $major = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $major;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin ngành học
 * @param int $id ID của ngành học
 * @param string $major_code Mã ngành
 * @param string $major_name Tên ngành
 * @param string $description Mô tả
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateMajor($id, $major_code, $major_name, $description = '') {
    $conn = getDbConnection();
    
    $sql = "UPDATE majors SET major_code = ?, major_name = ?, description = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $major_code, $major_name, $description, $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa ngành học theo ID
 * @param int $id ID của ngành học cần xóa
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteMajor($id) {
    $conn = getDbConnection();
    
    // Kiểm tra xem có sinh viên nào đang học ngành này không
    $checkSql = "SELECT COUNT(*) as count FROM students WHERE major = (SELECT major_name FROM majors WHERE id = ?)";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "i", $id);
        mysqli_stmt_execute($checkStmt);
        $result = mysqli_stmt_get_result($checkStmt);
        $row = mysqli_fetch_assoc($result);
        
        if ($row['count'] > 0) {
            mysqli_stmt_close($checkStmt);
            mysqli_close($conn);
            return false; // Không thể xóa vì còn sinh viên
        }
        
        mysqli_stmt_close($checkStmt);
    }
    
    $sql = "DELETE FROM majors WHERE id = ?";
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
 * Kiểm tra mã ngành đã tồn tại chưa
 * @param string $major_code Mã ngành
 * @param int $exclude_id ID cần loại trừ (khi cập nhật)
 * @return bool True nếu đã tồn tại, False nếu chưa
 */
function majorCodeExists($major_code, $exclude_id = 0) {
    $conn = getDbConnection();
    
    $sql = "SELECT id FROM majors WHERE major_code = ? AND id != ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $major_code, $exclude_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $exists = mysqli_num_rows($result) > 0;
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $exists;
    }
    
    mysqli_close($conn);
    return false;
}

?>

