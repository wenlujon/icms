<?php

/**
 * Forums Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(); ?>>

	<div class="bbp-forum-info-mobile">

		<?php if ( bbp_is_user_home() && bbp_is_subscriptions() ) : ?>

			<span class="bbp-row-actions">

				<?php do_action( 'bbp_theme_before_forum_subscription_action' ); ?>

				<?php bbp_forum_subscription_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>

				<?php do_action( 'bbp_theme_after_forum_subscription_action' ); ?>

			</span>

		<?php endif; ?>
		<a class="bbp-forum-title-mobile" href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a>

</div>

	<div class="bbp-forum-info-mobile">

		<?php do_action( 'bbp_theme_after_forum_title' ); ?>

		<?php do_action( 'bbp_theme_before_forum_description' ); ?>

		<div class="bbp-forum-content-mobile"><?php bbp_forum_content(); ?></div>

		<?php do_action( 'bbp_theme_after_forum_description' ); ?>

		<?php do_action( 'bbp_theme_before_forum_sub_forums' ); ?>

		<?php bbp_list_forums(); ?>

		<?php do_action( 'bbp_theme_after_forum_sub_forums' ); ?>

		<?php bbp_forum_row_actions(); ?>

	</div>

	<div class= "bbp-forum-meta-mobile">
		<?php do_action( 'bbp_theme_before_forum_title' ); ?>

		<div class="bbp-forum-reply-count-mobile">话题：<?php bbp_forum_topic_count(); ?></div>
		<div class="bbp-forum-freshness-author-mobile"> 最新回复：


				<?php do_action( 'bbp_theme_before_topic_author' ); ?>

				<?php bbp_author_link( array( 'post_id' => bbp_get_forum_last_active_id(), 'size' => 14 ) ); ?>

				<?php do_action( 'bbp_theme_after_topic_author' ); ?>

			&nbsp;&nbsp;

			<?php do_action( 'bbp_theme_before_forum_freshness_link' ); ?>

			<span class="bbp-forum-freshness-link-mobile"> <?php bbp_forum_freshness_link(); ?></span>

			<?php do_action( 'bbp_theme_after_forum_freshness_link' ); ?>

		</div>
</div>



</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->
