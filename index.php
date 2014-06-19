<?php 
	session_start();
	/*****************
	ENTRY POINT FOR GOTPROJECTS
	change made by server
	******************/
	require_once(dirname(__FILE__) . "/php/includes.php"); 
?>
<!DOCTYPE html>
<head>
	<?php renderHead(); ?>
</head>
<body>
	<div class="header">
		<?php renderMenu(); ?>
	</div>
	<div class="content">
		<h1 class="page_heading">
			Welcome to GotProjects
		</h1>
	</div>
	
</body>
</html>
