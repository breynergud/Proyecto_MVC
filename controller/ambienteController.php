<?php

/**
 * AmbienteController - Gestión de peticiones para Ambientes
 */

require_once dirname(__DIR__) . '/model/AmbienteModel.php';
require_once dirname(__DIR__) . '/model/SedeModel.php';

class AmbienteController
{
    private $model;

    public function __construct()
    {
        $this->model = new AmbienteModel(null, null, null);
    }

    /**
     * Obtener listado de todos los ambientes
     * Si se pasa sede_id, filtra por esa sede
     */
    public function index($sede_id = null)
    {
        if ($sede_id) {
            $this->model->setSedeSedeId($sede_id);
            $ambientes = $this->model->read();
        } else {
            $ambientes = $this->model->readAll();
        }
        $this->sendResponse($ambientes);
    }

    /**
     * Obtener un ambiente específico por ID
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de ambiente requerido'], 400);
            return;
        }

        $ambiente = $this->model->readById($id);

        if (!$ambiente) {
            $this->sendResponse(['error' => 'Ambiente no encontrado'], 404);
            return;
        }

        $this->sendResponse($ambiente);
    }

    /**
     * Crear un nuevo ambiente
     */
    public function store()
    {
        try {
            $id = $_POST['amb_id'] ?? null;
            $nombre = $_POST['amb_nombre'] ?? null;
            $sede_id = $_POST['sede_sede_id'] ?? null;
            $tipo = $_POST['tipo_ambiente'] ?? 'Convencional';

            if (!$id || !$nombre || !$sede_id) {
                $this->sendResponse(['error' => 'ID (5 caracteres), Nombre y Sede son obligatorios'], 400);
                return;
            }

            // Validar unicidad
            if ($this->model->exists($id)) {
                $this->sendResponse(['error' => "El ID de ambiente $id ya se encuentra registrado"], 409);
                return;
            }

            $this->model->setAmbId($id);
            $this->model->setAmbnombre($nombre);
            $this->model->setSedeSedeId($sede_id);
            $this->model->setTipoAmbiente($tipo);
            $newId = $this->model->create();

            if ($newId) {
                $this->sendResponse(['message' => 'Ambiente creado correctamente', 'id' => $newId], 201);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear el ambiente'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al crear ambiente', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un ambiente existente
     */
    public function update()
    {
        try {
            // Nota: amb_id es PK, si se quiere cambiar el ID, debería ser otra lógica o no permitirse.
            // Aquí asumimos que amb_id identifica el registro a actualizar.
            $id = $_POST['amb_id'] ?? null;
            $nombre = $_POST['amb_nombre'] ?? null;
            $sede_id = $_POST['sede_sede_id'] ?? null;
            $tipo = $_POST['tipo_ambiente'] ?? 'Convencional';

            if (!$id || !$nombre || !$sede_id) {
                $this->sendResponse(['error' => 'ID, Nombre y Sede son obligatorios'], 400);
                return;
            }

            $this->model->setAmbId($id);
            $this->model->setAmbnombre($nombre);
            $this->model->setSedeSedeId($sede_id);
            $this->model->setTipoAmbiente($tipo);

            if ($this->model->update()) {
                $this->sendResponse(['message' => 'Ambiente actualizado correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar el ambiente'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al actualizar ambiente', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un ambiente
     */
    public function destroy($id = null)
    {
        try {
            if (!$id) {
                $this->sendResponse(['error' => 'ID requerido para eliminar'], 400);
                return;
            }

            $this->model->setAmbId($id);

            if ($this->model->delete()) {
                $this->sendResponse(['message' => 'Ambiente eliminado correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar el ambiente'], 500);
            }
        } catch (Exception $e) {
            // Error común en pgsql: 23503 es violación de llave foránea
            $message = 'No se puede eliminar el ambiente porque tiene datos asociados (fichas o asignaciones).';
            if (method_exists($e, 'getCode') && $e->getCode() != '23503') {
                $message = 'Error al eliminar el ambiente: ' . $e->getMessage();
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
