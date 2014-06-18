<?php 
	session_start();
	require_once(dirname(__FILE__) . "/php/includes.php"); 
?>
<!DOCTYPE html>
<head>
	<?php renderHead(); ?>
	<script type="text/javascript">
	$(document).ready(function()
	{
		var editId=0;
		$("#projectType-dialog").dialog(
		{
			autoOpen:false,
			height:300,
			width:300,
			modal:true,
			buttons:
			{
				"Submit":function()
				{
					saveProjectType();
					$(this).dialog("close");
				},
				Cancel:function()
				{
					$(this).dialog("close");
				}
			},
			close:function()
			{
				$(this).dialog("close");
			}
		});
		loadAllProjectTypes();
	});
	
	function createProjectType()
	{
		$("#projectType-dialog").dialog("open");
		editId=0;
	}
	
	function saveProjectType()
	{
		var projectTypeName=$("#projectType_title").val();
		if (projectTypeName===undefined || projectTypeName.trim()=="")
		{
			alert("Please enter a project type name.");
			return;
		}
		
		projectType=new Object();
		if (editId!=0)
		{
			projectType.id=projectType;
		}
		projectType.title=projectTypeName;
		
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"saveProjectType", projectType:JSON.stringify(projectType)}
		})
		.done(function(data)
		{
			if (data=="ok")
			{
				alert("Saved");
				loadAllProjectTypes();
			}
			else
			{
				alert(data);
			}
		})
		.fail(function()
		{
			alert("An error occured while saving your project type.  Please try again later.");
		});
	}
	
	function loadAllProjectTypes()
	{
		$("#message").html("Loading...").show();
		$("#projectTypeTable").hide();
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadAllProjectTypes"}
		})
		.done(function(data)
		{
			var projectTypes=$.parseJSON(data);
			if (projectTypes.length==0)
			{
				$("#message").html("There are currently no project types to display");
				return;
			}
			
			$("#projectTypeRows").html("");
			$.each(projectTypes, function(k, v)
			{
				$("#projectTypeRows").append("<tr><td>" + v.id + "</td><td>" + v.title + "</td></tr>");
			});
			$("#message").hide();
			$("#projectTypeTable").show();
		})
		.fail(function()
		{
			$("#message").html("An error occured while loading your project types.  Please try again later.");
		});
	}
	
	</script>
	
</head>
<body>
	<div class="header">
		<?php renderMenu(); ?>
	</div>
	<div class="content">
		<h1 class="page_heading">
			Project Types
		</h1>
		<button onclick="createProjectType();">New Project Type</button>
		<div id="message"></div>
		<table id="projectTypeTable">
			<thead>
				<th>ID</th>
				<th>Title</th>
			</thead>
			<tbody id="projectTypeRows">
				
			</tbody>
		</table>
		<div id="projectType-dialog">
			<p>
				Please enter a title for your new project type and click Submit to save your field.
			</p>
			<table>
				<tr>
					<td>
						Title
					</td>
					<td>
						<input type="text" id="projectType_title" />
					</td>
				</tr>
			</table>
		</div>
</body>
</html>
