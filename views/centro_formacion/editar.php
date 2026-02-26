<?php
$pageTitle = 'Editar Centro de Formación - SENA';
$activeNavItem = 'centros';
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
                <a href="index.php">Centros de Formación</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Editar Centro</h1>
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
                        <p class="form-section-subtitle">Modifique el nombre del centro de formación</p>
                    </div>
                </div>

                <form id="editarCentroForm" class="modern-form">
                    <input type="hidden" id="cent_id" name="cent_id" value="<?php echo htmlspecialchars($id); ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="cent_nombre" class="form-label">Nombre del Centro *</label>
                            <input type="text" id="cent_nombre" name="cent_nombre" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="cent_correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" id="cent_correo" name="cent_correo" class="form-input" required>
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

<script src="../../assets/js/centro_formacion/editar.js?v=<?php echo time(); ?>"></script>
</body>
</html>
