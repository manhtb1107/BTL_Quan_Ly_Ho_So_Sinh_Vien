document.addEventListener('DOMContentLoaded', function() {
  // Tự động ẩn alert sau 3s nếu tồn tại
  setTimeout(() => {
    const alertNode = document.querySelector('.alert');
    if (alertNode && window.bootstrap && bootstrap.Alert) {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
      bsAlert.close();
    }
  }, 3000);

  // Hiệu ứng loading cho các nút hành động phổ biến
  const loadingClasses = ['btn-add', 'btn-primary', 'btn-search'];
  document.querySelectorAll('.btn').forEach((btn) => {
    btn.addEventListener('click', function () {
      if (loadingClasses.some((cls) => this.classList.contains(cls))) {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
        this.disabled = true;
        setTimeout(() => {
          this.innerHTML = originalText;
          this.disabled = false;
        }, 1000);
      }
    });
  });

  // Hiệu ứng hover cho bảng
  document.querySelectorAll('.table tbody tr').forEach((row) => {
    row.addEventListener('mouseenter', function () {
      this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
    });
    row.addEventListener('mouseleave', function () {
      this.style.boxShadow = 'none';
    });
  });

  // Hiệu ứng hover cho thẻ thống kê nếu có
  document.querySelectorAll('.stats-card').forEach((card) => {
    card.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-5px)';
      this.style.transition = 'all 0.3s ease';
    });
    card.addEventListener('mouseleave', function () {
      this.style.transform = 'translateY(0)';
    });
  });
});

