<?php

/**
 * SedeController - Gestión de peticiones para Sedes
 * Sigue principios de Clean Code y estandarización de respuestas JSON.
 */

require_once dirname(__DIR__) . '/model/SedeModel.php';

class SedeController
{
    private $model;

    public function __construct()
    {
        // El modelo requiere parámetros en el constructor, los inicializamos nulos
        $this->model = new SedeModel(null, null);
    }

    /**
     * Obtener listado de todas las sedes
     */
    public function index()
    {
        $sedes = $this->model->readAll();
        $this->sendResponse($sedes);
    }

    /**
     * Obtener una sede específica por ID
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de sede requerido'], 400);
            return;
        }

        $this->model->setSedeId($id);
        $result = $this->model->read();

        if (empty($result)) {
            $this->sendResponse(['error' => 'Sede no encontrada'], 404);
            return;
        }

        $this->sendResponse($result[0]);
    }

    /**
     * Crear una nueva sede
     */
    public function store()
    {
        try {
            $nombre = $_POST['sede_nombre'] ?? null;

            if (!$nombre) {
                $this->sendResponse(['error' => 'El nombre de la sede es obligatorio'], 400);
                return;
            }

            $this->model->setSedeNombre($nombre);
            $id = $this->model->create();

            if ($id) {
                $this->sendResponse(['message' => 'Sede creada correctamente', 'id' => $id], 201);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear la sede'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al crear la sede', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar una sede existente
     */
    public function update()
    {
        try {
            $id = $_POST['sede_id'] ?? null;
            $nombre = $_POST['sede_nombre'] ?? null;

            if (!$id || !$nombre) {
                $this->sendResponse(['error' => 'ID y nombre son obligatorios'], 400);
                return;
            }

            $this->model->setSedeId($id);
            $this->model->setSedeNombre($nombre);

            if ($this->model->update()) {
                $this->sendResponse(['message' => 'Sede actualizada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar la sede'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al actualizar la sede', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar una sede
     */
    public function destroy($sede_id = null)
    {
        try {
            if (!$sede_id) {
                $this->sendResponse(['error' => 'ID de sede requerido para eliminar'], 400);
                return;
            }

            $this->model->setSedeId($sede_id);

            if ($this->model->delete()) {
                $this->sendResponse(['message' => 'Sede eliminada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar la sede'], 500);
            }
        } catch (Exception $e) {
            // Error común en pgsql: 23503 es violación de llave foránea
            $message = 'No se puede eliminar la sede porque tiene ambientes o datos asociados.';
            if (method_exists($e, 'getCode') && $e->getCode() != '23503') {
                $message = 'Error al eliminar la sede: ' . $e->getMessage();
            }
            $this->sendResponse(['error' => $message], 500);
        }
    }

    public function getProgramas($sede_id = null)
    {
        if (!$sede_id) {
            $this->sendResponse(['error' => 'ID de sede requerido'], 400);
            return;
        }

        $this->model->setSedeId($sede_id);
        $programas = $this->model->getProgramasBySede();
        $this->sendResponse($programas);
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
