SET GLOBAL local_infile = 1;
SET foreign_key_checks = 0;

UPDATE customers
SET deleted_at = NULL;

UPDATE equipment
SET deleted_at = NULL;