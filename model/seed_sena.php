<?php
require_once dirname(__DIR__) . '/Conexion.php';

try {
    $db = Conexion::getConnect();
    $db->beginTransaction();

    echo "Insertando nuevos Programas del SENA...<br>";
    
    // Programas
    $db->exec("INSERT INTO programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo) VALUES (228101, 'Cocina', 1, 'Titulada') ON CONFLICT DO NOTHING");
    $db->exec("INSERT INTO programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo) VALUES (331102, 'Enfermería', 1, 'Titulada') ON CONFLICT DO NOTHING");
    $db->exec("INSERT INTO programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo) VALUES (821222, 'Asistencia Administrativa', 1, 'Titulada') ON CONFLICT DO NOTHING");

    echo "Insertando nuevas Competencias...<br>";
    
    // Competencias Cocina
    $db->exec("INSERT INTO competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) VALUES (10, 'Preparación de Alimentos', 300, 'Preparar alimentos de acuerdo con el orden de producción') ON CONFLICT DO NOTHING");
    $db->exec("INSERT INTO competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) VALUES (11, 'BPM', 60, 'Aplicar prácticas de higiene y manipulación de alimentos') ON CONFLICT DO NOTHING");
    
    // Competencias Enfermería
    $db->exec("INSERT INTO competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) VALUES (20, 'Atención al Paciente', 400, 'Asistir a personas en actividades de la vida diaria') ON CONFLICT DO NOTHING");
    $db->exec("INSERT INTO competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) VALUES (21, 'Primeros Auxilios', 120, 'Brindar atención básica de emergencias') ON CONFLICT DO NOTHING");

    // Competencias Asistencia Administrativa
    $db->exec("INSERT INTO competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) VALUES (30, 'Servicio al Cliente', 180, 'Atender clientes de acuerdo con procedimiento de servicio') ON CONFLICT DO NOTHING");
    $db->exec("INSERT INTO competencia (comp_id, comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) VALUES (31, 'Contabilidad Básica', 240, 'Contabilizar operaciones de acuerdo con las normas vigentes') ON CONFLICT DO NOTHING");

    echo "Asociando Competencias a Programas...<br>";
    
    // Cocina (228101)
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (228101, 10) ON CONFLICT DO NOTHING");
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (228101, 11) ON CONFLICT DO NOTHING");
    
    // Enfermería (331102)
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (331102, 20) ON CONFLICT DO NOTHING");
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (331102, 21) ON CONFLICT DO NOTHING");

    // Asistencia Administrativa (821222)
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (821222, 30) ON CONFLICT DO NOTHING");
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (821222, 31) ON CONFLICT DO NOTHING");
    
    // También les voy a asociar Matemáticas (2) e Inglés (3) que ya existen a todos
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (228101, 2) ON CONFLICT DO NOTHING");
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (228101, 3) ON CONFLICT DO NOTHING");
    
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (331102, 2) ON CONFLICT DO NOTHING");
    $db->exec("INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) VALUES (331102, 3) ON CONFLICT DO NOTHING");

    $db->commit();
    echo "¡Listo! Ya tienes más datos reales en tu base de datos.<br>";

} catch (PDOException $e) {
    if (isset($db)) $db->rollBack();
    echo "Error inserting data: " . $e->getMessage();
}
