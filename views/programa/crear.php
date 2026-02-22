<?php
$pageTitle = 'Registrar Programa - SENA';
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
                <a href="../dashboard/index.php">Inicio</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="index.php">Programas</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Registrar</span>
            </nav>
            <h1 class="page-title">Registrar Nuevo Programa</h1>
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
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h2>Información del Programa</h2>
                    <p>Defina los parámetros básicos de la formación académica</p>
                </div>
            </div>

            <form id="crearProgramaForm" class="form-content">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="prog_codigo" class="form-label required">Código de Programa</label>
                        <input type="number" id="prog_codigo" name="prog_codigo" class="form-input" placeholder="Ej: 228106" required>
                        <div class="form-help">Ingrese el código SofíaPlus de 6-7 dígitos.</div>
                    </div>

                    <div class="form-group">
                        <label for="prog_tipo" class="form-label required">Tipo de Programa</label>
                        <select id="prog_tipo" name="prog_tipo" class="form-input" required>
                            <option value="" disabled selected>Seleccione tipo...</option>
                            <option value="Tecnólogo">Tecnólogo</option>
                            <option value="Técnico">Técnico</option>
                            <option value="Especialización">Especialización</option>
                            <option value="Curso Corto">Curso Corto</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="prog_denominacion" class="form-label required">Denominación del Programa</label>
                        <input type="text" id="prog_denominacion" name="prog_denominacion" class="form-input" placeholder="Ej: Análisis y Desarrollo de Software" required>
                        <div class="form-help">Nombre completo tal como aparece en el registro calificado.</div>
                    </div>

                    <div class="form-group full-width">
                        <label for="tit_programa_titpro_id" class="form-label required">Título Académico que Otorga</label>
                        <select id="tit_programa_titpro_id" name="tit_programa_titpro_id" class="form-input" required>
                            <option value="" disabled selected>Cargando títulos...</option>
                        </select>
                        <div class="form-help">El título debe estar previamente registrado en el sistema.</div>
                    </div>
                </div>

                <!-- Competencies Section -->
                <div class="form-section mt-6 border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Competencias del Programa</h3>
                    <p class="text-sm text-gray-500 mb-4">Seleccione las competencias que se desarrollarán en este programa de formación.</p>
                    
                    <div id="loadingCompetencias" class="text-center py-4 text-gray-500">
                        <i class="fa-solid fa-rotate animate-spin mr-2"></i>
                        Cargando competencias...
                    </div>

                    <div id="competenciasContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto p-2 border rounded-md hidden">
                        <!-- Competencies checkboxes will be loaded here -->
                    </div>
                    <div id="competenciasError" class="text-red-500 text-sm mt-2 hidden"></div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Guardar Programa
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
                <p>Al registrar un programa, podrá posteriormente asociarle competencias y resultados de aprendizaje específicos.</p>
                <ul class="mt-4">
                    <li>Verifique que el código no exista</li>
                    <li>Asegúrese de seleccionar el título correcto</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script src="../../assets/js/programa/crear.js?v=<?php echo time(); ?>"></script>
</body>

</html>