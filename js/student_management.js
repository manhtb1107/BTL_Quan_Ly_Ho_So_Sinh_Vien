// Menu Toggle
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
}

// Auto-hide alerts
setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        bsAlert.close();
    }
}, 3000);

// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.data-table tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}

// Filter functionality
const filterSelects = document.querySelectorAll('.filter-select');
const applyFilterBtn = document.getElementById('applyFilter');
const clearFilterBtn = document.getElementById('clearFilter');

if (applyFilterBtn) {
    applyFilterBtn.addEventListener('click', () => {
        applyFilters();
    });
}

if (clearFilterBtn) {
    clearFilterBtn.addEventListener('click', () => {
        filterSelects.forEach(select => {
            select.value = '';
        });
        if (searchInput) searchInput.value = '';
        applyFilters();
    });
}

function applyFilters() {
    const majorFilter = document.getElementById('majorFilter')?.value.toLowerCase() || '';
    const statusFilter = document.getElementById('statusFilter')?.value.toLowerCase() || '';
    const classFilter = document.getElementById('classFilter')?.value.toLowerCase() || '';
    const searchTerm = searchInput?.value.toLowerCase() || '';
    
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const major = row.querySelector('td:nth-child(7)')?.textContent.toLowerCase() || '';
        const status = row.querySelector('td:nth-child(8)')?.textContent.toLowerCase() || '';
        const className = row.querySelector('td:nth-child(6)')?.textContent.toLowerCase() || '';
        const allText = row.textContent.toLowerCase();
        
        const matchMajor = !majorFilter || major.includes(majorFilter);
        const matchStatus = !statusFilter || status.includes(statusFilter);
        const matchClass = !classFilter || className.includes(classFilter);
        const matchSearch = !searchTerm || allText.includes(searchTerm);
        
        row.style.display = (matchMajor && matchStatus && matchClass && matchSearch) ? '' : 'none';
    });
}

// Delete confirmation
function confirmDelete(studentId, studentName) {
    if (confirm(`Bạn có chắc chắn muốn xóa sinh viên ${studentName}?`)) {
        window.location.href = `../handle/student_process.php?action=delete&id=${studentId}`;
    }
}

// Table row hover effect
document.querySelectorAll('.data-table tbody tr').forEach(row => {
    row.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.01)';
        this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
    });
    
    row.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
        this.style.boxShadow = 'none';
    });
});

// Pagination
function goToPage(page) {
    // Implement pagination logic here
    console.log('Go to page:', page);
}
