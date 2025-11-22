// Menu Toggle for Mobile
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.querySelector('.sidebar');

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768) {
        if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    }
});

// Counter Animation for Stats
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;

    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            element.textContent = target.toLocaleString();
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 16);
}

// Animate all stat values on page load
document.addEventListener('DOMContentLoaded', () => {
    const statValues = document.querySelectorAll('.stat-value[data-target]');
    statValues.forEach(stat => animateCounter(stat));
});

// Chart.js - Student Distribution Chart
const ctx = document.getElementById('studentDistributionChart');
if (ctx) {
    // Fetch data from API
    fetch('api/get_dashboard_data.php')
        .then(response => response.json())
        .then(data => {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels.length > 0 ? data.labels : ['Chưa có dữ liệu'],
                    datasets: [{
                        label: 'Số sinh viên',
                        data: data.values.length > 0 ? data.values : [0],
                        backgroundColor: data.colors,
                        borderRadius: 8,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#202124',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: '600'
                            },
                            bodyFont: {
                                size: 13
                            },
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f0f0f0',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                color: '#5f6368'
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                color: '#5f6368'
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
            // Fallback to default data
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Chưa có dữ liệu'],
                    datasets: [{
                        label: 'Số sinh viên',
                        data: [0],
                        backgroundColor: ['#4285f4'],
                        borderRadius: 8,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
}

// Smooth scroll for navigation
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
        // Remove active class from all items
        document.querySelectorAll('.nav-item').forEach(nav => {
            nav.classList.remove('active');
        });
        
        // Add active class to clicked item (except logout)
        if (!this.classList.contains('logout')) {
            this.classList.add('active');
        }
    });
});

// Search functionality
const searchInput = document.querySelector('.search-box input');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        // Add your search logic here
        console.log('Searching for:', searchTerm);
    });
}

// Notification bell animation
const bellIcon = document.querySelector('.btn-icon .fa-bell');
if (bellIcon) {
    setInterval(() => {
        bellIcon.classList.add('fa-shake');
        setTimeout(() => {
            bellIcon.classList.remove('fa-shake');
        }, 500);
    }, 10000); // Shake every 10 seconds
}


// Load Recent Activities
const activityList = document.getElementById('activityList');
if (activityList) {
    fetch('api/get_recent_activities.php')
        .then(response => response.json())
        .then(activities => {
            if (activities.length === 0) {
                activityList.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-3"></i>
                        <p>Chưa có hoạt động nào</p>
                    </div>
                `;
                return;
            }

            activityList.innerHTML = activities.map(activity => `
                <div class="activity-item">
                    <div class="activity-icon ${activity.color}">
                        <i class="fas fa-${activity.icon}"></i>
                    </div>
                    <div class="activity-content">
                        <p class="activity-title">${activity.title}</p>
                        <span class="activity-time">${activity.subtitle}</span>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading activities:', error);
            activityList.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <p>Không thể tải hoạt động</p>
                </div>
            `;
        });
}
