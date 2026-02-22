<?php
// Script de debug simple
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Programa</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        table { border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #39A900; color: white; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Debug Programa - Base de Datos</h1>
    
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    echo "<h2>1. Cargando conexión...</h2>";
    require_once __DIR__ . '/EnvLoader.php';
    require_once __DIR__ . '/Conexion.php';
    
    $db = Conexion::getConnect();
    echo "<p class='success'>✅ Conexión exitosa</p>";
    
    echo "<h2>2. Estructura de tabla PROGRAMA</h2>";
    $stmt = $db->query("
        SELECT column_name, data_type, character_maximum_length 
        FROM information_schema.columns 
        WHERE table_name = 'programa'
        ORDER BY ordinal_position
    ");
    
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Columna</th><th>Tipo</th><th>Longitud</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td><strong>{$col['column_name']}</strong></td>";
        echo "<td>{$col['data_type']}</td>";
        echo "<td>" . ($col['character_maximum_length'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>3. Títulos disponibles</h2>";
    $stmt = $db->query("SELECT * FROM titulo_programa LIMIT 3");
    $titulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($titulos) > 0) {
        echo "<ul>";
        foreach ($titulos as $t) {
            echo "<li>ID: {$t['titpro_id']} - {$t['titpro_nombre']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='error'>❌ No hay títulos</p>";
    }
    
    echo "<h2>4. Prueba de INSERT</h2>";
    
    // Obtener un título válido
    $titulo_id = $titulos[0]['titpro_id'] ?? 1;
    
    $query = "INSERT INTO programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo) 
              VALUES (:codigo, :denominacion, :titulo_id, :tipo)
              RETURNING prog_id";
    
    echo "<p><strong>Query:</strong></p>";
    echo "<pre>" . htmlspecialchars($query) . "</pre>";
    
    echo "<p><strong>Valores:</strong></p>";
    echo "<ul>";
    echo "<li>prog_codigo: 999999</li>";
    echo "<li>prog_denominacion: Test Debug</li>";
    echo "<li>tit_programa_titpro_id: $titulo_id</li>";
    echo "<li>prog_tipo: Técnico</li>";
    echo "</ul>";
    
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':codigo' => 999999,
        ':denominacion' => 'Test Debug',
        ':titulo_id' => $titulo_id,
        ':tipo' => 'Técnico'
    ]);
    
    $new_id = $stmt->fetchColumn();
    
    echo "<p class='success'>✅ INSERT exitoso! ID: $new_id</p>";
    
    // Limpiar
    $db->exec("DELETE FROM programa WHERE prog_id = $new_id");
    echo "<p class='info'>🗑️ Registro de prueba eliminado</p>";
    
    echo "<h2>5. Conclusión</h2>";
    echo "<p class='success'>✅ Todo funciona correctamente. El problema debe estar en otro lado.</p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>❌ Error de Base de Datos</h2>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Código:</strong> " . $e->getCode() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Exception $e) {
    echo "<h2 class='error'>❌ Error General</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

</body>
</html>
