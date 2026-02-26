<?php
$pageTitle = 'Registrar Centro de Formación - SENA';
$activeNavItem = 'centros';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../dashboard/index.php">Inicio</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="index.php">Centros de Formación</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Registrar</span>
            </nav>
            <h1 class="page-title">Registrar Nuevo Centro</h1>
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
                        <i class="fa-solid fa-landmark"></i>
                    </div>
                    <div>
                        <h2 class="form-section-title">Información del Centro</h2>
                        <p class="form-section-subtitle">Ingrese el nombre oficial del centro de formación</p>
                    </div>
                </div>

                <form id="crearCentroForm" class="modern-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="cent_nombre" class="form-label">Nombre del Centro *</label>
                            <input type="text" id="cent_nombre" name="cent_nombre" class="form-input" placeholder="Ej: Centro de Gestión de Mercados..." required>
                        </div>
                        <div class="form-group">
                            <label for="cent_correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" id="cent_correo" name="cent_correo" class="form-input" placeholder="Ej: centro@sena.edu.co" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="btn-secondary">Limpiar</button>
                        <button type="submit" class="btn-primary" id="saveBtn">
                            <i class="fa-solid fa-save"></i>
                            Guardar Centro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="../../assets/js/centro_formacion/crear.js?v=<?php echo time(); ?>"></script>
</body>
</html>
