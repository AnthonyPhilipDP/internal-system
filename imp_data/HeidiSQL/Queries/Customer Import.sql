SET foreign_key_checks = 0;

LOAD DATA LOCAL INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/customers.csv'
INTO TABLE customers
CHARACTER SET 'latin1'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(id, name, address, mobile1, mobile2, telephone1, telephone2, email, website, sec, vat, withHoldingTax, businessNature, qualifyingSystem, certifyingBody, dateCertified, payment, status, remarks, businessStyle, tin, createdDate, nickname);
    
/* 
    
*/