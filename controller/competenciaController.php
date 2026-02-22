<?php

/**
 * CompetenciaController - Gestión de peticiones para Competencias
 * Sigue principios de Clean Code y estandarización de respuestas JSON.
 */

require_once dirname(__DIR__) . '/model/CompetenciaModel.php';

class CompetenciaController
{
    private $model;

    public function __construct()
    {
        // El modelo requiere parámetros en el constructor, los inicializamos nulos
        $this->model = new CompetenciaModel(null, null, null, null);
    }

    /**
     * Obtener listado de todas las competencias
     */
    public function index()
    {
        $competencias = $this->model->readAll();
        $this->sendResponse($competencias);
    }

    /**
     * Obtener una competencia específica por ID
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de competencia requerido'], 400);
            return;
        }

        $this->model->setCompId($id);
        $result = $this->model->read();

        if (empty($result)) {
            $this->sendResponse(['error' => 'Competencia no encontrada'], 404);
            return;
        }

        $this->sendResponse($result[0]);
    }

    /**
     * Crear una nueva competencia
     */
    public function store()
    {
        try {
            $nombreCorto = $_POST['comp_nombre_corto'] ?? null;
            $horas = $_POST['comp_horas'] ?? null;
            $nombreUnidadCompetencia = $_POST['comp_nombre_unidad_competencia'] ?? null;

            // Validaciones
            if (!$nombreCorto || !$horas || !$nombreUnidadCompetencia) {
                $this->sendResponse(['error' => 'Todos los campos son obligatorios'], 400);
                return;
            }

            // Validar que horas sea un número positivo
            if (!is_numeric($horas) || $horas <= 0) {
                $this->sendResponse(['error' => 'Las horas deben ser un número positivo'], 400);
                return;
            }

            $this->model->setCompNombreCorto(trim($nombreCorto));
            $this->model->setCompHoras((int)$horas);
            $this->model->setCompNombreUnidadCompetencia(trim($nombreUnidadCompetencia));
            
            $id = $this->model->create();

            if ($id) {
                $this->sendResponse(['message' => 'Competencia creada correctamente', 'id' => $id], 201);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear la competencia'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al crear la competencia', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar una competencia existente
     */
    public function update()
    {
        try {
            $id = $_POST['comp_id'] ?? null;
            $nombreCorto = $_POST['comp_nombre_corto'] ?? null;
            $horas = $_POST['comp_horas'] ?? null;
            $nombreUnidadCompetencia = $_POST['comp_nombre_unidad_competencia'] ?? null;

            // Validaciones
            if (!$id || !$nombreCorto || !$horas || !$nombreUnidadCompetencia) {
                $this->sendResponse(['error' => 'Todos los campos son obligatorios'], 400);
                return;
            }

            // Validar que horas sea un número positivo
            if (!is_numeric($horas) || $horas <= 0) {
                $this->sendResponse(['error' => 'Las horas deben ser un número positivo'], 400);
                return;
            }

            $this->model->setCompId($id);
            $this->model->setCompNombreCorto(trim($nombreCorto));
            $this->model->setCompHoras((int)$horas);
            $this->model->setCompNombreUnidadCompetencia(trim($nombreUnidadCompetencia));

            if ($this->model->update()) {
                $this->sendResponse(['message' => 'Competencia actualizada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar la competencia'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al actualizar la competencia', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar una competencia
     */
    public function destroy($comp_id = null)
    {
        try {
            if (!$comp_id) {
                $this->sendResponse(['error' => 'ID de competencia requerido para eliminar'], 400);
                return;
            }

            $this->model->setCompId($comp_id);

            if ($this->model->delete()) {
                $this->sendResponse(['message' => 'Competencia eliminada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar la competencia'], 500);
            }
        } catch (Exception $e) {
            // Error común en pgsql: 23503 es violación de llave foránea
            $message = 'No se puede eliminar la competencia porque está asociada a programas de formación.';
            if (method_exists($e, 'getCode') && $e->getCode() != '23503') {
                $message = 'Error al eliminar la competencia: ' . $e->getMessage();
            }
            $this->sendResponse(['error' => $message], 500);
        }
    }

    /**
     * Helper para enviar respuestas JSON estandarizadas
     */
    private function sendResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
