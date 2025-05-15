SET foreign_key_checks = 0;
LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/customers.csv'
INTO TABLE customers
CHARACTER SET 'latin1'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(@customer_id, @name, @address, @telephone1, @telephone2, @email, @website, @sec, @vat, @withHoldingTax, @businessNature, @qualifyingSystem, @certifyingBody, @dateCertified, @payment, @status, @remarks, @businessStyle, @tin, @createdDate)
SET 
	customer_id = @customer_id,
	`name` = @name,
	address = @address,
	telephone1 = @telephone1,
	telephone2 = @telephone2,
	email = @email,
	website = @website,
	sec = @sec,
	vat = @vat,
	withHoldingTax = @withHoldingTax,
	businessNature = @businessNature,
	qualifyingSystem = @qualifyingSystem,
	certifyingBody = @certifyingBody,
	dateCertified = @dateCertified,
	payment = @payment,
	`status` = @status,
	remarks = @remarks,
	businessStyle = @businessStyle,
	tin = @tin,
	createdDate = @createdDate,
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