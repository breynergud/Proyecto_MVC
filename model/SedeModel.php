<?php
require_once dirname(__DIR__) . '/Conexion.php';
class SedeModel
{
    private $sede_id;
    private $sede_nombre;
    private $db;

    public function __construct($sede_id = null, $sede_nombre = null)
    {
        $this->setSedeId($sede_id);
        $this->setSedeNombre($sede_nombre);
        $this->db = Conexion::getConnect();
    }
    //getters 

    public function getSedeId()
    {
        return $this->sede_id;
    }
    public function getSedeNombre()
    {
        return $this->sede_nombre;
    }

    //setters 
    public function setSedeId($sede_id)
    {
        $this->sede_id = $sede_id;
    }
    public function setSedeNombre($sede_nombre)
    {
        $this->sede_nombre = $sede_nombre;
    }
    //crud
    public function create()
    {
        $query = "INSERT INTO sede (sede_nombre) 
        VALUES (:sede_nombre)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sede_nombre', $this->sede_nombre);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    public function read()
    {
        $sql = "SELECT * FROM sede WHERE sede_id = :sede_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sede_id' => $this->sede_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT * FROM sede ORDER BY sede_nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function update()
    {
        $query = "UPDATE sede SET sede_nombre = :sede_nombre WHERE sede_id = :sede_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sede_nombre', $this->sede_nombre);
        $stmt->bindParam(':sede_id', $this->sede_id);
        $stmt->execute();
        return $stmt;
    }
    public function delete()
    {
        $query = "DELETE FROM sede WHERE sede_id = :sede_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sede_id', $this->sede_id);
        $stmt->execute();
        return $stmt;
    }

    public function getProgramasBySede()
    {
        $sql = "SELECT p.*, t.titpro_nombre 
                FROM programa p
                INNER JOIN titulo_programa t ON p.tit_programa_titpro_id = t.titpro_id
                WHERE p.sede_sede_id = :sede_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sede_id' => $this->sede_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
