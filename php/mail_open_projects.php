<?php

require_once("includes.php"); 
global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);

//get all the projects in the system
$projects=$db->select("project", array("id","title"));

//go through each project
foreach($projects as &$project)
{
	//get the checklists for this project
	$project->checklists=array();
	$checklists=$db->select("project_list", array("id", "title"), array("project_id='" . $project->id . "'"));
	//get the fields that count for completion for each checklist
	foreach($checklists as &$checklist)
	{
		$checklist->fields=$db->query(
			"SELECT field_values.id, field_values.field_id, field.title FROM field_values
			LEFT JOIN field ON field.id=field_values.field_id
			WHERE field.count_for_completion=1 AND field_values.list_id='" . $checklist->id . "' AND (field_values.value='0' OR field_values.value='' OR field_values.value=NULL)"
		);
		if ($checklist->fields!=null || count($checklist->fields)>0)
		{
			//get the usergroups for each field...
			foreach($checklist->fields as &$field)
			{
				$field_usergroups=$db->select("field_permissions", array("group_id"), array("field_id='" . $field->field_id . "'"));
				$field->usergroups=array(); //the 1 is used to init the array with the super user
				foreach($field_usergroups as $fieldug)
				{
					array_push($field->usergroups, $fieldug->group_id);
				}
				if (!in_array('1', $field->usergroups))
				{
					array_push($field_usergroups, '1'); //add the Super User group to the field if it is not added yet
				}
			}
			array_push($project->checklists, $checklist);
		}
	}	
	
	//also get the notes that count but are not complete for this project
	$project->notes=$db->query("SELECT id, title FROM notes WHERE count_for_completion='1' AND (value=0 OR value='' OR value=NULL)");
	
	//get the users for this project
	$projectusers=$db->select("project_users", array("uid"), array("project_id='" . $project->id . "'"));
	$project->users=array();
	foreach($projectusers as $pu)
	{
		array_push($project->users, $pu->uid);
	}
	
}



$temp=$projects;
$projects=array();
foreach($temp as $project)
{
	if (count($project->checklists)>0 || count($project->notes)>0)
	{
		array_push($projects, $project);
	}
}
//print out the projects that have incomplete fields

if (count($projects)==0)
{
	die("There are no projects to send an email for.");
}

unset($temp);

//~ print_r($projects);
//right, now we need to get all the users and usergroups...
$users=$db->select("users", array("id", "username", "group_id"));
$usergroups=$db->select("usergroups", array("id", "title"));

//get all the users for each usergroup
foreach($usergroups as &$usergroup)
{
	$usergroup->users=$db->select("users", array("id", "username"), array("group_id='" . $usergroup->id . "'"));
}


//now we have an array of users, and usergroups with their users...
//run through the users
//~ print_r($projects);
foreach($users as $user)
{
	//run through projects...
	$open_projects=array();
	foreach($projects as $project)
	{
		//~ echo "<br /><br />" . print_r($projects, true) . "<br /><br />";
		
		$open_project=$project;
		if (in_array($user->id, $project->users))
		{
			//the user is involved in this project.  Check if there are any fields to which they are allowed to contribute...
			$open_checklists=array();
			//~ echo "<br /><br />00000000000000000000000000000000000000000000<br /><br />" . print_r($projects, true) . "<br />0000000000000000000000000000<br />";
			foreach($project->checklists as $checklist)
			{
				$list=$checklist;
				$open_fields=array();
				foreach($checklist->fields as $field)
				{
					if (in_array($user->group_id, $field->usergroups))
					{
						array_push($open_fields, $field);
					}
				}
				if (count($open_fields)>0)
				{
					$open_checklist=$checklist;
					$open_checklist->fields=$open_fields;
					array_push($open_checklists, $open_checklist);
				}
			}
			
			if (count($open_checklists)>0)
			{
				$open_project->checklists=$open_checklists;
				array_push($open_projects, $open_project);
			}
			
		}
		
	}
	
	if (count($open_projects)==0)
	{
		continue;
	}
	
	$email="";
	foreach($open_projects as $project)
	{
		$email.="===================================================================<br />";
		$email.="Project:<br />" . $project->title . "<br /><br />";
		foreach($project->checklists as $checklist)
		{
			$email.="->Checklist: " . $checklist->title . "<br /><br />";
			foreach($checklist->fields as $field)
			{
				$email.="----> " . $field->title . "<br />";
			}
			$email.="<br /><br />";
		}
		$email.="===================================================================<br />";
	}
	
	
	//~ TODO: Uncomment the line below.  We know that this works.
	echo "Email " . $user->username . ": <br />" . $email . "<hr />";
	
}

//cool stuff... Nwow compile emails for usergroups...
foreach($usergroups as &$usergroup)
{
	echo "<br /><br />Processing usergroup... " . $usergroup->title . "... ";
	if (count($usergroup->users)==0)
	{
		//~ echo "<br />Sending nothing to usergroup " . $usergroup->title . " because it contains no users<br />";
		continue;
	}
	
	
	$open_projects=array();
	foreach($projects as $project)
	{
		$open_checklists=array();
		foreach($project->checklists as $checklist)
		{
			$open_fields = array();
			foreach($checklist->fields as $field)
			{
				//~ echo "Now on field: " . $field->field_id . ": gid is " . $usergroup->id . " AND usergroups are " . print_r($field->usergroups, true) . "<br /><br /><br />";
				if (in_array($usergroup->id, $field->usergroups))
				{
					array_push($open_fields, $field);
				}
			}
			if (count($open_fields)>0)
			{
				$checklist->fields=$open_fields;
				array_push($open_checklists, $checklist);
			}
		}
		if (count($open_checklists)>0)
		{
			$project->checklists=$open_checklists;
			array_push($open_projects, $project);
		}
		
	}
	
	if (count($open_projects)==0)
	{
		continue;
	}
	
	$usergroup->usernames=array();
	foreach($usergroup->users as $user)
	{
		array_push($usergroup->usernames, $user->username);
	}
	
	$email="";
	foreach($open_projects as $project)
	{
		$email.="===================================================================<br />";
		$email.="Project:<br />" . $project->title . "<br /><br />";
		foreach($project->checklists as $checklist)
		{
			$email.="->Checklist: " . $checklist->title . "<br /><br />";
			foreach($checklist->fields as $field)
			{
				$email.="----> " . $field->title . "<br />";
			}
			$email.="<br /><br />";
		}
		$email.="===================================================================<br />";
	}
	
	echo "Email group: " . $usergroup->title . "(" . implode(', ', $usergroup->usernames) . "): <br />" . $email . "<hr />";
	
}

echo ">>END<<";

?>
