<?php

/**
 * Search
 *
 * @package bbPress
 * @subpackage Theme
 */
$forum_id = bbp_get_forum_id();
?>
 
<form role="search" method="get" id="bbp-search-form" action="<?php bbp_search_url(); ?>">
    <div>
        <label class="screen-reader-text hidden" for="bbp_search"><?php _e( 'Search for:', 'bbpress' ); ?></label>
        <input placeholder="Search <?php the_title(); ?> Topics" tabindex="<?php bbp_tab_index(); ?>" type="text" value="<?php echo esc_attr( bbp_get_search_terms() ); ?>" name="bbp_search" id="bbp_search" />
        <?php if( $forum_id ): ?>
        <input class="button" type="hidden" name="bbp_search_forum_id" value="<?php echo $forum_id; ?>" />
        <?php endif; ?>
        <input tabindex="<?php bbp_tab_index(); ?>" class="button" type="submit" id="bbp_search_submit" value="<?php esc_attr_e( 'Search', 'bbpress' ); ?>" />
    </div>
</form>
