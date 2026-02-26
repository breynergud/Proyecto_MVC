<?php
$pageTitle = 'Registrar Coordinación - SENA';
$activeNavItem = 'coordinaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../dashboard/index.php">Inicio</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="index.php">Coordinaciones</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Registrar</span>
            </nav>
            <h1 class="page-title">Registrar Nueva Coordinación</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="form-container">
            <div class="form-card">
                <div class="form-header">
                    <div class="form-icon">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div>
                        <h2 class="form-section-title">Información de la Coordinación</h2>
                        <p class="form-section-subtitle">Complete los datos para registrar la coordinación</p>
                    </div>
                </div>

                <form id="crearCoordinacionForm" class="modern-form">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="coord_descripcion" class="form-label">Descripción de la Coordinación *</label>
                            <input type="text" id="coord_descripcion" name="coord_descripcion" class="form-input" placeholder="Ej: Coordinación de Teleinformática" required>
                        </div>

                        <div class="form-group">
                            <label for="centro_formacion_cent_id" class="form-label">Centro de Formación *</label>
                            <select id="centro_formacion_cent_id" name="centro_formacion_cent_id" class="form-select" required>
                                <option value="">Seleccionando centros...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="coord_nombre_coordinador" class="form-label">Nombre del Coordinador *</label>
                            <input type="text" id="coord_nombre_coordinador" name="coord_nombre_coordinador" class="form-input" placeholder="Nombre completo" required>
                        </div>

                        <div class="form-group">
                            <label for="coord_correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" id="coord_correo" name="coord_correo" class="form-input" placeholder="usuario@sena.edu.co" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="btn-secondary">Limpiar</button>
                        <button type="submit" class="btn-primary" id="saveBtn">
                            <i class="fa-solid fa-save"></i>
                            Guardar Coordinación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="../../assets/js/coordinacion/crear.js?v=<?php echo time(); ?>"></script>
</body>
</html>
