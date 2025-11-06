<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/student_functions.php';
checkLogin(__DIR__ . '/../index.php');

// Lấy danh sách ngành
$majors = getAllMajors();

// Xử lý tìm kiếm
$students = [];
$selectedMajor = '';

if (isset($_GET['major']) && $_GET['major'] !== '') {
	$selectedMajor = $_GET['major'];
	$students = getStudentsByMajor($selectedMajor);
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Tìm kiếm sinh viên theo ngành - DNU</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
	<?php include './menu.php'; ?>
	<div class="container mt-3">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h3>
				<i class="fas fa-graduation-cap"></i> Tìm kiếm sinh viên theo ngành
			</h3>
			<a href="/baitaplon/views/student_management.php" class="btn btn-secondary">
				<i class="fas fa-arrow-left"></i> Quay lại
			</a>
		</div>

		<!-- Form tìm kiếm -->
		<div class="card mb-4">
			<div class="card-header bg-primary text-white">
				<h5 class="mb-0">
					<i class="fas fa-search"></i> Chọn ngành học
				</h5>
			</div>
			<div class="card-body">
				<form method="GET" action="">
					<div class="row">
						<div class="col-md-8">
							<select name="major" class="form-select" required>
								<option value="">-- Chọn ngành học --</option>
								<?php foreach($majors as $major): ?>
									<option value="<?= htmlspecialchars($major['major_name']) ?>" <?= $selectedMajor === $major['major_name'] ? 'selected' : '' ?>>
										<?= htmlspecialchars($major['major_name']) ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-4">
							<button type="submit" class="btn btn-primary">
								<i class="fas fa-search"></i> Tìm kiếm
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- Kết quả -->
		<?php if (!empty($students)): ?>
			<div class="card">
				<div class="card-header bg-success text-white">
					<h5 class="mb-0">
						<i class="fas fa-list"></i> Kết quả tìm kiếm (<?= count($students) ?> sinh viên)
					</h5>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped">
							<thead class="table-dark">
								<tr>
									<th>Mã SV</th>
									<th>Họ và tên</th>
									<th>Ngày sinh</th>
									<th>Giới tính</th>
									<th>Lớp</th>
									<th>Ngành</th>
									<th>Trạng thái</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($students as $student): ?>
									<tr>
										<td><?= htmlspecialchars($student['student_code']) ?></td>
										<td><?= htmlspecialchars($student['full_name'] ?? $student['student_name']) ?></td>
										<td><?= !empty($student['date_of_birth']) ? date('d/m/Y', strtotime($student['date_of_birth'])) : '-' ?></td>
										<td><?= htmlspecialchars($student['gender'] ?? '-') ?></td>
										<td><?= htmlspecialchars($student['class_name'] ?? '-') ?></td>
										<td><?= htmlspecialchars($student['major'] ?? '-') ?></td>
										<td><?= htmlspecialchars($student['status'] ?? 'Chưa xác định') ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php elseif (isset($_GET['major'])): ?>
			<div class="alert alert-warning">
				<i class="fas fa-exclamation-triangle"></i> Không tìm thấy sinh viên nào theo ngành đã chọn.
			</div>
		<?php endif; ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


