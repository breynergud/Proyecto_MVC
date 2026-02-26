<?php
$pageTitle = 'Dashboard - Asignación de Transversales';
$activeNavItem = 'dashboard';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';
?>

<main class="main-content">
    <!-- Hero Section with Background -->
    <div class="dashboard-hero-section">
        <div class="hero-content-full">
            <div class="hero-text-center">
                <h1 class="hero-title">
                    BIENVENIDO AL<br>
                    <span class="hero-highlight">SISTEMA DE ASIGNACIÓN</span><br>
                    <span class="hero-highlight">DE TRANSVERSALES</span>
                </h1>
                <p class="hero-subtitle">
                    Gestión integral de competencias, programas y asignaciones académicas del SENA
                </p>
                
                <div class="hero-stats">
                    <?php if ($userRole === 'centro'): ?>
                    <div class="stat-item">
                        <i class="fa-solid fa-building"></i>
                        <div>
                            <span class="stat-number" id="totalSedes">0</span>
                            <span class="stat-label">Sedes</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fa-solid fa-cube"></i>
                        <div>
                            <span class="stat-number" id="totalAmbientes">0</span>
                            <span class="stat-label">Ambientes</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fa-solid fa-certificate"></i>
                        <div>
                            <span class="stat-number" id="totalCompetencias">0</span>
                            <span class="stat-label">Competencias</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <div>
                            <span class="stat-number" id="totalProgramas">0</span>
                            <span class="stat-label">Programas</span>
                        </div>
                    </div>
                    <?php elseif ($userRole === 'coordinador'): ?>
                    <div class="stat-item">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <div>
                            <span class="stat-number" id="totalProgramas">0</span>
                            <span class="stat-label">Comp x Prog</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fa-solid fa-folder-open"></i>
                        <div>
                            <span class="stat-number" id="totalFichas">0</span>
                            <span class="stat-label">Fichas</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fa-solid fa-calendar-check"></i>
                        <div>
                            <span class="stat-number" id="totalAsignaciones">0</span>
                            <span class="stat-label">Asignaciones</span>
                        </div>
                    </div>
                    <?php elseif ($userRole === 'instructor'): ?>
                    <div class="stat-item">
                        <i class="fa-solid fa-calendar-day"></i>
                        <div>
                            <span class="stat-number" id="misAsignacionesHoy">0</span>
                            <span class="stat-label">Asignaciones Hoy</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fa-solid fa-clock"></i>
                        <div>
                            <span class="stat-number" id="proximasAsignaciones">0</span>
                            <span class="stat-label">Pendientes</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="recent-activity-section">
        <h2 class="section-title">Actividad Reciente</h2>
        
        <div class="activity-card">
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-icon green">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="activity-content">
                        <p class="activity-text">Sistema iniciado correctamente</p>
                        <span class="activity-time">Hace unos momentos</span>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon blue">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div class="activity-content">
                        <p class="activity-text">Bienvenido al sistema de asignación de transversales</p>
                        <span class="activity-time">Hoy</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Cargar estadísticas según el rol
document.addEventListener('DOMContentLoaded', async () => {
    const userRole = '<?php echo $userRole; ?>';
    try {
        if (userRole === 'centro') {
            // Cargar sedes
            const sedesR = await fetch('../../routing.php?controller=sede&action=index');
            const sedes = await sedesR.json();
            document.getElementById('totalSedes').textContent = Array.isArray(sedes) ? sedes.length : 0;

            // Cargar ambientes
            const ambR = await fetch('../../routing.php?controller=ambiente&action=index');
            const amb = await ambR.json();
            document.getElementById('totalAmbientes').textContent = Array.isArray(amb) ? amb.length : 0;

            // Cargar competencias
            const compR = await fetch('../../routing.php?controller=competencia&action=index');
            const comp = await compR.json();
            document.getElementById('totalCompetencias').textContent = Array.isArray(comp) ? comp.length : 0;

            // Cargar programas
            const progR = await fetch('../../routing.php?controller=programa&action=index');
            const prog = await progR.json();
            document.getElementById('totalProgramas').textContent = Array.isArray(prog) ? prog.length : 0;

        } else if (userRole === 'coordinador') {
            const progR = await fetch('../../routing.php?controller=programa&action=index');
            const prog = await progR.json();
            document.getElementById('totalProgramas').textContent = Array.isArray(prog) ? prog.length : 0;

            const fichR = await fetch('../../routing.php?controller=ficha&action=index');
            const fich = await fichR.json();
            document.getElementById('totalFichas').textContent = Array.isArray(fich) ? fich.length : 0;

            const asigR = await fetch('../../routing.php?controller=asignacion&action=index');
            const asig = await asigR.json();
            document.getElementById('totalAsignaciones').textContent = Array.isArray(asig) ? asig.length : 0;
        } else if (userRole === 'instructor') {
            // Cargar solo asignaciones del instructor
            const asigR = await fetch('../../routing.php?controller=asignacion&action=index');
            const asig = await asigR.json();
            // Filtrar y contar (esto se refinará en el controlador después)
            document.getElementById('proximasAsignaciones').textContent = Array.isArray(asig) ? asig.length : 0;
        }

    } catch (error) {
        console.error('Error cargando estadísticas:', error);
    }
});
</script>

</body>
</html>
