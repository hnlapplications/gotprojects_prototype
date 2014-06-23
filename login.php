<?php 
session_start();
require_once(dirname(__FILE__) . "/php/includes.php"); ?>
<!DOCTYPE html>
<head>
	<?php renderHead(); ?>
	<script type="text/javascript">
		
		// Display Functions
		function displayRegister()
		{
			document.getElementById('login').style.display='none';
			$("#signup").fadeIn();
		}
		
		function displayLogin()
		{
			document.getElementById('signup').style.display='none';
			$("#login").fadeIn();
		}
		
		$(document).ready(function()
		{
			// Login Form
			$("#loginForm").submit(function(e)
			{
				e.preventDefault();
				$.ajax(
				{
					url:"php/ajax.php",
					type:"POST",
					data:{task:"login", username:$("#username").val(), password:$("#password").val()}
				})
				.done(function(data)
				{
					//alert(data);
					
					var json = $.parseJSON(data);
					
					if (json.result!="ok")
					{
						alert("Username or password is incorrect.  Please try again.");
					}
					else
					{
						window.location="index.php";
					}
				})
				.fail(function()
				{
					alert("An error occurred while logging in.  Please try again later.");
				});
			});
			
			
			
			// Sign Up Form
			$("#userForm").submit(function(e)
			{
				e.preventDefault();
				$.ajax(
				{
					url:"php/ajax.php",
					type:"POST",
					data:{task:"signup", newusername:$("#newusername").val(), userpassword:$("#userpassword").val(), useremail:$("#useremail").val()}
				})
				.done(function(data)
				{
					//alert(data);
					
					var json = $.parseJSON(data);
					
					if (json.result!="ok")
					{
						alert("Problem Signing Up.  Please try again.");
					}
					else
					{
						alert("User Signed Up Successfully.  Please Log In.");
						window.location="index.php";
					}
				})
				.fail(function()
				{
					alert("An error occurred while logging in.  Please try again later.");
				});
			});
			
		});
		
	</script>
</head>
<body>
	<div id = "login" class="login">
		<h3> Login </h3>
		<form id="loginForm">
			<table>
				<tr>
					<td>
						Username
					</td>
					<td>
						<input type="text" id="username">
					</td>
				</tr>
				<tr>
					<td>
						Password
					</td>
					<td>
						<input type="password" id="password">
					</td>
				</tr>
			</table>
			<button>Login</button>
		</form>
		<br />
		<a href = "javascript:void(0)" onclick = "displayRegister()">Register</a>
	
	</div>
	
	<div id = "signup" class="signup" style = "display:none">
		<h3> Register </h3>
		<form id="userForm">
			<table>
				<tr>
					<td>
						Username
					</td>
					<td>
						<input type="text" id="newusername">
					</td>
				</tr>
				<tr>
					<td>
						Password
					</td>
					<td>
						<input type="password" id="userpassword">
					</td>
				</tr>
				<tr>
					<td>
						Email
					</td>
					<td>
						<input type="text" id="useremail">
					</td>
				</tr>
			</table>
			<button>Sign Up</button>
		</form>
		<br />
		<a href = "javascript:void(0)" onclick = "displayLogin()">Sign In</a>
	
	</div>
	
</body>
</html>
