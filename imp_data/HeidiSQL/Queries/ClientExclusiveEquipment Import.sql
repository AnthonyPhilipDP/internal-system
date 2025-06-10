DROP TEMPORARY TABLE IF EXISTS temp_data;

CREATE TEMPORARY TABLE temp_data (
    transaction_id BIGINT,
    exclusive_id VARCHAR(255)
);

LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/clientExclusiveEquipment.csv'
INTO TABLE temp_data
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Clean up the unwanted \r characters
UPDATE temp_data
SET exclusive_id = REPLACE(exclusive_id, '\r', '');

CREATE INDEX idx_transaction ON equipment(transaction_id);
CREATE INDEX idx_temp_transaction ON temp_data(transaction_id);

UPDATE equipment
SET exclusive_id = (
    SELECT exclusive_id FROM temp_data
    WHERE temp_data.transaction_id = equipment.transaction_id
    LIMIT 1
)
WHERE exclusive_id IS NULL OR exclusive_id = '';


DROP TEMPORARY TABLE temp_data;

UPDATE equipment
SET isClientExclusive = 1
WHERE exclusive_id IS NOT NULL AND exclusive_id != '';
