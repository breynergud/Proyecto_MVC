<?php
$pageTitle = 'Asignaciones - SENA';
$activeNavItem = 'asignaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';
?>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../dashboard/index.php">Inicio</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Gestión de Asignaciones</span>
            </nav>
            <h1 class="page-title">Calendario de Asignaciones</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Buscador de Ficha -->
        <div class="search-ficha-container">
            <div class="search-ficha-card">
                <div class="search-ficha-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="searchFicha" placeholder="Buscar ficha (número o programa)..." class="search-input">
                </div>
                <button id="btnBuscarFicha" class="btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Buscar
                </button>
            </div>
            
            <!-- Info de la Ficha -->
            <div id="fichaInfo" class="ficha-info-card" style="display: none;">
                <div class="ficha-info-header">
                    <div>
                        <h3 id="fichaNumero">Ficha #</h3>
                        <p id="fichaProgramaNombre"></p>
                    </div>
                    <div class="ficha-info-badges">
                        <span class="badge badge-jornada" id="fichaJornada"></span>
                        <span class="badge badge-instructor" id="fichaInstructor"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendario Nativo -->
        <div id="calendarioContainer" style="display: none;" class="mt-4">
            <div class="calendar-card">
                <div class="calendar-header">
                    <div class="calendar-nav">
                        <button id="btnPrevMonth" class="nav-btn"><i class="fa-solid fa-chevron-left"></i></button>
                        <button id="btnToday" class="nav-btn-today">Hoy</button>
                        <button id="btnNextMonth" class="nav-btn"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                    <h2 id="monthDisplay" class="calendar-month-year">Mes Año</h2>
                    <div class="calendar-view-options">
                        <span class="view-tag">Vista Mensual</span>
                    </div>
                </div>
                
                <div class="calendar-weekdays">
                    <div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div><div>Dom</div>
                </div>
                
                <div id="calendarGrid" class="calendar-grid">
                    <!-- Los días se generarán dinámicamente -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal: Crear Asignación -->
<div class="modal fade" id="createAssignmentModal" tabindex="-1" aria-labelledby="createAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createAssignmentModalLabel">Nueva Asignación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createAssignmentForm">
                    <input type="hidden" name="ficha_id" id="modalFichaId">
                    
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="modalFechaInicio" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Hora Inicio</label>
                            <input type="time" name="hora_inicio" id="modalHoraInicio" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="modalFechaFin" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Hora Fin</label>
                            <input type="time" name="hora_fin" id="modalHoraFin" class="form-control" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Competencia</label>
                            <select name="competencia_id" id="modalCompetencia" class="form-select" required>
                                <option value="">Seleccione una competencia...</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Instructor</label>
                            <select name="instructor_id" id="modalInstructor" class="form-select" required>
                                <option value="">Seleccione un instructor...</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ambiente</label>
                            <select name="ambiente_id" id="modalAmbiente" class="form-select" required>
                                <option value="">Seleccione un ambiente...</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnGuardarNuevaAsignacion" class="btn btn-success">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Asignación
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Ver Detalles -->
<div class="modal fade" id="viewAssignmentModal" tabindex="-1" aria-labelledby="viewAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewAssignmentModalLabel">Detalles de Asignación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="assignmentDetails">
                    <p><strong>Competencia:</strong> <span id="viewDetailCompetencia"></span></p>
                    <p><strong>Instructor:</strong> <span id="viewDetailInstructor"></span></p>
                    <p><strong>Inicia:</strong> <span id="viewDetailInicio"></span></p>
                    <p><strong>Termina:</strong> <span id="viewDetailFin"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="btnEliminarAsignacion">
                    <i class="fa-solid fa-trash-can me-2"></i>Eliminar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --sena-green: #39A900;
    --sena-green-dark: #2d8500;
    --sena-orange: #FF6B00;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-700: #374151;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}

.calendar-card {
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    padding: 24px;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.calendar-nav {
    display: flex;
    gap: 8px;
}

.nav-btn, .nav-btn-today {
    background: white;
    border: 1px solid var(--gray-200);
    padding: 8px 12px;
    border-radius: 8px;
    color: var(--gray-700);
    transition: all 0.2s;
    cursor: pointer;
}

.nav-btn:hover, .nav-btn-today:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
    color: var(--sena-green);
}

.nav-btn-today {
    font-weight: 600;
    padding: 8px 16px;
}

.calendar-month-year {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-700);
    margin: 0;
    text-transform: capitalize;
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: var(--gray-50);
    border-radius: 8px;
    margin-bottom: 8px;
}

.calendar-weekdays div {
    padding: 12px;
    text-align: center;
    font-weight: 600;
    color: var(--gray-700);
    font-size: 0.875rem;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: var(--gray-200);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    overflow: hidden;
}

.calendar-day {
    background: white;
    min-height: 120px;
    padding: 8px;
    transition: background 0.2s;
    cursor: pointer;
    display: flex;
    flex-direction: column;
}

.calendar-day:hover {
    background: var(--gray-50);
}

.calendar-day.other-month {
    background: #fbfbfb;
    color: #9ca3af;
}

.calendar-day.today {
    background: #f0fdf4;
}

.day-number {
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 8px;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.calendar-day.today .day-number {
    background: var(--sena-green);
    color: white;
}

.event-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
    overflow-y: auto;
    max-height: 80px;
}

.event-item {
    font-size: 0.75rem;
    padding: 4px 8px;
    background: #dcfce7;
    color: #166534;
    border-left: 3px solid var(--sena-green);
    border-radius: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 600;
}

.event-item:hover {
    filter: brightness(0.95);
}

.search-ficha-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.search-ficha-icon {
    width: 40px;
    height: 40px;
    background: #f3f4f6;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #39A900;
    font-size: 20px;
}
.ficha-info-card {
    margin-top: 16px;
    padding: 20px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}
.ficha-info-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.ficha-info-header h3 {
    font-size: 20px;
    color: #1f2937;
    margin-bottom: 4px;
}
.badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}
.badge-jornada {
    background: #fef9c3;
    color: #854d0e;
}
.badge-instructor {
    background: #f0fdf4;
    color: #166534;
}
</style>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/asignacion/index.js?v=<?php echo time(); ?>"></script>
</body>
</html>
