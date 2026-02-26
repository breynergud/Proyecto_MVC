<?php
$pageTitle = 'Editar Ficha - SENA';
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
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Modificar Ficha</h1>
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
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <div>
                    <h2>Actualizar Información</h2>
                    <p>Modifique los datos de la ficha</p>
                </div>
            </div>

            <form id="editarFichaForm" class="form-content">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="fich_id_display" class="form-label">Número de Ficha</label>
                        <input type="text" id="fich_id_display" class="form-input" value="<?php echo htmlspecialchars($id); ?>" readonly disabled>
                        <input type="hidden" name="fich_id" value="<?php echo htmlspecialchars($id); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="programa_prog_id" class="form-label required">Programa de Formación</label>
                        <select id="programa_prog_id" name="programa_prog_id" class="form-input" required>
                            <option value="" disabled>Seleccione un programa...</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="instructor_inst_id" class="form-label required">Instructor Líder</label>
                        <select id="instructor_inst_id" name="instructor_inst_id" class="form-input" required>
                            <option value="" disabled>Seleccione un instructor...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fich_jornada" class="form-label required">Jornada</label>
                        <select id="fich_jornada" name="fich_jornada" class="form-input" required>
                            <option value="" disabled>Seleccione jornada...</option>
                            <option value="Diurna">Diurna</option>
                            <option value="Nocturna">Nocturna</option>
                            <option value="Mixta">Mixta</option>
                            <option value="Fin de Semana">Fin de Semana</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="coordinacion_coord_id" class="form-label required">Coordinación</label>
                        <select id="coordinacion_coord_id" name="coordinacion_coord_id" class="form-input" required>
                            <option value="" disabled>Seleccione coordinación...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fich_fecha_ini_lectiva" class="form-label required">Inicio Etapa Lectiva</label>
                        <input type="date" id="fich_fecha_ini_lectiva" name="fich_fecha_ini_lectiva" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="fich_fecha_fin_lectiva" class="form-label required">Fin Etapa Lectiva</label>
                        <input type="date" id="fich_fecha_fin_lectiva" name="fich_fecha_fin_lectiva" class="form-input" required>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Actualizar Ficha
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="../../assets/js/ficha/editar.js?v=<?php echo time(); ?>"></script>
</body>
</html>
