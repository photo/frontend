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
    id varchar(6) NOT NULL,
    owner varchar(255) NOT NULL,
    appId varchar(255) DEFAULT NULL,
    targetId varchar(255) DEFAULT NULL,
    targetType varchar(255) DEFAULT NULL,
    email varchar(255) DEFAULT NULL,
    name varchar(255) DEFAULT NULL,
    avatar varchar(255) DEFAULT NULL,
    website varchar(255) DEFAULT NULL,
    targetUrl varchar(1000) DEFAULT NULL,
    permalink varchar(1000) DEFAULT NULL,
    type varchar(255) DEFAULT NULL,
    value varchar(255) DEFAULT NULL,
    datePosted varchar(255) DEFAULT NULL,
    status integer DEFAULT NULL,
    UNIQUE(id,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}action"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}activity (
    id varchar(6) PRIMARY KEY,
    owner varchar(255) NOT NULL,
    appId varchar(255) NOT NULL,
    type varchar(32) NOT NULL,
    data text NOT NULL,
    permission integer DEFAULT NULL,
    dateCreated integer NOT NULL
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
    id varchar(6) PRIMARY KEY,
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
    id varchar(255) PRIMARY KEY DEFAULT '',
    aliasOf varchar(255) DEFAULT NULL,
    value text NOT NULL
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}config"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}credential (
    id varchar(30) NOT NULL,
    owner varchar(255) NOT NULL,
    name varchar(255) DEFAULT NULL,
    image text,
    clientSecret varchar(255) DEFAULT NULL,
    userToken varchar(255) DEFAULT NULL,
    userSecret varchar(255) DEFAULT NULL,
    permissions varchar(255) DEFAULT NULL,
    verifier varchar(255) DEFAULT NULL,
    type varchar(100) NOT NULL,
    status integer DEFAULT '0',
    UNIQUE(id,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}credential"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}elementAlbum (
    id serial PRIMARY Key,
    owner varchar(255) NOT NULL,
    "type" photo_type NOT NULL,
    element varchar(6) NOT NULL DEFAULT 'photo',
    album varchar(255) NOT NULL,
    "order" smallint NOT NULL DEFAULT '0',
    UNIQUE(owner,type,element,album)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}elementAlbum"))
  {
  	postgresql_base($sql);
  	postgresql_base("CREATE INDEX element ON {$this->postgreSqlTablePrefix}elementAlbum (element);");
  }

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}elementGroup (
    id SERIAL PRIMARY Key,
    owner varchar(255) NOT NULL,
    "type" photo_album_type NOT NULL,
    element varchar(6) NOT NULL,
    "group" varchar(6) NOT NULL,
    UNIQUE(owner,type,element,"group")
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}elementGroup"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}elementTag (
    id SERIAL PRIMARY KEY,
    owner varchar(255) NOT NULL,
    "type" photo_type NOT NULL,
    element varchar(6) NOT NULL DEFAULT 'photo',
    tag varchar(255) NOT NULL,
    UNIQUE(owner,"type",element,tag)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}elementTag"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE "{$this->postgreSqlTablePrefix}group" (
    id varchar(6) NOT NULL,
    owner varchar(255) NOT NULL,
    appId varchar(255) DEFAULT NULL,
    name varchar(255) DEFAULT NULL,
    permission smallint NOT NULL,
    UNIQUE(id,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}group"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}groupMember (
    id serial PRIMARY KEY,
    owner varchar(255) NOT NULL,
    "group" varchar(6) NOT NULL,
    email varchar(255) NOT NULL,
    UNIQUE(owner,"group",email)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}groupMember"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}photo (
    id varchar(6) NOT NULL,
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
    UNIQUE(id,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}photo"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}photoVersion (
    id varchar(6) NOT NULL DEFAULT '',
    owner varchar(255) NOT NULL,
    key varchar(255) NOT NULL DEFAULT '',
    path varchar(1000) DEFAULT NULL,
    UNIQUE(id,owner,key)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}photoVersion"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}tag (
    id varchar(255) NOT NULL,
    owner varchar(255) NOT NULL,
    countPublic integer NOT NULL DEFAULT '0',
    countPrivate integer NOT NULL DEFAULT '0',
    extra text NOT NULL,
    UNIQUE(id,owner)
  );
SQL;
  if (!postgresql_db_table_exists("{$this->postgreSqlTablePrefix}tag"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE "{$this->postgreSqlTablePrefix}user" (
    id varchar(255) PRIMARY KEY,
    extra text NOT NULL
  );
SQL;
  if (!postgresql_db_table_exists("$this->postgreSqlTablePrefix}user"))
  	postgresql_base($sql);

  $sql = <<<SQL
  CREATE TABLE {$this->postgreSqlTablePrefix}webhook (
    id varchar(6) NOT NULL,
    owner varchar(255) NOT NULL,
    appId varchar(255) DEFAULT NULL,
    callback varchar(1000) DEFAULT NULL,
    topic varchar(255) DEFAULT NULL,
    UNIQUE(id,owner)
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

