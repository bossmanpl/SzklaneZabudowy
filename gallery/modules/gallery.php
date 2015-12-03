<?php
include('../admin/config.php');
include('../admin/mysql.open.php');
include('../admin/functions.php');

$albums = getAllAlbums('DESC');
$response = [];
foreach ($albums as $album) {
    $albumId = $album['AlbumID'];
    $response[$albumId] = $album;
    $response[$albumId]['images'] = getAlbumImages($albumId, 'DESC');
}
include('../admin/mysql.close.php');
echo json_encode($response);