-- =========================
-- TABLAS BASE
-- =========================

CREATE TABLE titulo_programa (
    titpro_id SERIAL PRIMARY KEY,
    titpro_nombre VARCHAR(45) NOT NULL
);

CREATE TABLE centro_formacion (
    cent_id SERIAL PRIMARY KEY,
    cent_nombre VARCHAR(100) NOT NULL
);

CREATE TABLE sede (
    sede_id SERIAL PRIMARY KEY,
    sede_nombre VARCHAR(45) NOT NULL
);

CREATE TABLE competencia (
    comp_id SERIAL PRIMARY KEY,
    comp_nombre_corto VARCHAR(30),
    comp_horas INT,
    comp_nombre_unidad_competencia VARCHAR(150)
);

-- =========================
-- TABLAS DEPENDIENTES
-- =========================

CREATE TABLE programa (
    prog_codigo SERIAL PRIMARY KEY,
    prog_denominacion VARCHAR(100) NOT NULL,
    tit_programa_titpro_id INT NOT NULL,
    prog_tipo VARCHAR(30),
    CONSTRAINT fk_programa_titulo
        FOREIGN KEY (tit_programa_titpro_id)
        REFERENCES titulo_programa(titpro_id)
);

CREATE TABLE instructor (
    inst_id SERIAL PRIMARY KEY,
    inst_nombres VARCHAR(45),
    inst_apellidos VARCHAR(45),
    inst_correo VARCHAR(45),
    inst_telefono BIGINT,
    centro_formacion_cent_id INT,
    inst_password VARCHAR(45),
    CONSTRAINT fk_instructor_centro
        FOREIGN KEY (centro_formacion_cent_id)
        REFERENCES centro_formacion(cent_id)
);

CREATE TABLE coordinacion (
    coord_id SERIAL PRIMARY KEY,
    coord_descripcion VARCHAR(45),
    centro_formacion_cent_id INT,
    coord_nombre_coordinador VARCHAR(45),
    coord_correo VARCHAR(45),
    coord_password VARCHAR(45),
    CONSTRAINT fk_coordinacion_centro
        FOREIGN KEY (centro_formacion_cent_id)
        REFERENCES centro_formacion(cent_id)
);

CREATE TABLE ambiente (
    amb_id VARCHAR(5) PRIMARY KEY,
    amb_nombre VARCHAR(45),
    sede_sede_id INT,
    CONSTRAINT fk_ambiente_sede
        FOREIGN KEY (sede_sede_id)
        REFERENCES sede(sede_id)
);

-- =========================
-- TABLAS INTERMEDIAS
-- =========================

CREATE TABLE competxprograma (
    programa_prog_id INT,
    competencia_comp_id INT,
    PRIMARY KEY (programa_prog_id, competencia_comp_id),
    CONSTRAINT fk_cp_programa
        FOREIGN KEY (programa_prog_id)
        REFERENCES programa(prog_codigo),
    CONSTRAINT fk_cp_competencia
        FOREIGN KEY (competencia_comp_id)
        REFERENCES competencia(comp_id)
);

CREATE TABLE instru_competencia (
    inscomp_id SERIAL PRIMARY KEY,
    instructor_inst_id INT,
    competxprograma_programa_prog_id INT,
    competxprograma_competencia_comp_id INT,
    inscomp_vigencia DATE,
    CONSTRAINT fk_ic_instructor
        FOREIGN KEY (instructor_inst_id)
        REFERENCES instructor(inst_id),
    CONSTRAINT fk_ic_competxprograma
        FOREIGN KEY (competxprograma_programa_prog_id, competxprograma_competencia_comp_id)
        REFERENCES competxprograma(programa_prog_id, competencia_comp_id)
);

-- =========================
-- FICHA
-- =========================

CREATE TABLE ficha (
    fich_id SERIAL PRIMARY KEY,
    programa_prog_id INT,
    instructor_inst_id_lider INT,
    fich_jornada VARCHAR(20),
    coordinacion_coord_id INT,
    fich_fecha_ini_lectiva DATE,
    fich_fecha_fin_lectiva DATE,
    CONSTRAINT fk_ficha_programa
        FOREIGN KEY (programa_prog_id)
        REFERENCES programa(prog_codigo),
    CONSTRAINT fk_ficha_instructor
        FOREIGN KEY (instructor_inst_id_lider)
        REFERENCES instructor(inst_id),
    CONSTRAINT fk_ficha_coordinacion
        FOREIGN KEY (coordinacion_coord_id)
        REFERENCES coordinacion(coord_id)
);

-- =========================
-- ASIGNACION
-- =========================

CREATE TABLE asignacion (
    asig_id SERIAL PRIMARY KEY,
    instructor_inst_id INT,
    asig_fecha_ini TIMESTAMP,
    asig_fecha_fin TIMESTAMP,
    ficha_fich_id INT,
    ambiente_amb_id VARCHAR(5),
    competencia_comp_id INT,
    CONSTRAINT fk_asig_instructor
        FOREIGN KEY (instructor_inst_id)
        REFERENCES instructor(inst_id),
    CONSTRAINT fk_asig_ficha
        FOREIGN KEY (ficha_fich_id)
        REFERENCES ficha(fich_id),
    CONSTRAINT fk_asig_ambiente
        FOREIGN KEY (ambiente_amb_id)
        REFERENCES ambiente(amb_id),
    CONSTRAINT fk_asig_competencia
        FOREIGN KEY (competencia_comp_id)
        REFERENCES competencia(comp_id)
);

CREATE TABLE detalle_asignacion (
    detasig_id SERIAL PRIMARY KEY,
    asignacion_asig_id INT,
    detasig_hora_ini TIMESTAMP,
    detasig_hora_fin TIMESTAMP,
    CONSTRAINT fk_detalle_asignacion
        FOREIGN KEY (asignacion_asig_id)
        REFERENCES asignacion(asig_id)
);
