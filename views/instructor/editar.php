<?php
$pageTitle = 'Editar Instructor - SENA';
$activeNavItem = 'instructores';
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
                <a href="index.php">Instructores</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Editar Instructor</h1>
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
            <p>Cargando información del instructor...</p>
        </div>

        <!-- Form Card -->
        <div id="formCard" class="form-card" style="display: none;">
            <div class="form-header">
                <div class="form-icon">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h2>Modificar Información</h2>
                    <p>Actualice los datos del instructor seleccionado</p>
                </div>
            </div>

            <form id="instructorEditForm" class="form-content">
                <input type="hidden" id="inst_id" name="inst_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="inst_nombre" class="form-label required">
                            Nombres
                        </label>
                        <input
                            type="text"
                            id="inst_nombre"
                            name="inst_nombre"
                            class="form-input"
                            required>
                        <div class="form-error" id="inst_nombre_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="inst_apellidos" class="form-label required">
                            Apellidos
                        </label>
                        <input
                            type="text"
                            id="inst_apellidos"
                            name="inst_apellidos"
                            class="form-input"
                            required>
                        <div class="form-error" id="inst_apellidos_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="inst_correo" class="form-label required">
                            Correo Electrónico
                        </label>
                        <input
                            type="email"
                            id="inst_correo"
                            name="inst_correo"
                            class="form-input"
                            required>
                        <div class="form-error" id="inst_correo_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="inst_telefono" class="form-label required">
                            Teléfono
                        </label>
                        <input
                            type="tel"
                            id="inst_telefono"
                            name="inst_telefono"
                            class="form-input"
                            required>
                        <div class="form-error" id="inst_telefono_error"></div>
                    </div>
                </div>

                <!-- Sección Especialidades (Opcional) -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <div class="form-header mb-4">
                        <div class="form-icon" style="background: var(--sena-orange); color: white;">
                            <i class="fa-solid fa-medal"></i>
                        </div>
                        <div>
                            <h2>Especialidades (Competencias)</h2>
                            <p>Modifique el programa y asigne las competencias que este instructor está avalado para dictar.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="form-group">
                            <label for="intruProgSelect" class="form-label">
                                Programa de Formación
                            </label>
                            <select id="intruProgSelect" class="form-input" onchange="instructorView.loadCompetenciasForPrograma()">
                                <option value="">Seleccione un programa...</option>
                            </select>
                        </div>

                        <div id="compContainer" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                            <h3 style="font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.75rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">
                                Seleccionar Competencias
                            </h3>
                            <div id="compList" class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto">
                                <!-- Checkboxes generados dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions mt-8">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Actualizar Instructor
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
                <p id="errorMessage">No se pudo cargar la información del instructor.</p>
                <a href="index.php" class="btn-primary mt-4">Volver a Instructores</a>
            </div>
        </div>

    </div>
</main>

<!-- Success Modal -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <div class="modal-header success">
            <i class="fa-solid fa-circle-check"></i>
            <h3>Instructor Actualizado</h3>
        </div>
        <div class="modal-body">
            <p>El instructor <strong id="updatedInstructorName"></strong> ha sido actualizado correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Ver Instructores</a>
            <button class="btn-secondary" onclick="closeSuccessModal()">Continuar Editando</button>
        </div>
    </div>
</div>

<script src="../../assets/js/instructor/editar.js?v=<?php echo time(); ?>"></script>
</body>

</html>
