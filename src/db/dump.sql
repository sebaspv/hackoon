CREATE TABLE alumno (
    correo VARCHAR(100) PRIMARY KEY,
    contrasena VARCHAR(100) NOT NULL,
    nom_usuario VARCHAR(100) NOT NULL,
    progr_niveles INT NOT NULL,
    puntaje_global INT NOT NULL,
    foto_perfil VARCHAR(100)
);

CREATE TABLE tema (
    id_tema INT PRIMARY KEY,
    tema VARCHAR(100) NOT NULL
);

CREATE TABLE dificultad (
    id_dificultad INT PRIMARY KEY,
    dificultad VARCHAR(100) NOT NULL
);

CREATE TABLE pregunta (
    id_pregunta INT PRIMARY KEY,
    nombre_pregunta VARCHAR(150) NOT NULL,
    pregunta TEXT NOT NULL,
    respuesta1 VARCHAR(100) NOT NULL,
    respuesta2 VARCHAR(100) NOT NULL,
    respuesta3 VARCHAR(100) NOT NULL,
    respuesta_correcta VARCHAR(100) NOT NULL,
    dificultad INT,
    FOREIGN KEY (dificultad) REFERENCES dificultad(id_dificultad)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE pregunta_tema (
    id_pregunta_tema INT PRIMARY KEY,
    id_pregunta INT,
    id_tema INT,
    FOREIGN KEY (id_pregunta) REFERENCES pregunta(id_pregunta)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_tema) REFERENCES tema(id_tema)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE coleccionable (
    id_objeto INT PRIMARY KEY,
    descripcion TEXT NOT NULL,
    id_pregunta_desbloqueo INT,
    imagen_objeto VARCHAR(100),
    FOREIGN KEY (id_pregunta_desbloqueo) REFERENCES pregunta(id_pregunta)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE objeto_usuario (
    correo_usuario VARCHAR(100),
    id_objeto INT,
    PRIMARY KEY (correo_usuario, id_objeto),
    FOREIGN KEY (correo_usuario) REFERENCES alumno(correo)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_objeto) REFERENCES coleccionable(id_objeto)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE usuario_nivel (
    correo_usuario VARCHAR(100),
    id_pregunta INT,
    puntaje_obtenido INT NOT NULL,
    PRIMARY KEY (correo_usuario, id_pregunta),
    FOREIGN KEY (correo_usuario) REFERENCES alumno(correo)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_pregunta) REFERENCES pregunta(id_pregunta)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
