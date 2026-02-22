<?php
$pageTitle = 'Gestión de Sedes - SENA';
$activeNavItem = 'sedes';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';
?>

<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Sedes</span>
            </nav>
            <h1 class="page-title">Administración de Sedes</h1>
        </div>

        <!-- Icons removed as per user request -->
        <div class="header-actions">
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Action Bar - Solo búsqueda -->
        <!-- Action Bar - Búsqueda y Registro -->
        <div class="action-bar-simple" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="search-container">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" placeholder="Buscar por nombre de sede..." class="search-input">
            </div>
            <a href="crear.php" class="btn-primary">
                <i class="fa-solid fa-plus"></i>
                Registrar Sede
            </a>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-20">ID</th>
                        <th>Nombre Sede</th>
                    </tr>
                </thead>
                <tbody id="sedesTableBody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-container">
                <div class="pagination-info">
                    <p>Mostrando <span id="showingFrom">1</span> a <span id="showingTo">5</span> de <span id="totalRecords">0</span> resultados</p>
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
            
            <!-- Botón Registrar debajo de la tabla -->
            <!-- Botón Registrar movido al inicio -->
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
            <p>¿Está seguro que desea eliminar la sede <strong id="sedeToDelete"></strong>?</p>
            <p class="text-sm text-gray-600">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
            <button class="btn-danger" onclick="confirmDelete()">Eliminar</button>
        </div>
    </div>
</div>

<script src="../../assets/js/sede/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>