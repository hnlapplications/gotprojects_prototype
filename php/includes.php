<?php
//session_start();
error_reporting(E_ERROR | E_PARSE);
if (basename($_SERVER['PHP_SELF'])!="login.php" && basename($_SERVER['PHP_SELF']&& basename($_SERVER['PHP_SELF']) !="mail_open_projects.php" && (!isset($_SESSION['uid']) || $_SESSION['uid']<1 || !isset($_SESSION['group_id']) || $_SESSION['group_id']<0))
{
	//echo basename($_SERVER['PHP_SELF']) . " has no session uid set!";
	header("Location: login.php");
}
else
{
	//echo "uid is " .  $_SESSION['uid'];
}

require_once("classes/hnldb.php");
require_once("classes/usergroup.php");
require_once("classes/field.php");
require_once("classes/list.php");
require_once("classes/projecttype.php");

$db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);

function getField($id) //get the details of the field
{
	//This function just get's the properties of a field and returns them as an OBJECT (hnldb's setting is "obj" by default)
	global $db; // $db=new HNLDB("mysql", "localhost", "gotprojects", "root", "", HNLDB::ERROR_EXCEPTION);
	$fields=array("id", "title", "datatype", "default_value", "searchable", "sortable", "published", "count_for_completion", "options");
	$conditions=array("id='". $id . "'");
	$result=$db->select("field", $fields, $conditions);

	
	
	if (count($result)>0)
	{
		$field=$result[0];
		//check if this field is allowed for the current user
		$field->allowed=false;
		if ($_SESSION['group_id']==1 || count($db->select("field_permissions", array("id"), array("field_id='" . $field->id . "'", "group_id='" . $_SESSION['group_id'] . "'")))>0) //1 is superuser
		{
			$field->allowed=true;
		}
		return $field; //result contains an array, which contains a single element... we only want that element
	}
	else
	{
		//~ return array(); //return a blank array
		return null;
	}
}

function renderHead()
{
	?>
	
	<!-- jquery and jquery-ui -->
	<script type="text/javascript" src="js/jquery-ui/js/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="js/jquery-ui/js/jquery-ui-1.10.4.js"></script>
	<link rel="stylesheet" type="text/css" href="js/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.css">
	
	<!-- Google Font -->
	<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,300italic,300,400italic,500,500italic,700italic,700' rel='stylesheet' type='text/css'>
	
	<!-- custom css and js-->
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript" src="js/project_functions.js"></script>
	<script type="text/javascript">
		function clearProjects()
		{
			$("#clear_projects_button").html("Clearing Projects...");
			$.ajax(
			{
				url:"php/ajax.php",
				type:"POST",
				data:{task:"clearProjects"}
			})
			.done(function(data)
			{
				if (data=="ok")
				{
					if (location.pathname.substring(location.pathname.lastIndexOf("/") + 1)=="project.php")
					{
						window.location="projects.php";
					}
					else
					{
						location.reload();
					}
				}
			})
			.fail(function()
			{
				alert("Couldn't clear projects.");
			});
		}
	</script>
	<?php
}

function renderMenu()
{
	?>
	<div class="menu">
		<ul>
			<li>
				<a href="admin.php">Usergroups</a>
			</li>
			<li>
				<a href="fields.php">Fields</a>
			</li>
			<li>
				<a href="lists.php">Checklists</a>
			</li>
			<li>
				<a href="project_types.php">Project Types</a>
			</li>
			<li>
				<a href="projects.php">Projects</a>
			</li>
			<li>
				<a href="logout.php">Log Out</a>
			</li>
			<li><a id="clear_projects_button" onclick="clearProjects();">CLEAR PROJECTS</a></li>
		</ul>
		<div class="greeting">
			<?php echo "Hi, " . $_SESSION['username']; ?>
		</div>
	</div>
	<?php
	
}

?>
