SET foreign_key_checks = 0;

-- Step 1: Alter the deleted_at column to VARCHAR
ALTER TABLE old_equipment MODIFY deleted_at VARCHAR(255);

-- Step 2: Import the data
LOAD DATA LOCAL INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/equipmentold.csv'
INTO TABLE old_equipment
CHARACTER SET 'latin1'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

-- Step 3: Update the deleted_at column to NULL
UPDATE old_equipment
SET deleted_at = NULL;

-- Step 4: Alter the deleted_at column back to TIMESTAMP
ALTER TABLE old_equipment MODIFY deleted_at TIMESTAMP NULL DEFAULT NULL;

SHOW WARNINGS;

SET foreign_key_checks = 1;