<?php 
	session_start();
	require_once(dirname(__FILE__) . "/php/includes.php"); 
	$disabled=(isset($_GET['id'])?"disabled":""); //disable inputs if necessary
?>
<!DOCTYPE html>
<head>
	<?php renderHead(); ?>
	<script type="text/javascript">
		
	var listRows=""; //used to generate HTML when adding items to each list
	var projectId=<?php echo (isset($_GET['id'])?$_GET['id']:"null"); ?>;
	var users="";
	var list_counter=0;
	$(document).ready(function()
	{
		//load all the users
		//this is just in case we have a users field so that we already have them ready when they are needed
		$("#loading_text").html("Loading users...");
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadAllUsers"}
		})
		.done(function(data)
		{
			users=$.parseJSON(data);
			prepareInterface();
		})
		.fail(function()
		{
			alert("Failed to load users...");
		});
		
		
		/*
			DIALOGS
		*/
		//custom list dialog
		$("#loading_text").html("Setting up interface...");
		$("#custom-list-dialog").dialog(
		{
			autoOpen:false,
			height:500,
			width:500,
			modal:true,
			buttons:
			{
				"Add":function()
				{
					addCustomLists();
				},
				Cancel:function()
				{
					
					$(this).dialog("close");
				},
				
			},
			close:function()
			{
				$(this).dialog("close");
			}
		});
		
		//custom field dialog
		$("#custom-field-dialog").dialog(
		{
			autoOpen:false,
			height:500,
			width:500,
			modal:true,
			buttons:
			{
				"Add":function()
				{
					addCustomFields();
				},
				Cancel:function()
				{
					
					$(this).dialog("close");
				},
				
			},
			close:function()
			{
				$(this).dialog("close");
			}
		});
		
		//note dialog
		$("#new-note-dialog").dialog(
		{
			autoOpen:false,
			height:500,
			width:500,
			modal:true,
			buttons:
			{
				"Submit":function()
				{
					saveNote();
				},
				Cancel:function()
				{
					
					$(this).dialog("close");
				},
				
			},
			close:function()
			{
				$(this).dialog("close");
			}
		});
		
		/*
			END DIALOGs
		*/
			
		
		
	});	//end $(document).ready
	
	
	
	//this function basically only populates the drop down which let's you select a project type
	function prepareInterface()
	{
		$("#loading_text").html("Preparing project...");
		
		$("#loading").show();
		$("#content").hide();
		
		//fill the custom note dialog "user" dropdown
		$("#note-user").html("");
		$.each(users, function(user_k, user)
		{
			$("#note-user").append("<option value='" + user.id + "'>" + user.username + "</option>");
		});
		
		
		var projectTypes=new Array();
		if (projectId==null)
		{
			$("#loading_text").html("Loading project types...");
			$.ajax(
			{
				url:"php/ajax.php",
				type:"POST",
				data:{task:"loadAllProjectTypes"}
			})
			.done(function(data)
			{
				$("#project_type").html("");
				var pTypes=$.parseJSON(data);
				$("#project_type").append("<option value='-1'>Select a Project Type</option>");
				$.each(pTypes, function(k, v)
				{
					$("#project_type").append("<option value='" + v.id + "'>" + v.title + "</option>");
				});
				
				$("#loading").hide();
				$("#content").show();
			})
			.fail(function()
			{
				alert("Error while loading project types");
			});
		}
		
		
		
		//if we have a project, load it...
		if (projectId==null)
		{
			//the last thing we do with a new project is to add a users list to it
			var usersHTML="<strong>Users</strong><br /><table>";
			$.each(users, function(k, v)
			{
				usersHTML+="<tr><td><input type='checkbox' class='project_user' data-user-id='" + v.id + "' /></td><td>" + v.username + "</td></tr>";
			});
			usersHTML+="</table>";
			$("#project_users").html(usersHTML);
			return;
		}
		
		$("#loading_text").html("Loading project details...");
		//load the project
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadProject", id:projectId}
		})
		.done(function(data)
		{
			if (data=="no_exist")
			{
				alert("The project you are looking for does not exist, please select one from the list of projects.");
				window.location="projects.php";
				return;
			}
			
			$("#loading_text").html("Building project interface...");
			var project=$.parseJSON(data);
			$("#project_lists").html("");
			
			
			//right, we have a project... populate the page! :)
			$("#title").val(project.title);
			//make sure the project type cant be changed
			$("#type").change(function(e)
			{
				e.preventDefault();
			});
			
			$("#project_type").val(project.project_type);
			
			//cool, now load the checklists with their items :) easy stuff
			var html="";
			$.each(project.checklists, function (k, checklist)
			{
				//create a div for this checklist
				
				html="<div class='checklist'>";
				//checklist title
				html+="<div class='checklist_title' data-list-title='" + checklist.title + "' data-list-counter='" + list_counter + "' data-checklist-id='" + checklist.id + "' data-list-origin='" + checklist.origin_list + "'>";
					html+=checklist.title;
					list_counter++;
				html+="</div>";
				
				//generate a table with the fields for this checklist based on their datatypes
				html+="<div class='checklist_fields'><table>";
					$.each(checklist.fields, function(field_k, field) //loop through the list fields, referring to each one by the field variable
					{
						switch(field.datatype)
						{
							case "text":
							case "string":
									html+="<tr class='" +(field.allowed==true?"":"disabled_row") + "'><td>" + field.title + "</td><td><input class='checklist_field' type='text' data-value-id='" + field.id + "' data-field-id='" + field.field_id + "' data-list-id='null' value='" + field.value + "' " + (field.allowed==true?"style=''":"disabled") + " /></td></tr>";
								break;
							case "int":
									html+="<tr class='" +(field.allowed==true?"":"disabled_row") + "'><td><input class='checklist_field' type='number' data-value-id='" + field.id + "' data-field-id='" + field.field_id + "' data-list-id='null' step='1'  value='" + field.value + "'  " + (field.allowed==true?"":"disabled") + "  /></td><td>" + field.title + "</td></tr>";
								break;
							case "float":
									html+="<tr class='" +(field.allowed==true?"":"disabled_row") + "'><td><input class='checklist_field' type='number' data-value-id='" + field.id + "' data-field-id='" + field.field_id + "' data-list-id='null' step='0.01'  value='" + field.value + "'  " + (field.allowed==true?"":"disabled") + " /></td><td>" + field.title + "</td></tr>";
								break;
							case "checkbox":
									html+="<tr class='" +(field.allowed==true?"":"disabled_row") + "'><td><input class='checklist_field' type='checkbox' data-value-id='" + field.id + "' data-field-id='" + field.field_id + "' data-list-id='null' "  + (field.value=="1"?"checked":"") +  (field.allowed==true?"":" disabled ") +  "/></td><td>" + field.title + "</td></tr>";
								break;
							case "list":
									html+="<tr class='" +(field.allowed==true?"":"disabled_row") + "'></td><td>" + field.title + "</td><td><select class='checklist_field' data-value-id='" + field.id + "' data-field-id='" + field.field_id + "' data-list-id='null' " + (field.allowed==true?"":"disabled") + " >";
										console.log("starting a select");
										console.log(field.options);
										var options=$.parseJSON(field.options);
										$.each(options, function(option_k, option)
										{
											html+="<option value='" + option + "' " + (field.value==option?"selected":"") + ">" + option + "</option>";
										});
										console.log("finished it");
									html+="</select></tr>";
								break;
							case "user":
									html+="<tr class='" +(field.allowed==true?"":"disabled_row") + "'><td>" + field.title + "</td><td><select class='checklist_field' data-value-id='" + field.id + "' data-field-id='" + field.field_id + "' data-list-id='null' " + (field.allowed==true?"":"disabled") + " >";
										$.each(users, function(user_k, user)
										{
											html+="<option value='" + user.id + "' " + (field.value==user.id?"selected":"") + ">" + user.username + "</option>";
										});
									html+="</select></td></tr>";
								break;
							default:
									console.log("Invalid field type: " + field.datatype);
								break;
						}
					});
				html+="</table></div>";
				
				html+="</div>";
				$("#project_lists").append(html);
			}); //$.each checklist	
			
			//create the existing notes
			if (project.notes.length>0)
			{
				if (!($("#project-notes").length))
				{
					$("#content").append("<div id='project-notes'><div class='project_notes_heading'>Project Notes</div><table id='project-notes-table'></table></div>");
				}
			}
			
			$("#loading_text").html("Building project notes interface...");
			
			$.each(project.notes, function(note_k, note)
			{
				if (note.value===null)
				{
					note.value="";
				}
				switch(note.datatype)
				{
					case "text":
					case "string":
							input="<input class='note' type='text' data-id='" + note.id + "' data-datatype='"  + note.datatype + "' data-title='" + note.title + "' data-user='"+ note.user +"' data-options='' value='" + (note.value==""||note.value=="null"?"":note.value) + "' />";
						break;
					case "int":
							input="<input class='note' type='number' data-id='" + note.id + "'  data-datatype='"  + note.datatype + "'  data-title='" + note.title + "' data-user='"+ note.user +"' data-options='' step='1'  value='" + (note.value==""||note.value=="null"?"":note.value) + "'/>";
						break;
					case "float":
							input="<input class='note' type='number' data-id='" + note.id + "'   data-datatype='"  + note.datatype + "'   data-title='" + note.title + "' data-user='"+ note.user +"' data-options='' step='0.01' value='" + (note.value==""||note.value=="null"?"":note.value) + "'/>";
						break;
					case "checkbox":
							input="<input class='note' type='checkbox'  data-id='" + note.id + "'  data-datatype='"  + note.datatype + "'   data-title='" + note.title + "' data-user='"+ note.user +"' data-options=''  " + (note.value=="1"?"checked":"") + "  />";
						break;
					case "list":
							input="<select class='note'  data-id='" + note.id + "'  data-datatype='"  + note.datatype + "' data-title='" + note.title + "' data-user='"+ note.user +"' data-options='" + note.options + "' >";
								$.each($.parseJSON(note.options), function(option_k, option)
								{
									input+="<option value='" + option + "' " + (option==note.value?"selected":"") + " >" + option + "</option>";
								});
							input+="</select>";
						break;
					case "user":
							input="<select class='note' data-id='" + note.id + "'  data-datatype='"  + note.datatype + "' data-title='" + note.title + "' data-user='"+ note.user +"'  data-options='' >";
								$.each(note.users, function(user_k, user)
								{
									input+="<option value='" + user.id + "' " + (user.id==note.value?"selected":"") + ">" + user.username + "</option>";
								});
							input+="</select>";
						break;
					default:
							console.log("Invalid field type: " + datatype);
						break;
				}
				var style="";
				if (note.user!='<?php echo $_SESSION['uid']; ?>')
				{
					style+="display:none; ";
				}
				$("#project-notes-table").append("<tr style='" + style + "'><td>" + note.title + "</td><td>" + input + "</td></tr>");
				
			});
			$("#project_top").append("<button class='project_action_button' id='addListButton' onclick='addCustomList();'>Add Checklist</button>");
			$("#project_top").append("<button class='project_action_button' id='addFieldButton' onclick='addCustomField();'>Add Field</button>");
			$("#project_top").append("<button class='project_action_button' id='addNoteButton' onclick='addNote();'>Add Note</button>");
			$("#project_top").append("<button class='project_action_button' id='saveProjectButtom' onclick='saveProject();'>Save</button>");
			
			var usersHTML="<strong>Users</strong><br /><table>";
			$.each(project.users, function(k, v)
			{
				usersHTML+="<tr><td><input type='checkbox' class='project_user' data-user-id='" + v.id + "' " + (v.inProject?"checked":"") + "/></td><td>" + v.username + "</td></tr>";
			});
			usersHTML+="</table>";
			$("#project_users").html(usersHTML);
					
			$("#loading").hide();
			$("#content").show();
		}) //$.ajax.done
		.fail(function()
		{
			alert("Could not load project.");
		}); //$.ajax.fail
	}
	
	//loads default lists for the selected project type
	function loadDefaultLists()
	{
		//function to load the default lists for project types.
		//this function should only be used with NEW PROJECTS! (ie. isset($_GET['id'])==false)
		//this function may only be called ONCE with an existing project, and that is when the page is loaded for editing.
		$("#project_lists").html("Loading project checklists...");
		$("#addListButton").remove();
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadDefaultLists", project_type:$("#project_type").val()}
		})
		.done(function(data)
		{
			$("#project_lists").html("");
			var lists=$.parseJSON(data);
			
			//okay so we have all the lists which contain all their checks... let's display them! :)
			$.each(lists, function(k, list) //key=>list
			{
				$("#project_lists").append(compileChecklistTable(list));
			});
			$("#project_top").append("<button id='addListButton' onclick='addCustomList();'>Add Checklist</button>");
			$("#project_top").append("<button id='addFieldButton' onclick='addCustomField();'>Add Field</button>");
			$("#content").append("<button id='saveProjectButtom' onclick='saveProject();'>Save</button>");
			
		})
		.fail(function()
		{
			$("#project_lists").html("Failed to load default project Lists...");
		});
	}
	
	//takes a list as input (with it's fields as one of the member variables and generates some html to create a checklist div
	function compileChecklistTable(list)
	{
		if (list===null) //exit if no input is provided
			return;
		var html="<div class='checklist'>";
		//checklist title
		html+="<div class='checklist_title' data-list-title='" + list.title + "' data-list-counter='" + list_counter + "' " + (list.origin_list==undefined||list.origin_list==null||list.origin_list==""?"":"data-origin-list='" + list.origin_list + "'") + ">";
			html+=list.title;
			list_counter++;
		html+="</div>";
		
		//generate a table with the fields for this checklist based on their datatypes
		html+="<div class='checklist_fields'><table>";
			$.each(list.fields, function(field_k, field) //loop through the list fields, referring to each one by the field variable
			{
				switch(field.datatype)
				{
					case "text":
					case "string":
							html+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td>" + field.title + "</td><td><input class='checklist_field' type='text' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' " + (field.editAllowed==true?"":"disabled") + "/></td></tr>";
						break;
					case "int":
							html+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td><input class='checklist_field' type='number' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' step='1' " + (field.editAllowed?"":"disabled") + " /></td><td>" + field.title + "</td></tr>";
						break;
					case "float":
							html+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td><input class='checklist_field' type='number' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' step='0.01' " + (field.editAllowed?"":"disabled") + "/></td><td>" + field.title + "</td></tr>";
						break;
					case "checkbox":
							html+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td><input class='checklist_field' type='checkbox' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' " + (field.editAllowed?"":"disabled") + " /></td><td>" + field.title + "</td></tr>";
						break;
					case "list":
							html+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'></td><td>" + field.title + "</td><td><select class='checklist_field' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' " + (field.editAllowed?"":"disabled") + ">";
								var options=$.parseJSON(field.options); //remember, options are json encoded
								$.each(options, function(option_k, option)
								{
									html+="<option value='" + option + "'>" + option + "</option>";
								});
							html+="</select></tr>";
						break;
					case "user":
							html+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td>" + field.title + "</td><td><select class='checklist_field' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' " + (field.editAllowed?"":"disabled") + ">";
								$.each(users, function(user_k, user)
								{
									html+="<option value='" + user.id + "'>" + user.username + "</option>";
								});
							html+="</select></td></tr>";
						break;
					default:
							console.log("Invalid field type: " + field.datatype);
						break;
				}
			});
		html+="</table></div>";
		
		html+="</div>";
	
		return html;
	}
	
	//takes a list as input(with it's fields) and creates the HTML for the custom list dialog
	function compileCustomChecklistTableForDialog(list)
	{
		if (list===null)
			return;
		var html="<div class='custom_checklist'>";
		html+="<div class='checklist_title'>";
			html+="<input type='checkbox' data-custom-id='" + list.id + "' class='custom_list' data-checklist-title='" + list.title + "'/>&nbsp" + list.title;
		html+="</div>";
		
		html+="<div class='checklist_fields'><table>";
			$.each(list.fields, function(field_k, field) //loop through the list fields, referring to each one by the field variable
			{
				html+="<tr><td><input class='custom_checklist_field' type='checkbox' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' data-data-type='" + field.datatype + "' data-options='" + field.options + "' data-title='" + field.title + "' data-edit-allowed='" + field.editAllowed + "'/></td><td>" + field.title + "</td></tr>";
			});
		html+="</table></div>";
		
		html+="</div>";
	
		return html;
	}
	
	// brings up the dialog which let's you add lists (or parts of lists)
	function addCustomList()
	{
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadDefaultLists"}
		})
		.done(function(data)
		{
			var html="Please select the lists (or just items) you would like to add below.<hr />";
			var lists=$.parseJSON(data);
			$.each(lists, function(k, list)
			{
				html+=compileCustomChecklistTableForDialog(list);
			});
			$("#custom-list-dialog").html(html);
			$("#custom-list-dialog").dialog("open");
			
			/*
				BEWARE: MESSY CODE AHEAD
			*/
			$(".custom_list").change(function()
			{
				//check//uncheck all cousins
				$(this).parent().siblings(".checklist_fields").children("table").children("tbody").children("tr").children("td").children("input").prop("checked", $(this).prop("checked"));
			});
			$(".custom_checklist_field").change(function()
			{
				if ($(this).prop("checked")==true)
				{
					//check the distant relative
					$(this).parent().parent().parent().parent().parent().siblings(".checklist_title").children("input").prop("checked", true);
				}
				else
				{
					//check if any cousins are checked.  If not, uncheck the parent
					var checked=false;
					$(this).parent().parent().parent().children("tr").each(function()
					{
						$(this).children("td").each(function()
						{
							$(this).children("input").each(function()
							{
								if ($(this).prop("checked")==true)
								{
									checked=true;
								}
							});
						});
					});
					$(this).parent().parent().parent().parent().parent().siblings(".checklist_title").children("input").prop("checked", checked);
				}
			});
		})
		
		/*
			PHEW, MESSY CODE IS BEHIND US!
		*/
		.fail(function()
		{
			alert("Good men were lost in our attempt to load this dialog.  Sorry about that.");
		});
	}
	
	//insert custom lists into the actual viewing page
	function addCustomLists()
	{
		//go through each of the listy thingys...
		var html="";
		$(".custom_list").each(function()
		{
			if (!($(this).prop("checked")))
			{
				return;
			}
			console.log($(this).data("checklist-title"));
			
			html+="<div class='checklist'>";
				html+="<div class='checklist_title'  data-list-title='" + $(this).data("checklist-title") + "' data-list-counter='" + list_counter + "' data-origin-list='0'>";
					html+=$(this).data("checklist-title");
					list_counter++;
				html+="</div>";
			html+="<div class='checklist_fields'><table>";
			$(this).parent().siblings(".checklist_fields").children("table").children("tbody").children("tr").children("td").children("input").each(function()
			{
				if (!$(this).prop("checked"))
				{
					return;
				}
				//we now have a checkbox...
				switch($(this).data("data-type"))
				{
					case "text":
					case "string":
							html+="<tr class='" +($(this).data("edit-allowed")==true?"":"disabled_row") + "'><td>" + $(this).data("title") + "</td><td><input class='checklist_field' type='text' data-value-id='null' data-field-id='" + $(this).data("field-id") + "' data-list-id='null' " + ($(this).data("edit-allowed")==true?"":"disabled") + " /></td></tr>";
						break;
					case "int":
							html+="<tr class='" +($(this).data("edit-allowed")==true?"":"disabled_row") + "'><td><input class='checklist_field' type='number' data-value-id='null' data-field-id='" + $(this).data("field-id")+ "' data-list-id='null' step='1'  " + ($(this).data("edit-allowed")==true?"":"disabled") + " /></td><td>" + $(this).data("title") + "</td></tr>";
						break;
					case "float":
							html+="<tr class='" +($(this).data("edit-allowed")==true?"":"disabled_row") + "'><td><input class='checklist_field' type='number' data-value-id='null' data-field-id='" + $(this).data("field-id") + "' data-list-id='null' step='0.01'  " + ($(this).data("edit-allowed")==true?"":"disabled") + " /></td><td>" + $(this).data("title") + "</td></tr>";
						break;
					case "checkbox":
							html+="<tr class='" +($(this).data("edit-allowed")==true?"":"disabled_row") + "'><td><input class='checklist_field' type='checkbox' data-value-id='null' data-field-id='" + $(this).data("field-id") + "' data-list-id='null'  " + ($(this).data("edit-allowed")==true?"":"disabled") + " /></td><td>" + $(this).data("title") + "</td></tr>";
						break;
					case "list":
							html+="<tr class='" +($(this).data("edit-allowed")==true?"":"disabled_row") + "'><td>" + $(this).data("title") + "</td><td><select class='checklist_field' data-value-id='null' data-field-id='" + $(this).data("field-id") + "' data-list-id='null'  " + ($(this).data("edit-allowed")==true?"":"disabled") + " >";
								var options=$(this).data("options");
								$.each(options, function(option_k, option)
								{
									html+="<option value='" + option + "'>" + option + "</option>";
								});
							html+="</select></td></tr>";
						break;
					case "user":
							html+="<tr class='" +($(this).data("edit-allowed")==true?"":"disabled_row") + "'><td>" + $(this).data("title") + "</td><td><select class='checklist_field' data-value-id='null' data-field-id='" + $(this).data("field-id") + "' data-list-id='null'  " + ($(this).data("edit-allowed")==true?"":"disabled") + " >";
								$.each(users, function(user_k, user)
								{
									html+="<option value='" + user.id + "'>" + user.username + "</option>";
								});
							html+="</select></td></tr>";
						break;
					default:
							console.log("Invalid field type: " + field.datatype);
						break;
				}
			});
			html+="</table></div>";
		
			html+="</div>";
		});
		$("#project_lists").append(html);
		$("#custom-list-dialog").html("").dialog("close");;
	}
	
	//bring up the dialog which let's you select custom fields to add
	function addCustomField()
	{
		var checklists=new Array();
		var html="";
		html+="<div class='destination_fields'>Please select a destination for your new fields:<br /><br />";
		html+="Existing checklist:<br /><div style=''>"
		$(".checklist_title").each(function()
		{
			html+="<div class='existing_field_div'><input type='checkbox' class='custom_field_destination' data-list-counter='" + $(this).data("list-counter") + "' />&nbsp;" + $(this).data("list-title") + "</div>";
		});
		html+="</div>";
		html+="<div class='new_checklist_div'>";
			html+="<input type='checkbox' id='new_list_checkbox'>&nbsp;New Checklist&nbsp;<input type='text' id='new_checklist_name' />";
		html+="</div></div>";
		html+="<div class='new_fields'> Select the fields you would like to add:<br />";
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadAllFields"}
		})
		.done(function(data)
		{
			var fields=$.parseJSON(data);
			$.each(fields, function (k, field)
			{
				//if (field.allowed)
				//{
					html+="<input type='checkbox' class='custom_field' data-field-id='" + field.id + "' data-field-title='" + field.title + "' data-field-options='" + String(field.options).replace(new RegExp('"', 'g'), '&quot;') + "' data-data-type='" + field.datatype + "' data-edit-allowed='" + field.allowed + "' />&nbsp;" + field.title + "<br />";
				//}
			});
			html+="</div>";
			$("#custom-field-dialog").html(html).dialog("open");
		})
		.fail(function()
		{
			alert("Good men were lost in our attempt to load this dialog.  Sorry about that.");
		});
	}
	
	//add the fields to the actual viewable interface
	function addCustomFields()
	{
		//first gather all the custom fields...
		var fields=new Array();
		var html="";
		$(".custom_field").each(function()
		{
			if (!($(this).prop("checked")))
			{
				return; //would be "continue", but we aren't in a regular loop...
			}
			
			var field=new Object();
			field.id=$(this).data("field-id");
			field.title=$(this).data("field-title");
			field.datatype=$(this).data("data-type");
			field.options= $(this).data("field-options");
			field.editAllowed= $(this).data("edit-allowed");
			fields.push(field);
		});
		
		console.log("FIELDS TO ADD:");
		console.log(fields);
		
		//now see where to put these fields... remember that we can have many source fields, and many destinations for them... They will all go to all the destinations though.
		var existing_lists=new Array();
		$(".custom_field_destination").each(function()
		{
			if (!($(this).prop("checked")))
			{
				return; //would be "continue", but we aren't in a regular loop...
			}
			
			var list=new Object();
			list.counter=$(this).data("list-counter");
			
			existing_lists.push(list);
		});

		listRows="";
		//lets generate some HTML for the fields to be added
		$.each(fields, function (k, field)
		{
			switch(field.datatype)
			{
				case "text":
				case "string":
						listRows+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td>" + field.title + "</td><td><input class='checklist_field' type='text' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' " + (field.editAllowed==true?"":"disabled") + "/></td></tr>";
					break;
				case "int":
						listRows+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td><input class='checklist_field' type='number' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' step='1' " + (field.editAllowed==true?"":"disabled") + "/></td><td>" + field.title + "</td></tr>";
					break;
				case "float":
						listRows+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td><input class='checklist_field' type='number' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' step='0.01' " + (field.editAllowed==true?"":"disabled") + "/></td><td>" + field.title + "</td></tr>";
					break;
				case "checkbox":
						listRows+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td><input class='checklist_field' type='checkbox' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' " + (field.editAllowed==true?"":"disabled") + " /></td><td>" + field.title + "</td></tr>";
					break;
				case "list":
						listRows+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td>" + field.title + "</td><td><select class='checklist_field' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null' " + (field.editAllowed==true?"":"disabled") + ">";
							console.log("About to parse options: " + field.options);
							$.each(field.options, function(option_k, option)
							{
								listRows+="<option value='" + option + "'>" + option + "</option>";
							});
						listRows+="</select></td></tr>";
					break;
				case "user":
						listRows+="<tr class='" +(field.editAllowed==true?"":"disabled_row") + "'><td>" + field.title + "</td><td><select class='checklist_field' data-value-id='null' data-field-id='" + field.id + "' data-list-id='null'" + (field.editAllowed==true?"":"disabled") + ">";
							$.each(users, function(user_k, user)
							{
								listRows+="<option value='" + user.id + "'>" + user.username + "</option>";
							});
						listRows+="</select></td></tr>";
					break;
				default:
						console.log("Invalid field type: " + field.datatype);
					break;
			}
		});
		
		//right, we have all the destination lists... let's add everything.  We'll take care of the custom new list option in a moment.
		$(".checklist").each(function() //loop through all the checklists
		{
			var thisChecklist=$(this);
			var lCounter=$(this).children(".checklist_title").first().data("list-counter"); //get the counter of the current DOM checklist
			$.each(existing_lists, function(k, v) //loop through the existing lists that were selected.  If we find a match between v and lCounter, we can add fields to the current list
			{
				console.log(v.counter);
				//remember, v is the destination list, lCounter is the current lists's counter.  So if v.counter and lCounter match, we know that we can add our fields here
				if (v.counter!=lCounter)
				{
					return; //continue the loop if we dont have a match...
				}
				//if we reach this point, add all the fields to this list
				thisChecklist.children(".checklist_fields").first().children("table").first().children("tbody").first().append(listRows);
			}); //exiting $.each
		});//exiting $.each
		
		//right, let's check for a custom list :) If we have one, add a new checklist div with the table and fields inside it.
		if ($("#new_list_checkbox").prop("checked")==true)
		{
			//add checklist div
			var html="<div class='checklist'>";
			html+="<div class='checklist_title' data-list-title='" + $("#new_checklist_name").val() + "' data-list-counter='" + list_counter + "' data-list-origin='0'>";
				html+=$("#new_checklist_name").val()
				list_counter++;
			html+="</div>";
			
			html+="<div class='checklist_fields'><table>";
				html+=listRows;
			html+="</table></div></div>";
			console.log("Done generating HTML: " + html);
			$("#project_lists").append(html);
			
			
		} //exiting if 
		$("#custom-field-dialog").html("").dialog("close");
	}
	
	//right, here's the big one.  Save the project.  loads of dom reading stuffs here
	function saveProject()
	{
		$("#loading").show();
		$("#loading_text").html("Preparing to project...");
		$("#content").hide();
		//gather basic info (title and type)
		var project=new Object();
		project.id=projectId;
		project.title=$("#title").val();
		project.type=$("#project_type").val();
		project.checklists=new Array();
		//read through each of the checlists...
		$("#loading_text").html("Collecting checklists...");
		$(".checklist").each(function()
		{
			var checklist=new Object();
			//get the title of the checklist
			checklist.title=$(this).children(".checklist_title").first().data("list-title");
			checklist.origin_list=$(this).children(".checklist_title").first().data("origin-list");
			
			if ($(this).children(".checklist_title").first().data("checklist-id")!=undefined && $(this).children(".checklist_title").first().data("checklist-id")!=null)
			{
				checklist.id=$(this).children(".checklist_title").first().data("checklist-id");
				
			}
			
			checklist.fields=new Array();
			//read the checklist fields
			var checklist_fields=$(this).children(".checklist_fields").first();
			checklist_fields.children("table").first().children("tbody").children("tr").each(function()
			{
				//read therough the td tags to get the fields
				$(this).children("td").each(function()
				{
					if ($(this).find(".checklist_field").length==0)
					{
						return; //continue the loop, this td doesn't have a field
					}
					
					
					var field_input=$(this).children(".checklist_field").first();
					var field=new Object;
					field.field_id=field_input.data("field-id");
					field.value="";
					field.value_id=field_input.data("value-id");;
					field.list_id="";
					//first, let's get the type of this element.  It'll either be an input or a select list...
					var elementType=field_input.prop("tagName");
					if (elementType.toLowerCase()=="input")
					{
						switch(field_input.attr("type"))
						{
							case "text":
								field.value=field_input.val();
								break;
							case "checkbox":
								field.value=0;
								if (field_input.prop("checked"))
								{
									field.value=1;
								}
								break;
							default:
								console.log("Invalid input type: " + field_input.attr("type"));
								break;
						} //switch
					} //if (elementType=="input")
					else if (elementType.toLowerCase()=="select")
					{
						field.value=field_input.val();
					} //else if (elementType=="select")
					else
					{
						console.log("Invalid field tag name: " + elementType);
					} //else
					//add the field to the list
					checklist.fields.push(field);
				}); //$(this).children("td").each
				
			}); //checklist_fields.children("table").first().children("tbody").children("tr").each
			//add the checklist to the array of checklists
			project.checklists.push(checklist);
		}); //exiting $.each(".checklist")
		
		$("#loading_text").html("Collecting users...");
		project.users=new Array();
		//get the users for this project
		$(".project_user").each(function()
		{
			if ($(this).prop("checked"))
			{
				project.users.push($(this).data("user-id"));
			}
		});
		
		//get the notes for this project
		$("#loading_text").html("Collecting notes...");
		project.notes=new Array();
		$(".note").each(function()
		{
			var note=new Object();
			note.title=$(this).data("title");
			note.datatype=$(this).data("datatype");
			note.options=$(this).data("options");
			note.user=$(this).data("user");
			
			var elementType=$(this).prop("tagName");
			if (elementType.toLowerCase()=="input")
			{
				switch($(this).attr("type"))
				{
					case "text":
						note.value=$(this).val();
						break;
					case "checkbox":
						note.value=0;
						if ($(this).prop("checked"))
						{
							note.value=1;
						}
						break;
					default:
						console.log("Invalid input type: " + $(this).attr("type"));
						break;
				} //switch
			} //if (elementType=="input")
			else if (elementType.toLowerCase()=="select")
			{
				note.value=$(this).val();
			} //else if (elementType=="select")
			else
			{
				note.value="UNDEFINED";
			}
			note.id=$(this).data("id");
			note.count_for_completion=$(this).data("count-for-completion");
			project.notes.push(note);
		});
		
		
		$("#loading_text").html("Saving project...");
		//send the project for saving...
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"saveProject", project:JSON.stringify(project)}
		})
		.done(function(data)
		{
			if (data=="ok")
			{
				location.href="projects.php";
			}
			else
			{
				alert(data);
			}
		})
		.fail(function()
		{
			alert("Good men were lost in our attempt to save this project.  Sorry about that");
		});
	}
	
	function addNote()
	{
		$("#new-note-dialog").dialog("open");
		$("#note-options-row").hide();
		$("#note-datatype").change(function()
		{
			$("note-options").val("");
			if ($(this).val()=="list")
			{
				$("#note-options-row").show();
			}
			else
			{
				$("#note-options-row").hide();
			}
		});
	}
	
	function saveNote()
	{
		if (!($("#project-notes").length))
		{
			$("#content").append("<div id='project-notes'><h3>Project Notes</h3><table id='project-notes-table'></table></div>");
		}
		
		//gather variables for this note
		var title=$("#note-title").val();
		var datatype=$("#note-datatype").val();
		var user=$("#note-user").val();
		var count_for_completion=($("#note_count_for_completion").prop("checked")==true?"1":"0");
		
		
		var style="";
		/*if (user!='<?php echo $_SESSION['uid']; ?>')
		{
			style+="display:none; ";
		}*/
		
		switch(datatype)
		{
			case "text":
			case "string":
					input="<input class='note' type='text' data-id='null' data-datatype='"  + datatype + "' data-title='" + title + "' data-user='"+ user +"' data-options='' data-count-for-completion='" + count_for_completion + "' />";
				break;
			case "int":
					input="<input class='note' type='number' data-id='null'  data-datatype='"  + datatype + "'  data-title='" + title + "' data-user='"+ user +"' data-options='' step='1' data-count-for-completion='" + count_for_completion + "' />";
				break;
			case "float":
					input="<input class='note' type='number' data-id='null'   data-datatype='"  + datatype + "'   data-title='" + title + "' data-user='"+ user +"' data-options='' step='0.01' data-count-for-completion='" + count_for_completion + "' />";
				break;
			case "checkbox":
					input="<input class='note' type='checkbox'  data-id='null'  data-datatype='"  + datatype + "'   data-title='" + title + "' data-user='"+ user +"' data-options='' data-count-for-completion='" + count_for_completion + "' />";
				break;
			case "list":
					var options=JSON.stringify($("#note-options").val().split("\n"));
					if (options.length==0)
					{
						alert("Please enter some options.");
						return;
					}
					console.log(options);
					input="<select class='note'  data-id='null'  data-datatype='"  + datatype + "' data-title='" + title + "' data-user='"+ user +"' data-options='" + options + "' data-count-for-completion='" + count_for_completion + "' >";
						$.each($.parseJSON(options), function(option_k, option)
						{
							input+="<option value='" + option + "'>" + option + "</option>";
						});
					input+="</select>";
				break;
			case "user":
					input="<select class='note' data-id='null'  data-datatype='"  + datatype + "' data-title='" + title + "' data-user='"+ user +"'  data-options=''data-count-for-completion='" + count_for_completion + "'  >";
						$.each(users, function(user_k, user)
						{
							input+="<option value='" + user.id + "'>" + user.username + "</option>";
						});
					input+="</select>";
				break;
			default:
					console.log("Invalid field type: " + datatype);
				break;
		}
		
		$("#project-notes-table").append("<tr style='" + style + "'><td>" + title + "</td><td>" + input + "</td></tr>");
		$("#new-note-dialog").dialog("close");
		
	}
	
	
	</script>
	
</head>
<body>
	<div class="header">
		<?php renderMenu(); ?>
	</div>
	<div id="loading" class="loading">
		<div id="loading_text">Please Wait...</div>
		<img src='images/loading.gif' />
	</div>
	<div id="content" class="content" style="display:none">
		<h1 class="page_heading">
			<?php echo (isset($_GET['id'])?"Edit Project":"New Project"); ?>
		</h1>
		<div id="project_top" class="project_top">
			<table>
				<tr>
					<td>
						Title
					</td>
					<td>
						<input type="text" id="title" />
					</td>
				</tr>
				<tr>
					<td>
						Type
					</td>
					<td>
						<select id="project_type" <?php echo $disabled; ?> onchange="loadDefaultLists(); ">
							
						</select>
					</td>
				</tr>
			</table>
			
		</div>
		<div id="project_users">
			
		</div>
		
		<div id="project_lists">
			
		</div>
	</div>
	
	<div id="custom-list-dialog">
		
	</div>
	
	<div id="custom-field-dialog">
		
	</div>
	
	<div id="new-note-dialog">
		<h3>New Note</h3>
		<table>
			<tr>
				<td>
					Title
				</td>
				<td>
					<input type="text" id="note-title" />
				</td>
			</tr>
			<tr>
				<td>
					Input Type
				</td>
				<td>
					<select id="note-datatype">
						<option value="string">Text</option>
						<option value="int">Number (Integer)</option>
						<option value="float">Number (Floating Point)</option>
						<option value="checkbox">Checkbox</option>
						<option value="list">List</option>
						<option value="user">User</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					User to Assign
				</td>
				<td>
					<select id="note-user">
					</select>
				</td>
			</tr>
			<tr id="note-options-row">
				<td>Options</td>
				<td>
					<textarea id="note-options"></textarea>
				</td>
			</tr>
			<tr id="note-options-row">
				<td>Count for Project Completion</td>
				<td>
					<input type="checkbox" id="note_count_for_completion" checked />
				</td>
			</tr>
		</table>
	</div>
	
	
</body>
</html>
