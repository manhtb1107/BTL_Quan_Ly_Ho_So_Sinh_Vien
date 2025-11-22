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

filterSelects.forEach(select => {
    select.addEventListener('change', () => {
        applyFilters();
    });
});

function applyFilters() {
    const departmentFilter = document.getElementById('departmentFilter')?.value.toLowerCase() || '';
    const teacherFilter = document.getElementById('teacherFilter')?.value.toLowerCase() || '';
    const semesterFilter = document.getElementById('semesterFilter')?.value.toLowerCase() || '';
    const searchTerm = searchInput?.value.toLowerCase() || '';
    
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const allText = row.textContent.toLowerCase();
        
        const matchDepartment = !departmentFilter || allText.includes(departmentFilter);
        const matchTeacher = !teacherFilter || allText.includes(teacherFilter);
        const matchSemester = !semesterFilter || allText.includes(semesterFilter);
        const matchSearch = !searchTerm || allText.includes(searchTerm);
        
        row.style.display = (matchDepartment && matchTeacher && matchSemester && matchSearch) ? '' : 'none';
    });
}

// Delete confirmation
function confirmDelete(subjectId, subjectName) {
    if (confirm(`Bạn có chắc chắn muốn xóa khóa học "${subjectName}"?`)) {
        window.location.href = `../handle/subject_process.php?action=delete&id=${subjectId}`;
    }
}

// Table row hover effect
document.querySelectorAll('.data-table tbody tr').forEach(row => {
    row.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.005)';
        this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
    });
    
    row.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
        this.style.boxShadow = 'none';
    });
});

// Pagination
function goToPage(page) {
    console.log('Go to page:', page);
    // Implement pagination logic here
}
