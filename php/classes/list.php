<?php

require_once(dirname(__FILE__) . "/../includes.php");

class Checklist
{
	public $id;
	public $title;
	public $fields;
	
	
	function __construct($_id, $_title)
	{
		$this->id=$_id;
		$this->title=$_title;
		
		//set up fields
		$this->fields=$this->getFields();
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
			$db->insert("list", $data);
			//$db->close();
		}
		else //if this object already has an id, simply update it
		{
			$data=array(
				"title"=>$this->title
			);
			$conditions=array("id='". $id . "'");
			$db->update("list", $data, $conditions);
			//$db->close();
		}
	}
	
	function getTitle($id)
	{
		global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
		$conditions=array("id='". $id . "'");
		$result=$db->select("list", array("title"), $conditions);
		//$db->close();
		return $result->id;
	}
	
	function getFields()
	{
		//echo "Getting fields for " . $this->id . "<br />";
		global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
		$fields=array("field_id");
		$conditions=array("list_id='". $this->id . "'");
		$result=$db->select("field_list", $fields, $conditions);
		//echo "Got field list for this list <br />";
		//return the actual fields rather than just their id's
		foreach($result as &$r)
		{
			//echo "Getting field " . $r->field_id . "<br />";
			$field=getField($r->field_id);
			//echo "ran getField()<br />";
			$r=new Field(
				$field->id, 
				$field->title, 
				$field->datatype, 
				$field->default_value, 
				$field->searchable, 
				$field->sortable, 
				$field->published, 
				$field->count_for_completion,
				$field->options
			);
			$r->editAllowed=$field->allowed;
			//echo "Got field...<br />";
			
			
		}
		
		return $result;
	}
}

?>
