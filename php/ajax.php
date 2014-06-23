<?php

session_start();
require_once("includes.php"); 
global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
switch($_POST['task'])
{
	case "login":
		login();
		break;
	case "signup":
		signUp();
		break;
	case "loadAllFields":
		loadAllFields();
		break;
	case "registerUserGroup":
		registerUserGroup();
		break;
	case "loadUserData":
		loadUserData();
		break;
	case "saveField":
		saveField();
		break;
	case "assignUserGroup":
		assignUserGroup();
		break;
	case "getField":
		echo json_encode(getField($_POST['id']));
		break;
	case "loadFieldUsergroups":
		loadFieldUsergroups();
		break;
	case "saveFieldUsergroups":
		saveFieldUsergroups();
		break;
	case "loadAllLists":
		loadAllLists();
		break;
	case "saveList":
		saveList();
		break;
	case "loadFieldLists":
		loadFieldLists();
		break;
	case "saveFieldLists":
		saveFieldLists();
		break;
	case "loadAllProjectTypes":
		loadAllProjectTypes();
		break;
	case "saveProjectType":
		saveProjectType();
		break;
	case "loadListProjectTypes":
		loadListProjectTypes();
		break;
	case "saveListPTypes":
		saveListPTypes();
		break;
	case "loadAllProjects":
		loadAllProjects();
		break;
	case "loadDefaultLists":
		loadDefaultLists();
		break;
	case "loadAllUsers":
		loadAllUsers();
		break;
	case "saveProject":
		saveProject();
		break;
	case "loadProject":
		loadProject();
		break;
	case "getStats":
		getStats();
		break;
	case "getProjectUsers":
		getProjectUsers();
		break;
	case "loadListFields":
		loadListFields();
		break;
	case "saveListFields":
		saveListFields();
		break;
	case "clearProjects":
		clearProjects();
		break;
	case "loadProjectUpdates":
		loadProjectUpdates();
		break;
	case "deleteProject":
		deleteProject();
		break;
	default:	
		Throw new Exception("AJAX Error: No function selected.");
}

function clearProjects()
{
	if ($_SESSION['group_id']!=1)
	{
		echo "You do not have permission to delete a project.";
		return;
	}
	
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	
	//clear field tables
	$db->delete("field_values", null);
	
	//clear project_users
	$db->delete("project_users", null);
	
	//clear project_list
	$db->delete("project_list", null);
	
	//clear project
	$db->delete("project", null);
	$db->delete("updates", null);
	$db->delete("notes", null);
	
	echo "ok";
}

function deleteProject()
{
	if ($_SESSION['group_id']!=1)
	{
		echo "You do not have permission to delete a project.";
		return;
	}
	
	global $db;
	
	$project_id=$_POST['project_id'];
	//get all the lists for this project
	$lists=$db->select("project_list", array("id"), array("project_id='" . $project_id . "'"));
	
	//get all the fields for this projects lists
	$fields=array();
	foreach($lists as $list)
	{
		$list_fields=$db->select("field_values", array("id"), array("list_id='" . $list->id . "'"));
		foreach($list_fields as $field)
		{
			array_push($fields, $field->id);
		}
		//delete the list
		$db->delete("project_list", array("id='" . $list->id . "'"));
	}
	
	//right, now delete the fields
	$db->delete("field_values", array("id IN (" . implode(', ', $fields) . ")"));
	
	//delete notes
	$db->delete("notes", array("project_id='" . $project_id . "'"));
	
	//delete updates
	$db->delete("updates", array("project_id='" . $project_id . "'"));
	
	//delete project_users
	$db->delete("project_users", array("project_id='" . $project_id . "'"));
	
	//delete the project
	$db->delete("project", array("id='" . $project_id . "'"));
	echo "ok";
}

function login()
{
	$username=$_POST['username'];
	$password=$_POST['password'];
	
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	
	$result=$db->select("users", array("id", "uid", "group_id", "email"), array("username='" . $username . "'", "password='" . $password . "'"));
	if ($result==null || count($result)<1 || count($result)>1)
	{
		echo json_encode(array("result"=>"fail"));
	}
	else
	{
		$_SESSION['uid']=$result[0]->id;
		$_SESSION['group_id']=$result[0]->group_id;
		$_SESSION['username']=$username;
		$_SESSION['email']=$email;
		
		echo json_encode(array("result"=>"ok"));
	}
	
}

function signUp()
{
	$username=$_POST['newusername'];
	$password=$_POST['userpassword'];
	$useremail=$_POST['useremail'];
	
	$uniqueId = time();
	
	$data=array("username"=>$username,"password"=>$password,"uid"=>$uniqueId,"email"=>$email);
	
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	
	$result=$db->insert("users", $data);
	
	/*try
	{
		$result=$db->insert("users", $data);
	}
	catch(Exception $e)
	{
		throw new Exception($e->getMessage());
	}*/
		
	if ($result)
	{
		echo json_encode(array("result"=>"ok", "id"=>$result));
	}
	else
	{
		echo json_encode(array("result"=>"fail", "id"=>$result));
	}
}

function registerUserGroup()
{
	$usergroupName=$_POST['usergroupName'];
	
	
	$data=array("title"=>$usergroupName);
	
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	
	$result=$db->insert("usergroups", $data);
	
	/*try
	{
		$result=$db->insert("users", $data);
	}
	catch(Exception $e)
	{
		throw new Exception($e->getMessage());
	}*/
		
	if ($result)
	{
		echo json_encode(array("result"=>"ok", "id"=>$result));
	}
	else
	{
		echo json_encode(array("result"=>"fail", "id"=>$result));
	}
}

function loadUsers()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$users=$db->select("users", array("*"),  null, null, null, "array");
	
	$userArray = array();
	
	foreach($users as &$row)
	{
		$name = $row['username'];
		array_push($userArray, $name);
	}
	
	return $userArray;
	//echo json_encode(array("result"=>$userArray));
}

function loadAllUsers()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$users=$db->select("users", array("*"),  null, null, null, "array");
	echo json_encode($users);
}

function loadUsersGroups()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$users=$db->select("usergroups", array("*"),  null, null, null,"array");
	
	$userGroupArray = array();
	
	foreach($users as &$row)
	{
		$name = $row['title'];
		array_push($userGroupArray, $name);
	}
	
	return $userGroupArray;
	//echo json_encode(array("result"=>$userGroupArray));
}

function loadUserData()
{
	$userDataArray = array();
	$userDataArray["users"] = loadUsers();
	$userDataArray["userGroup"] = loadUsersGroups();
	
	echo json_encode($userDataArray);
}

function loadAllFields()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$fields=$db->select("field", array("id"));
	foreach($fields as &$f)
	{
		$f=getField($f->id);
		//check which checklists this field belongs to
		$f->checklists=$db->select("field_list, list", array("list.title as title"), array("field_list.field_id='" . $f->id . "'" , "field_list.list_id=list.id"));
		$f->usergroups=$db->select("field_permissions, usergroups", array("usergroups.title as title"), array("field_permissions.field_id='" . $f->id . "'" , "field_permissions.group_id=usergroups.id"));
		
	}
	
	echo json_encode($fields);
}

function assignUserGroup()
{
	$userSelect=$_POST['userSelect'];
	$userGroupSelect=$_POST['userGroupSelect'];
	
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	
	$result=$db->select("usergroups", array("id"), array("title='" . $userGroupSelect . "'"));
	
	$ug_id=$result[0]->id;
	
	$data=array("group_id"=>$ug_id);
	$result=$db->update("users", $data, array("username='" . $userSelect . "'"));
	
	if ($result)
	{
		echo json_encode(array("result"=>"ok", "id"=>$result));
	}
	else
	{
		echo json_encode(array("result"=>"fail", "id"=>$result));
	}
	
	//echo json_encode(array("userSelect"=>$userSelect, "ug_id"=>$ug_id));
}

function saveField()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$f=json_decode($_POST['field']);
	$id=null;
	if (isset($f->id))
	{
		$id=$f->id;
	}
	$field=new Field($id, 
		$f->title,
		$f->datatype,
		$f->default_value,
		$f->searchable,
		$f->sortable,
		$f->published,
		$f->count_for_completion,
		$f->options
	);
	
	
	$field->save();
	
	
}
/*
function loadFieldUsergroups()
{
	$field_id=$_POST['id'];
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	//first, load all the user groups
	$usergroups=$db->select("usergroups", array("id", "title"));
	//loop through groups and check if the field in question is available to that group
	foreach($usergroups as &$u)
	{
		$u->allowed=false; //current usergroup is not allowed to use this field (just initializing here)
		$result=$db->select("field_permissions", array("id"), array("field_id='" . $field_id . "'", "group_id='" . $u->id . "'"));
		if (count($result)>0)
		{
			$u->allowed=true; //well what do ya know, they are allowed to use the field after all! 
		}
	}
	echo json_encode($usergroups);
}
*/

function loadFieldUsergroups()
{
	$field_id=$_POST['id'];
	global $db; 
	//first, load all the user groups
	$usergroups=$db->query("SELECT usergroups.id, usergroups.title, field_permissions.id AS permission_id
		FROM usergroups 
		LEFT JOIN field_permissions ON field_permissions.group_id = usergroups.id AND field_permissions.field_id=" . $field_id . "
		LEFT JOIN field ON field.id = field_permissions.field_id AND field.id =" . $field_id);
	//loop through groups and check if the field in question is available to that group
	foreach($usergroups as &$u)
	{
		$u->allowed=false; //current usergroup is not allowed to use this field (just initializing here)
		if ($u->permission_id!=null)
		{
			$u->allowed=true;
		}
	}
	echo json_encode($usergroups);
}

function saveFieldUsergroups()
{
	$field_id=$_POST['field_id'];
	$groups=json_decode($_POST['groups']);
	
	//delete the old stuff from the database
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$db->delete("field_permissions", array("field_id='" . $field_id . "'"));
	
	//write new values
	foreach($groups as $g)
	{
		$db->insert("field_permissions", array("field_id"=>$field_id, "group_id"=>$g));
	}
	
	echo "ok";
}

function loadAllLists()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$lists=$db->select("list", array("id", "title"));
	
	echo json_encode($lists);
}

function saveList()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$list=json_decode($_POST['list']);
	$id=null;
	if (isset($list->id))
	{
		$id=$list->id;
	}
	$checklist=new Checklist($id, $list->title);
	
	
	$checklist->save();
	echo "ok";
}
/*
function loadListFields()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$list_id=$_POST['id'];
	$fields=$db->select("field_list", array("DISTINCT(field_id) as field_id"));
	foreach($fields as &$field)
	{
		$field=getField($field->field_id);
		//right, so now we have the field... check if it is part of this list...
		$field->isPartOfList=false;
		if (count($db->select("field_list", array("id"), array("list_id='" . $list_id . "'", "field_id='" . $field->id . "'"))))
		{
			$field->isPartOfList=true;
		}
	}
	
	echo json_encode($fields);
}
* */

function loadListFields()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$list_id=$_POST['id'];
	$fields=$db->query("SELECT field.id as field_id, field_list.id as link
						FROM field
						LEFT JOIN field_list ON field_list.field_id=field.id AND field_list.list_id=" . $list_id ."
						LEFT JOIN list ON list.id=field_list.list_id AND list.id=" . $list_id . " ORDER BY field.id");
	foreach($fields as &$field)
	{
		$isPartOfList=($field->link!=null?true:false);
		$field=getField($field->field_id);
		//right, so now we have the field... check if it is part of this list...
		$field->isPartOfList=$isPartOfList;
	}
	
	echo json_encode($fields);
}
/*
function loadFieldLists()
{
	$field_id=$_POST['id'];
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$fields=array("list_id");
	$conditions=array("field_id='". $field_id . "'");
	$field_lists=$db->select("field_list", $fields, $conditions);
	
	//now we have all the lists which apply to this field
	//now load all the lists
	$all_lists=$db->select("list", array("id", "title"));
	
	//now run through $all_lists and check if each one is in the field lists, if it is, add a member variable to say that it does apply to this field.
	//at the end of the day we'll have an array of all lists which exist, with the ones that are applicable to this field marked.
	foreach($all_lists as &$list)
	{
		$list->isForField=false;
		foreach($field_lists as $f_list)
		{
			if ($f_list->list_id==$list->id)
			{
				$list->isForField=true;
			}
		}
	}
	
	echo json_encode($all_lists);
}
*/

function loadFieldLists()
{
	$field_id=$_POST['id'];
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$all_lists=$db->query("SELECT list.id, list.title, field_list.list_id as link FROM
							list
							LEFT JOIN field_list on field_list.list_id=list.id AND field_list.field_id=" . $field_id . "
							LEFT JOIN field on field_list.field_id=field.id AND field.id=" . $field_id);
	
	//now run through $all_lists and check if each one is in the field lists, if it is, add a member variable to say that it does apply to this field.
	//at the end of the day we'll have an array of all lists which exist, with the ones that are applicable to this field marked.
	foreach($all_lists as &$list)
	{
		$list->isForField=false;
		if ($list->link!=null)
		{
			$list->isForField=true;
		}
	}
	
	echo json_encode($all_lists);
}

function saveFieldLists()
{
	$field_id=$_POST['field_id'];
	$lists=json_decode($_POST['groups']);
	
	//delete the old stuff from the database
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$db->delete("field_list", array("field_id='" . $field_id . "'"));
	
	//write new values
	foreach($lists as $l)
	{
		$db->insert("field_list", array("field_id"=>$field_id, "list_id"=>$l));
	}
	
	//check if there are any dependent lists that exist... modify them too... their origin will be the list id in the lists array

	
	//get all fields and their origin lists
	$fields=$db->query("SELECT field_values.id, field_values.list_id, project_list.origin_list 
		FROM field_values
		LEFT JOIN project_list on project_list.id=field_values.list_id
		WHERE field_values.field_id=" . $field_id
	);
	
	//remove fields which should not be there
	foreach($fields as $field)
	{
		if (!in_array($field->origin_list, $lists)&&$field->origin_list!='0')
		{
			$db->delete("field_values", array("id='" . $field->id . "'"));
		}
	}
	
	//Right, we have now removed the field from spawned lists where it should no longer be.  But... We need to add it to new lists where it does not belong...
	//for each list provided, load an array of lists that were created using it as an origin.
		//check that each list has an id in this field's table.  If it doesn't, add one with defaults
	
	//first, load the field defaults just in case we need them.
	$default=$db->select("field", array("default_value"), array("id='" .$field_id."'"))[0]->default_value;
	foreach($lists as $l)
	{
		//load the lists that were created from $l
		$project_lists=$db->select("project_list", array("id"), array("origin_list='" . $l . "'"));
		//now check this field's table to see if one exists.
		foreach($project_lists as $project_list)
		{
			if (count($db->select("field_values", array("id"), array("list_id='" . $project_list->id . "'", "field_id='" . $field_id . "'")))==0)
			{
				$db->insert("field_values", array("field_id"=>$field_id, "value"=>$default, "list_id"=>$project_list->id));
			}
		}
		
	}
	
	echo "ok";
}

function loadAllProjectTypes()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$lists=$db->select("project_type", array("id", "title"));
	
	echo json_encode($lists);
}

function saveProjectType()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$projectType=json_decode($_POST['projectType']);
	$id=null;
	if (isset($projectType->id))
	{
		$id=$projectType->id;
	}
	$pType=new ProjectType($id, $projectType->title);
	
	
	$pType->save();
	echo "ok";
}

function loadListProjectTypes()
{
	$field_id=$_POST['id'];
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$fields=array("project_type_id");
	$conditions=array("list_id='". $field_id . "'");
	$list_ptypes=$db->select("default_list", $fields, $conditions);
	
	//now we have all the ptypes which apply to this list
	//now load all the ptypes
	$all_ptypes=$db->select("project_type", array("id", "title"));
	
	//now run through $all_lists and check if each one is in the field lists, if it is, add a member variable to say that it does apply to this field.
	//at the end of the day we'll have an array of all lists which exist, with the ones that are applicable to this field marked.
	foreach($all_ptypes as &$ptype)
	{
		$ptype->isForList=false;
		foreach($list_ptypes as $l_ptype)
		{
			if ($l_ptype->project_type_id==$ptype->id)
			{
				$ptype->isForField=true;
			}
		}
	}
	
	echo json_encode($all_ptypes);
}

function saveListPTypes()
{
	$list_id=$_POST['list_id'];
	$ptypes=json_decode($_POST['projectTypes']);
	
	//delete the old stuff from the database
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$db->delete("default_list", array("list_id='" . $list_id . "'"));
	
	//write new values
	foreach($ptypes as $p)
	{
		$db->insert("default_list", array("list_id"=>$list_id, "project_type_id"=>$p));
	}
	
	echo "ok";
}

function loadAllProjects()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$projects=$db->query("
		SELECT project.id, project.title, project.project_type, project_type.title as project_type_name FROM project
		LEFT JOIN project_type on project_type.id=project.project_type
	");
	
	//load all the fields that exist and count towards completion of a project so that they are abailable (don't load them again and again in the loop below, this remains constant)
	$all_fields=$db->select("field", array("id", "title"), array("count_for_completion='1'"));
	foreach($projects as &$p)
	{
		//get all the checklists for this project
		$checklists=$db->select("project_list", array("id", "title"), array("project_id='" . $p->id . "'"));	
		$project_fields=array(); //all the fields for this project
		$completedFields=0;		//all the 
		foreach($checklists as &$checklist)
		{
			//load all the fields that are applicable to this checklist
			$checklist->fields=$db->query(
				"SELECT field_values.id, field_values.value, field_values.field_id, field.title FROM field_values
				LEFT JOIN field on field.id=field_values.field_id AND field.count_for_completion=1
				WHERE field_values.list_id=" . $checklist->id
			);
			
			foreach($checklist->fields as $field)
			{
				array_push($project_fields, $field);
			}

		}
		
		//echo "we have " . count($project_fields) . " project fields<br /><br />";
		//also get the project's notes
		$p->userNotes=$db->select("notes", array("title", "value", "assigned_user"), array("count_for_completion='1'", "project_id='" . $p->id . "'"));
		
		
		//right, we now have all the fields this project.  calculate it's completion:
		//fields that are incomplete will either be blank OR contain something smaller than 1
		foreach($project_fields as $f)
		{
			if (trim($f->value)!=""&&$f->value!="0")
			{
				$completedFields++;
			}
		}
		foreach($p->userNotes as $note)
		{
			if (trim($note->value)!=""&&$note->value!="0")
			{
				$completedFields++;
			}
		}
		//echo "we have " . $completedFields . " completed fields";
		//we now know the total number of fields to this project as well as how many of them are completed.  Let's do the math.
		if (count($project_fields) + count($p->userNotes)==0)
		{
			$p->completion="n/a";
		}
		else
		{
			$p->completion=round($completedFields/(count($project_fields)+count($p->userNotes))*100, 2);
		}
		
		//calculate the GROUP COMPLETION OF THIS PROJET
		
		//we have $project_fields.  
		//Go through each one and check if the current $_SESSION['usergroup'] is allowed in it.  
		//Count them all.  Then count the number of complete ones.  Then make a percentage.
		//also list the fields which require work from this current group
		$p->allowedGroupFields=0;
		$p->allowedGroupFieldsCompleted=0;
		$p->allowedUserFields=0;
		$p->allowedUserFieldsCompleted=0;
		
		$p->groupFieldsToBeDone=array();
		$p->userFieldsToBeDone=array();
		//check if the user is assigned to this project
		$userAllowed=$db->select("project_users", array("id"), array("project_id='" . $p->id . "'", "uid='" . $_SESSION['uid'] . "'"));
		
		foreach($project_fields as $f)
		{
			if (count($result=$db->select("field_permissions", array("id"), array("field_id='" . $f->field_id . "'", "group_id='" . $_SESSION['group_id']. "'"))))
			{
				//count the number of fields to which the user and group is allowed
				$p->allowedGroupFields++;
				if ($userAllowed)
				{
					$p->allowedUserFields++;
				}
				
				//count the number of fields from the above that are complete
				if (trim($f->value)!=""&&$f->value!="0")
				{
					$p->allowedGroupFieldsCompleted++;
					if ($userAllowed)
					{
						$p->allowedUserFieldsCompleted++;
					}
				}
				else //add to the incomplete list
				{
					//get the field title
					array_push($p->groupFieldsToBeDone, $f->title);
					if ($userAllowed)
					{
						array_push($p->userFieldsToBeDone, $f->title);
					}
				}
			}
		}
		if ($p->allowedGroupFields==0)
		{
			$p->allowedGroupFieldsCompletion="n/a";
		}
		else
		{
			$p->allowedGroupFieldsCompletion=round($p->allowedGroupFieldsCompleted/$p->allowedGroupFields*100, 2);
		}
		
		//right, also get the user notes that are for this project and count towards completion...
		
		
		foreach($p->userNotes as $note)
		{
			if ($note->assigned_user!=$_SESSION['uid'])
			{
				continue;
			}
			
			$p->allowedUserFields++;
			if (trim($note->value)!="" && $note->value!=0)
			{
				$p->allowedUserFieldsCompleted++;
			}
			else
			{
				array_push($p->userFieldsToBeDone, $note->title);
			}
		}
		
		if ($p->allowedUserFields==0)
		{
			$p->allowedUserFieldsCompletion="n/a";
		}
		else
		{
			
			$p->allowedUserFieldsCompletion=round($p->allowedUserFieldsCompleted/$p->allowedUserFields*100, 2);
		}
		//END GROUP COMPLETION CALCULCATION
		
	}//foreach projects
	
	
	
	echo json_encode($projects);
}

function getProjectType($id)
{
	throw new Exception("Unimplemented function: getProjectType() in ajax.php");
}

function loadDefaultLists()
{
	//echo "starting to load<br />";
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	if (isset($_POST['project_type']))
	{
		$project_type=$_POST['project_type'];
		//~ $lists=$db->select("default_list", array("list_id"), array("project_type_id='" . $_POST['project_type'] . "'"));
		$lists=$db->query(
			"SELECT default_list.list_id, list.title, list.id as origin_list FROM default_list
				LEFT JOIN list ON list.id=default_list.list_id 
				WHERE default_list.project_type_id=" . $project_type
		);
	}
	else
		$lists=$db->select("list", array("id as list_id", "title"));
	//echo "Got lists<br />";
	
	foreach($lists as &$l)
	{
		$origin_list=(isset($l->origin_list)?$l->origin_list:$l->list_id); //remporary storage
		//echo "Creating checklist object<br />";
		$l=new Checklist($l->list_id, $l->title);
		$l->origin_list=$origin_list;
	}
	echo json_encode($lists);
}

function saveProject()
{
	
	$project=json_decode($_POST['project']);
	if (!isset($project->id) || $project->id==null)
	{
		saveNewProject($project);
	}
	else
	{
		updateProject($project);
	}
	
}

function saveNewProject($project)
{
	//insert the project
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$project_id=$db->insert("project", array(
		"title"=>$project->title,
		"project_type"=>$project->type,
	));
	
	//create the checklists
	foreach($project->checklists as $checklist)
	{
		$checklist_id=$db->insert("project_list", array(
			"title"=>$checklist->title,
			"project_id"=>$project_id,
			"origin_list"=>(isset($checklist->origin_list)&&$checklist->origin_list!=null?$checklist->origin_list:"0")
		));
		
		//insert fields for this checklist
		foreach($checklist->fields as $field)
		{
			$db->insert("field_values", array(
				"field_id"=>$field->field_id,
				"value"=>$field->value,
				"list_id"=>$checklist_id,
			));
		}
	}
	
	//save the project users
	foreach($project->users as $user)
	{
		$db->insert("project_users", array("project_id"=>$project_id, "uid"=>$user));
	}
	
	//save the project notes
	foreach($project->notes as $note)
	{
		$db->insert("notes",
		array(
			"title"			=> $note->title,
			"created_by"	=> $_SESSION['uid'],
			"assigned_user"	=> $note->user,
			"type"			=> $note->datatype,
			"value"			=> 0,
			"count_for_completion" => $note->count_for_completion,
			"options"		=> $note->options,
			"project_id"	=> $project_id
		));
	}
	
	echo "ok";
	
	
}

function updateProject($project)
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	
	writeProjectUpdates($project);
	
	//update project
	$db->update("project", array("title"=>$project->title),array("id='" . $project->id . "'"));
	
	//loop through checklists
	foreach($project->checklists as $checklist)
	{
		$listId;
		//create row in the project_list table if one does not exist.  Otherwise update the existing record
		if (!isset($checklist->id))
		{
			$listId=$db->insert("project_list", array("title"=>$checklist->title, "project_id"=>$project->id));
		}
		else //this will only be useful when we eventually decide to change the checklists names on the fly
		{
			$listId=$checklist->id;
			$db->update("project_list", array("title"=>$checklist->title), array("id='" . $checklist->id . "'"));
		}
		
		//loop through the fields... if there is no value_id, insert.  Otherwise update
		foreach($checklist->fields as $field)
		{
			if ($field->value_id==null)
			{
				$db->insert("field_values", array("field_id"=>$field->field_id, "value"=>$field->value, "list_id"=>$listId));
			}
			else
			{
				$db->update("field_values", array("value"=>$field->value), array("id='" . $field->value_id  . "'"));
			}
		}
	}
	
	//remove old users from the project so that we can save new ones
	$db->delete("project_users", array("project_id='" . $project->id . "'"));
	
	//save the project users
	foreach($project->users as $user)
	{
		$db->insert("project_users", array("project_id"=>$project->id, "uid"=>$user));
	}
	
	//save the project notes
	foreach($project->notes as $note)
	{
		if ($note->id==null)
		{
			$db->insert("notes",
			array(
				"title"			=> $note->title,
				"created_by"	=> $_SESSION['uid'],
				"assigned_user"	=> $note->user,
				"type"			=> $note->datatype,
				"value"			=> 0,
				"count_for_completion" => $note->count_for_completion,
				"project_id"	=> $project->id,
				"options"		=> json_encode($note->options)
			));
		}
		else
		{
			$db->update("notes",
			array(
				"value" => $note->value,
			), array(
				"id='" . $note->id . "'"
			));
		}
	}
	
	echo "ok";
}

function loadProject()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$projectId=$_POST['id'];
	//let's load the project.  fun eh!
	$projects=$db->select("project", array("id", "project_type", "title"), array("id='" . $projectId . "'"));
	if (count($projects)<1)
	{
		echo "no_exist";
		return;
	}
	$project=$projects[0];
	$project->checklists=$db->select("project_list", array("id", "title", "origin_list"), array("project_id='" . $projectId . "'"));
	//we now need to load the fields
	//first, get all the different kinds of fields available
	//then run through each one and look in it's appropriate table to check if a field exists for this checklist
	
	foreach($project->checklists as &$checklist)
	{
		
		
		$checklist->fields=array(); //used to store the fields for the current checklist
		
		$checklist->fields=$db->query("
		
			SELECT field_values.id, field_values.value, field_values.field_id, field.title, field.datatype, field.options FROM field_values
			LEFT JOIN field ON field.id=field_values.field_id
			WHERE field_values.list_id=" . $checklist->id
			
		);
					
			foreach($checklist->fields as &$f)
			{
				//get permissions
				$f->allowed=false;
				if ($_SESSION['group_id']==1 || count($db->select("field_permissions", array("id"), array("field_id='" . $f->field_id . "'", "group_id='" . $_SESSION['group_id'] . "'")))>0)
				{
					$f->allowed=true;
				}
			}
			
	}
	
	//also load the project's notes
	$project->notes=$db->select("notes", array("id", "title", "assigned_user as user", "type as datatype", "value", "options"), array("project_id='" . $projectId . "'"));
	
	//load users for this project
	$project->users=$db->select("users", array("*"),  null);
	$project_users=$db->select("project_users", array("uid"), array("project_id='" . $projectId . "'"));
	$project_users_array=array();
	foreach($project_users as $u)
	{
		array_push($project_users_array, $u->uid);
	}
	foreach($project->users as &$user)
	{
		$user->inProject=false;
		if (in_array($user->id, $project_users_array))
		{
			$user->inProject=true;
		}
	}
	if (count($project->users)==0)
	{
		return array();
	}
	
	echo json_encode($project);
}

function getStats()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	
	//load all the fields that exist and count towards completion of a project so that they are abailable (don't load them again and again in the loop below, this remains constant)
	
	//load all the nice little checklists for this pretty project. (Yep, this is more or less where I'm starting to get bored.
	$project_id=$_POST['id'];
	
	$checklists=$db->select("project_list", array("id", "title"), array("project_id='" . $project_id . "'"));	
	foreach($checklists as &$checklist)
	{
		//load all the fields that are applicable to this project
		$checklist->fields=array();
		$checklist->completedFields=0;
		
		//~ $checklist->fields=$db->select("field_values", array("id", "value"), array("list_id='" . $checklist->id . "'"));
		$checklist->fields=$db->query("SELECT field_values.id, field_values.value FROM field_values 
			LEFT JOIN field ON field.id=field_values.id AND field.count_for_completion=1 
			WHERE field_values.list_id=" . $checklist->id);

		//right, we now have all the fields this checklist.  calculate it's completion:
		//fields that are incomplete will either be blank OR contain something smaller than 1
		foreach($checklist->fields as $f)
		{
			if (trim($f->value)!=""&&$f->value!="0")
			{
				$checklist->completedFields++;
			}
		}
		
		$checklist->fieldCount=count($checklist->fields); //provide the count of number of complete fields separately for easy use 
		
		if ($checklist->fieldCount==0) //if there are no fields to complete then I guess we are already done huh
		{
			$checklist=null;
			//$checklist->completion="n/a";
		}
		else
		{
			$checklist->completion=round($checklist->completedFields/$checklist->fieldCount*100, 2);
		}
	}
	
	echo json_encode($checklists);
}

function getProjectUsers()
{
	$project_id=$_POST['id'];
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	//first get all the users
	$users=$db->select("users", array("*"),  null);
	
	foreach($users as &$user)
	{
		$user->inProject=false;
		if (count($db->select("project_users", array("id"), array("project_id='" . $project_id . "'", "uid='" . $user->id . "'"))))
		{
			$user->inProject=true;
		}
	}
	if (count($users)==0)
	{
		return array();
	}
	echo json_encode($users);
}

function saveListFields()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$fields=json_decode($_POST['fields']); //array of ids
	$list_id=$_POST['list']; //int
	
	//first delete all the old fields from field_list and then rewrite them
	$db->delete("field_list", array("list_id='" . $list_id . "'"));
	
	foreach($fields as $f)
	{
		$db->insert("field_list", array("field_id"=>$f, "list_id"=>$list_id));
	}
	
	
	//delete fields which are part of spawned lists and should not be.
	//first get all the fields that are part of spawned lists
	$current_fields=$db->query(
		"SELECT field_values.id, field_values.field_id FROM field_values
		LEFT JOIN project_list ON field_values.list_id=project_list.id
		WHERE project_list.origin_list=" . $list_id
	);
	
	foreach($current_fields as $f)
	{
		if (!in_array($f->field_id, $fields))
		{
			$db->delete("field_values", array("id='" . $f->id . "'"));
		}
	}
	
	//cool stuff.  
	//load all spawned lists
	
	$lists=$db->select("project_list", array("id"), array("origin_list='" . $list_id . "'"));
	foreach($lists as $list)
	{
		//run through the fields that should be in this list
		foreach($fields as $field)
		{
			//load the field defaults for if we need to insert them
			$defaults=$db->select("field", array("default_value"), array("id='" . $field . "'"))[0]->default_value;
			
			//if there are no fields matching $field in the current table matching this list, insert one
			if (count($db->select("field_values", array("id"), array("list_id='" . $list->id . "'", "field_id='" . $field . "'")))==0)
			{
				//insert $field's defaults
				$db->insert("field_values", array("field_id"=>$field, "value"=>$defaults, "list_id"=>$list->id));
			}
		}
	}
	
}

function writeProjectUpdates($project)
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	
	$data=new stdClass();
	$data->id=$project->id;
	$data->title=$project->title;
	$data->checklists=array();
	
	//loop through checklists
	foreach($project->checklists as $checklist)
	{
		$datachecklist=new stdClass();
		$datachecklist->title=$checklist->title;
		$datachecklist->id=0;
		
		if (isset($checklist->id))
		{
			$datachecklist->id=$checklist->id;
		}
		
		$datachecklist->fields=array();
		
		
		
		
		//loop through the fields... if there is no value_id, insert.  Otherwise update
		foreach($checklist->fields as $field)
		{
			$datafield=new stdClass();
			$datafield->value=$field->value;
			$datafield->field_id=$field->field_id;
			$datafield->value_id=0;
			if ($field->value_id!=null) //if this field already existed, check for a new value
			{
				$datafield->value_id=$field->value_id;
				//check the old value against this one
				$oldValue=$db->select("field_values", array("value"), array("id='" . $field->value_id . "'"))[0]->value;
				
				if ($oldValue==$field->value)
				{
					continue;
				}
				
				$datafield->id=$field->value_id;
				
				//get the field's title
				$datafield->title=$db->select("field", array("title"), array("id='" . $datafield->field_id . "'"))[0]->title;
				
				array_push($datachecklist->fields, $datafield);
				
			}
			else //this field did not exist yet, so let's put it in as an update
			{
				array_push($datachecklist->fields, $datafield);
			}
		}
		//we have collected the fields for this checklist, now add the checklist to the update if any changes were made
		if (count($datachecklist->fields))
		{
			array_push($data->checklists, $datachecklist);
		}
	}
	
	//check the users for this checklist
	$addedUsers=array();
	$removedUsers=array();
	//load the old users
	//make sure we only have an array of uids
	$oldUsersResultset=$db->select("project_users", array("id", "uid"), array("project_id='" . $project->id . "'"));
	$oldUsers=array();
	foreach($oldUsersResultset as $oldUserResult)
	{
		array_push($oldUsers, $oldUserResult->uid);
	}
	//now we have all the old users.  check this against the new users
	//see if anyone was removed
	foreach($oldUsers as $user)
	{
		if (!in_array($user, $project->users))
		{
			array_push($removedUsers, $user);
		}
	}
	
	//now check to see if anyone new was added
	foreach($project->users as $user)
	{
		if (!in_array($user, $oldUsers))
		{
			array_push($addedUsers, $user);
		}
	}
	
	$data->addedUsers=$addedUsers;
	$data->removedUsers=$removedUsers;
	
	$jsondata=json_encode($data);
	$db->insert("updates",
	array(
		"project_id"=>$project->id,
		"updates"=>$jsondata,
		"uid"=>$_SESSION['uid'],
		"date"=>date("Y-m-d H:i:s")
	));
	
	return true;
}

function loadProjectUpdates()
{
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$project_id=$_POST['id'];
	
	$updates=$db->select("updates", array("updates", "uid", "date"), array("project_id='" . $project_id . "'"), "date", "DESC");
	
	foreach($updates as &$update)
	{
		/*$update_data=$update->updates;//json_decode(json_decode($update->updates));
		print_r($update_data);
		$update->checklists=json_decode($update_data->checklists);
		foreach($update->checklists as &$checklist)
		{
			foreach($checklist->fields as &$field)
			{
				//get the field title
				$field->title=$db->select("field", array("title"), array("id='" . $field->field_id . "'"))[0]->title;
			}
		}*/
		//also get the username for the update
		$update->username=$db->select("users", array("username"), array("id='" . $update->uid . "'"))[0]->username;
	}
	
	
	echo json_encode($updates);
}

?>
