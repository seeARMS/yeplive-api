<?php 
/*
 
Template name: gotoEmap
 
*/
//echo "<script>alert('" . get_template_directory_uri() . "');</script>";
//echo "<script>alert('" . home_url('/') . "');</script>"

?>
<script>
	var url = "<?php echo home_url('/')?>" + "/listing/emap/";
	
	location.href = url;
</script>