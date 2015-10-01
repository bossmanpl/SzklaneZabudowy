<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mini Back-end Gallery</title>
<link href="css/api.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Mini Back-end Gallery - API</h1>
<h5>Gallery module created by  Arlind Nushi</h5>
<p>Defined functions:</p>

	<div class="functions_env">
    	Album Functions<br />
        <ul>
        	<li><a href="#getAllAlbums">getAllAlbums</a>($order)</li>
        	<li><a href="#albumExists">albumExists</a>($album_id)</li>
        	<li><a href="#getAlbum">getAlbum</a>($album_id)</li>
        	<li><a href="#totalAlbums">totalAlbums</a>()</li>
        	<li><a href="#moveAlbumUP">moveAlbumUP</a>($album_id)</li>
        	<li><a href="#moveAlbumDOWN">moveAlbumDOWN</a>($album_id)</li>
        	<li><a href="#deleteAlbum">deleteAlbum</a>($album_id)</li>
        	<li><a href="#getAlbumImages">getAlbumImages</a>($album_id, $order)</li>
        	<li><a href="#editAlbum">editAlbum</a>($aid, $aname, $desc, $th1, $th2, $th3)</li>
        </ul>
        
        <br />
        Image Functions
        <ul>
        	<li><a href="#imageExists">imageExists</a>($image_id)</li>
        	<li><a href="#getImage">getImage</a>($image_id)</li>
        	<li><a href="#deleteImage">deleteImage</a>($image_id)</li>
        	<li><a href="#setImageName">setImageName</a>($image_id, $name)</li>
            <li><a href="#getAllImages">getAllImages</a>($order)</li>
        </ul>
	</div>
    
    
    <div class="function" id="getAllAlbums">
    	<h1>function : getAllAlbums($order = 'ASC') - <a href="#top">top</a></h1>
        <p>Get all images on the database.</p>
        
        Function parameters
        <div class="param"><span>$order</span> - Set the order of fetched albums. Accepted values: ASC or DESC.</div>
        
        Return type
        <div class="param"><span>Array</span></div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="albumExists">
    	<h1>function : albumExists($album_id) - <a href="#top">top</a></h1>
        <p>Checks whether album exists or not.</p>
        
        Function parameters
        <div class="param"><span>$album_id</span> - Album ID</div>
        
        Return type
        <div class="param"><span>Boolean</span></div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="getAlbum">
    	<h1>function : getAlbum($album_id) - <a href="#top">top</a></h1>
        <p>Get album information as an array or null value if album id doesn't exists.</p>
        
        Function parameters
        <div class="param"><span>$album_id</span> - Album ID</div>
        
        Return type
        <div class="param"><span>Array</span> (AlbumID, AlbumName, Description, DateCreated, Thumbnail1Size, Thumbnail2Size, Thumbnail3Size, OrderID)</div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="totalAlbums">
    	<h1>function : totalAlbums() - <a href="#top">top</a></h1>
        <p>Get total number of albums created.</p>
        
        Function parameters
        <div class="param">No parameters</div>
        
        Return type
        <div class="param"><span>Integer</span></div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="moveAlbumUP">
    	<h1>function : moveAlbumUP($album_id) - <a href="#top">top</a></h1>
        <p>Change the order of albums. Move $album_id upper from the nearest album.</p>
        
        Function parameters
        <div class="param"><span>$album_id</span> - Album ID</div>
        
        Return type
        <div class="param"><span>Void</span></div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="moveAlbumDOWN">
    	<h1>function : moveAlbumDOWN($album_id) - <a href="#top">top</a></h1>
        <p>Change the order of albums. Move $album_id under the nearest album.</p>
        
        Function parameters
        <div class="param"><span>$album_id</span> - Album ID</div>
        
        Return type
        <div class="param"><span>Void</span></div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="deleteAlbum">
    	<h1>function : deleteAlbum($album_id) - <a href="#top">top</a></h1>
        <p>Delete an album and all of its images (if there is any on it). Returns <strong><em>true</em></strong> if the action is done successfully or <strong><em>false</em></strong> if album doesn't exists!</p>
        
        Function parameters
        <div class="param"><span>$album_id</span> - Album ID</div>
        
        Return type
        <div class="param"><span>Boolean</span></div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="getAlbumImages">
    	<h1>function : getAlbumImages($album_id, $order = ASC) - <a href="#top">top</a></h1>
        <p>Get all album images from <em><strong>$album_id</strong></em> in the ASCENDING order by default.</p>
        
        Function parameters
        <div class="param"><span>$album_id</span> - Album ID</div>
        <div class="param"><span>$order</span> - Order of images you get from album. Accepted values: ASC or DESC</div>
        
        Return type
        <div class="param"><span>Array</span></div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="editAlbum">
    	<h1>function : editAlbum($album_id, $album_name, $description = '', $thumbnail1 = null, $thumbnail2 = null, $thumbnail3 = null) - <a href="#top">top</a></h1>
        <p>This functions changes album properties such as name, description and thumbnail sizes.</p>
        
        Function parameters
        <div class="param"><span>$album_id</span> - Album ID</div>
        <div class="param"><span>$album_name</span> - New album name</div>
        <div class="param"><span>$description</span> - New album description</div>
        <div class="param"><span>$thumbnail1</span> - Size of first thumbnail that will be generated after upload (empty value will not create thumbnail)</div>
        <div class="param"><span>$thumbnail2</span> - Size of second thumbnail (accepted format <em><strong>number</strong></em>x<strong><em>number</em></strong> i.e. <strong><em>100</em></strong>x<strong><em>125</em></strong>)</div>
        <div class="param"><span>$thumbnail3</span> - Size of third thumbnail</div>
        
        Return type
        <div class="param"><span>Void</span></div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="imageExists">
    	<h1>function : imageExists($image_id) - <a href="#top">top</a></h1>
        <p>Check whether given image id exists or not.</p>
        
        Function parameters
        <div class="param"><span>$image_id</span> - Album ID</div>
        
        Return type
        <div class="param"><span>Boolean</span></div>
    </div>
    
    <div class="separator"></div>
    
	<div class="function" id="getImage">
    	<h1>function : getImage($image_id) - <a href="#top">top</a></h1>
        <p>Get specific image based on ID.</p>
        
        Function parameters
        <div class="param"><span>$image_id</span> - Album ID</div>
        
        Return type
        <div class="param"><span>Array</span> (ImageID, AlbumID, ImagePath, Name, UploadDate, OrderID, DefaultThumbnail, Thumbnail1, Thumbnail2, Thumbnail3)</div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="deleteImage">
    	<h1>function : deleteImage($image_id) - <a href="#top">top</a></h1>
        <p>Delete an image. True value will be returned if it has been deleted, otherwise false value will indicate that image doesn't exists.</p>
        
        Function parameters
        <div class="param"><span>$image_id</span> - Album ID</div>
        
        Return type
        <div class="param"><span>Boolean</span></div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="setImageName">
    	<h1>function : setImageName($image_id, $name) - <a href="#top">top</a></h1>
        <p>Set the name of an image.</p>
        
        Function parameters
        <div class="param"><span>$image_id</span> - Album ID</div>
        <div class="param"><span>$name</span> - New image name</div>
        
        Return type
        <div class="param"><span>Void</span></div>
    </div>
    
    <div class="separator"></div>
    
    <div class="function" id="getAllImages">
    	<h1>function : getAllImages($order = 'ASC') - <a href="#top">top</a></h1>
        <p>Get all images on the database.</p>
        
        Function parameters
        <div class="param"><span>$order</span> - Set the order of images. Accepted values: ASC or DESC.</div>
        
        Return type
        <div class="param"><span>Array</span></div>
    </div>

	<div class="note">
    	An example how you can use some of these functions can be found on <a href="example.php">this file</a>.
    </div>
	
</body>
</html>