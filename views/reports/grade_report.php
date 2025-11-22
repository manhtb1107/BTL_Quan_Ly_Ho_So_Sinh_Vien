<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/grade_functions.php';
require_once __DIR__ . '/../../functions/subject_functions.php';
checkLogin(__DIR__ . '/../../index.php');

$subjects = getAllSubjects();
$selectedSubject = isset($_GET['subject_id']) ? trim($_GET['subject_id']) : '';

if ($selectedSubject !== '') {
	$grades = getGradesBySubject((int)$selectedSubject);
} else {
	$grades = getAllGrades();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8">
	<title>Báo cáo điểm - DNU</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
	<?php include __DIR__ . '/../menu.php'; ?>
	<div class="container mt-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="mb-0"><i class="fas fa-clipboard-list"></i> Báo cáo điểm</h3>
			<a href="javascript:window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> In báo cáo</a>
		</div>

		<div class="card mb-4">
			<div class="card-header bg-primary text-white">
				<i class="fas fa-filter"></i> Bộ lọc
			</div>
			<div class="card-body">
				<form method="GET" action="">
					<div class="row g-2">
						<div class="col-md-6">
							<select name="subject_id" class="form-select">
								<option value="">-- Tất cả môn học --</option>
								<?php foreach ($subjects as $sub): ?>
									<option value="<?= (int)$sub['id'] ?>" <?= $selectedSubject == $sub['id'] ? 'selected' : '' ?>>
										<?= htmlspecialchars($sub['subject_name']) ?> (<?= htmlspecialchars($sub['subject_code']) ?>)
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-3">
							<button class="btn btn-primary w-100" type="submit"><i class="fas fa-search"></i> Lọc</button>
						</div>
						<div class="col-md-3">
							<a href="?" class="btn btn-outline-secondary w-100"><i class="fas fa-rotate"></i> Xóa lọc</a>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="card">
			<div class="card-header bg-dark text-white">
				<i class="fas fa-table"></i> Danh sách điểm (<?= count($grades) ?> bản ghi)
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-striped table-bordered mb-0">
						<thead class="table-light">
							<tr>
								<th>Mã SV</th>
								<th>Họ và tên</th>
								<th>Mã HP</th>
								<th>Môn học</th>
								<th>Giữa kỳ</th>
								<th>Cuối kỳ</th>
								<th>Tổng kết</th>
								<th>Điểm chữ</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($grades as $g): ?>
								<tr>
									<td><?= htmlspecialchars($g['student_code'] ?? '') ?></td>
									<td><?= htmlspecialchars($g['student_name'] ?? '') ?></td>
									<td><?= htmlspecialchars($g['subject_code'] ?? '') ?></td>
									<td><?= htmlspecialchars($g['subject_name'] ?? '') ?></td>
									<td><?= isset($g['midterm_score']) ? number_format((float)$g['midterm_score'], 2) : '-' ?></td>
									<td><?= isset($g['final_score']) ? number_format((float)$g['final_score'], 2) : '-' ?></td>
									<td><?= isset($g['total_score']) ? number_format((float)$g['total_score'], 2) : '-' ?></td>
									<td><?= htmlspecialchars($g['letter_grade'] ?? '') ?></td>
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


