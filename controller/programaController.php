<?php

/**
 * ProgramaController - Gestión de peticiones para Programas
 */

require_once dirname(__DIR__) . '/model/ProgramaModel.php';
require_once dirname(__DIR__) . '/model/TituloProgramaModel.php';
require_once dirname(__DIR__) . '/model/CompetenciaProgramaModel.php';
require_once dirname(__DIR__) . '/model/CompetenciaModel.php';

class ProgramaController
{
    private $model;
    private $tituloModel;
    private $competenciaProgramaModel;
    private $competenciaModel;

    public function __construct()
    {
        $this->model = new ProgramaModel();
        $this->tituloModel = new TituloProgramaModel();
        $this->competenciaProgramaModel = new CompetenciaProgramaModel();
        $this->competenciaModel = new CompetenciaModel();
    }

    /**
     * Obtener listado de todos los programas
     */
    public function index()
    {
        $programas = $this->model->readAll();
        
        // Agregar competencias a cada programa
        foreach ($programas as &$programa) {
            $competencias = $this->competenciaProgramaModel->getCompetenciasByPrograma($programa['prog_codigo']);
            $programa['competencias'] = $competencias;
            $programa['total_competencias'] = count($competencias);
        }
        
        $this->sendResponse($programas);
    }

    /**
     * Obtener los títulos de programa para dropdowns
     */
    public function getTitulos()
    {
        $titulos = $this->tituloModel->readAll();
        $this->sendResponse($titulos);
    }

    /**
     * Obtener un programa específico por ID
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de programa requerido'], 400);
            return;
        }

        $this->model->setProgCodigo($id); // Ajustado a setProgCodigo
        $result = $this->model->read();

        if (empty($result)) {
            $this->sendResponse(['error' => 'Programa no encontrado'], 404);
            return;
        }

        $this->sendResponse($result[0]);
    }

    /**
     * Crear un nuevo programa
     */
    public function store()
    {
        try {
            $codigo = $_POST['prog_codigo'] ?? null;
            $denominacion = $_POST['prog_denominacion'] ?? null;
            $tit_id = $_POST['tit_programa_titpro_id'] ?? null;
            $tipo = $_POST['prog_tipo'] ?? null;
            $competencias_ids = $_POST['competencias_ids'] ?? [];

            if (!$codigo || !$denominacion || !$tit_id) {
                $this->sendResponse(['error' => 'Faltan campos obligatorios'], 400);
                return;
            }

            // Validar unicidad
            if ($this->model->exists($codigo)) {
                $this->sendResponse(['error' => "El código de programa $codigo ya se encuentra registrado"], 409);
                return;
            }

            // Start transaction
            $this->model->beginTransaction(); 

            try {
                $this->model->setProgCodigo($codigo);
                $this->model->setProgDenominacion($denominacion);
                $this->model->setTitProgramaTitproId($tit_id);
                $this->model->setProgTipo($tipo);
                
                $id = $this->model->create();
    
                if ($id) {
                    // Save competencies
                    if (!empty($competencias_ids)) {
                        foreach ($competencias_ids as $comp_id) {
                            $this->competenciaProgramaModel->asociar($id, $comp_id);
                        }
                    }
                    
                    $this->model->commit();
                    $this->sendResponse(['message' => 'Programa creado correctamente', 'id' => $id], 201);
                } else {
                    $this->model->rollBack();
                    $this->sendResponse(['error' => 'No se pudo crear el programa'], 500);
                }

            } catch (Exception $e) {
                $this->model->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al crear el programa', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un programa existente
     */
    public function update()
    {
        try {
            // En el nuevo esquema, prog_codigo es PK.
            // Si asumimos que prog_id en el formulario se refiere al prog_codigo antigo o al mismo valor...
            // Usaremos prog_codigo como ID identificador.
            $codigo = $_POST['prog_codigo'] ?? null;
            $denominacion = $_POST['prog_denominacion'] ?? null;
            $tit_id = $_POST['tit_programa_titpro_id'] ?? null;
            $tipo = $_POST['prog_tipo'] ?? null;

            if (!$codigo || !$denominacion || !$tit_id) {
                $this->sendResponse(['error' => 'Faltan campos obligatorios'], 400);
                return;
            }

            $this->model->setProgCodigo($codigo); // PK
            $this->model->setProgDenominacion($denominacion);
            $this->model->setTitProgramaTitproId($tit_id);
            $this->model->setProgTipo($tipo);

            if ($this->model->update()) {
                $this->sendResponse(['message' => 'Programa actualizado correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar el programa'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al actualizar el programa', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un programa
     */
    public function destroy($id = null)
    {
        try {
            if (!$id) {
                $this->sendResponse(['error' => 'ID de programa requerido'], 400);
                return;
            }

            $this->model->setProgCodigo($id);

            if ($this->model->delete()) {
                $this->sendResponse(['message' => 'Programa eliminado correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar el programa'], 500);
            }
        } catch (Exception $e) {
            $message = 'Error al eliminar el programa: ' . $e->getMessage();
            // pgsql foreign key violation check could be added here if needed
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

    /**
     * Obtener competencias asociadas a un programa
     */
    public function getCompetencias($programa_id = null)
    {
        if (!$programa_id) {
            $this->sendResponse(['error' => 'ID de programa requerido'], 400);
            return;
        }

        $competencias = $this->competenciaProgramaModel->getCompetenciasByPrograma($programa_id);
        $this->sendResponse($competencias);
    }

    /**
     * Obtener competencias disponibles (no asociadas) para un programa
     */
    public function getCompetenciasDisponibles($programa_id = null)
    {
        if (!$programa_id) {
            // Si no hay programa_id, devolver todas las competencias
            $competencias = $this->competenciaModel->readAll();
        } else {
            $competencias = $this->competenciaProgramaModel->getCompetenciasDisponibles($programa_id);
        }
        $this->sendResponse($competencias);
    }

    /**
     * Asociar una competencia a un programa
     */
    public function asociarCompetencia()
    {
        try {
            $programa_id = $_POST['programa_id'] ?? null;
            $competencia_id = $_POST['competencia_id'] ?? null;

            if (!$programa_id || !$competencia_id) {
                $this->sendResponse(['error' => 'Se requieren programa_id y competencia_id'], 400);
                return;
            }

            if ($this->competenciaProgramaModel->asociar($programa_id, $competencia_id)) {
                $this->sendResponse(['message' => 'Competencia asociada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo asociar la competencia'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al asociar competencia', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Desasociar una competencia de un programa
     */
    public function desasociarCompetencia()
    {
        try {
            $programa_id = $_POST['programa_id'] ?? null;
            $competencia_id = $_POST['competencia_id'] ?? null;

            if (!$programa_id || !$competencia_id) {
                $this->sendResponse(['error' => 'Se requieren programa_id y competencia_id'], 400);
                return;
            }

            if ($this->competenciaProgramaModel->desasociar($programa_id, $competencia_id)) {
                $this->sendResponse(['message' => 'Competencia desasociada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo desasociar la competencia'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al desasociar competencia', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Asociar múltiples competencias a un programa
     */
    public function asociarCompetencias()
    {
        try {
            $programa_id = $_POST['programa_id'] ?? null;
            $competencias_ids = $_POST['competencias_ids'] ?? [];

            if (!$programa_id) {
                $this->sendResponse(['error' => 'Se requiere programa_id'], 400);
                return;
            }

            // Convertir a array si viene como string JSON
            if (is_string($competencias_ids)) {
                $competencias_ids = json_decode($competencias_ids, true);
            }

            if ($this->competenciaProgramaModel->reemplazarCompetencias($programa_id, $competencias_ids)) {
                $this->sendResponse(['message' => 'Competencias asociadas correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudieron asociar las competencias'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al asociar competencias', 'details' => $e->getMessage()], 500);
        }
    }
}
