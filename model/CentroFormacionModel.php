<?php
require_once dirname(__DIR__) . '/Conexion.php';

class CentroFormacionModel
{
    private $cent_id;
    private $cent_nombre;
    private $cent_correo;
    private $cent_password;
    private $db;

    public function __construct($cent_id = null, $cent_nombre = null, $cent_correo = null, $cent_password = null)
    {
        $this->cent_id = $cent_id;
        $this->cent_nombre = $cent_nombre;
        $this->cent_correo = $cent_correo;
        $this->cent_password = $cent_password;
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getCentId()
    {
        return $this->cent_id;
    }

    public function getCentNombre()
    {
        return $this->cent_nombre;
    }

    public function getCentCorreo()
    {
        return $this->cent_correo;
    }

    public function getCentPassword()
    {
        return $this->cent_password;
    }

    // Setters
    public function setCentId($cent_id)
    {
        $this->cent_id = $cent_id;
    }

    public function setCentNombre($cent_nombre)
    {
        $this->cent_nombre = $cent_nombre;
    }

    public function setCentCorreo($cent_correo)
    {
        $this->cent_correo = $cent_correo;
    }

    public function setCentPassword($cent_password)
    {
        $this->cent_password = $cent_password;
    }

    // CRUD
    public function create()
    {
        $query = "INSERT INTO centro_formacion (cent_nombre, cent_correo, cent_password) VALUES (:cent_nombre, :cent_correo, :cent_password)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cent_nombre', $this->cent_nombre);
        $stmt->bindParam(':cent_correo', $this->cent_correo);
        
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $passwordToUse = $this->cent_password ? $this->cent_password : $defaultPassword;
        $stmt->bindParam(':cent_password', $passwordToUse);
        
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function read()
    {
        $query = "SELECT * FROM centro_formacion WHERE cent_id = :cent_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cent_id', $this->cent_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $query = "SELECT * FROM centro_formacion ORDER BY cent_nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE centro_formacion SET cent_nombre = :cent_nombre, cent_correo = :cent_correo WHERE cent_id = :cent_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cent_nombre', $this->cent_nombre);
        $stmt->bindParam(':cent_correo', $this->cent_correo);
        $stmt->bindParam(':cent_id', $this->cent_id);
        return $stmt->execute();
    }

    public function updatePassword($id, $hashed_password)
    {
        $query = "UPDATE centro_formacion SET cent_password = :cent_password WHERE cent_id = :cent_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cent_password', $hashed_password);
        $stmt->bindParam(':cent_id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM centro_formacion WHERE cent_id = :cent_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cent_id', $this->cent_id);
        return $stmt->execute();
    }
}
