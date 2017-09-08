<?php
    $followee_id = get_the_author_meta( 'ID' );
    $logged_in_user_id = 0;
    if (is_user_logged_in()) {
            $logged_in_user_id = wp_get_current_user()->ID;
    }
    if ( ($followee_id != $logged_in_user_id) && is_single() ):
?>

<div class="followee-bottom">

    <div class="followee-avatar">
        <a target="_blank" href="<?php echo esc_url( bp_core_get_user_domain( $followee_id ) ); ?>">
                    <?php echo get_avatar( $followee_id, 52 ); ?>
        </a>
    </div>

    <div class="followee-info">
        <div class="followee-name">
                <a target="_blank" href="<?php echo esc_url( bp_core_get_user_domain( $followee_id ) ); ?>">
                     <?php echo get_the_author_meta( 'display_name', $followee_id ); ?>
                </a>
        </div>


            <div class="followee-summary">
                    <?php echo wp_trim_words(bp_get_member_profile_data( 'field=介绍&user_id='.$followee_id), 10); ?>
            </div>
    </div><!-- .followee-info -->

    <div id="item-bottom" class="followee-follow">
        <?php do_action('add_follow_in_sidebar'); ?>
    </div>

</div>


<?php endif; ?> <!-- followee -->
