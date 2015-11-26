<?php
include('../admin/config.php');
include('../admin/mysql.open.php');
include('../admin/functions.php');

$albums = getAllAlbums('ASC');
$response = [];
foreach ($albums as $album) {
    $albumId = $album['AlbumID'];
    $response[$albumId] = $album;
    $response[$albumId]['images'] = getAlbumImages($albumId, 'ASC');
}
echo json_encode($response);

include('../admin/mysql.close.php');