<?php
	$id = $_GET['id'];
?>
<div class="separator"></div>
<?php

	if( imageExists($id) )
	{
		$image = getImage($id);
		
		$name = $image['Name'];
		
		$image_path = $image['ImagePath'];
		$thumbnail_0 = dirname($image_path) . '/th_' . basename($image_path);
		$thumbnail_1 = dirname($image_path) . '/th1_' . basename($image_path);
		$thumbnail_2 = dirname($image_path) . '/th2_' . basename($image_path);
		$thumbnail_3 = dirname($image_path) . '/th3_' . basename($image_path);
		?>
        <br />
        <a class="view_image" href="<?php echo $image_path; ?>" title="Click to view original size">
        	<img src="<?php echo $image_path; ?>" width="800" />
        </a>
        
        <div class="clear"></div>
<div class="thumbnails">
        <strong>Thumbnails</strong><br />
        <?php
		
			if( file_exists($thumbnail_0) )
			{
				?>
  <img class="thumbnail" src="<?php echo $thumbnail_0; ?>" align="left">
                <?php
			}
		
			if( file_exists($thumbnail_1) )
			{
				?>
  <img class="thumbnail" src="<?php echo $thumbnail_1; ?>" align="left">
                <?php
			}
		
			if( file_exists($thumbnail_2) )
			{
				?>
                <img class="thumbnail" src="<?php echo $thumbnail_2; ?>" align="left">
                <?php
			}
		
			if( file_exists($thumbnail_3) )
			{
				?>
                <img class="thumbnail" src="<?php echo $thumbnail_3; ?>" align="left">
                <?php
			}
		?>
  <div class="clear"></div>
        
  <form name="form1" method="post" action="">
  	<input name="image_id" type="hidden" value="<?php echo $id; ?>">
    	<label for="name">Image Name:</label>      
        <textarea name="name" id="name" style="width:800px; padding:5px;" cols="45" rows="5"><?php echo $name; ?></textarea><br>
        <input type="submit" class="save_changes" name="change_img_name" id="change_img_name" value="Save changes">
  </form>
</div>

	<div class="separator"></div>
    
        <?php
	}
	else
	{
		?>
<br /><br />
<div class="error">Image doesn't exists! Please go back.</div>
<?php
	}
?>