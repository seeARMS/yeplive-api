<?php if ( ! et_is_listing_page() || ( is_single() && 'listing' == get_post_type() ) ) : ?>
	<footer id="main-footer">
		<div class="container">
			<?php get_sidebar( 'footer' ); ?>

			<!-- <p id="copyright"><?php printf( __( 'Designed by %1$s | Powered by %2$s', 'Explorable' ), '<a href="http://www.elegantthemes.com" title="Premium WordPress Themes">Elegant Themes</a>', '<a href="http://www.wordpress.org">WordPress</a>' ); ?></p>
			 -->
		</div> <!-- end .container -->
	</footer> <!-- end #main-footer -->
<?php endif; ?>

	<?php wp_footer(); ?>
</body>
<script>
	//document.getElementById("loadImg").style.left = parseInt(document.body.offsetWidth / 2 - document.getElementById("loadImg").offsetWidth-50) + "px";
	//document.getElementById("loadImg").style.top = parseInt(document.body.offsetHeight / 2 - document.getElementById("loadImg").offsetHeight) + "px"; 
	//document.getElementById("loadImg").style.display = "inline";
</script>
</html>