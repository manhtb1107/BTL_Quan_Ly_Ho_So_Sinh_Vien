<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách subjects từ database
 * @return array Danh sách subjects
 */
function getAllSubjects() {
    $conn = getDbConnection();
    
    // Truy vấn lấy tất cả subjects
    $sql = "SELECT * FROM subject ORDER BY subject_name";
    $result = mysqli_query($conn, $sql);

    $subjects = [];
    if ($result && mysqli_num_rows($result) > 0) {
        // Lặp qua từng dòng trong kết quả truy vấn $result
        while ($row = mysqli_fetch_assoc($result)) { 
            $subjects[] = $row; // Thêm mảng $row vào cuối mảng $subjects
        }
    }
    
    mysqli_close($conn);
    return $subjects;
}

/**
 * Thêm subject mới
 * @param array $data Dữ liệu môn học
 * @return bool True nếu thành công, False nếu thất bại
 */
function addSubject($data) {
    $conn = getDbConnection();

    // Kiểm tra xem bảng có cột nào
    $checkSql = "SHOW COLUMNS FROM subject";
    $checkResult = mysqli_query($conn, $checkSql);
    $columns = [];
    while ($row = mysqli_fetch_assoc($checkResult)) {
        $columns[] = $row['Field'];
    }
    
    // Tạo SQL động dựa trên các cột có sẵn
    $fields = ['subject_code', 'subject_name'];
    $values = [$data['subject_code'], $data['subject_name']];
    $types = 'ss';
    
    if (in_array('credits', $columns)) {
        $fields[] = 'credits';
        $values[] = $data['credits'];
        $types .= 'i';
    }
    
    if (in_array('major_id', $columns)) {
        $fields[] = 'major_id';
        $values[] = $data['major_id'];
        $types .= 'i';
    }
    
    if (in_array('subject_type', $columns) && !empty($data['subject_type'])) {
        $fields[] = 'subject_type';
        $values[] = $data['subject_type'];
        $types .= 's';
    }
    
    if (in_array('description', $columns) && !empty($data['description'])) {
        $fields[] = 'description';
        $values[] = $data['description'];
        $types .= 's';
    }
    
    $fieldList = implode(', ', $fields);
    $placeholders = implode(', ', array_fill(0, count($fields), '?'));
    
    $sql = "INSERT INTO subject ($fieldList) VALUES ($placeholders)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        $success = mysqli_stmt_execute($stmt);
        
        if (!$success) {
            error_log("SQL Error: " . mysqli_stmt_error($stmt));
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin một subject theo ID
 * @param int $id ID của subject
 * @return array|null Thông tin subject hoặc null nếu không tìm thấy
 */
function getSubjectById($id) {
    $conn = getDbConnection();

    $sql = "SELECT * FROM subject WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $subject = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $subject;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin subject
 * @param int $id ID của subject
 * @param array $data Dữ liệu môn học
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateSubject($id, $data) {
    $conn = getDbConnection();
    
    // Kiểm tra xem bảng có cột nào
    $checkSql = "SHOW COLUMNS FROM subject";
    $checkResult = mysqli_query($conn, $checkSql);
    $columns = [];
    while ($row = mysqli_fetch_assoc($checkResult)) {
        $columns[] = $row['Field'];
    }
    
    // Tạo SQL động dựa trên các cột có sẵn
    $updates = ['subject_code = ?', 'subject_name = ?'];
    $values = [$data['subject_code'], $data['subject_name']];
    $types = 'ss';
    
    if (in_array('credits', $columns)) {
        $updates[] = 'credits = ?';
        $values[] = $data['credits'];
        $types .= 'i';
    }
    
    if (in_array('major_id', $columns)) {
        $updates[] = 'major_id = ?';
        $values[] = $data['major_id'];
        $types .= 'i';
    }
    
    if (in_array('subject_type', $columns)) {
        $updates[] = 'subject_type = ?';
        $values[] = $data['subject_type'] ?? '';
        $types .= 's';
    }
    
    if (in_array('description', $columns)) {
        $updates[] = 'description = ?';
        $values[] = $data['description'] ?? '';
        $types .= 's';
    }
    
    $values[] = $id;
    $types .= 'i';
    
    $updateList = implode(', ', $updates);
    $sql = "UPDATE subject SET $updateList WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        $success = mysqli_stmt_execute($stmt);
        
        if (!$success) {
            error_log("SQL Error: " . mysqli_stmt_error($stmt));
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa subject theo ID
 * @param int $id ID của subject cần xóa
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteSubject($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM subject WHERE id = ?";
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
 * Lấy danh sách môn học theo ngành
 * @param int $majorId ID của ngành học
 * @return array Danh sách môn học
 */
function getSubjectsByMajor($majorId) {
    $conn = getDbConnection();
    
    $sql = "SELECT s.*, m.major_name, m.major_code 
            FROM subject s 
            LEFT JOIN majors m ON s.major_id = m.id 
            WHERE s.major_id = ? 
            ORDER BY s.subject_name";
    $stmt = mysqli_prepare($conn, $sql);
    
    $subjects = [];
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $majorId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $subjects[] = $row;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return $subjects;
}
?>
