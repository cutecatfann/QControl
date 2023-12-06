use usr_campbellr_0;

drop trigger if exists t_InsertBatchReject;
drop trigger if exists t_UpdateBatchReject;
drop trigger if exists t_DeleteBatchReject;
drop procedure if exists p_UpdateBatchStatus;
drop function if exists f_CheckPassFail;




-- Function
-- This returns whether or not a check passed 
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
    batch.batch_status,
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
    count(chck_id) as check_count,
    avg(pass) as pass_ratio, 
    batch_status 
    from v_BatchChcks group by batch_id order by batch_id;

-- Procedure
delimiter //


-- procedure
-- creates a batch of the desired product type in a coherent state
-- that is, in process, and in the earliest stage of production
drop procedure if exists p_CreateBatch;
CREATE PROCEDURE p_CreateBatch (
	in i_pt_name text)
proc_label:BEGIN
    -- Validate that the product type exists
    IF i_pt_name not in (SELECT pt_name FROM product_type) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid product id';
        LEAVE proc_label;
    END IF;
	insert into batch (pt_id, stage_id, batch_status) values
					  ((select pt_id from product_type where pt_name=i_pt_name), 
                      (select stage_id from stage where
						pt_id=(select pt_id from product_type where pt_name=i_pt_name) and prev_stage_id is null),
					  'in-process');
END//
delimiter ;
-- call p_CreateBatch("Cake");
-- Function: For each product type: see the ratio of pass fail



-- this enables the following triggers 
delimiter //
create procedure p_UpdateBatchStatus ()
begin
	update batch set batch_status = 'rejected' 
	where batch_status = 'in-process' and batch_id in 
    (select batch_id from v_BatchQualityStatus where pass_ratio < 0.8);
end //
delimiter ;

-- Trigger: changes a batch status to rejected if >20% of checks fail. 

delimiter //
CREATE TRIGGER t_InsertBatchReject
AFTER INSERT ON chck
FOR EACH ROW
BEGIN
	call p_UpdateBatchStatus();

END //
delimiter ;

delimiter //
CREATE TRIGGER t_UpdateBatchReject
AFTER INSERT ON chck
FOR EACH ROW
BEGIN
	call p_UpdateBatchStatus();
END //
delimiter ;

delimiter //
CREATE TRIGGER t_DeleteBatchReject
AFTER INSERT ON chck
FOR EACH ROW
BEGIN
	call p_UpdateBatchStatus();
END //
delimiter ;