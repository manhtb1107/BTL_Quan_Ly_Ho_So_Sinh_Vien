<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách grades từ database với thông tin sinh viên và môn học
 * @return array Danh sách grades
 */
function getAllGrades() {
    $conn = getDbConnection();
    
    // Truy vấn lấy tất cả grades với join bảng students và subject
    $sql = "SELECT g.id, g.student_id, g.subject_id, g.midterm_score, g.final_score, g.total_score, g.letter_grade,
                   s.student_code, s.student_name, sub.subject_code, sub.subject_name 
            FROM grade g
            LEFT JOIN students s ON g.student_id = s.id
            LEFT JOIN subject sub ON g.subject_id = sub.id
            ORDER BY g.id";
    $result = mysqli_query($conn, $sql);
    
    $grades = [];
    if ($result && mysqli_num_rows($result) > 0) {
        // Lặp qua từng dòng trong kết quả truy vấn $result
        while ($row = mysqli_fetch_assoc($result)) { 
            $grades[] = $row; // Thêm mảng $row vào cuối mảng $grades
        }
    }
    
    mysqli_close($conn);
    return $grades;
}

/**
 * Thêm grade mới
 * @param int $student_id ID sinh viên
 * @param int $subject_id ID môn học
 * @param float $grade Điểm
 * @return bool True nếu thành công, False nếu thất bại
 */
function addGrade($student_id, $subject_id, $grade) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO grade (student_id, subject_id, midterm_score, final_score, total_score) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iiddd", $student_id, $subject_id, $grade, $grade, $grade);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Lấy grade theo ID
 * @param int $id ID của grade
 * @return array|null Thông tin grade hoặc null nếu không tồn tại
 */
function getGradeById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT g.id, g.student_id, g.subject_id, g.midterm_score, g.final_score, g.total_score, g.letter_grade,
                   s.student_code, s.student_name, sub.subject_code, sub.subject_name
            FROM grade g
            LEFT JOIN students s ON g.student_id = s.id
            LEFT JOIN subject sub ON g.subject_id = sub.id
            WHERE g.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $grade = null;
        if ($result && mysqli_num_rows($result) > 0) {
            $grade = mysqli_fetch_assoc($result);
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $grade;
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật grade
 * @param int $id ID của grade
 * @param int $student_id ID sinh viên
 * @param int $subject_id ID môn học
 * @param float $grade Điểm
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateGrade($id, $student_id, $subject_id, $grade) {
    $conn = getDbConnection();
    
    $sql = "UPDATE grade SET student_id = ?, subject_id = ?, total_score = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iidi", $student_id, $subject_id, $grade, $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa grade
 * @param int $id ID của grade
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteGrade($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM grade WHERE id = ?";
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
 * Kiểm tra xem sinh viên đã có điểm môn học này chưa
 * @param int $student_id ID sinh viên
 * @param int $subject_id ID môn học
 * @return bool True nếu đã tồn tại, False nếu chưa
 */
function checkGradeExists($student_id, $subject_id) {
    $conn = getDbConnection();
    
    $sql = "SELECT id FROM grade WHERE student_id = ? AND subject_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $student_id, $subject_id);
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

/**
 * Lấy tất cả sinh viên để hiển thị trong dropdown
 * @return array Danh sách sinh viên
 */
function getAllStudentsForDropdown() {
    $conn = getDbConnection();
    
    $sql = "SELECT id, student_code, student_name FROM students ORDER BY student_name";
    $result = mysqli_query($conn, $sql);
    
    $students = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $students;
}

/**
 * Lấy tất cả môn học để hiển thị trong dropdown
 * @return array Danh sách môn học
 */
function getAllSubjectsForDropdown() {
    $conn = getDbConnection();
    
    $sql = "SELECT id, subject_code, subject_name FROM subject ORDER BY subject_name";
    $result = mysqli_query($conn, $sql);
    
    $subjects = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $subjects[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $subjects;
}

/**
 * Lấy danh sách điểm theo môn học
 * @param int $subjectId ID môn học
 * @return array Danh sách điểm với thông tin sinh viên và môn học
 */
function getGradesBySubject($subjectId) {
    $conn = getDbConnection();

    $sql = "SELECT g.id, g.student_id, g.subject_id, g.midterm_score, g.final_score, g.total_score, g.letter_grade,
                   s.student_code, s.student_name, sub.subject_code, sub.subject_name 
            FROM grade g
            LEFT JOIN students s ON g.student_id = s.id
            LEFT JOIN subject sub ON g.subject_id = sub.id
            WHERE g.subject_id = ?
            ORDER BY s.student_code";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $subjectId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $grades = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $grades[] = $row;
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $grades;
    }

    mysqli_close($conn);
    return [];
}
?>
