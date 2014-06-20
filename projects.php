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
		 loadAllProjects();
		 
		 $("#message_modal").dialog(
		 {
			 autoOpen:false,
			 modal:true,
			 width:400,
			 height:400,
			 buttons:
			 {
				 "Close":function()
				 {
					 $(this).dialog("close");
				 }
			 },
			 close:function()
			 {
				 $(this).dialog("close");
			 }
		 });
		 
		 $("#updates_modal").dialog(
		 {
			 autoOpen:false,
			 modal:true,
			 width:700,
			 height:600,
			 buttons:
			 {
				 "Close":function()
				 {
					 $(this).dialog("close");
				 }
			 },
			 close:function()
			 {
				 $(this).dialog("close");
			 }
		 });
	});	
	
	function loadAllProjects()
	{
		$("#message").html("loading...").show();
		$("#projects_table").hide();
		
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadAllProjects"}
		})
		.done(function(data)
		{
			var projects=$.parseJSON(data);
			if (projects.length==0)
			{
				$("#message").html("There are currently no projects to display.");
				return;
			}
			
			$.each(projects, function (k, v)
			{
				$("#projects_rows").append("<tr><td>" + v.id + "</td><td>" + v.title + "</td><td>" + v.project_type_name + "</td><td> <button class='group_stats_btn' data-group-to-be-done='" +  v.groupFieldsToBeDone +"'>Group: " + v.allowedGroupFieldsCompletion + (v.allowedGroupFieldsCompletion=="n/a"?"":"%") + "</button><button class='user_stats_btn' data-group-to-be-done='" +  v.userFieldsToBeDone +"'>You: " + v.allowedUserFieldsCompletion + (v.allowedUserFieldsCompletion=="n/a"?"":"%") +"</button><button onclick='viewStats(" + v.id + ", &apos;" + v.title + "&apos;);'>Total: " + v.completion + " %</button></td><td><button onclick='edit(" + v.id + ");'>Edit</button><button onclick='viewUpdates(" + v.id + ");'>View Updates</button></td></tr>");
			});
			$("#message").hide();
			$("#projects_table").show();
			
			$(".group_stats_btn").click(function()
			{
				html="Your group still needs to do the following:<br /><br />" + $(this).data("group-to-be-done").replace(new RegExp(',', 'g'), "<br />");
				$("#message_modal").html(html).dialog("open");
			});
			$(".user_stats_btn").click(function()
			{
				html="You still need to do the following:<br /><br />" + $(this).data("group-to-be-done").replace(new RegExp(',', 'g'), "<br />");
				$("#message_modal").html(html).dialog("open");
			});
		})
		.fail(function()
		{
			alert("An error occured while loading your projects.");
		});
		
	}
	
	function edit(id)
	{
		location.href="project.php?id=" + id;
	}
	
	function viewStats(id, title)
	{
		$("#message_modal").html("Loading...").dialog("open");;
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"getStats", id:id}
		})
		.done(function(data)
		{
			var html="<strong>Project Statistics: " + title + "</strong><table>";
			var checklists=$.parseJSON(data);
			$.each(checklists, function(k, v)
			{
				if (v===null)
				{
					return;
				}
				html+="<tr><td>" + v.title + "</td><td>" + v.completion + "% (" + v.completedFields + "/" + v.fieldCount + ")" + "</td></tr>";
			});
			html+="</table>";
			$("#message_modal").html(html);
		})
		.fail(function()
		{
			alert("Something bad happened while we were trying to fetch the statistics for this project.  Sorry about that.");
		});
	}
	
	function viewUpdates(id)
	{
		$("#updates_modal").html("Loading...").dialog("open");
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadProjectUpdates", id:id}
		})
		.done(function(data)
		{
			var updates=$.parseJSON(data);
			var html="";
			
			$.each(updates, function (k, update)
			{
				update.updates=$.parseJSON(update.updates);
				console.log(update.updates.checklists);
				if (update.updates.checklists.length==0)// || update.updates.addedUsers.length>0 || update.updates.removedUsers.length>0)) //uncomment this to check for users as well
				{
					return;
				}
				html+="<div class='update_container'>";
					html+="<strong>" + update.date + " ("+ update.username +")</strong><hr />";
						//show checklist updates
						html+="<div class='update_checklist'>";
							$.each(update.updates.checklists, function(k, checklist)
							{
								html+="Checklist: " + checklist.title + ":<br /><ul>";
								$.each(checklist.fields, function(k, field)
								{
									html+="<li>" + field.title + " changed to " + field.value + "</li>";
								});
								html+="</ul><hr />";
							});
						html+="</div>";

				html+="</div>";
			});
			if (html=="")
			{
				html="There are no updates for this project.";
			}
			$("#updates_modal").html(html);
		})
		.fail(function()
		{
			alert("Something bad happened while we were trying to fetch the updates for this project.  Sorry about that.");
		});
	}
	
	function deleteProject(id)
	{
		if (!confirm("Are you sure you want to delete this project? This action can NOT be undone."))
		{
			return;
		}
		//if we reach this point, we know that the user clicked OK...
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"deleteProject", project_id:id}
		})
		.done(function(data)
		{
			if (data!="ok")
			{
				alert(data);
			}
			else
			{
				alert("Project deleted.");
				location.reload();
			}
		})
		.fail(function()
		{
			alert("An error occured while attempting to delete your project");
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
			Projects
		</h1>
		<a class="link_button" href="project.php">New Project</a>
		<div id="message"></div>
		<table id="projects_table" style="clear:both">
			<thead>
				<th>ID</th>
				<th>Title</th>
				<th>Type</th>
				<th>Completion</th>
				<th>Actions</th>
			</thead>
			<tbody id="projects_rows">
				
			</tbody>
		</table>
	</div>
	<div id="message_modal">
	</div>
	<div id="updates_modal">
	</div>
</body>
</html>
