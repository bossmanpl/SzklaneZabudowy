
	$(document).ready(function()
	{
		$('.data_table .delete').click(function()
		{
			if( !confirm('Are you sure you want to delete this album?') )
				return false;
		});
	});
	
	
	function showLoader(message)
	{
		$(".loader").show().html(message);
	}
	
	function hideLoader()
	{
		$(".loader").hide();
	}