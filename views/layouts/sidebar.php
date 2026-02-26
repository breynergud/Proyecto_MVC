<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$activeNavItem = isset($activeNavItem) ? $activeNavItem : 'sedes';
$userRole = $_SESSION['user_role'] ?? '';
$userName = $_SESSION['user_name'] ?? 'Usuario';
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

        <?php if ($userRole === 'centro'): ?>
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
        <a href="../instructor/index.php" class="nav-item <?php echo ($activeNavItem === 'instructores') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chalkboard-user"></i>
            Instructores
        </a>
        <a href="../competencia/index.php" class="nav-item <?php echo ($activeNavItem === 'competencias') ? 'active' : ''; ?>">
            <i class="fa-solid fa-certificate"></i>
            Competencias
        </a>
        <a href="../coordinacion/index.php" class="nav-item <?php echo ($activeNavItem === 'coordinaciones') ? 'active' : ''; ?>">
            <i class="fa-solid fa-sitemap"></i>
            Coordinaciones
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'coordinador'): ?>
        <a href="../programa/index.php" class="nav-item <?php echo ($activeNavItem === 'programas') ? 'active' : ''; ?>">
            <i class="fa-solid fa-graduation-cap"></i>
            Comp x Programa
        </a>
        <a href="../ficha/index.php" class="nav-item <?php echo ($activeNavItem === 'fichas') ? 'active' : ''; ?>">
            <i class="fa-solid fa-folder-open"></i>
            Fichas
        </a>
        <a href="../instructor/index.php" class="nav-item <?php echo ($activeNavItem === 'instructores') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chalkboard-user"></i>
            Instructor x Competencia
        </a>
        <a href="../asignacion/index.php" class="nav-item <?php echo ($activeNavItem === 'asignaciones') ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-days"></i>
            Asignación
        </a>
        <?php endif; ?>

        <?php if ($userRole === 'instructor'): ?>
        <a href="../asignacion/index.php" class="nav-item <?php echo ($activeNavItem === 'asignaciones') ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-days"></i>
            Visualizar Asignación
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <img src="../../assets/imagenes/Foto-nota-2024-01-03T181318.611.webp" alt="Coordinador" class="profile-img">
            <div class="profile-info">
                <p class="profile-name"><?php echo htmlspecialchars($userName); ?></p>
                <p class="profile-role"><?php echo ucfirst(htmlspecialchars($userRole)); ?></p>
            </div>
            <button class="logout-btn" title="Cerrar sesión" onclick="logout()" style="background: none; border: none; cursor: pointer; color: inherit;">
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