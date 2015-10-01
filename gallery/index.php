<?php

	/**
	 * Mini Back-end Gallery
	 *
	 * Created by: Arlind Nushi
	 * Email: arlindd@gmail.com
	 */

	// This is an example of how you can built your own front-end gallery after successfull installation of this script
	// I'll demonstrate needed functions to make this gallery work as front-end
	
	############################################################################
	### BEFORE STARTING PLEASE MAKE SURE YOU HAVE CREATE SOME ALBUMS AND     ###
	### UPLOADED IMAGES IN ORDER TO ILLUSTRATE THIS EXAMPLE ON THE RIGHT WAY ###
	############################################################################
	
	// Wherever you are, you need to specify URL to your gallery Back-end
	$gallery_url = '../backend-gallery/';
	
	// We need to create MySQL so to that we include file called mysql.open.php on the gallery back-end directory, and also include config file
	// Make sure your back-end gallery path is correctly entered in order to make gallery front-end work properly
	include('../backend-gallery/config.php');
	include('../backend-gallery/mysql.open.php');
	
	// Include API functions of Gallery from gallery back-end directory
	include('../backend-gallery/functions.php');
	
	/***** MAIN JOB *****/
	
	
	
	
	// First of all, I want to parse list of all albums and tell you how to show them
	$all_albums = getAllAlbums('ASC'); // or DESC (ordering parameter)
	
	echo '<h2>Parse list of albums</h2>';
	echo '<ul>';
	foreach($all_albums as $album)
	{
		$id = $album['AlbumID'];
		$name = $album['AlbumName'];
		$description = $album['Description'];
		$date_created = date("d-m-Y", $album['DateCreated']);
		
		echo '<li><font size="5">'.$name.'</font> <br><small>[Album ID: '.$id.', Created on: '.$date_created.']<br>Description: '.$description.'</small></li>';
	}
	echo '</ul>';
	
	
	
	
	
	// Show number of total albums created
	echo 'Total albums created: ' . totalAlbums();
	echo '<br />';
	echo 'Total images uploaded: ' . count( getAllImages() );
	echo '<br />';
	
	
	
	
	
	// Parse all images in DESCENDING order
	$all_images = getAllImages('DESC');
	
	echo '<h2>Images from all albums</h2>';
	echo '<ul>';
	
	
	foreach($all_images as $image)
	{
		$id 				= $image['ImageID'];
		$album_id 			= $image['AlbumID'];
		$image_path 		= $gallery_url . $image['ImagePath'];
		$image_name 		= $image['Name'];
		$upload_date 		= date("d-m-Y H:i", $image['UploadDate']);
		$order_id 			= $image['OrderID'];
		$default_thumbnail 	= $gallery_url . $image['DefaultThumbnail'];
		$thumbnail_1		= $gallery_url . $image['Thumbnail1'];
		$thumbnail_2 		= $gallery_url . $image['Thumbnail2'];
		$thumbnail_3 		= $gallery_url . $image['Thumbnail3'];
		
		echo '<li style="float:left; margin:10px;">';
		echo '<a href="'.$image_path.'" target="_blank">';
		echo '<img src="'.$default_thumbnail.'" title="View Original Size / Image from Album ID: '.$album_id.'" />';
		echo '</a>';
		echo '</li>';
	}
	
	echo '</ul>';
	
	
	
	
	
	// Also you can parse Albums and Images in them simultaneously
	// Using already parsed albums on $all_images
	
	echo '<div style="clear:both;"></div>'; // Clear from floating
	
	echo '<h2>Albums with Images</h2>';
	echo '<ul>';
	foreach($all_albums as $album)
	{
		// Get Album Details
		$album_id = $album['AlbumID'];
		$name = $album['AlbumName'];
		$description = $album['Description'];
		$date_created = date("d-m-Y", $album['DateCreated']);
		
		// Get Images
		$images_for_this_album = getAlbumImages($album_id, 'ASC'); // From album id $album_id, with ASCENDING order
		
		echo '<li>';
		echo '<font size="5"><strong>'.$name.'</strong></font>'; // Album Name
		
		// Show images under this album
		echo '<ul>';
		
		foreach($images_for_this_album as $image)
		{
			// Get Single Image Details
			$id 				= $image['ImageID'];
			$album_id 			= $image['AlbumID'];
			$image_path 		= $gallery_url . $image['ImagePath'];
			$image_name 		= $image['Name'];
			$upload_date 		= date("d-m-Y H:i", $image['UploadDate']);
			$order_id 			= $image['OrderID'];
			$default_thumbnail 	= $gallery_url . $image['DefaultThumbnail'];
			$thumbnail_1		= $gallery_url . $image['Thumbnail1'];
			$thumbnail_2 		= $gallery_url . $image['Thumbnail2'];
			$thumbnail_3 		= $gallery_url . $image['Thumbnail3'];
			
			echo '<li style="float:left; margin:10px;">';
			echo '<a href="'.$image_path.'" target="_blank">';
			echo '<img src="'.$default_thumbnail.'" title="View Original Size" />';
			echo '</a>';
			echo '</li>';
		}
		
		echo '</ul>';
		echo '<div style="clear:both;"></div>'; // Clear from floating
		
		echo '</li>';
	}
	echo '</ul>';

	/***** END OF MAIN JOB *****/
	
	// At the end we close our connection with MySQL by simply including a file on the gallery back-end directory
	include('../backend-gallery/mysql.close.php');
?>