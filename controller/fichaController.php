<?php

/**
 * FichaController - Gestión de fichas
 */

require_once dirname(__DIR__) . '/model/FichaModel.php';
require_once dirname(__DIR__) . '/model/ProgramaModel.php';
require_once dirname(__DIR__) . '/model/InstructorModel.php';

class FichaController
{
    private $model;
    private $programaModel;
    private $instructorModel;

    public function __construct()
    {
        $this->model = new FichaModel();
        $this->programaModel = new ProgramaModel();
        $this->instructorModel = new InstructorModel();
    }

    /**
     * Obtener listado de todas las fichas
     */
    public function index()
    {
        try {
            $fichas = $this->model->readAll();
            $this->sendResponse($fichas);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener fichas', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener programas para dropdown
     */
    public function getProgramas()
    {
        try {
            $programas = $this->programaModel->readAll();
            $this->sendResponse($programas);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener programas', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener instructores para dropdown
     */
    public function getInstructores()
    {
        try {
            $instructores = $this->instructorModel->readAll();
            $this->sendResponse($instructores);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener instructores', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener coordinaciones para dropdown
     */
    public function getCoordinaciones()
    {
        try {
            $conn = Conexion::getConnect();
            $query = "SELECT c.coord_id, c.coord_nombre_coordinador as coord_nombre, cf.cent_nombre
                      FROM coordinacion c
                      INNER JOIN centro_formacion cf ON c.CENTRO_FORMACION_cent_id = cf.cent_id
                      ORDER BY c.coord_nombre_coordinador";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $coordinaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($coordinaciones);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener coordinaciones', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener una ficha específica por ID
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de ficha requerido'], 400);
            return;
        }

        $this->model->setFichId($id);
        $result = $this->model->read();

        if (empty($result)) {
            $this->sendResponse(['error' => 'Ficha no encontrada'], 404);
            return;
        }

        $this->sendResponse($result[0]);
    }

    /**
     * Crear una nueva ficha
     */
    public function store()
    {
        try {
            $fich_id = $_POST['fich_id'] ?? null;
            $programa_id = $_POST['programa_prog_id'] ?? null;
            $instructor_id = $_POST['instructor_inst_id'] ?? null; // Front might still send this name
            $jornada = $_POST['fich_jornada'] ?? null;
            $coordinacion_id = $_POST['coordinacion_coord_id'] ?? null;
            $fecha_ini = $_POST['fich_fecha_ini_lectiva'] ?? null;
            $fecha_fin = $_POST['fich_fecha_fin_lectiva'] ?? null;

            if (!$fich_id || !$programa_id || !$instructor_id || !$jornada || !$coordinacion_id || !$fecha_ini || !$fecha_fin) {
                $this->sendResponse(['error' => 'Faltan campos obligatorios (incluyendo fechas)'], 400);
                return;
            }

            $this->model->setFichId($fich_id);
            $this->model->setProgramaProgId($programa_id);
            $this->model->setInstructorInstIdLider($instructor_id);
            $this->model->setFichJornada($jornada);
            $this->model->setCoordinacionCoordId($coordinacion_id);
            $this->model->setFichFechaIniLectiva($fecha_ini);
            $this->model->setFichFechaFinLectiva($fecha_fin);

            $id = $this->model->create();

            if ($id) {
                $this->sendResponse(['message' => 'Ficha creada correctamente', 'id' => $id], 201);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear la ficha (el modelo retornó false)'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error BD: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar una ficha existente
     */
    public function update()
    {
        try {
            $id = $_POST['fich_id'] ?? null;
            $programa_id = $_POST['programa_prog_id'] ?? null;
            $instructor_id = $_POST['instructor_inst_id'] ?? null;
            $jornada = $_POST['fich_jornada'] ?? null;
            $coordinacion_id = $_POST['coordinacion_coord_id'] ?? null;
            $fecha_ini = $_POST['fich_fecha_ini_lectiva'] ?? null;
            $fecha_fin = $_POST['fich_fecha_fin_lectiva'] ?? null;

            if (!$id || !$programa_id || !$instructor_id || !$jornada || !$coordinacion_id || !$fecha_ini || !$fecha_fin) {
                $this->sendResponse(['error' => 'Faltan campos obligatorios (incluyendo fechas)'], 400);
                return;
            }

            $this->model->setFichId($id);
            $this->model->setProgramaProgId($programa_id);
            $this->model->setInstructorInstIdLider($instructor_id);
            $this->model->setFichJornada($jornada);
            $this->model->setCoordinacionCoordId($coordinacion_id);
            $this->model->setFichFechaIniLectiva($fecha_ini);
            $this->model->setFichFechaFinLectiva($fecha_fin);

            if ($this->model->update()) {
                $this->sendResponse(['message' => 'Ficha actualizada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar la ficha'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al actualizar la ficha', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar una ficha
     */
    public function destroy($id = null)
    {
        try {
            if (!$id) {
                $this->sendResponse(['error' => 'ID de ficha requerido'], 400);
                return;
            }

            $this->model->setFichId($id);

            if ($this->model->delete()) {
                $this->sendResponse(['message' => 'Ficha eliminada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar la ficha'], 500);
            }
        } catch (Exception $e) {
            $message = 'Error al eliminar la ficha: ' . $e->getMessage();
            $this->sendResponse(['error' => $message], 500);
        }
    }

    /**
     * Helper para enviar respuestas JSON
     */
    private function sendResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
