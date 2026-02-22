<?php
require_once dirname(__DIR__) . '/Conexion.php';

class FichaModel
{
    private $fich_id;
    private $programa_prog_id;
    private $instructor_inst_id_lider;
    private $fich_jornada;
    private $coordinacion_coord_id;
    private $fich_fecha_ini_lectiva;
    private $fich_fecha_fin_lectiva;

    public function __construct($fich_id = null, $programa_prog_id = null, $instructor_inst_id_lider = null, $fich_jornada = null, $coordinacion_coord_id = null, $fich_fecha_ini_lectiva = null, $fich_fecha_fin_lectiva = null)
    {
        if ($fich_id !== null) $this->setFichId($fich_id);
        if ($programa_prog_id !== null) $this->setProgramaProgId($programa_prog_id);
        if ($instructor_inst_id_lider !== null) $this->setInstructorInstIdLider($instructor_inst_id_lider);
        if ($fich_jornada !== null) $this->setFichJornada($fich_jornada);
        if ($coordinacion_coord_id !== null) $this->setCoordinacionCoordId($coordinacion_coord_id);
        if ($fich_fecha_ini_lectiva !== null) $this->setFichFechaIniLectiva($fich_fecha_ini_lectiva);
        if ($fich_fecha_fin_lectiva !== null) $this->setFichFechaFinLectiva($fich_fecha_fin_lectiva);
    }

    // Getters
    public function getFichId() { return $this->fich_id; }
    public function getProgramaProgId() { return $this->programa_prog_id; }
    public function getInstructorInstIdLider() { return $this->instructor_inst_id_lider; }
    public function getFichJornada() { return $this->fich_jornada; }
    public function getCoordinacionCoordId() { return $this->coordinacion_coord_id; }
    public function getFichFechaIniLectiva() { return $this->fich_fecha_ini_lectiva; }
    public function getFichFechaFinLectiva() { return $this->fich_fecha_fin_lectiva; }

    // Setters
    public function setFichId($fich_id) { $this->fich_id = $fich_id; }
    public function setProgramaProgId($programa_prog_id) { $this->programa_prog_id = $programa_prog_id; }
    public function setInstructorInstIdLider($instructor_inst_id_lider) { $this->instructor_inst_id_lider = $instructor_inst_id_lider; }
    public function setFichJornada($fich_jornada) { $this->fich_jornada = $fich_jornada; }
    public function setCoordinacionCoordId($coordinacion_coord_id) { $this->coordinacion_coord_id = $coordinacion_coord_id; }
    public function setFichFechaIniLectiva($fich_fecha_ini_lectiva) { $this->fich_fecha_ini_lectiva = $fich_fecha_ini_lectiva; }
    public function setFichFechaFinLectiva($fich_fecha_fin_lectiva) { $this->fich_fecha_fin_lectiva = $fich_fecha_fin_lectiva; }

    // CRUD
    public function create()
    {
        $db = Conexion::getConnect();
        $query = "INSERT INTO ficha (programa_prog_id, instructor_inst_id_lider, fich_jornada, coordinacion_coord_id, fich_fecha_ini_lectiva, fich_fecha_fin_lectiva) 
                  VALUES (:programa_prog_id, :instructor_inst_id_lider, :fich_jornada, :coordinacion_coord_id, :fich_fecha_ini_lectiva, :fich_fecha_fin_lectiva)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':programa_prog_id', $this->programa_prog_id, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_inst_id_lider', $this->instructor_inst_id_lider, PDO::PARAM_INT);
        $stmt->bindParam(':fich_jornada', $this->fich_jornada);
        $stmt->bindParam(':coordinacion_coord_id', $this->coordinacion_coord_id, PDO::PARAM_INT);
        $stmt->bindParam(':fich_fecha_ini_lectiva', $this->fich_fecha_ini_lectiva);
        $stmt->bindParam(':fich_fecha_fin_lectiva', $this->fich_fecha_fin_lectiva);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public function read()
    {
        $db = Conexion::getConnect();
        $sql = "SELECT * FROM ficha WHERE fich_id = :fich_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':fich_id', $this->fich_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $db = Conexion::getConnect();
        $sql = "SELECT 
                    f.fich_id,
                    f.fich_jornada,
                    f.programa_prog_id,
                    f.instructor_inst_id_lider,
                    f.coordinacion_coord_id,
                    f.fich_fecha_ini_lectiva,
                    f.fich_fecha_fin_lectiva,
                    p.prog_denominacion,
                    p.prog_codigo,
                    CONCAT(i.inst_nombres, ' ', i.inst_apellidos) as instructor_nombre,
                    c.coord_nombre_coordinador as coord_nombre -- Ajustado a coord_nombre_coordinador
                FROM ficha f
                INNER JOIN programa p ON f.programa_prog_id = p.prog_codigo -- Ajustado FK
                INNER JOIN instructor i ON f.instructor_inst_id_lider = i.inst_id
                INNER JOIN coordinacion c ON f.coordinacion_coord_id = c.coord_id
                ORDER BY f.fich_id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $db = Conexion::getConnect();
        $query = "UPDATE ficha 
                  SET programa_prog_id = :programa_prog_id, 
                      instructor_inst_id_lider = :instructor_inst_id_lider, 
                      fich_jornada = :fich_jornada,
                      coordinacion_coord_id = :coordinacion_coord_id,
                      fich_fecha_ini_lectiva = :fich_fecha_ini_lectiva,
                      fich_fecha_fin_lectiva = :fich_fecha_fin_lectiva
                  WHERE fich_id = :fich_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':programa_prog_id', $this->programa_prog_id, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_inst_id_lider', $this->instructor_inst_id_lider, PDO::PARAM_INT);
        $stmt->bindParam(':fich_jornada', $this->fich_jornada);
        $stmt->bindParam(':coordinacion_coord_id', $this->coordinacion_coord_id, PDO::PARAM_INT);
        $stmt->bindParam(':fich_fecha_ini_lectiva', $this->fich_fecha_ini_lectiva);
        $stmt->bindParam(':fich_fecha_fin_lectiva', $this->fich_fecha_fin_lectiva);
        $stmt->bindParam(':fich_id', $this->fich_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete()
    {
        $db = Conexion::getConnect();
        $query = "DELETE FROM ficha WHERE fich_id = :fich_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':fich_id', $this->fich_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
