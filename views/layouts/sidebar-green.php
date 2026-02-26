<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$activeNavItem = isset($activeNavItem) ? $activeNavItem : 'dashboard';
$userRole = $_SESSION['user_role'] ?? '';
$userName = $_SESSION['user_name'] ?? 'Usuario';
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

        <?php if ($userRole === 'centro'): ?>
        <a href="../sede/index.php" class="nav-item-green <?php echo ($activeNavItem === 'sedes') ? 'active' : ''; ?>">
            <i class="fa-solid fa-building"></i>
            <span>Sedes</span>
        </a>

        <a href="../ambiente/index.php" class="nav-item-green <?php echo ($activeNavItem === 'ambientes') ? 'active' : ''; ?>">
            <i class="fa-solid fa-cube"></i>
            <span>Ambientes</span>
        </a>

        <a href="../programa/index.php" class="nav-item-green <?php echo ($activeNavItem === 'programas') ? 'active' : ''; ?>">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>Programas</span>
        </a>

        <a href="../instructor/index.php" class="nav-item-green <?php echo ($activeNavItem === 'instructores') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i>
            <span>Instructores</span>
        </a>

        <a href="../competencia/index.php" class="nav-item-green <?php echo ($activeNavItem === 'competencias') ? 'active' : ''; ?>">
            <i class="fa-solid fa-certificate"></i>
            <span>Competencias</span>
        </a>

        <a href="../coordinacion/index.php" class="nav-item-green <?php echo ($activeNavItem === 'coordinaciones') ? 'active' : ''; ?>">
            <i class="fa-solid fa-sitemap"></i>
            <span>Coordinaciones</span>
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'coordinador'): ?>
        <a href="../programa/index.php" class="nav-item-green <?php echo ($activeNavItem === 'programas') ? 'active' : ''; ?>">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>Comp x Programa</span>
        </a>

        <a href="../ficha/index.php" class="nav-item-green <?php echo ($activeNavItem === 'fichas') ? 'active' : ''; ?>">
            <i class="fa-solid fa-folder-open"></i>
            <span>Fichas</span>
        </a>

        <a href="../instructor/index.php" class="nav-item-green <?php echo ($activeNavItem === 'instructores') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i>
            <span>Instructor x Competencia</span>
        </a>

        <a href="../asignacion/index.php" class="nav-item-green <?php echo ($activeNavItem === 'asignaciones') ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-days"></i>
            <span>Asignación</span>
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'instructor'): ?>
        <a href="../asignacion/index.php" class="nav-item-green <?php echo ($activeNavItem === 'asignaciones') ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-days"></i>
            <span>Visualizar Asignación</span>
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer-green">
        <div class="user-profile-green">
            <img src="../../assets/imagenes/LOGOsena.png" alt="Usuario" class="profile-img-green">
            <div class="profile-info-green">
                <p class="profile-name-green"><?php echo htmlspecialchars($userName); ?></p>
                <p class="profile-role-green"><?php echo ucfirst(htmlspecialchars($userRole)); ?></p>
            </div>
            <button class="logout-btn-green" title="Cerrar sesión" onclick="logout()">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </div>
    </div>
    <script>
        async function logout() {
            try {
                const response = await fetch('../../routing.php?controller=auth&action=logout');
                if (response.ok) {
                    window.location.href = '../auth/login.php';
                }
            } catch (error) {
                console.error('Error al cerrar sesión', error);
            }
        }
    </script>
</aside>

<!-- Custom Notifications -->
<?php require_once dirname(__DIR__) . '/layouts/notifications.php'; ?>
<script src="../../assets/js/utils/notifications.js?v=<?php echo time(); ?>"></script>
