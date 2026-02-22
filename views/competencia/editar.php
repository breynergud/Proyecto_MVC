<?php
$pageTitle = 'Editar Competencia - SENA';
$activeNavItem = 'competencias';
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
                <a href="index.php">Competencias</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Editar Competencia</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Loading State -->
        <div id="loadingState" class="loading-card">
            <div class="loading-spinner"></div>
            <p>Cargando información de la competencia...</p>
        </div>

        <!-- Form Card -->
        <div id="formCard" class="form-card" style="display: none;">
            <div class="form-header">
                <div class="form-icon">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h2>Modificar Información</h2>
                    <p>Actualice los datos de la competencia seleccionada</p>
                </div>
            </div>

            <form id="competenciaEditForm" class="form-content">
                <input type="hidden" id="comp_id" name="comp_id">

                <div class="form-group">
                    <label for="comp_nombre_corto" class="form-label required">Nombre Corto</label>
                    <input type="text" id="comp_nombre_corto" name="comp_nombre_corto" class="form-input" placeholder="Ej: Desarrollo de Software" required>
                    <div class="form-error" id="comp_nombre_corto_error"></div>
                    <div class="form-help">
                        Modifique el nombre descriptivo de la competencia.
                    </div>
                </div>

                <div class="form-group">
                    <label for="comp_horas" class="form-label required">Horas</label>
                    <input type="number" id="comp_horas" name="comp_horas" class="form-input" placeholder="Ej: 120" min="1" required>
                    <div class="form-error" id="comp_horas_error"></div>
                    <div class="form-help">
                        Cantidad de horas asignadas a esta competencia.
                    </div>
                </div>

                <div class="form-group">
                    <label for="comp_nombre_unidad_competencia" class="form-label required">Norma/Unidad de Competencia</label>
                    <textarea id="comp_nombre_unidad_competencia" name="comp_nombre_unidad_competencia" class="form-input" rows="4" placeholder="Ej: NCL 210101001 - Desarrollar software aplicando..." required></textarea>
                    <div class="form-error" id="comp_nombre_unidad_competencia_error"></div>
                    <div class="form-help">
                        Código y descripción de la norma o unidad de competencia.
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Actualizar Competencia
                    </button>
                </div>
            </form>
        </div>

        <!-- Error Card -->
        <div id="errorCard" class="error-card" style="display: none;">
            <div class="error-icon">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div>
                <h3>Error al Cargar</h3>
                <p id="errorMessage">No se pudo cargar la información de la competencia.</p>
                <a href="index.php" class="btn-primary mt-4">Volver a Competencias</a>
            </div>
        </div>

        <!-- Info Card -->
        <div id="infoCard" class="info-card" style="display: none;">
            <div class="info-header">
                <i class="fa-solid fa-circle-info"></i>
                <h3>Información de Edición</h3>
            </div>
            <div class="info-content">
                <ul>
                    <li>Los cambios se aplicarán inmediatamente al guardar</li>
                    <li>Verifique que las horas correspondan a la norma establecida</li>
                    <li>Si la competencia está asociada a programas, los cambios se reflejarán en ellos</li>
                    <li>Puede cancelar en cualquier momento sin guardar cambios</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<!-- Success Modal -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <div class="modal-header success">
            <i class="fa-solid fa-circle-check"></i>
            <h3>Competencia Actualizada</h3>
        </div>
        <div class="modal-body">
            <p>La competencia ha sido actualizada correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Ver Competencias</a>
            <button class="btn-secondary" onclick="closeSuccessModal()">Continuar Editando</button>
        </div>
    </div>
</div>

<script src="../../assets/js/competencia/editar.js?v=<?php echo time(); ?>"></script>
</body>

</html>
