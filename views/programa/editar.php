<?php
$pageTitle = 'Editar Programa - SENA';
$activeNavItem = 'programas';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';

$id = $_GET['id'] ?? null;
?>

<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../dashboard/index.php">Inicio</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="index.php">Programas</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Modificar Programa</h1>
        </div>

        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Form Card -->
        <div class="form-card">
            <div class="form-header">
                <div class="form-icon">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h2>Actualizar Registro</h2>
                    <p>Modifique los datos del programa académico</p>
                </div>
            </div>

            <form id="editarProgramaForm" class="form-content">
                <input type="hidden" name="prog_id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="prog_codigo" class="form-label required">Código de Programa</label>
                        <input type="number" id="prog_codigo" name="prog_codigo" class="form-input" required>
                        <div class="form-help">Código SofíaPlus original.</div>
                    </div>

                    <div class="form-group">
                        <label for="prog_tipo" class="form-label required">Tipo de Programa</label>
                        <select id="prog_tipo" name="prog_tipo" class="form-input" required>
                            <option value="" disabled>Seleccione tipo...</option>
                            <option value="Tecnólogo">Tecnólogo</option>
                            <option value="Técnico">Técnico</option>
                            <option value="Especialización">Especialización</option>
                            <option value="Curso Corto">Curso Corto</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="prog_denominacion" class="form-label required">Denominación</label>
                        <input type="text" id="prog_denominacion" name="prog_denominacion" class="form-input" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="tit_programa_titpro_id" class="form-label required">Título Académico</label>
                        <select id="tit_programa_titpro_id" name="tit_programa_titpro_id" class="form-input" required>
                            <option value="" disabled>Seleccione un título...</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Actualizar Programa
                    </button>
                </div>
            </form>
        </div>

        <!-- Competencias Asociadas -->
        <div class="form-card" style="margin-top: 24px;">
            <div class="form-header">
                <div class="info-header">
                    <i class="fa-solid fa-list-check"></i>
                    <h3>Gestión de Competencias</h3>
                </div>
                <div>
                    <h2>Competencias del Programa</h2>
                    <p>Gestione las competencias asociadas a este programa</p>
                </div>
            </div>

            <div class="form-content">
                <!-- Buscador -->
                <div class="form-group">
                    <label class="form-label">Buscar y Agregar Competencias</label>
                    <div class="search-container">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="searchCompetencia" placeholder="Buscar competencias disponibles..." class="search-input">
                    </div>
                </div>

                <!-- Competencias Disponibles -->
                <div id="competenciasDisponibles" style="display: none; margin-bottom: 20px;">
                    <h4 style="margin-bottom: 12px; font-size: 14px; color: #6b7280;">Competencias Disponibles</h4>
                    <div id="competenciasDisponiblesList" class="competencias-list-small"></div>
                </div>

                <!-- Competencias Asociadas -->
                <div>
                    <h4 style="margin-bottom: 12px; font-size: 14px; color: #1f2937;">
                        Competencias Asociadas (<span id="competenciasCount">0</span>)
                    </h4>
                    <div id="competenciasAsociadas" class="competencias-list-small">
                        <p class="text-center text-gray-500">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.competencias-list-small {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 300px;
    overflow-y: auto;
}

.competencia-item-small {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    transition: all 0.2s;
}

.competencia-item-small:hover {
    border-color: #39A900;
    background: #f9fafb;
}

.competencia-item-info {
    flex: 1;
}

.competencia-item-nombre {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.competencia-item-horas {
    display: inline-block;
    padding: 2px 8px;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.competencia-item-actions {
    display: flex;
    gap: 8px;
}

.btn-icon-small {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-add {
    background: #39A900;
    color: white;
}

.btn-add:hover {
    background: #2d8000;
}

.btn-remove {
    background: #ef4444;
    color: white;
}

.btn-remove:hover {
    background: #dc2626;
}

.btn-icon-small i {
    font-size: 18px;
}
</style>

<script src="../../assets/js/programa/editar.js?v=<?php echo time(); ?>"></script>
</body>

</html>