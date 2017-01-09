<?php
/*
Fancy Gallery Template: Default Gallery
Description: This template displays the thumbnail images with a link to the accordingly full size image.
Version: 1.1
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/
?>
<div class="gallery fancy-gallery <?php Echo BaseName(__FILE__, '.php') ?> gallery-columns-<?php Echo $this->gallery->attributes->columns ?>" id="gallery_<?php Echo $this->gallery->id ?>">
<?php ForEach($this->gallery->images AS $image): ?>
  <figure class="gallery-item">

    <div class="gallery-icon">
      <a href="<?php Echo Esc_Url($image->href) ?>" title="<?php Echo Esc_Attr($image->title) ?>" class="<?php Echo Esc_Attr($this->gallery->attributes->link_class) ?>"  data-caption="<?php Echo Esc_Attr($image->caption) ?>" data-description="<?php Echo Esc_Attr($image->description) ?>">
        <img <?php ForEach ($image->attributes AS $attribute => $value) PrintF('%s="%s" ', $attribute, Esc_Attr($value)) ?> >
      </a>
    </div>

    <?php If (!Empty($image->caption)): ?>
    <figcaption class="wp-caption-text gallery-caption"><?php Echo WPTexturize($image->caption) ?></figcaption>
    <?php EndIf ?>

  </figure>
<?php EndForEach ?>
</div>