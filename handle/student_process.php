<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../functions/student_functions.php';

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'import_excel':
        // Xử lý import từ file Excel/CSV
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/student.php?error=Phương thức không hợp lệ');
            exit();
        }

        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            header('Location: ../views/student.php?error=Vui lòng chọn file để import');
            exit();
        }

        $file = $_FILES['excel_file'];
        $tmpPath = $file['tmp_name'];
        $origName = $file['name'];

        $result = importStudentsFromSpreadsheet($tmpPath, $origName);

        if ($result['status'] === 'success') {
            $_SESSION['success'] = $result['message'];
        } elseif ($result['status'] === 'partial') {
            $_SESSION['success'] = $result['message'];
            if (!empty($result['errors'])) {
                $_SESSION['error'] = implode(' | ', $result['errors']);
            }
        } else {
            $_SESSION['error'] = $result['message'] . (empty($result['errors']) ? '' : (' | ' . implode(' | ', $result['errors'])));
        }

        header('Location: ../views/student.php');
        exit();
        break;
    case 'create':
        handleCreateStudent();
        break;
    case 'edit':
        handleEditStudent();
        break;
    case 'delete':
        handleDeleteStudent();
        break;
    case 'bulk_delete':
        handleBulkDeleteStudent();
        break;
    // default:
    //     header("Location: ../views/student.php?error=Hành động không hợp lệ");
    //     exit();
}
/**
 * Lấy tất cả danh sách sinh viên
 */
function handleGetAllStudents() {
    return getAllStudents();
}

function handleGetStudentById($id) {
    return getStudentById($id);
}

/**
 * Xử lý upload ảnh sinh viên
 * @return string|null Đường dẫn ảnh hoặc null nếu không upload
 */
function handleImageUpload($student_code) {
    if (!isset($_FILES['student_image']) || $_FILES['student_image']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $file = $_FILES['student_image'];
    
    // Kiểm tra lỗi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Kiểm tra kích thước file (tối đa 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    // Kiểm tra loại file
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return null;
    }
    
    // Tạo thư mục uploads/students nếu chưa có
    $uploadDir = __DIR__ . '/../uploads/students/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Tạo tên file mới (student_code_timestamp.extension)
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = $student_code . '_' . time() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return 'uploads/students/' . $fileName;
    }
    
    return null;
}

/**
 * Xóa ảnh cũ khi cập nhật
 */
function deleteOldImage($imagePath) {
    if (!empty($imagePath)) {
        $fullPath = __DIR__ . '/../' . $imagePath;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}

/**
 * Import students from spreadsheet (Excel .xlsx/.xls or CSV). Columns in the header row should match
 * student table fields (e.g. student_code, student_name, full_name, date_of_birth, gender, phone, email,
 * address, hometown, id_card, class_id / class_code, major / major_id / major_code / major_name, academic_year,
 * status, enrollment_date, graduation_date). Missing columns will be left empty.
 *
 * @param string $tmpPath Temporary uploaded file path
 * @param string $originalName Original filename (to detect extension)
 * @return array ['status'=>'success'|'partial'|'error','message'=>string,'errors'=>array]
 */
function importStudentsFromSpreadsheet($tmpPath, $originalName) {
    $errors = [];
    $successCount = 0;

    // Build lookup maps for classes and majors
    $classes = getAllClasses();
    $class_by_id = [];
    $class_by_code = [];
    foreach ($classes as $c) {
        if (isset($c['id'])) $class_by_id[intval($c['id'])] = $c;
        if (!empty($c['class_code'])) $class_by_code[$c['class_code']] = $c;
    }

    $majors = getAllMajors();
    $major_by_id = [];
    $major_by_code = [];
    $major_by_name = [];
    foreach ($majors as $m) {
        if (isset($m['id'])) $major_by_id[intval($m['id'])] = $m;
        if (!empty($m['major_code'])) $major_by_code[$m['major_code']] = $m;
        if (!empty($m['major_name'])) $major_by_name[$m['major_name']] = $m;
    }

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $rows = [];
    $encoding = 'UTF-8';

    // Try to detect and convert encoding if needed
    if (function_exists('mb_detect_encoding')) {
        $content = file_get_contents($tmpPath);
        $detected = mb_detect_encoding($content, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);
        if ($detected && $detected !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $detected);
            file_put_contents($tmpPath, $content);
        }
    }

    // Handle CSV files (default)
    if ($ext === 'csv' || $ext === '') {
        if (($handle = fopen($tmpPath, 'r')) === false) {
            return ['status'=>'error','message'=>'Không thể mở file CSV','errors'=>[]];
        }

        // Try to detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = ',';  // default
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        }

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            // Clean BOM from first cell of first row if present
            if (count($rows) === 0 && isset($data[0])) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
            }
            // Trim whitespace from all cells
            $data = array_map('trim', $data);
            $rows[] = $data;
        }
        fclose($handle);
    } else {
        // Try PhpSpreadsheet for Excel files if available
        if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmpPath);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray(null, true, true, false);
                // Trim all cells
                foreach ($rows as &$row) {
                    $row = array_map('trim', $row);
                }
            } catch (Exception $e) {
                return ['status'=>'error',
                    'message'=>'Lỗi khi đọc file Excel: ' . $e->getMessage(),
                    'errors'=>[]
                ];
            }
        } else {
            return ['status'=>'error',
                'message'=>'File có định dạng ' . strtoupper($ext) . '. Vui lòng dùng file CSV hoặc cài PhpSpreadsheet để đọc Excel',
                'errors'=>[]
            ];
        }
    }

    if (empty($rows)) {
        return ['status'=>'error','message'=>'File rỗng hoặc không có dữ liệu','errors'=>[]];
    }

    // Header processing (normalize)
    $header = array_map(function($h){ 
        $h = strtolower(trim($h));
        // Map some common variations
        $mapping = [
            'mã sv' => 'student_code',
            'mã sinh viên' => 'student_code',
            'tên đăng nhập' => 'student_name',
            'họ và tên' => 'full_name',
            'họ tên' => 'full_name',
            'ngày sinh' => 'date_of_birth',
            'giới tính' => 'gender',
            'điện thoại' => 'phone',
            'số điện thoại' => 'phone',
            'địa chỉ' => 'address',
            'quê quán' => 'hometown',
            'cccd' => 'id_card',
            'cmnd' => 'id_card',
            'mã lớp' => 'class_code',
            'lớp' => 'class_code',
            'mã ngành' => 'major_code',
            'ngành' => 'major',
            'niên khóa' => 'academic_year',
            'khóa học' => 'academic_year',
            'trạng thái' => 'status',
            'ngày nhập học' => 'enrollment_date',
            'ngày tốt nghiệp' => 'graduation_date'
        ];
        return $mapping[$h] ?? $h;
    }, $rows[0]);

    // Validate required columns
    $requiredColumns = ['student_code'];
    $missingRequired = array_diff($requiredColumns, $header);
    if (!empty($missingRequired)) {
        return [
            'status' => 'error',
            'message' => 'Thiếu các cột bắt buộc: ' . implode(', ', $missingRequired),
            'errors' => []
        ];
    }

    // Process data rows
    $conn = getDbConnection();
    mysqli_begin_transaction($conn);
    
    try {
        // Process data rows
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Skip completely empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            // create assoc by header
            $assoc = [];
            for ($j = 0; $j < count($header); $j++) {
                $colName = $header[$j] ?? '';
                $assoc[$colName] = isset($row[$j]) ? trim($row[$j]) : '';
            }

            // Build studentData with careful validation
            $studentData = [];
            
            // Required fields
            if (empty($assoc['student_code'])) {
                $errors[] = "Dòng " . ($i+1) . ": Thiếu mã sinh viên";
                continue;
            }
            
            $studentData['student_code'] = $assoc['student_code'];
            $studentData['student_name'] = $assoc['student_name'] ?? $studentData['student_code'];
            $studentData['full_name'] = $assoc['full_name'] ?? '';
            $studentData['image'] = $assoc['image'] ?? null;
            
            // Date fields with careful validation
            foreach (['date_of_birth', 'enrollment_date', 'graduation_date'] as $dateField) {
                $value = $assoc[$dateField] ?? '';
                if (!empty($value)) {
                    $date = validateDate($value);
                    if ($date === null) {
                        $errors[] = "Dòng " . ($i+1) . ": Ngày không hợp lệ ở cột $dateField: $value";
                        continue 2;  // Skip this row
                    }
                    $studentData[$dateField] = $date;
                } else {
                    $studentData[$dateField] = null;
                }
            }

            // Basic fields
            $studentData['gender'] = $assoc['gender'] ?? '';
            $studentData['phone'] = $assoc['phone'] ?? '';
            $studentData['email'] = $assoc['email'] ?? '';
            $studentData['address'] = $assoc['address'] ?? '';
            $studentData['hometown'] = $assoc['hometown'] ?? '';
            $studentData['id_card'] = $assoc['id_card'] ?? '';
            $studentData['academic_year'] = $assoc['academic_year'] ?? '';
            $studentData['status'] = $assoc['status'] ?? 'Đang học';

            // Handle class mapping
            $mapped_class_id = null;
            if (!empty($assoc['class_id'])) {
                if (is_numeric($assoc['class_id'])) {
                    $cid = intval($assoc['class_id']);
                    if (isset($class_by_id[$cid])) {
                        $mapped_class_id = $cid;
                    }
                }
            } elseif (!empty($assoc['class_code'])) {
                if (isset($class_by_code[$assoc['class_code']])) {
                    $mapped_class_id = intval($class_by_code[$assoc['class_code']]['id']);
                }
            }
            $studentData['class_id'] = $mapped_class_id;

            // Handle major mapping
            $mapped_major = null;
            $majorRaw = $assoc['major'] ?? ($assoc['major_code'] ?? null);
            
            if (!empty($majorRaw)) {
                if (is_numeric($majorRaw) && isset($major_by_id[intval($majorRaw)])) {
                    $mapped_major = intval($majorRaw);
                } elseif (isset($major_by_code[$majorRaw])) {
                    $mapped_major = intval($major_by_code[$majorRaw]['id']);
                } elseif (isset($major_by_name[$majorRaw])) {
                    $mapped_major = intval($major_by_name[$majorRaw]['id']);
                }
            }
            $studentData['major'] = $mapped_major;

            // Try to add the student
            $result = addStudent($studentData);
            if ($result) {
                $successCount++;
            } else {
                $errors[] = "Dòng " . ($i+1) . ": Lỗi khi thêm sinh viên " . $studentData['student_code'];
            }

        }

        // Commit transaction if all went well, or rollback if there were any errors
        if (empty($errors)) {
            mysqli_commit($conn);
            return [
                'status' => 'success',
                'message' => "Đã import thành công $successCount sinh viên",
                'errors' => []
            ];
        } else {
            mysqli_rollback($conn);
            $errorMessage = $successCount > 0 
                ? "Đã import $successCount sinh viên, nhưng có " . count($errors) . " lỗi" 
                : 'Không có sinh viên nào được import';
            
            return [
                'status' => $successCount > 0 ? 'partial' : 'error',
                'message' => $errorMessage,
                'errors' => $errors
            ];
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return [
            'status' => 'error',
            'message' => 'Lỗi khi import: ' . $e->getMessage(),
            'errors' => []
        ];
    } finally {
        mysqli_close($conn);
    }

    if ($successCount > 0 && empty($errors)) {
        return ['status'=>'success','message'=>"Đã import thành công $successCount sinh viên", 'errors'=>[]];
    }
    if ($successCount > 0 && !empty($errors)) {
        return ['status'=>'partial','message'=>"Đã import $successCount sinh viên, nhưng có " . count($errors) . " lỗi", 'errors'=>$errors];
    }
    return ['status'=>'error','message'=>'Không có sinh viên nào được import','errors'=>$errors];
}

/**
 * Xử lý tạo sinh viên mới
 */
function handleCreateStudent() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/student.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['student_code']) || !isset($_POST['student_name'])) {
        header("Location: ../views/student/create_student.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $student_code = trim($_POST['student_code']);
    $student_name = trim($_POST['student_name']);
    
    // Validate dữ liệu
    if (empty($student_code) || empty($student_name)) {
        header("Location: ../views/student/create_student.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    
    // Xử lý upload ảnh
    $imagePath = handleImageUpload($student_code);
    
    // Chuẩn bị dữ liệu sinh viên
    $studentData = [
        'student_code' => $student_code,
        'student_name' => $student_name,
        'full_name' => trim($_POST['full_name'] ?? ''),
        'date_of_birth' => !empty($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : null,
        'gender' => $_POST['gender'] ?? '',
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'hometown' => trim($_POST['hometown'] ?? ''),
        'id_card' => trim($_POST['id_card'] ?? ''),
        'class_id' => !empty($_POST['class_id']) ? intval($_POST['class_id']) : null,
        'major' => isset($_POST['major']) && $_POST['major'] !== '' ? intval($_POST['major']) : null,
        'academic_year' => trim($_POST['academic_year'] ?? ''),
        'status' => $_POST['status'] ?? 'Đang học',
        'enrollment_date' => !empty($_POST['enrollment_date']) ? trim($_POST['enrollment_date']) : null,
        'image' => $imagePath
    ];
    
    // Gọi hàm thêm sinh viên
    $result = addStudent($studentData);
    
    if ($result) {
        header("Location: ../views/student.php?success=Thêm sinh viên thành công");
    } else {
        header("Location: ../views/student/create_student.php?error=Có lỗi xảy ra khi thêm sinh viên");
    }
    exit();
}

/**
 * Xử lý chỉnh sửa sinh viên
 */
function handleEditStudent() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/student.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['id']) || !isset($_POST['student_code']) || !isset($_POST['student_name'])) {
        header("Location: ../views/student.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = $_POST['id'];
    $student_code = trim($_POST['student_code']);
    
    // Lấy thông tin sinh viên hiện tại để lấy ảnh cũ
    $currentStudent = getStudentById($id);
    $currentImage = $currentStudent['image'] ?? '';
    
    // Xử lý upload ảnh mới (nếu có)
    $newImagePath = handleImageUpload($student_code);
    
    // Nếu có ảnh mới, xóa ảnh cũ và sử dụng ảnh mới
    // Nếu không có ảnh mới, giữ nguyên ảnh cũ
    $imagePath = $newImagePath !== null ? $newImagePath : ($_POST['current_image'] ?? $currentImage);
    
    // Nếu có ảnh mới và có ảnh cũ, xóa ảnh cũ
    if ($newImagePath !== null && !empty($currentImage) && $currentImage !== $newImagePath) {
        deleteOldImage($currentImage);
    }
    
    // Chuẩn bị dữ liệu từ form
    $studentData = [
        'student_code' => $student_code,
        'student_name' => trim($_POST['student_name']),
        'full_name' => trim($_POST['full_name'] ?? ''),
        'date_of_birth' => !empty($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : null,
        'gender' => $_POST['gender'] ?? '',
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'hometown' => trim($_POST['hometown'] ?? ''),
        'id_card' => trim($_POST['id_card'] ?? ''),
    'class_id' => !empty($_POST['class_id']) ? intval($_POST['class_id']) : null,
    'major' => isset($_POST['major']) && $_POST['major'] !== '' ? intval($_POST['major']) : null,
        'academic_year' => trim($_POST['academic_year'] ?? ''),
        'status' => $_POST['status'] ?? 'Đang học',
        'enrollment_date' => !empty($_POST['enrollment_date']) ? trim($_POST['enrollment_date']) : null,
        'image' => $imagePath
    ];
    
    // Xử lý graduation_date nếu có
    if (isset($_POST['graduation_date']) && !empty($_POST['graduation_date'])) {
        $studentData['graduation_date'] = trim($_POST['graduation_date']);
    }
    
    // Validate dữ liệu cơ bản
    if (empty($studentData['student_code']) || empty($studentData['student_name'])) {
        header("Location: ../views/student/edit_student.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin bắt buộc");
        exit();
    }
    
    // Xử lý ngày tốt nghiệp nếu trạng thái là "Tốt nghiệp"
    if ($studentData['status'] === 'Tốt nghiệp') {
        if (isset($_POST['graduation_date']) && !empty(trim($_POST['graduation_date']))) {
            $studentData['graduation_date'] = trim($_POST['graduation_date']);
        } else {
            // Nếu không có ngày tốt nghiệp được chọn, sử dụng ngày hiện tại
            $studentData['graduation_date'] = date('Y-m-d');
        }
    } else {
        // Nếu không phải trạng thái tốt nghiệp, xóa ngày tốt nghiệp
        $studentData['graduation_date'] = null;
    }
    
    // Gọi function để cập nhật sinh viên
    $result = updateStudent($id, $studentData);
    
    if ($result) {
        header("Location: ../views/student.php?success=Cập nhật sinh viên thành công");
    } else {
        header("Location: ../views/edit_student.php?id=" . $id . "&error=Cập nhật sinh viên thất bại");
    }
    exit();
}

/**
 * Xử lý xóa sinh viên
 */
function handleDeleteStudent() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/student.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/student.php?error=Không tìm thấy ID sinh viên");
        exit();
    }
    
    $id = $_GET['id'];
    
    // Validate ID là số
    if (!is_numeric($id)) {
        header("Location: ../views/student.php?error=ID sinh viên không hợp lệ");
        exit();
    }
    
    // Gọi function để xóa sinh viên
    $result = deleteStudent($id);
    
    if ($result) {
        header("Location: ../views/student.php?success=Xóa sinh viên thành công");
    } else {
        header("Location: ../views/student.php?error=Xóa sinh viên thất bại");
    }
    exit();
}

/**
 * Xử lý xóa nhiều sinh viên
 */
function handleBulkDeleteStudent() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/student.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
        header("Location: ../views/student.php?error=Không có sinh viên nào được chọn");
        exit();
    }
    
    $ids = array_map('intval', $_POST['ids']);
    $successCount = 0;
    $failCount = 0;
    
    foreach ($ids as $id) {
        if (deleteStudent($id)) {
            $successCount++;
        } else {
            $failCount++;
        }
    }
    
    if ($successCount > 0 && $failCount === 0) {
        header("Location: ../views/student.php?success=Đã xóa thành công $successCount sinh viên");
    } elseif ($successCount > 0 && $failCount > 0) {
        header("Location: ../views/student.php?success=Đã xóa $successCount sinh viên, $failCount thất bại");
    } else {
        header("Location: ../views/student.php?error=Xóa sinh viên thất bại");
    }
    exit();
}
?>
