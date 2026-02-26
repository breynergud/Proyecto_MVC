<?php
require_once dirname(__DIR__) . '/Conexion.php';

try {
    $db = Conexion::getConnect();
    
    // Iniciar transacción
    $db->beginTransaction();

    echo "Limpiando datos existentes...<br>";
    $db->exec("TRUNCATE TABLE instru_competencia CASCADE");
    $db->exec("TRUNCATE TABLE competxprograma CASCADE");
    $db->exec("TRUNCATE TABLE asignacion CASCADE");
    $db->exec("TRUNCATE TABLE ficha CASCADE");
    $db->exec("TRUNCATE TABLE instructor CASCADE");
    $db->exec("TRUNCATE TABLE coordinacion CASCADE");
    $db->exec("TRUNCATE TABLE programa CASCADE");
    $db->exec("TRUNCATE TABLE titulo_programa CASCADE");
    $db->exec("TRUNCATE TABLE competencia CASCADE");
    $db->exec("TRUNCATE TABLE ambiente CASCADE");
    $db->exec("TRUNCATE TABLE sede CASCADE");
    $db->exec("TRUNCATE TABLE centro_formacion CASCADE");

    echo "Insertando Centro de Formación...<br>";
    $db->exec("INSERT INTO centro_formacion (cent_id, cent_nombre) VALUES (1, 'Centro de Servicios y Gestión Empresarial')");

    echo "Insertando Sede...<br>";
    $db->exec("INSERT INTO sede (sede_id, sede_nombre, foto) VALUES (1, 'Sede Principal', 'sede1.jpg')");

    echo "Insertando Ambientes...<br>";
    $db->exec("INSERT INTO ambiente (amb_id, amb_nombre, tipo_ambiente, sede_sede_id) VALUES ('A201', 'Aula D201 Biblioteca', 'Convencional', 1)");
    $db->exec("INSERT INTO ambiente (amb_id, amb_nombre, tipo_ambiente, sede_sede_id) VALUES ('L101', 'Laboratorio Sistemas 1', 'Especializado', 1)");

    echo "Insertando Titulo Programa...<br>";
    $db->exec("INSERT INTO titulo_programa (titpro_id, titpro_nombre) VALUES (1, 'Tecnólogo')");

    echo "Insertando Programa de Software...<br>";
    $db->exec("INSERT INTO programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo) VALUES (228118, 'Análisis y Desarrollo de Software (ADSO)', 1, 'Titulada')");

    echo "Insertando Competencias...<br>";
    $db->exec("INSERT INTO competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) VALUES (1, 'Bases de Datos', 96, 'Construir bases de datos según requisitos')");
    $db->exec("INSERT INTO competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) VALUES (2, 'Matemáticas', 48, 'Razonar cuantitativamente frente a situaciones susceptibles de ser abordadas de manera matemática en contextos laborales, sociales y personales.')");
    $db->exec("INSERT INTO competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) VALUES (3, 'Inglés', 192, 'Interactuar en lengua inglesa de forma oral y escrita')");

    echo "Asociando Competencias al Programa ADSO (competxprograma)...<br>";
    // Base de datos - ADSO
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (228118, 1)");
    // Matemáticas - ADSO
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (228118, 2)");
    // Inglés - ADSO
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (228118, 3)");

    echo "Insertando Coordinación...<br>";
    $db->exec("INSERT INTO coordinacion (coord_id, coord_descripcion, centro_formacion_cent_id, coord_nombre_coordinador, coord_correo, coord_password) VALUES (1, 'Coordinación Telemática', 1, 'Pepito Coordinador', 'pepito@sena.edu.co', '123')");

    echo "Insertando Instructores...<br>";
    // Instructor Bases de Datos
    $db->exec("INSERT INTO instructor (inst_id, inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id, inst_password) VALUES (1, 'Juan', 'Pérez (BD)', 'juan@sena.edu.co', 3001234567, 1, '123')");
    // Instructor Matemáticas
    $db->exec("INSERT INTO instructor (inst_id, inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id, inst_password) VALUES (2, 'Eduardo', 'Alvarpaja (Mate)', 'eduardo@sena.edu.co', 3007654321, 1, '123')");
    // Instructor Inglés
    $db->exec("INSERT INTO instructor (inst_id, inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id, inst_password) VALUES (3, 'Maria', 'Gómez (Inglés)', 'maria@sena.edu.co', 3009998888, 1, '123')");

    echo "Asignando Especialidades a Instructores (instru_competencia)...<br>";
    // Juan da BD en ADSO
    $db->exec("INSERT INTO instru_competencia (instructor_inst_id, competxprograma_programa_prog_id, competxprograma_competencia_comp_id, inscomp_vigencia) VALUES (1, 228118, 1, '2026-12-31')");
    // Eduardo da Matemáticas en ADSO
    $db->exec("INSERT INTO instru_competencia (instructor_inst_id, competxprograma_programa_prog_id, competxprograma_competencia_comp_id, inscomp_vigencia) VALUES (2, 228118, 2, '2026-12-31')");
    // Maria da Inglés en ADSO
    $db->exec("INSERT INTO instru_competencia (instructor_inst_id, competxprograma_programa_prog_id, competxprograma_competencia_comp_id, inscomp_vigencia) VALUES (3, 228118, 3, '2026-12-31')");

    echo "Insertando Ficha de ADSO...<br>";
    // Ficha requiere un inst_id_lider. Vamos a poner a Juan (1) como lider.
    $db->exec("INSERT INTO ficha (fich_id, programa_prog_id, instructor_inst_id_lider, fich_jornada, coordinacion_coord_id, fich_fecha_ini_lectiva, fich_fecha_fin_lectiva) VALUES (2900123, 228118, 1, 'Diurna', 1, '2024-01-01', '2025-06-30')");

    $db->commit();
    echo "<h2>¡Datos de prueba insertados con éxito!</h2>";
    echo "<p>Ahora la funcionalidad de asignación tendrá datos base para trabajar, probando filtros cruzados.</p>";

} catch (PDOException $e) {
    if (isset($db)) $db->rollBack();
    echo "Error inserting data: " . $e->getMessage();
}
