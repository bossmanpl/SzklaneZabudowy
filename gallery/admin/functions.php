<?php

	function getAllAlbums($order = 'ASC')
	{
		$order = strtoupper($order) == 'ASC' ? 'ASC' : 'DESC';
		
		$q = mysql_query("SELECT * FROM `mbg_albums` ORDER BY `OrderID` $order");
		
		if( mysql_num_rows($q) )
		{
			$albums = array();
			
			while($r = mysql_fetch_array($q))
			{
				array_push($albums, $r);
			}
			
			return $albums;
		}
		
		return null;
	}
	
	function totalAlbums()
	{
		$q = mysql_query("SELECT COUNT(*) FROM `mbg_albums`");
		$r = mysql_fetch_row($q);
		
		return $r[0];
	}
	
	function albumExists($album_id)
	{
		$q = mysql_query("SELECT * FROM `mbg_albums` WHERE `AlbumID` = '".mysql_real_escape_string($album_id)."'");
		
		if( mysql_num_rows($q) )
			return true;
		
		return false;
	}
	
	function getAlbum($album_id)
	{
		if( albumExists($album_id) )
		{
			$q = mysql_query("SELECT * FROM `mbg_albums` WHERE `AlbumID` = '$album_id'");
			$r = mysql_fetch_array($q);
			
			return $r;
		}
		
		return null;
	}
	
	function moveAlbumUP($album_id)
	{
		if( albumExists($album_id) )
		{
			// Current Album
			$album = getAlbum($album_id);
			$order_id = $album['OrderID'];
			
			// Nearest Album
			$q0 = mysql_query("SELECT * FROM `mbg_albums` WHERE `OrderID` < $order_id AND `AlbumID` <> $album_id ORDER BY `OrderID` DESC LIMIT 0,1");
			
			
			if( mysql_num_rows($q0) )
			{
				$r0 = mysql_fetch_array($q0);
				$r0_album_id = $r0['AlbumID'];
				$r0_order_id = $r0['OrderID'];
				
				mysql_query("UPDATE `mbg_albums` SET `OrderID` = $r0_order_id WHERE `AlbumID` = $album_id");
				mysql_query("UPDATE `mbg_albums` SET `OrderID` = $order_id WHERE `AlbumID` = $r0_album_id");
			}
		}
	}
	
	function moveAlbumDOWN($album_id)
	{
		if( albumExists($album_id) )
		{
			// Current Album
			$album = getAlbum($album_id);
			$order_id = $album['OrderID'];
			
			// Nearest Album
			$q0 = mysql_query("SELECT * FROM `mbg_albums` WHERE `OrderID` > $order_id AND `AlbumID` <> $album_id ORDER BY `OrderID` ASC LIMIT 0,1");
			
			
			if( mysql_num_rows($q0) )
			{
				$r0 = mysql_fetch_array($q0);
				$r0_album_id = $r0['AlbumID'];
				$r0_order_id = $r0['OrderID'];
				
				mysql_query("UPDATE `mbg_albums` SET `OrderID` = $r0_order_id WHERE `AlbumID` = $album_id");
				mysql_query("UPDATE `mbg_albums` SET `OrderID` = $order_id WHERE `AlbumID` = $r0_album_id");
			}
		}
	}
	
	function deleteAlbum($album_id)
	{
		if( albumExists($album_id) )
		{
			$images = getAlbumImages($album_id);
			
			foreach($images as $img)
			{
				$image_id = $img['ImageID'];
				deleteImage($image_id);
			}
			
			mysql_query("DELETE FROM `mbg_albums` WHERE `AlbumID` = '$album_id'");
			return true;
		}
		
		return false;
	}
	
	function getAlbumImages($album_id, $order = 'ASC')
	{
		$order = strtoupper($order) == 'ASC' ? 'ASC' : 'DESC';
		
		if( albumExists($album_id) )
		{
			$q = mysql_query("SELECT * FROM `mbg_images` WHERE `AlbumID` = '$album_id' ORDER BY `OrderID` $order");
			
			$arr = array();
			
			while($r = mysql_fetch_array($q))
			{
				// Get Thumbnails
				$image_path = $r['ImagePath'];
				
				$default_thumbnail = dirname($image_path) . '/th_' . basename($image_path);
				$thumbnail_1 = dirname($image_path) . '/th1_' . basename($image_path);
				$thumbnail_2 = dirname($image_path) . '/th2_' . basename($image_path);
				$thumbnail_3 = dirname($image_path) . '/th3_' . basename($image_path);
				
				$r['DefaultThumbnail'] = $default_thumbnail;
				
				if( file_exists($thumbnail_1) )
					$r['Thumbnail1'] = $thumbnail_1;
					
				if( file_exists($thumbnail_2) )
					$r['Thumbnail2'] = $thumbnail_2;
					
				if( file_exists($thumbnail_3) )
					$r['Thumbnail3'] = $thumbnail_3;
					
				array_push($arr, $r);
			}
			
			return $arr;
		}
				
		return null;
	}
	
	function editAlbum($album_id, $album_name, $description = '', $thumbnail1 = null, $thumbnail2 = null, $thumbnail3 = null)
	{
		if( albumExists($album_id) && $album_name )
		{
			mysql_query("UPDATE `mbg_albums` SET `AlbumName` = '".mysql_real_escape_string($album_name)."', `Description` = '".mysql_real_escape_string($description)."' WHERE `AlbumID` = '$album_id'");
			
			if( $thumbnail1 )
			{
				$thumbnail1 = explode("x", $thumbnail1);
				
				if( $thumbnail1[0] > 0 && $thumbnail1[1] > 0 )
				{
					mysql_query("UPDATE `mbg_albums` SET `Thumbnail1Size` = '$thumbnail1[0]x$thumbnail1[1]' WHERE `AlbumID` = '$album_id'");
				}
			}
			else
			{
				mysql_query("UPDATE `mbg_albums` SET `Thumbnail1Size` = NULL WHERE `AlbumID` = '$album_id'");
			}
			
			if( $thumbnail2 )
			{
				$thumbnail2 = explode("x", $thumbnail2);
				
				if( $thumbnail2[0] > 0 && $thumbnail2[1] > 0 )
				{
					mysql_query("UPDATE `mbg_albums` SET `Thumbnail2Size` = '$thumbnail2[0]x$thumbnail2[1]' WHERE `AlbumID` = '$album_id'");
				}
			}
			else
			{
				mysql_query("UPDATE `mbg_albums` SET `Thumbnail2Size` = NULL WHERE `AlbumID` = '$album_id'");
			}
			
			if( $thumbnail3 )
			{
				$thumbnail3 = explode("x", $thumbnail3);
				
				if( $thumbnail3[0] > 0 && $thumbnail3[1] > 0 )
				{
					mysql_query("UPDATE `mbg_albums` SET `Thumbnail3Size` = '$thumbnail3[0]x$thumbnail3[1]' WHERE `AlbumID` = '$album_id'");
				}
			}
			else
			{
				mysql_query("UPDATE `mbg_albums` SET `Thumbnail3Size` = NULL WHERE `AlbumID` = '$album_id'");
			}
		}
	}
	
	function imageExists($image_id)
	{
		$q = mysql_query("SELECT * FROM `mbg_images` WHERE `ImageID` = '".mysql_real_escape_string($image_id)."'");
		
		if( mysql_num_rows($q) )
		{
			return true;
		}
		
		return false;
	}
	
	function getImage($image_id)
	{
		$q = mysql_query("SELECT * FROM `mbg_images` WHERE `ImageID` = '".mysql_real_escape_string($image_id)."'");
		
		if( mysql_num_rows($q) )
		{
			$r = mysql_fetch_array($q);
			
			// Get Thumbnails
			$image_path = $r['ImagePath'];
			
			$default_thumbnail = dirname($image_path) . '/th_' . basename($image_path);
			$thumbnail_1 = dirname($image_path) . '/th1_' . basename($image_path);
			$thumbnail_2 = dirname($image_path) . '/th2_' . basename($image_path);
			$thumbnail_3 = dirname($image_path) . '/th3_' . basename($image_path);
			
			$r['DefaultThumbnail'] = $default_thumbnail;
			
			if( file_exists($thumbnail_1) )
				$r['Thumbnail1'] = $thumbnail_1;
				
			if( file_exists($thumbnail_2) )
				$r['Thumbnail2'] = $thumbnail_2;
				
			if( file_exists($thumbnail_3) )
				$r['Thumbnail3'] = $thumbnail_3;
			
			return $r;
		}
		
		return null;
	}
	
	function deleteImage($image_id)
	{
		$image = getImage($image_id);
		
		if( $image )
		{
			$image_path = $image['ImagePath'];
			
			@unlink($image_path);
			@unlink(dirname($image_path) . "/th_" . basename($image_path));
			@unlink(dirname($image_path) . "/th1_" . basename($image_path));
			@unlink(dirname($image_path) . "/th2_" . basename($image_path));
			@unlink(dirname($image_path) . "/th3_" . basename($image_path));
			
			mysql_query("DELETE FROM `mbg_images` WHERE `ImageID` = '$image[ImageID]'");
			
			return true;
		}
		
		return false;
	}
	
	function setImageName($image_id, $new_name)
	{
		if( imageExists($image_id) )
		{
			mysql_query("UPDATE `mbg_images` SET `Name` = '".mysql_real_escape_string($new_name)."' WHERE `ImageID` = '$image_id'");
		}
	}
	
	function getAllImages($order = 'ASC')
	{
		$order = strtoupper($order) == 'ASC' ? 'ASC' : 'DESC';
		
		$q = mysql_query("SELECT * FROM `mbg_images` ORDER BY `OrderID` $order");
		
		if( mysql_num_rows($q) )
		{
			$images = array();
			
			while($r = mysql_fetch_array($q))
			{
				// Get Thumbnails
				$image_path = $r['ImagePath'];
				
				$default_thumbnail = dirname($image_path) . '/th_' . basename($image_path);
				$thumbnail_1 = dirname($image_path) . '/th1_' . basename($image_path);
				$thumbnail_2 = dirname($image_path) . '/th2_' . basename($image_path);
				$thumbnail_3 = dirname($image_path) . '/th3_' . basename($image_path);
				
				$r['DefaultThumbnail'] = $default_thumbnail;
				
				if( file_exists($thumbnail_1) )
					$r['Thumbnail1'] = $thumbnail_1;
					
				if( file_exists($thumbnail_2) )
					$r['Thumbnail2'] = $thumbnail_2;
					
				if( file_exists($thumbnail_3) )
					$r['Thumbnail3'] = $thumbnail_3;
				
				array_push($images, $r);
			}
			
			return $images;
		}
		
		return null;
	}
	
	/* These functions are outside the API of Mini back-end gallery */
	
	function addImage($album_id, $path_to_file)
	{
		if( albumExists($album_id) && file_exists($path_to_file) )
		{
			$path_to_file = mysql_real_escape_string($path_to_file);
			mysql_query("INSERT INTO `mbg_images`(`AlbumID`,`ImagePath`,`UploadDate`,`OrderID`) VALUES('$album_id','$path_to_file',".time().",".time().")");
		}
	}
	
	function isLoggedIn($username, $password)
	{
		$hash = sha1( $username  . md5($password) );
		
		if( $hash == $_SESSION['token'] || $hash == $_COOKIE['token'] )
			return true;
		
		return false;
	}
	
	function loginUser($username, $password)
	{
		$hash = sha1( $username  . md5($password) );
		
		$_SESSION['token'] = $hash;
		setcookie('token', $hash);
	}
	
	function getPreferedSize($pref_w, $pref_h, $current_w, $current_h, $recursion = false)
	{
		if( !$recursion )
		{
			$new_size = resize($pref_w, $current_w, $current_h);
			
			if( $pref_w <= $new_size[0]	&& $pref_h <= $new_size[1] )
			{
				return $new_size;
			}
			
			return getPreferedSize($pref_w, $pref_h, $new_size[0], $new_size[1], true);
		}
		else
		{
			$pct = 1 / $current_w;
			
			$new_w = $current_w + ceil($pct * $current_w);
			$new_h = $current_h + ceil($pct * $current_h);
			
			if( $pref_w <= $new_w && $pref_h <= $new_h )
			{
				return array($new_w, $new_h);
			}
			else
			{
				return getPreferedSize($pref_w, $pref_h, $new_w, $new_h, true);
			}
		}
		
		return array($current_w, $current_h);
	}
	
	function resize($target, $width, $height)
	{
		if($width > $height)
		{ 
			$percentage = ($target / $width); 
		} 
		else 
		{ 
			$percentage = ($target / $height); 
		}
		
		$new_width = round($width * $percentage); 
		$new_height = round($height * $percentage);
		
		return array($new_width, $new_height);
	}
?>