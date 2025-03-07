UPDATE equipment
SET inspection = IF(JSON_VALID(inspection), inspection, CONCAT('{"value": "', REPLACE(REPLACE(inspection, '"', '\"'), '\n', '\\n'), '"}'));

ALTER TABLE equipment MODIFY COLUMN inspection JSON;
