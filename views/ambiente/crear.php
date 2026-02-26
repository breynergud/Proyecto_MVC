<?php
$pageTitle = 'Registrar Ambiente - SENA';
$activeNavItem = 'ambientes';
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
                <a href="index.php">Ambientes</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Registrar</span>
            </nav>
            <h1 class="page-title">Registrar Nuevo Ambiente</h1>
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
                    <i class="fa-solid fa-cube"></i>
                </div>
                <div>
                    <h2>Información del Ambiente</h2>
                    <p>Complete los datos para registrar un nuevo ambiente de aprendizaje</p>
                </div>
            </div>

            <form id="ambienteForm" class="form-content">
                <div class="form-group">
                    <label for="amb_id" class="form-label required">ID del Ambiente (Máx 5 caracteres)</label>
                    <input type="text" id="amb_id" name="amb_id" class="form-input" placeholder="Ej: S101" maxlength="5" required>
                    <div class="form-error" id="amb_id_error"></div>
                </div>

                <div class="form-group">
                    <label for="amb_nombre" class="form-label required">Nombre del Ambiente</label>
                    <input type="text" id="amb_nombre" name="amb_nombre" class="form-input" placeholder="Ej: Ambiente 101 - Sistemas" required>
                    <div class="form-error" id="amb_nombre_error"></div>
                </div>

                <div class="form-group">
                    <label for="tipo_ambiente" class="form-label required">Tipo de Ambiente</label>
                    <select id="tipo_ambiente" name="tipo_ambiente" class="form-input" required>
                        <option value="Convencional" selected>Convencional</option>
                        <option value="Especializado">Especializado</option>
                        <option value="Ext SENA-MEN">Ext SENA-MEN</option>
                        <option value="Ext Agropecuario">Ext Agropecuario</option>
                    </select>
                    <div class="form-error" id="tipo_ambiente_error"></div>
                </div>

                <div class="form-group">
                    <label for="sede_sede_id" class="form-label required">Sede</label>
                    <select id="sede_sede_id" name="sede_sede_id" class="form-input" required>
                        <option value="">Seleccione una sede...</option>
                        <!-- Sedes will be loaded here -->
                    </select>
                    <div class="form-error" id="sede_sede_id_error"></div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Guardar Ambiente
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
                    <li>El nombre del ambiente debe identificar claramente el espacio</li>
                    <li>Asegúrese de seleccionar la sede correcta para la ubicación física</li>
                    <li>Los ambientes se pueden deshabilitar si entran en mantenimiento</li>
                    <li>Puede configurar la capacidad y equipos en la sección de detalles</li>
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
            <h3>Ambiente Registrado</h3>
        </div>
        <div class="modal-body">
            <p>El ambiente ha sido registrado correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Ver Todos</a>
            <button class="btn-secondary" onclick="closeSuccessModal()">Registrar Otro</button>
        </div>
    </div>
</div>

<script src="../../assets/js/ambiente/crear.js?v=<?php echo time(); ?>"></script>
</body>

</html>