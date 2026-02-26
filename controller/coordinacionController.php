<?php

/**
 * CoordinacionController - Gestión de coordinaciones
 */

require_once dirname(__DIR__) . '/model/CoordinacionModel.php';
require_once dirname(__DIR__) . '/model/CentroFormacionModel.php';

class CoordinacionController
{
    private $model;
    private $centroModel;

    public function __construct()
    {
        $this->model = new CoordinacionModel();
        $this->centroModel = new CentroFormacionModel();
    }

    /**
     * Obtener listado de todas las coordinaciones
     */
    public function index()
    {
        try {
            $coordinaciones = $this->model->readAll();
            $this->sendResponse($coordinaciones);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener coordinaciones', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener centros de formación para dropdown
     */
    public function getCentros()
    {
        try {
            $centros = $this->centroModel->readAll();
            $this->sendResponse($centros);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener centros', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener una coordinación específica por ID
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de coordinación requerido'], 400);
            return;
        }

        $this->model->setCoordId($id);
        $result = $this->model->read();

        if (empty($result)) {
            $this->sendResponse(['error' => 'Coordinación no encontrada'], 404);
            return;
        }

        $this->sendResponse($result[0]);
    }

    /**
     * Crear una nueva coordinación
     */
    public function store()
    {
        try {
            $descripcion = $_POST['coord_descripcion'] ?? null;
            $centro_id = $_POST['centro_formacion_cent_id'] ?? null;
            $nombre_coordinador = $_POST['coord_nombre_coordinador'] ?? null;
            $correo = $_POST['coord_correo'] ?? null;

            if (!$descripcion || !$centro_id || !$nombre_coordinador) {
                $this->sendResponse(['error' => 'Faltan campos obligatorios'], 400);
                return;
            }

            $this->model->setCoordDescripcion($descripcion);
            $this->model->setCentroFormacionCentId($centro_id);
            $this->model->setCoordNombreCoordinador($nombre_coordinador);
            $this->model->setCoordCorreo($correo);

            $id = $this->model->create();

            if ($id) {
                $this->sendResponse(['message' => 'Coordinación creada correctamente', 'id' => $id], 201);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear la coordinación'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al crear la coordinación', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar una coordinación existente
     */
    public function update()
    {
        try {
            $id = $_POST['coord_id'] ?? null;
            $descripcion = $_POST['coord_descripcion'] ?? null;
            $centro_id = $_POST['centro_formacion_cent_id'] ?? null;
            $nombre_coordinador = $_POST['coord_nombre_coordinador'] ?? null;
            $correo = $_POST['coord_correo'] ?? null;

            if (!$id || !$descripcion || !$centro_id || !$nombre_coordinador) {
                $this->sendResponse(['error' => 'Faltan campos obligatorios'], 400);
                return;
            }

            $this->model->setCoordId($id);
            $this->model->setCoordDescripcion($descripcion);
            $this->model->setCentroFormacionCentId($centro_id);
            $this->model->setCoordNombreCoordinador($nombre_coordinador);
            $this->model->setCoordCorreo($correo);

            if ($this->model->update()) {
                $this->sendResponse(['message' => 'Coordinación actualizada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar la coordinación'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al actualizar la coordinación', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar una coordinación
     */
    public function destroy($id = null)
    {
        try {
            if (!$id) {
                $this->sendResponse(['error' => 'ID de coordinación requerido'], 400);
                return;
            }

            $this->model->setCoordId($id);

            if ($this->model->delete()) {
                $this->sendResponse(['message' => 'Coordinación eliminada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar la coordinación'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al eliminar la coordinación', 'details' => $e->getMessage()], 500);
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
