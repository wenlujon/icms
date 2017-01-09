<?php Namespace WordPress\Plugin\Fancy_Gallery ?>

<label class="screen-reader-text" for="post_author_override"><?php I18n::t('Owner') ?></label>

<?php
Global $post;
WP_DropDown_Users(Array(
  'name' => 'post_author_override',
  'selected' => Empty($post->ID) ? $user_ID : $post->post_author,
  'include_selected' => True )
);
?>

(<small><?php Echo I18n::t('Changes the owner of this gallery.') ?></small>)