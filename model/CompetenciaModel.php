<?php
require_once dirname(__DIR__) . '/Conexion.php';

class CompetenciaModel
{
    private $comp_id;
    private $comp_nombre_corto;
    private $comp_horas;
    private $comp_nombre_unidad_competencia;
    private $db;

    public function __construct($comp_id = null, $comp_nombre_corto = null, $comp_horas = null, $comp_nombre_unidad_competencia = null)
    {
        if ($comp_id !== null) $this->setCompId($comp_id);
        if ($comp_nombre_corto !== null) $this->setCompNombreCorto($comp_nombre_corto);
        if ($comp_horas !== null) $this->setCompHoras($comp_horas);
        if ($comp_nombre_unidad_competencia !== null) $this->setCompNombreUnidadCompetencia($comp_nombre_unidad_competencia);
        $this->db = Conexion::getConnect();
    }
    //getters 

    public function getCompId()
    {
        return $this->comp_id;
    }
    public function getCompNombreCorto()
    {
        return $this->comp_nombre_corto;
    }
    public function getCompHoras()
    {
        return $this->comp_horas;
    }
    public function getCompNombreUnidadCompetencia()
    {
        return $this->comp_nombre_unidad_competencia;
    }

    //setters 
    public function setCompId($comp_id)
    {
        $this->comp_id = $comp_id;
    }
    public function setCompNombreCorto($comp_nombre_corto)
    {
        $this->comp_nombre_corto = $comp_nombre_corto;
    }
    public function setCompHoras($comp_horas)
    {
        $this->comp_horas = $comp_horas;
    }
    public function setCompNombreUnidadCompetencia($comp_nombre_unidad_competencia)
    {
        $this->comp_nombre_unidad_competencia = $comp_nombre_unidad_competencia;
    }
    //crud
    public function create()
    {
        $query = "INSERT INTO competencia (comp_nombre_corto, comp_horas, comp_nombre_unidad_competencia) 
        VALUES (:comp_nombre_corto, :comp_horas, :comp_nombre_unidad_competencia)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':comp_nombre_corto', $this->comp_nombre_corto);
        $stmt->bindParam(':comp_horas', $this->comp_horas);
        $stmt->bindParam(':comp_nombre_unidad_competencia', $this->comp_nombre_unidad_competencia);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    public function read()
    {
        $sql = "SELECT * FROM competencia WHERE comp_id = :comp_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':comp_id' => $this->comp_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT * FROM competencia";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function update()
    {
        $query = "UPDATE competencia SET comp_nombre_corto = :comp_nombre_corto, comp_horas = :comp_horas, comp_nombre_unidad_competencia = :comp_nombre_unidad_competencia WHERE comp_id = :comp_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':comp_nombre_corto', $this->comp_nombre_corto);
        $stmt->bindParam(':comp_horas', $this->comp_horas);
        $stmt->bindParam(':comp_nombre_unidad_competencia', $this->comp_nombre_unidad_competencia);
        $stmt->bindParam(':comp_id', $this->comp_id);
        $stmt->execute();
        return $stmt;
    }
    public function delete()
    {
        $query = "DELETE FROM competencia WHERE comp_id = :comp_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':comp_id', $this->comp_id);
        $stmt->execute();
        return $stmt;
    }
}
