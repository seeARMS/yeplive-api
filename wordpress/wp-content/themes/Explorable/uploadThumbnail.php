<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

// pull the raw binary data from the POST array
$data = substr($_POST['data'], strpos($_POST['data'], ",") + 1);
// decode it
$decodedData = base64_decode($data);
// print out the raw data,
$filename = "../../uploads/thumbnails/";
$filename = $filename . $_POST['fname'];
// write the data out to the file
$fp = fopen($filename, 'wb');
fwrite($fp, $decodedData);
fclose($fp);

echo "<script>parent.window.document.getElementById('imgUrl').value = '" . home_url() . "/wp-content/uploads/thumbnails/" . $_POST['fname'] . "'</script>";

?>