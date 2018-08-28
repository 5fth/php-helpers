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
CREATE TABLE IF NOT EXISTS hello_world ( id INT AUTO_INCREMENT PRIMARY KEY);
CALL drop_column_if_exists('hello_world', 'name');
CALL drop_column_if_exists('hello_world', 'birth_year');
CALL drop_column_if_exists('hello_world', 'last_name');
ALTER TABLE hello_world ADD name VARCHAR(5);
ALTER TABLE hello_world ADD birth_year VARCHAR(4);
ALTER TABLE hello_world ADD last_name VARCHAR(5);
INSERT INTO hello_world (name,birth_year) VALUES ('Fatih','1995');
INSERT INTO hello_world (name,birth_year) VALUES ('Yigit','1990');
INSERT INTO hello_world (last_name,birth_year) VALUES ('Konur','1990');
