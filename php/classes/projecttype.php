<?php

require_once(dirname(__FILE__) . "/../includes.php");

class ProjectType
{
	private $id;
	private $title;
	
	function __construct($_id, $_title)
	{
		$this->id=$_id;
		$this->title=$_title;
	}
	
	function save()
	{
		global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
		if ($this->id==null) //if the id of this object is null, INSERT it to the database
		{
			$data=array(
				"title"=>$this->title
			);
			$db->insert("project_type", $data);
			//$db->close();
		}
		else //if this object already has an id, simply update it
		{
			$data=array(
				"title"=>$this->title
			);
			$conditions=array("id='". $this->id . "'");
			$db->update("project_type", $data, $conditions);
			//$db->close();
		}
	}
	
	function getTitle($id)
	{
		return $this->title;
	}
	
	function getDefaultLists()
	{
		global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
		$fields=array("list_type_id");
		$conditions=array("project_type_id='". $this->id . "'");
		$result=$db->select("default_list", $fields, $conditions);		
		return $result;
	}
}

?>
