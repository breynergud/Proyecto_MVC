<?php
require_once dirname(__DIR__) . '/Conexion.php';

class InstructorModel
{
    private $inst_id;
    private $inst_nombres;
    private $inst_apellidos;
    private $inst_correo;
    private $inst_telefono;
    private $centro_formacion_cent_id;
    private $inst_password;

    public function __construct($inst_id = null, $inst_nombres = null, $inst_apellidos = null, $inst_correo = null, $inst_telefono = null, $centro_formacion_cent_id = null, $inst_password = null)
    {
        if ($inst_id !== null) $this->setInstId($inst_id);
        if ($inst_nombres !== null) $this->setInstNombres($inst_nombres);
        if ($inst_apellidos !== null) $this->setInstApellidos($inst_apellidos);
        if ($inst_correo !== null) $this->setInstCorreo($inst_correo);
        if ($inst_telefono !== null) $this->setInstTelefono($inst_telefono);
        if ($centro_formacion_cent_id !== null) $this->setCentroFormacionCentId($centro_formacion_cent_id);
        if ($inst_password !== null) $this->setInstPassword($inst_password);
    }

    // Getters
    public function getInstId() { return $this->inst_id; }
    public function getInstNombres() { return $this->inst_nombres; }
    public function getInstApellidos() { return $this->inst_apellidos; }
    public function getInstCorreo() { return $this->inst_correo; }
    public function getInstTelefono() { return $this->inst_telefono; }
    public function getCentroFormacionCentId() { return $this->centro_formacion_cent_id; }
    public function getInstPassword() { return $this->inst_password; }

    // Setters
    public function setInstId($inst_id) { $this->inst_id = $inst_id; }
    public function setInstNombres($inst_nombres) { $this->inst_nombres = $inst_nombres; }
    public function setInstApellidos($inst_apellidos) { $this->inst_apellidos = $inst_apellidos; }
    public function setInstCorreo($inst_correo) { $this->inst_correo = $inst_correo; }
    public function setInstTelefono($inst_telefono) { $this->inst_telefono = $inst_telefono; }
    public function setCentroFormacionCentId($centro_formacion_cent_id) { $this->centro_formacion_cent_id = $centro_formacion_cent_id; }
    public function setInstPassword($inst_password) { $this->inst_password = $inst_password; }

    // CRUD
    public function create()
    {
        $db = Conexion::getConnect();
        $query = "INSERT INTO instructor (inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id, inst_password) 
                  VALUES (:inst_nombres, :inst_apellidos, :inst_correo, :inst_telefono, :centro_formacion_cent_id, :inst_password)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':inst_nombres', $this->inst_nombres);
        $stmt->bindParam(':inst_apellidos', $this->inst_apellidos);
        $stmt->bindParam(':inst_correo', $this->inst_correo);
        $stmt->bindParam(':inst_telefono', $this->inst_telefono);
        $stmt->bindParam(':centro_formacion_cent_id', $this->centro_formacion_cent_id, PDO::PARAM_INT);
        $stmt->bindParam(':inst_password', $this->inst_password);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public function read()
    {
        $db = Conexion::getConnect();
        $sql = "SELECT i.*, cf.cent_nombre 
                FROM instructor i 
                LEFT JOIN centro_formacion cf ON i.centro_formacion_cent_id = cf.cent_id
                WHERE i.inst_id = :inst_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':inst_id', $this->inst_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $db = Conexion::getConnect();
        $sql = "SELECT 
                    i.inst_id,
                    i.inst_nombres,
                    i.inst_apellidos,
                    i.inst_correo,
                    i.inst_telefono,
                    i.centro_formacion_cent_id,
                    cf.cent_nombre as centro_nombre
                FROM instructor i
                LEFT JOIN centro_formacion cf ON i.centro_formacion_cent_id = cf.cent_id
                ORDER BY i.inst_apellidos, i.inst_nombres";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $db = Conexion::getConnect();
        // Nota: Solo actualizamos la contraseña si se proporciona una nueva (lógica de controlador), 
        // pero aquí en el modelo asumimos que si está seteada, la actualizamos.
        // Se puede hacer más flexible, pero por ahora actualizamos todo.
        $query = "UPDATE instructor 
                  SET inst_nombres = :inst_nombres, 
                      inst_apellidos = :inst_apellidos, 
                      inst_correo = :inst_correo, 
                      inst_telefono = :inst_telefono,
                      centro_formacion_cent_id = :centro_formacion_cent_id,
                      inst_password = :inst_password
                  WHERE inst_id = :inst_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':inst_nombres', $this->inst_nombres);
        $stmt->bindParam(':inst_apellidos', $this->inst_apellidos);
        $stmt->bindParam(':inst_correo', $this->inst_correo);
        $stmt->bindParam(':inst_telefono', $this->inst_telefono);
        $stmt->bindParam(':centro_formacion_cent_id', $this->centro_formacion_cent_id, PDO::PARAM_INT);
        $stmt->bindParam(':inst_password', $this->inst_password);
        $stmt->bindParam(':inst_id', $this->inst_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete()
    {
        $db = Conexion::getConnect();
        $query = "DELETE FROM instructor WHERE inst_id = :inst_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':inst_id', $this->inst_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
