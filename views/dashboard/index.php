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
// Cargar estadísticas del dashboard
document.addEventListener('DOMContentLoaded', async () => {
    try {
        // Cargar sedes
        const sedesResponse = await fetch('../../routing.php?controller=sede&action=index', {
            headers: { 'Accept': 'application/json' }
        });
        const sedes = await sedesResponse.json();
        document.getElementById('totalSedes').textContent = Array.isArray(sedes) ? sedes.length : 0;

        // Cargar ambientes
        const ambientesResponse = await fetch('../../routing.php?controller=ambiente&action=index', {
            headers: { 'Accept': 'application/json' }
        });
        const ambientes = await ambientesResponse.json();
        document.getElementById('totalAmbientes').textContent = Array.isArray(ambientes) ? ambientes.length : 0;

        // Cargar competencias
        const competenciasResponse = await fetch('../../routing.php?controller=competencia&action=index', {
            headers: { 'Accept': 'application/json' }
        });
        const competencias = await competenciasResponse.json();
        document.getElementById('totalCompetencias').textContent = Array.isArray(competencias) ? competencias.length : 0;

        // Cargar programas (si existe)
        try {
            const programasResponse = await fetch('../../routing.php?controller=programa&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const programas = await programasResponse.json();
            document.getElementById('totalProgramas').textContent = Array.isArray(programas) ? programas.length : 0;
        } catch (e) {
            document.getElementById('totalProgramas').textContent = '0';
        }

    } catch (error) {
        console.error('Error cargando estadísticas:', error);
    }
});
</script>

</body>
</html>
