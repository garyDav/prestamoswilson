--https://donnierock.com/2013/10/08/bucles-y-condicionales-en-procedimientos-almacenados-de-mysql/
START TRANSACTION;
INSERT INTO ...
VALUES (...);
ROLLBACK;
COMMIT;

DROP PROCEDURE eliminarCT;
DELIMITER //
CREATE PROCEDURE eliminarCT(cod int,tabla int)
BEGIN
    DECLARE csaldo1 double DEFAULT 0;
    DECLARE csaldo2 double DEFAULT 0;
    DECLARE tsaldo1 double DEFAULT 0;
    DECLARE tsaldo2 double DEFAULT 0;

    DECLARE saldo1 double DEFAULT 0;
    DECLARE saldo2 double DEFAULT 0;

	DECLARE resta1 double DEFAULT 0;
	DECLARE resta2 double DEFAULT 0;
        
    IF (tabla=1) THEN
            SET csaldo1 = (SELECT caja1 FROM cafe WHERE cod_cafe=cod);
            SET csaldo2 = (SELECT caja2 FROM cafe WHERE cod_cafe=cod);
            SET saldo1 = (SELECT saldoc1 FROM saldos WHERE cod_saldo=1);
            SET saldo2 = (SELECT saldoc2 FROM saldos WHERE cod_saldo=1);
	SET resta1 = saldo1 - csaldo1;
	SET resta2 = saldo2 - csaldo2;
            UPDATE saldos SET saldoc1=resta1,saldoc2=resta2 WHERE cod_saldo=1;
	ELSE
    	IF (tabla=2) THEN
        	SET tsaldo1 = (SELECT caja1 FROM tour WHERE cod_tour=cod);
        	SET tsaldo2 = (SELECT caja2 FROM tour WHERE cod_tour=cod);
            SET saldo1 = (SELECT saldot1 FROM saldos WHERE cod_saldo=1);
	        SET saldo2 = (SELECT saldot2 FROM saldos WHERE cod_saldo=1);
		SET resta1 = saldo1 - tsaldo1;
		SET resta2 = saldo2 - tsaldo2;
            	UPDATE saldos SET saldot1=resta1,saldot2=resta2 WHERE cod_saldo=1;
    	END IF;
	END IF;
END;//
delimiter ;



--TRANSACCIONES
DELIMITER //
CREATE PROCEDURE tDeleteCascadePais(
    IN `v_id` int
)
BEGIN
    DECLARE error int DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET error=1;
        SELECT "Transaccion no completada: tDeleteCascadePais";
    END;
    START TRANSACTION;
    DELETE FROM pais WHERE id=v_id;
    DELETE FROM departamento WHERE id_pais=v_id;
    IF (error = 1) THEN
        ROLLBACK;
    ELSE
        COMMIT;
    END IF;
END //

--otro es valido pero es menos cabron que el otro
--DROP PROCEDURE IF EXISTS tDeleteCascadePais;
DELIMITER //
CREATE PROCEDURE tDeleteCascadePais(
    IN `v_id` int
)
BEGIN
    START TRANSACTION;
    DELETE FROM pais WHERE id=v_id;
    DELETE FROM departamento WHERE id_pais=v_id;
    COMMIT;
END //