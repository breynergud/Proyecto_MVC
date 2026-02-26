<?php
require_once dirname(__DIR__) . '/Conexion.php';

class InstructorModel
{
    private $inst_id;
    private $inst_nombres;
    private $inst_apellidos;
    private $inst_correo;
    private $inst_telefono;
    private $CENTRO_FORMACION_cent_id;
    private $inst_password;

    public function __construct($inst_id = null, $inst_nombres = null, $inst_apellidos = null, $inst_correo = null, $inst_telefono = null, $CENTRO_FORMACION_cent_id = null, $inst_password = null)
    {
        if ($inst_id !== null) $this->setInstId($inst_id);
        if ($inst_nombres !== null) $this->setInstNombres($inst_nombres);
        if ($inst_apellidos !== null) $this->setInstApellidos($inst_apellidos);
        if ($inst_correo !== null) $this->setInstCorreo($inst_correo);
        if ($inst_telefono !== null) $this->setInstTelefono($inst_telefono);
        if ($CENTRO_FORMACION_cent_id !== null) $this->setCentroFormacionCentId($CENTRO_FORMACION_cent_id);
        if ($inst_password !== null) $this->setInstPassword($inst_password);
    }

    // Getters
    public function getInstId() { return $this->inst_id; }
    public function getInstNombres() { return $this->inst_nombres; }
    public function getInstApellidos() { return $this->inst_apellidos; }
    public function getInstCorreo() { return $this->inst_correo; }
    public function getInstTelefono() { return $this->inst_telefono; }
    public function getCentroFormacionCentId() { return $this->CENTRO_FORMACION_cent_id; }
    public function getInstPassword() { return $this->inst_password; }

    // Setters
    public function setInstId($inst_id) { $this->inst_id = $inst_id; }
    public function setInstNombres($inst_nombres) { $this->inst_nombres = $inst_nombres; }
    public function setInstApellidos($inst_apellidos) { $this->inst_apellidos = $inst_apellidos; }
    public function setInstCorreo($inst_correo) { $this->inst_correo = $inst_correo; }
    public function setInstTelefono($inst_telefono) { $this->inst_telefono = $inst_telefono; }
    public function setCentroFormacionCentId($CENTRO_FORMACION_cent_id) { $this->CENTRO_FORMACION_cent_id = $CENTRO_FORMACION_cent_id; }
    public function setInstPassword($inst_password) { $this->inst_password = $inst_password; }

    // CRUD
    public function create()
    {
        $db = Conexion::getConnect();
        // No incluimos inst_id porque es IDENTITY, ni inst_password si está vacío se maneja en el controlador o aquí
        $query = "INSERT INTO instructor (inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id, inst_password) 
                  VALUES (:inst_nombres, :inst_apellidos, :inst_correo, :inst_telefono, :cent_id, :inst_password)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':inst_nombres', $this->inst_nombres);
        $stmt->bindParam(':inst_apellidos', $this->inst_apellidos);
        $stmt->bindParam(':inst_correo', $this->inst_correo);
        $stmt->bindParam(':inst_telefono', $this->inst_telefono);
        $stmt->bindParam(':cent_id', $this->CENTRO_FORMACION_cent_id, PDO::PARAM_INT);
        $stmt->bindParam(':inst_password', $this->inst_password);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public function saveEspecialidades($instructor_id, $programa_id, $competencias_array)
    {
        $db = Conexion::getConnect();
        
        // Asignar todas las competencias recibidas
        if (!empty($competencias_array)) {
            $queryInsert = "INSERT INTO instru_competencia (instructor_inst_id, competxprograma_programa_prog_id, competxprograma_competencia_comp_id, inscomp_vigencia) 
                          VALUES (:inst_id, :prog_id, :comp_id, :vigencia)";
            $stmtInsert = $db->prepare($queryInsert);
            
            // Vigencia por defecto a final de año del año siguiente
            $vigencia = (date('Y') + 1) . '-12-31';

            foreach ($competencias_array as $comp_id) {
                $stmtInsert->bindParam(':inst_id', $instructor_id, PDO::PARAM_INT);
                $stmtInsert->bindParam(':prog_id', $programa_id, PDO::PARAM_INT);
                $stmtInsert->bindParam(':comp_id', $comp_id, PDO::PARAM_INT);
                $stmtInsert->bindParam(':vigencia', $vigencia);
                $stmtInsert->execute();
            }
        }
        return true;
    }

    public function getEspecialidades($instructor_id)
    {
        $db = Conexion::getConnect();
        $query = "SELECT competxprograma_programa_prog_id as programa_id, competxprograma_competencia_comp_id as comp_id 
                  FROM instru_competencia 
                  WHERE instructor_inst_id = :inst_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':inst_id', $instructor_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteEspecialidades($instructor_id)
    {
        $db = Conexion::getConnect();
        $query = "DELETE FROM instru_competencia WHERE instructor_inst_id = :inst_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':inst_id', $instructor_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function read()
    {
        $db = Conexion::getConnect();
        $sql = "SELECT i.*, cf.cent_nombre 
                FROM instructor i 
                LEFT JOIN centro_formacion cf ON i.CENTRO_FORMACION_cent_id = cf.cent_id
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
                    i.CENTRO_FORMACION_cent_id,
                    cf.cent_nombre as centro_nombre
                FROM instructor i
                LEFT JOIN centro_formacion cf ON i.CENTRO_FORMACION_cent_id = cf.cent_id
                ORDER BY i.inst_apellidos, i.inst_nombres";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $db = Conexion::getConnect();
        $query = "UPDATE instructor 
                  SET inst_nombres = :inst_nombres, 
                      inst_apellidos = :inst_apellidos, 
                      inst_correo = :inst_correo, 
                      inst_telefono = :inst_telefono,
                      CENTRO_FORMACION_cent_id = :cent_id
                  WHERE inst_id = :inst_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':inst_nombres', $this->inst_nombres);
        $stmt->bindParam(':inst_apellidos', $this->inst_apellidos);
        $stmt->bindParam(':inst_correo', $this->inst_correo);
        $stmt->bindParam(':inst_telefono', $this->inst_telefono);
        $stmt->bindParam(':cent_id', $this->CENTRO_FORMACION_cent_id, PDO::PARAM_INT);
        $stmt->bindParam(':inst_id', $this->inst_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updatePassword($id, $hashed_password)
    {
        $db = Conexion::getConnect();
        $query = "UPDATE instructor SET inst_password = :inst_password WHERE inst_id = :inst_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':inst_password', $hashed_password);
        $stmt->bindParam(':inst_id', $id, PDO::PARAM_INT);
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
