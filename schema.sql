use f23_qualityControl;


-- Ensure that there are no conflicting tables.
drop table if exists usr_audit;
drop table if exists chck;
drop table if exists usr;
drop table if exists item;
drop table if exists batch;
drop table if exists check_type;
drop table if exists stage;
drop table if exists product_type;





-- user table with user details and roles
CREATE TABLE usr (
	usr_id int primary key auto_increment,
    usr_name text not null,
    usr_role ENUM('q_manager', 'q_lead', 'q_tech') not null,
    creation_date datetime default CURRENT_TIMESTAMP not null,
    modified_date datetime on update CURRENT_TIMESTAMP not null,
    pword_hash text not null,
    user_email text not null
    );

-- Table for different product types
create table product_type (
	pt_id int primary key auto_increment,
    pt_name text not null unique,
    pt_desc text not null,
    pt_creation_date datetime default CURRENT_TIMESTAMP not null
);

-- Table to define various stages in production
create table stage (
	stage_id int primary key auto_increment,
    stage_name text not null,
    pt_id int not null,
    prev_stage_id int unique, -- unique ensure that you end up with a linked list and not a tree, null indicates the first stage
    foreign key (prev_stage_id) references stage(stage_id),
    foreign key (pt_id) references product_type(pt_id)
);
 
 -- Table for different types of checks or tests performed
create table check_type (
	ct_id int primary key auto_increment,
    ct_name text not null,
    ct_desc text not null,
    stage_id int not null,
    percent_check float not null,
    lower_bound float not null,
    upper_bound float not null,
    foreign key (stage_id) references stage(stage_id)
);

-- Table for batches of products
create table batch (
	batch_id int primary key auto_increment,
    pt_id int not null,
    stage_id int not null,
    creation_date datetime default CURRENT_TIMESTAMP not null,
    batch_status enum('in-process', 'accepted', 'rejected') not null,
    foreign key (pt_id) references product_type(pt_id),
    foreign key (stage_id) references stage(stage_id)
    );
    
-- Table for individual items in a batch
create table item (
	serial_number int primary key auto_increment,
    batch_id int not null,
    foreign key (batch_id) references batch(batch_id)
); 

-- Table for recording checks or tests performed
create table chck (
	chck_id int primary key auto_increment,
    ct_id int not null,
    usr_id int not null,
    batch_id int not null,
    chck_value float not null,
    entry_date datetime default CURRENT_TIMESTAMP not null,
    modified_date datetime on update CURRENT_TIMESTAMP not null,
    foreign key (ct_id) references check_type(ct_id),
    foreign key (usr_id) references usr(usr_id),
    foreign key (batch_id) references batch(batch_id)
);

-- Simple audit table to keep track of all changes made to the usr table via triggers
CREATE TABLE usr_audit (
    audit_id INT PRIMARY KEY AUTO_INCREMENT,
    usr_name TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    usr_role ENUM('q_manager', 'q_lead', 'q_tech') NOT NULL,
    user_email TEXT NOT NULL,
    action_performed_by TEXT NOT NULL,
    action_type ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL
);

-- Insert statements to populate product_type table
insert into product_type (pt_name, pt_desc) values ('Chocolate Chip', 'Chocolate Chip Cookies');
insert into product_type (pt_name, pt_desc) values ('Cake', 'Cake with frosting');

-- Insert statements to populate stage table
insert into stage (stage_name, pt_id, prev_stage_id) values ('Mixing', (select pt_id from product_type where pt_name='Chocolate Chip'), null);
insert into stage (stage_name, pt_id, prev_stage_id) values ('Baking', (select pt_id from product_type where pt_name='Chocolate Chip'), 
	(select stage_id from stage as s where stage_name='Mixing' and pt_id=(select pt_id from product_type where pt_name='Chocolate Chip')));
insert into stage (stage_name, pt_id, prev_stage_id) values ('Packaging', (select pt_id from product_type where pt_name='Chocolate Chip'),
	(select stage_id from stage as s where stage_name='Baking' and pt_id=(select pt_id from product_type where pt_name='Chocolate Chip')));
insert into stage (stage_name, pt_id, prev_stage_id) values ('Mixing', (select pt_id from product_type where pt_name='Cake'), null);
insert into stage (stage_name, pt_id, prev_stage_id) values ('Baking', (select pt_id from product_type where pt_name='Cake'),
	(select stage_id from stage as s where stage_name='Mixing' and pt_id=(select pt_id from product_type where pt_name='Cake')));
insert into stage (stage_name, pt_id, prev_stage_id) values ('Packaging', (select pt_id from product_type where pt_name='Cake'),
	(select stage_id from stage as s where stage_name='Baking' and pt_id=(select pt_id from product_type where pt_name='Cake')));

-- Insert statements to populate check_type table with specific checks for each product and stage
insert into check_type (ct_name, ct_desc, stage_id, percent_check, lower_bound, upper_bound)
	values ('Weight Before Baking', 'Mass of dough after baking and before mixing', 
			(select stage_id from stage where stage_name='Mixing' and 
			pt_id=(select pt_id from product_type where pt_name='Chocolate Chip')),
            0.1, 0.5, 0.7);
insert into check_type (ct_name, ct_desc, stage_id, percent_check, lower_bound, upper_bound)
	values ('Viscosity', 'Viscosity of mixed dough', 
			(select stage_id from stage where stage_name='Mixing' and 
			pt_id=(select pt_id from product_type where pt_name='Chocolate Chip')),
            0.1, 0.5, 0.7);
insert into check_type (ct_name, ct_desc, stage_id, percent_check, lower_bound, upper_bound)
	values ('Internal Temp', 'Internal temp immediately after removal from oven', 
			(select stage_id from stage where stage_name='Baking' and 
			pt_id=(select pt_id from product_type where pt_name='Chocolate Chip')),
            0.1, 0.5, 0.7);
insert into check_type (ct_name, ct_desc, stage_id, percent_check, lower_bound, upper_bound)
	values ('Color', 'Reflectance after baking', 
			(select stage_id from stage where stage_name='Baking' and 
			pt_id=(select pt_id from product_type where pt_name='Chocolate Chip')),
            0.1, 0.5, 0.7);
insert into check_type (ct_name, ct_desc, stage_id, percent_check, lower_bound, upper_bound)
	values ('Final weight', 'Weight of final cookie in packaging', 
			(select stage_id from stage where stage_name='Packaging' and 
			pt_id=(select pt_id from product_type where pt_name='Chocolate Chip')),
            0.1, 0.5, 0.7);
            
            
            
-- Insert statements to populate check_type table with specific checks for each product and stage
insert into check_type (ct_name, ct_desc, stage_id, percent_check, lower_bound, upper_bound)
	values ('Weight Before Baking', 'Mass of batter after mixing and before baking', 
			(select stage_id from stage where stage_name='Mixing' and 
			pt_id=(select pt_id from product_type where pt_name='Cake')),
            0.1, 0.4, 0.6);
insert into check_type (ct_name, ct_desc, stage_id, percent_check, lower_bound, upper_bound)
	values ('Viscosity', 'Viscosity of mixed batter', 
			(select stage_id from stage where stage_name='Mixing' and 
			pt_id=(select pt_id from product_type where pt_name='Cake')),
            0.1, 0.4, 0.6);
insert into check_type (ct_name, ct_desc, stage_id, percent_check, lower_bound, upper_bound)
	values ('Internal Temp', 'Internal temp immediately after removal from oven', 
			(select stage_id from stage where stage_name='Baking' and 
			pt_id=(select pt_id from product_type where pt_name='Cake')),
            0.1, 0.4, 0.6);
insert into check_type (ct_name, ct_desc, stage_id, percent_check, lower_bound, upper_bound)
	values ('Color', 'Reflectance after baking', 
			(select stage_id from stage where stage_name='Baking' and 
			pt_id=(select pt_id from product_type where pt_name='Cake')),
            0.1, 0.4, 0.6);
insert into check_type (ct_name, ct_desc, stage_id, percent_check, lower_bound, upper_bound)
	values ('Final weight', 'Weight of final cake with frosting, in box', 
			(select stage_id from stage where stage_name='Packaging' and 
			pt_id=(select pt_id from product_type where pt_name='Cake')),
            0.1, 0.4, 0.6);

-- Insert statements to populate batch table
insert into batch (pt_id, stage_id, batch_status) values
	((select pt_id from product_type where pt_name='Chocolate Chip'),
     (select stage_id from stage where stage_name='Mixing' and
     pt_id=(select pt_id from product_type where pt_name='Chocolate Chip')),
     'in-process');
insert into batch (pt_id, stage_id, batch_status) values
	((select pt_id from product_type where pt_name='Chocolate Chip'),
     (select stage_id from stage where stage_name='Baking' and
     pt_id=(select pt_id from product_type where pt_name='Chocolate Chip')),
     'in-process');
insert into batch (pt_id, stage_id, batch_status) values
	((select pt_id from product_type where pt_name='Chocolate Chip'),
     (select stage_id from stage where stage_name='Packaging' and
     pt_id=(select pt_id from product_type where pt_name='Chocolate Chip')),
     'accepted');
insert into batch (pt_id, stage_id, batch_status) values
	((select pt_id from product_type where pt_name='Cake'),
     (select stage_id from stage where stage_name='Mixing' and
     pt_id=(select pt_id from product_type where pt_name='Cake')),
     'in-process');
insert into batch (pt_id, stage_id, batch_status) values
	((select pt_id from product_type where pt_name='Cake'),
     (select stage_id from stage where stage_name='Baking' and
     pt_id=(select pt_id from product_type where pt_name='Cake')),
     'in-process');
insert into batch (pt_id, stage_id, batch_status) values
	((select pt_id from product_type where pt_name='Cake'),
     (select stage_id from stage where stage_name='Packaging' and
     pt_id=(select pt_id from product_type where pt_name='Cake')),
     'accepted');


-- Inserting user records
insert into usr (usr_name, usr_role, pword_hash, user_email) values ('Chandler', 'q_tech', '11111', 'campbellr@sou.edu');
insert into usr (usr_name, usr_role, pword_hash, user_email) values ('Mimi', 'q_manager', '22222', 'pieperm@sou.edu');
insert into usr (usr_name, usr_role, pword_hash, user_email) values ('Alex Johnson', 'q_tech', 'a1b2c3', 'alexj@example.com');
insert into usr (usr_name, usr_role, pword_hash, user_email) values ('Samantha Lee', 'q_manager', 's4m5n6', 'samanthal@example.com');
insert into usr (usr_name, usr_role, pword_hash, user_email) values ('Raj Patel', 'q_lead', 'r7a8j9', 'rajpatel@example.com');
insert into usr (usr_name, usr_role, pword_hash, user_email) values ('Emily Zhang', 'q_tech', 'e0m1z2', 'emilyz@example.com');
insert into usr (usr_name, usr_role, pword_hash, user_email) values ('Luis Garcia', 'q_manager', 'l3g4c5', 'luisg@example.com');

-- Setting random variables to insert a random check record
set @pt = (select pt_id from product_type order by RAND() limit 1);
set @stage = (select stage_id from stage where pt_id=@pt order by RAND() limit 1);
set @ct = (select ct_id from check_type where stage_id=@stage order by RAND() limit 1);
set @batch = (select batch_id from batch where pt_id=@pt order by RAND() limit 1);
set @usr = (select usr_id from usr order by RAND() limit 1);
insert into chck (ct_id, usr_id, batch_id, chck_value) values
	(@ct, @usr, @batch, rand()*0.25+0.5);






