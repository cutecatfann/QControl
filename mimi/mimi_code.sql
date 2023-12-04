/*
Title: Mimi's MySQL Object Implementation
Author: Mimi Pieper
Date: 01/12/2023
Description: This file contains the MySQL objects Mimi created for the QControl database. 

It contains a View (v_userRole), Procedure (p_GetQualityCheckRatio), Function (f_AverageCheckValue), and Triggers (after_usr_insert, after_usr_update, after_usr_delete)
*/

-- View: See certain details about all users in the database
-- Expected Output is a table with all current users, one example will be:
-- User ID : 2 | Name : Mimi | Role : q_manager | Status : Active
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
-- The procedure is useful for assessing the overall quality performance of different product types. A higher pass/fail ratio indicates better quality control or fewer issues during production for that product type. It's useful for quality managers to monitor and improve the manufacturing process.

-- Pass/Fail Ratio Calculation: This is a fraction. The numerator is the total count of batches with the status 'accepted' for each product type. The denominator is the total count of batches with the status 'rejected' for each product type, with a minimum value of 1 to avoid division by zero.

-- Expected Output: You will see:
-- Chocolate Chip | 1.0000
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
-- Expected Input/Output: Enter any Batch ID like 2, which will result in 0.564037. Any non used Batch IDs (example: 8387), will result in a value of 0.
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
-- Example Input: User Name: Marvin, User Email: marvinlovesfish@gmail.com, Password: password, User Role: q_tech
-- Example Output: You will see a log of the most recent insert with this success message: User added successfully.
-- Here is an example output: 8 | Marvin | 2023-12-04 13:47:08	| q_tech |marvinlovesfish@gmail.com	| pieperm@localhost | INSERT
CREATE DEFINER=`pieperm`@`%` TRIGGER after_usr_insert
AFTER INSERT ON usr
FOR EACH ROW
BEGIN
    INSERT INTO usr_audit (usr_name, usr_role, user_email, action_performed_by, action_type)
    VALUES (NEW.usr_name, NEW.usr_role, NEW.user_email, USER(), 'INSERT');
END

-- After Update:
-- Example Input: Origial User Name: Marvin, New User Name: Marvin Pieper
-- Example Output: You will see a log of the most recent insert with this success message: User updated successfully. Below is the log of your most recent change.
-- 9 | Marvin Pieper | 2023-12-04 13:49:20 | q_tech | marvinlovesfish@gmail.com	| pieperm@localhost	| UPDATE
CREATE DEFINER=`pieperm`@`%` TRIGGER after_usr_update
AFTER UPDATE ON usr
FOR EACH ROW
BEGIN
    INSERT INTO usr_audit (usr_name, usr_role, user_email, action_performed_by, action_type)
    VALUES (NEW.usr_name, NEW.usr_role, NEW.user_email, USER(), 'UPDATE');
END

-- After Delete: 
-- Example Input: Marvin
-- Example Output: You will see a log of the most recent insert with this success message: User deleted successfully.Below is the log of your most recent change.
-- 9 | Marvin Pieper | 2023-12-04 13:49:20 | q_tech | marvinlovesfish@gmail.com | pieperm@localhost | UPDATE
CREATE DEFINER=`pieperm`@`%` TRIGGER after_usr_delete
AFTER DELETE ON usr
FOR EACH ROW
BEGIN
    INSERT INTO usr_audit (usr_name, usr_role, user_email, action_performed_by, action_type)
    VALUES (OLD.usr_name, OLD.usr_role, OLD.user_email, USER(), 'DELETE');
END
