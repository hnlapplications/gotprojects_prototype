<?php

	session_start();
	
	if(isset($_SESSION['uid']))
		unset($_SESSION['uid']);
		
	if(isset($_SESSION['group_id']))
		unset($_SESSION['group_id']);
		
	if(isset($_SESSION['username']))
		unset($_SESSION['username']);
	
header('Location: index.php');
	

?>