<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/student_functions.php';
checkLogin(__DIR__ . '/../index.php');

$classes = getAllClasses();
$selectedClass = isset($_GET['class_id']) ? trim($_GET['class_id']) : '';
$students = [];
if ($selectedClass !== '') {
	$students = getStudentsByClass((int)$selectedClass);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<title>Báo cáo danh sách sinh viên theo lớp - DNU</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
	<?php include './menu.php'; ?>
	<div class="container mt-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="mb-0"><i class="fas fa-list"></i> Danh sách sinh viên theo lớp</h3>
			<a href="javascript:window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> In báo cáo</a>
		</div>

		<div class="card mb-4">
			<div class="card-header bg-primary text-white">
				<i class="fas fa-filter"></i> Chọn lớp
			</div>
			<div class="card-body">
				<form method="GET" action="">
					<div class="row g-2">
						<div class="col-md-8">
							<select name="class_id" class="form-select" required>
								<option value="">-- Chọn lớp học --</option>
								<?php foreach ($classes as $class): ?>
									<option value="<?= (int)$class['id'] ?>" <?= $selectedClass == $class['id'] ? 'selected' : '' ?>>
										<?= htmlspecialchars($class['class_name']) ?> (<?= htmlspecialchars($class['class_code']) ?>)
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-4">
							<button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Xem danh sách</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<?php if (!empty($students)): ?>
			<div class="card">
				<div class="card-header bg-dark text-white">
					<i class="fas fa-users"></i> Lớp: 
					<?php 
						$cur = null; 
						foreach ($classes as $c) { if ((string)$c['id'] === (string)$selectedClass) { $cur = $c; break; } } 
						echo htmlspecialchars(($cur['class_name'] ?? '') . ' (' . ($cur['class_code'] ?? '') . ')');
					?>
					 - <?= count($students) ?> sinh viên
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-striped table-bordered mb-0">
							<thead class="table-light">
								<tr>
									<th>#</th>
									<th>Mã SV</th>
									<th>Họ và tên</th>
									<th>Ngày sinh</th>
									<th>Giới tính</th>
									<th>Ngành</th>
									<th>Trạng thái</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($students as $i => $s): ?>
									<tr>
										<td><?= $i + 1 ?></td>
										<td><?= htmlspecialchars($s['student_code']) ?></td>
										<td><?= htmlspecialchars($s['full_name'] ?? $s['student_name']) ?></td>
										<td><?= !empty($s['date_of_birth']) ? date('d/m/Y', strtotime($s['date_of_birth'])) : '-' ?></td>
										<td><?= htmlspecialchars($s['gender'] ?? '-') ?></td>
										<td><?= htmlspecialchars($s['major'] ?? '-') ?></td>
										<td><?= htmlspecialchars($s['status'] ?? 'Chưa xác định') ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php elseif (isset($_GET['class_id'])): ?>
			<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Không có sinh viên trong lớp đã chọn.</div>
		<?php endif; ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


