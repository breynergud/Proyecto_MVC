<?php
require_once dirname(__DIR__) . '/Conexion.php';
class ProgramaModel
{
    private $prog_codigo;
    private $prog_denominacion;
    private $tit_programa_titpro_id;
    private $prog_tipo;
    private $db;

    public function __construct($prog_codigo = null, $prog_denominacion = null, $tit_programa_titpro_id = null, $prog_tipo = null)
    {
        if ($prog_codigo !== null) $this->setProgCodigo($prog_codigo);
        if ($prog_denominacion !== null) $this->setProgDenominacion($prog_denominacion);
        if ($tit_programa_titpro_id !== null) $this->setTitProgramaTitproId($tit_programa_titpro_id);
        if ($prog_tipo !== null) $this->setProgTipo($prog_tipo);
        $this->db = Conexion::getConnect();
    }

    // Getters
    public function getProgCodigo()
    {
        return $this->prog_codigo;
    }
    public function getProgDenominacion()
    {
        return $this->prog_denominacion;
    }
    public function getTitProgramaTitproId()
    {
        return $this->tit_programa_titpro_id;
    }
    public function getProgTipo()
    {
        return $this->prog_tipo;
    }

    // Setters
    public function setProgCodigo($prog_codigo)
    {
        $this->prog_codigo = $prog_codigo;
    }
    public function setProgDenominacion($prog_denominacion)
    {
        $this->prog_denominacion = $prog_denominacion;
    }
    public function setTitProgramaTitproId($tit_programa_titpro_id)
    {
        $this->tit_programa_titpro_id = $tit_programa_titpro_id;
    }
    public function setProgTipo($prog_tipo)
    {
        $this->prog_tipo = $prog_tipo;
    }

    // CRUD
    public function create()
    {
        // Si prog_codigo está setead, lo usamos (asumiendo que el usuario quiere forzar el ID/Código).
        // Si no, dejamos que SERIAL actúe (aunque el controlador valida que sea requerido).
        if ($this->prog_codigo) {
            $query = "INSERT INTO programa (prog_codigo, prog_denominacion, tit_programa_titpro_id, prog_tipo) 
                      VALUES (:prog_codigo, :prog_denominacion, :tit_programa_titpro_id, :prog_tipo)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':prog_codigo', $this->prog_codigo);
            $stmt->bindParam(':prog_denominacion', $this->prog_denominacion);
            $stmt->bindParam(':tit_programa_titpro_id', $this->tit_programa_titpro_id);
            $stmt->bindParam(':prog_tipo', $this->prog_tipo);
            $stmt->execute();
            return $this->prog_codigo;
        } else {
            $query = "INSERT INTO programa (prog_denominacion, tit_programa_titpro_id, prog_tipo) 
                      VALUES (:prog_denominacion, :tit_programa_titpro_id, :prog_tipo)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':prog_denominacion', $this->prog_denominacion);
            $stmt->bindParam(':tit_programa_titpro_id', $this->tit_programa_titpro_id);
            $stmt->bindParam(':prog_tipo', $this->prog_tipo);
            $stmt->execute();
            return $this->db->lastInsertId();
        }
    }

    public function read()
    {
        $sql = "SELECT p.*, t.titpro_nombre 
                FROM programa p
                INNER JOIN titulo_programa t ON p.tit_programa_titpro_id = t.titpro_id
                WHERE p.prog_codigo = :prog_codigo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':prog_codigo' => $this->prog_codigo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAll()
    {
        $sql = "SELECT p.*, t.titpro_nombre 
                FROM programa p
                INNER JOIN titulo_programa t ON p.tit_programa_titpro_id = t.titpro_id
                ORDER BY p.prog_codigo DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE programa 
                  SET prog_denominacion = :prog_denominacion, 
                      tit_programa_titpro_id = :tit_programa_titpro_id, 
                      prog_tipo = :prog_tipo
                  WHERE prog_codigo = :prog_codigo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':prog_denominacion', $this->prog_denominacion);
        $stmt->bindParam(':tit_programa_titpro_id', $this->tit_programa_titpro_id);
        $stmt->bindParam(':prog_tipo', $this->prog_tipo);
        $stmt->bindParam(':prog_codigo', $this->prog_codigo);
        return $stmt->execute();
    }

    public function delete()
    {
        try {
            $this->db->beginTransaction();

            // Eliminar asociaciones con competencias primero
            $queryComp = "DELETE FROM competxprograma WHERE programa_prog_id = :prog_codigo";
            $stmtComp = $this->db->prepare($queryComp);
            $stmtComp->bindParam(':prog_codigo', $this->prog_codigo);
            $stmtComp->execute();

            // Luego eliminar el programa
            $query = "DELETE FROM programa WHERE prog_codigo = :prog_codigo";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':prog_codigo', $this->prog_codigo);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function exists($codigo)
    {
        $query = "SELECT COUNT(*) as count FROM programa WHERE prog_codigo = :prog_codigo";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':prog_codigo' => $codigo]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollBack()
    {
        return $this->db->rollBack();
    }
}
