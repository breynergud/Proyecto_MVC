<?php
$pageTitle = 'Editar Sede - SENA';
$activeNavItem = 'sedes';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';
?>

<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="index.php">Sedes</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Editar Sede</h1>
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
            <p>Cargando información de la sede...</p>
        </div>

        <!-- Form Card -->
        <div id="formCard" class="form-card" style="display: none;">
            <div class="form-header">
                <div class="form-icon">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h2>Modificar Información</h2>
                    <p>Actualice los datos de la sede seleccionada</p>
                </div>
            </div>

            <form id="sedeEditForm" class="form-content">
                <input type="hidden" id="sede_id" name="sede_id">

                <div class="form-group">
                    <label for="sede_nombre" class="form-label required">
                        Nombre de la Sede
                    </label>
                    <input
                        type="text"
                        id="sede_nombre"
                        name="sede_nombre"
                        class="form-input"
                        placeholder="Ej: Centro de Tecnologías Avanzadas"
                        required>
                    <div class="form-error" id="sede_nombre_error"></div>
                    <div class="form-help">
                        Modifique el nombre de la sede. Debe ser único en el sistema.
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Actualizar Sede
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
                <p id="errorMessage">No se pudo cargar la información de la sede.</p>
                <a href="index.php" class="btn-primary mt-4">Volver a Sedes</a>
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
                    <li>El nombre debe ser único en el sistema</li>
                    <li>Verifique que no existan programas asignados antes de cambios importantes</li>
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
            <h3>Sede Actualizada</h3>
        </div>
        <div class="modal-body">
            <p>La sede <strong id="updatedSedeName"></strong> ha sido actualizada correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Ver Sedes</a>
            <button class="btn-secondary" onclick="closeSuccessModal()">Continuar Editando</button>
        </div>
    </div>
</div>

<script src="../../assets/js/sede/editar.js"></script>
</body>

</html>