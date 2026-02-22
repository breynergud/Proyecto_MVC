<?php

// Carga de variables de entorno para que Conexion.php funcione
require_once __DIR__ . '/EnvLoader.php';

// Prevenir salida de errores HTML que rompan el JSON en peticiones AJAX
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
}

$controllers = array(
    'sede' => ['index', 'show', 'store', 'update', 'destroy', 'getProgramas'],
    'ambiente' => ['index', 'show', 'store', 'update', 'destroy'],
    'competencia' => ['index', 'show', 'store', 'update', 'destroy'],
    'programa' => ['index', 'show', 'store', 'update', 'destroy', 'getTitulos', 'getCompetencias', 'getCompetenciasDisponibles', 'asociarCompetencia', 'desasociarCompetencia', 'asociarCompetencias'],
    'titulo_programa' => ['index', 'show', 'store', 'update', 'destroy'],
    'ficha' => ['index', 'show', 'store', 'update', 'destroy', 'getProgramas', 'getInstructores', 'getCoordinaciones'],
    'instructor' => ['index', 'show', 'store', 'update', 'destroy'],
    'asignacion' => ['index', 'store', 'destroy', 'getFichaInfo', 'getAsignacionesByFicha', 'getCompetenciasPendientes', 'getInstructoresByCompetencia'],
    'coordinacion' => ['index', 'show', 'store', 'update', 'destroy', 'getCentros'],
    'centro_formacion' => ['index', 'show', 'store', 'update', 'destroy'],
    // Agrega más controladores y acciones aquí si lo necesitas
);

// Obtener controlador y acción buscando en GET y POST
$controller = $_GET['controller'] ?? $_POST['controller'] ?? 'sede';
$action = $_GET['action'] ?? $_POST['action'] ?? 'index';

// Validación básica de existencia del controlador y acción
if (array_key_exists($controller, $controllers)) {
    if (in_array($action, $controllers[$controller])) {
        call($controller, $action);
    } else {
        call('sede', 'index'); // acción por defecto si no existe
    }
} else {
    call('sede', 'index'); // controlador por defecto si no existe
}

function call($controller, $action)
{
    $controllerFile = 'controller/' . $controller . 'Controller.php';

    // Verificar que el archivo del controlador existe
    if (!file_exists($controllerFile)) {
        http_response_code(404);
        echo "<h1>Error 404: Controlador no encontrado</h1>";
        return;
    }

    require_once($controllerFile);

    switch ($controller) {
        case 'sede':
            require_once('model/SedeModel.php');
            $controllerObj = new SedeController();
            break;
        case 'ambiente':
            require_once('model/AmbienteModel.php');
            require_once('model/SedeModel.php');
            $controllerObj = new AmbienteController();
            break;
        case 'competencia':
            require_once('model/CompetenciaModel.php');
            $controllerObj = new CompetenciaController();
            break;
        case 'programa':
            require_once('model/ProgramaModel.php');
            require_once('model/TituloProgramaModel.php');
            require_once('model/CompetenciaProgramaModel.php');
            require_once('model/CompetenciaModel.php');
            $controllerObj = new ProgramaController();
            break;
        case 'titulo_programa':
            require_once('model/TituloProgramaModel.php');
            $controllerObj = new TituloProgramaController();
            break;
        case 'ficha':
            require_once('model/FichaModel.php');
            require_once('model/ProgramaModel.php');
            require_once('model/InstructorModel.php');
            $controllerObj = new FichaController();
            break;
        case 'instructor':
            require_once('model/InstructorModel.php');
            $controllerObj = new InstructorController();
            break;
        case 'asignacion':
            require_once('model/AsignacionModel.php');
            require_once('model/FichaModel.php');
            require_once('model/InstructorModel.php');
            require_once('model/CompetenciaModel.php');
            $controllerObj = new AsignacionController();
            break;
        case 'coordinacion':
            require_once('model/CoordinacionModel.php');
            require_once('model/CentroFormacionModel.php');
            $controllerObj = new CoordinacionController();
            break;
        case 'centro_formacion':
            require_once('model/CentroFormacionModel.php');
            $controllerObj = new CentroFormacionController();
            break;
        default:
            http_response_code(404);
            echo "<h1>Error 404: Controlador no encontrado</h1>";
            return;
    }

    // Verificar que el método existe
    if (!method_exists($controllerObj, $action)) {
        http_response_code(404);
        echo "<h1>Error 404: Acción no encontrada</h1>";
        return;
    }

    try {
        $reflection = new ReflectionMethod($controllerObj, $action);
        $args = [];

        // Recolecta los parámetros esperados desde $_POST o $_GET en el orden correcto
        foreach ($reflection->getParameters() as $param) {
            $paramName = $param->getName();
            if (isset($_POST[$paramName])) {
                $args[] = $_POST[$paramName];
            } elseif (isset($_GET[$paramName])) {
                $args[] = $_GET[$paramName];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } elseif (!$param->isOptional()) {
                http_response_code(400);
                if (ini_get('display_errors') == 0) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => "Falta el parámetro requerido: $paramName"]);
                } else {
                    echo "<h1>Error 400: Falta el parámetro requerido: $paramName</h1>";
                }
                return;
            }
        }

        // Llama al método con los argumentos necesarios
        $controllerObj->{$action}(...$args);
    } catch (Exception $e) {
        http_response_code(500);
        if (ini_get('display_errors') == 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error interno en el servidor', 'details' => $e->getMessage()]);
        } else {
            echo "<h1>Error 500: " . $e->getMessage() . "</h1>";
        }
    }
}
