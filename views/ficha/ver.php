<?php
$pageTitle = 'Ver Ficha - SENA';
$activeNavItem = 'fichas';
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
                <a href="index.php">Fichas</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Detalle</span>
            </nav>
            <h1 class="page-title">Detalles de la Ficha</h1>
        </div>

        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </a>
            <a href="editar.php?id=<?php echo $_GET['id']; ?>" class="btn-primary">
                <i class="fa-solid fa-pen-to-square"></i>
                Editar Ficha
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Info Card -->
        <div class="detail-card">
            <div class="detail-header">
                <div class="detail-icon">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <div>
                    <h2 id="fichaNumero">Ficha #</h2>
                    <p>Información completa de la ficha</p>
                </div>
            </div>

            <div class="detail-content">
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Código de Ficha</label>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-barcode text-sena-green"></i>
                            <span id="fichaCodigo" class="detail-value font-bold">Cargando...</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Instructor Líder</label>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-user text-sena-green"></i>
                            <span id="instructorNombre" class="detail-value">Cargando...</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Programa</label>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-graduation-cap text-sena-green"></i>
                            <span id="programaNombre" class="detail-value">Cargando...</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Jornada</label>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-clock text-sena-green"></i>
                            <span id="jornada" class="detail-value">Cargando...</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <label>Coordinación</label>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-building text-sena-green"></i>
                            <span id="coordinacion" class="detail-value">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.detail-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.detail-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-bottom: 2px solid #39A900;
}

.detail-icon {
    width: 56px;
    height: 56px;
    background: #39A900;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
}

.detail-header h2 {
    font-size: 24px;
    color: #1f2937;
    margin-bottom: 4px;
}

.detail-header p {
    color: #6b7280;
    font-size: 14px;
}

.detail-content {
    padding: 24px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
}

.detail-item label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.flex.items-center.gap-2 {
    display: flex;
    align-items: center;
    gap: 8px; /* Adjusted gap for consistency */
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 3px solid #39A900;
}

.flex.items-center.gap-2 i {
    font-size: 20px; /* Adjusted font size for consistency */
    color: #39A900;
    flex-shrink: 0;
}

.flex.items-center.gap-2 span {
    font-size: 15px;
    color: #1f2937;
    font-weight: 500;
}
</style>

<script src="../../assets/js/ficha/ver.js?v=<?php echo time(); ?>"></script>
</body>
</html>
