<?php
require_once dirname(__DIR__) . '/Conexion.php';

/**
 * CompetenciaProgramaModel - Maneja la relación muchos a muchos entre Competencia y Programa
 */
class CompetenciaProgramaModel
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::getConnect();
    }

    /**
     * Asociar una competencia a un programa
     */
    public function asociar($programa_id, $competencia_id)
    {
        $query = "INSERT INTO competxprograma (programa_prog_id, competencia_comp_id) 
                  VALUES (:programa_id, :competencia_id)
                  ON CONFLICT DO NOTHING";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':programa_id', $programa_id, PDO::PARAM_INT);
        $stmt->bindParam(':competencia_id', $competencia_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Desasociar una competencia de un programa
     */
    public function desasociar($programa_id, $competencia_id)
    {
        $query = "DELETE FROM competxprograma 
                  WHERE programa_prog_id = :programa_id 
                  AND competencia_comp_id = :competencia_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':programa_id', $programa_id, PDO::PARAM_INT);
        $stmt->bindParam(':competencia_id', $competencia_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtener todas las competencias de un programa
     */
    public function getCompetenciasByPrograma($programa_id)
    {
        $query = "SELECT c.* 
                  FROM competencia c
                  INNER JOIN competxprograma cp ON c.comp_id = cp.competencia_comp_id
                  WHERE cp.programa_prog_id = :programa_id
                  ORDER BY c.comp_nombre_corto";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':programa_id', $programa_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los programas de una competencia
     */
    public function getProgramasByCompetencia($competencia_id)
    {
        $query = "SELECT p.*, t.titpro_nombre 
                  FROM programa p
                  INNER JOIN competxprograma cp ON p.prog_codigo = cp.programa_prog_id
                  INNER JOIN titulo_programa t ON p.tit_programa_titpro_id = t.titpro_id
                  WHERE cp.competencia_comp_id = :competencia_id
                  ORDER BY p.prog_denominacion";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':competencia_id', $competencia_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener competencias NO asociadas a un programa
     */
    public function getCompetenciasDisponibles($programa_id)
    {
        $query = "SELECT c.* 
                  FROM competencia c
                  WHERE c.comp_id NOT IN (
                      SELECT competencia_comp_id 
                      FROM competxprograma 
                      WHERE programa_prog_id = :programa_id
                  )
                  ORDER BY c.comp_nombre_corto";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':programa_id', $programa_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si una competencia está asociada a un programa
     */
    public function estaAsociada($programa_id, $competencia_id)
    {
        $query = "SELECT COUNT(*) as count 
                  FROM competxprograma 
                  WHERE programa_prog_id = :programa_id 
                  AND competencia_comp_id = :competencia_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':programa_id', $programa_id, PDO::PARAM_INT);
        $stmt->bindParam(':competencia_id', $competencia_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Asociar múltiples competencias a un programa
     */
    public function asociarMultiples($programa_id, $competencias_ids)
    {
        if (empty($competencias_ids)) {
            return true;
        }

        $this->db->beginTransaction();
        
        try {
            foreach ($competencias_ids as $competencia_id) {
                $this->asociar($programa_id, $competencia_id);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Reemplazar todas las competencias de un programa
     */
    public function reemplazarCompetencias($programa_id, $competencias_ids)
    {
        $this->db->beginTransaction();
        
        try {
            // Eliminar todas las asociaciones actuales
            $query = "DELETE FROM competxprograma WHERE programa_prog_id = :programa_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':programa_id', $programa_id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Asociar las nuevas competencias
            if (!empty($competencias_ids)) {
                foreach ($competencias_ids as $competencia_id) {
                    $this->asociar($programa_id, $competencia_id);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
