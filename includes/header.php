<?php
require_once 'auth.php';
require_once 'db.php';
require_once 'functions.php';

// Ensure user is logged in
requireLogin();

$conn = getDbConnection();
$logoPath = '';

$stmt = $conn->prepare("SELECT logo_path FROM site_settings WHERE id = 1");
$stmt->execute();
$stmt->bind_result($logoPath);
$stmt->fetch();
$stmt->close();

$conn->close();

$isAdmin = isAdmin($_SESSION['user_id']);

// New function to check if user is a Technology Officer
function isTechnologyOfficer($position) {
    return stripos($position, 'technology officer') !== false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'ISA Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="icon" href="assets/images/isa-fab.ico" type="image/x-icon">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }
        #sidebar {
            width: 250px;
            background-color: #343a40;
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        #sidebar.hidden {
            margin-left: -250px;
        }
        #content {
            flex: 1;
            margin-left: 250px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }
        #content.expanded {
            margin-left: 0;
        }
        #top-header {
            background-color: #f8f9fa;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        #content.expanded #top-header {
            width: 100%;
        }
        .nav-item {
            position: relative;
        }
        .nav-link, .sub-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 1rem;
            color: #fff !important;
            text-decoration: none;
            border: none;
        }
        .nav-link:hover, .sub-link:hover {
            background-color: #495057;
        }
        .nav-link span, .sub-link span {
            flex-grow: 1;
            text-align: left;
        }
        .dropdown-toggle::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            border: none;
            vertical-align: middle;
            margin-left: 0.5rem;
        }
        .dropdown-toggle[aria-expanded="true"]::after {
            content: '\f106';
        }
        .nav-link:not(.dropdown-toggle)::after {
            content: none;
        }
        .submenu {
            padding-left: 1rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .submenu.show {
            max-height: 1000px;
            transition: max-height 0.5s ease-in;
        }
        .submenu .sub-link {
            padding-left: 2rem;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
        .logout-btn:hover {
            background-color: #c82333;
            color: white;
        }
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background-color: #2c3136;
        }
        .menu-icon {
          background: none;
          border: none;
          font-size: 24px;
          color: #333;
          cursor: pointer;
          padding: 10px;
          transition: color 0.3s ease;
        }
        .menu-icon:hover {
          color: #007bff;
        }
        .menu-icon .fa-times {
          font-size: 28px;
        }
        .nav-link.active, .sub-link.active {
            background-color: #495057;
            color: #ffffff !important;
        }
        .submenu .sub-link.active {
            background-color: #6c757d;
        }

        /* Responsive behavior */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            #sidebar.hidden {
                margin-left: 0;
            }
            #content {
                margin-left: 0;
            }
            #content.expanded {
                margin-left: 250px;
            }
            #top-header {
                width: 100%;
                z-index: 1001;
                position: fixed;
                top: 0;
                left: 0;
                transition: all 0.3s;
            }
            #content.expanded #top-header {
                width: calc(100% - 250px);
                left: 250px;
            }
            #main-content {
                margin-top: 60px;
            }
        }
    </style>
</head>
<body>
    
<!-- Sidebar -->
<nav id="sidebar" class="bg-dark">
    <div class="sidebar-header">
        <img src="<?php echo $logoPath ?: 'assets/images/isa-logo.png'; ?>" alt="ISA Logo" class="img-fluid" style="max-width: 60px;">
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-home me-2"></i>
                <span>Home</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="messages.php">
                <i class="fas fa-envelope me-2"></i>
                <span>Visitor Messages</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#profileSubmenu">
                <i class="fas fa-user me-2"></i>
                <span>Profile</span>
            </a>
            <div class="collapse submenu" id="profileSubmenu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="sub-link" href="profile.php">My Profile</a>
                    </li>
                    <?php if ($isAdmin): ?>
                    <li class="nav-item">
                        <a class="sub-link" href="update_officer.php">Update Officer Details</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </li>
        <?php if ($isAdmin): ?>
        <li class="nav-item">
            <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#userManagementSubmenu">
                <i class="fas fa-users-cog me-2"></i>
                <span>User Management</span>
            </a>
            <div class="collapse submenu" id="userManagementSubmenu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="sub-link" href="user_management.php?tab=isa-officers">All Officers</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="user_management.php?tab=add-officer">Add New Officer</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="user_management.php?tab=officer-points">Officer's Point</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="user_management.php?tab=change-pwd">Change Password</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="user_management.php?tab=retire-officer">Retire Officer Now</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="user_management.php?tab=retired-officers">Retired Officers</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#isaSettingsSubmenu">
                <i class="fas fa-cog me-2"></i>
                <span>ISA Settings</span>
            </a>
            <div class="collapse submenu" id="isaSettingsSubmenu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="sub-link" href="isa_settings.php?tab=upload_logo">Upload Logo</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="isa_settings.php?tab=officer_position">Officer Position</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="isa_settings.php?tab=degree">Degree</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="isa_settings.php?tab=user_control">Enable/Disable Officer</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="isa_settings.php?tab=user_permissions">User Permissions</a>
                    </li>
                </ul>
            </div>
        </li>
        <?php endif; ?>
        <?php if (isAdminOrTechOfficer() || isTechnologyOfficer($_SESSION['user_position'])): ?>
        <li class="nav-item">
            <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#isaMainPageSubmenu">
                <i class="fas fa-globe me-2"></i>
                <span>ISA Main Page</span>
            </a>
            <div class="collapse submenu" id="isaMainPageSubmenu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="sub-link" href="isa_front_page.php">Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="resources.php">Resources</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="gallery.php">Gallery</a>
                    </li>
                </ul>
            </div>
        </li>
        <?php endif; ?>

        <?php if (isAdmin($_SESSION['user_id']) || (isTechnologyOfficer($_SESSION['user_position']) && $_SESSION['status'] === 'active')): ?>
        <li class="nav-item">
            <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#emailNotificationSubmenu">
                <i class="fas fa-envelope-open-text me-2"></i>
                <span>Email & Notification</span>
            </a>
            <div class="collapse submenu" id="emailNotificationSubmenu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="sub-link" href="email_notification.php?submenu=officers_birthday">Officer's Birthday</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="email_notification.php?submenu=birthday_history">Birthday History</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="email_notification.php?submenu=email_categories">Email Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="email_notification.php?submenu=email_templates">Email Templates</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="email_notification.php?submenu=smtp_settings">SMTP Settings</a>
                    </li>
                </ul>
            </div>
        </li>
        <?php endif; ?>
        
        <li class="nav-item">
            <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#isaVolunteerSubmenu">
                <i class="fas fa-hands-helping me-2"></i>
                <span>ISA Volunteer</span>
            </a>
            <div class="collapse submenu" id="isaVolunteerSubmenu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="sub-link" href="volunteer_opportunities.php">Opportunities</a>
                    </li>
                    <li class="nav-item">
                        <a class="sub-link" href="volunteer_applications.php">Applications</a>
                    </li>
                    <!-- Add more submenu items as needed -->
                </ul>
            </div>
        </li>
    </ul>
</nav>

<!-- Top Header -->
<div id="content">
    <header id="top-header" class="d-flex align-items-center justify-content-between">
        <div>
            <button id="menu-toggle" class="menu-icon">
              <i id="menu-icon" class="fas fa-bars"></i>
            </button>
        </div>
        <div class="ms-auto d-flex align-items-center">
            <a href="#" class="btn btn-link position-relative me-3">
                <i class="fas fa-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                    <span class="visually-hidden">unread messages</span>
                </span>
            </a>
            <img src="<?php echo htmlspecialchars($_SESSION['user_profile_picture'] ?? 'assets/images/default-profile.jpg'); ?>" alt="Profile Picture" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
            <div class="d-none d-lg-block">
                <span><?php echo htmlspecialchars($_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name']); ?></span>
                <small class="d-block text-muted"><?php echo htmlspecialchars($_SESSION['user_position']); ?></small>
            </div>
        </div>
    </header>
    <!-- Main Content -->
    <div id="main-content" class="p-4">
        <!-- Your page content goes here -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const menuToggle = document.getElementById('menu-toggle');
    const menuIcon = document.getElementById('menu-icon');
    const topHeader = document.getElementById('top-header');

    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('hidden');
        content.classList.toggle('expanded');
        menuIcon.classList.toggle('fa-bars');
        menuIcon.classList.toggle('fa-times');
        
        if (window.innerWidth <= 768) {
            if (content.classList.contains('expanded')) {
                topHeader.style.left = '250px';
                topHeader.style.width = 'calc(100% - 250px)';
            } else {
                topHeader.style.left = '0';
                topHeader.style.width = '100%';
            }
        }
    });

    function saveMenuState() {
        const activeLink = document.querySelector('.nav-link.active, .sub-link.active');
        if (activeLink) {
            localStorage.setItem('activeLink', activeLink.getAttribute('href'));
        }
    }

    function restoreMenuState() {
        const activeLink = localStorage.getItem('activeLink');
        if (activeLink) {
            const link = document.querySelector(`.nav-link[href="${activeLink}"], .sub-link[href="${activeLink}"]`);
            if (link) {
                setActiveLink(link);
            }
        }
    }

    function setActiveLink(link) {
        // Remove active class from all links
        document.querySelectorAll('.nav-link, .sub-link').forEach(l => l.classList.remove('active'));
        
        // Add active class to the clicked link
        link.classList.add('active');
        
        // If it's a submenu link, expand the parent menu
        if (link.classList.contains('sub-link')) {
            const submenu = link.closest('.submenu');
            if (submenu) {
                submenu.classList.add('show');
                const toggle = document.querySelector(`[href="#${submenu.id}"]`);
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'true');
                    toggle.classList.remove('collapsed');
                }
            }
        }
    }

    function toggleSubmenu(toggle) {
        const submenuId = toggle.getAttribute('href').substring(1);
        const submenu = document.getElementById(submenuId);
        
        if (submenu.classList.contains('show')) {
            submenu.classList.remove('show');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.classList.add('collapsed');
        } else {
            submenu.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
            toggle.classList.remove('collapsed');
        }
    }

    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSubmenu(this);
        });
    });

    document.querySelectorAll('.nav-link, .sub-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.classList.contains('dropdown-toggle')) {
                setActiveLink(this);
                saveMenuState();
            }
        });
    });

    // Restore menu state on page load
    restoreMenuState();

    function adjustLayout() {
        if (window.innerWidth <= 768) {
            sidebar.style.height = window.innerHeight + 'px';
            if (content.classList.contains('expanded')) {
                topHeader.style.left = '250px';
                topHeader.style.width = 'calc(100% - 250px)';
            } else {
                topHeader.style.left = '0';
                topHeader.style.width = '100%';
            }
        } else {
            sidebar.style.height = '100vh';
            topHeader.style.left = '';
            topHeader.style.width = '';
        }
    }

    window.addEventListener('resize', adjustLayout);

    // Initial call to set layout
    adjustLayout();
});
</script>
</body>
</html>