<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/student_functions.php';
checkLogin(__DIR__ . '/../index.php');

$students = getGraduatedStudentsList();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<title>Báo cáo hồ sơ tốt nghiệp - DNU</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
	<?php include './menu.php'; ?>
	<div class="container mt-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="mb-0"><i class="fas fa-certificate"></i> Danh sách hồ sơ tốt nghiệp</h3>
			<a href="javascript:window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> In danh sách</a>
		</div>

		<div class="card">
			<div class="card-header bg-dark text-white">
				<i class="fas fa-users"></i> Tổng số: <?= count($students) ?> sinh viên
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-striped table-bordered mb-0">
						<thead class="table-light">
							<tr>
								<th>#</th>
								<th>Mã SV</th>
								<th>Họ và tên</th>
								<th>Lớp</th>
								<th>Ngành</th>
								<th>Năm học</th>
								<th>Ngày nhập học</th>
								<th>GPA</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($students as $i => $s): ?>
								<tr>
									<td><?= $i + 1 ?></td>
									<td><?= htmlspecialchars($s['student_code']) ?></td>
									<td><?= htmlspecialchars($s['full_name'] ?? $s['student_name']) ?></td>
									<td><?= htmlspecialchars($s['class_name'] ?? '-') ?></td>
									<td><?= htmlspecialchars($s['major'] ?? '-') ?></td>
									<td><?= htmlspecialchars($s['academic_year'] ?? '-') ?></td>
									<td><?= !empty($s['enrollment_date']) ? date('d/m/Y', strtotime($s['enrollment_date'])) : '-' ?></td>
									<td><?= isset($s['gpa']) ? number_format((float)$s['gpa'], 2) : '-' ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


