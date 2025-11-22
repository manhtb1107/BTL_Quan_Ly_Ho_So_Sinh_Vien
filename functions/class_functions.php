<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách class từ database
 * @return array Danh sách lớp
 */
function getAllClass() {
    $conn = getDbConnection();
    
    // Truy vấn lấy tất cả lớp học
    $sql = "SELECT * FROM class ORDER BY class_name";
    $result = mysqli_query($conn, $sql);
    
    $classes = [];
    if ($result && mysqli_num_rows($result) > 0) {
        // Lặp qua từng dòng trong kết quả truy vấn $result
        while ($row = mysqli_fetch_assoc($result)) { 
            $classes[] = $row; // Thêm mảng $row vào cuối mảng $classes
        }
    }
    
    mysqli_close($conn);
    return $classes;
}

/**
 * Thêm lớp học mới
 * @param string $class_code Mã lớp
 * @param string $class_name Tên lớp
 * @param string $major Ngành học
 * @param string $academic_year Niên khóa
 * @return bool True nếu thành công, False nếu thất bại
 */
function addClass($class_code, $class_name, $major, $academic_year) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO class (class_code, class_name, major, academic_year) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $class_code, $class_name, $major, $academic_year);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin một lớp học theo ID
 * @param int $id ID của lớp học
 * @return array|null Thông tin lớp học hoặc null nếu không tìm thấy
 */
function getClassById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM class WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $class = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $class;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin lớp học
 * @param int $id ID của lớp học
 * @param string $class_code Mã lớp mới
 * @param string $class_name Tên lớp mới
 * @param string $major Ngành học mới
 * @param string $academic_year Niên khóa mới
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateClass($id, $class_code, $class_name, $major, $academic_year) {
    $conn = getDbConnection();
    
    $sql = "UPDATE class SET class_code = ?, class_name = ?, major = ?, academic_year = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $class_code, $class_name, $major, $academic_year, $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa lớp học theo ID
 * @param int $id ID của lớp học cần xóa
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteClass($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM class WHERE id = ?";
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
?>
