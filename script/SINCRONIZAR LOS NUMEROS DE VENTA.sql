SET @database_name = 'dist_hist';

DELIMITER $$
CREATE PROCEDURE del_fk() BEGIN
IF EXISTS(
SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = @database_name AND TABLE_NAME = 'venta'
     AND CONSTRAINT_NAME = 'venta_ibfk_1'
)
THEN
    ALTER TABLE venta DROP FOREIGN KEY venta_ibfk_1;

END IF;
END$$
DELIMITER ; 

CALL del_fk();
DROP PROCEDURE del_fk;

DELETE FROM documento_venta;

UPDATE venta SET numero_documento = venta_id;

INSERT INTO documento_venta 
    (id_tipo_documento, nombre_tipo_documento, documento_Serie, documento_Numero)
(SELECT venta_id, 'NOTA DE ENTREGA', '0001', LPAD(venta_id, 5, '0') FROM venta);

SET @auto_increment := (SELECT `AUTO_INCREMENT` 
FROM  INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = @database_name
AND   TABLE_NAME = 'venta' limit 1);

SET @s = CONCAT('ALTER TABLE documento_venta AUTO_INCREMENT=', @auto_increment);
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;


ALTER TABLE venta
ADD CONSTRAINT venta_ibfk_1
FOREIGN KEY (numero_documento) REFERENCES documento_venta(id_tipo_documento);

