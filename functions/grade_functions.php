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

/**
 * Cập nhật hoặc thêm điểm hàng loạt
 * @param array $gradesData Mảng dữ liệu điểm
 * @param int $subjectId ID môn học
 * @param string $semester Học kỳ
 * @param string $academicYear Năm học
 * @return bool True nếu thành công
 */
function batchUpdateGrades($gradesData, $subjectId, $semester, $academicYear) {
    $conn = getDbConnection();
    $success = true;
    
    mysqli_begin_transaction($conn);
    
    try {
        foreach ($gradesData as $data) {
            $studentId = (int)$data['student_id'];
            $gradeId = !empty($data['grade_id']) ? (int)$data['grade_id'] : 0;
            $test1Score = !empty($data['test1_score']) ? (float)$data['test1_score'] : null;
            $test2Score = !empty($data['test2_score']) ? (float)$data['test2_score'] : null;
            $attendanceScore = !empty($data['attendance_score']) ? (float)$data['attendance_score'] : null;
            $finalScore = !empty($data['final_score']) ? (float)$data['final_score'] : null;
            
            // Validate điểm từ 0 đến 10
            if ($test1Score !== null && ($test1Score < 0 || $test1Score > 10)) {
                throw new Exception("Điểm kiểm tra 1 phải từ 0 đến 10");
            }
            if ($test2Score !== null && ($test2Score < 0 || $test2Score > 10)) {
                throw new Exception("Điểm kiểm tra 2 phải từ 0 đến 10");
            }
            if ($attendanceScore !== null && ($attendanceScore < 0 || $attendanceScore > 10)) {
                throw new Exception("Điểm chuyên cần phải từ 0 đến 10");
            }
            if ($finalScore !== null && ($finalScore < 0 || $finalScore > 10)) {
                throw new Exception("Điểm cuối kỳ phải từ 0 đến 10");
            }
            
            // Tính tổng điểm: ((Test1 + Test2) / 2) × 30% + Attendance × 10% + Final × 60%
            $totalScore = null;
            if ($test1Score !== null && $test2Score !== null && $attendanceScore !== null && $finalScore !== null) {
                $totalScore = ((($test1Score + $test2Score) / 2) * 0.3) + ($attendanceScore * 0.1) + ($finalScore * 0.6);
            }
            
            // Xác định điểm chữ
            $letterGrade = null;
            if ($totalScore !== null) {
                if ($totalScore >= 8.5) $letterGrade = 'A';
                elseif ($totalScore >= 7.0) $letterGrade = 'B';
                elseif ($totalScore >= 5.5) $letterGrade = 'C';
                elseif ($totalScore >= 4.0) $letterGrade = 'D';
                else $letterGrade = 'F';
            }
            
            if ($gradeId > 0) {
                // Cập nhật điểm hiện có
                $sql = "UPDATE grade SET test1_score = ?, test2_score = ?, attendance_score = ?, final_score = ?, 
                        total_score = ?, letter_grade = ?, semester = ?, academic_year = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "dddddssi", $test1Score, $test2Score, $attendanceScore, $finalScore, 
                                      $totalScore, $letterGrade, $semester, $academicYear, $gradeId);
            } else {
                // Thêm điểm mới (chỉ khi có ít nhất 1 điểm được nhập)
                if ($test1Score !== null || $test2Score !== null || $attendanceScore !== null || $finalScore !== null) {
                    $sql = "INSERT INTO grade (student_id, subject_id, test1_score, test2_score, attendance_score, 
                            final_score, total_score, letter_grade, semester, academic_year) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    // 10 parameters: ii (2) + ddddd (5) + s (1) + ss (2) = iidddddss
                    mysqli_stmt_bind_param($stmt, "iidddddsss", $studentId, $subjectId, $test1Score, $test2Score, 
                                          $attendanceScore, $finalScore, $totalScore, $letterGrade, $semester, $academicYear);
                } else {
                    continue; // Bỏ qua nếu không có điểm nào
                }
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Lỗi khi cập nhật điểm");
            }
            mysqli_stmt_close($stmt);
        }
        
        mysqli_commit($conn);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $success = false;
    }
    
    mysqli_close($conn);
    return $success;
}
?>
