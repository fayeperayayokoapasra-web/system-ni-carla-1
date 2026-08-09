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

<style>
    .sidebar-shell {
        position: fixed;
        top: 65px;
        left: 0;
        bottom: 0;
        z-index: 900;
    }

    .sidebar {
        width: 220px;
        height: calc(100vh - 65px);
        background: linear-gradient(180deg, #064e3b 0%, #022c22 100%);
        color: #fff;
        padding: 16px 12px;
        box-sizing: border-box;
        box-shadow: 8px 0 24px rgba(0, 0, 0, 0.14);
        transition: width 0.25s ease;
        overflow: hidden;
    }

    .sidebar.collapsed {
        width: 74px;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px 6px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.16);
        margin-bottom: 12px;
    }

    .sidebar-logo {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #10b981, #34d399);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }

    .sidebar-brand h4 {
        margin: 0;
        font-size: 14px;
    }

    .sidebar-brand p {
        margin: 2px 0 0;
        font-size: 11px;
        color: #d1fae5;
    }

    .toggle-btn {
        background: #10b981;
        border: none;
        color: #fff;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        cursor: pointer;
        margin: 12px 0 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .sidebar-nav a {
        display: block;
        color: #f9fafb;
        text-decoration: none;
        padding: 11px 12px;
        margin: 6px 0;
        border-radius: 10px;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .sidebar-nav a:hover,
    .sidebar-nav a.active {
        background: rgba(255, 255, 255, 0.16);
        transform: translateX(3px);
    }

    .sidebar-nav a.active {
        box-shadow: inset 3px 0 0 #34d399;
        font-weight: 600;
    }

    .sidebar.collapsed .sidebar-brand > div:not(.sidebar-logo),
    .sidebar.collapsed .sidebar-nav a span {
        display: none;
    }

    .sidebar.collapsed .sidebar-nav a {
        padding: 10px 0;
        border-radius: 8px;
    }

    .main {
        margin-left: 240px;
        padding: 90px 20px 20px;
        transition: margin-left 0.25s ease;
    }

    .main.expanded,
    .main.sidebar-collapsed {
        margin-left: 94px;
    }
</style>

<script>
(function() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');

    window.toggleSidebar = function() {
        if (!sidebar || !main) return;
        sidebar.classList.toggle('collapsed');
        main.classList.toggle('expanded');
        main.classList.toggle('sidebar-collapsed');
    };

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
