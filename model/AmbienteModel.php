<?php
require_once dirname(__DIR__) . '/Conexion.php';
class AmbienteModel
{
    private $amb_id;
    private $amb_nombre;
    private $tipo_ambiente;
    private $SEDE_sede_id;

    private $db;

    public function __construct($amb_id = null, $amb_nombre = null, $SEDE_sede_id = null, $tipo_ambiente = 'Convencional')
    {
        if ($amb_id !== null) $this->setAmbId($amb_id);
        if ($amb_nombre !== null) $this->setAmbnombre($amb_nombre);
        if ($SEDE_sede_id !== null) $this->setSedeSedeId($SEDE_sede_id);
        $this->tipo_ambiente = $tipo_ambiente;
        $this->db = Conexion::getConnect();
    }
    //getters 

    public function getAmbId()
    {
        return $this->amb_id;
    }
    public function getAmbnombre()
    {
        return $this->amb_nombre;
    }
    public function getTipoAmbiente()
    {
        return $this->tipo_ambiente;
    }
    public function getSedeSedeId()
    {
        return $this->SEDE_sede_id;
    }

    //setters 
    public function setAmbId($amb_id)
    {
        $this->amb_id = $amb_id;
    }
    public function setAmbnombre($amb_nombre)
    {
        $this->amb_nombre = $amb_nombre;
    }
    public function setTipoAmbiente($tipo_ambiente)
    {
        $this->tipo_ambiente = $tipo_ambiente;
    }
    public function setSedeSedeId($SEDE_sede_id)
    {
        $this->SEDE_sede_id = $SEDE_sede_id;
    }
    //crud
    public function create()
    {
        $query = "INSERT INTO ambiente (amb_id, amb_nombre, tipo_ambiente, SEDE_sede_id) VALUES (:amb_id, :amb_nombre, :tipo_ambiente, :sede)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':amb_id', $this->amb_id);
        $stmt->bindParam(':amb_nombre', $this->amb_nombre);
        $stmt->bindParam(':tipo_ambiente', $this->tipo_ambiente);
        $stmt->bindParam(':sede', $this->SEDE_sede_id);
        $stmt->execute();
        return $this->amb_id;
    }
    public function read()
    {
        $sql = "SELECT a.*, s.sede_nombre 
                FROM ambiente a 
                INNER JOIN sede s ON a.SEDE_sede_id = s.sede_id 
                WHERE a.SEDE_sede_id = :sede";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sede' => $this->SEDE_sede_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT a.*, s.sede_nombre 
                FROM ambiente a 
                INNER JOIN sede s ON a.SEDE_sede_id = s.sede_id
                ORDER BY a.amb_nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readById($id)
    {
        $sql = "SELECT a.*, s.sede_nombre 
                FROM ambiente a 
                INNER JOIN sede s ON a.SEDE_sede_id = s.sede_id 
                WHERE a.amb_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function update()
    {
        $query = "UPDATE ambiente SET amb_nombre = :amb_nombre, tipo_ambiente = :tipo_ambiente, SEDE_sede_id = :sede WHERE amb_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':amb_nombre', $this->amb_nombre);
        $stmt->bindParam(':tipo_ambiente', $this->tipo_ambiente);
        $stmt->bindParam(':sede', $this->SEDE_sede_id);
        $stmt->bindParam(':id', $this->amb_id);
        $stmt->execute();
        return $stmt;
    }
    public function delete()
    {
        $query = "DELETE FROM ambiente WHERE amb_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->amb_id);
        $stmt->execute();
        return $stmt;
    }

    public function exists($id)
    {
        $query = "SELECT COUNT(*) as count FROM ambiente WHERE amb_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
