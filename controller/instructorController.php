<?php

/**
 * InstructorController - Gestión de instructores
 */

require_once dirname(__DIR__) . '/model/InstructorModel.php';

class InstructorController
{
    private $model;

    public function __construct()
    {
        $this->model = new InstructorModel();
    }

    /**
     * Obtener listado de todos los instructores
     */
    public function index()
    {
        $instructores = $this->model->readAll();
        $this->sendResponse($instructores);
    }

    /**
     * Obtener un instructor específico por ID
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de instructor requerido'], 400);
            return;
        }

        $this->model->setInstId($id);
        $result = $this->model->read();

        if (empty($result)) {
            $this->sendResponse(['error' => 'Instructor no encontrado'], 404);
            return;
        }

        $this->sendResponse($result[0]);
    }

    /**
     * Crear un nuevo instructor
     */
    public function store()
    {
        try {
            // Verificar datos requeridos
            if (empty($_POST['inst_nombre']) || empty($_POST['inst_apellidos']) || empty($_POST['inst_correo'])) {
                $this->sendResponse(['error' => 'Faltan campos obligatorios'], 400);
                return;
            }

            // Asignar valores al modelo
            $this->model->setInstNombres($_POST['inst_nombre']);
            $this->model->setInstApellidos($_POST['inst_apellidos']);
            $this->model->setInstCorreo($_POST['inst_correo']);
            $this->model->setInstTelefono($_POST['inst_telefono'] ?? null);

            $id = $this->model->create();

            if ($id) {
                $this->sendResponse(['message' => 'Instructor creado correctamente', 'id' => $id], 201);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear el instructor'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al crear el instructor', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un instructor existente
     */
    public function update()
    {
        try {
            $id = $_POST['inst_id'] ?? null;
            
            if (!$id) {
                $this->sendResponse(['error' => 'ID de instructor requerido'], 400);
                return;
            }

            // Verificar datos requeridos
            if (empty($_POST['inst_nombre']) || empty($_POST['inst_apellidos']) || empty($_POST['inst_correo'])) {
                $this->sendResponse(['error' => 'Faltan campos obligatorios'], 400);
                return;
            }

            $this->model->setInstId($id);
            $this->model->setInstNombres($_POST['inst_nombre']);
            $this->model->setInstApellidos($_POST['inst_apellidos']);
            $this->model->setInstCorreo($_POST['inst_correo']);
            $this->model->setInstTelefono($_POST['inst_telefono'] ?? null);

            if ($this->model->update()) {
                $this->sendResponse(['message' => 'Instructor actualizado correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar el instructor'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al actualizar el instructor', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un instructor
     */
    public function destroy()
    {
        try {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                $this->sendResponse(['error' => 'ID de instructor requerido'], 400);
                return;
            }

            $this->model->setInstId($id);

            if ($this->model->delete()) {
                $this->sendResponse(['message' => 'Instructor eliminado correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar el instructor'], 500);
            }
        } catch (Exception $e) {
            // Capturar error de integridad referencial (ej: clave foránea en asignación)
            if (strpos($e->getMessage(), '23503') !== false) { // Código PostgreSQL para violación de FK
                $this->sendResponse(['error' => 'No se puede eliminar el instructor porque está asignado a fichas.'], 409);
            } else {
                $this->sendResponse(['error' => 'Error al eliminar el instructor', 'details' => $e->getMessage()], 500);
            }
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
