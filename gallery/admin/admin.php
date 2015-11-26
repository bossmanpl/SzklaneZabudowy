<?php

	/**
	 * Mini Back-end Gallery
	 *
	 * Created by: Arlind Nushi
	 * Email: arlindd@gmail.com
	 */

	session_start();

	error_reporting(E_ERROR | E_WARNING | E_PARSE);

	include("config.php");
	
	$action = $_GET['action'];
	$action = strtolower($action);
	
	include('mysql.open.php');
	include("inc/ImageTools.class.php");
	include("functions.php");
	
	if( !isLoggedIn($admin_username, $admin_password) )
	{
		if( $upload_image = $_FILES['upload_image'] )
		{
			include("misc.php");
		}
		else
			header("Location: index.php");
			
		exit;
	}
	
	include("misc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.uploadify-v2.1.4/swfobject.js"></script>
<script type="text/javascript" src="js/jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="js/misc.js"></script>
<link href="css/ui-lightness/jquery-ui-1.8.9.custom.css" rel="stylesheet" type="text/css" />
<link href="css/main.css" rel="stylesheet" type="text/css" />
<link href="js/jquery.uploadify-v2.1.4/uploadify.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	if( file_exists("install.php") )
	{
		define("_ERROR_", "Please delete install.php file!");
	}
?>
	<?php
		if( defined("_SUCCESS_") )
		{
	?>
    <div class="success">
    	<?php echo _SUCCESS_; ?>
    </div>
    <?php
		}
	?>
    
	<?php
		if( defined("_ERROR_") )
		{
	?>
    <div class="error">
    	<?php echo _ERROR_; ?>
    </div>
    <?php
		}
	?>
    
	<a href="?action=settings" class="button">General Settings</a>
	<a href="admin.php" class="button">Manage Albums</a>
	<a href="?action=new_album" class="button">Create New Album</a>
    
    <?php
		
		switch( $action )
		{
			case "new_album":
				include("create_new_album.php");
				break;
			
			case "album":
				include("album_manage.php");
				break;
			
			case "editimage":
				include("editimage.php");
				break;
			
			case "settings":
				include("settings.php");
				break;
			
			default:
				include("albums.php");
		}
	?>
    
    <div class="copyrights">
    &copy; Mini Back-end Gallery created by <a href="mailto:arlindd@gmail.com">Arlind Nushi</a> - <a href="api.php">API</a> - <a href="example.php">Integration</a> <a href="?logout" class="logout">Logout</a>
    </div>
</body>
</html>
<?php
	@mysql_close($connect);
?>