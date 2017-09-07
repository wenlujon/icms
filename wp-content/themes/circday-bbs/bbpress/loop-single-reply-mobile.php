<?php

/**
 * Replies Loop - Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="post-<?php bbp_reply_id(); ?>" class="bbp-reply-header">

	<div class="bbp-meta">

		<?php bbp_reply_author_link( array( 'show_role' => false, 'size' => 2, 'type' => name ) ); ?>
		<br />
		<span class="bbp-reply-post-date">发表于<?php bbp_reply_post_date(0, true); ?></span>

		<?php if ( bbp_is_single_user_replies() ) : ?>

			<span class="bbp-header">
				<?php _e( 'in reply to: ', 'bbpress' ); ?>
				<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink( bbp_get_reply_topic_id() ); ?>"><?php bbp_topic_title( bbp_get_reply_topic_id() ); ?></a>
			</span>

		<?php endif; ?>

		<a href="<?php bbp_reply_url(); ?>" class="bbp-reply-permalink"><?php bbp_reply_position(); ?></a>

	</div><!-- .bbp-meta -->

</div><!-- #post-<?php bbp_reply_id(); ?> -->

<div <?php bbp_reply_class(); ?>>

	<div class="bbp-reply-author-mobile">

		<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>


		<?php if ( bbp_is_user_keymaster() ) : ?>

			<?php do_action( 'bbp_theme_before_reply_author_admin_details' ); ?>

			<div class="bbp-reply-ip"><?php bbp_author_ip( bbp_get_reply_id() ); ?></div>

			<?php do_action( 'bbp_theme_after_reply_author_admin_details' ); ?>

		<?php endif; ?>


	</div><!-- .bbp-reply-author -->

	<div class="bbp-reply-content">

		<?php do_action( 'bbp_theme_before_reply_content' ); ?>

		<?php bbp_reply_content(); ?>

		<?php do_action( 'bbp_theme_after_reply_content' ); ?>

	</div><!-- .bbp-reply-content -->

	<div class="bbp-reply-admin-links-mobile">

		<?php do_action( 'bbp_theme_before_reply_admin_links' ); ?>

		<?php bbp_reply_admin_links(); ?>

		<?php do_action( 'bbp_theme_after_reply_admin_links' ); ?>
	</div><!-- bbp-reply-admin-links -->





</div><!-- .reply -->
