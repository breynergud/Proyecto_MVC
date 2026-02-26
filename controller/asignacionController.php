<?php

/**
 * AsignacionController - Gestión de asignaciones de instructores
 */

require_once dirname(__DIR__) . '/model/AsignacionModel.php';
require_once dirname(__DIR__) . '/model/FichaModel.php';
require_once dirname(__DIR__) . '/model/InstructorModel.php';
require_once dirname(__DIR__) . '/model/CompetenciaModel.php';
require_once dirname(__DIR__) . '/model/AmbienteModel.php';
require_once dirname(__DIR__) . '/model/DetalleAsignacionModel.php';

class AsignacionController
{
    private $model;
    private $fichaModel;
    private $instructorModel;
    private $competenciaModel;
    private $detalleModel;

    public function __construct()
    {
        $this->model = new AsignacionModel();
        $this->fichaModel = new FichaModel();
        $this->instructorModel = new InstructorModel();
        $this->competenciaModel = new CompetenciaModel();
        $this->detalleModel = new DetalleAsignacionModel();
    }

    /**
     * Obtener información completa de una ficha
     */
    public function getFichaInfo($ficha_id = null)
    {
        if (!$ficha_id) {
            $this->sendResponse(['error' => 'ID de ficha requerido'], 400);
            return;
        }

        try {
            $conn = Conexion::getConnect();
            
            $query = "SELECT 
                        f.fich_id,
                        f.fich_jornada,
                        f.fich_fecha_ini_lectiva,
                        p.prog_denominacion as programa_nombre,
                        p.prog_codigo,
                        CONCAT(i.inst_nombres, ' ', i.inst_apellidos) as instructor_nombre
                      FROM ficha f
                      LEFT JOIN programa p ON f.programa_prog_id = p.prog_codigo
                      LEFT JOIN instructor i ON f.instructor_inst_id_lider = i.inst_id
                      WHERE f.fich_id = :ficha_id";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $ficha = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$ficha) {
                $this->sendResponse(['error' => 'Ficha no encontrada'], 404);
                return;
            }
            
            $this->sendResponse($ficha);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener información de la ficha', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener eventos para FullCalendar filtrados por ficha
     */
    public function getEventos($ficha_id = null)
    {
        if (!$ficha_id) {
            $this->sendResponse(['error' => 'ID de ficha requerido'], 400);
            return;
        }

        try {
            $conn = Conexion::getConnect();
            
            $userRole = $_SESSION['user_role'] ?? '';
            $userId = $_SESSION['user_id'] ?? null;

            $query = "SELECT 
                        a.asig_id as id,
                        c.comp_nombre_corto as title,
                        a.asig_fecha_ini as start,
                        a.asig_fecha_fin as end,
                        CONCAT(i.inst_nombres, ' ', i.inst_apellidos) as instructor
                      FROM asignacion a
                      LEFT JOIN competencia c ON a.competencia_comp_id = c.comp_id
                      LEFT JOIN instructor i ON a.instructor_inst_id = i.inst_id
                      WHERE a.ficha_fich_id = :ficha_id";
            
            if ($userRole === 'instructor' && $userId) {
                $query .= " AND a.instructor_inst_id = :userId";
            }
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            if ($userRole === 'instructor' && $userId) {
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            }
            $stmt->execute();
            
            $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($eventos);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener eventos', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener lista de instructores para el modal
     */
    public function getInstructoresList()
    {
        try {
            $conn = Conexion::getConnect();
            $query = "SELECT inst_id as id, CONCAT(inst_nombres, ' ', inst_apellidos) as nombre FROM instructor ORDER BY inst_apellidos";
            $stmt = $conn->query($query);
            $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($instructores);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener instructores'], 500);
        }
    }

    /**
     * Obtener lista de ambientes para el modal
     */
    public function getAmbientesList()
    {
        try {
            $conn = Conexion::getConnect();
            $query = "SELECT amb_id as id, amb_nombre as nombre FROM ambiente ORDER BY amb_nombre";
            $stmt = $conn->query($query);
            $ambientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($ambientes);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener ambientes'], 500);
        }
    }

    /**
     * Obtener lista de competencias generales (todas)
     */
    public function getCompetenciasList()
    {
        try {
            $conn = Conexion::getConnect();
            $query = "SELECT comp_id as id, comp_nombre_corto as nombre FROM competencia ORDER BY comp_nombre_corto";
            $stmt = $conn->query($query);
            $competencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($competencias);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener competencias'], 500);
        }
    }

    /**
     * Obtener competencias asociadas a un programa específico
     */
    public function getCompetenciasByPrograma($programa_id = null)
    {
        $programa_id = $programa_id ?? $_POST['programa_id'] ?? null;
        
        if (!$programa_id) {
            $this->sendResponse(['error' => 'ID de programa requerido'], 400);
            return;
        }

        try {
            $conn = Conexion::getConnect();
            $query = "SELECT c.comp_id as id, c.comp_nombre_corto as nombre 
                      FROM competencia c
                      INNER JOIN competxprograma cp ON c.comp_id = cp.competencia_comp_id
                      WHERE cp.programa_prog_id = :programa_id
                      ORDER BY c.comp_nombre_corto";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':programa_id', $programa_id);
            $stmt->execute();
            
            $competencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($competencias);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener competencias del programa', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener asignaciones de una ficha
     */
    public function getAsignacionesByFicha($ficha_id = null)
    {
        if (!$ficha_id) {
            $this->sendResponse(['error' => 'ID de ficha requerido'], 400);
            return;
        }

        try {
            $conn = Conexion::getConnect();
            
            $query = "SELECT 
                        a.asig_id,
                        a.asig_fecha_ini,
                        a.asig_fecha_fin,
                        c.comp_nombre_corto as competencia_nombre,
                        CONCAT(i.inst_nombres, ' ', i.inst_apellidos) as instructor_nombre,
                        amb.amb_nombre as ambiente_nombre
                      FROM asignacion a
                      LEFT JOIN competencia c ON a.competencia_comp_id = c.comp_id
                      LEFT JOIN instructor i ON a.instructor_inst_id = i.inst_id
                      LEFT JOIN ambiente amb ON a.ambiente_amb_id = amb.amb_id
                      WHERE a.ficha_fich_id = :ficha_id
                      ORDER BY a.asig_fecha_ini";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($asignaciones);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener asignaciones', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener competencias pendientes de una ficha
     * (Competencias del programa que aún no han sido asignadas)
     */
    public function getCompetenciasPendientes($ficha_id = null)
    {
        if (!$ficha_id) {
            $this->sendResponse(['error' => 'ID de ficha requerido'], 400);
            return;
        }

        try {
            $conn = Conexion::getConnect();
            
            $query = "SELECT DISTINCT c.comp_id, c.comp_nombre_corto, c.comp_horas, c.comp_nombre_unidad_competencia
                      FROM competencia c
                      INNER JOIN competxprograma cp ON c.comp_id = cp.competencia_comp_id
                      INNER JOIN ficha f ON cp.programa_prog_id = f.programa_prog_id
                      WHERE f.fich_id = :ficha_id
                      AND c.comp_id NOT IN (
                          SELECT competencia_comp_id 
                          FROM asignacion 
                          WHERE ficha_fich_id = :ficha_id
                      )
                      ORDER BY c.comp_nombre_corto";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $competencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($competencias);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener competencias pendientes', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener instructores especializados en una competencia
     * Por ahora devuelve todos los instructores, pero se puede filtrar por especialidad
     */
    public function getInstructoresByCompetencia($competencia_id = null, $programa_id = null)
    {
        $competencia_id = $competencia_id ?? $_GET['competencia_id'] ?? $_POST['competencia_id'] ?? null;
        $programa_id = $programa_id ?? $_GET['programa_id'] ?? $_POST['programa_id'] ?? null;

        if (!$competencia_id || !$programa_id) {
            $this->sendResponse(['error' => 'ID de competencia y programa requeridos'], 400);
            return;
        }

        try {
            $conn = Conexion::getConnect();
            
            $query = "SELECT DISTINCT
                        i.inst_id,
                        i.inst_nombres,
                        i.inst_apellidos,
                        i.inst_correo,
                        i.inst_telefono,
                        cf.cent_nombre as centro_nombre
                      FROM instructor i
                      INNER JOIN instru_competencia ic ON i.inst_id = ic.instructor_inst_id
                      LEFT JOIN centro_formacion cf ON i.centro_formacion_cent_id = cf.cent_id
                      WHERE ic.competxprograma_competencia_comp_id = :competencia_id
                      AND ic.competxprograma_programa_prog_id = :programa_id
                      ORDER BY i.inst_apellidos, i.inst_nombres";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':competencia_id', $competencia_id, PDO::PARAM_INT);
            $stmt->bindParam(':programa_id', $programa_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($instructores);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener instructores', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Crear una nueva asignación
     */
    public function store()
    {
        try {
            // Obtener datos de POST
            $ficha_id = $_POST['ficha_id'] ?? null;
            $instructor_id = $_POST['instructor_id'] ?? null;
            $competencia_id = $_POST['competencia_id'] ?? null;
            $ambiente_id = $_POST['ambiente_id'] ?? null;
            $fecha_inicio = $_POST['fecha_inicio'] ?? null;
            $fecha_fin = $_POST['fecha_fin'] ?? null;
            $hora_inicio = $_POST['hora_inicio'] ?? '07:00:00';
            $hora_fin = $_POST['hora_fin'] ?? '12:00:00';

            // Validación básica
            if (!$ficha_id || !$instructor_id || !$competencia_id || !$ambiente_id || !$fecha_inicio || !$fecha_fin) {
                $this->sendResponse(['status' => 'error', 'message' => 'Faltan campos obligatorios'], 400);
                return;
            }

            // Validar coherencia de fechas
            if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
                $this->sendResponse(['status' => 'error', 'message' => 'La fecha de fin no puede ser anterior a la de inicio'], 400);
                return;
            }

            // Verificar Límite de 2 asignaciones (Transversales) por día para la Ficha
            $asignacionesDia = $this->model->countAsignacionesFichaPorDia($ficha_id, $fecha_inicio, $fecha_fin);
            if ($asignacionesDia >= 2) {
                $this->sendResponse(['status' => 'error', 'message' => 'La ficha ya tiene el máximo permitido de 2 transversales para este rango de fechas'], 400);
                return;
            }

            // Verificar Cruces de Horario (Ficha, Ambiente o Instructor)
            $cruces = $this->model->checkCrucesHorarios($fecha_inicio, $fecha_fin, $hora_inicio, $hora_fin, $instructor_id, $ambiente_id, $ficha_id);
            if (!empty($cruces)) {
                $this->sendResponse(['status' => 'error', 'message' => 'Existe un cruce de horarios. El instructor, ambiente o la ficha ya están ocupados en este periodo.'], 400);
                return;
            }

            $this->model->setFichaFichId($ficha_id);
            $this->model->setInstructorInstId($instructor_id);
            $this->model->setCompetenciaCompId($competencia_id);
            $this->model->setAmbienteAmbId($ambiente_id);
            $this->model->setAsigFechaIni($fecha_inicio);
            $this->model->setAsigFechaFin($fecha_fin);
            
            $id = $this->model->create();
            
            if ($id) {
                // Guardar detalles de tiempo
                $this->detalleModel->setAsignacionAsigId($id);
                $this->detalleModel->setDetasigHoraIni($hora_inicio);
                $this->detalleModel->setDetasigHoraFin($hora_fin);
                $this->detalleModel->create();

                $this->sendResponse(['status' => 'success', 'message' => 'Asignación creada correctamente', 'id' => $id], 201);
            } else {
                $this->sendResponse(['status' => 'error', 'message' => 'No se pudo crear la asignación'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['status' => 'error', 'message' => 'Error al crear la asignación', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar todas las asignaciones
     */
    public function index()
    {
        try {
            $conn = Conexion::getConnect();
            
            $userRole = $_SESSION['user_role'] ?? '';
            $userId = $_SESSION['user_id'] ?? null;

            $query = "SELECT 
                        a.asig_id,
                        a.asig_fecha_ini,
                        a.asig_fecha_fin,
                        f.fich_id,
                        c.comp_nombre_corto as competencia_nombre,
                        CONCAT(i.inst_nombres, ' ', i.inst_apellidos) as instructor_nombre,
                        amb.amb_nombre as ambiente_nombre,
                        p.prog_denominacion as programa_nombre
                      FROM asignacion a
                      INNER JOIN ficha f ON a.ficha_fich_id = f.fich_id
                      INNER JOIN competencia c ON a.competencia_comp_id = c.comp_id
                      INNER JOIN instructor i ON a.instructor_inst_id = i.inst_id
                      INNER JOIN ambiente amb ON a.ambiente_amb_id = amb.amb_id
                      INNER JOIN programa p ON f.programa_prog_id = p.prog_codigo";
            
            if ($userRole === 'instructor' && $userId) {
                $query .= " WHERE a.instructor_inst_id = :userId";
            }
            
            $query .= " ORDER BY a.asig_id DESC";
            
            $stmt = $conn->prepare($query);
            if ($userRole === 'instructor' && $userId) {
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            }
            $stmt->execute();
            
            $asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($asignaciones);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener asignaciones', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar una asignación
     */
    public function destroy($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de asignación requerido'], 400);
            return;
        }

        try {
            $id = (int)$id;
            
            // Primero eliminar detalles (FK)
            $conn = Conexion::getConnect();
            $stmtDet = $conn->prepare("DELETE FROM detallexasignacion WHERE ASIGNACION_ASIG_ID = :id");
            $stmtDet->execute([':id' => $id]);

            $this->model->setAsigId($id);
            $result = $this->model->delete();

            if ($result) {
                $this->sendResponse(['message' => 'Asignación eliminada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar la asignación'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al eliminar la asignación', 'details' => $e->getMessage()], 500);
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
