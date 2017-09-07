<?php

/**
 * Topics Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_topics_loop' ); ?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" class="bbp-topics">

	<?php if ( !wp_is_mobile() ) : ?>
	<li class="bbp-header">

		<ul class="forum-titles">
			<li class="bbp-topic-title"><?php _e( '主题', 'bbpress' ); ?></li>
			<li class="bbp-topic-voice-count"><?php _e( '参与人数', 'bbpress' ); ?></li>
			<li class="bbp-topic-reply-count"><?php bbp_show_lead_topic() ? _e( '回复/点击', 'bbpress' ) : _e( '回复/点击', 'bbpress' ); ?></li>
			<li class="bbp-topic-freshness"><?php _e( '最后发表', 'bbpress' ); ?></li>
		</ul>

	</li>
	<?php endif; ?>

	<li class="bbp-body">

		<?php while ( bbp_topics() ) : bbp_the_topic(); ?>

		       <?php
                                if ( wp_is_mobile() ) {
                                        bbp_get_template_part( 'loop', 'single-topic-mobile' );
                                }
                                else {
                                        bbp_get_template_part( 'loop', 'single-topic' );
                                }
                        ?>

		<?php endwhile; ?>

	</li>

	<li class="bbp-footer">

		<div class="tr">
			<p>
				<span class="td colspan<?php echo ( bbp_is_user_home() && ( bbp_is_favorites() || bbp_is_subscriptions() ) ) ? '5' : '4'; ?>">&nbsp;</span>
			</p>
		</div><!-- .tr -->

	</li>

</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->

<?php do_action( 'bbp_template_after_topics_loop' ); ?>
