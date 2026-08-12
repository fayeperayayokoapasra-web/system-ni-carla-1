<link rel="stylesheet" href="assets/css/sidebar.css">

<div class="sidebar-shell">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo">C&C</div>
            <div>
                <h4>Cut &amp; Coat</h4>
                <p>Customer Area</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="customerdashboard.php"><span>Dashboard</span></a>
            <a href="customerreservations.php"><span>My Reservations</span></a>
            <a href="customerbook.php"><span>Book Appointment</span></a>
            <a href="customerservices.php"><span>Services</span></a>
            <a href="customerfeedback.php"><span>Feedback</span></a>
        </nav>
    </div>
</div>

<script>
(function() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');

    document.addEventListener('DOMContentLoaded', function() {
        if (!sidebar) return;
        const currentPath = window.location.pathname.split('/').pop().toLowerCase();
        const currentUrl = window.location.href.toLowerCase();

        sidebar.querySelectorAll('a').forEach(function(link) {
            const href = link.getAttribute('href');
            if (!href) return;
            const linkPath = href.split('?')[0].split('#')[0].toLowerCase();
            const linkUrl = link.href.toLowerCase();
            const isCurrentPage = linkPath === currentPath || linkUrl === currentUrl;
            if (isCurrentPage) {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            } else {
                link.classList.remove('active');
                link.removeAttribute('aria-current');
            }
        });
    });
})();
</script>
