CREATE TABLE alumno (
    correo VARCHAR(100) PRIMARY KEY,
    contrasena VARCHAR(100) NOT NULL,
    nom_usuario VARCHAR(100) NOT NULL,
    progr_niveles INT NOT NULL,
    puntaje_global INT NOT NULL,
    foto_perfil VARCHAR(100)
);

CREATE TABLE nivel (
    id_nivel INT PRIMARY KEY,
    tematica VARCHAR(100) NOT NULL,
    dificultad ENUM('Fácil', 'Intermedio', 'Difícil') NOT NULL
);

CREATE TABLE coleccionable (
    id_objeto INT PRIMARY KEY,
    descripcion TEXT NOT NULL,
    id_nivel_desbloqueo INT,
    imagen_objeto VARCHAR(100),
    FOREIGN KEY (id_nivel_desbloqueo) REFERENCES nivel(id_nivel)
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
    id_nivel INT,
    puntaje_obtenido INT NOT NULL,
    PRIMARY KEY (correo_usuario, id_nivel),
    FOREIGN KEY (correo_usuario) REFERENCES alumno(correo)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_nivel) REFERENCES nivel(id_nivel)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
