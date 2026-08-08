<div class="sidebar-shell">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo">C&C</div>
            <div>
                <h4>Cut &amp; Coat</h4>
                <p>Admin Panel</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="admindashboard.php"><span>Dashboard</span></a>
            <a href="adminreservations.php"><span>Reservations</span></a>
            <a href="adminwalkins.php"><span>Walk-Ins</span></a>
            <a href="admincustomers.php"><span>Customers</span></a>
            <a href="adminservices.php"><span>Services</span></a>
            <a href="adminstaff.php"><span>Staff</span></a>
            <a href="adminsched.php"><span>Schedule</span></a>
            <a href="adminfeedback.php"><span>Feedback</span></a>
            <a href="adminreports.php"><span>Reports</span></a>
        </nav>
    </div>
</div>

<script>
(function() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const messagePage = document.getElementById('messagePage');

    function setActiveSidebarLink() {
        if (!sidebar) return;
            const currentPath = window.location.pathname.split('/').pop().toLowerCase();
            const currentUrl = window.location.href.toLowerCase();

        sidebar.querySelectorAll('a').forEach(function(link) {
            const href = link.getAttribute('href');
            if (!href) return;
                const linkPath = href.split('?')[0].split('#')[0].toLowerCase();
                const linkUrl = new URL(href, window.location.href).href.toLowerCase();
                const isCurrentPage = linkPath === currentPath || linkPath === currentPath.replace(/\.php$/, '') || linkUrl === currentUrl;

            if (isCurrentPage) {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            } else {
                link.classList.remove('active');
                link.removeAttribute('aria-current');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        setActiveSidebarLink();
    });
})();
</script>
