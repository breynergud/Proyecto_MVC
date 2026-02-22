<?php

/**
 * CentroFormacionController - Gestión de centros de formación
 */

require_once dirname(__DIR__) . '/model/CentroFormacionModel.php';

class CentroFormacionController
{
    private $model;

    public function __construct()
    {
        $this->model = new CentroFormacionModel();
    }

    /**
     * Obtener listado de todos los centros
     */
    public function index()
    {
        try {
            $centros = $this->model->readAll();
            $this->sendResponse($centros);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener centros', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener un centro específico por ID
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de centro requerido'], 400);
            return;
        }

        $this->model->setCentId($id);
        $result = $this->model->read();

        if (empty($result)) {
            $this->sendResponse(['error' => 'Centro no encontrado'], 404);
            return;
        }

        $this->sendResponse($result[0]);
    }

    /**
     * Crear un nuevo centro
     */
    public function store()
    {
        try {
            $nombre = $_POST['cent_nombre'] ?? null;

            if (!$nombre) {
                $this->sendResponse(['error' => 'El nombre del centro es requerido'], 400);
                return;
            }

            $this->model->setCentNombre($nombre);
            $id = $this->model->create();

            if ($id) {
                $this->sendResponse(['message' => 'Centro creado correctamente', 'id' => $id], 201);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear el centro'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al crear el centro', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un centro existente
     */
    public function update()
    {
        try {
            $id = $_POST['cent_id'] ?? null;
            $nombre = $_POST['cent_nombre'] ?? null;

            if (!$id || !$nombre) {
                $this->sendResponse(['error' => 'Faltan campos obligatorios'], 400);
                return;
            }

            $this->model->setCentId($id);
            $this->model->setCentNombre($nombre);

            if ($this->model->update()) {
                $this->sendResponse(['message' => 'Centro actualizado correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar el centro'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al actualizar el centro', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un centro (con verificación de integridad)
     */
    public function destroy($id = null)
    {
        try {
            if (!$id) {
                $this->sendResponse(['error' => 'ID de centro requerido'], 400);
                return;
            }

            $this->model->setCentId($id);
            
            // Verificación manual de dependencias si la BD no arroja error descriptivo
            // O simplemente atrapar la excepción de PDO (Foreign Key Violation)
            if ($this->model->delete()) {
                $this->sendResponse(['message' => 'Centro eliminado correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar el centro'], 500);
            }
        } catch (PDOException $e) {
            // Error 23503 en PostgreSQL es violación de llave foránea
            if ($e->getCode() == '23503') {
                $this->sendResponse(['error' => 'No se puede eliminar el centro porque tiene coordinaciones o instructores asociados.'], 409);
            } else {
                $this->sendResponse(['error' => 'Error de base de datos', 'details' => $e->getMessage()], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al eliminar el centro', 'details' => $e->getMessage()], 500);
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
