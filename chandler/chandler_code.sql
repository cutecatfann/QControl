use f23_qualityControl;
drop function if exists f_CheckPassFail;
create function f_CheckPassFail(i_chck_id int)
returns bool deterministic
return 
	(select case when chck_value > lower_bound and chck_value < upper_bound then 1 else 0 end from
		chck left join check_type on chck.ct_id=check_type.ct_id where chck_id=i_chck_id);

-- View 
-- This view shows you checks with their associated batch information, which enables the other view

drop view if exists v_BatchQualityStatus;
drop view if exists v_BatchChcks;
create view v_BatchChcks as
select 
	product_type.pt_name, 
	batch.batch_id, 
    check_type.ct_name, 
    check_type.lower_bound,
    check_type.upper_bound,
    chck.chck_id, 
    chck.chck_value, 
	f_CheckPassFail(chck.chck_id) as pass
	from batch inner join chck on batch.batch_id=chck.batch_id
           left join check_type on chck.ct_id=check_type.ct_id
           left join stage on check_type.stage_id=stage.stage_id
           left join product_type on stage.pt_id=product_type.pt_id
	order by batch.batch_id, stage.stage_id, check_type.ct_id, chck.chck_id;
    
-- this shows you batches and their pass/fail check ratio
create view v_BatchQualityStatus as
select
	batch_id,
    pt_name,
    avg(pass) as pass_ratio
    from v_BatchChcks group by batch_id order by batch_id;

-- Procedure
delimiter //


-- function
-- creates a batch of the desired product type in a coherent state
-- that is, in process, and in the earliest stage of production
drop procedure if exists p_CreateBatch;
CREATE PROCEDURE p_CreateBatch (
	in i_pt_id int)
proc_label:BEGIN
    -- Validate that the product type exists
    IF i_pt_id not in (SELECT pt_id FROM product_type) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid product id';
        LEAVE proc_label;
    END IF;
	insert into batch (pt_id, stage_id, batch_status) values
					  (i_pt_id, 
                      (select stage_id from stage where
						pt_id=i_pt_id and prev_stage_id is null),
					  'in-process');
END//
delimiter ;
-- call p_CreateBatch((select pt_id from product_type where pt_name="Cake"));
-- Function: For each product type: see the ratio of pass fail


-- Tigger:  t_NewUserTest
/*
CREATE TRIGGER validate_new_user
BEFORE INSERT ON usr
FOR EACH ROW
BEGIN
    -- Check email format (basic regex for email validation)
    IF NOT NEW.user_email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid email format';
    END IF;
END
*/
