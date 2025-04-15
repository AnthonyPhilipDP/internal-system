SET foreign_key_checks = 0;
SHOW WARNINGS;
LOAD DATA LOCAL INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/equipment1.csv'
INTO TABLE equipment
CHARACTER SET 'latin1'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(@trans_no, @date_in, @customer_id, @equip_id, @make, @model, @serial, @descriptio, @caldate, @caldue, @coderange, @calprocedure, @prevcon, @incondition, @outcondition, @val, @temp, @humidity, @calinterval, @form, @stat, @dateout, @invoiced, @inter)
SET
    transaction_id = @trans_no,
    inDate = @date_in,
    customer_id = @customer_id,
    equipment_id = @equip_id,
    make = @make,
    model = @model,
    serial = @serial,
    description = @descriptio,
    calibrationDate = @caldate,
    calibrationDue = @caldue,
    code_range = @coderange,
    calibrationProcedure = @calprocedure,
    previousCondition = @prevcon,
    inCondition = @incondition,
    outCondition = @outcondition,
    validation = @val,
    temperature = @temp,
    humidity = @humidity,
    calibrationInterval = @calinterval,
    status = @stat,
    outDate = @dateout,
	 created_at = NOW();

/* 
    Intermediate Check, Invoiced, and Form is not yet included in this one
*/