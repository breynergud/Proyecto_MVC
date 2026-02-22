<?php
$pageTitle = 'Gestión de Programas - SENA';
$activeNavItem = 'programas';
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
                <span>Programas</span>
            </nav>
            <h1 class="page-title">Administración de Programas</h1>
        </div>
        <div class="header-actions">
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Action Bar - Solo búsqueda -->
        <!-- Action Bar - Búsqueda y Registro -->
        <div class="action-bar-simple" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="search-container">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" placeholder="Buscar por código o denominación..." class="search-input">
            </div>
            <a href="crear.php" class="btn-primary">
                <i class="fa-solid fa-plus"></i>
                Registrar Programa
            </a>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10 text-sena-green font-bold">ID</th>
                        <th class="w-15">Código</th>
                        <th>Denominación</th>
                        <th>Título</th>
                        <th class="w-15">Tipo</th>
                        <th class="w-15 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="programasTableBody">
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
            <p>¿Está seguro que desea eliminar el programa <strong id="programaToDelete"></strong>?</p>
            <p class="text-sm text-gray-600">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
            <button class="btn-danger" id="confirmDeleteBtn" onclick="confirmDelete()">Eliminar</button>
        </div>
    </div>
</div>

<!-- Competencias Modal -->
<div id="competenciasModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3>Gestionar Competencias</h3>
            <button class="modal-close" onclick="closeCompetenciasModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom: 16px;">
                <strong id="programaNombre" style="color: #39A900; font-size: 16px;"></strong>
            </div>

            <!-- Buscador -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Buscar Competencias</label>
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="searchCompetenciaModal" class="search-input" placeholder="Buscar por nombre...">
                </div>
            </div>

            <!-- Tabs -->
            <div style="display: flex; gap: 8px; margin-bottom: 16px; border-bottom: 2px solid #e5e7eb;">
                <button id="tabAsociadas" class="tab-btn active" onclick="programaView.switchTab('asociadas')">
                    Asociadas (<span id="countAsociadas">0</span>)
                </button>
                <button id="tabDisponibles" class="tab-btn" onclick="programaView.switchTab('disponibles')">
                    Disponibles (<span id="countDisponibles">0</span>)
                </button>
            </div>

            <!-- Competencias Asociadas -->
            <div id="competenciasAsociadasTab" class="tab-content active">
                <div id="competenciasAsociadasList" style="max-height: 400px; overflow-y: auto;">
                    <p class="text-center text-gray-500">Cargando...</p>
                </div>
            </div>

            <!-- Competencias Disponibles -->
            <div id="competenciasDisponiblesTab" class="tab-content" style="display: none;">
                <div id="competenciasDisponiblesList" style="max-height: 400px; overflow-y: auto;">
                    <p class="text-center text-gray-500">Cargando...</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeCompetenciasModal()">Cerrar</button>
        </div>
    </div>
</div>

<style>
.tab-btn {
    padding: 10px 20px;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    font-weight: 500;
    color: #6b7280;
    transition: all 0.2s;
}

.tab-btn.active {
    color: #39A900;
    border-bottom-color: #39A900;
}

.tab-btn:hover {
    color: #39A900;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.competencia-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 8px;
    background: white;
    transition: all 0.2s;
}

.competencia-card:hover {
    border-color: #39A900;
    background: #f9fafb;
}

.competencia-info {
    flex: 1;
}

.competencia-nombre {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.competencia-detalles {
    font-size: 12px;
    color: #6b7280;
}

.competencia-horas {
    display: inline-block;
    padding: 2px 8px;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 4px;
    font-weight: 500;
    margin-right: 8px;
}

.btn-icon-action {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-icon-action i {
    font-size: 16px;
}

.btn-add-comp {
    background: #39A900;
    color: white;
}

.btn-add-comp:hover {
    background: #2d8000;
}

.btn-remove-comp {
    background: #ef4444;
    color: white;
}

.btn-remove-comp:hover {
    background: #dc2626;
}
</style>

<script src="../../assets/js/programa/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>