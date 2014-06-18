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
		$("#list-dialog").dialog(
		{
			autoOpen:false,
			height:300,
			width:300,
			modal:true,
			buttons:
			{
				"Submit":function()
				{
					saveList();
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
		
		$("#ptypes-dialog").dialog(
		{
			autoOpen:false,
			height:300,
			width:300,
			modal:true,
			buttons:
			{
				"Submit":function()
				{
					saveListPTypes();
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
		
		$("#fields-dialog").dialog(
		{
			autoOpen:false,
			height:300,
			width:300,
			modal:true,
			buttons:
			{
				"Submit":function()
				{
					saveFields();
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
		
		loadAllLists();
	});
	
	function createList()
	{
		$("#list-dialog").dialog("open");
		editId=0;
	}
	
	function saveList()
	{
		var listName=$("#list_title").val();
		if (listName===undefined || listName.trim()=="")
		{
			alert("Please enter a list name.");
			return;
		}
		
		list=new Object();
		if (editId!=0)
		{
			list.id=editId;
		}
		list.title=listName;
		
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"saveList", list:JSON.stringify(list)}
		})
		.done(function(data)
		{
			if (data=="ok")
			{
				alert("Saved");
				loadAllLists();
			}
			else
			{
				alert(data);
			}
		})
		.fail(function()
		{
			alert("An error occured while saving your list.  Please try again later.");
		});
	}
	
	function loadAllLists()
	{
		$("#message").html("Loading...").show();
		$("#listTable").hide();
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadAllLists"}
		})
		.done(function(data)
		{
			var lists=$.parseJSON(data);
			if (lists.length==0)
			{
				$("#message").html("There are currently no lists to display");
				return;
			}
			
			$("#listRows").html("");
			$.each(lists, function(k, v)
			{
				$("#listRows").append("<tr><td>" + v.id + "</td><td>" + v.title + "</td><td><button onclick='assignProjectTypes(" + v.id + ");'>Project Types</button><button onclick='assignFields(" + v.id + ");'>Fields</button></td></tr>");
			});
			$("#message").hide();
			$("#listTable").show();
		})
		.fail(function()
		{
			$("#message").html("An error occured while loading your lists.  Please try again later.");
		});
	}
	
	function assignFields(id)
	{
		editId=id;
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadListFields", id:id}
		})
		.done(function(data)
		{
			var fields=$.parseJSON(data);
			var html="Select the fields for this list:<br /><br />";
			$.each(fields, function(k, field)
			{
				html+="<input type='checkbox' class='list-field-checkbox' data-field-id='" + field.id + "' " +  (field.isPartOfList?"checked":"") + " />&nbsp; " + field.title + "<br />";
			});
			$("#fields-dialog").html(html);
			$("#fields-dialog").dialog("open");
		})
		.fail(function()
		{
			alert("Cound not load dialog for assigning fields");
		});
	}
	
	function saveFields()
	{
		//get all the fields to save...
		var fields=new Array();
		$(".list-field-checkbox").each(function()
		{
			if (!($(this).prop("checked")))
			{
				return; //continue the jQuery loop
			}
			
			fields.push($(this).data("field-id"));
		});
		
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"saveListFields", fields:JSON.stringify(fields), list:editId}
		})
		.done(function(data)
		{
			alert(data);
		})
		.fail(function()
		{
			alert("Could not save fields.");
		});
	}
	
	function assignProjectTypes(id)
	{
		editId=id;
		$("#ptypes-dialog").dialog("open");
		$("#ptypes-dialog").html("Loading...");
		//load project types for this list
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"loadListProjectTypes", id:id}
		})
		.done(function(data)
		{
			var ptypes=$.parseJSON(data);
			//output them each with a checkbox
			html="";
			$.each(ptypes, function(k, v)
			{
				html+="<input type='checkbox' data-ptype-id='" + v.id + "' class='field_ptypeid_checkbox' " + (v.isForField==true?"checked":"") + "/> " + v.title + "<br />";
			});
			console.log(html);
			$("#ptypes-dialog").html(html);
		})
		.fail(function()
		{
			alert("Failed to load project types");
		});
	}
	
	function saveListPTypes()
	{
		//get all the user groups
		var ptypes = new Array();
		$(".field_ptypeid_checkbox").each(function()
		{
			if ($(this).prop("checked")==true)
			{
				ptypes.push($(this).data("ptype-id"));
			}
		});
		$.ajax(
		{
			url:"php/ajax.php",
			type:"POST",
			data:{task:"saveListPTypes", list_id:editId, projectTypes:JSON.stringify(ptypes)}
		})
		.done(function(data)
		{
			if (data=="ok")
			{
				alert("Saved");
				$("#ptypes-dialog").dialog("close");
				loadAllFields();
			}
		})
		.fail(function()
		{
			alert("Failed to save project types for this checklist.");
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
			Lists
		</h1>
		<button onclick="createList();">New List</button>
		<div id="message"></div>
		<table id="listTable">
			<thead>
				<th>ID</th>
				<th>Title</th>
			</thead>
			<tbody id="listRows">
				
			</tbody>
		</table>
		<div id="list-dialog">
			<p>
				Please enter a title for your new field and click Submit to save your field.
			</p>
			<table>
				<tr>
					<td>
						Title
					</td>
					<td>
						<input type="text" id="list_title" />
					</td>
				</tr>
			</table>
		</div>
		
		<div id="ptypes-dialog" title="Project Types">
		
		</div>
		<div id="fields-dialog" title="Project Types">
		
		</div>
</body>
</html>
