DROP FUNCTION IF EXISTS column_exists;

DELIMITER $$
CREATE FUNCTION column_exists(
  tname VARCHAR(64),
  cname VARCHAR(64)
)
  RETURNS BOOLEAN
  BEGIN
    RETURN 0 < (SELECT COUNT(*)
                FROM `INFORMATION_SCHEMA`.`COLUMNS`
                WHERE `TABLE_SCHEMA` = SCHEMA()
                      AND `TABLE_NAME` = tname
                      AND `COLUMN_NAME` = cname);
  END $$
DELIMITER ;

-- drop_column_if_exists:

DROP PROCEDURE IF EXISTS drop_column_if_exists;

DELIMITER $$
CREATE PROCEDURE drop_column_if_exists(
  tname VARCHAR(64),
  cname VARCHAR(64)
)
  BEGIN
    IF column_exists(tname, cname)
    THEN
      SET @drop_column_if_exists = CONCAT('ALTER TABLE `', tname, '` DROP COLUMN `', cname, '`');
      PREPARE drop_query FROM @drop_column_if_exists;
      EXECUTE drop_query;
    END IF;
  END $$
DELIMITER ;