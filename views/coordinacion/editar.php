<?php
$pageTitle = 'Editar Coordinación - SENA';
$activeNavItem = 'coordinaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../dashboard/index.php">Inicio</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="index.php">Coordinaciones</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Editar Coordinación</h1>
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
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h2 class="form-section-title">Actualizar Información</h2>
                        <p class="form-section-subtitle">Modifique los campos necesarios para la coordinación</p>
                    </div>
                </div>

                <form id="editarCoordinacionForm" class="modern-form">
                    <input type="hidden" id="coord_id" name="coord_id" value="<?php echo htmlspecialchars($id); ?>">
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="coord_descripcion" class="form-label">Descripción de la Coordinación *</label>
                            <input type="text" id="coord_descripcion" name="coord_descripcion" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="centro_formacion_cent_id" class="form-label">Centro de Formación *</label>
                            <select id="centro_formacion_cent_id" name="centro_formacion_cent_id" class="form-select" required>
                                <option value="">Cargando centros...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="coord_nombre_coordinador" class="form-label">Nombre del Coordinador *</label>
                            <input type="text" id="coord_nombre_coordinador" name="coord_nombre_coordinador" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="coord_correo" class="form-label">Correo Electrónico</label>
                            <input type="email" id="coord_correo" name="coord_correo" class="form-input">
                        </div>

                        <div class="form-group text-sm text-gray-500 italic mt-2">
                             La contraseña se mantendrá igual si se deja en blanco (implementación base).
                        </div>
                        <div class="form-group">
                            <label for="coord_password" class="form-label">Nueva Contraseña (opcional)</label>
                            <input type="password" id="coord_password" name="coord_password" class="form-input" placeholder="Odejar en blanco">
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn-secondary">Cancelar</a>
                        <button type="submit" class="btn-primary" id="saveBtn">
                            <i class="fa-solid fa-save"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="../../assets/js/coordinacion/editar.js?v=<?php echo time(); ?>"></script>
</body>
</html>
