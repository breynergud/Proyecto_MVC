<?php
require_once dirname(__DIR__) . '/Conexion.php';

class FichaModel
{
    private $fich_id;
    private $PROGRAMA_prog_id;
    private $INSTRUCTOR_inst_id_lider;
    private $fich_jornada;
    private $COORDINACION_coord_id;
    private $fich_fecha_ini_lectiva;
    private $fich_fecha_fin_lectiva;

    public function __construct($fich_id = null, $PROGRAMA_prog_id = null, $INSTRUCTOR_inst_id_lider = null, $fich_jornada = null, $COORDINACION_coord_id = null, $fich_fecha_ini_lectiva = null, $fich_fecha_fin_lectiva = null)
    {
        if ($fich_id !== null) $this->setFichId($fich_id);
        if ($PROGRAMA_prog_id !== null) $this->setProgramaProgId($PROGRAMA_prog_id);
        if ($INSTRUCTOR_inst_id_lider !== null) $this->setInstructorInstIdLider($INSTRUCTOR_inst_id_lider);
        if ($fich_jornada !== null) $this->setFichJornada($fich_jornada);
        if ($COORDINACION_coord_id !== null) $this->setCoordinacionCoordId($COORDINACION_coord_id);
        if ($fich_fecha_ini_lectiva !== null) $this->setFichFechaIniLectiva($fich_fecha_ini_lectiva);
        if ($fich_fecha_fin_lectiva !== null) $this->setFichFechaFinLectiva($fich_fecha_fin_lectiva);
    }

    // Getters
    public function getFichId() { return $this->fich_id; }
    public function getProgramaProgId() { return $this->PROGRAMA_prog_id; }
    public function getInstructorInstIdLider() { return $this->INSTRUCTOR_inst_id_lider; }
    public function getFichJornada() { return $this->fich_jornada; }
    public function getCoordinacionCoordId() { return $this->COORDINACION_coord_id; }
    public function getFichFechaIniLectiva() { return $this->fich_fecha_ini_lectiva; }
    public function getFichFechaFinLectiva() { return $this->fich_fecha_fin_lectiva; }

    // Setters
    public function setFichId($fich_id) { $this->fich_id = $fich_id; }
    public function setProgramaProgId($PROGRAMA_prog_id) { $this->PROGRAMA_prog_id = $PROGRAMA_prog_id; }
    public function setInstructorInstIdLider($INSTRUCTOR_inst_id_lider) { $this->INSTRUCTOR_inst_id_lider = $INSTRUCTOR_inst_id_lider; }
    public function setFichJornada($fich_jornada) { $this->fich_jornada = $fich_jornada; }
    public function setCoordinacionCoordId($COORDINACION_coord_id) { $this->COORDINACION_coord_id = $COORDINACION_coord_id; }
    public function setFichFechaIniLectiva($fich_fecha_ini_lectiva) { $this->fich_fecha_ini_lectiva = $fich_fecha_ini_lectiva; }
    public function setFichFechaFinLectiva($fich_fecha_fin_lectiva) { $this->fich_fecha_fin_lectiva = $fich_fecha_fin_lectiva; }

    // CRUD
    public function create()
    {
        $db = Conexion::getConnect();
        
        // Manejar fechas vacías como nulas
        $fecha_ini = empty($this->fich_fecha_ini_lectiva) ? null : $this->fich_fecha_ini_lectiva;
        $fecha_fin = empty($this->fich_fecha_fin_lectiva) ? null : $this->fich_fecha_fin_lectiva;

        $query = "INSERT INTO ficha (fich_id, PROGRAMA_prog_id, INSTRUCTOR_inst_id_lider, fich_jornada, COORDINACION_coord_id, fich_fecha_ini_lectiva, fich_fecha_fin_lectiva) 
                  VALUES (:fich_id, :programa_prog_id, :instructor_inst_id_lider, :fich_jornada, :coordinacion_coord_id, :fich_fecha_ini_lectiva, :fich_fecha_fin_lectiva)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':fich_id', $this->fich_id, PDO::PARAM_INT);
        $stmt->bindParam(':programa_prog_id', $this->PROGRAMA_prog_id, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_inst_id_lider', $this->INSTRUCTOR_inst_id_lider, PDO::PARAM_INT);
        $stmt->bindParam(':fich_jornada', $this->fich_jornada);
        $stmt->bindParam(':coordinacion_coord_id', $this->COORDINACION_coord_id, PDO::PARAM_INT);
        
        // Vincular fechas, permitiendo nulos
        $stmt->bindValue(':fich_fecha_ini_lectiva', $fecha_ini, $fecha_ini === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':fich_fecha_fin_lectiva', $fecha_fin, $fecha_fin === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        
        $stmt->execute();
        return $this->fich_id; 
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
                    f.PROGRAMA_prog_id,
                    f.INSTRUCTOR_inst_id_lider,
                    f.COORDINACION_coord_id,
                    f.fich_fecha_ini_lectiva,
                    f.fich_fecha_fin_lectiva,
                    p.prog_denominacion,
                    p.prog_codigo,
                    CONCAT(i.inst_nombres, ' ', i.inst_apellidos) as instructor_nombre,
                    c.coord_nombre_coordinador as coord_nombre 
                FROM ficha f
                INNER JOIN programa p ON f.PROGRAMA_prog_id = p.prog_codigo 
                INNER JOIN instructor i ON f.INSTRUCTOR_inst_id_lider = i.inst_id
                INNER JOIN coordinacion c ON f.COORDINACION_coord_id = c.coord_id
                ORDER BY f.fich_id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $db = Conexion::getConnect();
        
        $fecha_ini = empty($this->fich_fecha_ini_lectiva) ? null : $this->fich_fecha_ini_lectiva;
        $fecha_fin = empty($this->fich_fecha_fin_lectiva) ? null : $this->fich_fecha_fin_lectiva;

        $query = "UPDATE ficha 
                  SET PROGRAMA_prog_id = :programa_prog_id, 
                      INSTRUCTOR_inst_id_lider = :instructor_inst_id_lider, 
                      fich_jornada = :fich_jornada,
                      COORDINACION_coord_id = :coordinacion_coord_id,
                      fich_fecha_ini_lectiva = :fich_fecha_ini_lectiva,
                      fich_fecha_fin_lectiva = :fich_fecha_fin_lectiva
                  WHERE fich_id = :fich_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':programa_prog_id', $this->PROGRAMA_prog_id, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_inst_id_lider', $this->INSTRUCTOR_inst_id_lider, PDO::PARAM_INT);
        $stmt->bindParam(':fich_jornada', $this->fich_jornada);
        $stmt->bindParam(':coordinacion_coord_id', $this->COORDINACION_coord_id, PDO::PARAM_INT);
        
        $stmt->bindValue(':fich_fecha_ini_lectiva', $fecha_ini, $fecha_ini === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':fich_fecha_fin_lectiva', $fecha_fin, $fecha_fin === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        
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
