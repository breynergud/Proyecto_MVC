<?php
$activeNavItem = isset($activeNavItem) ? $activeNavItem : 'sedes';
?>
<!-- Sidebar Verde SENA -->
<aside class="sidebar-green">
    <div class="sidebar-header-green">
        <div class="logo-green">
            <img src="../../assets/imagenes/LOGOsena.png" alt="SENA Logo" class="logo-img-green">
            <div class="logo-divider-green"></div>
            <div class="logo-text-container">
                <span class="logo-text-green">Asignación de</span>
                <span class="logo-text-green">Transversales</span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav-green">
        <p class="nav-section-green">MENÚ PRINCIPAL</p>
        
        <a href="../dashboard/index.php" class="nav-item-green <?php echo ($activeNavItem === 'dashboard') ? 'active' : ''; ?>">
            <i class="fa-solid fa-table-cells-large"></i>
            <span>Dashboard</span>
        </a>



        <a href="../sede/index.php" class="nav-item-green <?php echo ($activeNavItem === 'sedes') ? 'active' : ''; ?>">
            <i class="fa-solid fa-building"></i>
            <span>Sedes</span>
        </a>

        <a href="../ambiente/index.php" class="nav-item-green <?php echo ($activeNavItem === 'ambientes') ? 'active' : ''; ?>">
            <i class="fa-solid fa-cube"></i>
            <span>Ambientes</span>
        </a>

        <a href="../competencia/index.php" class="nav-item-green <?php echo ($activeNavItem === 'competencias') ? 'active' : ''; ?>">
            <i class="fa-solid fa-certificate"></i>
            <span>Competencias</span>
        </a>

        <a href="../programa/index.php" class="nav-item-green <?php echo ($activeNavItem === 'programas') ? 'active' : ''; ?>">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>Programas</span>
        </a>

        <a href="../titulo_programa/index.php" class="nav-item-green <?php echo ($activeNavItem === 'titulos') ? 'active' : ''; ?>">
            <i class="fa-solid fa-medal"></i>
            <span>Títulos</span>
        </a>

        <a href="../ficha/index.php" class="nav-item-green <?php echo ($activeNavItem === 'fichas') ? 'active' : ''; ?>">
            <i class="fa-solid fa-folder-open"></i>
            <span>Fichas</span>
        </a>

        <a href="../asignacion/index.php" class="nav-item-green <?php echo ($activeNavItem === 'asignaciones') ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-days"></i>
            <span>Asignaciones</span>
        </a>

        <a href="../coordinacion/index.php" class="nav-item-green <?php echo ($activeNavItem === 'coordinaciones') ? 'active' : ''; ?>">
            <i class="fa-solid fa-sitemap"></i>
            <span>Coordinaciones</span>
        </a>

        <a href="../centro_formacion/index.php" class="nav-item-green <?php echo ($activeNavItem === 'centros') ? 'active' : ''; ?>">
            <i class="fa-solid fa-landmark"></i>
            <span>Centros de Formación</span>
        </a>

        <a href="../instructor/index.php" class="nav-item-green <?php echo ($activeNavItem === 'instructores') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i>
            <span>Instructores</span>
        </a>
    </nav>

    <div class="sidebar-footer-green">
        <div class="user-profile-green">
            <img src="../../assets/imagenes/LOGOsena.png" alt="Usuario" class="profile-img-green">
            <div class="profile-info-green">
                <p class="profile-name-green">Pepito</p>
                <p class="profile-role-green">Coordinador</p>
            </div>
            <button class="logout-btn-green" title="Cerrar sesión">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </div>
    </div>
</aside>

<!-- Custom Notifications -->
<?php require_once dirname(__DIR__) . '/layouts/notifications.php'; ?>
<script src="../../assets/js/utils/notifications.js?v=<?php echo time(); ?>"></script>
