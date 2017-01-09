<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
		<?php 
			$first_image = wpdocs_get_first_image_url(get_the_ID());
			
			if ($first_image) :
			
		?>

		<a href="<?php echo esc_url( get_permalink(  ) ); ?>">
			<div class="post-thumbnail-index" href="<?php the_permalink(); ?>" aria-hidden="true">
			<?php
				echo '<img src=" ' ;
				echo $first_image;   // image src
				echo '" class="current">';
			?>
			</div>
		</a>


		<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && twentyfourteen_categorized_blog() ) : ?>
		<div class="entry-meta-index">
			<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); ?></span>
		</div>
		<?php
			endif;

			get_the_title_for_index();
		?>

		<div class="entry-meta-index">
			<span class="category-link"><?php get_category_for_index(); ?></span>
			<?php
				if ( 'post' == get_post_type() )
					index_posted_on();

				if (  ( comments_open() || get_comments_number() ) ) :
			?>
			<span class="comments-link-index"><?php comments_popup_link( __( '0评论', 'twentyfourteen' ), __( '1评论 ', 'twentyfourteen' ), __( '%评论', 'twentyfourteen' ) ); ?></span>
			<?php
				endif;

				//edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
			?>

		</div><!-- .entry-meta -->

	<?php 
		else:
	?>

		<a href="<?php echo esc_url( get_permalink(  ) ); ?>">
		</a>


		<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && twentyfourteen_categorized_blog() ) : ?>
		<div class="entry-meta-index-no-pic">
			<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); ?></span>
		</div>
		<?php
			endif;

			get_the_title_for_index();
		?>

		<div class="entry-meta-index-no-pic">
			<span class="category-link"><?php get_category_for_index(); ?></span>
			<?php
				if ( 'post' == get_post_type() )
					index_posted_on();

				if (  ( comments_open() || get_comments_number() ) ) :
			?>
			<span class="comments-link-index"><?php comments_popup_link( __( '0评论', 'twentyfourteen' ), __( '1评论 ', 'twentyfourteen' ), __( '%评论', 'twentyfourteen' ) ); ?></span>
			<?php
				endif;

				//edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
			?>

		</div><!-- .entry-meta -->


	<?php endif; ?>

	</header><!-- .entry-header -->

	<!-- <?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?> -->
</article><!-- #post-## -->
