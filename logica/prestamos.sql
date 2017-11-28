-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-11-2017 a las 22:15:43
-- Versión del servidor: 10.1.26-MariaDB
-- Versión de PHP: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `prestamos`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `pInsertClients` (IN `v_ci` INT, IN `v_ex` VARCHAR(3), IN `v_name` VARCHAR(50), IN `v_last_name` VARCHAR(50), IN `v_civil_status` VARCHAR(15), IN `v_profession` VARCHAR(60), IN `v_address` VARCHAR(60), IN `v_cellphone1` VARCHAR(15), IN `v_cellphone2` VARCHAR(15), IN `v_src` VARCHAR(255), IN `v_fec_nac` DATE, IN `v_sex` VARCHAR(9))  BEGIN
IF NOT EXISTS(SELECT id FROM clients WHERE ci LIKE v_ci) THEN
INSERT INTO clients VALUES(null,v_ci,v_ex,v_name,v_last_name,v_civil_status,v_profession,v_address,v_cellphone1,v_cellphone2,v_src,v_fec_nac,v_sex,CURRENT_TIMESTAMP);
SELECT @@identity AS id,'not' AS error, 'Cliente registrado.' AS msj;
ELSE
SELECT 'yes' error,'Error: CI ya registrado.' msj;
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pInsertGive` (IN `v_id_user` INT, IN `v_id_clients` INT, IN `v_id_userin` INT, IN `v_amount` FLOAT, IN `v_fec_pre` DATE, IN `v_month` SMALLINT, IN `v_fine` FLOAT, IN `v_interest` FLOAT, IN `v_type` VARCHAR(5), IN `v_detail` TEXT, IN `v_gain` FLOAT)  BEGIN
INSERT INTO give VALUES(null,v_id_user,v_id_clients,v_id_userin,v_amount,v_fec_pre,v_month,v_fine,v_interest,v_type,v_detail,v_gain,0,0,0,0,1);
SELECT @@identity AS id,'not' AS error, 'Prestamo registrado.' AS msj;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pInsertUser` (IN `v_ci` INT, IN `v_ex` VARCHAR(3), IN `v_name` VARCHAR(50), IN `v_last_name` VARCHAR(50), IN `v_email` VARCHAR(100), IN `v_sex` VARCHAR(9), IN `v_pwd` VARCHAR(100), IN `v_type` VARCHAR(5))  BEGIN
IF NOT EXISTS(SELECT id FROM user WHERE email LIKE v_email) THEN
INSERT INTO user VALUES(null,v_ci,v_ex,v_name,v_last_name,v_email,v_sex,'avatar.png',v_pwd,v_type,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);
SELECT @@identity AS id,v_type AS tipo, 'not' AS error, 'Registro insertado.' AS msj;
ELSE
SELECT 'yes' error,'Error: Correo ya registrado.' msj;
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pReporte` (IN `v_fecha` DATE)  BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pSession` (IN `v_email` VARCHAR(100), IN `v_pwd` VARCHAR(100))  BEGIN
DECLARE us int(11);
SET us = (SELECT id FROM user WHERE email LIKE v_email);
IF(us) THEN
IF EXISTS(SELECT id FROM user WHERE id = us AND pwd LIKE v_pwd) THEN
SELECT id,type,'not' AS error,'Espere por favor...' AS msj FROM user WHERE id = us;
ELSE
SELECT 'yes' error,'Error: Contraseña incorrecta.' msj;
END IF;
ELSE
SELECT 'yes' error,'Error: Correo no registrado.' msj;
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pUpdateUser` (IN `v_id` INT, IN `v_email` VARCHAR(100), IN `v_pwdA` VARCHAR(100), IN `v_pwdN` VARCHAR(100), IN `v_pwdR` VARCHAR(100), IN `v_src` VARCHAR(255))  BEGIN
DECLARE us int(11);
SET us = (SELECT id FROM user WHERE id=v_id AND pwd LIKE v_pwdA);

IF ( (v_pwdA NOT LIKE '') AND (v_src NOT LIKE '') ) THEN

IF (us) THEN
IF (v_pwdN LIKE v_pwdR) THEN
UPDATE user SET email=v_email,pwd=v_pwdN,src=v_src WHERE id=v_id;
SELECT v_id AS id, 'not' AS error,'Perfil actualizado.' AS msj;
ELSE
SELECT v_id AS id, 'yes' AS error,'Las contraseñas no coinciden, repita bien la nueva contraseña.' AS msj;
END IF;
ELSE
SELECT v_id AS id, 'yes' AS error,'La contraseña antigua no es correcta.' AS msj, (v_pwdA NOT LIKE '') AS res;
END IF;

END IF;

IF ( (v_pwdA LIKE '') AND (v_src LIKE '') ) THEN
UPDATE user SET email=v_email WHERE id=v_id;
SELECT v_id AS id, 'not' AS error,'Perfil actualizado.' AS msj;
ELSE

IF ( (v_pwdA LIKE '') AND (v_src NOT LIKE '') ) THEN
UPDATE user SET email=v_email,src=v_src WHERE id=v_id;
SELECT v_id AS id, 'not' AS error,'Perfil actualizado.' AS msj;
ELSE
IF ( (v_src LIKE '') AND (v_pwdA NOT LIKE '') ) THEN
IF (us) THEN
IF (v_pwdN LIKE v_pwdR) THEN
UPDATE user SET email=v_email,pwd=v_pwdN WHERE id=v_id;
SELECT v_id AS id, 'not' AS error,'Perfil actualizado.' AS msj;
ELSE
SELECT v_id AS id, 'yes' AS error,'Las contraseñas no coinciden, repita bien la nueva contraseña.' AS msj;
END IF;
ELSE
SELECT v_id AS id, 'yes' AS error,'La contraseña antigua no es correcta.' AS msj;
END IF;
END IF;
END IF;

END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `tInsertPayment` (IN `v_id_give` INT, IN `v_fec_pago_actual` DATE, IN `v_fec_pago` DATE, IN `v_observation` TEXT)  BEGIN
DECLARE g_amount float;
DECLARE g_month smallint(6);
DECLARE g_fine float;
DECLARE g_interest float;
DECLARE g_type varchar(5);
DECLARE g_gain float;

DECLARE p_interests float;
DECLARE p_capital_shares float;
DECLARE p_lender float;
DECLARE p_assistant float;
DECLARE p_month_payment smallint(6);
DECLARE p_dif_pagos int;
DECLARE i int;
DECLARE sum_fine float;
DECLARE pamount int;

DECLARE error int DEFAULT 0;
DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
BEGIN
SET error=1;
SELECT "yes" error,"Transaccion no completada: tInsertPayment" msj;
END;


START TRANSACTION;
SET g_amount = (SELECT amount FROM give WHERE id=v_id_give);
SET g_month = (SELECT month FROM give WHERE id=v_id_give);
SET g_fine = (SELECT fine FROM give WHERE id=v_id_give);
SET g_interest = (SELECT interest FROM give WHERE id=v_id_give);
SET g_type = (SELECT type FROM give WHERE id=v_id_give);
SET g_gain = (SELECT gain FROM give WHERE id=v_id_give);
SET p_month_payment = (SELECT COUNT(id) FROM payment WHERE id_give=v_id_give);

IF g_type = 'men' THEN
SET p_interests = g_amount*(g_interest/100);
IF g_month = (p_month_payment+1) THEN
SET p_capital_shares = g_amount;
UPDATE give SET visible=2 WHERE id=v_id_give;
ELSE
SET p_capital_shares = 0;
END IF;

SET p_dif_pagos = (SELECT DATEDIFF(v_fec_pago,v_fec_pago_actual));
SET i = 0;
SET sum_fine = 0;
while i < p_dif_pagos do
SET sum_fine = sum_fine+g_fine;
SET i=i+1;
end while;
SET p_interests = p_interests+sum_fine;
SET p_lender = p_interests*((100-g_gain)/100);
SET p_assistant = p_interests*(g_gain/100);
ELSE
SET pamount = g_amount/g_month;
SET p_interests = (g_amount-(pamount*p_month_payment))*(g_interest/100);

IF g_month = (p_month_payment+1) THEN
SET pamount = pamount+(g_amount-(pamount*g_month));
UPDATE give SET visible=2 WHERE id=v_id_give;
END IF;
SET p_capital_shares = pamount;
SET p_dif_pagos = (SELECT DATEDIFF(v_fec_pago,v_fec_pago_actual));
SET i = 0;
SET sum_fine = 0;
while i < p_dif_pagos do
SET sum_fine = sum_fine+g_fine;
SET i=i+1;
end while;
SET p_interests = p_interests+sum_fine;
SET p_lender = p_interests*((100-g_gain)/100);
SET p_assistant = p_interests*(g_gain/100);
END IF;

UPDATE give SET total_capital=total_capital+p_capital_shares,
total_interest=total_interest+p_interests,
total_lender=total_lender+p_lender,
total_assistant=total_assistant+p_assistant WHERE id=v_id_give;
INSERT INTO payment VALUES(null,v_id_give,v_fec_pago,p_interests,p_capital_shares,p_lender,p_assistant,v_observation);
SELECT (p_month_payment+1) AS pago,"not" error,"insertado correctamente" msj;


IF (error = 1) THEN
ROLLBACK;
ELSE
COMMIT;
END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `ci` int(11) DEFAULT NULL,
  `ex` varchar(3) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `civil_status` varchar(15) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `profession` varchar(60) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `address` varchar(60) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `cellphone1` varchar(15) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `cellphone2` varchar(15) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `src` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `fec_nac` date DEFAULT NULL,
  `sex` varchar(9) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `registered` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id`, `ci`, `ex`, `name`, `last_name`, `civil_status`, `profession`, `address`, `cellphone1`, `cellphone2`, `src`, `fec_nac`, `sex`, `registered`) VALUES
(1, 7208004, 'Tj', 'vanesa', 'medina', 'solter@', 'estudiante', 'raul de cordova', '78423232', '76859403', 'avatar.png', '1990-10-20', 'Femenino', '2017-11-13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `give`
--

CREATE TABLE `give` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_clients` int(11) DEFAULT NULL,
  `id_userin` int(11) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `fec_pre` date DEFAULT NULL,
  `month` smallint(6) DEFAULT NULL,
  `fine` float DEFAULT NULL,
  `interest` float DEFAULT NULL,
  `type` varchar(5) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `detail` text COLLATE utf8_spanish2_ci,
  `gain` float DEFAULT NULL,
  `total_capital` float DEFAULT NULL,
  `total_interest` float DEFAULT NULL,
  `total_lender` float DEFAULT NULL,
  `total_assistant` float DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `give`
--

INSERT INTO `give` (`id`, `id_user`, `id_clients`, `id_userin`, `amount`, `fec_pre`, `month`, `fine`, `interest`, `type`, `detail`, `gain`, `total_capital`, `total_interest`, `total_lender`, `total_assistant`, `visible`) VALUES
(1, 1, 1, 1, 2000, '2017-11-13', 12, 10, 20, 'men', 'adfasd', 10, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `id_give` int(11) DEFAULT NULL,
  `fec_pago` date DEFAULT NULL,
  `interests` float DEFAULT NULL,
  `capital_shares` float DEFAULT NULL,
  `lender` float DEFAULT NULL,
  `assistant` float DEFAULT NULL,
  `observation` text COLLATE utf8_spanish2_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `ci` int(11) DEFAULT NULL,
  `ex` varchar(3) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `sex` varchar(9) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `src` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `pwd` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `type` varchar(5) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `last_connection` datetime DEFAULT NULL,
  `registered` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `ci`, `ex`, `name`, `last_name`, `email`, `sex`, `src`, `pwd`, `type`, `last_connection`, `registered`) VALUES
(1, 7208003, 'Tj', 'Wilson', 'Medina Chipana', 'wilson@gmail.com', 'Masculino', 'wilson.jpg', '585f7f3723df82f91fffd25a5c6900597cd4d1c1', 'supad', '2017-10-19 22:12:08', '2017-10-19'),
(3, 111, 'Tj', 'usuarioprueba', 'xxx', 'prueba@gmail.com', 'Masculino', 'avatar.png', '648e1e75f439157e1d0708606f00dcbca661c01e', 'admin', '2017-11-25 18:25:38', '2017-11-25');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `give`
--
ALTER TABLE `give`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_clients` (`id_clients`),
  ADD KEY `id_userin` (`id_userin`);

--
-- Indices de la tabla `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_give` (`id_give`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `give`
--
ALTER TABLE `give`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `give`
--
ALTER TABLE `give`
  ADD CONSTRAINT `give_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `give_ibfk_2` FOREIGN KEY (`id_clients`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `give_ibfk_3` FOREIGN KEY (`id_userin`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`id_give`) REFERENCES `give` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
