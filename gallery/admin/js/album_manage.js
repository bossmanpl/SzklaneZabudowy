
	$(document).ready(function()
	{
		var album_id = $("#album_id").val();
		
		$("#upload_image").uploadify(
		{
			'uploader': 'js/jquery.uploadify-v2.1.4/uploadify.swf',
    		'cancelImg' : 'js/jquery.uploadify-v2.1.4/cancel.png',
			'auto':true,
			'wmode':'transparent',
			'buttonText': 'Upload Images',
			'multi': true,
			'script': 'admin.php?album_id='+album_id,
			'fileDataName': 'upload_image',
			'onError' : function(event,ID,fileObj,errorObj)
			{
				console.log( errorObj );
			},
			'onComplete'  : function(event, ID, fileObj, response, data)
			{
				eval("response = eval("+response+")");
				if( response.errors )
				{
					alert("An error occured and image cannot be uploaded!\n\nError Code: [" + response.errors+ "]" );
				}
				else
				{
					var new_image_element = '<li class="images" data-imageid="'+response.ImageID+'">';
					new_image_element += '<img src="'+response.thumbnailUrl+'" width="110" height="95" alt="" />';
					new_image_element += '<div class="clear"></div>';
                    new_image_element += '<a href="#" class="edit_img">Edit</a>';
                    new_image_element += '<a href="#" class="delete_img">Delete</a>';
					new_image_element += '</li>';
					
					var image_element = $(new_image_element).appendTo(".album_images");
					image_element.hide().show("puff");
					deleteImgEvent( image_element.children('.delete_img') );
					editImgEvent( image_element.children('.edit_img') );
					addSortableEvent();
					
					var number = Number( $(".images_uploaded_num").html() );
					$(".images_uploaded_num").html( number+1 );
					
					$(".noimages").remove();
				}
			}
		});
		
		addSortableEvent();
		
		$(".delete_img").each(function()
		{
			var $this = $(this);
			deleteImgEvent($this);
		});
		
		$(".edit_img").each(function()
		{
			var $this = $(this);
			editImgEvent($this);
		});
	});
	
	function deleteImgEvent($this)
	{
		$this.click(function()
		{
			var li_element = $(this).parent();
			var image_id = li_element.attr("data-imageid");
			
			if( confirm('Are you sure you want to delete this image?') )
			{
				$.ajax(
				{
					url: "admin.php?deleteimageid="+image_id,
					beforeSend: function()
					{
						showLoader("Deleting image...");
					},
					success: function(text)
					{
						li_element.hide("explode", function()
						{
							li_element.remove();
							updateImageNumber();
						});
						
						hideLoader();
					}
				});
			}
			
			return false;
		});
	}
	
	function editImgEvent($this)
	{
		$this.click(function()
		{
			var li_element = $(this).parent();
			var image_id = li_element.attr("data-imageid");
			
			var url = 'admin.php?action=editimage&id='+image_id;
			window.open(url, "editImageName", "width=900,height=650");
						
			return false;
		});
	}
	
	function addSortableEvent()
	{
		$(".album_images").sortable(
		{
			update: function()
			{
				var new_order = "";
				var incrementor = 0;
				
				$(".album_images li").each(function()
				{
					if( incrementor > 0 )
						new_order += ",";
						
					new_order += incrementor + "=" + $(this).attr("data-imageid");
					
					incrementor++;
				});
				
				$.ajax(
				{
					url: "admin.php?images_new_order=" + $("#album_id").val() + "&order_string=" + new_order,
					beforeSend: function()
					{
						showLoader("Saving current images order...");
					},
					success: function( resp )
					{
						hideLoader();
					}
				});
			}
		});
		
		$(".album_images").disableSelection();
	}

	
	function updateImageNumber()
	{
		var total_images = $(".album_images .image").length;
		$(".images_uploaded_num").text(total_images);
	}