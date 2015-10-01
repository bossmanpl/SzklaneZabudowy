<?php

	// Delete
	if( $id = $_GET['deletealbum'] )
	{
		if( deleteAlbum($id) )
		{
			define('_SUCCESS_', 'Album has been deleted successfully.');
		}
		else
		{
			define('_ERROR_', "Album doesn't exists or it has been deleted before!");
		}
	}
	
	// Move Up
	if( $id = $_GET['moveup'] )
	{
		moveAlbumUP($id);
	}
	
	// Move Down
	if( $id = $_GET['movedown'] )
	{
		moveAlbumDOWN($id);
	}
	
	// Create New Album
	if( isset($_POST['create_album']) )
	{
		$name = mysql_real_escape_string($_POST['album_name']);
		$description = mysql_real_escape_string($_POST['description']);
		
		$thumbnail1 = $_POST['thumbnail1'];
		$size1 = strtolower($_POST['size1']);
		
		$thumbnail2 = $_POST['thumbnail2'];
		$size2 = strtolower($_POST['size2']);
		
		$thumbnail3 = $_POST['thumbnail3'];
		$size3 = strtolower($_POST['size3']);
		
		if( $name )
		{
			$date_created = time();
			$order_id = time();
			$q = mysql_query("INSERT INTO `mbg_albums`(`AlbumName`,`Description`,`DateCreated`,`OrderID`) VALUES('$name','$description','$date_created',$order_id)") or die(mysql_error());
			
			define("_SUCCESS_", "Album created successfully.");
			$last_id = mysql_insert_id();
			
			// Thumbnail 1 Rules
			if( $thumbnail1 )
			{
				$size1 = explode("x", $size1);
				
				if( $size1[0] > 0 && $size1[1] > 0 )
				{
					mysql_query("UPDATE `mbg_albums` SET `Thumbnail1Size` = '$size1[0]x$size1[1]' WHERE `AlbumID` = '$last_id'");
				}
			}
			
			// Thumbnail 2 Rules
			if( $thumbnail2 )
			{
				$size2 = explode("x", $size2);
				
				if( $size2[0] > 0 && $size2[1] > 0 )
				{
					mysql_query("UPDATE `mbg_albums` SET `Thumbnail2Size` = '$size2[0]x$size2[1]' WHERE `AlbumID` = '$last_id'");
				}
			}
			
			// Thumbnail 3 Rules
			if( $thumbnail3 )
			{
				$size3 = explode("x", $size3);
				
				if( $size3[0] > 0 && $size3[1] > 0 )
				{
					mysql_query("UPDATE `mbg_albums` SET `Thumbnail3Size` = '$size3[0]x$size3[1]' WHERE `AlbumID` = '$last_id'");
				}
			}
		}
	}
	
	// Start Uploading Files
	if( $upload_image = $_FILES['upload_image'] )
	{
		$album_id = $_GET['album_id'];
		$allowed_file_types = array("jpg","png","jpeg","gif");
		
		$file_name = $upload_image['name'];
		$file_type = strtolower(end(explode(".", $file_name)));
		$file_tmp  = $upload_image['tmp_name'];
		
		$path_to_upload_files = $images_path;
		
		if( in_array($file_type, $allowed_file_types) )
		{
			if( albumExists($album_id) )
			{
				$album = getAlbum($album_id);
				
				// Generate Name
				switch( strtolower($naming) )
				{
					case "hash":
					case "random":
						$new_name = substr(time(), 5) . '_' . substr(md5(time()+rand(1000,9999)), 0, 6) . '_' . substr(sha1(time()+rand(1000,9999)), 0, 6) . '.' . $file_type;
						break;
					
					case "normal":
						$new_name = $file_name;
						break;
						
					default:
						$new_name = str_replace(array(',',"'",'"'), '-', strtolower($file_name));
				}
				
				$album_path = $path_to_upload_files . 'album_' . $album_id . '/';
				$upload_file_path = $album_path . $new_name;
				
				if( !file_exists($album_path) )
				{
					mkdir($album_path);
				}
				
				move_uploaded_file($file_tmp, $upload_file_path);
				$imagesize = getimagesize($upload_file_path);
				
				// Create Default Thumbnail
				$dt_pref_size = array(110, 95);
				$dt_ps = getPreferedSize($dt_pref_size[0], $dt_pref_size[1], $imagesize[0], $imagesize[1]);
				
				$default_thumbnail = new ImageTools($upload_file_path);
				$default_thumbnail->resizeNewByWidth($dt_pref_size[0], $dt_pref_size[1], $dt_ps[0], "#FFF");
				$default_thumbnail->save($album_path, "th_$new_name", 100, true);
				
				
				// Create Defined Thumbnail 1
				if( $thumbnail1 = $album['Thumbnail1Size'] )
				{
					$thumbnail1 = explode("x", $thumbnail1);
					
					if( $thumbnail1[0] > 0 && $thumbnail1[1] > 0 )
					{
						$th1_pref_size = getPreferedSize($thumbnail1[0], $thumbnail1[1], $imagesize[0], $imagesize[1]);
						
						$thumbnail1_create = new ImageTools($upload_file_path);
						$thumbnail1_create->resizeNewByWidth($thumbnail1[0], $thumbnail1[1], $th1_pref_size[0], "#FFF");
						$thumbnail1_create->save($album_path, "th1_$new_name", 100, true);
					}
				}
				
				
				// Create Defined Thumbnail 2
				if( $thumbnail2 = $album['Thumbnail2Size'] )
				{
					$thumbnail2 = explode("x", $thumbnail2);
					
					if( $thumbnail2[0] > 0 && $thumbnail2[1] > 0 )
					{
						$th2_pref_size = getPreferedSize($thumbnail2[0], $thumbnail2[1], $imagesize[0], $imagesize[1]);
						
						$thumbnail2_create = new ImageTools($upload_file_path);
						$thumbnail2_create->resizeNewByWidth($thumbnail2[0], $thumbnail2[1], $th2_pref_size[0], "#FFF");
						$thumbnail2_create->save($album_path, "th2_$new_name", 100, true);
					}
				}
				
				
				// Create Defined Thumbnail 3
				if( $thumbnail3 = $album['Thumbnail3Size'] )
				{
					$thumbnail3 = explode("x", $thumbnail3);
					
					if( $thumbnail3[0] > 0 && $thumbnail3[1] > 0 )
					{
						$th3_pref_size = getPreferedSize($thumbnail3[0], $thumbnail3[1], $imagesize[0], $imagesize[1]);
						
						$thumbnail3_create = new ImageTools($upload_file_path);
						$thumbnail3_create->resizeNewByWidth($thumbnail3[0], $thumbnail3[1], $th3_pref_size[0], "#FFF");
						$thumbnail3_create->save($album_path, "th3_$new_name", 100, true);
					}
				}
				
				addImage($album_id, $upload_file_path);
				
				$last_id = mysql_insert_id();
				
				$image = getImage($last_id);
				$image['errors'] = false;
				$image['thumbnailUrl'] = dirname($image['ImagePath']) . '/th_' . basename($image['ImagePath']);
				
				$json = json_encode($image);
				
				echo $json;
			}
			else
			{
				echo json_encode( array("errors" => "AlbumNotExists") );
			}
		}
		else
		{
			echo json_encode( array("errors" => "InvalidFileType") );
		}
		
		@mysql_close($connect);
		exit;
	}
	
	// Change Order of Images
	if( $_GET['images_new_order'] && $_GET['order_string'] )
	{
		$album_id = $_GET['images_new_order'];
		$order_string = $_GET['order_string'];
		
		$ids = explode(",", $order_string);
		
		foreach($ids as $id_str)
		{
			$order_row = explode("=", $id_str);
			
			$order_id = $order_row[0];
			$image_id = $order_row[1];
			
			mysql_query("UPDATE `mbg_images` SET `OrderID` = '$order_id' WHERE `ImageID` = '$image_id' AND `AlbumID` = '$album_id'");
		}
		
		@mysql_close($connect);
		exit;
	}
	
	// Change Order of Albums
	if( $_GET['albums_new_order'] && $_GET['order_string'] )
	{
		$order_string = $_GET['order_string'];
		
		$ids = explode(",", $order_string);
		
		foreach($ids as $id_str)
		{
			$order_row = explode("=", $id_str);
			
			$order_id = $order_row[0];
			$album_id = $order_row[1];
			
			mysql_query("UPDATE `mbg_albums` SET `OrderID` = '$order_id' WHERE `AlbumID` = '$album_id'");
		}
		
		@mysql_close($connect);
		exit;
	}
	
	// Delete Image from Album
	if( $image_id = $_GET['deleteimageid'] )
	{
		deleteImage($image_id);
		
		@mysql_close($connect);
		exit;
	}

	// Change Image Name
	if( isset($_POST['change_img_name']) )
	{
		$image_id = $_POST['image_id'];
		$name = $_POST['name'];
		
		if( $image_id )
		{
			setImageName($image_id, $name);
			define("_SUCCESS_", "Image name has been saved");
		}
	}
	
	// Edit Album Info
	if( isset($_POST['edit_album']) )
	{
		$album_id = $_GET['id'];
		$name = $_POST['album_name'];
		$description = $_POST['description'];
		
		$thumbnail1 = $_POST['thumbnail1'];
		$size1 = $_POST['size1'];
		
		$thumbnail2 = $_POST['thumbnail2'];
		$size2 = $_POST['size2'];
		
		$thumbnail3 = $_POST['thumbnail3'];
		$size3 = $_POST['size3'];
		
		editAlbum($album_id, $name, $description, $thumbnail1 ? $size1 : null, $thumbnail2 ? $size2 : null, $thumbnail3 ? $size3 : null);
		define("_SUCCESS_", "Album edited successfully");
	}
	
	// Save Config File
	if( isset($_POST['settings_save_changes']) )
	{
		$admin_username = $_POST['admin_username'];
		$admin_password = $_POST['admin_password'];
		
		$gallery_title = $_POST['gallery_title'];
		$naming = $_POST['naming'];
		
		$thumbnail1size = strtolower($_POST['thumbnail1size']);
		$thumbnail2size = strtolower($_POST['thumbnail2size']);
		$thumbnail3size = strtolower($_POST['thumbnail3size']);
		
		if( $thumbnail1size && !preg_match("/^[0-9]+x[0-9]+$/", $thumbnail1size) )
		{
			$thumbnail1size = null;
		}
		
		if( $thumbnail2size && !preg_match("/^[0-9]+x[0-9]+$/", $thumbnail2size) )
		{
			$thumbnail2size = null;
		}
		
		if( $thumbnail3size && !preg_match("/^[0-9]+x[0-9]+$/", $thumbnail3size) )
		{
			$thumbnail3size = null;
		}
		
		if( is_writable("config.php") )
		{
			// Open Config File
			$fp = fopen("config.php", "r+");
			$new_file_string = "";
			
			while($line = fgets($fp))
			{
				if( $admin_username && preg_match('/\$admin_username/', $line) )
				{
					$new_file_string .= "\t" . '$admin_username = "'.addslashes($admin_username).'";' . "\n";
				}
				else
				if( $admin_password && preg_match('/\$admin_password/', $line) )
				{
					$new_file_string .= "\t" . '$admin_password = "'.addslashes($admin_password).'";' . "\n";
				}
				else
				if( $gallery_title && preg_match('/\$title/', $line) )
				{
					$new_file_string .= "\t" . '$title = "'.addslashes($gallery_title).'";' . "\n";
				}
				else
				if( $naming && preg_match('/\$naming/', $line) )
				{
					$new_file_string .= "\t" . '$naming = "'.addslashes($naming).'";' . "\n";
				}
				else
				if( $thumbnail1size && preg_match('/\$thumbnail_1_size/', $line) )
				{
					$new_file_string .= "\t" . '$thumbnail_1_size = "'.addslashes($thumbnail1size).'";' . "\n";
				}
				else
				if( $thumbnail2size && preg_match('/\$thumbnail_2_size/', $line) )
				{
					$new_file_string .= "\t" . '$thumbnail_2_size = "'.addslashes($thumbnail2size).'";' . "\n";
				}
				else
				if( $thumbnail3size && preg_match('/\$thumbnail_3_size/', $line) )
				{
					$new_file_string .= "\t" . '$thumbnail_3_size = "'.addslashes($thumbnail3size).'";' . "\n";
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
			
			define("_SUCCESS_", "Settings have been saved.");
			
		}
		else
			define("_ERROR_", "Config file is not writable!");
	}
	
	// Logout
	if( isset($_GET['logout']) )
	{
		session_destroy();
		setcookie("token", "-");
		header("Location: index.php");
	}
?>