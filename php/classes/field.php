<?php
//session_start(); 
require_once(dirname(__FILE__) . "/../includes.php");

class Field
{
	//member variables
	public $id;
	public $title;
	public $datatype;
	public $default_value;
	public $searchable;
	public $sortable;
	public $published;
	public $count_for_completion;
	public $options;
	public $editAllowed=false;
	
	//constructor takes all arguments and maps them to members
	function __construct($_id, $_title, $_datatype, $_default_value, $_searchable, $_sortable, $_published, $_count_for_completion, $_options)
	{
		$this->id=$_id;
		$this->title=$_title;
		$this->datatype=$_datatype;
		$this->default_value=$_default_value;
		$this->searchable=$_searchable;
		$this->sortable=$_sortable;
		$this->published=$_published;
		$this->count_for_completion=$_count_for_completion;
		$this->options=$_options;
		
		if (trim($this->default_value)=="")
		{
			switch($this->datatype)
			{
				case "string":
					$this->default_value="0";
					break;
				case "int":
					$this->default_value="0";
					break;
				case "float":
					$this->default_value="0";
					break;
				case "checkbox":
					$this->default_value="0";
					break;
				case "list":
					$this->default_value="0";
					break;
				case "user":
					$this->default_value="0";
					break;
				default:
					throw new Exception("Error creating new field data table: Data type " . $this->datatype . " is not supported.");
					break;
			}
		}
		//check if the current user is allowed to edit this field
		if (isset($this->id) && $this->id!=null)
		{
			$groups=$this->getUsergroups();
			foreach($groups as $group)
			{
				if ($group->group_id==$_SESSION['group_id'])
				{
					$this->editAllowed=true;
				}
			}
		}
	}
	
	function save()
	{
		global $db;  //$db=new HNLDB("mysql", "localhost", "ggotwebc_gotprojects", "ggotwebc_gotproj", "wq#%Z^7Z(*IM", HNLDB::ERROR_EXCEPTION);
		//if no id is set, INSERT the field to the database
		//create an array of ("column name"=>$value)
		
		
		$data=array(
			"title"=>$this->title,
			"datatype"=>$this->datatype,
			"default_value"=>$this->default_value,
			"searchable"=>$this->searchable,
			"sortable"=>$this->sortable,
			"published"=>$this->published,
			"count_for_completion"=>$this->count_for_completion,
			"options"=>$this->options,
		);
		if ($this->id==null)
		{
			$this->id=$db->insert("field", $data);
		}
		else //if an ID is set, UPDATE the field
		{
			//conditions is a string array, concatenated with AND.  If you want to use OR, include it within one of the strings
			$conditions=array("id='". $this->id . "'");
			$db->update("field", $data, $conditions);
		}
	}

	
	function getUsergroups()
	{
		global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
		$fields=array("group_id");
		$conditions=array("field_id='". $this->id . "'");
		$result=$db->select("field_permissions", $fields, $conditions);
		//$db->close();
		return $result;
	}
	
	function getLists()
	{
		global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
		$fields=array("list_id");
		$conditions=array("field_id='". $id . "'");
		$result=$db->select("field_list", $fields, $conditions);
		//$db->close();
		return $result;
	}
}

?>
