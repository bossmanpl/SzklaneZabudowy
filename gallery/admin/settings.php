<div class="separator"></div>
<h1>General Settings</h1>
<script type="text/javascript">
	$(document).ready(function()
	{
		$("#thumbnail1size").focus(function(){ $("#thumbnail1").attr("checked", true); });
		$("#thumbnail2size").focus(function(){ $("#thumbnail2").attr("checked", true); });
		$("#thumbnail3size").focus(function(){ $("#thumbnail3").attr("checked", true); });
	});
</script>
<form name="form1" method="post" action="admin.php">
  <table border="0" cellspacing="2" cellpadding="2">
    <tr>
      <td colspan="2">
      <strong>Admin User</strong>
      <div class="separator"></div>
      </td>
    </tr>
    <tr>
      <td width="120"><label for="admin_username">Username:</label></td>
      <td>
      <input type="text" class="input" name="admin_username" id="admin_username" value="<?php echo $admin_username; ?>"></td>
    </tr>
    <tr>
      <td><label for="admin_password">Password:</label></td>
      <td>
      <input type="password" class="input" name="admin_password" id="admin_password"> 
      <span class="highlighted">Only if you want to change</span></td>
    </tr>
    <tr>
      <td height="50" colspan="2" valign="bottom"><strong>Gallery Settings</strong>
        <div class="separator"></div></td>
    </tr>
    <tr>
      <td><label for="gallery_title">Title:</label></td>
      <td>
      <input type="text" name="gallery_title" value="<?php echo htmlspecialchars($title); ?>" id="gallery_title" class="input"></td>
    </tr>
    <tr>
      <td><label for="naming">Image naming:</label></td>
      <td>
        <select name="naming" id="naming" class="input" style="width:170px">
          <option value="normal">Normal</option>
          <option value="hash"<?php echo $naming == 'hash' || $naming == 'random' ? ' selected' : ''; ?>>Hash (random)</option>
          <option value="nospaces"<?php echo $naming == 'nospaces' ? ' selected' : ''; ?>>No spaces</option>
      </select></td>
    </tr>
    <tr>
      <td height="30" colspan="2" valign="bottom"><strong>By default</strong></td>
    </tr>
    <tr>
      <td><label for="thumbnail1size" title="Thumbnail prefix: th1_">Create thumbnail 1:</label></td>
      <td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="thumbnail1" type="checkbox" id="thumbnail1" <?php echo $thumbnail_1_size ? 'checked' : ''; ?> value="1">
            </td>
          <td>
            <input name="thumbnail1size" type="text" class="input" id="thumbnail1size" value="<?php echo $thumbnail_1_size; ?>" size="6"> 
            <span class="highlighted">i.e. 125x94</span></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td><label for="thumbnail2size" title="Thumbnail prefix: th2_">Create thumbnail 2:</label></td>
      <td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="thumbnail2" type="checkbox" id="thumbnail2" <?php echo $thumbnail_2_size ? 'checked' : ''; ?> value="1">
            </td>
          <td>
            <input name="thumbnail2size" type="text" class="input" id="thumbnail2size" value="<?php echo $thumbnail_2_size; ?>" size="6"></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td><label for="thumbnail3size" title="Thumbnail prefix: th3_">Create thumbnail 3:</label></td>
      <td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="thumbnail3" type="checkbox" id="thumbnail3" <?php echo $thumbnail_3_size ? 'checked' : ''; ?> value="1">
            </td>
          <td>
            <input name="thumbnail3size" type="text" class="input" id="thumbnail3size" value="<?php echo $thumbnail_3_size; ?>" size="6"></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td colspan="2" align="right"><input type="submit" name="settings_save_changes" class="save_changes" id="settings_save_changes" value="Save Changes"></td>
    </tr>
  </table>
</form>
<div class="separator"></div>