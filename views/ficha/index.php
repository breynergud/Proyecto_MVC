<?php
$pageTitle = 'Gestión de Fichas - SENA';
$activeNavItem = 'fichas';
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
                <span>Fichas</span>
            </nav>
            <h1 class="page-title">Administración de Fichas</h1>
        </div>
        <div class="header-actions">
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Action Bar -->
        <div class="action-bar-simple">
            <div class="search-container">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" placeholder="Buscar por número de ficha o programa..." class="search-input">
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10 text-sena-green font-bold">Ficha</th>
                        <th>Programa</th>
                        <th class="w-20">Instructor Líder</th>
                        <th class="w-15">Jornada</th>
                        <th class="w-20">Coordinación</th>
                        <th class="w-15 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="fichasTableBody">
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
            
            <!-- Botón Registrar -->
            <div class="table-footer-actions">
                <a href="crear.php" class="btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Registrar Ficha
                </a>
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
            <p>¿Está seguro que desea eliminar la ficha <strong id="fichaToDelete"></strong>?</p>
            <p class="text-sm text-gray-600">Esta acción no se puede deshacer y eliminará todas las asignaciones asociadas.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
            <button class="btn-danger" id="confirmDeleteBtn" onclick="confirmDelete()">Eliminar</button>
        </div>
    </div>
</div>

<script src="../../assets/js/ficha/index.js?v=<?php echo time(); ?>"></script>
</body>
</html>
