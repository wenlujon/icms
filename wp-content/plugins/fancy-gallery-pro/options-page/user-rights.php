<?php Namespace WordPress\Plugin\Fancy_Gallery;

# User capabilities
$post_type = Get_Post_Type_Object(Core::$gallery_post_type->name);
If (!$post_type) return;
$arr_capabilities = Array(
  $post_type->cap->edit_posts => $this->t('Edit and create (own) galleries'),
  $post_type->cap->edit_others_posts => $this->t('Edit others galleries'),
  $post_type->cap->edit_private_posts => $this->t('Edit (own) private galleries'),
  $post_type->cap->edit_published_posts => $this->t('Edit (own) published galleries'),

  $post_type->cap->delete_posts => $this->t('Delete (own) galleries'),
  $post_type->cap->delete_private_posts => $this->t('Delete (own) private galleries'),
  $post_type->cap->delete_published_posts => $this->t('Delete (own) published galleries'),
  $post_type->cap->delete_others_posts => $this->t('Delete others galleries'),

  $post_type->cap->publish_posts => $this->t('Publish galleries'),
  $post_type->cap->read_private_posts => $this->t('View (others) private galleries')
);

# Taxonomies
ForEach (Core::$gallery_post_type->arr_taxonomies AS $taxonomie => $tax_args )
  $arr_capabilities[ $tax_args['capabilities']['manage_terms'] ] = SPrintF($this->t('Manage %s'), $tax_args['labels']['name']);

# Show the user roles
ForEach ($GLOBALS['wp_roles']->roles AS $role_name => $arr_role) : ?>
  <h4><?php Echo Translate_User_Role($arr_role['name']) ?></h4>

  <?php ForEach ($arr_capabilities AS $capability => $caption) : ?>

    <div class="capability-selection">
      <span class="caption"><?php Echo $caption ?></span>

      <input type="radio" name="capabilities[<?php Echo $role_name ?>][<?php Echo $capability ?>]" id="capabilities[<?php Echo $role_name ?>][<?php Echo $capability ?>][yes]" value="yes" <?php Checked(IsSet($arr_role['capabilities'][$capability])) ?> >
      <label for="capabilities[<?php Echo $role_name ?>][<?php Echo $capability ?>][yes]"><?php _e('Yes') ?></label>

      <input type="radio" name="capabilities[<?php Echo $role_name ?>][<?php Echo $capability ?>]" value="no" id="capabilities[<?php Echo $role_name ?>][<?php Echo $capability ?>][no]" <?php Checked(!IsSet($arr_role['capabilities'][$capability])) ?> >
      <label for="capabilities[<?php Echo $role_name ?>][<?php Echo $capability ?>][no]"><?php _e('No') ?></label>
    </div>

  <?php EndForEach ?>

<?php EndForEach;