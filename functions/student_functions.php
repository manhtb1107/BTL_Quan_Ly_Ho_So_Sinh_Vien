<?php
require_once 'db_connection.php';

/**
 * Validate và chuyển đổi giá trị ngày thành định dạng YYYY-MM-DD hoặc NULL
 * @param mixed $dateValue Giá trị ngày cần validate
 * @return string|null Định dạng YYYY-MM-DD hoặc NULL nếu không hợp lệ
 */
function validateDate($dateValue) {
    if (empty($dateValue) || $dateValue === null || $dateValue === '') {
        return null;
    }
    
    // Nếu là chuỗi rỗng sau khi trim
    $dateValue = trim($dateValue);
    if (empty($dateValue)) {
        return null;
    }
    
    // Kiểm tra nếu chỉ là năm (4 chữ số)
    if (preg_match('/^\d{4}$/', $dateValue)) {
        // Chuyển năm thành ngày đầu năm: YYYY-01-01
        return $dateValue . '-01-01';
    }
    
    // Kiểm tra định dạng YYYY-MM-DD
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
        // Validate ngày có hợp lệ không
        $date = DateTime::createFromFormat('Y-m-d', $dateValue);
        if ($date && $date->format('Y-m-d') === $dateValue) {
            return $dateValue;
        }
    }
    
    // Thử parse với các định dạng khác
    $timestamp = strtotime($dateValue);
    if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
    }
    
    // Nếu không parse được, trả về null
    return null;
}

/**
 * Lấy tất cả danh sách sinh viên từ database với thông tin đầy đủ
 * @return array Danh sách sinh viên
 */
function getAllStudents() {
    $conn = getDbConnection();
    
    // Truy vấn lấy tất cả sinh viên với thông tin lớp học và ngành học
    $sql = "SELECT s.*, c.class_name, c.class_code, m.major_name, m.major_code
            FROM students s 
            LEFT JOIN class c ON s.class_id = c.id 
            LEFT JOIN majors m ON s.major = m.id
            ORDER BY s.id";
    $result = mysqli_query($conn, $sql);
    
    $students = [];
    if ($result && mysqli_num_rows($result) > 0) {
        // Lặp qua từng dòng trong kết quả truy vấn $result
        while ($row = mysqli_fetch_assoc($result)) { 
            $students[] = $row; // Thêm mảng $row vào cuối mảng $students
        }
    }
    
    mysqli_close($conn);
    return $students;
}

/**
 * Thêm sinh viên mới với thông tin đầy đủ
 * @param array $studentData Mảng chứa thông tin sinh viên
 * @return bool True nếu thành công, False nếu thất bại
 */
function addStudent($studentData) {
    $conn = getDbConnection();
    
    // Validate và chuyển đổi ngày
    $dateOfBirth = validateDate($studentData['date_of_birth'] ?? null);
    $enrollmentDate = validateDate($studentData['enrollment_date'] ?? null);
    
    $sql = "INSERT INTO students (
        student_code, student_name, full_name, image, date_of_birth, gender, 
        phone, email, address, hometown, id_card, class_id, major, 
        academic_year, status, enrollment_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        $imagePath = $studentData['image'] ?? null;
        mysqli_stmt_bind_param($stmt, "ssssssssssssisss", 
            $studentData['student_code'],
            $studentData['student_name'],
            $studentData['full_name'],
            $imagePath,
            $dateOfBirth,
            $studentData['gender'],
            $studentData['phone'],
            $studentData['email'],
            $studentData['address'],
            $studentData['hometown'],
            $studentData['id_card'],
            $studentData['class_id'],
            $studentData['major'],
            $studentData['academic_year'],
            $studentData['status'],
            $enrollmentDate
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
 * Lấy thông tin một sinh viên theo ID với thông tin đầy đủ
 * @param int $id ID của sinh viên
 * @return array|null Thông tin sinh viên hoặc null nếu không tìm thấy
 */
function getStudentById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT s.*, c.class_name, c.class_code, m.major_name, m.major_code
            FROM students s 
            LEFT JOIN class c ON s.class_id = c.id 
            LEFT JOIN majors m ON s.major = m.id
            WHERE s.id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $student = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $student;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin sinh viên
 * @param int $id ID của sinh viên
 * @param array $studentData Mảng chứa thông tin sinh viên cần cập nhật
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateStudent($id, $studentData) {
    $conn = getDbConnection();
    
    // Validate và chuyển đổi ngày
    $dateOfBirth = validateDate($studentData['date_of_birth'] ?? null);
    $enrollmentDate = validateDate($studentData['enrollment_date'] ?? null);
    
    // Validate và chuyển đổi graduation_date nếu có
    $graduationDate = isset($studentData['graduation_date']) ? validateDate($studentData['graduation_date']) : null;
    
    // Chuẩn bị dữ liệu
    $updateData = [
        'student_code' => $studentData['student_code'] ?? '',
        'student_name' => $studentData['student_name'] ?? '',
        'full_name' => $studentData['full_name'] ?? '',
        'image' => $studentData['image'] ?? null,
        'date_of_birth' => $dateOfBirth,
        'gender' => $studentData['gender'] ?? '',
        'phone' => $studentData['phone'] ?? '',
        'email' => $studentData['email'] ?? '',
        'address' => $studentData['address'] ?? '',
        'hometown' => $studentData['hometown'] ?? '',
        'id_card' => $studentData['id_card'] ?? '',
        'class_id' => $studentData['class_id'] ?: null,
        'major' => $studentData['major'] ?? '',
        'academic_year' => $studentData['academic_year'] ?? '',
        'status' => $studentData['status'] ?? 'Đang học',
        'enrollment_date' => $enrollmentDate,
        'graduation_date' => $graduationDate
    ];
    
    $sql = "UPDATE students SET 
        student_code = ?, 
        student_name = ?, 
        full_name = ?, 
        image = ?,
        date_of_birth = ?, 
        gender = ?, 
        phone = ?, 
        email = ?, 
        address = ?, 
        hometown = ?, 
        id_card = ?, 
        class_id = ?, 
        major = ?, 
        academic_year = ?, 
        status = ?, 
        enrollment_date = ?,
        graduation_date = ?
        WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssssssssissssi", 
            $updateData['student_code'],
            $updateData['student_name'],
            $updateData['full_name'],
            $updateData['image'],
            $updateData['date_of_birth'],
            $updateData['gender'],
            $updateData['phone'],
            $updateData['email'],
            $updateData['address'],
            $updateData['hometown'],
            $updateData['id_card'],
            $updateData['class_id'],
            $updateData['major'],
            $updateData['academic_year'],
            $updateData['status'],
            $updateData['enrollment_date'],
            $updateData['graduation_date'],
            $id
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
 * Xóa sinh viên theo ID
 * @param int $id ID của sinh viên cần xóa
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteStudent($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM students WHERE id = ?";
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
 * Lấy danh sách tất cả lớp học
 * @return array Danh sách lớp học
 */
function getAllClasses() {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM class ORDER BY class_name";
    $result = mysqli_query($conn, $sql);
    
    $classes = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $classes[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $classes;
}

/**
 * Lấy danh sách tất cả ngành học
 * @return array Danh sách ngành học
 */
function getAllMajors() {
    $conn = getDbConnection();
    
    $sql = "SELECT id, major_code, major_name FROM majors ORDER BY major_name";
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
 * Tìm kiếm sinh viên theo từ khóa
 * @param string $keyword Từ khóa tìm kiếm
 * @return array Danh sách sinh viên tìm được
 */
function searchStudents($keyword) {
    $conn = getDbConnection();
    
    $sql = "SELECT s.*, c.class_name, c.class_code, m.major_name, m.major_code
            FROM students s 
            LEFT JOIN class c ON s.class_id = c.id 
            LEFT JOIN majors m ON s.major = m.id
            WHERE s.student_code LIKE ? OR s.student_name LIKE ? OR s.full_name LIKE ? 
            OR s.phone LIKE ? OR s.email LIKE ? OR m.major_name LIKE ?
            ORDER BY s.id";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        $searchTerm = "%$keyword%";
        mysqli_stmt_bind_param($stmt, "ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $students;
    }
    
    mysqli_close($conn);
    return [];
}

/**
 * Lấy thống kê sinh viên theo trạng thái
 * @return array Thống kê sinh viên
 */
function getStudentStatistics() {
    $conn = getDbConnection();
    
    $sql = "SELECT 
                status,
                COUNT(*) as count,
                AVG(gpa) as avg_gpa
            FROM students 
            GROUP BY status";
    
    $result = mysqli_query($conn, $sql);
    
    $statistics = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $statistics[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $statistics;
}

/**
 * Lấy danh sách sinh viên theo lớp
 * @param int $classId ID của lớp
 * @return array Danh sách sinh viên
 */
function getStudentsByClass($classId) {
    $conn = getDbConnection();
    
    $sql = "SELECT s.*, c.class_name, c.class_code 
            FROM students s 
            LEFT JOIN class c ON s.class_id = c.id 
            WHERE s.class_id = ? 
            ORDER BY s.student_code";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $classId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $students;
    }
    
    mysqli_close($conn);
    return [];
}

/**
 * Lấy danh sách sinh viên theo ngành
 * @param string $major Tên ngành
 * @return array Danh sách sinh viên
 */
function getStudentsByMajor($major) {
    $conn = getDbConnection();
    
    $sql = "SELECT s.*, c.class_name, c.class_code 
            FROM students s 
            LEFT JOIN class c ON s.class_id = c.id 
            WHERE s.major = ? 
            ORDER BY s.student_code";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $major);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $students;
    }
    
    mysqli_close($conn);
    return [];
}

/**
 * Lấy danh sách sinh viên theo đối tượng ưu tiên
 * @param string $priorityGroup Đối tượng ưu tiên
 * @return array Danh sách sinh viên
 */
function getStudentsByPriority($priorityGroup) {
    $conn = getDbConnection();
    
    $sql = "SELECT s.*, c.class_name, c.class_code 
            FROM students s 
            LEFT JOIN class c ON s.class_id = c.id 
            WHERE s.priority_group = ? 
            ORDER BY s.student_code";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $priorityGroup);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $students;
    }
    
    mysqli_close($conn);
    return [];
}
/**
 * Lấy danh sách nhóm đối tượng ưu tiên phân biệt
 * @return array Danh sách các nhóm ưu tiên (mỗi phần tử là ['priority_group' => ...])
 */
function getDistinctPriorityGroups() {
    $conn = getDbConnection();

    $sql = "SELECT DISTINCT priority_group FROM students WHERE priority_group IS NOT NULL AND priority_group <> '' ORDER BY priority_group";
    $result = mysqli_query($conn, $sql);

    $groups = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $groups[] = $row;
        }
    }

    mysqli_close($conn);
    return $groups;
}

/**
 * Lấy danh sách sinh viên theo trạng thái
 * @param string $status Trạng thái (ví dụ: 'Tốt nghiệp')
 * @return array Danh sách sinh viên
 */
function getStudentsByStatus($status) {
    $conn = getDbConnection();

    $sql = "SELECT s.*, c.class_name, c.class_code 
            FROM students s 
            LEFT JOIN class c ON s.class_id = c.id 
            WHERE s.status = ? 
            ORDER BY s.student_code";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $status);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $students = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $students;
    }

    mysqli_close($conn);
    return [];
}

/**
 * Lấy danh sách sinh viên đã tốt nghiệp
 * @return array Danh sách sinh viên tốt nghiệp
 */
function getGraduatedStudentsList() {
    return getStudentsByStatus('Tốt nghiệp');
}
?>
