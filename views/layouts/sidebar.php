<?php
$activeNavItem = isset($activeNavItem) ? $activeNavItem : 'sedes';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../../assets/imagenes/LOGOsena.png" alt="SENA Logo" class="logo-img">
            <div class="logo-divider"></div>
            <span class="logo-text">Gestión de Transversales</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <p class="nav-section">Principal</p>
        <a href="../dashboard/index.php" class="nav-item <?php echo ($activeNavItem === 'dashboard') ? 'active' : ''; ?>">
            <i class="fa-solid fa-table-cells-large"></i>
            Dashboard
        </a>

        <p class="nav-section">Gestión</p>
        <a href="../sede/index.php" class="nav-item <?php echo ($activeNavItem === 'sedes') ? 'active' : ''; ?>">
            <i class="fa-solid fa-building"></i>
            Sedes
        </a>
        <a href="../ambiente/index.php" class="nav-item <?php echo ($activeNavItem === 'ambientes') ? 'active' : ''; ?>">
            <i class="fa-solid fa-cube"></i>
            Ambientes
        </a>
        <a href="../programa/index.php" class="nav-item <?php echo ($activeNavItem === 'programas') ? 'active' : ''; ?>">
            <i class="fa-solid fa-graduation-cap"></i>
            Programas
        </a>
        <a href="../titulo_programa/index.php" class="nav-item <?php echo ($activeNavItem === 'titulos') ? 'active' : ''; ?>">
            <i class="fa-solid fa-certificate"></i>
            Títulos de Programa
        </a>
        <a href="#" class="nav-item <?php echo ($activeNavItem === 'instructores') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chalkboard-user"></i>
            Instructores
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <img src="../../assets/imagenes/Foto-nota-2024-01-03T181318.611.webp" alt="Coordinador" class="profile-img">
            <div class="profile-info">
                <p class="profile-name">Carlos Rodriguez</p>
                <p class="profile-role">Coordinador Académico</p>
            </div>
            <i class="fa-solid fa-right-from-bracket"></i>
        </div>
    </div>
</aside>

<!-- Custom Notifications -->
<?php require_once dirname(__DIR__) . '/layouts/notifications.php'; ?>
<script src="../../assets/js/utils/notifications.js?v=<?php echo time(); ?>"></script>