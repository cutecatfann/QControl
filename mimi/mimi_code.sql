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

-- Function: For each product type: see the ratio of pass fail
CREATE FUNCTION get_quality_check_ratio()
RETURNS TEXT
DETERMINISTIC
BEGIN
    DECLARE result TEXT DEFAULT '';
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur_pt_id INT;
    DECLARE cur_pt_name TEXT;
    DECLARE cur_ratio TEXT;
    DECLARE cur CURSOR FOR 
        SELECT pt.pt_id, pt.pt_name
        FROM product_type pt;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO cur_pt_id, cur_pt_name;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SELECT CONCAT(
                'Product Type: ', cur_pt_name, ', Pass/Fail Ratio: ',
                IFNULL(SUM(CASE WHEN b.batch_status = 'accepted' THEN 1 ELSE 0 END), 0) / 
                GREATEST(IFNULL(SUM(CASE WHEN b.batch_status = 'rejected' THEN 1 ELSE 0 END), 1), 1)
            ) INTO cur_ratio
        FROM batch b
        WHERE b.pt_id = cur_pt_id
        GROUP BY b.pt_id;

        SET result = CONCAT(result, cur_ratio, '\n');
    END LOOP;

    CLOSE cur;

    RETURN result;
END

-- Tigger:  t_NewUserTest
-- Description: When new users are added to the database, this trigger verifies that their data has the correct formatting and length. It will check for Role selection (is it a valid role), and proper email regex. If there is an incorrect value it will reject the entry.
CREATE TRIGGER validate_new_user
BEFORE INSERT ON usr
FOR EACH ROW
BEGIN
    -- Check if role is valid
    IF NEW.usr_role NOT IN ('q_manager', 'q_lead', 'q_tech') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid role selected';
    END IF;

    -- Check email format (basic regex for email validation)
    IF NOT NEW.user_email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid email format';
    END IF;
END
