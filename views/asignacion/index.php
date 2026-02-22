<?php
$pageTitle = 'Asignaciones - SENA';
$activeNavItem = 'asignaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';
?>

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

        <!-- Calendario -->
        <div id="calendarioContainer" style="display: none;">
            <div class="calendario-header">
                <button id="btnPrevMonth" class="btn-nav-month">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <h2 id="mesAnio">Febrero 2026</h2>
                <button id="btnNextMonth" class="btn-nav-month">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <div class="calendario-grid">
                <div class="calendario-dias-header">
                    <div class="dia-header">DOM</div>
                    <div class="dia-header">LUN</div>
                    <div class="dia-header">MAR</div>
                    <div class="dia-header">MIÉ</div>
                    <div class="dia-header">JUE</div>
                    <div class="dia-header">VIE</div>
                    <div class="dia-header">SÁB</div>
                </div>
                <div id="calendarioDias" class="calendario-dias">
                    <!-- Los días se generarán dinámicamente -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal: Crear Asignación -->
<div id="asignacionModal" class="modal">
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h3>Nueva Asignación</h3>
            <button class="modal-close" onclick="closeAsignacionModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <!-- Fecha seleccionada -->
            <div class="selected-date-info">
                <i class="fa-solid fa-calendar-days"></i>
                <span id="selectedDate"></span>
            </div>

            <!-- Seleccionar Competencia -->
            <div class="form-group">
                <label class="form-label required">Competencia Pendiente</label>
                <select id="competenciaSelect" class="form-input">
                    <option value="">Seleccione una competencia...</option>
                </select>
                <div class="form-help">Competencias que faltan por asignar a esta ficha</div>
            </div>

            <!-- Seleccionar Ambiente -->
            <div class="form-group">
                <label class="form-label required">Ambiente</label>
                <select id="ambienteSelect" class="form-input">
                    <option value="">Seleccione un ambiente...</option>
                </select>
            </div>

            <!-- Horario -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">Hora Inicio</label>
                    <input type="time" id="horaInicio" class="form-input" value="06:00">
                </div>
                <div class="form-group">
                    <label class="form-label required">Hora Fin</label>
                    <input type="time" id="horaFin" class="form-input" value="12:00">
                </div>
            </div>

            <!-- Lista de Instructores -->
            <div id="instructoresSection" style="display: none;">
                <h4 style="margin: 20px 0 12px; color: #1f2937; font-size: 16px;">
                    Instructores Especializados
                </h4>
                <div id="instructoresList" class="instructores-list">
                    <!-- Se llenará dinámicamente -->
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeAsignacionModal()">Cancelar</button>
            <button id="btnGuardarAsignacion" class="btn-primary" disabled>
                <i class="fa-solid fa-floppy-disk"></i>
                Guardar Asignación
            </button>
        </div>
    </div>
</div>

<style>
.search-ficha-container {
    margin-bottom: 24px;
}

.search-ficha-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.search-ficha-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #39A900 0%, #2d8000 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.search-ficha-input-group {
    flex: 1;
}

.search-ficha-input-group label {
    display: block;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 4px;
    font-weight: 500;
}

.ficha-info-card {
    margin-top: 16px;
    padding: 20px;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-left: 4px solid #39A900;
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

.ficha-info-header p {
    color: #6b7280;
    font-size: 14px;
}

.ficha-info-badges {
    display: flex;
    gap: 8px;
}

.badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.badge-jornada {
    background: #dbeafe;
    color: #1e40af;
}

.badge-instructor {
    background: #fef3c7;
    color: #92400e;
}

.calendario-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    background: white;
    border-radius: 12px 12px 0 0;
    border-bottom: 2px solid #e5e7eb;
}

.calendario-header h2 {
    font-size: 24px;
    color: #1f2937;
}

.btn-nav-month {
    width: 40px;
    height: 40px;
    border: none;
    background: #f3f4f6;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-nav-month:hover {
    background: #39A900;
    color: white;
}

.calendario-grid {
    background: white;
    border-radius: 0 0 12px 12px;
    padding: 20px;
}

.calendario-dias-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 8px;
}

.dia-header {
    text-align: center;
    font-weight: 700;
    color: #39A900;
    padding: 12px;
    font-size: 14px;
}

.calendario-dias {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.dia-cell {
    min-height: 120px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.dia-cell:hover {
    border-color: #39A900;
    background: #f9fafb;
}

.dia-cell.otro-mes {
    background: #f9fafb;
    opacity: 0.5;
}

.dia-numero {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.dia-cell.otro-mes .dia-numero {
    color: #9ca3af;
}

.asignacion-item {
    background: #39A900;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    margin-bottom: 4px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 4px;
}

.btn-delete-asig {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    padding: 2px;
    font-size: 10px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-delete-asig:hover {
    color: white;
    transform: scale(1.1);
}

.selected-date-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f0fdf4;
    border-left: 4px solid #39A900;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 600;
    color: #1f2937;
}

.selected-date-info i {
    font-size: 1.25rem;
    color: #39A900;
}

.instructores-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 300px;
    overflow-y: auto;
}

.instructor-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.instructor-card:hover {
    border-color: #39A900;
    background: #f9fafb;
}

.instructor-card.selected {
    border-color: #39A900;
    background: #f0fdf4;
}

.instructor-info {
    flex: 1;
}

.instructor-nombre {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.instructor-detalles {
    font-size: 12px;
    color: #6b7280;
}

.instructor-radio {
    width: 20px;
    height: 20px;
    accent-color: #39A900;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #6b7280;
    cursor: pointer;
    transition: color 0.2s;
}

.modal-close:hover {
    color: #1f2937;
}

/* Disabled days */
.dia-cell.disabled {
    background: #f3f4f6;
    cursor: not-allowed;
    opacity: 0.6;
}

.dia-cell.disabled:hover {
    border-color: #e5e7eb;
}
</style>

<script src="../../assets/js/asignacion/index.js?v=<?php echo time(); ?>"></script>
</body>
</html>
