<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>

<div id="main-content" class="main-content">


	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">


		<header class="entry-header"> 
			<div id="dynamicheight" class="w3-display-container">

		<?php
		$i = 0;
		if (  twentyfourteen_has_featured_posts() ) {
			$featured_posts = twentyfourteen_get_featured_posts();

			foreach ( (array) $featured_posts as $order => $post ) :
				setup_postdata( $post );


				$first_image_url = wpdocs_get_first_image_url(get_the_ID());
				
				if (!$first_image_url) {
					continue;
				}

				$i++;

				print( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">'  );
		?>

			  <img class="mySlides" src="<?php echo $first_image_url;?>" style="width:100%">
			</a>

		<div class="icms-featured-text">

	<h2 class="slider-headline"><a href="<?php echo esc_url( get_permalink() );?>"><?php get_the_title_for_index(25);?></a></h2>

</div>

		<?php
			if ($i == 3) {
				break;   // foreach
				
			}
			endforeach;	// foreach

			wp_reset_postdata();

		}

			if (  more_posts() ) :
				// Start the Loop.

				while ( more_posts() ) : 
				
					the_post();

					$first_image_url = wpdocs_get_first_image_url(get_the_ID());
					
					if (!$first_image_url) {
						continue;
					} 

					$post_type = get_post_format();
					if ($post_type == "video") {
						continue;
					}
					
				print( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">'  );
		?>

	  <img class="mySlides" src="<?php echo $first_image_url; ?>" class="mySlides">
	</a>

<div class="icms-featured-text">
	<h2 class="slider-headline"><a href="<?php echo esc_url( get_permalink() );?>"><?php echo get_the_title_for_index(25);?></a></h2>
</div>


<?php
				$i++;
				if ($i == 3) {
				     break;
				}
				endwhile;    	// the_post
			rewind_posts(); 
			//wp_reset_postdata();
			endif;			// have_posts
?>

	<div class="w3-center w3-section w3-large w3-text-white w3-display-bottommiddle" style="width:100%">
<?php
	for ($j = 1; $j <= $i; $j++) {
?>
	<span class="w3-badge demo w3-border w3-transparent w3-hover-white" onclick="currentDiv(<?php echo $j; ?>)"></span>
<?php
	}
?>
    <!-- <span class="w3-badge demo w3-border w3-transparent w3-hover-white" onclick="currentDiv(3)"></span> -->
  </div>

</div>
</header>


		<?php
			if ( have_posts() ) :
				$iter = 0;
				$display_once = 0;
				$display_pos = rand(1, 7);
				// Start the Loop.
				while ( have_posts() ) : the_post();
					if ($display_once == 0) {
						if ($iter == $display_pos) {
							/* $display = rand(0, 1); */
							$display = 1;
							if ($display == 1) {
								$display_once = 1;
								get_template_part( 'google-ads', 'none' );
							}
						}
						$iter++;
					}
					/*
					 * Include the post format-specific template for the content. If you want to
					 * use this in a child theme, then include a file called called content-___.php
					 * (where ___ is the post format) and that will be used instead.
					 */
					get_template_part( 'content-index', get_post_format() );

				endwhile;
				// Previous/next post navigation.
				twentyfourteen_paging_nav();

			else :
				// If no content, include the "No posts found" template.
				get_template_part( 'content', 'none' );

			endif;
		?>

		</div><!-- #content -->
	</div><!-- #primary -->
	<?php get_sidebar( 'content' ); ?>
</div><!-- #main-content -->

<script>
var slideIndex = 1;

function currentDiv(n) {
	showDivs(slideIndex = n);
}

function showDivs(n) {
	var i;
	var x = document.getElementsByClassName("mySlides");
	var y = document.getElementsByClassName("icms-featured-text");
	var dots = document.getElementsByClassName("demo");

	if (n > x.length) {slideIndex = 1}    
	if (n < 1) {slideIndex = x.length}
	for (i = 0; i < x.length; i++) {
		x[i].style.display = "none";  
		y[i].style.display = "none";  
	}

	for (i = 0; i < dots.length; i++) {
		dots[i].className = dots[i].className.replace(" w3-white", "");
	}

	x[slideIndex-1].style.display = "block";  
	y[slideIndex-1].style.display = "block";  
	dots[slideIndex-1].className += " w3-white";
}


    function keepAspectRatio(id, width, height) {
        var aspectRatioDiv = document.getElementByClassName(id);
        aspectRatioDiv.style.width = window.innerWidth;
        aspectRatioDiv.style.height = (window.innerWidth / (width / height)) + "px";
    }

var slideIndex2 = 0;
carousel();

function carousel() {
	var i;
	var x = document.getElementsByClassName("mySlides");
	var y = document.getElementsByClassName("icms-featured-text");
	var dots = document.getElementsByClassName("demo");


	for (i = 0; i < x.length; i++) {
		x[i].style.display = "none"; 
		y[i].style.display = "none"; 
		dots[i].className = dots[i].className.replace(" w3-white", "");

	}

	slideIndex2++;
	if (slideIndex2 > x.length) {slideIndex2 = 1} 
	x[slideIndex2-1].style.display = "block"; 
	y[slideIndex2-1].style.display = "block"; 
	dots[slideIndex2-1].className += " w3-white";


    //run the function when the window loads
	//x[slideIndex2-1].style.width = window.innerWidth;
	//x[slideIndex2-1].style.height = (window.innerWidth / (16 / 9 )) + "px";





	setTimeout(carousel, 8000); // Change image every 3 seconds
}


</script>

<?php
get_sidebar();
get_footer();
