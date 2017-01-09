<?php
Namespace WordPress\Plugin\Fancy_Gallery\Widget;
Use WordPress\Plugin\Fancy_Gallery\I18n;

class Random_Images Extends \WP_Widget {
  var $core; #  Pointer to the core class

  function __construct(){
    $this->core = $GLOBALS['WordPress\Plugin\Fancy_Gallery\Core'];

    # Setup the Widget data
    parent::__construct (
      'fancy-gallery-random-images',
      I18n::t('Random Images'),
      Array('description' => I18n::t('Displays some random images from your galleries.'))
    );
  }

  function Default_Options(){
    # Default settings
    return Array(
      'limit'  => 0,
      'exclude' => False,
      'thumb_width' => get_option('thumbnail_size_w'),
      'thumb_height' => get_option('thumbnail_size_h'),
      'link_target' => 'file'
    );
  }

  function Load_Options($options){
    $options = (Array) $options;

    # Delete empty values
    ForEach ($options AS $key => $value)
      If (!$value) Unset($options[$key]);

    # Load options
    $this->arr_option = Array_Merge ($this->Default_Options(), $options);
  }

  function Get_Option($key, $default = False){
    If (IsSet($this->arr_option[$key]) && $this->arr_option[$key])
      return $this->arr_option[$key];
    Else
      return $default;
  }

  function Set_Option($key, $value){
    $this->arr_option[$key] = $value;
  }

  function Get_Fancy_Images($orderby = 'ID', $order = 'ASC', $limit = -1){
    Global $wpdb;

    # Check Parameters
    If (!$orderby) $orderby = 'ID';
    If (!In_Array($order, Array('ASC', 'DESC', 'RAND'))) $order = 'ASC';
    $limit = IntVal ($limit);

    # Build Statement
    $stmt = "
      SELECT
        attachment.*,
        gallery.ID gallery_id
      FROM
        {$wpdb->posts} attachment,
        {$wpdb->posts} gallery
      WHERE  attachment.post_type = \"attachment\"
      AND    attachment.post_mime_type LIKE \"image/%\"
      AND    gallery.post_type = \"{$this->core->gallery_post_type->name}\"
      AND    attachment.post_parent = gallery.ID
      GROUP BY attachment.ID ";
    If ($order == 'RAND')
      $stmt .= 'ORDER BY RAND() ';
    Else
      $stmt .= 'ORDER BY attachment.' . $orderby . ' ' . $order . ' ';
    If ($limit > 0) $stmt .= 'LIMIT ' . $limit;

    return $wpdb->Get_Results($stmt);
  }

  function Form ($settings){
    # Load options
    $this->load_options ($settings); Unset ($settings);
    ?>
    <p style="color:green"><?php $this->core->mocking_bird->Pro_Notice('widget') ?></p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('title') ?>"><?php _e('Title:') ?></label>
      <input type="text" id="<?php Echo $this->Get_Field_Id('title') ?>" name="<?php Echo $this->Get_Field_Name('title')?>" value="<?php Echo Esc_Attr($this->Get_Option('title')) ?>" class="widefat">
      <small><?php Echo I18n::t('Leave blank to use the widget default title.') ?></small>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('limit') ?>"><?php Echo I18n::t('Number of Images:') ?></label>
      <input type="number" id="<?php Echo $this->Get_Field_Id('limit') ?>" name="<?php Echo $this->Get_Field_Name('limit')?>" value="<?php Echo Esc_Attr($this->Get_Option('limit')) ?>" class="widefat">
      <small><?php Echo I18n::t('Leave blank (or "0") to show all.') ?></small>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('link_target') ?>"><?php Echo I18n::t('Link target:') ?></label>
      <select name="<?php Echo $this->Get_Field_Name('link_target')?>" id="<?php Echo $this->Get_Field_Id('link_target') ?>" class="widefat">
        <option value="file" <?php Selected($this->Get_Option('link_target'), 'file') ?>><?php Echo I18n::t('Fullsize Image') ?></option>
        <option value="gallery" <?php Selected($this->Get_Option('link_target'), 'gallery') ?>><?php Echo I18n::t('Image Gallery') ?></option>
      </select>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('thumb_width') ?>"><?php Echo I18n::t('Thumbnail width:') ?></label>
      <input type="number" name="<?php Echo $this->Get_Field_Name('thumb_width') ?>" id="<?php Echo $this->Get_Field_Id('thumb_width') ?>" value="<?php Echo Esc_Attr($this->Get_Option('thumb_width')) ?>" <?php Disabled(True) ?> > px
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('thumb_height') ?>"><?php Echo I18n::t('Thumbnail height:') ?></label>
      <input type="number" name="<?php Echo $this->Get_Field_Name('thumb_height') ?>" id="<?php Echo $this->Get_Field_Id('thumb_height') ?>" value="<?php Echo Esc_Attr($this->Get_Option('thumb_height')) ?>" <?php Disabled(True) ?> > px
    </p>

    <p>
      <input type="checkbox" name="<?php Echo $this->Get_Field_Name('thumb_grayscale') ?>" id="<?php Echo $this->Get_Field_Id('thumb_grayscale') ?>" value="yes" <?php Disabled(True) ?>  >
      <label for="<?php Echo $this->Get_Field_Id('thumb_grayscale') ?>"><?php Echo I18n::t('Convert thumbnails to grayscale.') ?></label>
    </p>

    <p>
      <input type="checkbox" name="<?php Echo $this->Get_Field_Name('thumb_negate') ?>" id="<?php Echo $this->Get_Field_Id('thumb_negate') ?>" value="yes" <?php Disabled(True) ?> >
      <label for="<?php Echo $this->Get_Field_Id('thumb_negate') ?>"><?php Echo I18n::t('Negate the thumbnails.') ?></label>
    </p>

    <h3><?php Echo I18n::t('Template') ?></h3>
    <p><?php Echo I18n::t('Please choose a template to display this widget.') ?></p>
    <?php ForEach ( $this->core->Get_Template_Files() AS $name => $properties ) : ?>
    <p>
      <input type="radio" name="<?php Echo $this->Get_Field_Name('template') ?>" id="<?php Echo Sanitize_Title($properties['name']) ?>" value="<?php Echo Esc_Attr($properties['file']) ?>"
        <?php Checked($this->Get_Option('template'), $properties['file']) ?>
        <?php Checked(!$this->Get_Option('template') && $properties['file'] == $this->core->Get_Default_Template()) ?> >
      <label for="<?php Echo Sanitize_Title($properties['name']) ?>">
      <?php If (Empty($properties['name'])) : ?>
        <em><?php Echo $properties['file'] ?></em>
      <?php Else : ?>
        <strong><?php Echo $properties['name'] ?></strong>
      <?php EndIf; ?>
      </label>
      <?php If ($properties['description']) : ?><br>
      <?php Echo $properties['description']; Endif ?>
    </p>
    <?php EndForEach;

  }

  function Widget ($args, $settings){}

  function Update ($new_settings, $old_settings){
    return $new_settings;
  }

}