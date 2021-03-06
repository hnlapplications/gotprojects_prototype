<?php

require_once("includes.php"); 
require("classes/phpmailer/class.phpmailer.php");
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
$users=$db->select("users", array("id", "username", "group_id", "email"));
$usergroups=$db->select("usergroups", array("id", "title"));

//get all the users for each usergroup
foreach($usergroups as &$usergroup)
{
	$usergroup->users=$db->select("users", array("id", "username", "email"), array("group_id='" . $usergroup->id . "'"));
}


//now we have an array of users, and usergroups with their users...
//run through the users
foreach($users as $user)
{
	//run through projects...
	$open_projects=array();
	foreach($projects as $project)
	{		
		$open_project=new stdClass();
		$open_project->title=$project->title;
		$open_project->checklists=$project->checklists;
		$open_project->users=$project->users;
		
		if (in_array($user->id, $open_project->users))
		{
			//the user is involved in this project.  Check if there are any fields to which they are allowed to contribute...
			$open_checklists=array();
			
			foreach($open_project->checklists as $checklist)
			{
				$list=new stdClass();
				$list->id=$checklist->id;
				$list->title=$checklist->title;
				$list->fields=$checklist->fields;
				
				$open_fields=array();
				
				foreach($list->fields as &$field)
				{
					if (in_array($user->group_id, $field->usergroups))
					{
						array_push($open_fields, $field);
					}
				}
				
				
				if (count($open_fields)>0)
				{
					$list->fields=$open_fields;
					array_push($open_checklists, $list);
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
		$email.="<strong>Project:</strong> " . $project->title . "<br /><br />";
		foreach($project->checklists as $checklist)
		{
			$email.="<ul><li>Checklist: " . $checklist->title . "</li><ul>";
			foreach($checklist->fields as $field)
			{
				$email.="<li>" . $field->title . "</li>";
			}
			$email.="</ul></ul>";
		}
	}
	$email.="</ul>";
	
	//echo "Email " . $user->username . " (" . $user->email . "): <br />" . $email . "<hr />";
	
	$mail = new PHPMailer();

	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->Host     = "mail.gotweb.co.za"; // SMTP server
	$mail->SMTPAuth   = true;
	$mail->Username     = "gotprojects@gotweb.co.za";
	$mail->Password     = "Jimly$465GoT";
	$mail->AddAddress($user->email);
	$mail->IsHTML(true); 
	$mail->Subject  = "GotProjects: Your Assigned Projects";
	$mail->Body     = "Hi, " . $user->username . "<br /><br />Here are projects that you are assigned to and that have checks that you can still complete:<br /><br />" . $email;
	$mail->WordWrap = 50;

	if(!$mail->Send()) 
	{
		echo 'Message was not sent.';
		echo 'Mailer error: ' . $mail->ErrorInfo;
	} 
	else 
	{
		echo 'Message has been sent.';
	}
	
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
		$open_project=new stdClass();
		$open_project->title=$project->title;
		$open_project->checklists=$project->checklists;
		$open_project->users=$project->users;
		
		$open_checklists=array();
		foreach($open_project->checklists as $checklist)
		{
			$list=new stdClass();
			$list->id=$checklist->id;
			$list->title=$checklist->title;
			$list->fields=$checklist->fields;
			
			$open_fields = array();
			
			
			foreach($checklist->fields as $field)
			{
				
				if (in_array($usergroup->id, $field->usergroups))
				{
					array_push($open_fields, $field);
				}
			}
			
			if (count($open_fields)>0)
			{
				$list->fields=$open_fields;
				array_push($open_checklists, $list);
			}
			
			
		}
		if (count($open_checklists)>0)
		{
			$open_project->checklists=$open_checklists;
			array_push($open_projects, $open_project);
		}
		
	}
	
	if (count($open_projects)==0)
	{
		continue;
	}
	
	$usergroup->usernames=array();
	$usergroup->useremails=array();
	foreach($usergroup->users as $user)
	{
		array_push($usergroup->usernames, $user->username);
		array_push($usergroup->useremails, $user->email);
	}
	
	//~ $email="";
	//~ foreach($open_projects as $project)
	//~ {
		//~ $email.="===================================================================<br />";
		//~ $email.="Project:<br />" . $project->title . "<br /><br />";
		//~ foreach($project->checklists as $checklist)
		//~ {
			//~ $email.="->Checklist: " . $checklist->title . "<br /><br />";
			//~ foreach($checklist->fields as $field)
			//~ {
				//~ $email.="----> " . $field->title . "<br />";
			//~ }
			//~ $email.="<br /><br />";
		//~ }
		//~ $email.="===================================================================<br />";
	//~ }
	
	$email="";
	foreach($open_projects as $project)
	{
		$email.="<strong>Project:</strong> " . $project->title . "<br /><br />";
		foreach($project->checklists as $checklist)
		{
			$email.="<ul><li>Checklist: " . $checklist->title . "</li><ul>";
			foreach($checklist->fields as $field)
			{
				$email.="<li>" . $field->title . "</li>";
			}
			$email.="</ul></ul>";
		}
	}
	$email.="</ul>";
	
	//echo "Email group: " . $usergroup->title . "(" . implode(', ', $usergroup->useremails) . "): <br />" . $email . "<hr />";
	$mail = new PHPMailer();

	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->Host     = "mail.gotweb.co.za"; // SMTP server
	$mail->SMTPAuth   = true;
	$mail->Username     = "gotprojects@gotweb.co.za";
	$mail->Password     = "Jimly$465GoT";
	foreach($usergroup->useremails as $useremail)
	{
		$mail->AddAddress($useremail);
	}
	$mail->IsHTML(true); 
	$mail->Subject  = "GotProjects: Your Group Assigned Projects";
	$mail->Body     = "Hi, <br /><br />Here are projects that are assigned to your usergroup and that have checks that you can still complete:<br /><br />" . $email;
	$mail->WordWrap = 50;
	
	echo $mail->Body . "<hr />";

	if(!$mail->Send()) 
	{
		echo 'Message was not sent.';
		echo 'Mailer error: ' . $mail->ErrorInfo;
	} 
	else 
	{
		echo 'Message has been sent.';
	}
	
}


echo ">>END<<";

?>
