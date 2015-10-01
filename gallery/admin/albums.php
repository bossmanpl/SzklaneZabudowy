<?php
	$q = mysql_query("SELECT *, (SELECT COUNT(*) FROM `mbg_images` WHERE `AlbumID` = t1.AlbumID) total_images FROM `mbg_albums` t1 ORDER BY `OrderID` ASC");
?>
<script type="text/javascript" src="js/albums.js"></script>
<table class="data_table" border="0" cellspacing="0" cellpadding="0">
 <thead>
  <tr>
    <th width="240" align="left">Album Name</th>
    <th width="140" align="left">Date Created</th>
    <th width="80" align="left">Images</th>
    <th width="340" align="left">Options</th>
  </tr>
 </thead>
 
 <tbody>
 <?php
 while($r = mysql_fetch_array($q))
 {
	 $id = $r['AlbumID'];
	 $name = $r['AlbumName'];
	 $date_c = date("d/m/Y", $r['DateCreated']);
	 $total_images = $r['total_images'];
 ?>
  <tr class="album_entry" data-albumid="<?php echo $id; ?>">
    <td width="240"><?php echo $name; ?></td>
    <td width="140" class="gray"><?php echo $date_c; ?></td>
    <td width="80"><?php echo $total_images; ?></td>
    <td><a href="?action=album&amp;id=<?php echo $id; ?>" class="manage">Manage Images</a> <a href="?deletealbum=<?php echo $id; ?>" class="delete">Delete Album</a> <a href="?moveup=<?php echo $id; ?>" class="up">Up</a> <a href="?movedown=<?php echo $id; ?>" class="down">Down</a></td>
  </tr>
 <?php
 }
 
 if( !mysql_num_rows($q) )
 {
	 ?>
     <tr>
      <td colspan="4">There is no album created</td>
     </tr>
     <?php
 }
 ?>
 </tbody>
</table>

<div class="loader">Loading...</div>