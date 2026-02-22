-- ===============================
-- CREAR TABLAS FALTANTES
-- ===============================

-- Verificar si existe centro_formacion
CREATE TABLE IF NOT EXISTS centro_formacion (
    cent_id SERIAL PRIMARY KEY,
    cent_nombre VARCHAR(100) NOT NULL
);

-- Verificar si existe coordinacion
CREATE TABLE IF NOT EXISTS coordinacion (
    coord_id SERIAL PRIMARY KEY,
    coord_nombre VARCHAR(45) NOT NULL,
    centro_formacion_cent_id INT NOT NULL,
    CONSTRAINT fk_coord_centro
        FOREIGN KEY (centro_formacion_cent_id)
        REFERENCES centro_formacion(cent_id)
);

-- ===============================
-- INSERTAR DATOS DE PRUEBA
-- ===============================

-- Insertar centros de formación (solo si no existen)
INSERT INTO centro_formacion (cent_nombre) 
SELECT 'Centro de Biotecnología Agropecuaria'
WHERE NOT EXISTS (SELECT 1 FROM centro_formacion WHERE cent_nombre = 'Centro de Biotecnología Agropecuaria');

INSERT INTO centro_formacion (cent_nombre) 
SELECT 'Centro de Tecnologías de la Información'
WHERE NOT EXISTS (SELECT 1 FROM centro_formacion WHERE cent_nombre = 'Centro de Tecnologías de la Información');

INSERT INTO centro_formacion (cent_nombre) 
SELECT 'Centro Industrial y del Desarrollo Empresarial'
WHERE NOT EXISTS (SELECT 1 FROM centro_formacion WHERE cent_nombre = 'Centro Industrial y del Desarrollo Empresarial');

-- Insertar coordinaciones (solo si no existen)
INSERT INTO coordinacion (coord_nombre, centro_formacion_cent_id) 
SELECT 'Coordinación Académica', 1
WHERE NOT EXISTS (SELECT 1 FROM coordinacion WHERE coord_nombre = 'Coordinación Académica');

INSERT INTO coordinacion (coord_nombre, centro_formacion_cent_id) 
SELECT 'Coordinación de Sistemas', 2
WHERE NOT EXISTS (SELECT 1 FROM coordinacion WHERE coord_nombre = 'Coordinación de Sistemas');

INSERT INTO coordinacion (coord_nombre, centro_formacion_cent_id) 
SELECT 'Coordinación Industrial', 3
WHERE NOT EXISTS (SELECT 1 FROM coordinacion WHERE coord_nombre = 'Coordinación Industrial');

INSERT INTO coordinacion (coord_nombre, centro_formacion_cent_id) 
SELECT 'Coordinación de Formación Profesional', 1
WHERE NOT EXISTS (SELECT 1 FROM coordinacion WHERE coord_nombre = 'Coordinación de Formación Profesional');

-- Insertar instructores de prueba (solo si no existen)
INSERT INTO instructor (inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id) 
SELECT 'Juan Carlos', 'Pérez García', 'juan.perez@sena.edu.co', 3001234567, 1
WHERE NOT EXISTS (SELECT 1 FROM instructor WHERE inst_correo = 'juan.perez@sena.edu.co');

INSERT INTO instructor (inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id) 
SELECT 'María Fernanda', 'López Martínez', 'maria.lopez@sena.edu.co', 3007654321, 2
WHERE NOT EXISTS (SELECT 1 FROM instructor WHERE inst_correo = 'maria.lopez@sena.edu.co');

INSERT INTO instructor (inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id) 
SELECT 'Pedro Antonio', 'Rodríguez Silva', 'pedro.rodriguez@sena.edu.co', 3009876543, 3
WHERE NOT EXISTS (SELECT 1 FROM instructor WHERE inst_correo = 'pedro.rodriguez@sena.edu.co');

INSERT INTO instructor (inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id) 
SELECT 'Ana María', 'González Torres', 'ana.gonzalez@sena.edu.co', 3005551234, 1
WHERE NOT EXISTS (SELECT 1 FROM instructor WHERE inst_correo = 'ana.gonzalez@sena.edu.co');

INSERT INTO instructor (inst_nombres, inst_apellidos, inst_correo, inst_telefono, centro_formacion_cent_id) 
SELECT 'Carlos Eduardo', 'Ramírez Díaz', 'carlos.ramirez@sena.edu.co', 3005559876, 2
WHERE NOT EXISTS (SELECT 1 FROM instructor WHERE inst_correo = 'carlos.ramirez@sena.edu.co');

-- Verificar datos insertados
SELECT 'Centros de Formación creados:' as mensaje, COUNT(*) as total FROM centro_formacion
UNION ALL
SELECT 'Coordinaciones creadas:', COUNT(*) FROM coordinacion
UNION ALL
SELECT 'Instructores creados:', COUNT(*) FROM instructor;
