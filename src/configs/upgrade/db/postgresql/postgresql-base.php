<?php
try
{
  $utilityObj = new Utility;
  $sql = <<<SQL
  CREATE DATABASE `{$this->postgreSqlDb}` WITH OWNER `$this->postgreSqlUser` ENCODING 'UTF8';
SQL;
  $pdo = new PDO(sprintf('%s:host=%s', 'pgsql', $this->postgreSqlHost), $this->postgreSqlUser, $utilityObj->decrypt($this->postgreSqlPassword));
  $pdo->exec($sql);

  if (!postgresql_db_enum_exists("photo_type"))
  	postgresql_base("CREATE TYPE photo_type AS ENUM ('photo');");

  if (!postgresql_db_enum_exists("photo_album_type"))
  	postgresql_base("CREATE TYPE photo_album_type AS ENUM ('photo','album');");

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}action (
    idx varchar(6) NOT NULL,
    owner varchar(255) NOT NULL,
    appId varchar(255) DEFAULT NULL,
    targetid varchar(255) DEFAULT NULL,
    targettype varchar(255) DEFAULT NULL,
    email varchar(255) DEFAULT NULL,
    name varchar(255) DEFAULT NULL,
    avatar varchar(255) DEFAULT NULL,
    website varchar(255) DEFAULT NULL,
    targeturl varchar(1000) DEFAULT NULL,
    permalink varchar(1000) DEFAULT NULL,
    type varchar(255) DEFAULT NULL,
    value varchar(255) DEFAULT NULL,
    dateposted varchar(255) DEFAULT NULL,
    status integer DEFAULT NULL,
    UNIQUE(idx,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}action"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}activity (
    idx varchar(6) PRIMARY KEY,
    owner varchar(255) NOT NULL,
    appid varchar(255) NOT NULL,
    type varchar(32) NOT NULL,
    data text NOT NULL,
    permission integer DEFAULT NULL,
    datecreated integer NOT NULL
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}activity"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}admin (
    key varchar(255) PRIMARY KEY,
    value varchar(255) NOT NULL
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}admin"))
  {
  	postgresql_base($sql);
  	postgresql_base("CREATE UNIQUE INDEX key ON {$this->postgreSqlTablePrefix}admin (key);");
  }

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}album (
    idx varchar(6) PRIMARY KEY,
    owner varchar(255) NOT NULL,
    name varchar(255) NOT NULL,
    extra text,
    count integer NOT NULL DEFAULT '0',
    permission smallint NOT NULL DEFAULT '1'
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}album"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}config (
    idx varchar(255) PRIMARY KEY DEFAULT '',
    aliasof varchar(255) DEFAULT NULL,
    value text NOT NULL
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}config"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}credential (
    idx varchar(30) NOT NULL,
    owner varchar(255) NOT NULL,
    name varchar(255) DEFAULT NULL,
    image text,
    clientsecret varchar(255) DEFAULT NULL,
    usertoken varchar(255) DEFAULT NULL,
    usersecret varchar(255) DEFAULT NULL,
    permissions varchar(255) DEFAULT NULL,
    verifier varchar(255) DEFAULT NULL,
    type varchar(100) NOT NULL,
    status integer DEFAULT '0',
    UNIQUE(idx,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}credential"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}elementalbum (
    idx serial PRIMARY Key,
    owner varchar(255) NOT NULL,
    type photo_type NOT NULL,
    element varchar(6) NOT NULL DEFAULT 'photo',
    album varchar(255) NOT NULL,
    orderby smallint NOT NULL DEFAULT '0',
    UNIQUE(owner,type,element,album)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}elementalbum"))
  {
  	postgresql_base($sql);
  	postgresql_base("CREATE INDEX element ON {$this->postgreSqlTablePrefix}elementalbum (element);");
  }

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}elementgroup (
    "id" SERIAL PRIMARY Key,
    "owner" varchar(255) NOT NULL,
    "type" photo_album_type NOT NULL,
    "element" varchar(6) NOT NULL,
    "group" varchar(6) NOT NULL,
    UNIQUE("owner","type","element","group")
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}elementgroup"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}elementtag (
    idx SERIAL PRIMARY KEY,
    owner varchar(255) NOT NULL,
    tagtype photo_type NOT NULL,
    element varchar(6) NOT NULL DEFAULT 'photo',
    tag varchar(255) NOT NULL,
    UNIQUE(owner,tagtype,element,tag)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}elementtag"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}groupname (
    idx varchar(6) NOT NULL,
    owner varchar(255) NOT NULL,
    appid varchar(255) DEFAULT NULL,
    name varchar(255) DEFAULT NULL,
    permission smallint NOT NULL,
    UNIQUE(idx,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}groupname"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}groupmember (
    "id" serial PRIMARY KEY,
    "owner" varchar(255) NOT NULL,
    "group" varchar(6) NOT NULL,
    "email" varchar(255) NOT NULL,
    UNIQUE("owner","group","email")
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}groupmember"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}photo (
    idx varchar(6) NOT NULL,
    owner varchar(255) NOT NULL,
    appId varchar(255) NOT NULL,
    host varchar(255) DEFAULT NULL,
    title varchar(255) DEFAULT NULL,
    description text,
    key varchar(255) DEFAULT NULL,
    hash varchar(255) DEFAULT NULL,
    size integer DEFAULT NULL,
    width integer DEFAULT NULL,
    height integer DEFAULT NULL,
    extra text,
    exif text,
    latitude float(6) DEFAULT NULL,
    longitude float(6) DEFAULT NULL,
    views integer DEFAULT NULL,
    status integer DEFAULT NULL,
    permission integer DEFAULT NULL,
    license varchar(255) DEFAULT NULL,
    dateTaken integer DEFAULT NULL,
    dateTakenDay integer DEFAULT NULL,
    dateTakenMonth integer DEFAULT NULL,
    dateTakenYear integer DEFAULT NULL,
    dateUploaded integer DEFAULT NULL,
    dateUploadedDay integer DEFAULT NULL,
    dateUploadedMonth integer DEFAULT NULL,
    dateUploadedYear integer DEFAULT NULL,
    filenameOriginal varchar(255) DEFAULT NULL,
    pathOriginal varchar(1000) DEFAULT NULL,
    pathBase varchar(1000) DEFAULT NULL,
    groups text,
    tags text,
    UNIQUE(idx,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}photo"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}photoversion (
    idx varchar(6) NOT NULL DEFAULT '',
    owner varchar(255) NOT NULL,
    key varchar(255) NOT NULL DEFAULT '',
    path varchar(1000) DEFAULT NULL,
    UNIQUE(idx,owner,key)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}photoversion"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}tag (
    idx varchar(255) NOT NULL,
    owner varchar(255) NOT NULL,
    countPublic integer NOT NULL DEFAULT '0',
    countPrivate integer NOT NULL DEFAULT '0',
    extra text NOT NULL,
    UNIQUE(idx,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}tag"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}username (
    idx varchar(255) PRIMARY KEY,
    extra text NOT NULL
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}username)"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}webhook (
    idx varchar(6) NOT NULL,
    owner varchar(255) NOT NULL,
    appId varchar(255) DEFAULT NULL,
    callback varchar(1000) DEFAULT NULL,
    topic varchar(255) DEFAULT NULL,
    UNIQUE(idx,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}webhook"))
  	postgresql_base($sql);

  $sql = <<<SQL
    INSERT INTO {$this->postgreSqlTablePrefix}admin (key,value) 
    VALUES (:key, :value)
SQL;
  postgresql_base($sql, array(':key' => 'version', ':value' => '1.3.1'));

  return true;
}
catch(Exception $e)
{
  getLogger()->crit($e->getMessage());
  return false;
}

function postgresql_base($sql, $params = array())
{
  try
  {
    getDatabase()->execute($sql, $params);
    getLogger()->info($sql);
  }
  catch(Exception $e)
  {
    getLogger()->crit($e->getMessage()); 
    throw $e;
  }
}

function postgresql_db_table_exists($table) {
    $table = strtolower($table);
    $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_name = '$table'";
    $params = array();
    $result = getDatabase()->one($sql, $params);
    getLogger()->info("$table");
    getLogger()->info($result['count']);
    return (bool) $result['count'];
}

function postgresql_db_enum_exists($label) {
    $label = strtolower($label);
    $sql = "SELECT COUNT(*) FROM pg_type WHERE typname = '$label'";
    $params = array();
    $result = getDatabase()->one($sql, $params);
    return (bool) $result['count'];
}

