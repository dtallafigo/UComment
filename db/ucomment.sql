------------------------------
-- Archivo de base de datos --
------------------------------
DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
        id                  BIGSERIAL       PRIMARY KEY
    ,   log_us              varchar(60)     NOT NULL UNIQUE
    ,   email               varchar(255)    NOT NULL UNIQUE
    ,   password            varchar(255)    NOT NULL
    ,   rol                 varchar(255)    DEFAULT 'user'
    ,   auth_key            varchar(255)
    ,   token               varchar(2048)
    ,   url_img             varchar(2048)   DEFAULT 'user.svg'
    ,   bio                 varchar(280)    DEFAULT 'Hola!'
    ,   ubi                 varchar(50)     
);

DROP TABLE IF EXISTS comentarios CASCADE;

CREATE TABLE comentarios
(
        id              BIGSERIAL       PRIMARY KEY
    ,   usuario_id      BIGINT          NOT NULL REFERENCES usuarios (id) ON DELETE CASCADE
    ,   text            varchar(280)    NOT NULL 
    ,   created_at      TIMESTAMP(0)    DEFAULT CURRENT_TIMESTAMP
    ,   respuesta       BIGINT          REFERENCES comentarios (id) ON DELETE SET NULL
    ,   citado          BIGINT          REFERENCES comentarios (id) ON DELETE SET NULL
    ,   url_img         varchar(2048)
);

DROP TABLE IF EXISTS seguidores CASCADE;

CREATE TABLE seguidores
(
    seguidor_id     BIGINT          REFERENCES usuarios (id) ON DELETE CASCADE
  , seguido_id      BIGINT          REFERENCES usuarios (id) ON DELETE CASCADE
  , created_at      TIMESTAMP(0)    DEFAULT CURRENT_TIMESTAMP
  , PRIMARY KEY (seguidor_id, seguido_id)
);

DROP TABLE IF EXISTS likes CASCADE;

/* En esta tabla se almacena el comentario y el usuario que ha realizado la accion */
CREATE TABLE likes
(
        usuario_id      BIGINT      REFERENCES usuarios (id) ON DELETE CASCADE
    ,   comentario_id   BIGINT      REFERENCES comentarios(id) ON DELETE CASCADE
    ,   created_at      TIMESTAMP(0)    DEFAULT CURRENT_TIMESTAMP
    ,   PRIMARY KEY (usuario_id, comentario_id)  
);

DROP TABLE IF EXISTS comsave CASCADE;

/* Tabla donde se guardan los comentarios favorios */
CREATE TABLE comsave
(
        usuario_id      BIGINT      REFERENCES usuarios (id) ON DELETE CASCADE
    ,   comentario_id   BIGINT      REFERENCES comentarios(id) ON DELETE CASCADE
    ,   PRIMARY KEY (usuario_id, comentario_id)  
);

DROP TABLE IF EXISTS bloqueados CASCADE;

CREATE TABLE bloqueados
(
    usuario     BIGINT      REFERENCES usuarios (id) ON DELETE CASCADE
  , bloqueado   BIGINT      REFERENCES usuarios (id) ON DELETE CASCADE
  , PRIMARY KEY (usuario, bloqueado)
);

INSERT INTO usuarios (log_us, email, password, rol, auth_key)
VALUES  ('florido', 'david.xipi99@hotmail.com', crypt('hola', gen_salt('bf', 10)), 'admin', ''),
        ('david', 'david.florido@iesdonana.org', crypt('hola', gen_salt('bf', 10)), 'user', ''),
        ('antonio', 'antonio@hotmail.com', crypt('hola', gen_salt('bf', 10)), 'user', ''),
        ('damian', 'damian@hotmail.com', crypt('hola', gen_salt('bf', 10)), 'user', ''),
        ('jaime', 'jaime@hotmail.com', crypt('hola', gen_salt('bf', 10)), 'user', ''),
        ('martin', 'martin@hotmail.com', crypt('hola', gen_salt('bf', 10)), 'user', ''),
        ('chuso', 'chuso@hotmail.com', crypt('hola', gen_salt('bf', 10)), 'user', ''),
        ('cheto', 'cheto@hotmail.com', crypt('hola', gen_salt('bf', 10)), 'user', '');

INSERT INTO usuarios (log_us, email, password, rol, auth_key, ubi)
VALUES  ('Vandal', 'Vandal@hotmail.com', crypt('hola', gen_salt('bf', 10)), 'user', '', 'https://vandal.elespanol.com/');

INSERT INTO comentarios (usuario_id, text, respuesta, citado)
VALUES  ('1', 'Primer comentario.', NULL, NULL),
        ('1', 'Segundo comentario.', NULL, NULL),
        ('1', 'Tercero comentario.', NULL, NULL),
        ('1', 'Cuarto comentario.', NULL, NULL),
        ('1', 'Quinto comentario.', NULL, NULL),
        ('1', 'Sexto comentario.', NULL, NULL),
        ('2', 'Primer comentario.', NULL, NULL),
        ('2', 'Segundo comentario.', NULL, NULL),
        ('2', 'Tercero comentario.', NULL, NULL),
        ('2', 'Cuarto comentario.', NULL, NULL),
        ('2', 'Quinto comentario.', NULL, NULL),
        ('2', 'Sexto comentario.', NULL, NULL);

INSERT INTO seguidores (seguido_id, seguidor_id)
VALUES ('3', '1'),
       ('4', '1'),
       ('1', '2'),
       ('1', '5'),
       ('1', '3'),
       ('1', '4'),
       ('2', '1'),
       ('2', '4');