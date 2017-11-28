CREATE DATABASE prestamos;
use prestamos;

CREATE TABLE user (
	id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
	ci int,
	ex varchar(3),
	name varchar(50),
	last_name varchar(50),
	email varchar(100),
	pwd varchar(100),
	type varchar(5),
	last_connection datetime,
	registered date
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE clients (
	id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
	ci int,
	ex varchar(3),
	name varchar(50),
	last_name varchar(50),
	fec_nac date,
	sex char(1),
	registered date
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE give (
	id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
	id_user int,
	id_clients int,
	amount float,
	fec_pre date,
	month smallint,
	fine float,
	interest float,
	type varchar(5),
	detail text,
	gain float,
	total_capital float,
	total_interest float,
	total_lender float,
	total_assistant float,

	FOREIGN KEY (id_user) REFERENCES user(id),
	FOREIGN KEY (id_clients) REFERENCES clients(id) 

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE payment (
	id int PRIMARY KEY AUTO_INCREMENT NOT NULL,
	id_give int,
	fec_pago date,
	interests float,
	capital_shares float,
	lender float,
	assistant float,
	observation text,

	FOREIGN KEY (id_give) REFERENCES give(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;








/*Procediminetos Almacenados*/
delimiter //
DROP PROCEDURE IF EXISTS pSession;
CREATE PROCEDURE pSession(
IN v_email varchar(100),
IN v_pwd varchar(100)
)
BEGIN
DECLARE us int(11);
SET us = (SELECT id FROM user WHERE email LIKE v_email);
IF(us) THEN
IF EXISTS(SELECT id FROM user WHERE id = us AND pwd LIKE v_pwd) THEN
SELECT id,'' error,type,last_connection FROM user WHERE id = us;
ELSE
SELECT 'Error: Contraseña incorrecta.' error;
END IF;
ELSE
SELECT 'Error: Correo no registrado.' error;
END IF;
END //


DROP PROCEDURE IF EXISTS pInsertUser;
CREATE PROCEDURE pInsertUser (
IN v_nombre varchar(50),
IN v_apellido varchar(50),
IN v_correo varchar(100),
IN v_contra varchar(100),
IN v_tipo varchar(5)
)
BEGIN
IF NOT EXISTS(SELECT id FROM user WHERE correo LIKE v_correo) THEN
INSERT INTO user VALUES(null,v_nombre,v_apellido,v_correo,v_contra,v_tipo,CURRENT_TIMESTAMP);
SELECT @@identity AS id,v_tipo AS tipo, 'success' AS error;
ELSE
SELECT 'Error: Correo ya registrado.' error;
END IF;
END //


DROP PROCEDURE IF EXISTS pReporte;
CREATE PROCEDURE pReporte (
IN v_fecha date
)
BEGIN
IF EXISTS(SELECT id FROM pasaje WHERE SUBSTRING(fecha,1,10) LIKE v_fecha) THEN
SELECT p.id,p.num_asiento,p.ubicacion,p.precio,p.fecha,v.horario,
v.origen,v.destino,ch.ci AS ci_chofer,ch.nombre AS nombre_chofer,ch.img AS img_chofer,b.num AS num_bus,
cli.ci AS ci_cliente,cli.nombre AS nombre_cliente,cli.apellido AS apellido_cliente 
FROM bus as b,chofer as ch,viaje as v,cliente as cli,pasaje as p 
WHERE v.id_chofer=ch.id AND v.id_bus=b.id AND p.id_viaje=v.id AND p.id_cliente=cli.id AND 
p.fecha > CONCAT(v_fecha,' ','00:00:01') AND p.fecha < CONCAT(v_fecha,' ','23:59:59');
ELSE
SELECT 'No se encontraron ventas en esa fecha' error;
END IF;
END //
