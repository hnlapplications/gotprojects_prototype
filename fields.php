<?php 
	session_start();
	require_once(dirname(__FILE__) . "/php/includes.php"); 
?>
<!DOCTYPE html>
<head>
	<?php renderHead(); ?>
	<script type="text/javascript">
		var editId=null;
		
		$(document).ready(function()
		{
			loadAllFields();
			
			$("#field-dialog").dialog(
			{
				autoOpen:false,
				height:500,
				width:500,
				modal:true,
				buttons:
				{
					"Save":function()
					{
						saveField();
					},
					Cancel:function()
					{
						var title=$("#field_title").val("");
						var datatype=$("#field_datatype").val("");
						var default_value=$("#field_default_value").val("");
						var searchable=$("#field_").val("");
						var sortable=$("#field_sortable").val("");
						var published=$("#field_published").val("");
						var count_for_completion=$("#field_count_for_completion").val("");
						var options=$("#field_options").val("");
						$(this).dialog("close");
					},
					
				},
				close:function()
				{
					$(this).dialog("close");
				}
			});
			
			//user groups dialog
			$("#usergroups-dialog").dialog(
			{
				autoOpen:false,
				height:500,
				width:500,
				modal:true,
				buttons:
				{
					"Save":function()
					{
						saveUserGroups();
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
			
			//checklists dialog
			$("#lists-dialog").dialog(
			{
				autoOpen:false,
				height:500,
				width:500,
				modal:true,
				buttons:
				{
					"Save":function()
					{
						saveChecklists();
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
			
			$("#field_datatype").change(function()
			{
				$("#field_options").val("");
				if ($(this).val()=="list")
				{
					$("#row_options").show();
				}
				else
				{
					$("#row_options").hide();
				}
			});
		});
		
		
		function loadAllFields()
		{
			$("#fieldsTable tbody").html("");
			$("#message").html("Loading...").show();
			$("#fieldsTable").hide();
			$.ajax(
			{
				url:"php/ajax.php",
				type:"POST",
				data:{task:"loadAllFields"}
			})
			.done(function(data)
			{
				var fields=$.parseJSON(data);
				if (fields.length==0)
				{
					$("#message").html("There are no fields to display.").show();
					$("#fieldsTable").hide();
					return;
				}
				$("#message").hide();
				$.each(fields, function(k, v)
				{
					
					$("#fieldsTable").show();
					var html="";
					html="<tr>";
						html+="<td>";
							html+=v.id;
						html+="</td>";
						html+="<td>";
							html+=v.title;
						html+="</td>";
						html+="<td>";
							html+=v.datatype;
						html+="</td>";
						html+="<td>";
							html+=v.default_value;
						html+="</td>";
						html+="<td>";
							html+=(v.searchable==1?"Yes":"No");
						html+="</td>";
						html+="<td>";
							html+=(v.sortable==1?"Yes":"No");
						html+="</td>";
						html+="<td>";
							html+=(v.published==1?"Yes":"No");
						html+="</td>";
						html+="<td>";
							html+=(v.count_for_completion==1?"Yes":"No");
						html+="</td>";
						
						html+="<td>";
							html+="<button onclick='edit(" + v.id + ");'>Edit</button>";
							html+="<button onclick='assignUsergroups(" + v.id + ")'>Usergroups</button>";
							html+="<button onclick='assignLists(" + v.id + ");'>Checklists</button>";
						html+="</td>";
						html+="<td>";
							var usergroupsarray=new Array();
							$.each(v.usergroups, function(usergroup_k, usergroup)
							{
								usergroupsarray.push(usergroup.title);
							});
							html+=usergroupsarray.join(", ");
						html+="</td>";
						html+="<td>";
							var checklistarray=new Array();
							$.each(v.checklists, function(checklist_k, checklist)
							{
								checklistarray.push(checklist.title);
							});
							html+=checklistarray.join(", ");
						html+="</td>";
						html+="<td>";
							console.log(v.options);
							html+=(v.options.trim()!=""?$.parseJSON(v.options).join(", "):"N/a");
						html+="</td>";
					html+="</tr>";
					$("#fieldsTable tbody").append(html);
				});
			})
			.fail(function()
			{
				alert("An error occured while loading fields.  Please try again later.");
			});
		}
		
		function createField()
		{
			$("#field-dialog").dialog("open");
			editId=null;
		}
		
		function saveField()
		{
			//collect variables
			var field= new Object();
			if (editId!=null)
			{
				field.id=editId;
			}
			field.title=$("#field_title").val();
			field.datatype=$("#field_datatype").val();
			field.default_value=$("#field_default_value").val();
			
			if (field.default_value.trim=="")
			{
				switch(field.datatype)
				{
					case "string":
						field.default_value="0";
						break;
					case "int":
						field.default_value="0";
						break;
					case "float":
						field.default_value="0";
						break;
					case "checkbox":
						field.default_value="0";
						break;
					case "list":
						field.default_value="0";
						break;
					case "user":
						field.default_value="0";
						break;
				}
			}
			
			field.searchable=($("#field_searchable").prop("checked")==true?1:0);
			field.sortable=($("#field_sortable").prop("checked")==true?1:0);
			field.published=($("#field_published").prop("checked")==true?1:0);
			field.count_for_completion=($("#field_count_for_completion").prop("checked")==true?1:0);
			field.options=JSON.stringify($("#field_options").val().split("\n"));
			
			var json=JSON.stringify(field);
			$.ajax(
			{
				url:"php/ajax.php",
				type:"POST",
				data:{task:"saveField", field:json}, 
			})
			.done(function(data)
			{
				editId=null;
				if (data!="ok")
				{
					alert(data);
					$("#field-dialog").dialog("close");
					//loadAllFields();
					location.reload();
				}
			})
			.fail(function()
			{
				alert("An error occured while saving.");
			});
		}
		
		function edit(id)
		{
			$("#field-dialog").dialog("open");
			$.ajax(
			{
				url:"php/ajax.php",
				type:"POST",
				data:{task:"getField", id:id}
			})
			.done(function(data)
			{
				$("#field_searchable").prop("checked", false);
				$("#field_sortable").prop("checked", false);
				$("#field_published").prop("checked", false);
				$("#field_count_for_completion").prop("checked", false);
				editId=id;
				field=$.parseJSON(data);
				$("#field_title").val(field.title);
				$("#field_datatype").val(field.datatype);
				$("#field_default_value").val(field.default_value);
				if (field.searchable==1)
				{
					$("#field_searchable").prop("checked", true);
				}
				if (field.sortable==1)
				{
					$("#field_sortable").prop("checked", true);
				}
				if (field.published==1)
				{
					$("#field_published").prop("checked", true);
				}
				if (field.count_for_completion==1)
				{
					$("#field_count_for_completion").prop("checked", true);
				}
				
				$("#field_options").val(($.parseJSON(field.options)).join("\n"));
			})
			.fail(function()
			{
				alert("An error occured while loading the field to be edited");
			});
		}
		
		function assignUsergroups(id)
		{
			editId=id;
			$("#usergroups-dialog").dialog("open");
			$("#usergroups-dialog").html("Loading...");
			//load user groups for this field (and all the other groups of course
			$.ajax(
			{
				url:"php/ajax.php",
				type:"POST",
				data:{task:"loadFieldUsergroups", id:id}
			})
			.done(function(data)
			{
				var groups=$.parseJSON(data);
				//output them each with a checkbox
				html="";
				$.each(groups, function(k, v)
				{
					html+="<input type='checkbox' data-group-id='" + v.id + "' class='field_groupid_checkbox' " + (v.allowed==true?"checked":"") + "/> " + v.title + "<br />";
				});
				console.log(html);
				$("#usergroups-dialog").html(html);
			})
			.fail(function()
			{
				alert("Failed to load usergroups");
			});
		}
		
		function saveUserGroups()
		{
			//get all the user groups
			var groups = new Array();
			$(".field_groupid_checkbox").each(function()
			{
				if ($(this).prop("checked")==true)
				{
					groups.push($(this).data("group-id"));
				}
			});
			$.ajax(
			{
				url:"php/ajax.php",
				type:"POST",
				data:{task:"saveFieldUsergroups", field_id:editId, groups:JSON.stringify(groups)}
			})
			.done(function(data)
			{
				editId=null;
				if (data=="ok")
				{
					alert("Saved");
					$("#usergroups-dialog").dialog("close");
					loadAllFields();
				}
			})
			.fail(function()
			{
				alert("Failed to save user groups for this field.");
			});
		}
		
		function assignLists(id)
		{
			editId=id;
			$("#lists-dialog").dialog("open");
			$("#lists-dialog").html("Loading...");
			//load checklists for this field 
			$.ajax(
			{
				url:"php/ajax.php",
				type:"POST",
				data:{task:"loadFieldLists", id:id}
			})
			.done(function(data)
			{
				var lists=$.parseJSON(data);
				//output them each with a checkbox
				html="";
				$.each(lists, function(k, v)
				{
					html+="<input type='checkbox' data-list-id='" + v.id + "' class='field_listid_checkbox' " + (v.isForField==true?"checked":"") + "/> " + v.title + "<br />";
				});
				console.log(html);
				$("#lists-dialog").html(html);
			})
			.fail(function()
			{
				alert("Failed to load checklists");
			});
		}
		
		function saveChecklists()
		{
			//get all the user groups
			var lists = new Array();
			$(".field_listid_checkbox").each(function()
			{
				if ($(this).prop("checked")==true)
				{
					lists.push($(this).data("list-id"));
				}
			});
			$.ajax(
			{
				url:"php/ajax.php",
				type:"POST",
				data:{task:"saveFieldLists", field_id:editId, groups:JSON.stringify(lists)}
			})
			.done(function(data)
			{
				editId=null;
				if (data=="ok")
				{
					alert("Saved");
					$("#lists-dialog").dialog("close");
					loadAllFields();
				}
			})
			.fail(function()
			{
				alert("Failed to save checklists for this field.");
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
			Fields
		</h1>
		<button onclick="createField();">New Field</button>
		<div id="message">
			Loading...
		</div>
		<table id="fieldsTable" style="display:none">
			<thead>
				<th>ID</th>
				<th>Title</th>
				<th>Data Type</th>
				<th>Default</th>
				<th>Searchable</th>
				<th>Sortable</th>
				<th>Published</th>
				<th>Count for Completion</th>
				<th>Actions</th>
				<th>Usergroups</th>
				<th>Checklists</th>
				<th>Options</th>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</div>
	
	<div id="field-dialog" title="Field">
		<form>
			<table>
				<tr>
					<td>
						Title
					</td>
					<td>
						<input type="text" id="field_title" />
					</td>
				</tr>
				<tr>
					<td>
						Data Type
					</td>
					<td>
						<select id="field_datatype">
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
						Default Value
					</td>
					<td>
						<input type="text" id="field_default_value" />
					</td>
				</tr>
				<tr>
					<td>
						Searchable
					</td>
					<td>
						<input type="checkbox" id="field_searchable" />
					</td>
				</tr>
				<tr>
					<td>
						Sortable
					</td>
					<td>
						<input type="checkbox" id="field_sortable" />
					</td>
				</tr>
				<tr>
					<td>
						Published
					</td>
					<td>
						<input type="checkbox" id="field_published" />
					</td>
				</tr>
				<tr>
					<td>
						Count for Project Completion
					</td>
					<td>
						<input type="checkbox" id="field_count_for_completion"/>
					</td>
				</tr>
				<tr id="row_options" style="display:none">
					<td>
						Options
					</td>
					<td>
						<textarea id="field_options" ></textarea>
					</td>
				</tr>
			</table>
		</form>
	</div>
	
	<div id="usergroups-dialog" title="Field">
		
	</div>
	
	<div id="lists-dialog" title="Checklists">
		
	</div>
	
</body>
</html>
