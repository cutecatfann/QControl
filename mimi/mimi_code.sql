-- View: See certain details about all users in the database
CREATE VIEW v_UserRole AS
SELECT 
    usr_id AS UserID,
    usr_name AS Name,
    usr_role AS Role,
    CASE 
        WHEN (SELECT COUNT(*) FROM chck WHERE usr_id = usr.usr_id) > 0 THEN 'Active'
        ELSE 'Inactive'
    END AS Status
FROM usr;

SELECT * from v_UserRole;

-- Procedure
CREATE DEFINER=`pieperm`@`%` PROCEDURE `p_GetQualityCheckRatio`()
BEGIN
    SELECT 
        pt.pt_name AS 'Product Type',
        IFNULL(SUM(CASE WHEN b.batch_status = 'accepted' THEN 1 ELSE 0 END), 0) / 
        GREATEST(IFNULL(SUM(CASE WHEN b.batch_status = 'rejected' THEN 1 ELSE 0 END), 1), 1) AS 'Pass/Fail Ratio'
    FROM 
        product_type pt
    LEFT JOIN 
        batch b ON pt.pt_id = b.pt_id
    GROUP BY 
        pt.pt_id;
END

-- Function: Calculates the average check value for a specific batch. This will show the quality level for a given batch.
CREATE DEFINER=`pieperm`@`%` FUNCTION `f_AverageCheckValue`(batch_id_input INT) RETURNS float
    DETERMINISTIC
BEGIN 
    DECLARE avg_value FLOAT; 
    SELECT AVG(chck_value) INTO avg_value 
    FROM chck 
    WHERE batch_id = batch_id_input; 
    RETURN IFNULL(avg_value, 0); 
END

-- Tigger: Three triggers logs all changes to the usr table into the usr_audit table
-- After Insert:
CREATE DEFINER=`pieperm`@`%` TRIGGER after_usr_insert
AFTER INSERT ON usr
FOR EACH ROW
BEGIN
    INSERT INTO usr_audit (usr_name, usr_role, user_email, action_performed_by, action_type)
    VALUES (NEW.usr_name, NEW.usr_role, NEW.user_email, USER(), 'INSERT');
END

-- After Update:
CREATE DEFINER=`pieperm`@`%` TRIGGER after_usr_update
AFTER UPDATE ON usr
FOR EACH ROW
BEGIN
    INSERT INTO usr_audit (usr_name, usr_role, user_email, action_performed_by, action_type)
    VALUES (NEW.usr_name, NEW.usr_role, NEW.user_email, USER(), 'UPDATE');
END

-- After Delete: 
CREATE DEFINER=`pieperm`@`%` TRIGGER after_usr_delete
AFTER DELETE ON usr
FOR EACH ROW
BEGIN
    INSERT INTO usr_audit (usr_name, usr_role, user_email, action_performed_by, action_type)
    VALUES (OLD.usr_name, OLD.usr_role, OLD.user_email, USER(), 'DELETE');
END
