<?php

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

/***********************************************************************
 * 
 * hnldb.php
 * Author: JP Meyer - HNL Applications
 * Author Email: jp@hnlapp.co.za
 * Author Phone: +27 12 665 2537
 * 
 * Description:
 * ====================================================================
 * This class serves as a simple wrapper for the PDO class in PHP,
 * including functions for basic database functions:
 * -> Insert
 * -> Update
 * -> Select
 * -> Delete
 * 
 * 
 * 
 * 
 * ********************************************************************/

class HNLDB
{
	const ERROR_SILENT = PDO::ERRMODE_SILENT;
	const ERROR_WARNING = PDO::ERRMODE_WARNING;
	const ERROR_EXCEPTION = PDO::ERRMODE_EXCEPTION;
	
	//database details
	private $type;		//mysql, pgsql, etc
	private $host;		//host
	private $dbname;	//database name
	private $user;		//username
	private $pass;		//password
	
	private $dbh;		//database handle
	private $sth;		//statement handle
	private $error_mode;	//error mode
	
	
	
	function __construct($type, $host, $dbname, $user, $pass, $error_mode)
	{
		if (trim($type)=="")
		{
			throw new Exception("HNLDB Error: No database type provided.  (ie. mysql, etc)");
		}
		if (trim($host)=="")
		{
			throw new Exception("HNLDB Error: No hostname provided.");
		}
		if (trim($dbname)=="")
		{
			throw new Exception("HNLDB Error: No database specified.");
		}
		if (trim($user)=="")
		{
			throw new Exception("HNLDB Error: No database user specified.");
		}
		if (gettype($pass)=="NULL")
		{
			throw new Exception("HNLDB Error: No database password specified.");
		}
		if (!isset($error_mode)||$error_mode==null)
		{
			throw new Exception("HNLDB Error: No error mode specified.");
		}
		if (!($error_mode==self::ERROR_SILENT||$error_mode==self::ERROR_WARNING||$error_mode==self::ERROR_EXCEPTION))
		{
			throw new Exception("HNLDB Error: Invalid error mode specified.");
		}
		
		
		$this->type=$type;
		$this->host=$host;
		$this->dbname=$dbname;
		$this->user=$user;
		$this->pass=$pass;
		$this->error_mode=$error_mode;
		
		try
		{
			$this->connect();
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
		
	}
	
	private function connect()
	{
		try
		{
			switch($this->type)
			{
				case "mysql":
					$this->dbh=new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->user, $this->pass);
					$this->dbh->setAttribute(PDO::ATTR_ERRMODE, $this->error_mode);
					break;
				default:
					throw new Exception("Unsupported database type: " . $this->type);
					break;
			}
		}
		catch(PDOException $e)
		{
			throw new Exception($e->getMessage());
		}
	}
	
	public function close()
	{
		$this->dbh=null;
	}
	
	
	
	public function insert($table, $data)
	{
		/******************
		 * 
		 * $table: the name of the table (string)
		 * $data: Key=>value array containing data to be inserted
		 * 			key represents the name of the column where
		 * 			it's data will be inserted.
		 * 
		 * **************/
		 
		 if (gettype($table)!="string")
		 {
			 throw new Exception("HNLDB Error: Table is expected to be a string");
		 }
		 if (gettype($data)!="array")
		 {
			 throw new Exception("HNLDB Error: Data is expected to be a key value array.  Keys should be names of columns, and their values should be the values to update.");
		 }
		 
		 $columns=array();
		 $named_placeholders=array();
		 foreach($data as $k=>$v)
		 {
			 array_push($columns, $k);
			 array_push($named_placeholders, ":" . $k);
		 }
		 
		 $columns=implode(", ", $columns);
		 $named_placeholders=implode(", ", $named_placeholders);
		 
		 $statement="INSERT INTO " . $table . "(" . $columns . ") VALUES (" . $named_placeholders . ")";
		 $this->sth=$this->dbh->prepare($statement);
		 $this->sth->execute($data);
		 
		 return $this->dbh->lastInsertId();
		 
	}
	
	public function update($table, $data, $conditions)
	{
		/******************
		 * 
		 * $table: the name of the table (string)
		 * $data: Key=>value array containing data to be inserted
		 * 			key represents the name of the column where
		 * 			it's data will be inserted.
		 * $conditions:  array of STRING conditions
		 * 
		 * **************/
		 
		 if (gettype($table)!="string")
		 {
			 throw new Exception("HNLDB Error: Table is expected to be a string");
		 }
		 if (gettype($data)!="array")
		 {
			 throw new Exception("HNLDB Error: Data is expected to be a key value array.  Keys should be names of columns, and their values should be the values to update.");
		 }
		 if (gettype($conditions)!="array"&&gettype($conditions)!="NULL")
		 {
			 throw new Exception("HNLDB Error: Conditions parameter is expected to be an array of strings");
		 }
		 
		 if (gettype($conditions)!="NULL")
		 {
			 foreach($conditions as $c)
			 {
				 if (gettype($c)!="string")
				 {
					 throw new Exception("HNLDB Error: Conditions parameter is expected to be an array of strings");
				 }
			 }
		 }
		 
		 $columns=array();
		 $named_placeholders=array();
		 foreach($data as $k=>$v)
		 {
			 array_push($columns, $k."=:".$k);
		 }
		 
		 $columns=implode(", ", $columns);
		 
		 $statement="UPDATE " . $table . " SET " . $columns;
		 if (gettype($conditions)!="NULL")
		 {
			 $statement.=" WHERE " . implode(" AND ", $conditions);
		 }
		 $this->sth=$this->dbh->prepare($statement);
		 return $this->sth->execute($data);
		 
	}
	
	public function select($table, $columns, $conditions=null, $sortCol=null, $sortDirn=null ,$mode="object")
	{
		if ($mode!="object"&&$mode!="array")
		{
			throw new Exception("HNLDB Error: Mode is expected to be either object or array.");
		}
		
		if (gettype($table)!="string")
		{
			throw new Exception("HNLDB Error: Table is expected to be a string");
		}
		if (gettype($conditions)!="array"&&gettype($conditions)!="NULL")
		{
			throw new Exception("HNLDB Error: Conditions parameter is expected to be an array of strings");
		}

		$statement="SELECT " . implode(", ", $columns) . " FROM " . $table;
		if (gettype($conditions)!="NULL")
		{
			$statement .= " WHERE " . implode(" AND " , $conditions);
		}
		
		if ($sortCol!=null)
		{
			$statement .= " ORDER BY " . $sortCol;
			if ($sortDirn!=null)
			{
				$statement .= " " . $sortDirn;
			}
		}
		
		try
		{ 
			
			$this->sth=$this->dbh->prepare($statement);
			$this->sth->execute();
			if ($mode=="object")
			{
				return $this->sth->fetchAll(PDO::FETCH_OBJ);
			}
			else
			{
				return $this->sth->fetchAll();
			}
		}
		catch (PDOException $e)
		{
			Throw new Exception("HNLDB Error: SQL: " . $statement . " e---> "  . print_r($e, true));
		}
		
	}
	
	public function query($query, $mode="object")
	{
		try
		{ 
			$this->sth=$this->dbh->prepare($query);
			$this->sth->execute();
			if ($mode=="object")
			{
				return $this->sth->fetchAll(PDO::FETCH_OBJ);
			}
			else
			{
				return $this->sth->fetchAll();
			}
		}
		catch (PDOException $e)
		{
			Throw new Exception("HNLDB Error: SQL: " . $query . " e---> "  . print_r($e, true));
		}
	}
	
	public function delete($table, $conditions)
	{
		if (gettype($table)!="string")
		{
			throw new Exception("HNLDB Error: Table is expected to be a string");
		}
		if (gettype($conditions)!="array"&&gettype($conditions)!="NULL")
		{
			throw new Exception("HNLDB Error: Conditions parameter is expected to be an array of strings");
		}
		$statement="DELETE FROM " . $table;
		if (gettype($conditions)!="NULL")
		{
			$statement .= " WHERE " . implode($conditions);
		}
		
		$this->sth=$this->dbh->prepare($statement);
		$this->sth->execute();
	}
	
	//NEW: CREATE TABLE
	/******************
	 * 
	 * $name: the name of the table (string)
	 * $columns: an array containing the columns, ie:
	 * 		array("id int NOT NULL auto_incrememnt primary key",
	 * 				"title" varchar(250),
	 * 			);
	 * 
	 * **************/
	public function createTable($name, $columns)
	{
		
		$statement="CREATE TABLE IF NOT EXISTS " .$name . " (" .implode(", ", $columns) . " )";
		 
		$this->sth=$this->dbh->prepare($statement);
		$this->sth->execute();
	}
	
	public function renameTable($oldName, $newName)
	{
		$statement="RENAME TABLE " .$oldName . " TO " . $newName;
		 
		$this->sth=$this->dbh->prepare($statement);
		$this->sth->execute();
	}
}
?>
