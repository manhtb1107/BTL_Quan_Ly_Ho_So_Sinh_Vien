<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/student_functions.php';
checkLogin(__DIR__ . '/../index.php');

// Số liệu tổng quan
$totalStudents = count(getAllStudents());
$totalClasses = count(getAllClasses());
$totalMajors = count(getAllMajors());
$graduated = count(getGraduatedStudentsList());
$stats = getStudentStatistics();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<title>Thống kê tổng quan - DNU</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
	<?php include './menu.php'; ?>
	<div class="container mt-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="mb-0"><i class="fas fa-chart-pie"></i> Thống kê tổng quan</h3>
			<a href="javascript:window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> In</a>
		</div>

		<div class="row g-3">
			<div class="col-md-3">
				<div class="card text-center">
					<div class="card-body">
						<i class="fas fa-users fa-2x text-primary"></i>
						<h4 class="mt-2 mb-0"><?= $totalStudents ?></h4>
						<p class="text-muted mb-0">Tổng sinh viên</p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card text-center">
					<div class="card-body">
						<i class="fas fa-chalkboard-teacher fa-2x text-success"></i>
						<h4 class="mt-2 mb-0"><?= $totalClasses ?></h4>
						<p class="text-muted mb-0">Tổng lớp</p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card text-center">
					<div class="card-body">
						<i class="fas fa-graduation-cap fa-2x text-warning"></i>
						<h4 class="mt-2 mb-0"><?= $totalMajors ?></h4>
						<p class="text-muted mb-0">Tổng ngành</p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card text-center">
					<div class="card-body">
						<i class="fas fa-certificate fa-2x text-info"></i>
						<h4 class="mt-2 mb-0"><?= $graduated ?></h4>
						<p class="text-muted mb-0">Đã tốt nghiệp</p>
					</div>
				</div>
			</div>
		</div>

		<div class="card mt-4">
			<div class="card-header bg-dark text-white">
				<i class="fas fa-tags"></i> Thống kê theo trạng thái
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-striped mb-0">
						<thead class="table-light">
							<tr>
								<th>Trạng thái</th>
								<th>Số lượng</th>
								<th>GPA trung bình</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($stats as $row): ?>
								<tr>
									<td><?= htmlspecialchars($row['status'] ?? 'Chưa xác định') ?></td>
									<td><?= (int)($row['count'] ?? 0) ?></td>
									<td><?= isset($row['avg_gpa']) && $row['avg_gpa'] !== null ? number_format((float)$row['avg_gpa'], 2) : '-' ?></td>
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


