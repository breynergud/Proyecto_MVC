<?php
$pageTitle = 'Eliminar Sede - SENA';
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
                <span>Eliminar</span>
            </nav>
            <h1 class="page-title">Eliminar Sede</h1>
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

        <!-- Delete Confirmation -->
        <div id="deleteConfirmation" class="delete-container" style="display: none;">
            <!-- Warning Card -->
            <div class="warning-card">
                <div class="warning-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="warning-content">
                    <h2>¡Atención! Eliminación de Sede</h2>
                    <p>Está a punto de eliminar permanentemente la siguiente sede del sistema:</p>
                </div>
            </div>

            <!-- Sede Info Card -->
            <div class="sede-info-card">
                <div class="sede-info-header">
                    <div class="sede-info-icon">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                        <h3 id="sedeNombre">Cargando...</h3>
                        <p>ID: <span id="sedeId">-</span></p>
                    </div>
                </div>
            </div>

            <!-- Impact Analysis -->
            <div class="impact-card">
                <div class="impact-header">
                    <i class="fa-solid fa-chart-line"></i>
                    <h3>Análisis de Impacto</h3>
                </div>
                <div class="impact-content">
                    <div class="impact-item">
                        <div class="impact-icon">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div class="impact-details">
                            <span class="impact-count" id="programasCount">0</span>
                            <span class="impact-label">Programas asociados</span>
                        </div>
                    </div>

                    <div class="impact-item">
                        <div class="impact-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="impact-details">
                            <span class="impact-count" id="instructoresCount">0</span>
                            <span class="impact-label">Instructores asignados</span>
                        </div>
                    </div>

                    <div class="impact-item">
                        <div class="impact-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="impact-details">
                            <span class="impact-count" id="fichasCount">0</span>
                            <span class="impact-label">Fichas en formación</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consequences Warning -->
            <div id="consequencesWarning" class="consequences-card" style="display: none;">
                <div class="consequences-header">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <h3>Consecuencias de la Eliminación</h3>
                </div>
                <div class="consequences-content">
                    <ul id="consequencesList">
                        <!-- Dynamic consequences will be added here -->
                    </ul>
                </div>
            </div>

            <!-- Confirmation Form -->
            <div class="confirmation-card">
                <div class="confirmation-header">
                    <i class="fa-solid fa-shield-halved"></i>
                    <h3>Confirmación Requerida</h3>
                </div>

                <form id="deleteForm" class="confirmation-form">
                    <input type="hidden" id="sede_id_delete" name="sede_id">

                    <div class="form-group">
                        <label for="confirmText" class="form-label required">
                            Para confirmar, escriba el nombre de la sede:
                        </label>
                        <input
                            type="text"
                            id="confirmText"
                            name="confirmText"
                            class="form-input"
                            placeholder="Escriba el nombre exacto de la sede"
                            required>
                        <div class="form-error" id="confirmText_error"></div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="confirmUnderstand" name="confirmUnderstand" required>
                        <label for="confirmUnderstand">
                            Entiendo que esta acción es irreversible y eliminará permanentemente la sede y todas sus relaciones.
                        </label>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn-secondary">
                            <i class="fa-solid fa-circle-xmark"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn-danger" id="deleteButton" disabled>
                            <i class="fa-solid fa-trash"></i>
                            Eliminar Permanentemente
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cannot Delete Card -->
        <div id="cannotDeleteCard" class="error-card" style="display: none;">
            <div class="error-icon">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div>
                <h3>No se puede Eliminar</h3>
                <p id="cannotDeleteMessage">Esta sede no puede ser eliminada porque tiene elementos asociados.</p>
                <div id="blockingElements" class="blocking-elements">
                    <!-- Blocking elements will be listed here -->
                </div>
                <a href="index.php" class="btn-primary mt-4">Volver a Sedes</a>
            </div>
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
    </div>
</main>

<!-- Success Modal -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <div class="modal-header success">
            <i class="fa-solid fa-circle-check"></i>
            <h3>Sede Eliminada</h3>
        </div>
        <div class="modal-body">
            <p>La sede <strong id="deletedSedeName"></strong> ha sido eliminada permanentemente del sistema.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Volver a Sedes</a>
        </div>
    </div>
</div>

<script src="../../assets/js/sede/eliminar.js"></script>
</body>

</html>