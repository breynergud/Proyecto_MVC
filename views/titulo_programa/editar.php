<?php
$pageTitle = 'Editar Título - SENA';
$activeNavItem = 'titulos';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';

$id = $_GET['id'] ?? null;
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
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Modificar Título</h1>
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
                    <h2>Editar Información</h2>
                    <p>Actualice los datos del título académico seleccionado</p>
                </div>
            </div>

            <form id="editarTituloForm" class="form-content">
                <input type="hidden" name="titpro_id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="form-group">
                    <label for="titpro_nombre" class="form-label required">
                        Nombre del Título
                    </label>
                    <input
                        type="text"
                        id="titpro_nombre"
                        name="titpro_nombre"
                        class="form-input"
                        required>
                    <div class="form-help">
                        Asegúrese de que el nombre sea correcto antes de guardar los cambios.
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Actualizar Título
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="../../assets/js/titulo_programa/editar.js?v=<?php echo time(); ?>"></script>
</body>

</html>