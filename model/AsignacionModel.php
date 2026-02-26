<?php
require_once dirname(__DIR__) . '/Conexion.php';
class AsignacionModel
{
    private $asig_id;
    private $INSTRUCTOR_inst_id;
    private $asig_fecha_ini;
    private $asig_fecha_fin;
    private $FICHA_fich_id;
    private $AMBIENTE_amb_id;
    private $COMPETENCIA_comp_id;
    private $db;

    public function __construct($asig_id = null, $INSTRUCTOR_inst_id = null, $asig_fecha_ini = null, $asig_fecha_fin = null, $FICHA_fich_id = null, $AMBIENTE_amb_id = null, $COMPETENCIA_comp_id = null)
    {
        if ($asig_id !== null) $this->setAsigId($asig_id);
        if ($INSTRUCTOR_inst_id !== null) $this->setInstructorInstId($INSTRUCTOR_inst_id);
        if ($asig_fecha_ini !== null) $this->setAsigFechaIni($asig_fecha_ini);
        if ($asig_fecha_fin !== null) $this->setAsigFechaFin($asig_fecha_fin);
        if ($FICHA_fich_id !== null) $this->setFichaFichId($FICHA_fich_id);
        if ($AMBIENTE_amb_id !== null) $this->setAmbienteAmbId($AMBIENTE_amb_id);
        if ($COMPETENCIA_comp_id !== null) $this->setCompetenciaCompId($COMPETENCIA_comp_id);
        $this->db = Conexion::getConnect();
    }
    //getters 

    public function getAsigId() { return $this->asig_id; }
    public function getInstructorInstId() { return $this->INSTRUCTOR_inst_id; }
    public function getAsigFechaIni() { return $this->asig_fecha_ini; }
    public function getAsigFechaFin() { return $this->asig_fecha_fin; }
    public function getFichaFichId() { return $this->FICHA_fich_id; }
    public function getAmbienteAmbId() { return $this->AMBIENTE_amb_id; }
    public function getCompetenciaCompId() { return $this->COMPETENCIA_comp_id; }

    //setters 
    public function setAsigId($asig_id) { $this->asig_id = $asig_id; }
    public function setInstructorInstId($INSTRUCTOR_inst_id) { $this->INSTRUCTOR_inst_id = $INSTRUCTOR_inst_id; }
    public function setAsigFechaIni($asig_fecha_ini) { $this->asig_fecha_ini = $asig_fecha_ini; }
    public function setAsigFechaFin($asig_fecha_fin) { $this->asig_fecha_fin = $asig_fecha_fin; }
    public function setFichaFichId($FICHA_fich_id) { $this->FICHA_fich_id = $FICHA_fich_id; }
    public function setAmbienteAmbId($AMBIENTE_amb_id) { $this->AMBIENTE_amb_id = $AMBIENTE_amb_id; }
    public function setCompetenciaCompId($COMPETENCIA_comp_id) { $this->COMPETENCIA_comp_id = $COMPETENCIA_comp_id; }

    //crud
    public function create()
    {
        $query = "INSERT INTO asignacion (INSTRUCTOR_inst_id, asig_fecha_ini, asig_fecha_fin, FICHA_fich_id, AMBIENTE_amb_id, COMPETENCIA_comp_id) 
        VALUES (:instructor_id, :asig_fecha_ini, :asig_fecha_fin, :ficha_id, :ambiente_id, :competencia_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':instructor_id', $this->INSTRUCTOR_inst_id);
        $stmt->bindParam(':asig_fecha_ini', $this->asig_fecha_ini);
        $stmt->bindParam(':asig_fecha_fin', $this->asig_fecha_fin);
        $stmt->bindParam(':ficha_id', $this->FICHA_fich_id);
        $stmt->bindParam(':ambiente_id', $this->AMBIENTE_amb_id);
        $stmt->bindParam(':competencia_id', $this->COMPETENCIA_comp_id);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    public function read()
    {
        $sql = "SELECT * FROM asignacion WHERE INSTRUCTOR_inst_id = :instructor_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':instructor_id' => $this->INSTRUCTOR_inst_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT * FROM asignacion";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function update()
    {
        $query = "UPDATE asignacion SET INSTRUCTOR_inst_id = :instructor_id, asig_fecha_ini = :asig_fecha_ini, asig_fecha_fin = :asig_fecha_fin, FICHA_fich_id = :ficha_id, AMBIENTE_amb_id = :ambiente_id, COMPETENCIA_comp_id = :competencia_id WHERE ASIG_ID = :asig_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':instructor_id', $this->INSTRUCTOR_inst_id);
        $stmt->bindParam(':asig_fecha_ini', $this->asig_fecha_ini);
        $stmt->bindParam(':asig_fecha_fin', $this->asig_fecha_fin);
        $stmt->bindParam(':ficha_id', $this->FICHA_fich_id);
        $stmt->bindParam(':ambiente_id', $this->AMBIENTE_amb_id);
        $stmt->bindParam(':competencia_id', $this->COMPETENCIA_comp_id);
        $stmt->bindParam(':asig_id', $this->asig_id);
        $stmt->execute();
        return $stmt;
    }
    public function delete()
    {
        $query = "DELETE FROM asignacion WHERE ASIG_ID = :asig_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':asig_id', $this->asig_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Verifica si hay cruces de horarios para el instructor, ambiente o ficha
     */
    public function checkCrucesHorarios($fecha_inicio, $fecha_fin, $hora_inicio, $hora_fin, $instructor_id, $ambiente_id, $ficha_id)
    {
        $query = "
            SELECT asig.ASIG_ID 
            FROM asignacion asig
            INNER JOIN detallexasignacion det ON asig.ASIG_ID = det.ASIGNACION_ASIG_ID
            WHERE 
                (asig.asig_fecha_ini <= :fecha_fin AND asig.asig_fecha_fin >= :fecha_ini)
                AND (det.detasig_hora_ini < :hora_fin AND det.detasig_hora_fin > :hora_ini)
                AND (
                    asig.INSTRUCTOR_inst_id = :instructor_id OR 
                    asig.AMBIENTE_amb_id = :ambiente_id OR 
                    asig.FICHA_fich_id = :ficha_id
                )
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':fecha_ini' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin,
            ':hora_ini' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':instructor_id' => $instructor_id,
            ':ambiente_id' => $ambiente_id,
            ':ficha_id' => $ficha_id
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta cuántas asignaciones tiene una ficha en una fecha específica
     */
    public function countAsignacionesFichaPorDia($ficha_id, $fecha_inicio, $fecha_fin)
    {
        $query = "
            SELECT COUNT(ASIG_ID) as total 
            FROM asignacion 
            WHERE FICHA_fich_id = :ficha_id 
              AND (asig_fecha_ini <= :fecha_fin AND asig_fecha_fin >= :fecha_ini)
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':ficha_id' => $ficha_id,
            ':fecha_ini' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }
}
