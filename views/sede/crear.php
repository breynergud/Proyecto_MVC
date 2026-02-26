<?php
$pageTitle = 'Crear Sede - SENA';
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
                <span>Crear</span>
            </nav>
            <h1 class="page-title">Registrar Nueva Sede</h1>
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
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <h2>Información de la Sede</h2>
                    <p>Complete los datos para registrar una nueva sede</p>
                </div>
            </div>

            <form id="sedeForm" class="form-content">
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
                        Ingrese el nombre completo de la sede. Debe ser único en el sistema.
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Guardar Sede
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
                    <li>El nombre de la sede debe ser único en el sistema</li>
                    <li>Una vez creada, la sede estará disponible para asignar programas</li>
                    <li>Puede editar la información posteriormente si es necesario</li>
                    <li>Las sedes inactivas no aparecerán en las listas de selección</li>
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
            <h3>Sede Creada Exitosamente</h3>
        </div>
        <div class="modal-body">
            <p>La sede <strong id="createdSedeName"></strong> ha sido registrada correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Ver Sedes</a>
            <button class="btn-secondary" onclick="closeSuccessModal()">Crear Otra</button>
        </div>
    </div>
</div>

<script src="../../assets/js/sede/crear.js"></script>
</body>

</html>