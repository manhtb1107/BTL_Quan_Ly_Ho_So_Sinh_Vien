document.addEventListener('DOMContentLoaded', function() {
  // Dropdown "Báo cáo" trên desktop dùng hover, mobile dùng click mặc định của Bootstrap
  const reportDropdown = document.querySelector('#reportDropdown');
  const reportMenu = document.querySelector('#reportDropdownMenu');

  function isDesktop() {
    return window.innerWidth >= 992;
  }

  if (reportDropdown && reportMenu && isDesktop()) {
    let hideTimeout;
    let showTimeout;
    const HIDE_DELAY = 300;
    const SHOW_DELAY = 100;

    // Ngăn toggle khi click trên desktop (chỉ hover)
    reportDropdown.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
    });

    const dropdownParent = reportDropdown.closest('.nav-item.dropdown');
    if (dropdownParent) {
      dropdownParent.addEventListener('mouseenter', function() {
        clearTimeout(hideTimeout);
        showTimeout = setTimeout(function() {
          reportMenu.classList.add('show-delay');
          reportMenu.style.display = 'block';
          reportMenu.style.opacity = '1';
          reportMenu.style.transform = 'translateY(0)';
          reportMenu.style.visibility = 'visible';
          reportDropdown.setAttribute('aria-expanded', 'true');
        }, SHOW_DELAY);
      });
      dropdownParent.addEventListener('mouseleave', function() {
        clearTimeout(showTimeout);
        hideTimeout = setTimeout(function() {
          reportMenu.classList.remove('show-delay');
          reportMenu.style.opacity = '0';
          reportMenu.style.transform = 'translateY(-10px)';
          setTimeout(function() {
            reportMenu.style.display = 'none';
            reportMenu.style.visibility = 'hidden';
          }, 300);
          reportDropdown.setAttribute('aria-expanded', 'false');
        }, HIDE_DELAY);
      });
    }
  }

  // Dropdown user dùng click
  const userDropdownToggle = document.querySelector('#userDropdownToggle');
  const userDropdownMenu = document.querySelector('#userDropdownMenu');
  if (userDropdownToggle && userDropdownMenu) {
    userDropdownToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const isExpanded = userDropdownToggle.getAttribute('aria-expanded') === 'true';
      if (isExpanded) {
        userDropdownMenu.classList.remove('show');
        userDropdownToggle.setAttribute('aria-expanded', 'false');
      } else {
        userDropdownMenu.classList.add('show');
        userDropdownToggle.setAttribute('aria-expanded', 'true');
      }
    });
    document.addEventListener('click', function(e) {
      const userMenu = document.querySelector('.user-menu');
      if (userMenu && !userMenu.contains(e.target)) {
        userDropdownMenu.classList.remove('show');
        userDropdownToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }
});

