<?php
$commands = $errors = array();
$owner = getConfig()->get('user')->email;

  // DROP INDEXes
$commands[] = $sql = <<<SQL
  ALTER TABLE op_action DROP PRIMARY KEY
SQL;
mysql_1_2_1($sql);
//
$commands[] = $sql = <<<SQL
  ALTER TABLE op_action DROP INDEX id
SQL;
mysql_1_2_1($sql);


// ALTER TABLE to add owner column to action table
$commands[] = $sql = <<<SQL
  ALTER TABLE `op_action` ADD `owner` VARCHAR( 255 ) NOT NULL AFTER `id`
SQL;
mysql_1_2_1($sql);

// CREATE INDEX
$commands[] = $sql = <<<SQL
  ALTER TABLE `openphoto_upgrade`.`op_action` ADD UNIQUE (
  `id` ,
  `owner`
  )
SQL;
mysql_1_2_1($sql);



function mysql_1_2_1($sql)
{
  global $errors;
  try
  {
    getDatabase()->execute($sql);
    getLogger()->info($sql);
  }
  catch(Exception $e)
  {
    getLogger()->crit($e->getMessage()); 
  }
}
