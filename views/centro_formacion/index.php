<?php
$pageTitle = 'Gestión de Centros de Formación - SENA';
$activeNavItem = 'centros';
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
                <span>Centros de Formación</span>
            </nav>
            <h1 class="page-title">Administración de Centros de Formación</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Action Bar -->
        <div class="action-bar-simple" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="search-container">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" placeholder="Buscar centro por nombre..." class="search-input">
            </div>
            <a href="crear.php" class="btn-primary">
                <i class="fa-solid fa-plus"></i>
                Registrar Centro
            </a>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10 text-sena-green font-bold">ID</th>
                        <th>Nombre del Centro de Formación</th>
                        <th class="w-15 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="centrosTableBody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-container">
                <div class="pagination-info">
                    <p>Total: <span id="totalCentros" class="font-bold text-sena-green">0</span> | Mostrando <span id="showingFrom">1</span> a <span id="showingTo">5</span> de <span id="totalRecords">0</span> resultados</p>
                </div>
                <nav class="pagination">
                    <button class="pagination-btn" id="prevBtn">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <div id="paginationNumbers"></div>
                    <button class="pagination-btn" id="nextBtn">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</main>

<!-- Delete Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar Eliminación</h3>
            <button class="modal-close" onclick="closeDeleteModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>¿Está seguro que desea eliminar el centro <strong id="centroToDelete"></strong>?</p>
            <p class="text-sm text-gray-600">Esta acción no se puede deshacer si el centro no tiene dependencias.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
            <button class="btn-danger" id="confirmDeleteBtn">Eliminar</button>
        </div>
    </div>
</div>

<script src="../../assets/js/centro_formacion/index.js?v=<?php echo time(); ?>"></script>
</body>
</html>
