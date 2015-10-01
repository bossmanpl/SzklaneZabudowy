	$(function()
	{
		$(".data_table").sortable(
		{
			items:".album_entry",
			update: function()
			{
				var order_string = "";
				
				$(".album_entry").each(function(id)
				{
					var album_id = $(this).attr("data-albumid");
					
					if( id > 0 )
						order_string += ",";
						
					order_string += id+"="+album_id;
				});
				
				$.ajax(
				{
					url:"admin.php?albums_new_order=true&order_string="+order_string,
					beforeSend: function()
					{
						showLoader("Saving current albums order...");
					},
					success: function( resp )
					{
						hideLoader();
					}
				});
			}
		});
	});