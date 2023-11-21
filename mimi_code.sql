-- View
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
CREATE PROCEDURE p_RecordQualityCheck (
    IN input_batch_id INT,
    IN input_check_type_id INT,
    IN input_check_value VARCHAR(100),
    IN input_user_id INT,
    IN input_status ENUM('in-process', 'accepted', 'rejected')
)
BEGIN
    -- Validate that the batch exists
    IF NOT EXISTS (SELECT * FROM batch WHERE batch_id = input_batch_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid batch ID.';
        LEAVE PROCEDURE;
    END IF;

    -- Validate that the check type exists
    IF NOT EXISTS (SELECT * FROM check_type WHERE ct_id = input_check_type_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid check type ID.';
        LEAVE PROCEDURE;
    END IF;

    -- Validate that the user exists
    IF NOT EXISTS (SELECT * FROM usr WHERE usr_id = input_user_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid user ID.';
        LEAVE PROCEDURE;
    END IF;

    -- Insert the new record into the chck table
    INSERT INTO chck (ct_id, usr_id, batch_id, chck_value, entry_date, modified_date)
    VALUES (input_check_type_id, input_user_id, input_batch_id, input_check_value, NOW(), NOW());

    -- Update the batch status
    UPDATE batch SET batch_status = input_status WHERE batch_id = input_batch_id;
END

CALL p_RecordQualityCheck(
    1,     
    1,      
    '5.0', 
    1,      
    'accepted' 
);

SELECT * FROM chck ORDER BY chck_id DESC LIMIT 1; 
