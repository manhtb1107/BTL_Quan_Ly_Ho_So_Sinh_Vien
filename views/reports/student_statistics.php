<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/student_functions.php';
checkLogin(__DIR__ . '/../../index.php');

$stats = getStudentStatistics();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<title>Thống kê sinh viên - DNU</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
	<?php include __DIR__ . '/../menu.php'; ?>
	<div class="container mt-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="mb-0"><i class="fas fa-chart-bar"></i> Thống kê sinh viên theo trạng thái</h3>
			<a href="javascript:window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> In báo cáo</a>
		</div>

		<div class="row">
			<?php foreach ($stats as $row): ?>
				<div class="col-md-4 mb-3">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title"><i class="fas fa-tag"></i> <?= htmlspecialchars($row['status'] ?? 'Chưa xác định') ?></h5>
							<p class="mb-1"><strong>Số lượng:</strong> <?= (int)($row['count'] ?? 0) ?></p>
							<p class="mb-0"><strong>GPA trung bình:</strong> <?= isset($row['avg_gpa']) && $row['avg_gpa'] !== null ? number_format((float)$row['avg_gpa'], 2) : '-' ?></p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="card mt-4">
			<div class="card-header bg-dark text-white">
				<i class="fas fa-table"></i> Bảng thống kê chi tiết
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


