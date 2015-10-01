<?php

	session_start();
	
	include("config.php");
	
	$db_host = $db_user = $db_pass = $db_name = "";
	
	$config_writable = is_writable('config.php');
	$uploads_writable = is_writable($images_path);
	
	if( !$config_writable || !$uploads_writable )
	{
		define("_ERROR_", "Setup cannot continue because required files and directories are not all writable!");
	}
	else
	if( isset($_POST['setup_complete']) )
	{
		$db_host = $_POST['db_host'];
		$db_user = $_POST['db_user'];
		$db_pass = $_POST['db_pass'];
		$db_name = $_POST['db_name'];
		
		$test_connection = @mysql_connect($db_host, $db_user, $db_pass);
		
		if( $test_connection )
		{
			
			if( @mysql_select_db($db_name) )
			{				
				$sql_string1 = "CREATE TABLE IF NOT EXISTS `mbg_albums` (
  `AlbumID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `AlbumName` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `DateCreated` int(20) NOT NULL,
  `Thumbnail1Size` varchar(10) DEFAULT NULL,
  `Thumbnail2Size` varchar(10) DEFAULT NULL,
  `Thumbnail3Size` varchar(10) DEFAULT NULL,
  `OrderID` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`AlbumID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;";
				
				$sql_string2 = "CREATE TABLE IF NOT EXISTS `mbg_images` (
  `ImageID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `AlbumID` int(11) unsigned NOT NULL,
  `ImagePath` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `UploadDate` int(20) NOT NULL,
  `OrderID` int(20) NOT NULL,
  PRIMARY KEY (`ImageID`),
  KEY `AlbumID` (`AlbumID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=251 ;";

				
				$sql_string3 = "ALTER TABLE `mbg_images` ADD CONSTRAINT `mbg_images_ibfk_1` FOREIGN KEY (`AlbumID`) REFERENCES `mbg_albums` (`AlbumID`);";
				
				$username = $_POST['username'];
				$password = $_POST['password'];
				
				if( $username && $password )
				{
					@mysql_query($sql_string1);
					@mysql_query($sql_string2);
					@mysql_query($sql_string3);
					
					// Open Config File
					$fp = fopen("config.php", "r+");
					$new_file_string = "";
					
					while($line = fgets($fp))
					{
						if( $username && preg_match('/\$admin_username/', $line) )
						{
							$new_file_string .= "\t" . '$admin_username = "'.addslashes($username).'";' . "\n";
						}
						else
						if( $password && preg_match('/\$admin_password/', $line) )
						{
							$new_file_string .= "\t" . '$admin_password = "'.addslashes($password).'";' . "\n";
						}
						else
						if( $db_host && preg_match('/\$db_host/', $line) )
						{
							$new_file_string .= "\t" . '$db_host = "'.addslashes($db_host).'";' . "\n";
						}
						else
						if( $db_user && preg_match('/\$db_user/', $line) )
						{
							$new_file_string .= "\t" . '$db_user = "'.addslashes($db_user).'";' . "\n";
						}
						else
						if( preg_match('/\$db_pass/', $line) )
						{
							$new_file_string .= "\t" . '$db_pass = "'.addslashes($db_pass).'";' . "\n";
						}
						else
						if( $db_name && preg_match('/\$db_name/', $line) )
						{
							$new_file_string .= "\t" . '$db_name = "'.addslashes($db_name).'";' . "\n";
						}
						else
						{
							$new_file_string .= $line;
						}
					}
					
					fclose($fp);
					
					$fp = fopen("config.php", "w");
					fwrite($fp, $new_file_string);
					fclose($fp);
					
					define("_SUCCESS_", "Setup has finished installing and configuring back-end gallery. You can now login with your chosen admin username and password. <br /><br />Also don't forget to delete install.php file! <a href=index.php>Click to continue</a>");
					$doexit = true;
				}
				else
				{
					define("_ERROR_", "Please choose admin username and password!");
				}
			}
			else
			{
				define("_ERROR_", "Database `$db_name` doesn't exists!");
			}
			
			@mysql_close($test_connection);
		}
		else
		{
			define("_ERROR_", "Cannot connect to your MySQL server. Make sure you've entered valid connection details!");
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Installer - Mini Back-end Gallery</title>
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<link href="css/main.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Mini Back-end Gallery installation</h1>
<div class="separator" style="margin-bottom:10px;"></div>
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
<?php
	if( !$doexit && $_GET['step'] == 2 && $config_writable && $uploads_writable )
	{
		?>
        <h2>Phase 2 - Database Tables Installation &amp; Admin User</h2>
        <form id="form1" name="form1" method="post" action="">
          <table border="0" cellspacing="2" cellpadding="2">
            <tr>
              <td height="20" colspan="2"><strong>Database Information</strong></td>
            </tr>
            <tr>
              <td width="140"><label for="db_host">Host:</label></td>
              <td>
              <input type="text" name="db_host" id="db_host" class="input" value="<?php echo $db_host; ?>" /></td>
            </tr>
            <tr>
              <td><label for="db_user">Username:</label></td>
              <td>
              <input type="text" name="db_user" id="db_user" class="input" value="<?php echo $db_user; ?>" /></td>
            </tr>
            <tr>
              <td><label for="db_pass">Password:</label></td>
              <td>
              <input type="password" name="db_pass" id="db_pass" class="input" /></td>
            </tr>
            <tr>
              <td><label for="db_name">Database Name:</label></td>
              <td>
              <input type="text" name="db_name" id="db_name" class="input" value="<?php echo $db_name; ?>" /></td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
              <td height="20" colspan="2"><strong>Admin User</strong></td>
            </tr>
            <tr>
              <td><label for="username">Username:</label></td>
              <td>
              <input type="text" name="username" id="username" class="input" /></td>
            </tr>
            <tr>
              <td><label for="password">Password:</label></td>
              <td>
              <input type="password" name="password" id="password" class="input" /></td>
            </tr>
            <tr>
              <td colspan="2" align="right" style="padding-top:6px;"><input type="submit" name="setup_complete" id="setup_complete" value="Setup complete" class="button" /></td>
            </tr>
          </table>
        </form>
        <?php
	}
	else
	if( !$doexit )
	{
?>
<h2>Phase 1 - Permissions Check</h2>
<div class="<?php echo $config_writable ? 'writable' : 'not_writable'; ?>">Config file is: <strong><?php echo $config_writable ? 'writable' : 'not writable'; ?></strong></div>
<div class="<?php echo $uploads_writable ? 'writable' : 'not_writable'; ?>">Images directory <strong><em><?php echo $images_path; ?></em></strong> is: <strong><?php echo $uploads_writable ? 'writable' : 'not writable'; ?></strong></div>
<?php
		if( $config_writable && $uploads_writable )
		{
			?>
<a href="?step=2" class="button">Continue / Next step</a>
			<?php
		}
	
	}
?>
</body>
</html>