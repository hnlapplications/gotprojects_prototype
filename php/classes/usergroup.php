<?php

require_once(dirname(__FILE__) . "/../includes.php");

class Usergroup
{
	private $id;
	private $title;
	
	function __construct($_id, $_title)
	{
		$id=$_id;
		$title=$_title;
	}
	
	function save()
	{
		global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
		if ($this->id==null)//if the id of this object is null, INSERT it to the database
		{
			$data=array(
				"id"=>$this->id,
				"title"=>$this->title
			);
			$db->insert("usergroups", $data);
			//$db->close();
		}
		else //if this object already has an id, simply update it
		{
			$data=array(
				"title"=>$this->title
			);
			$conditions=array("id='". $id . "'");
			$db->update("usergroups", $data, $conditions);
			//$db->close();
		}
	}
	
	function getTitle($id)
	{
		global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
		$conditions=array("id='". $id . "'");
		$result=$db->select("usergroups", array("title"), $conditions);
		$db->close();
		return $result->id;
	}
}

?>
