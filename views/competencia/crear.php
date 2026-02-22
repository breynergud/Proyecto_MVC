<?php
$pageTitle = 'Registrar Competencia - SENA';
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
                <span>Registrar</span>
            </nav>
            <h1 class="page-title">Registrar Nueva Competencia</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="form-card">
            <div class="form-header">
                <div class="form-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h2>Información de la Competencia</h2>
                    <p>Complete los datos para registrar una nueva competencia</p>
                </div>
            </div>

            <form id="competenciaForm" class="form-content">
                <div class="form-group">
                    <label for="comp_nombre_corto" class="form-label required">Nombre Corto</label>
                    <input type="text" id="comp_nombre_corto" name="comp_nombre_corto" class="form-input" placeholder="Ej: Desarrollo de Software" required>
                    <div class="form-error" id="comp_nombre_corto_error"></div>
                    <div class="form-help">
                        Ingrese un nombre descriptivo y conciso para la competencia.
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
                        Código y descripción de la norma o unidad de competencia según el marco nacional.
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Guardar Competencia
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="info-card">
            <div class="info-header">
                <i class="fa-solid fa-circle-info"></i>
                <h3>Información Importante</h3>
            </div>
            <div class="info-content">
                <ul>
                    <li>Las competencias se asociarán posteriormente a programas de formación</li>
                    <li>El nombre corto debe ser único y descriptivo</li>
                    <li>Las horas deben corresponder a la duración establecida en la norma</li>
                    <li>Puede editar la información posteriormente si es necesario</li>
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
            <h3>Competencia Registrada</h3>
        </div>
        <div class="modal-body">
            <p>La competencia ha sido registrada correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Ver Competencias</a>
            <button class="btn-secondary" onclick="closeSuccessModal()">Registrar Otra</button>
        </div>
    </div>
</div>

<script src="../../assets/js/competencia/crear.js?v=<?php echo time(); ?>"></script>
</body>

</html>
