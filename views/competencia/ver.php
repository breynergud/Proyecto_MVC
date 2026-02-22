<?php
$pageTitle = 'Ver Competencia - SENA';
$activeNavItem = 'competencias';
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
                <a href="index.php">Competencias</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Detalle</span>
            </nav>
            <h1 class="page-title">Detalle de Competencia</h1>
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
            <p>Cargando información de la competencia...</p>
        </div>

        <!-- Detail Card -->
        <div id="detailCard" class="form-card" style="display: none;">
            <div class="form-header">
                <div class="form-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h2 id="competenciaNombre">Cargando...</h2>
                    <p>Información detallada de la competencia</p>
                </div>
            </div>

            <div class="form-content">
                <div class="detail-grid">
                    <div class="detail-item">
                        <label class="detail-label">ID</label>
                        <p class="detail-value" id="detailId">-</p>
                    </div>

                    <div class="detail-item">
                        <label class="detail-label">Nombre Corto</label>
                        <p class="detail-value" id="detailNombreCorto">-</p>
                    </div>

                    <div class="detail-item">
                        <label class="detail-label">Horas</label>
                        <p class="detail-value" id="detailHoras">-</p>
                    </div>

                    <div class="detail-item full-width">
                        <label class="detail-label">Norma/Unidad de Competencia</label>
                        <p class="detail-value" id="detailNorma">-</p>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i>
                        Volver al Listado
                    </a>
                    <a href="#" id="editBtn" class="btn-primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Editar Competencia
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Card -->
        <div id="errorCard" class="error-card" style="display: none;">
            <div class="error-icon">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div>
                <h3>Error al Cargar</h3>
                <p id="errorMessage">No se pudo cargar la información de la competencia.</p>
                <a href="index.php" class="btn-primary mt-4">Volver a Competencias</a>
            </div>
        </div>
    </div>
</main>

<script src="../../assets/js/competencia/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>
