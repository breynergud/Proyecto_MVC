<?php
$pageTitle = 'Detalles del Programa - SENA';
$activeNavItem = 'programas';
require_once '../layouts/head.php';
require_once '../layouts/sidebar-green.php';

$id = $_GET['id'] ?? null;
?>

<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-sm sticky top-0 z-20 border-b border-slate-200 dark:border-slate-700 px-8 py-4 flex justify-between items-center">
        <div>
            <nav aria-label="Breadcrumb" class="flex text-sm text-slate-500 dark:text-slate-400 mb-1">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="index.php" class="hover:text-sena-green dark:hover:text-sena-green transition-colors">Programas</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right text-base mx-1"></i>
                            <span class="text-slate-800 dark:text-white font-medium">Detalle del Programa</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-graduation-cap text-2xl text-sena-green"></i>
                Información del Programa
            </h1>
        </div>

        <div class="flex items-center gap-4">
            <a href="index.php" class="flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-sena-green transition-colors px-3 py-2 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/10 border border-transparent hover:border-sena-green/20">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="text-sm font-medium">Regresar</span>
            </a>
        </div>
    </header>

    <div class="p-8 max-w-7xl mx-auto space-y-6">
        <!-- Loading State -->
        <div id="loadingState" class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-12 text-center">
            <div class="w-8 h-8 border-3 border-sena-green border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-slate-600 dark:text-slate-400">Cargando información del programa...</p>
        </div>

        <!-- Detail Grid -->
        <div id="programaDetails" class="space-y-6" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info Card -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-20 h-20 bg-sena-green/10 dark:bg-sena-green/20 rounded-full flex items-center justify-center mb-4 shadow-sm">
                                <i class="fa-solid fa-graduation-cap text-4xl text-sena-green"></i>
                            </div>        </div>
                                    <div>
                                        <h3 class="text-slate-900 dark:text-white font-bold">Programa Académico</h3>
                                        <p class="text-slate-500 text-xs">Información General</p>
                                    </div>
                                </div>
                                <a href="#" id="editBtn" class="text-sena-green hover:text-emerald-700 transition-colors flex items-center gap-1 text-sm font-medium">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    Editar
                                </a>
                            </div>

                            <div class="space-y-4">
                                <div class="py-2 border-b border-slate-100 dark:border-slate-700">
                                    <p class="text-slate-500 dark:text-slate-400 text-xs uppercase mb-1 font-semibold tracking-wider">Denominación</p>
                                    <p id="viewProgDenominacion" class="text-slate-900 dark:text-white text-base font-bold leading-tight"></p>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-700">
                                    <span class="text-slate-500 dark:text-slate-400 text-sm">Código de Programa</span>
                                    <span id="viewProgCodigoVal" class="font-mono text-slate-700 dark:text-slate-300 text-sm font-bold bg-slate-50 dark:bg-slate-800 px-2 py-0.5 rounded">-</span>
                                </div>
                                <div class="py-2 border-b border-slate-100 dark:border-slate-700">
                                    <p class="text-slate-500 dark:text-slate-400 text-xs uppercase mb-1 font-semibold tracking-wider">Título Otorgado</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <i class="fa-solid fa-certificate text-sena-green"></i>
                                        <p id="dispTitulo" class="text-slate-900 dark:text-white text-sm font-medium">-</p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-slate-500 dark:text-slate-400 text-sm">Tipo de Programa</span>
                                    <span id="viewProgTipo" class="bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Competencias Card Placeholder -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30">
                                <i class="fa-solid fa-book text-slate-400"></i>
                            </div>    Competencias vinculadas
                            </h3>
                        </div>
                        <div class="p-6">
                            <div id="competenciasList" class="space-y-4">
                                <!-- Competencias will be loaded here -->
                            </div>
                            <div id="noCompetencias" class="text-center py-12">
                                <i class="fa-solid fa-book text-slate-200 text-5xl mb-3"></i>
                                <p class="text-slate-500">No hay competencias asociadas a este programa aún.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone / Delete Button -->
            <div class="flex justify-end pt-4">
                <button id="deleteBtn" class="flex items-center gap-2 bg-white dark:bg-slate-800 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 px-4 py-3 rounded-xl shadow-md border border-red-100 dark:border-red-900/30 transition-all duration-300 hover:translate-y-[-2px] active:translate-y-[0px] group">
                <i class="fa-solid fa-trash text-xl group-hover:shake"></i>
                <span class="font-bold text-sm">Eliminar Programa</span>
            </button>
            </div>
        </div>

        <!-- Error Card -->
        <div id="errorCard" class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-red-200 dark:border-red-700 p-12 text-center" style="display: none;">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-circle-exclamation text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Programa No Encontrado</h3>
            <p id="errorMessage" class="text-slate-600 dark:text-slate-400 mb-6">No se pudo cargar la información del programa seleccionado.</p>
            <a href="index.php" class="inline-flex items-center gap-2 bg-sena-green hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                <i class="fa-solid fa-arrow-left"></i>
                Volver al Listado
            </a>
        </div>
    </div>
</main>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="modalOverlay"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-surface-light dark:bg-surface-dark w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden relative z-10 scale-95 opacity-0 transition-all duration-300" id="modalContent">
            <div class="p-6 text-center">
                        <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4 mx-auto">
                            <i class="fa-solid fa-circle-exclamation text-3xl text-red-600 dark:text-red-400"></i>
                        </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">¿Eliminar Programa?</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm mb-6">
                    Estás a punto de eliminar el programa <strong id="programaToDeleteName" class="text-slate-900 dark:text-white"></strong>. Esta acción eliminará permanentemente el registro de la base de datos.
                </p>
                <div class="flex gap-3">
                    <button id="cancelDeleteBtn" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Cancelar
                    </button>
                    <button id="confirmDeleteBtn" class="flex-1 px-4 py-3 rounded-xl bg-red-600 text-white font-bold text-sm hover:bg-red-700 shadow-md transition-colors">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/js/programa/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>