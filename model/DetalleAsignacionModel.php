<?php
require_once dirname(__DIR__) . '/Conexion.php';
class DetalleAsignacionModel
{
    private $ASIGNACION_ASIG_ID;
    private $detasig_hora_ini;
    private $detasig_hora_fin;
    private $detasig_id;
    private $db;

    public function __construct($ASIGNACION_ASIG_ID = null, $detasig_hora_ini = null, $detasig_hora_fin = null, $detasig_id = null)
    {
        if ($ASIGNACION_ASIG_ID !== null) $this->setAsignacionAsigId($ASIGNACION_ASIG_ID);
        if ($detasig_hora_ini !== null) $this->setDetasigHoraIni($detasig_hora_ini);
        if ($detasig_hora_fin !== null) $this->setDetasigHoraFin($detasig_hora_fin);
        if ($detasig_id !== null) $this->setDetasigId($detasig_id);
        $this->db = Conexion::getConnect();
    }
    //getters 

    public function getAsignacionAsigId() { return $this->ASIGNACION_ASIG_ID; }
    public function getDetasigHoraIni() { return $this->detasig_hora_ini; }
    public function getDetasigHoraFin() { return $this->detasig_hora_fin; }
    public function getDetasigId() { return $this->detasig_id; }

    //setters 
    public function setAsignacionAsigId($ASIGNACION_ASIG_ID) { $this->ASIGNACION_ASIG_ID = $ASIGNACION_ASIG_ID; }
    public function setDetasigHoraIni($detasig_hora_ini) { $this->detasig_hora_ini = $detasig_hora_ini; }
    public function setDetasigHoraFin($detasig_hora_fin) { $this->detasig_hora_fin = $detasig_hora_fin; }
    public function setDetasigId($detasig_id) { $this->detasig_id = $detasig_id; }

    //crud
    public function create()
    {
        $query = "INSERT INTO detallexasignacion (ASIGNACION_ASIG_ID, detasig_hora_ini, detasig_hora_fin) 
        VALUES (:asignacion_id, :detasig_hora_ini, :detasig_hora_fin)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':asignacion_id', $this->ASIGNACION_ASIG_ID);
        $stmt->bindParam(':detasig_hora_ini', $this->detasig_hora_ini);
        $stmt->bindParam(':detasig_hora_fin', $this->detasig_hora_fin);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    public function read()
    {
        $sql = "SELECT * FROM detallexasignacion WHERE ASIGNACION_ASIG_ID = :asignacion_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':asignacion_id' => $this->ASIGNACION_ASIG_ID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT * FROM detallexasignacion";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function update()
    {
        $query = "UPDATE detallexasignacion SET ASIGNACION_ASIG_ID = :asignacion_id, detasig_hora_ini = :detasig_hora_ini, detasig_hora_fin = :detasig_hora_fin WHERE detasig_id = :detasig_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':asignacion_id', $this->ASIGNACION_ASIG_ID);
        $stmt->bindParam(':detasig_hora_ini', $this->detasig_hora_ini);
        $stmt->bindParam(':detasig_hora_fin', $this->detasig_hora_fin);
        $stmt->bindParam(':detasig_id', $this->detasig_id);
        $stmt->execute();
        return $stmt;
    }
    public function delete()
    {
        $query = "DELETE FROM detallexasignacion WHERE detasig_id = :detasig_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':detasig_id', $this->detasig_id);
        $stmt->execute();
        return $stmt;
    }
}
