<script type="text/javascript">
	$(function()
	{
		$("label").disableSelection();
		sizeCheck(1);
		sizeCheck(2);
		sizeCheck(3);
		
		$("#create_new_album").submit(function()
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
<div class="separator"></div>
<h1>Create New Album</h1>
<form action="?" method="post" name="create_new_album" id="create_new_album">
  <table border="0" cellspacing="2" cellpadding="2">
    <tr>
      <td>
      <label for="album_name">Album Name:</label>
      <input type="text" name="album_name" class="input" id="album_name"></td>
    </tr>
    <tr>
      <td height="90">
      <label for="description">Description:</label>
      <textarea name="description" id="description" class="input" cols="45" rows="5"></textarea></td>
    </tr>
    <tr>
      <td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="thumbnail1" type="checkbox" id="thumbnail1" <?php echo $thumbnail_1_size ? 'checked' : ''; ?> value="1">
            </td>
          <td><label for="thumbnail1" title="Thumbnail prefix: th1_">Create thumbnail 1</label></td>
          </tr>
      </table>
        <table class="size1" style="display:<?php echo $thumbnail_1_size ? 'block' : 'none'; ?>" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="right_padding"><label for="size1">Set thumbnail size:</label><br></td>
            <td><input type="text" name="size1" id="size1" value="<?php echo $thumbnail_1_size; ?>" class="input" size="6"></td>
          </tr>
      </table></td>
    </tr>
    <tr><td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="thumbnail2" type="checkbox" id="thumbnail2" <?php echo $thumbnail_2_size ? 'checked' : ''; ?> value="1">
            </td>
          <td><label for="thumbnail2" title="Thumbnail prefix: th2_">Create thumbnail 2</label></td>
          </tr>
      </table>
        <table class="size2" style="display:<?php echo $thumbnail_2_size ? 'block' : 'none'; ?>" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="right_padding"><label for="size2">Set thumbnail size:</label><br></td>
            <td><input type="text" name="size2" id="size2" value="<?php echo $thumbnail_2_size; ?>" class="input" size="6"></td>
          </tr>
      </table></td>
    </tr>
    <tr><td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="thumbnail3" type="checkbox" id="thumbnail3" <?php echo $thumbnail_3_size ? 'checked' : ''; ?> value="1">
            </td>
          <td><label for="thumbnail3" title="Thumbnail prefix: th3_">Create thumbnail 3</label></td>
          </tr>
      </table>
        <table class="size3" style="display:<?php echo $thumbnail_3_size ? 'block' : 'none'; ?>" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="right_padding"><label for="size3">Set thumbnail size:</label><br></td>
            <td><input type="text" name="size3" id="size3" value="<?php echo $thumbnail_3_size; ?>" class="input" size="6"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td><input type="submit" name="create_album" class="button" id="create_album" value="Create Album"></td>
    </tr>
  </table>
</form>