<script type="text/javascript">
	$(function()
	{
		$("label").disableSelection();
		sizeCheck(1);
		sizeCheck(2);
		sizeCheck(3);
		
		$("#edit_album").submit(function()
		{
			var album_name = $("#album_name");
			
			if( !album_name.val().length )
			{
				album_name.focus();
				return false;
			}
		});
	});
	
	function sizeCheck(size)
	{
		var chckox = $("#thumbnail"+size);
		
		chckox.click(function()
		{
			if( $(this).attr('checked') )
			{
				$(".size"+size).show();
				$("#size"+size).select();
			}
			else
			{
				$(".size"+size).hide();
			}
		});
	}
</script>
<div class="edit_gallery">
<?php
	$thumbnail1 = $album['Thumbnail1Size'];
	$thumbnail2 = $album['Thumbnail2Size'];
	$thumbnail3 = $album['Thumbnail3Size'];
?>
<form action="?action=album&id=<?php echo $album['AlbumID']; ?>" method="post" name="edit_album" id="edit_album">
  <table border="0" cellspacing="2" cellpadding="2">
    <tr>
      <td>
      <label for="album_name">Album Name:</label>
      <input type="text" name="album_name" class="input" id="album_name" value="<?php echo addslashes($album['AlbumName']); ?>"></td>
    </tr>
    <tr>
      <td height="90">
      <label for="description">Description:</label>
      <textarea name="description" id="description" class="input" cols="45" rows="5"><?php echo $album['Description']; ?></textarea></td>
    </tr>
    <tr>
      <td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="thumbnail1" type="checkbox" id="thumbnail1" <?php echo $thumbnail1 ? 'checked' : ''; ?> value="1">
            </td>
          <td><label for="thumbnail1">Create Thumbnail [1]</label></td>
          </tr>
      </table>
        <table class="size1" style="display:<?php echo $thumbnail1 ? 'block' : 'none'; ?>" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="right_padding"><label for="size1">Set thumbnail size:</label><br></td>
            <td><input type="text" name="size1" id="size1" value="<?php echo $thumbnail1; ?>" class="input" size="6"></td>
          </tr>
      </table></td>
    </tr>
    <tr><td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="thumbnail2" type="checkbox" id="thumbnail2" <?php echo $thumbnail2 ? 'checked' : ''; ?> value="1">
            </td>
          <td><label for="thumbnail2">Create Thumbnail [2]</label></td>
          </tr>
      </table>
        <table class="size2" style="display:<?php echo $thumbnail2 ? 'block' : 'none'; ?>" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="right_padding"><label for="size2">Set thumbnail size:</label><br></td>
            <td><input type="text" name="size2" id="size2" value="<?php echo $thumbnail2; ?>" class="input" size="6"></td>
          </tr>
      </table></td>
    </tr>
    <tr><td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="thumbnail3" type="checkbox" id="thumbnail3" <?php echo $thumbnail3 ? 'checked' : ''; ?> value="1">
            </td>
          <td><label for="thumbnail3">Create Thumbnail [3]</label></td>
          </tr>
      </table>
        <table class="size3" style="display:<?php echo $thumbnail3 ? 'block' : 'none'; ?>" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="right_padding"><label for="size3">Set thumbnail size:</label><br></td>
            <td><input type="text" name="size3" id="size3" value="<?php echo $thumbnail3; ?>" class="input" size="6"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td><input type="submit" name="edit_album" class="button" id="edit_album" value="Edit Album"></td>
    </tr>
  </table>
</form>
</div>