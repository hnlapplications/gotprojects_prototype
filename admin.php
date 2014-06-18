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
			// Load Users and User Groups to populate drop downs
			$.ajax(
			{
				url:"php/ajax.php",
				type:"POST",
				data:{task:"loadUserData"}
			})
			.done(function(data)
			{
				var json = $.parseJSON(data);
				
				$.each(json.users, function(k, v)
				{
					$('#userSelect').append( new Option(v,v) );
				});
				
				$.each(json.userGroup, function(k, v)
				{
					$('#userGroupSelect').append( new Option(v,v) );
				});
			})
			.fail(function()
			{
				alert("An error occurred.  Please try again later.");
			});
					
			// Register User Group Form
			$("#userGroupForm").submit(function(e)
			{	
				if ($("#usergroupName").val() != "")
				{
					e.preventDefault();
					$.ajax(
					{
						url:"php/ajax.php",
						type:"POST",
						data:{task:"registerUserGroup", usergroupName:$("#usergroupName").val()}
					})
					.done(function(data)
					{
						//alert(data);
						
						var json = $.parseJSON(data);
						
						if (json.result!="ok")
						{
							alert("Usergroup creation failed.  Please try again.");
						}
						else
						{
							alert("Usergroup created Succesfully.");
								window.location="admin.php";
						}
					})
					.fail(function()
					{
						alert("An error occurred while logging in.  Please try again later.");
					});
				}
				else
				{
					alert("Please enter a name.");
				}
			});
			
			// Assign User to User Group Form
			$("#assignUser").submit(function(e)
			{	
				if ($("#userSelect").val() != "0" && $("#userGroupSelect").val() != "0")
				{
					e.preventDefault();
					$.ajax(
					{
						url:"php/ajax.php",
						type:"POST",
						data:{task:"assignUserGroup", userSelect:$("#userSelect").val(), userGroupSelect:$("#userGroupSelect").val()}
					})
					.done(function(data)
					{
						//alert(data);
						
						var json = $.parseJSON(data);
						
						if (json.result!="ok")
						{
							alert("Usergroup Assignment failed.  Please try again.");
						}
						else
						{
							alert("Usergroup Assigned Succesfully.");
								window.location="admin.php";
						}
					})
					.fail(function()
					{
						alert("An error occurred while logging in.  Please try again later.");
					});
				}
				else
				{
					alert("Please Choose Values.");
				}
			});
			
		});
		
	</script>
	
</head>
<body>
	<div class="header">
		<?php renderMenu(); ?>
	</div>
	<div class="content">
		<h1 class="page_heading">
			Welcome to GotProjects Administration
		</h1>
		
		<div id = "userGroupRegister" class="userGroupRegister">
			<h3> Register User group </h3>
			<form id="userGroupForm">
				<table>
					<tr>
						<td>
							Usergroup Name
						</td>
						<td>
							<input type="text" id="usergroupName">
						</td>
					</tr>
				</table>
				<button>Create</button>
			</form>
			<br />
		</div>
		
		<div id = "assignUser" class="assignUser">
			<h3> Assign User to User group </h3>
			<form id="userGroupForm">
				<table>
					<tr>
						<td>
							<select name='userSelect' id = 'userSelect' >
								<option value='0' selected ="selected" > -- Select User -- </option>
							</select>
						</td>
						<td>
							<select name='userGroupSelect' id = 'userGroupSelect' >
								<option value='0' selected ="selected" > -- Select User Group -- </option>
							</select>
						</td>
					</tr>
				</table>
				<br />
				<button>Assign User to Group</button>
			</form>
			<br />
		</div>
	
	</div>
	
	
	
</body>
</html>