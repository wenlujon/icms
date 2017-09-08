<?php
/**
 * The Content Sidebar
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

if ( ! is_active_sidebar( 'sidebar-2' ) ) {
	return;
}
?>
<div id="content-sidebar" class="content-sidebar widget-area" role="complementary">
    <?php   
        $contributor_id = get_the_author_meta( 'ID' );
        $logged_in_user_id = 0;
        if (is_user_logged_in()) {
                $logged_in_user_id = wp_get_current_user()->ID;
        }
        if ( $contributor_id && ($contributor_id != $logged_in_user_id) && is_single() ): 
    ?>

<aside id="tag_cloud-2" class="widget2 widget_recent_entries">
    <div class="followee-sidebar">
        <div class="followee-upper">

            <div class="followee-avatar">
                <a target="_blank" href="<?php echo esc_url( bp_core_get_user_domain( $contributor_id ) ); ?>">
                            <?php echo get_avatar( $contributor_id, 52 ); ?>
                </a>
            </div>

            <div class="followee-right">
                <div class="followee-name">
                <a target="_blank" href="<?php echo esc_url( bp_core_get_user_domain( $contributor_id ) ); ?>">
                    <?php echo get_the_author_meta( 'display_name', $contributor_id ); ?>
                </a>
                </div>

                <div id="item-sidebar" class="followee-follow">
                    <?php do_action('add_follow_in_sidebar'); ?>
                </div>
            </div> <!-- .followee-right -->

        </div><!-- .followee-upper -->


        <div class="followee-summary">
            <p> 
                <?php bp_member_profile_data( 'field=介绍&user_id='.$contributor_id ); ?>
            </p>
        </div>

    </div><!-- .followee-sidebar -->
</aside>

	<?php endif; ?>

	<?php dynamic_sidebar( 'sidebar-2' ); ?>


</div><!-- #content-sidebar -->
