SET foreign_key_checks = 0;

#Client Exlusive Import Query
LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/clientExclusive.csv'
INTO TABLE client_exclusives
CHARACTER SET 'latin1'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(@customer_id, @exclusive_id, @`name`, @address)
SET 
	customer_id = @customer_id,
	exclusive_id = @exclusive_id,
	`name` = @`name`,
	address = @address,
	created_at = NOW();
	
SET foreign_key_checks = 1;

#CSV Formatting Instructions

#Replace Characters
	-- Convert all commas (,) to spaces.
	-- Change all backslashes (\) to vertical bars (|).
	-- Replace all semicolons (;) with spaces.

#Data Formatting
	-- Set all values to TEXT format, except for date and ID fields.
	-- Dates must remain unchanged.
	-- Ensure IDs are numeric only—remove any special characters if present.
	
#Others
	-- Remove the parenthesis of the telephone numbers.	
	-- Change " to nothing
	-- Remove new line in row 8 and 15