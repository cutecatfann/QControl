

-- View v_BatchItems
create view v_ItemsStage as
select
    serial_number,
    (select stage_id from batch where batch.batch_id = item.batch_id) as production_stage
    from item;


-- Procedure p_UpdateBatch
create procedure p_UpdateBatch(in b int, in s int
begin
	declare past_stage int;
	declare new_stage_prod_type int;
	declare batch_prod_type int;

    select stage_id into past_stage from batch where batch_id = b;
    select pt_id into new_stage_prod_type from stage where stage_id = s;
    select pt_id into batch_prod_type from batch where batch_id = b;

    update batch
        set stage_id = s
        where batch_id = b and (new_stage_prod_type = batch_prod_type);
end

-- Function f_TimeSinceBatchCreation
create function f_TimeSinceBatchCreation(in b int)
begin
    select DATEDIFF(CURRENT_DATE(), creation_date) as DaysSinceCreated from batch where batch_id = b;
end

-- Trigger t_BatchUpdateLastModified
create trigger t_BatchUpdateLastModified
	after update on chck
	for each row
	update chck set modified_date = curdate() where old.chck_id = chck_id;
