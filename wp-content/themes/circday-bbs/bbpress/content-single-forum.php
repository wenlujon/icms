<?php

/**
 * Single Forum Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums">

	<?php bbp_breadcrumb(); ?>

	<?php bbp_forum_subscription_link(); ?>
	
	<?php do_action( 'bbp_template_before_single_forum' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

		<?php bbp_single_forum_description(); ?>

		<?php if ( bbp_has_forums() ) : ?>

			<?php bbp_get_template_part( 'loop', 'forums' ); ?>

		<?php endif; ?>

		<?php if ( !bbp_is_forum_category() && bbp_has_topics() ) : ?>
			<?php if ( bbp_current_user_can_access_create_topic_form() ) : ?>
				<?php if ( is_user_logged_in() ): ?>
					<h1 align=right><a class="button" type="submit" href="#create_new_topic">
					<?php esc_html_e( '发表新帖', 'bbpress' ); ?></a></h1>
				<?php endif; ?>
			<?php else : ?>
				<?php if ( is_user_logged_in() ): ?>
                                        <?php
                                        global $group;
                                        $link = sprintf( '<h1 align=right><a class="button" rel="nofollow" type="submit" href="%s">%s</a></h1>',
                                                bp_get_group_permalink( $group ),
                                                '如想发表新帖请加入此群组'
                                        );
                                        echo $link;
                                        ?>

				<?php else : ?>
					<?php
					$link = sprintf( '<h1 align=right><a class="button" rel="nofollow" type="submit" href="%s">%s</a></h1>',
						wp_login_url( get_permalink() ),
						'发表新帖'
					);
					echo $link;
					?>
				<?php endif; ?>
			<?php endif; ?>

			<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

			<?php bbp_get_template_part( 'loop',       'topics'    ); ?>

			<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

			<a id="create_new_topic"></a>
			<?php bbp_get_template_part( 'form',       'topic'     ); ?>

		<?php elseif ( !bbp_is_forum_category() ) : ?>

			<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

			<?php bbp_get_template_part( 'form',       'topic'     ); ?>

		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_forum' ); ?>

</div>
