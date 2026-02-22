<?php
$activeNavItem = isset($activeNavItem) ? $activeNavItem : 'sedes';
?>
<!-- Top Navbar -->
<nav class="top-navbar" id="topNavbar">
    <div class="navbar-container">
        <!-- Toggle Button -->
        <button class="navbar-toggle" id="navbarToggle" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars menu-icon"></i>
            <i class="fa-solid fa-xmark close-icon"></i>
        </button>

        <!-- Logo Section -->
        <div class="navbar-logo">
            <img src="../../assets/imagenes/LOGOsena.png" alt="SENA Logo" class="logo-img">
            <span class="logo-text">Asignación de Transversales</span>
        </div>

        <!-- Navigation Links -->
        <div class="navbar-links" id="navbarLinks">
            <a href="../dashboard/index.php" class="nav-link <?php echo ($activeNavItem === 'dashboard') ? 'active' : ''; ?>">
                <i class="fa-solid fa-table-cells-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="../sede/index.php" class="nav-link <?php echo ($activeNavItem === 'sedes') ? 'active' : ''; ?>">
                <i class="fa-solid fa-building"></i>
                <span>Sedes</span>
            </a>
            <a href="../ambiente/index.php" class="nav-link <?php echo ($activeNavItem === 'ambientes') ? 'active' : ''; ?>">
                <i class="fa-solid fa-cube"></i>
                <span>Ambientes</span>
            </a>
            <a href="../competencia/index.php" class="nav-link <?php echo ($activeNavItem === 'competencias') ? 'active' : ''; ?>">
                <i class="fa-solid fa-certificate"></i>
                <span>Competencias</span>
            </a>
            <a href="../programa/index.php" class="nav-link <?php echo ($activeNavItem === 'programas') ? 'active' : ''; ?>">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Programas</span>
            </a>
            <a href="../titulo_programa/index.php" class="nav-link <?php echo ($activeNavItem === 'titulos') ? 'active' : ''; ?>">
                <i class="fa-solid fa-medal"></i>
                <span>Títulos</span>
            </a>
            <a href="#" class="nav-link <?php echo ($activeNavItem === 'instructores') ? 'active' : ''; ?>">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Instructores</span>
            </a>
        </div>

        <!-- User Profile -->
        <div class="navbar-user">
            <div class="user-info">
                <p class="user-name">Carlos Rodriguez</p>
                <p class="user-role">Coordinador</p>
            </div>
            <img src="../../assets/imagenes/Foto-nota-2024-01-03T181318.611.webp" alt="Usuario" class="user-avatar">
            <button class="logout-btn" title="Cerrar sesión">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Navbar Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const navbarToggle = document.getElementById('navbarToggle');
    const topNavbar = document.getElementById('topNavbar');
    const navbarLinks = document.getElementById('navbarLinks');
    
    navbarToggle.addEventListener('click', () => {
        topNavbar.classList.toggle('collapsed');
        navbarLinks.classList.toggle('show');
    });
    
    // Close navbar when clicking outside
    document.addEventListener('click', (e) => {
        if (!topNavbar.contains(e.target) && navbarLinks.classList.contains('show')) {
            topNavbar.classList.remove('collapsed');
            navbarLinks.classList.remove('show');
        }
    });
});
</script>

<!-- Custom Notifications -->
<?php require_once dirname(__DIR__) . '/layouts/notifications.php'; ?>
<script src="../../assets/js/utils/notifications.js?v=<?php echo time(); ?>"></script>
