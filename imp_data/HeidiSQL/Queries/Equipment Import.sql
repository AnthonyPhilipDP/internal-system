SET foreign_key_checks = 0;
LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.0/Uploads/equipments.csv'
INTO TABLE equipment
CHARACTER SET 'latin1'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(
	@transaction_id,
	@inDate, 
	@customer_id, 
	@poNoCalibration, 
	@poNoRealign, 
	@poNoRepair, 
	@prNo, 
	@equipment_id, 
	@make, 
	@model, 
	@`description`, 
	@`serial`, 
	@calibrationDate, 
	@calibrationDue, 
	@code_range, 
	@calibrationProcedure, 
	@previousCondition, 
	@inCondition, 
	@outCondition, 
	@`validation`,
	@temperature,
	@humidity,
	@calibrationInterval,
	@worksheet,
	@`reference`,
	@category,
	@service,
	@`status`,
	@comments,
	@outDate,
	@oldInspection,
	@oldAccessories,
	@ar_id,
	@DR_No2,
	@DR,
	@RP,
	@rep,
	@certify,
	@DAILY_rep,
	@Item_No,
	@standardsUsed,
	@Invoiced,
	@certifiyWithSpaces,
	@DR_No3,
	@drNoDocument,
	@Dr_No4,
	@Num_Pages,
	@Remarks,
	@PriorityRemarks,
	@intermediateCheck,
	@calibrationType,
	@laboratory,
	@documentReleasedDate,
	@DrNoDocReleased,
	@assignedTo,
	@documentReceivedBy
)
SET 
	transaction_id = @transaction_id,
	inDate = @inDate,
	customer_id = @customer_id,
	poNoCalibration = @poNoCalibration,
	poNoRealign = @poNoRealign,
	poNoRepair = @poNoRepair,
	prNo = @prNo,
	equipment_id = @equipment_id,
	make =  @make,
	model = @model,
	`description` = @`description`,
	`serial` = @`serial`,
	calibrationDate = @calibrationDate,
	calibrationDue = @calibrationDue,
	code_range = @code_range,
	calibrationProcedure = @calibrationProcedure,
	previousCondition = @previousCondition,
	inCondition = @inCondition,
	outCondition = @outCondition,
	`validation` = @`validation`,
	temperature = @temperature,
	humidity = @humidity,
	calibrationInterval = @calibrationInterval,
	worksheet =  @worksheet,
	`reference` = @`reference`,
	category = @category,
	service = @service,
	`status` = @`status`,
	comments = @comments,
	outDate = @outDate,
	oldInspection = @oldInspection,
	oldAccessories = @oldAccessories,
	ar_id = @ar_id,
	DR_No2 = @DR_No2,
	DR = @DR,
	RP =  @RP,
	rep =  @rep,
	certify = @certify,
	DAILY_rep = @DAILY_rep,
	Item_No = @Item_No,
	standardsUsed = @standardsUsed,
	Invoiced = @Invoiced,
	certifiyWithSpaces = @certifiyWithSpaces,
	DR_No3 = @DR_No3,
	drNoDocument = @drNoDocument,
	Dr_No4 = @Dr_No4,
	Num_Pages = @Num_Pages,
	Remarks = @Remarks,
	PriorityRemarks = @PriorityRemarks,
	intermediateCheck = @intermediateCheck,
	calibrationType = @calibrationType,
	laboratory = @laboratory,
	documentReleasedDate = @documentReleasedDate,
	DrNoDocReleased = @DrNoDocReleased,
	assignedTo = @assignedTo,
	documentReceivedBy = @documentReceivedBy,
	created_at = NOW();
SET foreign_key_checks = 1;

UPDATE equipment
SET calibrationDue = 
    CASE 
        WHEN calibrationDue = '' THEN NULL
        ELSE DATE_FORMAT(STR_TO_DATE(calibrationDue, '%d/%m/%Y'), '%Y-%m-%d')
    END;

#CSV Formatting Instructions

#Replace Characters
	-- Convert all commas (,) to spaces.
	-- Change all backslashes (\) to vertical bars (|).
	-- Replace all semicolons (;) with spaces.
	-- In customer_id, change all the blank to 1.
	-- In intermediateCheck, NO = 0, YES = 1, Blanks = 0.

#Data Formatting
	-- Set all values to TEXT format, except for dates and ID fields.
	-- Keep dates unchanged.
	-- Ensure IDs are numeric only, remove any special characters if present.
	-- In boolean types of data, change false to 0 and true to 1.