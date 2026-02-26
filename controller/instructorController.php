<?php

/**
 * InstructorController - Gestión de instructores
 */

require_once dirname(__DIR__) . '/model/InstructorModel.php';
require_once dirname(__DIR__) . '/model/CompetenciaProgramaModel.php';


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

        $instructor_data = $result[0];
        $instructor_data['especialidades'] = $this->model->getEspecialidades($id);

        $this->sendResponse($instructor_data);
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
            
            // Valores por defecto para campos obligatorios no presentes en el formulario
            $this->model->setCentroFormacionCentId(1); // Centro por defecto
            $this->model->setInstPassword(password_hash('admin123', PASSWORD_DEFAULT)); // Password por defecto: admin123 (hasheado)

            // Iniciar transacción explícita
            $db = Conexion::getConnect();
            $db->beginTransaction();

            $id = $this->model->create();

            if ($id) {
                // Guardar Especialidades si existen
                $programa_id = $_POST['programa_id'] ?? null;
                $competencias = isset($_POST['competencias']) ? json_decode($_POST['competencias'], true) : [];
                
                if ($programa_id && !empty($competencias)) {
                    $this->model->saveEspecialidades($id, $programa_id, $competencias);
                }

                $db->commit();
                $this->sendResponse(['message' => 'Instructor creado correctamente', 'id' => $id], 201);
            } else {
                $db->rollBack();
                $this->sendResponse(['error' => 'No se pudo crear el instructor'], 500);
            }
        } catch (Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            // Log de error detallado
            file_put_contents(__DIR__ . '/instructor_error.log', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
            $this->sendResponse(['error' => 'Error al crear el instructor', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener listado de Programas para Especialidades
     */
    public function getProgramas()
    {
        try {
            require_once dirname(__DIR__) . '/model/ProgramaModel.php';
            $programaModel = new ProgramaModel();
            $programas = $programaModel->readAll();
            $this->sendResponse($programas);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error', 'details' => $e->getMessage()], 500);
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
            $this->model->setCentroFormacionCentId(1); // Mantener centro por defecto

            // Iniciar transacción explícita
            $db = Conexion::getConnect();
            $db->beginTransaction();

            if ($this->model->update()) {
                // Guardar Especialidades si existen
                $programa_id = $_POST['programa_id'] ?? null;
                $competencias = isset($_POST['competencias']) ? json_decode($_POST['competencias'], true) : [];
                
                $this->model->deleteEspecialidades($id);
                if ($programa_id && !empty($competencias)) {
                    $this->model->saveEspecialidades($id, $programa_id, $competencias);
                }

                $db->commit();
                $this->sendResponse(['message' => 'Instructor actualizado correctamente']);
            } else {
                $db->rollBack();
                $this->sendResponse(['error' => 'No se pudo actualizar el instructor'], 500);
            }
        } catch (Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
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
     * Obtener competencias asociadas a un programa específico (para el filtro de especialidades)
     */
    public function getCompetenciasInstructorPrograma($programa_id = null)
    {
        $programa_id = $programa_id ?? $_POST['programa_id'] ?? $_GET['programa_id'] ?? null;

        if (!$programa_id) {
            $this->sendResponse(['error' => 'ID de programa requerido'], 400);
            return;
        }

        try {
            $compProgModel = new CompetenciaProgramaModel();
            $competencias = $compProgModel->getCompetenciasByPrograma($programa_id);

            // Mapear para que coincida con lo que espera el JS (id, nombre)
            $result = array_map(function ($c) {
                return [
                    'id' => $c['comp_id'],
                    'nombre' => $c['comp_nombre_corto']
                ];
            }, $competencias);

            $this->sendResponse($result);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener competencias', 'details' => $e->getMessage()], 500);
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
