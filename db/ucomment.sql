------------------------------
-- Archivo de base de datos --
------------------------------

CREATE EXTENSION pgcrypto;

DROP TABLE IF EXISTS usuarios CASCADE;


CREATE TABLE usuarios
(
        id          BIGSERIAL       PRIMARY KEY
    ,   log_us      varchar(60)     NOT NULL UNIQUE
    ,   email       varchar(255)    NOT NULL UNIQUE
    ,   password    varchar(255)    NOT NULL
    ,   rol         varchar(255)    DEFAULT 'user'
    ,   auth_key    varchar(255)
    ,   url_img     varchar(2048)   DEFAULT 'user.svg'
    ,   bio         varchar(280)    DEFAULT 'Hola!'
    ,   ubi         varchar(50)   
);


DROP TABLE IF EXISTS comentarios CASCADE;


CREATE TABLE comentarios
(
        id              BIGSERIAL       PRIMARY KEY
    ,   usuario_id      BIGINT          NOT NULL REFERENCES usuarios (id) ON DELETE CASCADE
    ,   text            varchar(280)    NOT NULL 
    ,   created_at      TIMESTAMP(0)    DEFAULT CURRENT_TIMESTAMP
    ,   respuesta       BIGINT          REFERENCES comentarios (id) ON DELETE CASCADE
    ,   citado          BIGINT          REFERENCES comentarios (id) ON DELETE CASCADE
    ,   img             varchar(2048)
);