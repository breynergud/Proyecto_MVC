<?php
$pageTitle = 'Registrar Título - SENA';
$activeNavItem = 'titulos';
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
                <a href="index.php">Títulos</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Crear</span>
            </nav>
            <h1 class="page-title">Registrar Nuevo Título</h1>
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
                    <i class="fa-solid fa-certificate"></i>
                </div>
                <div>
                    <h2>Información del Título</h2>
                    <p>Defina el nombre oficial del título académico</p>
                </div>
            </div>

            <form id="crearTituloForm" class="form-content">
                <div class="form-group">
                    <label for="titpro_nombre" class="form-label required">
                        Nombre del Título
                    </label>
                    <input
                        type="text"
                        id="titpro_nombre"
                        name="titpro_nombre"
                        class="form-input"
                        placeholder="Ej: Tecnólogo en Análisis y Desarrollo de Software"
                        required>
                    <div class="form-help">
                        Ingrese la denominación completa tal como aparece en el registro calificado.
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Guardar Título
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
                    <li>Use mayúsculas iniciales para nombres propios</li>
                    <li>Evite abreviaturas innecesarias</li>
                    <li>Este título podrá ser asociado a múltiples programas</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script src="../../assets/js/titulo_programa/crear.js?v=<?php echo time(); ?>"></script>
</body>

</html>