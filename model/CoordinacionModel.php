<?php
require_once dirname(__DIR__) . '/Conexion.php';

class CoordinacionModel
{
    private $coord_id;
    private $coord_descripcion;
    private $centro_formacion_cent_id;
    private $coord_nombre_coordinador;
    private $coord_correo;
    private $coord_password;
    private $db;

    public function __construct($coord_id = null, $coord_descripcion = null, $centro_formacion_cent_id = null, $coord_nombre_coordinador = null, $coord_correo = null, $coord_password = null)
    {
        $this->coord_id = $coord_id;
        $this->coord_descripcion = $coord_descripcion;
        $this->centro_formacion_cent_id = $centro_formacion_cent_id;
        $this->coord_nombre_coordinador = $coord_nombre_coordinador;
        $this->coord_correo = $coord_correo;
        $this->coord_password = $coord_password;
        $this->db = Conexion::getConnect();
    }

    // Getters y Setters
    public function getCoordId() { return $this->coord_id; }
    public function setCoordId($coord_id) { $this->coord_id = $coord_id; }

    public function getCoordDescripcion() { return $this->coord_descripcion; }
    public function setCoordDescripcion($coord_descripcion) { $this->coord_descripcion = $coord_descripcion; }

    public function getCentroFormacionCentId() { return $this->centro_formacion_cent_id; }
    public function setCentroFormacionCentId($centro_formacion_cent_id) { $this->centro_formacion_cent_id = $centro_formacion_cent_id; }

    public function getCoordNombreCoordinador() { return $this->coord_nombre_coordinador; }
    public function setCoordNombreCoordinador($coord_nombre_coordinador) { $this->coord_nombre_coordinador = $coord_nombre_coordinador; }

    public function getCoordCorreo() { return $this->coord_correo; }
    public function setCoordCorreo($coord_correo) { $this->coord_correo = $coord_correo; }

    public function getCoordPassword() { return $this->coord_password; }
    public function setCoordPassword($coord_password) { $this->coord_password = $coord_password; }

    // CRUD
    public function create()
    {
        $query = "INSERT INTO coordinacion (coord_descripcion, centro_formacion_cent_id, coord_nombre_coordinador, coord_correo, coord_password) 
                  VALUES (:coord_descripcion, :centro_formacion_cent_id, :coord_nombre_coordinador, :coord_correo, :coord_password)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':coord_descripcion', $this->coord_descripcion);
        $stmt->bindParam(':centro_formacion_cent_id', $this->centro_formacion_cent_id);
        $stmt->bindParam(':coord_nombre_coordinador', $this->coord_nombre_coordinador);
        $stmt->bindParam(':coord_correo', $this->coord_correo);
        $stmt->bindParam(':coord_password', $this->coord_password);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function read()
    {
        $query = "SELECT c.*, cf.cent_nombre 
                  FROM coordinacion c
                  LEFT JOIN centro_formacion cf ON c.centro_formacion_cent_id = cf.cent_id
                  WHERE c.coord_id = :coord_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':coord_id', $this->coord_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $query = "SELECT c.*, cf.cent_nombre 
                  FROM coordinacion c
                  LEFT JOIN centro_formacion cf ON c.centro_formacion_cent_id = cf.cent_id
                  ORDER BY c.coord_descripcion ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE coordinacion 
                  SET coord_descripcion = :coord_descripcion, 
                      centro_formacion_cent_id = :centro_formacion_cent_id,
                      coord_nombre_coordinador = :coord_nombre_coordinador,
                      coord_correo = :coord_correo,
                      coord_password = :coord_password
                  WHERE coord_id = :coord_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':coord_descripcion', $this->coord_descripcion);
        $stmt->bindParam(':centro_formacion_cent_id', $this->centro_formacion_cent_id);
        $stmt->bindParam(':coord_nombre_coordinador', $this->coord_nombre_coordinador);
        $stmt->bindParam(':coord_correo', $this->coord_correo);
        $stmt->bindParam(':coord_password', $this->coord_password);
        $stmt->bindParam(':coord_id', $this->coord_id);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM coordinacion WHERE coord_id = :coord_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':coord_id', $this->coord_id);
        return $stmt->execute();
    }
}
